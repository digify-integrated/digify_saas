<?php

namespace App\Middlewares;

/**
 * Middleware Interface
 * 
 * This interface defines the structure for middleware classes that handle HTTP requests
 * before they reach the application’s core logic. It enforces the implementation of 
 * a `handle` method to control the flow of request execution.
 */
interface MiddlewareInterface {
    /**
     * Handle the request and determine whether to proceed with further execution.
     * 
     * This method is implemented by each middleware to define its specific logic,
     * such as authentication checks, redirection, logging, or other actions that should
     * be performed before allowing the request to proceed to the application logic.
     * 
     * @return bool Returns `true` to continue the request execution or `false` to halt the request
     *              (for example, in the case of an authentication failure or invalid request).
     */
    public function handle(): bool;
}
