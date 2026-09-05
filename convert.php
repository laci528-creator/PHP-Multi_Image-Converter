<?php
require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/filename_functions.inc.php";
require_once __DIR__ . "/includes/image_functions.inc.php";
require_once __DIR__ . "/includes/upload_functions.inc.php";
require_once __DIR__ . "/includes/validation_functions.inc.php";
require_once __DIR__ . "/includes/zip_functions.inc.php";
require_once __DIR__ . "/includes/batch_functions.inc.php";

$msg = "";
$msg2 = "";
$msg3 = "";

$maxFiles = MAX_CONVERT_FILES;
$convertedFiles = [];

if($_SERVER["REQUEST_METHOD"] === "POST") {
    if(!empty($_FILES["myUpload"]["name"][0])) {
        $quality = ($_POST["quality"] ?? "") === "" ? 85 : $_POST["quality"];
        $valQuality = validateQuality($quality);

            if($valQuality['success'] === true) {
                $q = $valQuality["quality"];
                $formatValidation = validateOutputFormat($_POST["output_format"] ?? "");
                
                    if ($formatValidation["success"]) {
                        $outputformat = $formatValidation["format"];
                            $f = $_FILES["myUpload"];
                            $fileCount = count($f["name"]);
                            
                            if($fileCount <= $maxFiles) {
                                $batch = createBatchPath('uploads_bildconverter');

                                for ($i = 0; $i < $fileCount; $i++) {
                                    $file = getUploadedFileByIndex($f, $i);
                                    $filename = $file["name"];
                                    $validation = validateUploadedImage($file);

                                        if($validation['success'])  {

                                        $newFilename = createSafeFilename($filename, $outputformat);
                                        $outputPath = $batch["outputDir"] . $newFilename;
                                        $previewPath = $batch["publicOutputDir"] . $newFilename;
                                        $inputPath = $file["tmp_name"];
                                        $ok = Format_konvert($inputPath, $outputPath, $outputformat, $q);

                                            if($ok) {
                                                $convertedFiles[] = $outputPath;

                                                $msg .=  '<p class="success">Die Datei <strong>' . htmlspecialchars($newFilename) . '</strong> wurde erfolgreich in das Format ' . htmlspecialchars($outputformat) .' konvertiert.</p>';
                                                $msg2 .= '<div class="preview-card">';
                                                $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilename) . '</h3>';
                                                $msg2 .= '<img src="' . htmlspecialchars($previewPath) . '" alt="Konvertiertes Bild">';
                                                $msg2 .= '<p><a href="' . htmlspecialchars($previewPath) . '" target="_blank" rel="noopener noreferrer">Bild in Originalgröße öffnen</a></p>';
                                                $msg2 .= '</div>';
                                            } 
                                            else {
                                                    $msg .= '<p class="error">Die Konvertierung von ' . htmlspecialchars($filename) . ' ist fehlgeschlagen.</p>';
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
                            $msg = '<p class="error">' . htmlspecialchars($formatValidation["message"]) . '</p>';
                        }
            }
            else {
                    $msg =  '<p class="error">' . htmlspecialchars($valQuality["message"]) . '</p>';
                }
    } else {
        $msg = '<p class="error">Bitte wählen Sie mindestens eine Bilddatei aus.</p>';
        }

}

if (!empty($convertedFiles)) {
    $zipPath = $batch["batchDir"] . $batch["batchId"] . '.zip';

    $zipCreated = createZip($convertedFiles, $zipPath);

        if ($zipCreated) {

        $msg3 = '<p><a class="download-button" href="download_zip.php?batch=' . urlencode($batch["batchId"]) . '">Download ZIP-Datei</a></p>';
        }
        else {
            $msg3 = '<p class="error">Die ZIP-Datei konnte nicht erstellt werden.</p>';
        }
}

?>

    <?php require __DIR__ . "/includes/header.inc.php"; ?>
        <main class="main-container">
		<h1>Bildformat konvertieren</h1>
		
		<form method="post" enctype="multipart/form-data" class="tool-form">
			<label>
                <div
                    class="drop-zone"
                    data-input="convert-images"
                    data-max-files="<?php echo $maxFiles; ?>"
                >
                    <p class="drop-zone-text">
                        Bilder hierher ziehen oder auswählen
                    </p>

                    <p class="drop-zone-hint">
                        Maximal <?php echo $maxFiles; ?> Dateien · JPEG, GIF, PNG, WebP, AVIF
                    </p>

                    <button type="button" class="drop-zone-button">
                        Dateien auswählen
                    </button>

                    <p class="drop-zone-files">
                        Keine Dateien ausgewählt.
                    </p>
                </div>
				<input class="drop-zone-input" id="convert-images" type="file" name="myUpload[]" multiple accept="image/jpeg,image/gif,image/png,image/webp,image/avif">
			</label>
            <label>
				Bitte wählen Sie das gewünschte Ausgabeformat aus (JPEG, PNG, WebP, AVIF):
                <select name="output_format">
                    <option value="jpeg">JPEG</option>
                    <option value="png">PNG</option>
                    <option value="webp">WebP</option>
                    <option value="avif">AVIF</option>
                </select>
            </label>
            <label>
				Bitte geben Sie die gewünschte Bildqualität ein (1-99, Standard: 85):
                <input type="number" name="quality" min="1" max="99" value="85">
            </label>
			<input type="submit" value="Hochladen und konvertieren" class="action-button">
		</form>
        <?php echo($msg3); ?>
        <?php echo($msg); ?>
        <?php if (!empty($msg2)): ?>
            <h2>Vorschau der konvertierten Bilder</h2>
		<?php echo $msg2; ?>
        <?php endif; ?>
        <script src="js/dropzone.js"></script>
        </main>
        <?php require __DIR__ . "/includes/footer.inc.php"; ?>
