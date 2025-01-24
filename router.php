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
        // Convert dynamic segments (e.g., {id}) into regex named groups (e.g., (?P<id>\w+))
        $routePattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\w+)', $route);

        // Store route information
        $this->routes[$routePattern] = [
            'controller' => $controller,
            'method' => $method,
            'httpMethod' => $httpMethod
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
        // Get the base URL (to handle the script's directory)
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        
        // Remove the base URL from the requested URL
        $url = str_replace($baseUrl, '', $url);

        // Parse the URL to get only the path
        $url = parse_url($url, PHP_URL_PATH);

        // Iterate through defined routes to find a match
        foreach ($this->routes as $pattern => $routeData) {
            if (preg_match("#^$pattern$#", $url, $matches)) {
                // Check if the HTTP method matches
                if ($_SERVER['REQUEST_METHOD'] === $routeData['httpMethod']) {
                    $params = [];

                    // Extract dynamic parameters from the route
                    foreach ($matches as $key => $value) {
                        if (!is_int($key)) {
                            $params[$key] = $value;
                        }
                    }

                    $controller = $routeData['controller'];
                    $method = $routeData['method'];

                    // Include the controller file
                    require_once './app/Controllers/' . $controller . '.php';

                    // Fully qualified class name with namespace
                    $controllerClass = 'App\\Controllers\\' . $controller;

                    // Instantiate the controller and call the appropriate method
                    $controllerObj = new $controllerClass();
                    call_user_func_array([$controllerObj, $method], $params);
                    return;
                } else {
                    // HTTP method not allowed
                    header("HTTP/1.1 405 Method Not Allowed");
                    exit;
                }
            }
        }

        // Route not found - include the 404 error page
        require_once './app/Views/Errors/404.php';
    }
}
