<?php
require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/message_functions.inc.php";
require_once __DIR__ . "/includes/filename_functions.inc.php";
require_once __DIR__ . "/includes/image_functions.inc.php";
require_once __DIR__ . "/includes/upload_functions.inc.php";
require_once __DIR__ . "/includes/validation_functions.inc.php";
require_once __DIR__ . "/includes/zip_functions.inc.php";
require_once __DIR__ . "/includes/batch_functions.inc.php";


$msg = "";
$msg2 = "";
$msg3 = "";
$resizedFiles = [];
$extensionMap = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/gif" => "gif",
    "image/webp" => "webp",
    "image/avif" => "avif"
];

$maxFiles = MAX_RESIZE_FILES;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_FILES["myUpload"]["name"][0]) && isset($_POST["neu_resolution"]) && $_POST["neu_resolution"] !== "") {
        $resolution = $_POST["neu_resolution"];
        $validationResult = validateResolution($resolution);
            if ($validationResult["success"] === true) {
                $neuResolution = $validationResult["resolution"];
                $f = $_FILES["myUpload"];
                $fileCount = count($f["name"]);

                if($fileCount <= $maxFiles) {

                        $batch = createBatchPath('uploads_bildconverter');

                        for ($i = 0; $i < $fileCount; $i++) {   
                            $file = getUploadedFileByIndex($f, $i);
                            $filename = $file["name"];
                            $validation = validateUploadedImage($file);
                                if($validation['success'] === true)  { 

                                    $extension = $extensionMap[$validation["mime"]];
                                    $newFilename = createSafeFilename($filename, $extension);
                                    $newFilenameWithResolution = pathinfo($newFilename, PATHINFO_FILENAME) . "_" . $neuResolution . "." . $extension;

                                    $outputPath = $batch["outputDir"] . $newFilenameWithResolution;
                                    $previewPath = $batch["publicOutputDir"] . $newFilenameWithResolution;

                                    $inputPath = $file["tmp_name"];
                                    // ta($newFilenameWithResolution);

                                    $ok = Convert_Bild($inputPath, $neuResolution, $outputPath);

                                        if ($ok) {
                                            $resizedFiles[] = $outputPath;

                                            $imageSize = getimagesize($outputPath);
                                            $width = $imageSize[0] ?? 0;
                                            $height = $imageSize[1] ?? 0;

                                            $msg .= '<p class="success">Die Datei <strong>' . htmlspecialchars($newFilenameWithResolution) . '</strong> wurde erfolgreich auf ' . htmlspecialchars((string)$width) . ' x ' . htmlspecialchars((string)$height) . ' px skaliert.</p>';
                                                $msg2 .= '<div class="preview-card">';
                                                $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilenameWithResolution) . '</h3>';
                                                $msg2 .= '<p>Neue Bildgröße: ' . htmlspecialchars((string)$width) . ' x ' . htmlspecialchars((string)$height) . ' px</p>';
                                                $msg2 .= '<img src="' . htmlspecialchars($previewPath) . '" alt="Skaliertes Bild">';
                                                $msg2 .= '<p><a href="' . htmlspecialchars($previewPath) . '" target="_blank" rel="noopener noreferrer">Bild in Originalgröße öffnen</a></p>';
                                                $msg2 .= '</div>';
                                        } else {
                                            $msg .= '<p class="error">Die Datei <strong>' . htmlspecialchars($filename) . '</strong> wurde hochgeladen, aber die Skalierung ist fehlgeschlagen.</p>';
                                        }

                                }
                                else {
                                    $msg .= '<p class="error">Fehler bei der Datei <strong>' . htmlspecialchars($filename) . '</strong>: ' . htmlspecialchars($validation["message"]) . '</p>';
                                }
                        }
                }
                else {
                    $msg = '<p class="error">Bitte laden Sie maximal ' . $maxFiles . ' Bilder hoch.</p>';
                }

            } 
            else {
                $msg = '<p class="error">' . htmlspecialchars($validationResult["message"]) . '</p>';
            }
    }
    else {
        $msg = '<p class="error">Bitte laden Sie mindestens eine Datei hoch und geben Sie die gewünschte Auflösung an.</p>';
    }
}

if (!empty($resizedFiles)) {
    $zipPath = $batch["batchDir"] . $batch["batchId"] . '.zip';

    $zipCreated = createZip($resizedFiles, $zipPath);

        if ($zipCreated) {

        $msg3 = '<p><a class="download-button" href="download_zip.php?batch=' . urlencode($batch["batchId"]) . '">Download ZIP-Datei</a></p>';
        }
        else {
            $msg3 = '<p class="error">Die ZIP-Datei konnte nicht erstellt werden.</p>';
        }
}

?>
<!doctype html>
<html lang="de">
	<head>
		<title>Bilder skalieren</title>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
	</head>
	<body>
        <?php require __DIR__ . "/includes/header.inc.php"; ?>
        <main>
		<h1>Bildgröße für mehrere Dateien ändern</h1>
		
		<form method="post" enctype="multipart/form-data" class="tool-form">
            <label>
                <div
                    class="drop-zone"
                    data-input="resize-images"
                    data-max-files="<?php echo $maxFiles; ?>"
                >
                    <p class="drop-zone-text">
                        Bilder hierher ziehen oder auswählen
                    </p>

                    <p class="drop-zone-hint">
                        Maximal <?php echo $maxFiles; ?> Dateien · JPG, GIF, PNG, WebP, AVIF
                    </p>

                    <button type="button" class="drop-zone-button">
                        Dateien auswählen
                    </button>

                    <p class="drop-zone-files">
                        Keine Dateien ausgewählt.
                    </p>
                </div>

                <input
                    class="drop-zone-input"
                    id="resize-images"
                    type="file"
                    name="myUpload[]"
                    multiple
                    accept="image/jpeg,image/png,image/gif,image/webp,image/avif"
                    hidden
                >
            </label>
            <label>
				Bitte geben Sie die Länge der längeren Bildseite in Pixel an (Standard: 800 px):
                <input type="number" name="neu_resolution" min="50" max="4000" value="800">
            </label>
			<input type="submit" name="HC" value="Hochladen und Skalieren" class="action-button">
		</form>
        <br>
		<?php echo($msg); ?>
		<?php echo($msg3); ?>
        <?php if (!empty($msg2)): ?>
            <h2>Vorschau der skalierten Bilder</h2>
		<?php echo $msg2; ?>
        <?php endif; ?>
<script src="js/dropzone.js"></script>
        </main>
        <?php require __DIR__ . "/includes/footer.inc.php"; ?>
	</body>
</html>