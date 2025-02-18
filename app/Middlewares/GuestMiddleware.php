<?php

namespace App\Middlewares;

use App\Middlewares\MiddlewareInterface;

/**
 * Guest Middleware
 * 
 * This middleware ensures that only **unauthenticated** users can access specific routes.
 * If the user is already logged in (i.e., authenticated), they are redirected to the `/app` route
 * to prevent access to guest-only pages like login or registration.
 */
class GuestMiddleware implements MiddlewareInterface {
    /**
     * Handles the check for guest users before allowing access to a route.
     * 
     * This method ensures that authenticated users (i.e., those with `user_account_id` in their session)
     * cannot access routes meant for guests (such as login or registration). If the user is authenticated,
     * they are redirected to the `/app` route to avoid them accessing guest-specific pages.
     * 
     * @return bool Always returns `true` to allow further execution if the user is not authenticated.
     *              If the user is authenticated, a redirect happens, and further execution is halted.
     */
    public function handle(): bool
    {
        // Check if the user is authenticated by looking for 'user_account_id' in the session
        if (isset($_SESSION['user_account_id'])) {
            // If authenticated, redirect to the '/app' route to prevent access to guest-only pages
            header("Location: /app");
            exit; // Stop further execution after redirect
        }

        // If not authenticated, allow the request to proceed to the guest-only route
        return true;
    }
}
