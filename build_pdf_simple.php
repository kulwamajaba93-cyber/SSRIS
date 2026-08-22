<?php
echo "=== PHP Works ===\n";
error_reporting(E_ALL); ini_set('display_errors', 1);
$PUML_PATH = __DIR__ . '/docs/ssris_er_diagram.puml';
$OUT_PDF   = __DIR__ . '/docs/SSRIS_ER_Diagram.pdf';
$OUT_PNG   = __DIR__ . '/docs/SSRIS_ER_Diagram.png';

echo "→ Files exist check: \n";
echo "  puml? " . (file_exists($PUML_PATH) ? 'YES' : 'NO') . "\n";
echo "  gd?   " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";
echo "  zlib? " . (extension_loaded('zlib') ? 'YES' : 'NO') . "\n";
echo "  curl? " . (extension_loaded('curl') ? 'YES' : 'NO') . "\n";
echo "  openssl? " . (extension_loaded('openssl') ? 'YES' : 'NO') . "\n";
echo "\n→ Encoding puml ...\n";

$src = file_get_contents($PUML_PATH);
$defl = gzdeflate($src, 9);
$map = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
$enc = '';
$n = strlen($defl);
for ($i = 0; $i < $n; $i += 3) {
    $b1 = ord($defl[$i]);
    $b2 = ($i+1<$n)? ord($defl[$i+1]) : 0;
    $b3 = ($i+2<$n)? ord($defl[$i+2]) : 0;
    $c1 = $b1 >> 2;
    $c2 = (($b1 & 0x3) << 4) | ($b2 >> 4);
    $c3 = (($b2 & 0xF) << 2) | ($b3 >> 6);
    $c4 = $b3 & 0x3F;
    $enc .= $map[$c1].$map[$c2].$map[$c3].$map[$c4];
    if ($i+1>=$n) $enc = substr($enc,0,-2);
    elseif ($i+2>=$n) $enc = substr($enc,0,-1);
}
$pngUrl = "https://www.plantuml.com/plantuml/png/0/$enc";
$pdfUrl = "https://www.plantuml.com/plantuml/pdf/0/$enc";
echo "Encoded OK. strlen = ".strlen($enc)."\n";
echo "PDF URL: $pdfUrl\n";
echo "\n→ Fetching PlantUML PDF directly via file_get_contents + context ...\n";
$ctx = stream_context_create([
    'http'=>['timeout'=>180,'user_agent'=>'SSRIS-ERD/1.0','follow_location'=>1,'max_redirects'=>5],
    'ssl' =>['verify_peer'=>false,'verify_peer_name'=>false]
]);
$pdf = @file_get_contents($pdfUrl,false,$ctx);
if ($pdf!==false && strpos($pdf,'%PDF-')===0) {
    file_put_contents(__DIR__ . '/docs/SSRIS_ER_Diagram.pdf', $pdf);
    echo "✅ SSRIS_ER_Diagram.pdf written (".number_format(strlen($pdf)/1024,1)." KB)\n";
} else {
    echo "❌ direct PDF failed. strlen(pdf) = " . strlen($pdf) . "\n";
    echo "→ Trying PNG ...\n";
    $png = @file_get_contents($pngUrl,false,$ctx);
    if ($png!==false && strlen($png)>2000) {
        file_put_contents($OUT_PNG,$png);
        $info = getimagesize($OUT_PNG);
        echo "✅ PNG OK {$info[0]}x{$info[1]} (".number_format(strlen($png)/1024,1)." KB)\n";
        // Convert PNG to PDF via GD + minimal writer.
        echo "→ Building PDF from PNG via GD ...\n";
        $im = imagecreatefrompng($OUT_PNG);
        $w2 = imagesx($im); $h2 = imagesy($im);
        $bg = imagecreatetruecolor($w2,$h2);
        imagefill($bg,0,0,imagecolorallocate($bg,255,255,255));
        imagecopy($bg,$im,0,0,0,0,$w2,$h2); imagedestroy($im); $im=$bg;
        // Title bar
        $bar = imagecreatetruecolor($w2,100);
        imagefill($bar,0,0,imagecolorallocate($bar,13,71,161));
        imagefilledrectangle($bar,0,92,$w2,100,imagecolorallocate($bar,255,213,79));
        $ttfb = '';
        foreach (['C:\Windows\Fonts\timesbd.ttf','C:\Windows\Fonts\arialbd.ttf','C:\Windows\Fonts\arial.ttf'] as $t) if (file_exists($t)) {$ttfb=$t;break;}
        $ttfr = '';
        foreach (['C:\Windows\Fonts\times.ttf','C:\Windows\Fonts\arial.ttf'] as $t) if (file_exists($t)) {$ttfr=$t;break;}
        $wht = imagecolorallocate($bar,255,255,255);
        $gld = imagecolorallocate($bar,255,213,79);
        if ($ttfb) {
            imagettftext($bar,24,0,24,44,$wht,$ttfb,'SSRIS — Database Architecture Entity-Relationship Diagram');
            imagettftext($bar,12,0,24,74,$gld,$ttfr,'Moshi Cooperative University (MOCU) · Separate Role Tables · 1:1 · 1:N · M:N via pivot');
        } else {
            imagestring($bar,5,24,24,'SSRIS ER Diagram',$wht);
            imagestring($bar,2,24,60,'MOCU  Separate Role Tables  1:1  1:N  M:N',$gld);
        }
        $composite = imagecreatetruecolor($w2,$h2+100);
        imagefill($composite,0,0,imagecolorallocate($composite,255,255,255));
        imagecopy($composite,$bar,0,0,0,0,$w2,100);
        imagecopy($composite,$im,0,100,0,0,$w2,$h2);
        imagedestroy($im); imagedestroy($bar); $im=$composite;
        $h2 += 100;
        // Page: A2 landscape pts 1684 x 1190
        $WP = 1684; $HP = 1190;
        $mx = 36; $my = 60; $capH = 34;
        $aW = $WP-2*$mx; $aH = $HP-2*$my-$capH;
        $scale = min($aW/$w2,$aH/$h2,1);
        $dw = $w2*$scale; $dh = $h2*$scale;
        $dx = $mx + ($aW-$dw)/2;
        $dy = $my + ($aH-$dh)/2 + $capH;
        ob_start(); imagejpeg($im,null,92); $jpeg = ob_get_clean(); imagedestroy($im);
        $jpLen = strlen($jpeg);
        $caption = 'Figure 1 — SSRIS Full Entity-Relationship Diagram (ERD) — 16 Tables with PK/FK & Cardinalities';
        $footer  = 'Student Supervisor Research Interaction System  •  Database Architecture  •  MOCU © 2026';
        $content =
            "q 1 1 1 rg 0 0 $WP $HP re f Q\n".
            "q 0.85 0.85 0.85 RG 2 w ".($dx-3)." ".($dy-3)." ".($dw+6)." ".($dh+6)." re S Q\n".
            "q $dw 0 0 $dh $dx $dy cm /Im1 Do Q\n".
            "q 0 0 0 RG 0.6 w $dx $dy $dw $dh re S Q\n";
        // Rough center caption (Helvetica ~5.5pt per char)
        $cw = strlen($caption)*5.5;
        $content .= "BT /F1 11 Tf 0 0 0 rg ".($WP/2 - $cw/2)." ".($dy-18)." Td (".str_replace(['\\','(',')'],['\\\\','\\(', '\\)'],$caption).") Tj ET\n";
        $content .= "q 0.8 0.8 0.8 rg 0 45 $WP 0.5 re f Q\nBT /F1 9 Tf 0.4 0.4 0.4 rg 50 30 Td (".str_replace(['\\','(',')'],['\\\\','\\(', '\\)'],$footer).") Tj ET\n";
        $pcLen = strlen($content);

        // Build PDF objects
        $buf = "%PDF-1.7\n%âãÏÓ\n";
        $objs = [];
        $add = function($body) use(&$buf,&$objs){
            $idx = count($objs)+1; $objs[] = strlen($buf);
            $buf .= "{$idx} 0 obj\n{$body}endobj\n";
            return $idx;
        };
        $f1  = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\n");
        $im1 = $add("<< /Type /XObject /Subtype /Image /Width {$w2} /Height {$h2} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$jpLen} >>\nstream\n{$jpeg}\nendstream\n");
        $con = $add("<< /Length {$pcLen} >>\nstream\n{$content}\nendstream\n");
        $res = "<< /Font << /F1 {$f1} 0 R >> /XObject << /Im1 {$im1} 0 R >> >>";
        $pages = 9999;
        $page  = $add("<< /Type /Page /Parent {$pages} 0 R /MediaBox [0 0 {$WP} {$HP}] /Resources {$res} /Contents {$con} 0 R >>\n");
        $pages = $add("<< /Type /Pages /Count 1 /Kids [{$page} 0 R] >>\n");
        $buf = str_replace('9999 0 R', "{$pages} 0 R", $buf);
        // Rebuild offsets
        $lines = preg_split('/(?<=endobj\n)/', $buf);
        $buf2=''; $objs=[];
        foreach ($lines as $c) { if($c==='') continue; $objs[] = strlen($buf2); $buf2.=$c; }
        $buf = $buf2;
        $cat = $add("<< /Type /Catalog /Pages {$pages} 0 R >>\n");
        $xrefPos = strlen($buf);
        $total = count($objs)+1;
        $buf .= "xref\n0 {$total}\n";
        $buf .= sprintf("%010d %05d f \n",0,65535);
        foreach ($objs as $p) $buf .= sprintf("%010d %05d n \n",$p,0);
        $buf .= "trailer\n<< /Size {$total} /Root {$cat} 0 R >>\nstartxref\n{$xrefPos}\n%%EOF";
        file_put_contents($OUT_PDF,$buf);
        echo "✅ SSRIS_ER_Diagram.pdf written from PNG (".number_format(filesize($OUT_PDF)/1024,1)." KB)\n";
    } else {
        echo "❌ PNG failed too. strlen=".strlen($png)."\n";
        echo "→ Use the ssris_er_diagram.html viewer (open in browser, click Print to PDF).\n";
    }
}
