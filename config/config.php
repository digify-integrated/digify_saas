<?php

declare(strict_types=1);

// -------------------------------------------------------------
// Timezone Configuration
// -------------------------------------------------------------
date_default_timezone_set('Asia/Manila');

// -------------------------------------------------------------
// Application Configuration
// -------------------------------------------------------------
define('APP_NAME', 'Digify');                 // Application name
define('APP_URL', 'http://localhost/');       // Application base URL
define('DEBUG_MODE', true);                   // Enable/disable error display

// -------------------------------------------------------------
// Database Configuration
// -------------------------------------------------------------
define('DB_HOST', 'localhost');               // Database host
define('DB_NAME', 'digifydb');                // Database name
define('DB_USER', 'digify');                  // Database username
define('DB_PASS', 'qKHJpbkgC6t93nQr');        // Database password
define('DB_CHARSET', 'utf8mb4');              // Database charset (UTF-8MB4 recommended)

// -------------------------------------------------------------
// Encryption Configuration
// -------------------------------------------------------------
define('ENCRYPTION_KEY', '4b$Gy#89%q*aX@^p&cT!sPv6(5w)zSd+R'); // Encryption key
define('SECRET_KEY', '9n6ui[N];T\?{Wju[@zq^7)y>gsz2ltMT');     // Secret key for encryption

// -------------------------------------------------------------
// File Upload Configuration
// -------------------------------------------------------------
define('UPLOAD_DIR', __DIR__ . '/../uploads');    // File upload directory
define('MAX_FILE_SIZE', 10485760);                // Maximum file size for upload (10MB)

// -------------------------------------------------------------
// Mail Configuration
// -------------------------------------------------------------
define('MAIL_SMTP_SERVER', 'smtp.hostinger.com');            // SMTP server
define('MAIL_SMTP_PORT', 465);                             // SMTP port (typically 465 for SSL)
define('MAIL_USERNAME', 'cgmi-noreply@christianmotors.ph'); // SMTP username
define('MAIL_PASSWORD', 'P@ssw0rd');                       // SMTP password
define('MAIL_FROM_EMAIL', 'cgmi-noreply@christianmotors.ph'); // Sender email address
define('MAIL_FROM_NAME', 'CGMI No Reply');                  // Sender name
define('MAIL_SMTP_SECURE', 'ssl');                          // SMTP secure protocol
define('MAIL_SMTP_AUTH', true);                             // Enable SMTP authentication

// -------------------------------------------------------------
// Google reCAPTCHA Configuration
// -------------------------------------------------------------
define('RECAPTCHA_SITE_KEY', 'your-site-key');    // reCAPTCHA site key
define('RECAPTCHA_SECRET_KEY', 'your-secret-key'); // reCAPTCHA secret key

// -------------------------------------------------------------
// Default User Interface Images
// -------------------------------------------------------------
define('DEFAULT_AVATAR_IMAGE', './assets/images/default/default-avatar.jpg');
define('DEFAULT_BG_IMAGE', './assets/images/default/default-bg.jpg');
define('DEFAULT_LOGIN_LOGO_IMAGE', './assets/images/default/default-logo-placeholder.png');
define('DEFAULT_MENU_LOGO_IMAGE', './assets/images/default/default-menu-logo.png');
define('DEFAULT_MODULE_ICON_IMAGE', './assets/images/default/default-module-icon.svg');
define('DEFAULT_FAVICON_IMAGE', './assets/images/default/default-favicon.svg');
define('DEFAULT_COMPANY_LOGO', './assets/images/default/default-company-logo.png');
define('DEFAULT_APP_MODULE_LOGO', './assets/images/default/app-module-logo.png');
define('DEFAULT_PLACEHOLDER_IMAGE', './assets/images/default/default-image-placeholder.png');
define('DEFAULT_ID_PLACEHOLDER_FRONT', './assets/images/default/id-placeholder-front.jpg');
define('DEFAULT_UPLOAD_PLACEHOLDER', './assets/images/default/upload-placeholder.png');

// -------------------------------------------------------------
// Security Configuration
// -------------------------------------------------------------
define('DEFAULT_PASSWORD', 'P@ssw0rd');                           // Default password for users
define('MAX_FAILED_LOGIN_ATTEMPTS', 5);                            // Max failed login attempts before lockout
define('RESET_PASSWORD_TOKEN_DURATION', 10);                       // Duration for password reset token (in minutes)
define('REGISTRATION_VERIFICATION_TOKEN_DURATION', 180);           // Duration for registration verification token (in minutes)
define('DEFAULT_PASSWORD_DURATION', 180);                          // Password expiration duration (in days)
define('MAX_FAILED_OTP_ATTEMPTS', 5);                              // Max failed OTP attempts before lockout
define('DEFAULT_OTP_DURATION', 5);                                 // OTP validity duration (in minutes)
define('DEFAULT_SESSION_INACTIVITY', 30);                          // Session inactivity duration (in minutes)
define('BASE_LOCK_DURATION', 60);                                  // Lock duration (in seconds)
define('DEFAULT_PASSWORD_RECOVERY_LINK', 'password-reset.php?id='); // Default password recovery link

// -------------------------------------------------------------