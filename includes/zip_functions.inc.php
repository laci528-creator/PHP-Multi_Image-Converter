<?php 

function createZip(array $files, string $zielOrdner, string $batchId): string|false {
    if (empty($files)) {
        return false;
    }

    $zipName = $batchId . ".zip";
    $zipPath = rtrim($zielOrdner, "/") . "/" . $zipName;

    $zip = new ZipArchive();

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }

    foreach ($files as $file) {
        if (file_exists($file)) {
            $zip->addFile($file, basename($file));
        }
    }

    $zip->close();

    return $zipName;
}


?>