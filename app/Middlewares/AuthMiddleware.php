<?php

namespace App\Middlewares;

use App\Middlewares\MiddlewareInterface;

/**
 * Authentication Middleware
 * 
 * This middleware ensures that only authenticated users can access certain routes.
 */
class AuthMiddleware implements MiddlewareInterface {
    /**
     * Handle authentication check before allowing access to a route.
     * 
     * If the user is not authenticated (i.e., `user_account_id` is not set in the session),
     * a `403 Forbidden` response is sent.
     * 
     * @return bool Returns `true` if the user is authenticated, `false` otherwise.
     */
    public function handle() {
        if (!isset($_SESSION['user_account_id'])) {
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return false; // Stop further execution
        }

        return true; // Allow request to proceed
    }
}
