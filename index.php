<?php

// Start the session to manage user login state
session_start();

// Include the required classes for routing and autoloading
require_once './router.php';
require_once './autoload.php';

/**
 * Initialize Router and User Authentication Status
 */
$router = new Router();
$isLoggedIn = isset($_SESSION['user_account_id']); // Check if user is logged in

/**
 * Define all routes, including public, unauthenticated, and logged-in routes
 */
$routes = [
    // Public routes (accessible to all)
    'public' => [
        '/about' => ['AboutController', 'index', 'GET'],
        '/contact' => ['ContactController', 'index', 'GET'],
    ],
    
    // Routes accessible only to unauthenticated users (e.g., login, authentication)
    'unauthenticated' => [
        '/login' => ['AuthenticationController', 'index', 'GET'],
        '/authenticate' => ['AuthenticationController', 'authenticate', 'POST'],
    ],
    
    // Routes accessible only to logged-in users
    'protected' => [
        '/app' => ['AppController', 'index', 'GET'],
    ],
];

/**
 * Register Routes based on User Authentication Status
 */
foreach ($routes as $type => $routeSet) {
    foreach ($routeSet as $route => $controllerMethod) {
        // Public routes are accessible to everyone
        if ($type === 'public') {
            $httpMethod = $controllerMethod[2] ?? 'GET'; // Default to GET if not specified
            $router->add($route, $controllerMethod[0], $controllerMethod[1], $httpMethod);
        }

        // Unauthenticated routes should only be accessible when the user is not logged in
        elseif ($type === 'unauthenticated' && !$isLoggedIn) {
            $httpMethod = $controllerMethod[2] ?? 'GET';
            $router->add($route, $controllerMethod[0], $controllerMethod[1], $httpMethod);
        }

        // Protected routes are accessible only when the user is logged in
        elseif ($type === 'protected' && $isLoggedIn) {
            $httpMethod = $controllerMethod[2] ?? 'GET';
            $router->add($route, $controllerMethod[0], $controllerMethod[1], $httpMethod);
        }
    }
}

/**
 * Get the requested URL and route it to the appropriate controller method
 */
$routeUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Strip query parameters, focus on the path
$router->route($routeUrl);
