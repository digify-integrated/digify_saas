<?php

require_once './config/config.php';

/**
 * Autoloader function for automatically loading class files.
 * This function follows the PSR-4 standard for autoloading.
 * It loads class files from the application directory and vendor directories (e.g., PHPMailer, Composer libraries).
 *
 * @param string $class The fully-qualified name of the class to be loaded.
 * @throws Exception If the class file is not found.
 */
spl_autoload_register(function ($class) {
    // Define the base directory for the application (root directory)
    $baseDir = __DIR__ . '/';

    // Attempt to load the class from the app directory
    if ($classPath = loadFromAppDirectory($baseDir, $class)) {
        require_once $classPath;
        return;
    }

    // Attempt to load the class from the vendor directory (for PHPMailer)
    if ($classPath = loadFromVendorDirectory($class)) {
        require_once $classPath;
        return;
    }

    // Attempt to load using Composer's autoloader if available
    if (loadComposerAutoloader($baseDir)) {
        return;
    }

    // If class file is not found, throw an exception
    throw new Exception("Class file not found for: $class");
});

/**
 * Load class from the app directory.
 *
 * @param string $baseDir The base directory for the app.
 * @param string $class The fully-qualified class name.
 * @return string|null The file path if found, null otherwise.
 */
function loadFromAppDirectory($baseDir, $class) {
    // Convert the namespace to the file path
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    // Return file path if the class file exists
    if (file_exists($file)) {
        return $file;
    }

    return null;
}

/**
 * Load class from the vendor directory (for PHPMailer or other vendor namespaces).
 *
 * @param string $class The fully-qualified class name.
 * @return string|null The file path if found, null otherwise.
 */
function loadFromVendorDirectory($class) {
    // Check if the class belongs to the PHPMailer namespace or any other vendor namespaces
    if (strpos($class, 'PHPMailer\\') === 0) {
        $file = __DIR__ . '/vendor/phpmailer/src/' . str_replace('PHPMailer\\', '', $class) . '.php';
        if (file_exists($file)) {
            return $file;
        }
    }

    // Add more vendor-specific checks here as needed
    // e.g., handling other libraries like Guzzle, Symfony, etc.

    return null;
}

/**
 * Load Composer autoloader if available.
 *
 * @param string $baseDir The base directory.
 * @return bool True if autoloader is found and loaded, false otherwise.
 */
function loadComposerAutoloader($baseDir) {
    $composerDir = $baseDir . '/vendor/autoload.php';
    
    // Check if Composer autoloader exists and require it
    if (file_exists($composerDir)) {
        require_once $composerDir;
        return true;
    }

    return false;
}
