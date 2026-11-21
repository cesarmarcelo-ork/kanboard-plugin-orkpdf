<?php
$source = file_get_contents(__DIR__ . '/../Template/pdf/report.php');
foreach (array('orkpdf-report-standalone-styles','@media print','body *{visibility:hidden!important}', '.orkpdf-report-wrap,.orkpdf-report-wrap *{visibility:visible!important}', 'print-color-adjust:exact', 'size:A4 <?= $orientation ?>') as $part) {
    if (strpos($source, $part) === false) {fwrite(STDERR,"Missing print style: $part\n");exit(1);}
}
echo "PASS: standalone print stylesheet and Kanboard chrome suppression\n";
