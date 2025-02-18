<?php

namespace App\Controllers;

use App\Models\Authentication;
use App\Models\SecuritySetting;
use App\Helpers\SystemHelper;
use App\Core\Security;
use App\Core\CSRFValidation;
use App\Services\AuthenticationService;

/**
 * Class AuthenticationController
 *
 * This controller handles the user authentication process, including displaying
 * the login form and validating login requests. It interacts with models, services,
 * and helpers to authenticate users while ensuring CSRF protection and validation.
 *
 * @package App\Controllers
 */
class AuthenticationController {
    /**
     * @var Authentication
     */
    private Authentication $authentication;

    /**
     * @var SecuritySetting
     */
    private SecuritySetting $securitySetting;

    /**
     * @var SystemHelper
     */
    private SystemHelper $systemHelper;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var CSRFValidation
     */
    private CSRFValidation $csrfValidation;

    /**
     * @var AuthenticationService
     */
    private AuthenticationService $authenticationService;

    /**
     * AuthenticationController constructor.
     *
     * @param Authentication $authentication The Authentication model for user authentication.
     * @param SecuritySetting $securitySetting The SecuritySetting model for security configurations.
     * @param SystemHelper $systemHelper The SystemHelper instance used for system-level utilities.
     * @param Security $security The Security class used for handling encrypted data and OTP generation.
     * @param CSRFValidation $csrfValidation The CSRFValidation class used to prevent cross-site request forgery.
     * @param AuthenticationService $authenticationService The service responsible for handling the authentication process.
     */
    public function __construct(
        Authentication $authentication,
        SecuritySetting $securitySetting,
        SystemHelper $systemHelper,
        Security $security,
        CSRFValidation $csrfValidation,
        AuthenticationService $authenticationService,
    ) {
        $this->authentication = $authentication;
        $this->securitySetting = $securitySetting;
        $this->systemHelper = $systemHelper;
        $this->security = $security;
        $this->csrfValidation = $csrfValidation;
        $this->authenticationService = $authenticationService;
    }

    /**
     * Displays the login page.
     * 
     * This method is used to show the login form to the user. It is typically called
     * when the user navigates to the login page or needs to re-authenticate.
     */
    public function index(): void {
        require_once './app/Views/Pages/Authentication/login.php';
    }

    /**
     * Handles user authentication.
     *
     * This method processes the login request, validates the incoming data, checks CSRF tokens,
     * and attempts to log the user in using the provided username and password.
     * It returns appropriate responses based on whether the login attempt is successful or not.
     * 
     * @throws \Exception If an unexpected error occurs during login.
     */
    public function authenticate(): void {
        // Ensure the request method is POST to prevent unauthorized access to login
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.1 405 Method Not Allowed");
            exit;
        }

        // Perform CSRF validation
        $this->csrfValidation->validate();

        // Sanitize input
        $username = filter_var($input['username'] ?? '', FILTER_SANITIZE_STRING);
        $password = filter_var($input['password'] ?? '', FILTER_SANITIZE_STRING);

        // Attempt to authenticate the user
        try {
            $this->authenticationService->attemptLogin(
                $username,
                $password
            );
        } catch (\Exception $e) {
            $this->systemHelper->sendErrorResponse($e->getMessage());
        }
    }
}
