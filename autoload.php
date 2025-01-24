<?php
require_once './config/config.php';

/**
 * Autoloader function for automatically loading class files.
 * This function follows PSR-4 standard for autoloading classes in PHP.
 * It looks for class files in the application directory and vendor directories (e.g., PHPMailer, Composer libraries).
 *
 * @param string $class The fully-qualified name of the class to be loaded.
 * @throws Exception Throws an exception if the class file is not found.
 */
spl_autoload_register(function ($class) {
    // Define the base directory for the app (root directory)
    $baseDir = __DIR__ . '/';

    // Convert the namespace to the file path
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    // If the class is found in the app directory (controllers, models, etc.)
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // Handle the case for PHPMailer or other vendor namespaces
    // Check if the class belongs to the PHPMailer namespace or any other vendor namespaces
    if (strpos($class, 'PHPMailer\\') === 0) {
        $file = __DIR__ . '/vendor/phpmailer/phpmailer/src/' . str_replace('PHPMailer\\', '', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Check for other third-party libraries or vendor namespaces
    // This section is for handling other libraries installed via Composer or manually
    $composerDir = __DIR__ . '/vendor/autoload.php';
    if (file_exists($composerDir)) {
        require_once $composerDir; // Composer autoloader should load all libraries
        return;
    }

    // If the class file is not found, throw an exception
    throw new Exception("Class file not found for: $class. Searched at: $file");
});