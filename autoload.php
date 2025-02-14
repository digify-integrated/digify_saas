<?php

require_once './config/config.php';

/**
 * Autoloader function to load class files dynamically.
 * 
 * - Implements **PSR-4 Autoloading** standards.
 * - Supports **custom application classes** and **vendor libraries** (e.g., PHPMailer).
 * - Tries to load the class from:
 *   1. **App directory**
 *   2. **Vendor directory** (for PHPMailer or other vendor-specific classes)
 *   3. **Composer's autoloader** (if available)
 * 
 * @param string $class The fully-qualified class name.
 * @throws Exception If the class file is not found.
 */
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    // Load class from the application directory
    if ($filePath = getClassFilePath($baseDir, $class)) {
        require_once $filePath;
        return;
    }

    // Load class from the vendor directory (e.g., PHPMailer)
    if ($filePath = getVendorClassFilePath($class)) {
        require_once $filePath;
        return;
    }

    // Load Composer autoloader if available
    if (loadComposerAutoloader($baseDir)) {
        return;
    }

    // If no matching class file is found, throw an exception
    throw new Exception("Class file not found for: $class");
});

/**
 * Convert class namespace to a file path and check if it exists.
 * 
 * @param string $baseDir The base directory of the application.
 * @param string $class The fully-qualified class name.
 * @return string|null The class file path if found, null otherwise.
 */
function getClassFilePath($baseDir, $class) {
    // Convert namespace to file path
    $filePath = $baseDir . str_replace('\\', '/', $class) . '.php';

    return file_exists($filePath) ? $filePath : null;
}

/**
 * Locate and return the file path of vendor library classes (e.g., PHPMailer).
 * 
 * @param string $class The fully-qualified class name.
 * @return string|null The vendor class file path if found, null otherwise.
 */
function getVendorClassFilePath($class) {
    // Check if the class belongs to PHPMailer or other vendor namespaces
    if (strpos($class, 'PHPMailer\\') === 0) {
        $filePath = __DIR__ . '/vendor/phpmailer/src/' . str_replace('PHPMailer\\', '', $class) . '.php';
        return file_exists($filePath) ? $filePath : null;
    }

    // Extend support for other vendor libraries if necessary
    return null;
}

/**
 * Load Composer's autoloader if available.
 * 
 * @param string $baseDir The base directory.
 * @return bool True if Composer autoloader is found and loaded, false otherwise.
 */
function loadComposerAutoloader($baseDir) {
    $composerAutoloadFile = $baseDir . '/vendor/autoload.php';

    if (file_exists($composerAutoloadFile)) {
        require_once $composerAutoloadFile;
        return true;
    }

    return false;
}
