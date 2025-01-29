<?php

/**
 * Router class to manage application routing.
 * 
 * The Router class allows defining routes, handling HTTP requests, 
 * and mapping them to the appropriate controller and method. 
 * It supports dynamic parameters in routes.
 */
class Router {
    /**
     * @var array $routes Array storing the defined routes.
     */
    private $routes = [];

    /**
     * Add a new route to the router.
     * 
     * @param string $route       The URL pattern for the route. Dynamic segments should be enclosed in curly braces (e.g., "/users/{id}").
     * @param string $controller  The name of the controller to handle the route.
     * @param string $method      The name of the method within the controller to execute.
     * @param string $httpMethod  The HTTP method for the route (e.g., 'GET', 'POST'). Default is 'GET'.
     * 
     * @return void
     */
    public function add($route, $controller, $method, $httpMethod = 'GET') {
        // Convert dynamic segments (e.g., {id}) into regex named groups (e.g., (?P<id>[^/]+))
        $routePattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        // Pre-compile the regex pattern to enhance matching performance
        $this->routes[] = [
            'pattern' => '#^' . $routePattern . '$#', 
            'controller' => $controller,
            'method' => $method,
            'httpMethod' => strtoupper($httpMethod)
        ];
    }

    /**
     * Handle an incoming HTTP request and route it to the appropriate controller and method.
     * 
     * @param string $url The URL of the incoming request.
     * 
     * @return void
     */
    public function route($url) {
        $url = $this->normalizeUrl($url);

        // Iterate through defined routes to find a match
        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $url, $matches)) {
                // Check if the HTTP method matches
                if ($_SERVER['REQUEST_METHOD'] === $route['httpMethod']) {
                    $params = $this->extractParams($matches);

                    $controllerClass = 'App\\Controllers\\' . $route['controller'];

                    // Include the controller file and check existence
                    $this->loadController($controllerClass);

                    // Instantiate the controller and invoke the method
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $route['method']], $params);
                    return;
                } else {
                    $this->sendError(405, "Method Not Allowed");
                }
            }
        }

        // Route not found - include the 404 error page
        $this->sendError(404, "Page Not Found");
    }

    /**
     * Normalize the URL (e.g., removing base path and query strings).
     * 
     * @param string $url The URL to normalize.
     * @return string Normalized URL.
     */
    private function normalizeUrl($url) {
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $url = str_replace($baseUrl, '', $url);
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Extract dynamic parameters from regex matches.
     * 
     * @param array $matches The regex matches.
     * @return array The extracted parameters.
     */
    private function extractParams($matches) {
        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = filter_var($value, FILTER_SANITIZE_STRING); // Sanitize inputs for security
            }
        }
        return $params;
    }

    /**
     * Include the controller file and handle errors if not found.
     * 
     * @param string $controllerClass The fully qualified controller class name.
     * @return void
     */
    private function loadController($controllerClass) {
        // Autoloader will automatically take care of loading the controller class
        if (!class_exists($controllerClass)) {
            $this->sendError(500, "Controller not found: $controllerClass");
        }
    }



    /**
     * Send an HTTP error response and exit.
     * 
     * @param int $code The HTTP status code.
     * @param string $message The error message.
     * @return void
     */
    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
