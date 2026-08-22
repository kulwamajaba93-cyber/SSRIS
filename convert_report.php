<?php
$mdPath = __DIR__ . '/docs/project_report.md';
$htmlPath = __DIR__ . '/docs/project_report.html';

$md = file_get_contents($mdPath);
$lines = explode("\n", $md);

function convertInline($text) {
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/s', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);
    return $text;
}

function findChapterStart($lines) {
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], '## CHAPTER ONE: INTRODUCTION') === 0) {
            return $i;
        }
    }
    return 0;
}

$startIdx = findChapterStart($lines);

$inUl = false;
$inOl = false;
$inP = false;
$contentHtml = '';
$closeUl = '</ul>';
$closeOl = '</ol>';
$closeP = '</p>';

function closeBlocks(&$contentHtml, &$inUl, &$inOl, &$inP, $closeUl, $closeOl, $closeP) {
    if ($inP) { $contentHtml .= $closeP; $inP = false; }
    if ($inUl) { $contentHtml .= $closeUl; $inUl = false; }
    if ($inOl) { $contentHtml .= $closeOl; $inOl = false; }
}

$chapterClassAdded = false;

for ($i = $startIdx; $i < count($lines); $i++) {
    $line = rtrim($lines[$i]);
    $trimmed = trim($line);

    if ($trimmed === '') {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        continue;
    }

    if ($trimmed === '---') {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        $contentHtml .= '<hr>';
        continue;
    }

    // H2: Chapters, References, Appendices  (## )
    if (preg_match('/^## (.+)$/', $trimmed, $m)) {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        $text = convertInline($m[1]);
        $rawText = $m[1];

        if (strpos($rawText, 'CHAPTER ONE') === 0) {
            $contentHtml .= '<div class="page" id="ch1"><h2 class="chapter-title" id="ch1-title">' . $text . '</h2>';
        } elseif (strpos($rawText, 'CHAPTER TWO') === 0) {
            $contentHtml .= '</div><div class="page" id="ch2"><h2 class="chapter-title">' . $text . '</h2>';
        } elseif (strpos($rawText, 'CHAPTER THREE') === 0) {
            $contentHtml .= '</div><div class="page" id="ch3"><h2 class="chapter-title">' . $text . '</h2>';
        } elseif (strpos($rawText, 'CHAPTER FOUR') === 0) {
            $contentHtml .= '</div><div class="page" id="ch4"><h2 class="chapter-title">' . $text . '</h2>';
        } elseif (strpos($rawText, 'CHAPTER FIVE') === 0) {
            $contentHtml .= '</div><div class="page" id="ch5"><h2 class="chapter-title">' . $text . '</h2>';
        } elseif (strpos($rawText, 'References') === 0) {
            $contentHtml .= '</div><div class="page" id="refs"><h2>' . $text . '</h2>';
        } elseif (strpos($rawText, 'APPENDICES') === 0) {
            $contentHtml .= '</div><div class="page appendix" id="apps"><h2>' . $text . '</h2>';
        } else {
            $contentHtml .= '<h2>' . $text . '</h2>';
        }
        continue;
    }

    // H3: 1.1 Background, etc. (### )
    if (preg_match('/^### (.+)$/', $trimmed, $m)) {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        $text = convertInline($m[1]);
        $raw = $m[1];
        $anchors = [
            '1.1 Background' => 's11',
            '1.2 Statement of the Problem' => 's12',
            '1.3 Objectives of the Project' => 's13',
            '1.4 Project Questions' => 's14',
            '1.5 Significance of the Study' => 's15',
            '1.6 Scope of the Study' => 's16',
            '1.7 Limitation of the Study' => 's17',
            '1.8 Project Framework' => 's18',
            '2.1 Literature Review' => 's21',
            '2.2 Theoretical Review' => 's22',
            '2.3 Empirical Review' => 's23',
            '2.4 Critique' => 's24',
            '2.5 Knowledge Gap' => 's25',
            '3.1 Study Area' => 's31',
            '3.2 Project Design and Approach' => 's32',
            '3.3 Target Population' => 's33',
            '3.4 Sampling' => 's34',
            '3.5 Data Collection' => 's35',
            '3.6 Data Analysis' => 's36',
            '3.7 Ethical Consideration' => 's37',
            '4.1 Results' => 's41',
            '4.2 Discussion' => 's42',
            '5.1 Summary' => 's51',
            '5.2 Conclusion' => 's52',
            '5.3 Recommendations' => 's53',
        ];
        $id = isset($anchors[$raw]) ? ' id="' . $anchors[$raw] . '"' : '';
        $contentHtml .= '<h3' . $id . '>' . $text . '</h3>';
        continue;
    }

    // H4: 1.3.1 etc (#### )
    if (preg_match('/^#### (.+)$/', $trimmed, $m)) {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        $text = convertInline($m[1]);
        $contentHtml .= '<h4>' . $text . '</h4>';
        continue;
    }

    // Table header/simple markdown table detection - skip detailed for now
    if (preg_match('/^\|.+\|$/', $trimmed)) {
        closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
        // Skip table separator
        $next = $i + 1;
        if (isset($lines[$next]) && preg_match('/^\|[\s:\-|]+\|$/', trim($lines[$next]))) {
            $i++; // skip separator
        }
        // Convert just this line to a simple paragraph row
        $cells = array_map('trim', explode('|', trim($trimmed, '|')));
        $rowHtml = '';
        foreach ($cells as $c) {
            $rowHtml .= '<td>' . convertInline($c) . '</td>';
        }
        $contentHtml .= '<table><tr>' . $rowHtml . '</tr></table>';
        continue;
    }

    // Unordered list: - or *
    if (preg_match('/^\s*[\-*]\s+(.+)$/', $trimmed, $m)) {
        if ($inP) { $contentHtml .= $closeP; $inP = false; }
        if ($inOl) { $contentHtml .= $closeOl; $inOl = false; }
        if (!$inUl) { $contentHtml .= '<ul>'; $inUl = true; }
        $contentHtml .= '<li>' . convertInline($m[1]) . '</li>';
        continue;
    }

    // Ordered list: 1. or 1)
    if (preg_match('/^\s*\d+[\.\)]\s+(.+)$/', $trimmed, $m)) {
        if ($inP) { $contentHtml .= $closeP; $inP = false; }
        if ($inUl) { $contentHtml .= $closeUl; $inUl = false; }
        if (!$inOl) { $contentHtml .= '<ol>'; $inOl = true; }
        $contentHtml .= '<li>' . convertInline($m[1]) . '</li>';
        continue;
    }

    // Paragraph (default)
    if (!$inP) {
        if ($inUl) { $contentHtml .= $closeUl; $inUl = false; }
        if ($inOl) { $contentHtml .= $closeOl; $inOl = false; }
        $contentHtml .= '<p>';
        $inP = true;
    } else {
        $contentHtml .= ' ';
    }
    $contentHtml .= convertInline($trimmed);
}

