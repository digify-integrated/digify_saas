<?php

/**
 * Router class to manage application routing with support for:
 * - Route definitions with dynamic parameters.
 * - Middleware execution before accessing routes.
 * - Route grouping with shared middlewares and URL prefixes.
 */
class Router {
    /**
     * @var array $routes Stores the defined routes with their patterns, controllers, methods, and middlewares.
     */
    private $routes = [];

    /**
     * @var array $groupMiddlewares Stores middlewares applied to a group of routes.
     */
    private $groupMiddlewares = [];

    /**
     * @var string $groupPrefix Stores the URL prefix applied to a group of routes.
     */
    private $groupPrefix = '';

    /**
     * Define a group of routes with shared middlewares and/or URL prefix.
     * 
     * @param array    $options  Associative array with optional keys:
     *                           - 'middleware' (array): List of middleware instances to apply.
     *                           - 'prefix' (string): Common prefix to be added to all routes in the group.
     * @param callable $callback A function that defines routes within the group.
     */
    public function group(array $options, callable $callback) {
        // Store previous group configurations
        $previousMiddlewares = $this->groupMiddlewares;
        $previousPrefix = $this->groupPrefix;

        // Merge middleware and prefix settings
        if (isset($options['middleware'])) {
            $this->groupMiddlewares = array_merge($this->groupMiddlewares, $options['middleware']);
        }

        if (isset($options['prefix'])) {
            $this->groupPrefix .= '/' . trim($options['prefix'], '/');
        }

        // Execute the callback to register grouped routes
        $callback($this);

        // Restore previous group configurations after the group execution
        $this->groupMiddlewares = $previousMiddlewares;
        $this->groupPrefix = $previousPrefix;
    }

    /**
     * Add a new route to the router.
     * 
     * @param string $route      The URL pattern for the route. Dynamic segments should be enclosed in `{}` (e.g., `/users/{id}`).
     * @param string $controller The name of the controller handling this route.
     * @param string $method     The method within the controller that should be executed.
     * @param string $httpMethod The HTTP method for this route (e.g., 'GET', 'POST'). Default is 'GET'.
     * @param array  $middlewares Optional array of middleware instances to apply to this specific route.
     */
    public function add($route, $controller, $method, $httpMethod = 'GET', $middlewares = []) {
        // Apply group prefix to the route
        $fullRoute = $this->groupPrefix . $route;

        // Merge group-level and route-specific middlewares
        $middlewares = array_merge($this->groupMiddlewares, $middlewares);

        // Convert dynamic segments `{param}` into regex named groups `(?P<param>[^/]+)`
        $routePattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $fullRoute);

        // Store route definition
        $this->routes[] = [
            'pattern' => '#^' . $routePattern . '$#',
            'controller' => $controller,
            'method' => $method,
            'httpMethod' => strtoupper($httpMethod),
            'middlewares' => $middlewares
        ];
    }

    /**
     * Handle an incoming request and route it to the appropriate controller and method.
     * 
     * @param string $url The requested URL.
     */
    public function route($url) {
        // Normalize the URL for comparison
        $url = $this->normalizeUrl($url);

        // Iterate over defined routes to find a match
        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $url, $matches)) {
                // Check if the HTTP method matches
                if ($_SERVER['REQUEST_METHOD'] === $route['httpMethod']) {
                    $params = $this->extractParams($matches);

                    // Execute middleware before accessing the controller
                    foreach ($route['middlewares'] as $middleware) {
                        if (!$middleware->handle()) {
                            return; // Stop execution if middleware fails
                        }
                    }

                    // Resolve the controller class name
                    $controllerClass = 'App\\Controllers\\' . $route['controller'];

                    // Load and instantiate the controller
                    $this->loadController($controllerClass);
                    $controller = new $controllerClass();

                    // Call the specified method with parameters
                    call_user_func_array([$controller, $route['method']], $params);
                    return;
                } else {
                    $this->sendError(405, "Method Not Allowed");
                }
            }
        }

        // No matching route found
        $this->sendError(404, "Page Not Found");
    }

    /**
     * Normalize the requested URL by stripping the base path and query strings.
     * 
     * @param string $url The raw requested URL.
     * @return string The normalized URL.
     */
    private function normalizeUrl($url) {
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return str_replace($baseUrl, '', parse_url($url, PHP_URL_PATH));
    }

    /**
     * Extract dynamic route parameters from regex matches.
     * 
     * @param array $matches The regex matches from the URL pattern.
     * @return array The extracted parameters.
     */
    private function extractParams($matches) {
        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }
        return $params;
    }

    /**
     * Load a controller class and handle errors if it does not exist.
     * 
     * @param string $controllerClass The fully qualified controller class name.
     */
    private function loadController($controllerClass) {
        if (!class_exists($controllerClass)) {
            $this->sendError(500, "Controller not found: $controllerClass");
        }
    }

    /**
     * Send an HTTP error response with a given status code and message.
     * 
     * @param int    $code    The HTTP status code (e.g., 404, 500).
     * @param string $message The error message to display.
     */
    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
