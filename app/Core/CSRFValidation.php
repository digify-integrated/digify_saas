<?php
declare(strict_types=1);

namespace App\Core;

use App\Helpers\SystemHelper;

/**
 * Class CSRFValidation
 * 
 * This class handles Cross-Site Request Forgery (CSRF) token validation. 
 * It ensures that the CSRF token submitted with a form matches the one stored in the session,
 * providing protection against CSRF attacks.
 * 
 * @package App\Core
 */
class CSRFValidation {
    /**
     * @var SystemHelper
     */
    private SystemHelper $systemHelper;

    /**
     * CSRFValidation constructor.
     * 
     * @param SystemHelper $systemHelper The SystemHelper instance used for response handling.
     */
    public function __construct(SystemHelper $systemHelper) {
        $this->systemHelper = $systemHelper;
    }

    /**
     * Validates the CSRF token against the one stored in the session.
     * 
     * This method ensures that a valid CSRF token is provided in the request.
     * If the token is missing or invalid, an error response will be triggered.
     * 
     * @return void
     * @throws \Exception If the CSRF token is missing or invalid.
     */
    public function validate(): void {
        // Check if the CSRF token is present in both the POST request and the session
        if (!$this->isCsrfTokenPresent()) {
            $this->systemHelper->sendErrorResponse('CSRF token is missing.');
        }

        // Verify that the CSRF token from the session matches the token from the POST request
        if (!$this->isCsrfTokenValid()) {
            $this->systemHelper->sendErrorResponse('Invalid CSRF token.');
        }
    }

    /**
     * Checks if the CSRF token is present in the POST request.
     * 
     * @return bool Returns true if the CSRF token is present in the POST request.
     */
    private function isCsrfTokenPresent(): bool {
        return isset($_POST['csrf_token']) && isset($_SESSION['csrf_token']);
    }

    /**
     * Verifies that the CSRF token provided in the POST request is valid.
     * 
     * This method uses the `hash_equals` function to prevent timing attacks when comparing the tokens.
     * 
     * @return bool Returns true if the CSRF token is valid.
     */
    private function isCsrfTokenValid(): bool {
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
