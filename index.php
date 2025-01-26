<?php

// Start the session to manage user login state
session_start();

/**
 * Include the Router class to handle routing of requests to the appropriate controllers.
 * The Router class maps URLs to controllers and their respective methods.
 */
require_once './router.php';

/**
 * Instantiate the Router object to manage routing functionality.
 * The Router object is responsible for registering routes and directing requests to the correct controller.
 */
$router = new Router();

/**
 * Define public routes that are accessible to unauthenticated users.
 * Public routes can be accessed without user login, e.g., login, registration pages.
 * These routes are registered when the user is not logged in.
 */
$publicRoutes = [
    '/login' => ['AuthenticationController', 'index'], // Route: '/' -> AuthenticationController::index
    '/authenticate' => ['AuthenticationController', 'authenticate', 'POST'], // Route: '/' -> AuthenticationController::index
];

/**
 * Define routes that are accessible only to authenticated users.
 * These routes require the user to be logged in (i.e., a valid session exists).
 * Routes in this array are registered only if the user is logged in.
 */
$loggedInRoutes = [
    '/app' => ['AppController', 'index'], // Route: '/app' -> AppController::index
];

/**
 * Check if the user is logged in by verifying the session.
 * The session value 'user_account_id' is used to determine if the user is authenticated.
 */
$isLoggedIn = isset($_SESSION['user_account_id']);

/**
 * Add public routes to the router if the user is not logged in.
 * These routes are accessible without authentication, so they are only added when $isLoggedIn is false.
 */
foreach ($publicRoutes as $route => $controllerMethod) {
    if (!$isLoggedIn) {
        $router->add($route, $controllerMethod[0], $controllerMethod[1]);
    }
}

/**
 * Add logged-in routes to the router if the user is logged in.
 * These routes are protected and only accessible to authenticated users, so they are added when $isLoggedIn is true.
 */
foreach ($loggedInRoutes as $route => $controllerMethod) {
    if ($isLoggedIn) {
        $router->add($route, $controllerMethod[0], $controllerMethod[1]);
    }
}

/**
 * Get the current URL from the server's request URI.
 * The request URI is used to determine the page or route being accessed by the user.
 */
$url = $_SERVER['REQUEST_URI'];

/**
 * Route the request to the appropriate controller and method.
 * The $router->route method will match the current URL to the corresponding route and invoke the controller's method.
 */
$router->route($url);