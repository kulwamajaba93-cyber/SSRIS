<?php
/**
 * SSRIS ER Diagram — PDF Export Helpers
 *
 * Strategy (since PlantUML binary may not be installed):
 *   1. We use the official PlantUML online server (www.plantuml.com)
 *      to render ssris_er_diagram.puml → PNG via their public HTTP API.
 *   2. Then we wrap the high-res PNG in a landscape A2 multi-page PDF
 *      with a title page + legend, using native PHP GD for any post-
 *      processing, and build the final PDF with PHP's built-in
 *      hash-based Deflate + minimal PDF writer (no deps, works on any
 *      PHP with GD).
 *
 * Output: c:/laragon/www/ssris/docs/SSRIS_ER_Diagram.pdf
 */

$PUML_PATH = __DIR__ . '/docs/ssris_er_diagram.puml';
$MMD_PATH  = __DIR__ . '/docs/ssris_er_diagram.mmd';
$OUT_PDF   = __DIR__ . '/docs/SSRIS_ER_Diagram.pdf';
$OUT_PNG   = __DIR__ . '/docs/SSRIS_ER_Diagram.png';

echo "====================================================================\n";
echo " SSRIS — Entity-Relationship Diagram PDF Builder\n";
echo "====================================================================\n";

if (!file_exists($PUML_PATH)) { fwrite(STDERR, "Missing: $PUML_PATH\n"); exit(1); }

/* ------------------------------------------------------------------ *
 * STEP 1 — Encode the .puml in PlantUML's 6-bit "deflate + custom    *
 * base64" format required by their public server.                    *
 * ------------------------------------------------------------------ */
function plantuml_encodex64(string $raw): string {
    // PlantUML encoding: UTF-8 → DEFLATE (raw, no header) → custom 6-bit base64 alphabet
    $deflated = gzdeflate($raw, 9);
    $map = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
    $out = '';
    $n   = strlen($deflated);
    for ($i = 0; $i < $n; $i += 3) {
        $b1 = ord($deflated[$i]);
        $b2 = ($i + 1 < $n) ? ord($deflated[$i + 1]) : 0;
        $b3 = ($i + 2 < $n) ? ord($deflated[$i + 2]) : 0;
        $c1 =  $b1 >> 2;
        $c2 = (($b1 & 0x3) << 4) | ($b2 >> 4);
        $c3 = (($b2 & 0xF) << 2) | ($b3 >> 6);
        $c4 =  $b3 & 0x3F;
        $out .= $map[$c1] . $map[$c2] . $map[$c3] . $map[$c4];
        if ($i + 1 >= $n) { $out = substr($out, 0, -2); }
        elseif ($i + 2 >= $n) { $out = substr($out, 0, -1); }
    }
    return $out;
}

$pumlSource = file_get_contents($PUML_PATH);
$encoded    = plantuml_encodex64($pumlSource);

// PlantUML server URLs: png, svg, pdf. Use PNG first as it's the most reliable.
$pngUrl = "https://www.plantuml.com/plantuml/png/0/{$encoded}";
$svgUrl = "https://www.plantuml.com/plantuml/svg/0/{$encoded}";
$pdfUrl = "https://www.plantuml.com/plantuml/pdf/0/{$encoded}";

echo "→ PlantUML diagram encoded (" . strlen($encoded) . " chars).\n";
echo "→ Trying direct PlantUML → PDF download first...\n";

/* ------------------------------------------------------------------ *
 * STEP 2 — Try fetching PDF directly from PlantUML server.           *
 * ------------------------------------------------------------------ */
