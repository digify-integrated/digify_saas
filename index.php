<?php

/**
 * Bootstrap the application by initializing the session, loading dependencies,
 * and setting up the router with defined routes.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Secure token
}

// Include required class files
require_once './router.php';
require_once './autoload.php';

// Import middleware classes
use App\Middlewares\AuthMiddleware;
use App\Middlewares\GuestMiddleware;

// Instantiate the router
$router = new Router();

// Instantiate middleware instances
$authMiddleware = new AuthMiddleware();
$guestMiddleware = new GuestMiddleware();

/**
 * Public Routes - Accessible to everyone (no authentication required).
 */
$router->add('/about', 'AboutController', 'index', 'GET');
$router->add('/contact', 'ContactController', 'index', 'GET');

/**
 * Guest Routes - Only accessible when the user is **not logged in**.
 * These routes prevent logged-in users from accessing authentication pages.
 */
$router->group(['middleware' => [$guestMiddleware]], function ($router) {
    $router->add('/login', 'AuthenticationController', 'index', 'GET');         // Login page
    $router->add('/authenticate', 'AuthenticationController', 'authenticate', 'POST'); // Login processing
});

/**
 * Protected Routes - Only accessible when the user **is logged in**.
 * Users must be authenticated to access these routes.
 */
$router->group(['middleware' => [$authMiddleware], 'prefix' => 'app'], function ($router) {
    $router->add('/', 'AppController', 'index', 'GET');                  // Dashboard homepage
    $router->add('/dashboard', 'DashboardController', 'index', 'GET');   // User dashboard
    $router->add('/settings', 'SettingsController', 'index', 'GET');     // User settings
});

/**
 * Get the requested URL and process the route.
 */
$routeUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->route($routeUrl);