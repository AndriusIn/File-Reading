<?php declare(strict_types = 1);

require_once __DIR__ . '/Psr4AutoloaderClass.php';

// Instantiate the loader
$loader = new \Example\Psr4AutoloaderClass;

// Register the autoloader
$loader->register();

// Register the base directories for the namespace prefix
$loader->addNamespace('App', __DIR__ . '/src');

use App\Controller\FileController;

$fileController = new FileController();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="index.css">
        <title>Failo skaitymas</title>
    </head>
    <body>
        <div class="custom-form">
            <h2>Failo skaitymas</h2>
            <form method="POST">
                <input type="text"
                       name="filePath"
                       placeholder="Failo pavadinimas (pilnas kelias iki failo)"
                       value="<?php echo isset($_POST['filePath']) ? trim($_POST['filePath']) : ''; ?>"
                >
                <input type="submit" value="Rodyti failo turinį">
            </form>
        </div>
        <?php
        // Check if form was submitted
        if (isset($_POST['filePath'])) {
            $filePath = trim($_POST['filePath']);

            $fileContent = 'Failo laukelis negali būti tuščias!';

            // Check if file input isn't empty
            if (!empty($filePath)) {
                $fileContent = 'Įvestas failas neegzistuoja!';

                if (file_exists($filePath)) {
                    $fileContent = 'Įvestas failas privalo būti tekstinis!';

                    $mimeContentType = mime_content_type($filePath);

                    // Allow only text media types (text/plain, text/csv...)
                    if (strpos($mimeContentType, 'text/') !== FALSE) {
                        $fileContent = 'Įvestas failas neatitinka CSV, XML arba JSON formato!';

                        $associativeArray = $fileController->getAssociativeArray($filePath);

                        if ($associativeArray !== FALSE) {
                            $fileContent = htmlspecialchars(file_get_contents($filePath));
                            //$fileContent = var_export($associativeArray, true);
                        } else {
                            $fileContent .= PHP_EOL . htmlspecialchars(file_get_contents($filePath));
                        }
                    }
                }
            }
        }
        ?>
        <p class="custom-paragraph"><?php echo isset($_POST['filePath']) ? $fileContent : ''; ?></p>
    </body>
</html>
