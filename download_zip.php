<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function deleteDirectory(string $ordner): void {
    if (!is_dir($ordner)) {
        return;
    }

    $eintraege = scandir($ordner);
    if ($eintraege === false) {
        return;
    }
    foreach ($eintraege as $eintrag) {
        if ($eintrag === '.' || $eintrag === '..') {
            continue;
        }

        $vollerPfad = $ordner . $eintrag;

        if (is_dir($vollerPfad)) {
            deleteDirectory($vollerPfad . '/');
        } else {
            unlink($vollerPfad);
        }
    }

    rmdir($ordner);
}







if (!isset($_GET['batch']) || $_GET['batch'] === '') {
    exit('Keine Batch-ID angegeben.');
}


$batchId = basename($_GET['batch']);
$batchPfad = __DIR__ . '/uploads_bildconverter/' . $batchId . '/';
$datei = $batchId . '.zip';
$pfad = $batchPfad . $datei;

if (!file_exists($pfad)) {
    exit('Datei nicht gefunden.');
}

if (pathinfo($pfad, PATHINFO_EXTENSION) !== 'zip') {
    exit('Ungültige Datei.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $datei . '"');
header('Content-Length: ' . filesize($pfad));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$ok = readfile($pfad);

if ($ok !== false) {
    deleteDirectory($batchPfad);
}

exit;