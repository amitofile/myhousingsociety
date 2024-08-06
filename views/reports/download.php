
<?php

session_start();

if (filter_input(INPUT_GET, "code") !== $_SESSION['report_token']) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

$file = filter_input(INPUT_GET, "file");

switch ($file) {
    case 'total_bill':
        $file_url = '/var/www/downloaddocs/reports/total_bill.csv';
        break;
    case 'paid_bill':
        $file_url = '/var/www/downloaddocs/reports/paid_bill.csv';
        break;
    case 'paid_monthwise':
        $file_url = '/var/www/downloaddocs/reports/paid_monthwise.csv';
        break;
    default:
        header('HTTP/1.0 404 Not Found', TRUE, 404);
        die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
        break;
}

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: utf-8");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);

?>