closeBlocks($contentHtml, $inUl, $inOl, $inP, $closeUl, $closeOl, $closeP);
$contentHtml .= '</div>'; // close last page

$head = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRIS Project Report</title>
    <style>
        @page { size: A4; margin: 2.54cm; }
        body { font-family: \'Times New Roman\', Times, serif; font-size: 12pt; line-height: 1.6; margin: 0; padding: 0; color: #000; }
        .page { min-height: 29.7cm; padding: 2.54cm; page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        h1 { font-size: 16pt; font-weight: bold; text-align: center; margin: 24pt 0; text-transform: uppercase; }
        h2 { font-size: 14pt; font-weight: bold; margin: 18pt 0 12pt 0; text-transform: uppercase; }
        h3 { font-size: 12pt; font-weight: bold; margin: 12pt 0 8pt 0; }
        h4 { font-size: 11pt; font-weight: bold; margin: 10pt 0 6pt 0; }
        p { margin: 8pt 0; text-align: justify; }
        .bold { font-weight: bold; }
        ul, ol { margin: 8pt 0; padding-left: 40px; }
        li { margin: 4pt 0; }
        table { width: 100%; border-collapse: collapse; margin: 12pt 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .toc { margin: 24pt 0; }
        .toc-item { margin: 6pt 0; text-decoration: none; color: #000; display: block; }
        .toc-item:hover { text-decoration: underline; }
        .toc-level-1 { margin-left: 0; }
        .toc-level-2 { margin-left: 20px; }
        .toc-level-3 { margin-left: 40px; }
        .chapter-title { font-size: 18pt; font-weight: bold; text-align: center; margin: 30pt 0 20pt 0; text-transform: uppercase; }
        .certification-text { margin: 30pt 0; text-align: justify; }
        .signature-line { margin-top: 60pt; text-align: center; }
        .signature-space { margin-top: 40pt; height: 60pt; }
        .appendix { page-break-before: always; }
        hr { border: none; border-top: 2px solid #333; margin: 20px 0; }
        a { color: #003366; text-decoration: none; }
        a:hover { text-decoration: underline; }
        code { font-family: \'Courier New\', Courier, monospace; background: #f5f5f5; padding: 2px 4px; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { page-break-after: always; }
            .page:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body>';

$certification = '
    <div class="page">
        <h1>CERTIFICATION</h1>
        <p class="certification-text">
            This is to certify that this project report titled "Student Supervisor Research Interaction System (SSRIS)" has been submitted by the student for the partial fulfillment of the requirements for the award of [Degree Name] at Moshi Cooperative University (MOCU).
        </p>
        <div class="signature-space"></div>
        <div class="signature-line">
            <p class="bold">_________________________</p>
            <p>Supervisor\'s Signature</p>
        </div>
        <div class="signature-line">
            <p class="bold">_________________________</p>
            <p>Date</p>
        </div>
    </div>';

$abbreviations = '
    <div class="page">
        <h1>LIST OF ABBREVIATIONS</h1>
        <table>
            <tr><td><strong>SSRIS</strong></td><td>Student Supervisor Research Interaction System</td></tr>
            <tr><td><strong>MOCU</strong></td><td>Moshi Cooperative University</td></tr>
            <tr><td><strong>HOD</strong></td><td>Head of Department</td></tr>
            <tr><td><strong>SMS</strong></td><td>Short Message Service</td></tr>
            <tr><td><strong>API</strong></td><td>Application Programming Interface</td></tr>
            <tr><td><strong>HTTP</strong></td><td>Hypertext Transfer Protocol</td></tr>
            <tr><td><strong>HTTPS</strong></td><td>Hypertext Transfer Protocol Secure</td></tr>
            <tr><td><strong>MVC</strong></td><td>Model-View-Controller</td></tr>
            <tr><td><strong>ORM</strong></td><td>Object-Relational Mapping</td></tr>
            <tr><td><strong>CSRF</strong></td><td>Cross-Site Request Forgery</td></tr>
            <tr><td><strong>UI</strong></td><td>User Interface</td></tr>
            <tr><td><strong>UX</strong></td><td>User Experience</td></tr>
        </table>
    </div>';

$listTables = '
    <div class="page">
        <h1>LIST OF TABLES</h1>
        <table>
            <tr><td>Table 1</td><td>System User Roles and Permissions</td></tr>
            <tr><td>Table 2</td><td>Document Types and Stages</td></tr>
            <tr><td>Table 3</td><td>Database Schema Overview</td></tr>
            <tr><td>Table 4</td><td>System Features Summary</td></tr>
        </table>
    </div>';

$listFigures = '
    <div class="page">
        <h1>LIST OF FIGURES</h1>
        <table>
            <tr><td>Figure 1</td><td>System Architecture Diagram</td></tr>
            <tr><td>Figure 2</td><td>Entity Relationship Diagram</td></tr>
            <tr><td>Figure 3</td><td>User Workflow Diagram</td></tr>
            <tr><td>Figure 4</td><td>System Interface Screenshots</td></tr>
        </table>
    </div>';

$toc = '
    <div class="page">
        <h1>TABLE OF CONTENTS</h1>
        <div class="toc">
            <div class="toc-item toc-level-1"><strong>CERTIFICATION</strong></div>
            <div class="toc-item toc-level-1"><strong>LIST OF ABBREVIATIONS</strong></div>
            <div class="toc-item toc-level-1"><strong>LIST OF TABLES</strong></div>
            <div class="toc-item toc-level-1"><strong>LIST OF FIGURES</strong></div>
            <div class="toc-item toc-level-1"><strong>TABLE OF CONTENTS</strong></div>
            <div class="toc-item toc-level-1"><a href="#ch1"><strong>CHAPTER ONE: INTRODUCTION</strong></a></div>
            <div class="toc-item toc-level-2"><a href="#s11">1.1 Background</a></div>
            <div class="toc-item toc-level-2"><a href="#s12">1.2 Statement of the Problem</a></div>
            <div class="toc-item toc-level-2"><a href="#s13">1.3 Objectives of the Project</a></div>
            <div class="toc-item toc-level-2"><a href="#s14">1.4 Project Questions</a></div>
            <div class="toc-item toc-level-2"><a href="#s15">1.5 Significance of the Study</a></div>
            <div class="toc-item toc-level-2"><a href="#s16">1.6 Scope of the Study</a></div>
            <div class="toc-item toc-level-2"><a href="#s17">1.7 Limitation of the Study</a></div>
            <div class="toc-item toc-level-2"><a href="#s18">1.8 Project Framework</a></div>
            <div class="toc-item toc-level-1"><a href="#ch2"><strong>CHAPTER TWO: LITERATURE REVIEW</strong></a></div>
            <div class="toc-item toc-level-2"><a href="#s21">2.1 Literature Review</a></div>
            <div class="toc-item toc-level-2"><a href="#s22">2.2 Theoretical Review</a></div>
            <div class="toc-item toc-level-2"><a href="#s23">2.3 Empirical Review</a></div>
            <div class="toc-item toc-level-2"><a href="#s24">2.4 Critique</a></div>
            <div class="toc-item toc-level-2"><a href="#s25">2.5 Knowledge Gap</a></div>
            <div class="toc-item toc-level-1"><a href="#ch3"><strong>CHAPTER THREE: METHODOLOGY</strong></a></div>
            <div class="toc-item toc-level-2"><a href="#s31">3.1 Study Area</a></div>
            <div class="toc-item toc-level-2"><a href="#s32">3.2 Project Design and Approach</a></div>
            <div class="toc-item toc-level-2"><a href="#s33">3.3 Target Population</a></div>
            <div class="toc-item toc-level-2"><a href="#s34">3.4 Sampling</a></div>
            <div class="toc-item toc-level-2"><a href="#s35">3.5 Data Collection</a></div>
            <div class="toc-item toc-level-2"><a href="#s36">3.6 Data Analysis</a></div>
            <div class="toc-item toc-level-2"><a href="#s37">3.7 Ethical Consideration</a></div>
            <div class="toc-item toc-level-1"><a href="#ch4"><strong>CHAPTER FOUR: RESULTS AND DISCUSSION</strong></a></div>
            <div class="toc-item toc-level-2"><a href="#s41">4.1 Results</a></div>
            <div class="toc-item toc-level-2"><a href="#s42">4.2 Discussion</a></div>
            <div class="toc-item toc-level-1"><a href="#ch5"><strong>CHAPTER FIVE: SUMMARY, CONCLUSION AND RECOMMENDATIONS</strong></a></div>
            <div class="toc-item toc-level-2"><a href="#s51">5.1 Summary</a></div>
            <div class="toc-item toc-level-2"><a href="#s52">5.2 Conclusion</a></div>
            <div class="toc-item toc-level-2"><a href="#s53">5.3 Recommendations</a></div>
            <div class="toc-item toc-level-1"><a href="#refs"><strong>References</strong></a></div>
            <div class="toc-item toc-level-1"><a href="#apps"><strong>APPENDICES</strong></a></div>
        </div>
    </div>';

$footer = '</body></html>';

$fullHtml = $head . $certification . $abbreviations . $listTables . $listFigures . $toc . $contentHtml . $footer;

file_put_contents($htmlPath, $fullHtml);

echo "✅ HTML report generated successfully\n";
echo "📁 File: " . $htmlPath . "\n";
echo "📊 Size: " . round(strlen($fullHtml) / 1024) . " KB\n";
