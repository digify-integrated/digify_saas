<?php

namespace App\Middlewares;

use App\Middlewares\MiddlewareInterface;

/**
 * Guest Middleware
 * 
 * This middleware ensures that only **unauthenticated** users can access certain routes.
 * If the user is already logged in, they are redirected to the `/app` route.
 */
class GuestMiddleware implements MiddlewareInterface {
    /**
     * Handle guest user check before allowing access to a route.
     * 
     * If the user is already authenticated, they are redirected to `/app` to prevent 
     * accessing guest-only pages like login or registration.
     * 
     * @return bool Always returns `true` to allow further execution.
     */
    public function handle() {
        if (isset($_SESSION['user_account_id'])) {
            header("Location: /app");
            exit; // Prevent further execution
        }

        return true; // Allow request to proceed
    }
}
