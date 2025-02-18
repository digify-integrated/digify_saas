<?php

namespace App\Services;

use App\Models\Authentication;
use App\Models\SecuritySetting;
use App\Core\Security;
use App\Helpers\SystemHelper;
use App\Mail\OTPMail;
use DateTime;
use Exception;

/**
 * Class AuthenticationService
 * Handles user authentication processes including login attempts, password expiry, 
 * two-factor authentication, and session creation.
 * 
 * @package App\Services
 */
class AuthenticationService {
    private Authentication $authentication;
    private SecuritySetting $securitySetting;
    private Security $security;
    private SystemHelper $systemHelper;
    private OTPMail $otpMail;

    /**
     * AuthenticationService constructor.
     * 
     * @param Authentication $authentication
     * @param SecuritySetting $securitySetting
     * @param Security $security
     * @param SystemHelper $systemHelper
     * @param OTPMail $otpMail
     */
    public function __construct(
        Authentication $authentication,
        SecuritySetting $securitySetting,
        Security $security,
        SystemHelper $systemHelper,
        OTPMail $otpMail
    ) {
        $this->authentication = $authentication;
        $this->securitySetting = $securitySetting;
        $this->security = $security;
        $this->systemHelper = $systemHelper;
        $this->otpMail = $otpMail;
    }

    /**
     * Attempt to log a user in with the provided credentials.
     * 
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @return void
     * @throws Exception If login attempt fails or any condition is not met.
     */
    public function attemptLogin(string $username, string $password): void {
        $loginCredentials = $this->authentication->fetchLoginCredentials($username);

        if (!$loginCredentials) {
            $this->systemHelper->sendErrorResponse('Invalid credentials. Please check and try again.');
        }

        $userAccountID = $loginCredentials['user_account_id'];
        $email = $loginCredentials['email'];
        $storedPassword = $this->security->decryptData($loginCredentials['password']);
        $failedLoginAttempts = $loginCredentials['failed_login_attempts'] ?? 0;
        $lastFailedLoginAttempt = $loginCredentials['last_failed_login_attempt'];
        $lockedDuration = $loginCredentials['locked_duration'] ?? 0;
        $active = $loginCredentials['active'];
        $passwordExpiryDate = $loginCredentials['password_expiry_date'] ?? date('Y-m-d');
        $twoFactorAuth = $loginCredentials['two_factor_auth'] ?? 'No';

        // Password validation
        if ($password !== $storedPassword) {
            return $this->handleFailedLogin($userAccountID, $failedLoginAttempts, $lastFailedLoginAttempt, $lockedDuration);
        }

        // Account validation
        if ($active === 'No') {
            $this->systemHelper->sendErrorResponse('Your account is inactive. Please contact the administrator for assistance.');
        }

        // Handle account lock duration
        if ($lockedDuration > 0) {
            $this->handleUnlockTime($lastFailedLoginAttempt, $lockedDuration);
        }

        // Handle expired password
        $this->handleExpiredPassword($passwordExpiryDate);

        // Handle two-factor authentication
        if ($twoFactorAuth === 'Yes') {
            $this->handleTwoFactorAuth($userAccountID, $email);
        }
        
        // Create user session
        $this->createSession($userAccountID);
    }

    /**
     * Handles a failed login attempt, including locking the account after multiple failed attempts.
     * 
     * @param int $userAccountID The user account ID.
     * @param int $failedLoginAttempts The current failed login attempts count.
     * @param string $lastFailedLoginAttempt The last failed login attempt timestamp.
     * @param int $lockedDuration The current account lock duration in seconds.
     * @return void
     * @throws Exception If the account is locked or the max failed attempts are exceeded.
     */
    private function handleFailedLogin(int $userAccountID, int $failedLoginAttempts, string $lastFailedLoginAttempt, int $lockedDuration): void {
        $failedLoginAttempts++;
        
        $maxFailedAttempts = $this->securitySetting->fetchSecuritySetting(1)['value'] ?? 5;

        if ($failedLoginAttempts <= $maxFailedAttempts) {
            $this->authentication->updateLoginAttempt($userAccountID, $failedLoginAttempts);
            $this->systemHelper->sendSuccessResponse('Invalid credentials. Please check and try again.');
        }

        $this->handleUnlockTime($lastFailedLoginAttempt, $lockedDuration);

        $baseLockDuration = $this->securitySetting->fetchSecuritySetting(8)['value'] ?? BASE_LOCK_DURATION;
        $maxLockDuration = 3600; // 1 hour max lock duration
        $lockDuration = min($maxLockDuration, $baseLockDuration * pow(2, ($failedLoginAttempts - $maxFailedAttempts)));

        $this->authentication->updateAccountLockDuration($userAccountID, $lockDuration);
        $this->systemHelper->sendSuccessResponse('Your account is locked. Try again later.');
    }

    /**
     * Handles the unlock time for an account after it has been locked.
     * 
     * @param string $lastFailedLoginAttempt The timestamp of the last failed login attempt.
     * @param int $lockedDuration The account lock duration in seconds.
     * @return void
     * @throws Exception If the account is still locked and cannot be accessed.
     */
    private function handleUnlockTime(string $lastFailedLoginAttempt, int $lockedDuration): void {
        $unlockTime = strtotime($lastFailedLoginAttempt) + $lockedDuration;
        if (time() <= $unlockTime) {
            $remainingTime = $unlockTime - time();
            $this->systemHelper->sendSuccessResponse("Account locked. Try again in $remainingTime seconds.");
        }
    }

    /**
     * Checks if the user's password has expired.
     * 
     * @param string $passwordExpiryDate The expiry date of the password.
     * @return void
     * @throws Exception If the password is expired.
     */
    private function handleExpiredPassword($passwordExpiryDate): void {
        if (new DateTime() > new DateTime($passwordExpiryDate)) {
            $this->systemHelper->sendSuccessResponse('Your password has expired. Please reset it to proceed.');
        }
    }

    /**
     * Creates a user session upon successful login.
     * 
     * @param int $userAccountID The user account ID.
     * @return void
     */
    private function createSession(int $userAccountID): void {
        $this->authentication->updateLastConnection($userAccountID);
    
        $_SESSION['user_account_id'] = $userAccountID;

        $this->systemHelper->sendSuccessResponse('', '',  ['redirectLink' => '/apps']);
    }

    /**
     * Initiates the two-factor authentication process by sending an OTP.
     * 
     * @param int $userAccountID The user account ID.
     * @param string $email The user's email address to send the OTP.
     * @return void
     */
    private function handleTwoFactorAuth(int $userAccountID, string $email): void {
        $securitySettingDetails = $this->securitySetting->fetchSecuritySetting(5);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $otp = $this->security->generateOTPToken(6);
        $encryptedOTP = $this->security->encryptData($otp);
        $otpExpiryDate = date('Y-m-d H:i:s', strtotime('+' . $otpDuration . ' minutes'));
    
        $this->authentication->updateOTP($userAccountID, $encryptedOTP, $otpExpiryDate);
        $this->otpMail->sendOTP($email, $otp);

        $this->systemHelper->sendSuccessResponse('', ['redirectLink' => '/otp/' . $userAccountID]);
    }
}