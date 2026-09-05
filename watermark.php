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
$watermarkedFiles = [];

$maxFiles = MAX_WATERMARK_FILES; // Maximale Anzahl an Dateien, die hochgeladen werden können

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_FILES["images"]["name"][0]) && !empty($_FILES["watermark"]["name"]) ) {
        $opacity = ($_POST["opacity"] ?? "") === "" ? 50 : $_POST["opacity"];
        $valOpacity = validateOpacity($opacity);
            if($valOpacity['success'] === true) {
                $o = $valOpacity["opacity"];
                $f = $_FILES["images"];
                $fileCount = count($f["name"]);
                    if($fileCount <= $maxFiles) {
                        $w = $_FILES["watermark"];
                        $watermarkName = $w["name"];
                        $watermarkTmpName = $w["tmp_name"];
                            $position = validatePosition($_POST["position"] ?? "bottom-right");
                            $watermarkValidation = validateUploadedImage($w);
                                if ($watermarkValidation["success"] === true &&
                                    $watermarkValidation["mime"] === "image/png") {
                                    $batch = createBatchPath('uploads_bildconverter');

                                    for ($i = 0; $i < $fileCount; $i++) {

                                        $file = getUploadedFileByIndex($f, $i);
                                        $filename = $file["name"];
                                            $fileValidation = validateUploadedImage($file);
                                        if ($fileValidation["success"] === true &&
                                            $fileValidation["mime"] === "image/jpeg") {
                                            $newFilename = createSafeFilename($filename, "jpeg");

                                            $outputPath = $batch["outputDir"] . $newFilename;
                                            $previewPath = $batch["publicOutputDir"] . $newFilename;
                                            $inputPath = $file["tmp_name"];

                                                    $ok = addWatermark(
                                                        $inputPath,
                                                        $watermarkTmpName,
                                                        $outputPath,
                                                        $position,
                                                        $o
                                                    );
                                                        if ($ok) {
                                                            $watermarkedFiles[] = $outputPath;

                                                            $msg .= '<p class="success">Wasserzeichen erfolgreich zu ' . htmlspecialchars($filename) . ' hinzugefügt.</p>';
                                                            $msg2 .= '<div class="preview-card">';
                                                            $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilename) . '</h3>';
                                                            $msg2 .= '<img src="' . htmlspecialchars($previewPath) . '" alt="Bild mit Wasserzeichen">';
                                                            $msg2 .= '<p><a href="' . htmlspecialchars($previewPath) . '" target="_blank" rel="noopener noreferrer">Bild in Originalgröße öffnen</a></p>';
                                                            $msg2 .= '</div>';
                                                        } else {
                                                            $msg .= '<p class="error">Fehler beim Hinzufügen des Wasserzeichens zu <strong>' . htmlspecialchars($filename) . '</strong>.</p>';
                                                        }
                                        }
                                        else {
                                            $errorMessage = $fileValidation["success"] === false
                                                ? $fileValidation["message"]
                                                : "Bitte laden Sie eine JPG-Datei hoch.";

                                            $msg .= '<p class="error"><strong>'
                                                . htmlspecialchars($filename)
                                                . '</strong>: '
                                                . htmlspecialchars($errorMessage)
                                                . '</p>';
                                        }
                                    }
                        }
                        else {
                            $errorMessage = $watermarkValidation["success"] === false
                                ? $watermarkValidation["message"]
                                : "Bitte laden Sie eine PNG-Datei als Wasserzeichen hoch.";

                            $msg = '<p class="error">'
                                . htmlspecialchars($errorMessage)
                                . '</p>';
                        }

                    }
                    else {
                        $msg = '<p class="error">Bitte laden Sie maximal ' . $maxFiles . ' Bilder hoch.</p>';
                        }
            }
            else {
                $msg = '<p class="error">' . htmlspecialchars($valOpacity['message']) . '</p>';
            }

    }
    else {
        $msg = '<p class="error">Bitte wählen Sie mindestens eine Bilddatei für das Bild und ein Wasserzeichen aus.</p>';
        }
}

if (!empty($watermarkedFiles)) {
    $zipPath = $batch["batchDir"] . $batch["batchId"] . '.zip';

    $zipCreated = createZip($watermarkedFiles, $zipPath);

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
        <h1>Wasserzeichen zu Bildern hinzufügen</h1>
            <form method="post" enctype="multipart/form-data" class="tool-form">
                <label>
                <div
                    class="drop-zone"
                    data-input="insert-watermark"
                    data-max-files="<?= $maxFiles ?>"
                >
                    <p class="drop-zone-text">
                        Bilder hierher ziehen oder auswählen
                    </p>

                    <p class="drop-zone-hint">
                        Maximal <?php echo $maxFiles; ?> Dateien · nur JPEG
                    </p>

                    <button type="button" class="drop-zone-button">
                        Dateien auswählen
                    </button>

                    <p class="drop-zone-files">
                        Keine Dateien ausgewählt.
                    </p>
                </div>
                    <input class="drop-zone-input" id="insert-watermark" type="file" name="images[]" multiple accept="image/jpeg">
                </label>
                <label>
                    Wasserzeichen / Logo auswählen:
                    <input type="file" name="watermark" accept="image/png">
                </label>
                <label>
                    Position:
                    <select name="position">
                        <option value="bottom-right">Rechts unten</option>
                        <option value="bottom-left">Links unten</option>
                        <option value="top-right">Rechts oben</option>
                        <option value="top-left">Links oben</option>
                        <option value="center">Mitte</option>
                    </select>
                </label>
                <label>
                    Transparenz:
                    <input type="number" name="opacity" min="1" max="100" value="50">
                </label>
                <input type="submit" value="Wasserzeichen hinzufügen" class="action-button">
            </form>
		    <?php echo($msg3); ?>
            <?php echo($msg); ?>
            <?php if (!empty($msg2)): ?>
                <h2>Vorschau der Bilder mit Wasserzeichen</h2>
            <?php echo($msg2); ?>
            <?php endif; ?>
            <script src="js/dropzone.js"></script>
            </main>
            <?php require __DIR__ . "/includes/footer.inc.php"; ?>