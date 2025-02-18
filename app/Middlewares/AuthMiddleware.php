<?php

namespace App\Middlewares;

use App\Middlewares\MiddlewareInterface;

/**
 * Authentication Middleware
 * 
 * This middleware ensures that only authenticated users can access certain routes.
 * It checks if the user is authenticated by verifying if `user_account_id` is set in the session.
 * If not, it returns a 403 Forbidden response.
 */
class AuthMiddleware implements MiddlewareInterface {
    /**
     * Handles the authentication check before allowing access to a route.
     * 
     * This method verifies if the user is authenticated by checking if `user_account_id` is present
     * in the session. If the user is not authenticated, it sends a `403 Forbidden` response
     * and stops further execution of the request.
     * 
     * @return bool Returns `true` if the user is authenticated and the request is allowed to proceed.
     *              Returns `false` if the user is not authenticated, and the request is blocked with a `403` response.
     */
    public function handle(): bool
    {
        // Check if user is authenticated by looking for the 'user_account_id' in the session
        if (!isset($_SESSION['user_account_id'])) {
            // User is not authenticated, send a '403 Forbidden' response
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return false; // Stop further execution
        }

        // User is authenticated, allow the request to proceed
        return true;
    }
}