$ctx = stream_context_create([
    'http' => [
        'timeout'         => 120,
        'user_agent'      => 'SSRIS-ERD/1.0 (+https://mocu.ac.tz)',
        'follow_location' => 1,
        'max_redirects'   => 5,
    ],
    'ssl'  => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$pdfDirect = @file_get_contents($pdfUrl, false, $ctx);
if ($pdfDirect !== false && strpos($pdfDirect, '%PDF-') === 0) {
    // PlantUML PDF has the diagram only — wrap it with cover+legend? Simpler: just save it.
    // Prepend title page using a minimal PDF merger would be complex. Instead we produce
    // a separate wrapped PDF using our own PNG-based pipeline further down. For now keep this
    // as the "clean diagram only" PlantUML-generated PDF.
    $plantPdf = __DIR__ . '/docs/SSRIS_ER_Diagram_PlantUML.pdf';
    file_put_contents($plantPdf, $pdfDirect);
    echo "✅ PlantUML direct PDF saved: " . basename($plantPdf) .
         " (" . number_format(strlen($pdfDirect) / 1024, 1) . " KB)\n";
}

/* ------------------------------------------------------------------ *
 * STEP 3 — Download high-res PNG and build a FULL PDF with title     *
 * page, legend, and the diagram — landscape A2.                      *
 * ------------------------------------------------------------------ */
echo "→ Downloading high-res PNG diagram from PlantUML server...\n";
$pngBytes = @file_get_contents($pngUrl, false, $ctx);
if ($pngBytes === false || strlen($pngBytes) < 1000) {
    fwrite(STDERR, "⚠ PlantUML server PNG download failed. Trying SVG fallback...\n");
    $pngBytes = false;
}
if ($pngBytes === false) {
    $svgBytes = @file_get_contents($svgUrl, false, $ctx);
    if ($svgBytes !== false && strlen($svgBytes) > 1000) {
        $svgPath = __DIR__ . '/docs/SSRIS_ER_Diagram.svg';
        file_put_contents($svgPath, $svgBytes);
        echo "⚠ SVG saved (cannot rasterize to PNG without ImageMagick/Inkscape): " . basename($svgPath) . "\n";
    }
}
if ($pngBytes !== false) {
    file_put_contents($OUT_PNG, $pngBytes);
    $info = getimagesize($OUT_PNG);
    if ($info !== false) {
        echo "✅ PNG saved: " . basename($OUT_PNG) .
             " ({$info[0]}×{$info[1]} px, " .
             number_format(strlen($pngBytes) / 1024, 1) . " KB)\n";
    } else {
        $pngBytes = false;
    }
}

/* ------------------------------------------------------------------ *
 * STEP 4 — Build final wrapped PDF (cover + legend + diagram) with   *
 * a minimal, dependency-free PDF writer.                             *
 * ------------------------------------------------------------------ */
class MiniPdf {
    private $pages = []; // each: ['w'=>pt,'h'=>pt,'streams'=>[stream,...]]
    private $fonts = [];
    function addPage($wPt, $hPt) { $this->pages[] = ['w'=>$wPt,'h'=>$hPt,'streams'=>[]]; return count($this->pages)-1; }
    function addStream($pageIdx, $content) { $this->pages[$pageIdx]['streams'][] = $content; }
    function escape($s) {
        return str_replace(['\\','(',')'], ['\\\\','\\(', '\\)'], (string)$s);
    }
    function output($file) {
        $objs   = []; $xref = []; $pos = 0;
        $out    = "%PDF-1.7\n%âãÏÓ\n";
        $pos    = strlen($out);
        function addObj(&$out,&$objs,&$xref,&$pos,$body){
            $idx = count($objs)+1; $xref[$idx] = $pos;
            $out .= "{$idx} 0 obj\n{$body}endobj\n";
            $pos  = strlen($out);
            return $idx;
        }
        // Font: F1 Helvetica
        $f1 = addObj($out,$objs,$xref,$pos,
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\n");
        // For each page: content, page dict, pages tree
        $pageIds = []; $contentIds = [];
        foreach ($this->pages as $p) {
            $stream = implode("\n", $p['streams']);
            $len    = strlen($stream);
            $cid    = addObj($out,$objs,$xref,$pos,
                "<< /Length {$len} >>\nstream\n{$stream}\nendstream\n");
            $contentIds[] = $cid;
            $pid    = addObj($out,$objs,$xref,$pos,
                "<< /Type /Page /Parent 9999 0 R /MediaBox [0 0 {$p['w']} {$p['h']}] " .
                "/Resources << /Font << /F1 {$f1} 0 R >> >> " .
                "/Contents {$cid} 0 R >>\n");
            $pageIds[] = $pid;
        }
        // Pages tree object (rewrite 9999 placeholder)
        $kids = '';
        foreach ($pageIds as $p) $kids .= "{$p} 0 R ";
        $kids = trim($kids);
        $pagesObj = addObj($out,$objs,$xref,$pos,
            "<< /Type /Pages /Count " . count($this->pages) . " /Kids [{$kids}] >>\n");
        $out = preg_replace_callback('/9999 0 R/', function() use($pagesObj){ return "{$pagesObj} 0 R"; }, $out);
        // Rebuild xref after replacement
        $pos = 0; $xref = [];
        $lines = preg_split('/(?<=endobj\n)/', $out);
        $out2  = '';
        foreach ($lines as $chunk) {
            if ($chunk === '') continue;
            $xref[] = $pos;
            $out2 .= $chunk;
            $pos   = strlen($out2);
        }
        $out = $out2;
        // Catalog
        $catalogId = addObj($out,$objs,$xref,$pos,
            "<< /Type /Catalog /Pages {$pagesObj} 0 R /ViewerPreferences << /PageMode /UseNone >> >>\n");
        // XREF
        $xrefPos = strlen($out);
        $total   = count($xref) + 1;
        $out .= "xref\n0 {$total}\n";
        $out .= sprintf("%010d %05d f \n", 0, 65535);
        foreach ($xref as $p) {
            $out .= sprintf("%010d %05d n \n", $p, 0);
        }
        $out .= "trailer\n<< /Size {$total} /Root {$catalogId} 0 R >>\n";
        $out .= "startxref\n{$xrefPos}\n%%EOF";
        file_put_contents($file, $out);
    }
    // Helpers
    function textPageContent($w, $h, $title, $subTitle, $lines, $footer=null) {
        $cmds = [];
        // White background
        $cmds[] = "q 1 1 1 rg 0 0 {$w} {$h} re f Q";
        // Top bar
        $barH = 90;
        $cmds[] = "q 0.051 0.278 0.631 rg 0 ".($h - $barH)." {$w} {$barH} re f Q";
        $cmds[] = "q 0.976 0.839 0.310 rg 0 ".($h - $barH - 6)." {$w} 6 re f Q";
        // Title in bar
        $cmds[] = "BT /F1 1 0 0 1 0 0 Tm /F1 26 Tf 1 1 1 rg 50 ".($h - 45)." Td (" .
                  $this->escape($title) . ") Tj ET";
        $cmds[] = "BT /F1 12 Tf 0.9 0.9 0.95 rg 50 ".($h - 70)." Td (" .
                  $this->escape($subTitle) . ") Tj ET";
        // Body lines
        $y = $h - $barH - 40;
        $left = 50;
        foreach ($lines as $ln) {
            if (is_array($ln)) {
                [$text, $size, $bold, $indent, $color] = array_pad($ln, 5, null);
                $size = $size ?: 11;
                $indent = $indent ?: 0;
                $rgb  = $color ?: [0,0,0];
                $cmds[] = "BT /F1 {$size} Tf {$rgb[0]} {$rgb[1]} {$rgb[2]} rg " .
                          ($left + $indent) . " {$y} Td (" .
                          $this->escape($text) . ") Tj ET";
            } else {
                $cmds[] = "BT /F1 11 Tf 0 0 0 rg {$left} {$y} Td (" .
                          $this->escape($ln) . ") Tj ET";
            }
            $y -= 18;
            if ($y < 80) break;
        }
        if ($footer) {
            $cmds[] = "q 0.8 0.8 0.8 rg 0 45 {$w} 0.5 re f Q";
            $cmds[] = "BT /F1 9 Tf 0.4 0.4 0.4 rg 50 30 Td (" .
                      $this->escape($footer) . ") Tj ET";
        }
        return implode("\n", $cmds);
    }
    function imagePageContent($w, $h, $pngFile, $caption=null, $footer=null) {
        $imgInfo = @getimagesize($pngFile);
        if (!$imgInfo) return '';
        [$iw,$ih,$type] = $imgInfo;
        if ($type != IMAGETYPE_PNG) return '';
        $im = imagecreatefrompng($pngFile);
        // Ensure white background (in case of transparency)
        $w2 = imagesx($im); $h2 = imagesy($im);
        $bg = imagecreatetruecolor($w2,$h2);
        imagefill($bg,0,0,imagecolorallocate($bg,255,255,255));
        imagealphablending($bg,true);
        imagecopy($bg,$im,0,0,0,0,$w2,$h2);
        imagedestroy($im); $im = $bg;
        // Fit on page with margin
        $mx = 36; $my = 80; $availW = $w - 2*$mx; $availH = $h - 2*$my - ($caption?40:0);
        $scale = min($availW/$w2, $availH/$h2, 1);
        $dw = $w2*$scale; $dh = $h2*$scale;
        $dx = $mx + ($availW - $dw)/2;
        $dy = $my + ($availH - $dh)/2 + ($caption?40:0);
        // Use raw-image DCT-decoded bytes via JPEG (simpler embedding in PDF).
        ob_start();
        imagejpeg($im, null, 92);
        $jpegBytes = ob_get_clean();
        $jpegLen   = strlen($jpegBytes);
        imagedestroy($im);
        $streams = [];
        // White bg + thin border
        $streams[] = "q 1 1 1 rg 0 0 {$w} {$h} re f Q";
        $streams[] = "q 0.85 0.85 0.85 RG 2 w " .
                     ($dx-3) . " " . ($dy-3) . " " . ($dw+6) . " " . ($dh+6) . " re S Q";
        $streams[] = "q 0 0 0 RG 0.6 w {$dx} {$dy} {$dw} {$dh} re S Q";
        // XObject inline would need external objects -> instead we embed the JPG
        // via standard stream. We'll encode the image as an object separately.
        $this->_pendingJpeg = ['bytes'=>$jpegBytes,'w'=>$w2,'h'=>$h2];
        $streams[] = "q {$dw} 0 0 {$dh} {$dx} {$dy} cm /Im1 Do Q";
        if ($caption) {
            $streams[] = "BT /F1 12 Tf 0 0 0 rg " . ($w/2 - 150) . " " .
                         ($dy - 26) . " Td (" . $this->escape($caption) . ") Tj ET";
        }
        if ($footer) {
            $streams[] = "q 0.8 0.8 0.8 rg 0 45 {$w} 0.5 re f Q";
            $streams[] = "BT /F1 9 Tf 0.4 0.4 0.4 rg 50 30 Td (" .
                         $this->escape($footer) . ") Tj ET";
        }
        return implode("\n", $streams);
    }
    private $_pendingJpeg = null;
    function writeImagePage($pdfPath, $wPt, $hPt, $pngPath, $caption, $footer, $titleBar=null) {
        $imgInfo = @getimagesize($pngPath);
        if (!$imgInfo) return false;
        [$iw,$ih,$type] = $imgInfo;
        if ($type != IMAGETYPE_PNG) return false;
        $im = imagecreatefrompng($pngPath);
        $w2 = imagesx($im); $h2 = imagesy($im);
        $bg = imagecreatetruecolor($w2,$h2);
        imagefill($bg,0,0,imagecolorallocate($bg,255,255,255));
        imagealphablending($bg,true);
        imagecopy($bg,$im,0,0,0,0,$w2,$h2);
        imagedestroy($im); $im=$bg;
        // Build title bar on top if requested
        if ($titleBar) {
            $bar = imagecreatetruecolor($w2, 100);
            imagefill($bar,0,0,imagecolorallocate($bar,13,71,161));
            $accent = imagecolorallocate($bar,255,213,79);
            imagefilledrectangle($bar,0,92,$w2,100,$accent);
            $tcol = imagecolorallocate($bar,255,255,255);
            $scol = imagecolorallocate($bar,230,230,250);
            // Draw text with GD built-in fonts
            $ttf = '';
            if (function_exists('imagettftext')) {
                // Attempt to find a system TTF
                $candidates = [
                    'C:\Windows\Fonts\timesbd.ttf',
                    'C:\Windows\Fonts\times.ttf',
                    'C:\Windows\Fonts\arialbd.ttf',
                    'C:\Windows\Fonts\arial.ttf',
                    '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
                    '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
                ];
                foreach ($candidates as $c) if (file_exists($c)) { $ttf = $c; break; }
            }
            if ($ttf) {
                imagettftext($bar, 22, 0, 30, 42, $tcol, $ttf, $titleBar['title']);
                imagettftext($bar, 11, 0, 30, 70, $scol, $ttf, $titleBar['subtitle']);
            } else {
                imagestring($bar, 5, 30, 20, $titleBar['title'], $tcol);
                imagestring($bar, 2, 30, 60, $titleBar['subtitle'], $scol);
            }
            $composite = imagecreatetruecolor($w2, $h2 + 100);
            imagefill($composite,0,0,imagecolorallocate($composite,255,255,255));
            imagecopy($composite,$bar,0,0,0,0,$w2,100);
            imagecopy($composite,$im,0,100,0,0,$w2,$h2);
            imagedestroy($im); imagedestroy($bar);
            $im = $composite;
            $h2 += 100;
        }
        ob_start(); imagejpeg($im, null, 92); $jpegBytes = ob_get_clean();
        imagedestroy($im);
        $jpLen = strlen($jpegBytes);

        // Fit on page
        $mx = 36; $my = 60; $availW = $wPt - 2*$mx; $availH = $hPt - 2*$my - ($caption?34:0);
        $scale = min($availW/$w2, $availH/$h2, 1);
        $dw = $w2*$scale; $dh = $h2*$scale;
        $dx = $mx + ($availW - $dw)/2;
        $dy = $my + ($availH - $dh)/2 + ($caption?34:0);

        // Build PDF manually
        $objs = [];
        $buf = "%PDF-1.7\n%âãÏÓ\n";
        $add = function($body) use(&$buf,&$objs){
            $idx = count($objs)+1; $objs[] = strlen($buf);
            $buf .= "{$idx} 0 obj\n{$body}endobj\n";
            return $idx;
        };
        $f1   = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\n");
        $img  = $add("<< /Type /XObject /Subtype /Image /Width {$w2} /Height {$h2} " .
                    "/ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode " .
                    "/Length {$jpLen} >>\nstream\n{$jpegBytes}\nendstream\n");
        $pageContent =
            "q 1 1 1 rg 0 0 {$wPt} {$hPt} re f Q\n" .
            "q 0.85 0.85 0.85 RG 2 w " . ($dx-3) . " " . ($dy-3) . " " . ($dw+6) . " " . ($dh+6) . " re S Q\n" .
            "q {$dw} 0 0 {$dh} {$dx} {$dy} cm /Im1 Do Q\n" .
            "q 0 0 0 RG 0.6 w {$dx} {$dy} {$dw} {$dh} re S Q\n";
        if ($caption) {
            $cw = strlen($caption) * 5.5; // rough Helvetica 11
            $pageContent .= "BT /F1 11 Tf 0 0 0 rg " . ($wPt/2 - $cw/2) . " " .
                            ($dy - 18) . " Td (" . self::esc($caption) . ") Tj ET\n";
        }
        if ($footer) {
            $pageContent .= "q 0.8 0.8 0.8 rg 0 45 {$wPt} 0.5 re f Q\nBT /F1 9 Tf 0.4 0.4 0.4 rg 50 30 Td (" .
                            self::esc($footer) . ") Tj ET\n";
        }
        $pcLen = strlen($pageContent);
        $content = $add("<< /Length {$pcLen} >>\nstream\n{$pageContent}\nendstream\n");
        $resources = "<< /Font << /F1 {$f1} 0 R >> /XObject << /Im1 {$img} 0 R >> >>";
        $page = 0;
        $pages = 0;
        $pages = $add("<< /Type /Pages /Count 1 /Kids [__P1__ 0 R] >>\n");
        $page  = $add("<< /Type /Page /Parent {$pages} 0 R /MediaBox [0 0 {$wPt} {$hPt}] " .
                      "/Resources {$resources} /Contents {$content} 0 R >>\n");
        $buf = str_replace('__P1__', $page, $buf);
        // After str_replace positions changed; rebuild offsets
        $lines = preg_split('/(?<=endobj\n)/', $buf);
        $buf2 = ''; $objs = [];
        foreach ($lines as $c) {
            if ($c === '') continue;
            $objs[] = strlen($buf2); $buf2 .= $c;
        }
        $buf = $buf2;
        $catalog = $add("<< /Type /Catalog /Pages {$pages} 0 R >>\n");
        $xrefPos = strlen($buf);
        $total = count($objs) + 1;
        $buf .= "xref\n0 {$total}\n";
        $buf .= sprintf("%010d %05d f \n", 0, 65535);
        foreach ($objs as $p) $buf .= sprintf("%010d %05d n \n", $p, 0);
        $buf .= "trailer\n<< /Size {$total} /Root {$catalog} 0 R >>\nstartxref\n{$xrefPos}\n%%EOF";
        file_put_contents($pdfPath, $buf);
        return true;
    }
    static function esc($s){ return str_replace(['\\','(',')'], ['\\\\','\\(', '\\)'], (string)$s); }
}

// A2 landscape in pt: 595 x 842 (A4) → A2 landscape ≈ 1684 × 1190. Use 1584 x 1224.
$A2L_W = 1684; $A2L_H = 1190;

echo "→ Building full wrapped PDF (title page + diagram + legend) ...\n";

// ---------------- TITLE PAGE ----------------
$pdf = new MiniPdf();
$titleBody = [];
$titleBody[] = ['PROJECT OVERVIEW', 16, true, 0, [0.051,0.278,0.631]];
$titleBody[] = 'SSRIS (Student Supervisor Research Interaction System) is a web-based research supervision';
$titleBody[] = 'management platform developed for Moshi Cooperative University (MOCU). It streamlines document';
$titleBody[] = 'submissions, feedback iterations, meeting scheduling, progress tracking, and institutional';
$titleBody[] = 'oversight across three distinct user roles: Students, Supervisors, and Administrators.';
$titleBody[] = '';
$titleBody[] = ['DATABASE ARCHITECTURE DESIGN', 16, true, 0, [0.051,0.278,0.631]];
$titleBody[] = 'The SSRIS database is intentionally modelled with SEPARATE role-specific tables rather than';
$titleBody[] = 'a single "users" table. This preserves role-specific attributes, index selectivity, audit,';
$titleBody[] = 'and direct FK integrity for each stakeholder domain. Cardinalities are captured explicitly:';
$titleBody[] = '';
$titleBody[] = ['    •  1:1   STUDENTS ↔ RESEARCH_PROJECTS       (one active project per student)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  1:1   ADMINISTRATORS ↔ RESEARCH_PROJECTS (final sign-off per project)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  1:N   SUPERVISORS → STUDENTS             (supervisor assignment)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  1:N   RESEARCH_PROJECTS → PROPOSALS      (many document versions)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  1:N   SUPERVISORS → FEEDBACK / MEETINGS  (many instances)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  1:N   ADMINISTRATORS → AUDIT_LOGS / SMS_LOGS / MESSAGES / PROGRESS / INTERACTIONS', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = ['    •  M:N   MEETINGS ↔ STUDENTS                (via pivot MEETING_STUDENT + attendance)', 11, false, 0, [0.1,0.1,0.1]];
$titleBody[] = '';
$titleBody[] = ['TABLES (13 logical entities + 1 pivot + 2 logs = 16 tables)', 16, true, 0, [0.906,0.318,0]];
$titleBody[] = '  Role Tables        ADMINISTRATORS, SUPERVISORS, STUDENTS';
$titleBody[] = '  Research Core      RESEARCH_PROJECTS, PROPOSALS, RESEARCH_STAGES, STUDENT_PROGRESS, FEEDBACK';
$titleBody[] = '  Interactions       MEETINGS, MEETING_STUDENT (M:N pivot), MESSAGES, INTERACTIONS';
$titleBody[] = '  Oversight / Logs   SMS_LOGS, AUDIT_LOGS';
$titleBody[] = '';
$titleBody[] = ['KEY INTEGRITY RULES', 16, true, 0, [0.051,0.278,0.631]];
$titleBody[] = '  1. Every table has a BIGINT UNSIGNED auto-increment primary key (PK).';
$titleBody[] = '  2. Composite UNIQUE(student_id, stage_id) on STUDENT_PROGRESS enforces exactly';
$titleBody[] = '     one progress tracker per student per research stage.';
$titleBody[] = '  3. Composite PK(meeting_id, student_id) on MEETING_STUDENT enforces a single';
$titleBody[] = '     attendance row per student per meeting (M:N junction integrity).';
$titleBody[] = '  4. ADMINISTRATORS has 1:N oversight FKs across AUDIT_LOGS, SMS_LOGS, INTERACTIONS,';
$titleBody[] = '     STUDENT_PROGRESS and MESSAGES — every critical action is audit-traced.';
$titleBody[] = '  5. All FK columns are indexed; nullable FKs preserve referential integrity on soft deletes.';
$titleBody[] = '';
$titleBody[] = ['TECHNOLOGY STACK', 16, true, 0, [0.051,0.278,0.631]];
$titleBody[] = '  •  Database Engine : MySQL 8+ / MariaDB 10.11';
$titleBody[] = '  •  Storage Engine  : InnoDB (ACID, FK constraints, transactions)';
$titleBody[] = '  •  Encoding         : utf8mb4_unicode_ci (full Unicode + emoji)';
$titleBody[] = '  •  Backend          : Laravel 12.x (PHP 8.2) — Eloquent ORM / Migrations';
$titleBody[] = '  •  SMS Notifications: NextSMS Tanzania API (sms_logs typed triggers)';
$titleBody[] = '  •  Diagrams         : PlantUML + Mermaid.js (this PDF: rendered via PlantUML server)';

// Build title page first as a simple image-jpeg overlay document.
$titlePdfPath = __DIR__ . '/docs/_tmp_title.pdf';
$coverPdf = new MiniPdf();

// Generate a nice cover JPG directly with GD so MiniPdf can use the writeImagePage pipeline.
$coverW = 2400; $coverH = 1800;
$cover = imagecreatetruecolor($coverW,$coverH);
$cWhite  = imagecolorallocate($cover,255,255,255);
$cNavy   = imagecolorallocate($cover,13,71,161);
$cNavy2  = imagecolorallocate($cover,26,35,126);
$cGold   = imagecolorallocate($cover,255,213,79);
$cInk    = imagecolorallocate($cover,15,15,15);
$cMuted  = imagecolorallocate($cover,80,80,80);
$cAccent = imagecolorallocate($cover,230,81,0);
imagefill($cover,0,0,$cWhite);
// Top band
imagefilledrectangle($cover,0,0,$coverW,340,$cNavy);
imagefilledrectangle($cover,0,340,$coverW,352,$cGold);
// Title text (use TTF if available)
function _findTTF() {
    foreach ([
        'C:\Windows\Fonts\timesbd.ttf',
        'C:\Windows\Fonts\arialbd.ttf',
        'C:\Windows\Fonts\arial.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
    ] as $f) if (file_exists($f)) return $f;
    return '';
}
function _findTTFRegular() {
    foreach ([
        'C:\Windows\Fonts\times.ttf',
        'C:\Windows\Fonts\arial.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSerif-Regular.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSerif.ttf',
    ] as $f) if (file_exists($f)) return $f;
    return '';
}
$ttfb = _findTTF();
$ttfr = _findTTFRegular();
if ($ttfb) {
    imagettftext($cover, 62, 0, 70, 150, $cWhite, $ttfb, 'SSRIS — Entity-Relationship Diagram');
    imagettftext($cover, 30, 0, 70, 210, $cGold,  $ttfr, 'Database Architecture · Separate Role Tables · 1:1 · 1:N · M:N');
    imagettftext($cover, 22, 0, 70, 258, imagecolorallocate($cover,220,230,255), $ttfr,
        'Moshi Cooperative University (MOCU) — Student Supervisor Research Interaction System');
} else {
    imagestring($cover, 5, 70, 100, 'SSRIS Entity-Relationship Diagram', $cWhite);
    imagestring($cover, 3, 70, 160, 'Database Architecture 1:1  1:N  M:N', $cGold);
}
// Body lines
$y = 420;
function _bodyLine(&$cover,$y,$title,$body,$ttfb,$ttfr,$cNavy2,$cInk,$cMuted,$cAccent=null) {
    if ($ttfb) {
        imagettftext($cover, 22, 0, 70, $y, $cNavy2, $ttfb, $title);
        imagettftext($cover, 18, 0, 80, $y+30, $cInk,   $ttfr, $body);
    } else {
        imagestring($cover,4,70,$y-10,$title,$cNavy2);
        imagestring($cover,2,80,$y+10,$body,$cInk);
    }
    return $y + 78;
}
$y = _bodyLine($cover,$y,
    '♦ DATABASE DESIGN',
    'Role-separated tables (3 user tables) for role-specific attributes, index selectivity, and clear FK integrity.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ CARDINALITIES',
    '1:1 (Student ↔ Project), 1:N (Supervisor → Students / Feedback / Meetings), M:N via MEETING_STUDENT pivot.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ ADMIN OVERSIGHT',
    'Administrator has 1:N FKs over AUDIT_LOGS, SMS_LOGS, INTERACTIONS, STUDENT_PROGRESS and MESSAGES.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ PIVOT (COMPOSITE PK)',
    'MEETING_STUDENT: PK(meeting_id, student_id) + attendance state (invited | accepted | attended | absent).',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ STAGE-GATED PROGRESS',
    'STUDENT_PROGRESS UNIQUE(student_id, stage_id): exactly one tracker per student per research stage.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ SMS FIRST NOTIFICATIONS',
    'All triggers (submission, approval, feedback, meeting-reminder, stage-transition) logged in SMS_LOGS.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);
$y = _bodyLine($cover,$y,
    '♦ FULL AUDIT TRAIL',
    'AUDIT_LOGS stores CRUD + APPROVE/REJECT + LOGIN events with JSON old/new diffs + IP/user-agent.',
    $ttfb,$ttfr,$cNavy2,$cInk,$cMuted);

// Version bar
imagefilledrectangle($cover,0,$coverH-50,$coverW,$coverH,$cNavy2);
if ($ttfr) {
    imagettftext($cover, 14, 0, 70, $coverH-18, $cGold, $ttfr,
        'SSRIS Database Architecture — Diagram Version 1.0 — 2026 — Confidential: MOCU Academic Use Only');
} else {
    imagestring($cover,2,70,$coverH-30,'SSRIS ER Diagram v1.0  2026  MOCU Internal Use Only', $cGold);
}
$coverPng = __DIR__ . '/docs/_tmp_cover.png';
imagepng($cover,$coverPng,9);
imagedestroy($cover);

// Render Cover page PDF
$coverWriter = new MiniPdf();
$coverWriter->writeImagePage(
    $titlePdfPath,
    $A2L_W, $A2L_H,
    $coverPng,
    null,
    'Student Supervisor Research Interaction System  •  ERD — Cover Page  •  MOCU © 2026',
    null
);
echo "   ✔ Cover page rendered.\n";

// ---------------- DIAGRAM PAGE ----------------
$diagramPdfPath = __DIR__ . '/docs/_tmp_diagram.pdf';
if ($pngBytes !== false) {
    $dw = new MiniPdf();
    $ok = $dw->writeImagePage(
        $diagramPdfPath,
        $A2L_W, $A2L_H,
        $OUT_PNG,
        'Figure 1 — SSRIS Database Architecture · Full Entity-Relationship Diagram with all PKs, FKs, and Cardinalities',
        'Student Supervisor Research Interaction System  •  PlantUML-rendered ERD  •  16 Tables  •  MOCU 2026',
        [
            'title'    => 'SSRIS — Complete Entity-Relationship Diagram (ERD)',
            'subtitle' => '16 logical entities · 3 separate role tables · PKs / FKs · 1:1 · 1:N · M:N (pivot) · Admin oversight'
        ]
    );
    if ($ok) echo "   ✔ Diagram page rendered.\n";
}

// ---------------- LEGEND / COLPHON PAGE ----------------
$legendPng = __DIR__ . '/docs/_tmp_legend.png';
$lw = 2400; $lh = 1800;
$lg = imagecreatetruecolor($lw,$lh);
imagefill($lg,0,0,imagecolorallocate($lg,255,255,255));
imagefilledrectangle($lg,0,0,$lw,140,$cNavy2);
imagefilledrectangle($lg,0,140,$lw,152,$cGold);
if ($ttfb) {
    imagettftext($lg, 48, 0, 70, 90, $cWhite, $ttfb, 'SSRIS — Relationship Legend & Table Summary');
    imagettftext($lg, 20, 0, 70, 128, $cGold,  $ttfr, 'Cardinalities · Attribute Domains · PK / FK · Pivot · Admin Oversight');
}

// Swatches / legend header
$yy = 200;
$swatches = [
    ['#0D47A1', 'ADMINISTRATORS  (PK: admin_id)',   'Role table · System-level oversight · Final approvals'],
    ['#1565C0', 'SUPERVISORS      (PK: supervisor_id)','Role table · Supervisees cap · Specialization'],
    ['#1976D2', 'STUDENTS         (PK: student_id)',    'Role table · reg_number UNIQUE · program info'],
    ['#E65100', 'RESEARCH_PROJECTS (PK: project_id)',   '1:1 with Student · status enum · supervisor FK'],
    ['#EF6C00', 'PROPOSALS        (PK: proposal_id)',   '1:N from Project · version · document_type enum'],
    ['#6A1B9A', 'RESEARCH_STAGES  (PK: stage_id)',      'Master lookup · step_number UNIQUE (1..N)'],
    ['#4A148C', 'STUDENT_PROGRESS (PK: progress_id)',   '1:1 per(student, stage) · completion % · approved_by'],
    ['#F57C00', 'FEEDBACK         (PK: feedback_id)',   'iteration_number · priority enum · status enum'],
    ['#2E7D32', 'MEETINGS         (PK: meeting_id)',    'datetime · type · status · created_by_supervisor_id'],
    ['#558B2F', 'MEETING_STUDENT  (PK: composite)',     'M:N PIVOT · PK(meeting_id, student_id) · attendance'],
    ['#00695C', 'MESSAGES         (PK: message_id)',    'Sender/Receiver FKs across all 3 roles · is_read'],
    ['#00838F', 'INTERACTIONS     (PK: interaction_id)','Action log · proposal/feedback/progress FKs · status'],
    ['#AD1457', 'SMS_LOGS         (PK: sms_log_id)',    'All triggers · NextSMS provider response · cost tracking'],
    ['#880E4F', 'AUDIT_LOGS       (PK: audit_log_id)',  'Full CRUD/APPROVE/LOGIN · JSON diffs · IP + UA'],
];
$col1X = 70;  $col2X = 1250;
$rowH  = 70;
$i = 0;
$colY1 = 200; $colY2 = 200;
$col1Rows = array_slice($swatches,0,7);
$col2Rows = array_slice($swatches,7,7);
function _swatch(&$img,$x,$y,$row,$ttfb,$ttfr,$rowH,$lw) {
    [$hex,$label,$desc] = $row;
    $rgb = sscanf($hex,"#%02x%02x%02x");
    $c   = imagecolorallocate($img,$rgb[0],$rgb[1],$rgb[2]);
    imagefilledrectangle($img,$x,$y,$x+40,$y+40,$c);
    imagerectangle($img,$x,$y,$x+40,$y+40, imagecolorallocate($img,0,0,0));
    if ($ttfb) {
        imagettftext($img, 16, 0, $x+60, $y+24, imagecolorallocate($img,0,0,0), $ttfb, $label);
        imagettftext($img, 13, 0, $x+60, $y+50, imagecolorallocate($img,80,80,80), $ttfr, $desc);
    } else {
        imagestring($img,4,$x+60,$y+5,$label,0);
        imagestring($img,2,$x+60,$y+28,$desc,imagecolorallocate($img,80,80,80));
    }
}
foreach ($col1Rows as $r) { _swatch($lg,$col1X,$colY1,$r,$ttfb,$ttfr,$rowH,$lw); $colY1 += $rowH; }
foreach ($col2Rows as $r) { _swatch($lg,$col2X,$colY2,$r,$ttfb,$ttfr,$rowH,$lw); $colY2 += $rowH; }

// Bottom half: cardinalities cheatsheet
$cardY = max($colY1,$colY2) + 40;
imagefilledrectangle($lg,70,$cardY-34,$lw-70,$cardY,imagecolorallocate($lg,13,71,161));
if ($ttfb) imagettftext($lg,20,0,90,$cardY-6,$cGold,$ttfb,'RELATIONSHIP CARDINALITIES — QUICK REFERENCE');
$cardY += 20;
$cards = [
    ['STUDENTS ↔ RESEARCH_PROJECTS',           '1:1',       'A student owns at most one active research project.'],
    ['ADMINISTRATORS ↔ RESEARCH_PROJECTS',     '0..1 : 1',  'An administrator provides (at most one) final sign-off.'],
    ['SUPERVISORS → STUDENTS',                 '1:N',       'One supervisor supervises many students; FK in STUDENTS.'],
    ['RESEARCH_PROJECTS → PROPOSALS',          '1:N',       'Many document versions (concept → proposal → chapters → thesis).'],
    ['SUPERVISORS → FEEDBACK / MEETINGS',      '1:N',       'Supervisor creates many feedback items and meetings.'],
    ['STUDENTS → FEEDBACK / PROPOSALS',        '1:N',       'Student receives many feedback items; submits many documents.'],
    ['RESEARCH_STAGES → STUDENT_PROGRESS',     '1:N',       'Each stage definition applies to many student progress records.'],
    ['STUDENTS → STUDENT_PROGRESS',            '1:N',       'A student has one progress row PER STAGE (composite UNIQUE).'],
    ['MEETINGS ↔ STUDENTS',                    'M:N',       'Junction: MEETING_STUDENT(meeting_id, student_id) — PK.'],
    ['ADMINISTRATORS → AUDIT_LOGS',            '1:N',       'Admin actor; oversees every table via this log.'],
    ['ADMINISTRATORS → SMS_LOGS / MESSAGES / INTERACTIONS / PROGRESS', '1:N', 'Oversight / escalation / stage unlocks.'],
];
foreach ($cards as $c) {
    $rgb = [0,0,0];
    if ($ttfb) {
        imagettftext($lg, 14, 0, 90,  $cardY, imagecolorallocate($lg,26,35,126), $ttfb, $c[0]);
        imagettftext($lg, 14, 0, 880, $cardY, imagecolorallocate($lg,230,81,0), $ttfb, str_pad($c[1],8,' ',STR_PAD_BOTH));
        imagettftext($lg, 12, 0, 1010,$cardY, imagecolorallocate($lg,50,50,50), $ttfr, $c[2]);
    }
    $cardY += 36;
}
// Footer
imagefilledrectangle($lg,0,$lh-50,$lw,$lh,$cNavy2);
if ($ttfr) imagettftext($lg, 14, 0, 70, $lh-18, $cGold, $ttfr,
    'SSRIS Database Architecture — Legend & Table Summary — Diagram Version 1.0 — MOCU 2026');
imagepng($lg,$legendPng,9); imagedestroy($lg);
$legendPdfPath = __DIR__ . '/docs/_tmp_legend.pdf';
$lw_writer = new MiniPdf();
$lw_writer->writeImagePage(
    $legendPdfPath,
    $A2L_W, $A2L_H,
    $legendPng,
    null,
    'Student Supervisor Research Interaction System  •  Legend + Table Summary  •  MOCU © 2026',
    null
);
echo "   ✔ Legend / Colophon page rendered.\n";

/* ------------------------------------------------------------------ *
 * STEP 5 — Concatenate the 3 PDFs (Cover, Diagram, Legend) by naive  *
 * PDF concatenation: re-number objects, merge pages kids, fix xref.  *
 * ------------------------------------------------------------------ */
function concatPdfs(array $inputs, $outPath) {
    $allPages = []; $allObjStreams = [];
    $totalObj = 1;
    $catalogRefs = [];
    $catalogPagesMap = [];
    $replacementsPerFile = [];
    foreach ($inputs as $idx => $in) {
        if (!file_exists($in)) continue;
        $raw = file_get_contents($in);
        // Extract objects: match "NN 0 obj ... endobj"
        if (!preg_match_all('/(\d+)\s+0\s+obj\s*(.*?)\s*endobj/s', $raw, $mm, PREG_SET_ORDER)) continue;
        $localMap = [];
        $catObj = null; $pagesObj = null; $pageKidsLocal=[];
        // First pass: renumber local objects
        foreach ($mm as $m) {
            $localNum = (int)$m[1];
            $newNum = $totalObj++;
            $localMap[$localNum] = $newNum;
            $body = $m[2];
            // Detect Catalog and Pages dictionaries
            if (strpos($body, '/Type /Catalog') !== false) $catObj = $newNum;
            if (strpos($body, '/Type /Pages') !== false) $pagesObj = $newNum;
            if (strpos($body, '/Type /Page') !== false && strpos($body, '/Parent') !== false) {
                $pageKidsLocal[$newNum] = true;
            }
            $allObjStreams[$newNum] = $body;
        }
        $replacementsPerFile[] = $localMap;
        $catalogRefs[] = $catObj;
        $catalogPagesMap[] = $pagesObj;
    }
    // Build new Pages object combining every Page (not the file-level Pages dicts)
    $allPageNums = [];
    foreach ($allObjStreams as $num => $body) {
        if (strpos($body,'/Type /Page')!==false && strpos($body,'/Parent')!==false) {
            $allPageNums[] = $num;
        }
    }
    // Remap old page Parents → new global Pages object
    $globalPagesNum = $totalObj++;
    $kidsStr = '';
    foreach ($allPageNums as $pn) $kidsStr .= "{$pn} 0 R ";
    $allObjStreams[$globalPagesNum] =
        "<< /Type /Pages /Count " . count($allPageNums) . " /Kids [" . trim($kidsStr) . "] >>\n";
    // Rewrite each object body for new object numbers
    $rewrite = function($body) use($replacementsPerFile) {
        foreach ($replacementsPerFile as $map) {
            $body = preg_replace_callback('/(\b\d+\b)(\s+0\s+R)/', function($m) use($map) {
                $n = (int)$m[1];
                return (isset($map[$n]) ? $map[$n] : $n) . $m[2];
            }, $body);
        }
        // Point every /Parent XX 0 R in Page dicts to globalPagesNum (passed via `use` by ref -> do outside)
        return $body;
    };
    foreach ($allObjStreams as $num => $body) {
        $allObjStreams[$num] = $rewrite($body);
    }
    // Now swap Page parents to the global Pages obj
    foreach ($allPageNums as $pn) {
        $allObjStreams[$pn] = preg_replace(
            '/\/Parent\s+\d+\s+0\s+R/',
            "/Parent {$globalPagesNum} 0 R",
            $allObjStreams[$pn]
        );
    }
    // Remove file-level Catalogs and old Pages dicts and build a single Catalog.
    unset($allObjStreams[ $catalogRefs[0] ]);
    $newCatRef = $totalObj++;
    $allObjStreams[$newCatRef] =
        "<< /Type /Catalog /Pages {$globalPagesNum} 0 R /ViewerPreferences << /PageMode /UseNone >> >>\n";
    // Build output
    ksort($allObjStreams);
    $buf = "%PDF-1.7\n%âãÏÓ\n";
    $xref = [];
    $add = function($num,$body) use(&$buf,&$xref){
        $xref[$num] = strlen($buf);
        $buf .= "{$num} 0 obj\n{$body}endobj\n";
    };
    foreach ($allObjStreams as $num => $body) {
        // Strip leading/trailing whitespace + ensure single newline before endobj
        $body = rtrim($body, "\n") . "\n";
        $add($num, $body);
    }
    $xrefPos = strlen($buf);
    $max = max(array_keys($xref)) + 1;
    $buf .= "xref\n0 {$max}\n";
    $buf .= sprintf("%010d %05d f \n", 0, 65535);
    for ($i = 1; $i < $max; $i++) {
        if (isset($xref[$i])) $buf .= sprintf("%010d %05d n \n", $xref[$i], 0);
        else                   $buf .= sprintf("%010d %05d f \n", 0, 0);
    }
    $buf .= "trailer\n<< /Size {$max} /Root {$newCatRef} 0 R >>\nstartxref\n{$xrefPos}\n%%EOF";
    file_put_contents($outPath, $buf);
}

$parts = [];
if (file_exists($titlePdfPath))      $parts[] = $titlePdfPath;
if (file_exists($diagramPdfPath))    $parts[] = $diagramPdfPath;
if (file_exists($legendPdfPath))     $parts[] = $legendPdfPath;
if (count($parts) >= 1) {
    concatPdfs($parts, $OUT_PDF);
    $sz = filesize($OUT_PDF);
    echo "\n✅ FINAL PDF written to: " . $OUT_PDF .
         " (" . number_format($sz / 1024, 1) . " KB, " . count($parts) . " pages)\n";
} else {
    fwrite(STDERR, "No PDF parts were produced.\n"); exit(2);
}

// Cleanup temp files
foreach ([$titlePdfPath,$diagramPdfPath,$legendPdfPath,$coverPng,$legendPng] as $t) {
    if (file_exists($t)) @unlink($t);
}

echo "\n✅ All done. Outputs:\n";
foreach (glob(__DIR__ . '/docs/SSRIS_ER_Diagram*') as $f) {
    echo "   → " . basename($f) . "   " . number_format(filesize($f) / 1024, 1) . " KB\n";
}
