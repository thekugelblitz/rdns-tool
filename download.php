<?php
if (isset($_POST['records']) && isset($_POST['filename'])) {
    $records = unserialize(base64_decode($_POST['records']));
    $filename = $_POST['filename'];

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen(implode("\n", $records)));
    flush(); // Flush system output buffer

    echo implode("\n", $records);
    exit;
} else {
    echo "No records or filename provided.";
}
