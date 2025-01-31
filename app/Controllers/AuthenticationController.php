<?php

namespace App\Controllers;

use App\Models\Authentication; 
use App\Models\SecuritySetting; 
use App\Helpers\SystemHelper; 
use App\Core\Security;

use DateTime;

class AuthenticationController {
    private $authentication;
    private $securitySetting;
    private $systemHelper;
    private $security;

    public function __construct() {
        $this->authentication = new Authentication();
        $this->securitySetting = new SecuritySetting();
        $this->systemHelper = new SystemHelper();
        $this->security = new Security();
    }

    public function index() {
        require_once './app/Views/Pages/Authentication/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $deviceInfo = filter_input(INPUT_POST, 'device_info', FILTER_SANITIZE_STRING);

            $loginCredentials = $this->authentication->checkLoginCredentialsExist(null, $username);

            if ($loginCredentials['total'] === 0) {
                $this->systemHelper->sendErrorResponse('Invalid credentials. Please check and try again.');
            }

            $ipAddress = $this->systemHelper->getPublicIPAddress();
            $location = $this->systemHelper->getLocation($ipAddress);

            $loginCredentialsDetails = $this->authentication->getLoginCredentials(null, $username);
            $userAccountID = $loginCredentialsDetails['user_account_id'] ?? '';
            $email = $loginCredentialsDetails['email'] ?? '';
            $active = $loginCredentialsDetails['active'] ?? '';
            $locked = $loginCredentialsDetails['locked'] ?? '';
            $userPassword = $this->security->decryptData($loginCredentialsDetails['password'] ?? '');
            $failedLoginAttempts = $loginCredentialsDetails['failed_login_attempts'] ?? '';
            $passwordExpiryDate = $loginCredentialsDetails['password_expiry_date'] ?? '';
            $twoFactorAuth = $loginCredentialsDetails['two_factor_auth'] ?? '';
            $lockedDuration = $loginCredentialsDetails['locked_duration'] ?? '';
            $lastFailedLoginAttempt = $loginCredentialsDetails['last_failed_login_attempt'] ?? '';
            $encryptedUserID = $this->security->encryptData($userAccountID);

            if ($password !== $userPassword) {
                $failedLoginAttempts++;
            
                $securitySettingDetails = $this->securitySetting->getSecuritySetting(1);
                $maxFailedLoginAttempts = $securitySettingDetails['value'] ?? MAX_FAILED_LOGIN_ATTEMPTS;
        
                if($failedLoginAttempts <= $maxFailedLoginAttempts){
                    $this->authentication->updateLoginAttempt($userAccountID, $failedLoginAttempts);
        
                    $this->systemHelper->sendErrorResponse('Invalid credentials. Please check and try again.');
                }

                $time = time();
                $lastFailedLoginTimestamp = strtotime($lastFailedLoginAttempt);
                $unlockTime = $lastFailedLoginTimestamp + $lockedDuration;
            
                if ($time <= $unlockTime) {
                    $remainingTime = $unlockTime - $time;
                    $durationParts = $this->systemHelper->formatDuration($remainingTime);
                }
                else {
                    $this->authentication->updateLoginAttempt($userAccountID, $failedLoginAttempts);

                    $userAccountLockDurationSettingDetails = $this->securitySetting->getSecuritySetting(9);
                    $baseLockDuration = $userAccountLockDurationSettingDetails['value'] ?? BASE_LOCK_DURATION;
                    $maxLockDuration = 3600;
            
                    $lockDuration = min($maxLockDuration,$baseLockDuration * pow(2, ($failedLoginAttempts - $maxFailedLoginAttempts)));
            
                    $this->authentication->updateAccountLockDuration($userAccountID, $lockDuration);
            
                    $durationParts = $this->systemHelper->formatDuration($lockDuration);
                }
            
                $message = 'Your account is locked. Try again in ' . (!empty($durationParts) ? implode(', ', $durationParts) : 'a moment') . '.';
                
                $this->systemHelper->sendErrorResponse($message);
            }
    
            if ($active === 'No') {
                $this->systemHelper->sendErrorResponse('Your account is inactive. Please contact the administrator for assistance.');
            }

            if ($locked === 'Yes') {
                $this->systemHelper->sendErrorResponse('Your account is locked. Please contact the administrator for assistance.');
            }
    
            if ($this->checkPasswordHasExpired($passwordExpiryDate)) {
                $this->systemHelper->sendErrorResponse('Your password has expired. Please reset it to proceed.');
            }
        
            if ($twoFactorAuth === 'Yes') {
                $this->handleTwoFactorAuth($userAccountID, $email, $encryptedUserID);
            }

            $sessionToken = $this->security->generateToken(6, 6);
            $encryptedSessionToken = $this->security->encryptData($sessionToken);
    
            $this->authentication->updateLastConnection($userAccountID, $encryptedSessionToken);
            $this->authentication->insertLoginSession($userAccountID, $location, 'Ok', $deviceInfo, $ipAddress);
    
            $_SESSION['user_account_id'] = $userAccountID;
            $_SESSION['session_token'] = $encryptedSessionToken;
    
            #$this->systemHelper->sendSuccessResponse('', '',  ['redirectLink' => 'apps.php']);
        }
        else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit;
        }
    }

    private function handlePasswordExpiration($userAccountID) {
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(3);
        $defaultForgotPasswordLink = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_RECOVERY_LINK;

        $resetPasswordTokenDurationDetails = $this->securitySetting->getSecuritySetting(6);
        $resetPasswordTokenDuration = $resetPasswordTokenDurationDetails['value'] ?? RESET_PASSWORD_TOKEN_DURATION;

        $resetToken = $this->security->generateToken();
        $encryptedResetToken = $this->security->encryptData($resetToken);
        $encryptedUserAccountID = $this->security->encryptData($userAccountID);
        $resetTokenExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('+'. $resetPasswordTokenDuration .' minutes')));
    
        $this->authentication->updateResetToken($userAccountID, $encryptedResetToken, $resetTokenExpiryDate);

        $this->systemHelper->sendErrorResponse('Account Password Expired', 'Your password has expired. Please reset it to proceed.', [
            'passwordExpired' => true,
            'redirectLink' => $defaultForgotPasswordLink . $encryptedUserAccountID .'&token=' . $encryptedResetToken
        ]);
    }

    private function handleTwoFactorAuth($userAccountID, $email, $encryptedUserID) {
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $otp = $this->security->generateOTPToken(6);
        $encryptedOTP = $this->security->encryptData($otp);
        $otpExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('+'. $otpDuration .' minutes')));
        $failedLoginAttempts = $this->security->encryptData(0);
    
        $this->authentication->updateOTP($userAccountID, $encryptedOTP, $otpExpiryDate, $failedLoginAttempts);
        $this->sendOTP($email, $otp, 1);

        $this->systemHelper->sendSuccessResponse('', '', ['redirectLink' => 'otp-verification.php?id=' . $encryptedUserID]);
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        $checkLoginCredentialsExist = $this->authentication->checkLoginCredentialsExist(null, $email);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper->sendErrorResponse('Invalid Credentials', 'Invalid credentials. Please check and try again.',  ['notExist' => true]);
        }

        $loginCredentialsDetails = $this->authentication->getLoginCredentials(null, $email);
        $userAccountID = $loginCredentialsDetails['user_account_id'];
        $active = $this->security->decryptData($loginCredentialsDetails['active']);
        $locked = $this->security->decryptData($loginCredentialsDetails['locked']);
        $encryptedUserID = $this->security->encryptData($userAccountID);
    
        if ($active === 'No') {
            $this->systemHelper->sendErrorResponse('Account Inactive', 'Your account is inactive. Please contact the administrator for assistance.',  ['notActive' => true]);
        }
    
        if ($locked === 'Yes') {
            $this->systemHelper->sendErrorResponse('Account Locked', 'Your account is locked. Please contact the administrator for assistance.',  ['locked' => true]);
        }

        $securitySettingDetails = $this->securitySetting->getSecuritySetting(6);
        $resetPasswordTokenDuration = $securitySettingDetails['value'] ?? RESET_PASSWORD_TOKEN_DURATION;

        $resetToken = $this->security->generateToken();
        $encryptedResetToken = $this->security->encryptData($resetToken);
        $resetTokenExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('+'. $resetPasswordTokenDuration .' minutes')));
    
        $this->authentication->updateResetToken($userAccountID, $encryptedResetToken, $resetTokenExpiryDate);
        $this->sendPasswordReset($email, $encryptedUserID, $encryptedResetToken, $resetPasswordTokenDuration, 2);

        $this->systemHelper->sendSuccessResponse('Password Reset', "We've sent a password reset link to your registered email address. Please check your inbox and follow the provided instructions to securely reset your password. If you don't receive the email within a few minutes, please also check your spam folder.");
    }

    public function verifyOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_SANITIZE_NUMBER_INT);
        $otpCode1 = filter_input(INPUT_POST, 'otp_code_1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode2 = filter_input(INPUT_POST, 'otp_code_2', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode3 = filter_input(INPUT_POST, 'otp_code_3', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode4 = filter_input(INPUT_POST, 'otp_code_4', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode5 = filter_input(INPUT_POST, 'otp_code_5', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode6 = filter_input(INPUT_POST, 'otp_code_6', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $deviceInfo = filter_input(INPUT_POST, 'device_info', FILTER_SANITIZE_STRING);
        $otpVerificationCode = $otpCode1 . $otpCode2 . $otpCode3 . $otpCode4 . $otpCode5 . $otpCode6;

        $checkLoginCredentialsExist = $this->authentication->checkLoginCredentialsExist($userAccountID, null);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper->sendErrorResponse('Invalid Credentials', 'Invalid credentials. Please check and try again.',  ['notExist' => true]);
        }

        $loginCredentialsDetails = $this->authentication->getLoginCredentials($userAccountID, null);
        $otp = $this->security->decryptData($loginCredentialsDetails['otp']);
        $failedOTPAttempts = $this->security->decryptData($loginCredentialsDetails['failed_otp_attempts']);
        $otpExpiryDate = $this->security->decryptData($loginCredentialsDetails['otp_expiry_date']);
        $active = $this->security->decryptData($loginCredentialsDetails['active']);
        $locked = $this->security->decryptData($loginCredentialsDetails['locked']);
    
        if ($active === 'No') {
            $this->systemHelper->sendErrorResponse('Account Inactive', 'Your account is inactive. Please contact the administrator for assistance.',  ['notActive' => true]);
        }
    
        if ($locked === 'Yes') {
            $this->systemHelper->sendErrorResponse('Account Locked', 'Your account is locked. Please contact the administrator for assistance.',  ['locked' => true]);
        }

        $ipAddress = $this->systemHelper->getPublicIPAddress();
        $location = $this->systemHelper->getLocation($ipAddress);

        if ($otpVerificationCode !== $otp) {
            $securitySettingDetails = $this->securitySetting->getSecuritySetting(2);
            $maxFailedOTPAttempts = $securitySettingDetails['value'] ?? MAX_FAILED_OTP_ATTEMPTS;

            if ($failedOTPAttempts >= $maxFailedOTPAttempts) {
                $otpExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('-1 year')));
                $this->authentication->updateOTPAsExpired($userAccountID, $otpExpiryDate);

                $this->authentication->insertLoginSession($userAccountID, $location, 'Invalid OTP Code', $deviceInfo, $ipAddress);

                $this->systemHelper->sendErrorResponse('Invalid OTP Code', 'The OTP code you entered is invalid. Please request a new one.',  ['otpMaxFailedAttempt' => true]);
            }
    
            $this->authentication->updateFailedOTPAttempts($userAccountID, $this->security->encryptData($failedOTPAttempts + 1));

            $this->systemHelper->sendErrorResponse('Invalid OTP Code', 'The OTP code you entered is incorrect.',  ['incorrectOTPCode' => true]);
        }

        if (strtotime(date('Y-m-d H:i:s')) > strtotime($otpExpiryDate)) {
            $this->systemHelper->sendErrorResponse('Expired OTP Code', 'The OTP code you entered is expired. Please request a new one.',  ['expiredOTP' => true]);
        }

        $sessionToken = $this->security->generateToken(6, 6);
        $encryptedSessionToken = $this->security->encryptData($sessionToken);

        $this->authentication->updateLastConnection($userAccountID, $encryptedSessionToken);

        $this->authentication->insertLoginSession($userAccountID, $location, 'Ok', $deviceInfo, $ipAddress);
        
        $_SESSION['user_account_id'] = $userAccountID;
        $_SESSION['session_token'] = $encryptedSessionToken;

        $this->systemHelper->sendSuccessResponse('', '',  ['redirectLink' => 'apps.php']);
    }

    public function passwordReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_SANITIZE_NUMBER_INT);
        $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);

        $checkLoginCredentialsExist = $this->authentication->checkLoginCredentialsExist($userAccountID, null);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper->sendErrorResponse('Invalid Credentials', 'Invalid credentials. Please check and try again.',  ['notExist' => true]);
        }

        $loginCredentialsDetails = $this->authentication->getLoginCredentials($userAccountID, null);
        $email = $loginCredentialsDetails['email'];
        $active = $this->security->decryptData($loginCredentialsDetails['active']);
        $locked = $this->security->decryptData($loginCredentialsDetails['locked']);
        $resetTokenExpiryDate = $this->security->decryptData($loginCredentialsDetails['reset_token_expiry_date']);
    
        if ($active === 'No') {
            $this->systemHelper->sendErrorResponse('Account Inactive', 'Your account is inactive. Please contact the administrator for assistance.',  ['notActive' => true]);
        }
    
        if ($locked === 'Yes') {
            $this->systemHelper->sendErrorResponse('Account Locked', 'Your account is locked. Please contact the administrator for assistance.',  ['locked' => true]);
        }

        if(strtotime(date('Y-m-d H:i:s')) > strtotime($resetTokenExpiryDate)){
            $this->systemHelper->sendErrorResponse('Password Reset Link Expired', 'The password reset link has expired. Please request a new link to reset your password.');
        }

        $checkPasswordHistory = $this->checkPasswordHistory($userAccountID, $newPassword);
    
        if ($checkPasswordHistory > 0) {
            $this->systemHelper->sendErrorResponse('Password Already Used', 'Your new password cannot be identical to your previous one for security reasons. Please choose a different password to proceed.', ['passwordExist' => true]);
        }

        $securitySettingDetails = $this->securitySetting->getSecuritySetting(4);
        $defaultPasswordDuration = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_DURATION;
    
        $passwordExpiryDate = $this->security->encryptData(date('Y-m-d', strtotime('+'. $defaultPasswordDuration .' days')));
        
        $encryptedPassword = $this->security->encryptData($newPassword);

        $this->authentication->updateUserPassword($userAccountID, $encryptedPassword, $passwordExpiryDate, $this->security->encryptData('No'), $this->security->encryptData(0), $this->security->encryptData(0));

        $resetTokenExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('-1 year')));
        $this->authentication->updateResetTokenAsExpired($userAccountID, $resetTokenExpiryDate);

        $this->systemHelper->sendSuccessResponse('Password Reset Success', 'Your password has been successfully updated. For security reasons, please use your new password to log in.');
    }

    public function resendOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);

        $loginCredentialsDetails = $this->authentication->getLoginCredentials($userAccountID, null);
        $email = $loginCredentialsDetails['email'] ?? null;

        if(empty($email)){
            $this->systemHelper->sendErrorResponse('Account Inactive', 'Your account is inactive. Please contact the administrator for assistance.',  ['notActive' => true]);
        }
        
        $this->resendOTPCode($userAccountID, $email);
        
        $response = [
            'success' => true
        ];

        echo json_encode($response);
        exit;
    }
    
    private function resendOTPCode($userAccountID, $email) {
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $otp = $this->security->generateOTPToken(6);
        $encryptedOTP = $this->security->encryptData($otp);
        $otpExpiryDate = $this->security->encryptData(date('Y-m-d H:i:s', strtotime('+'. $otpDuration .' minutes')));
        $failedLoginAttempts = $this->security->encryptData(0);
    
        $this->authentication->updateOTP($userAccountID, $encryptedOTP, $otpExpiryDate, $failedLoginAttempts);
        $this->sendOTP($email, $otp, 1);
    }

    public function sendOTP($email, $otp, $notificationSettingID) {
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $notificationSettingDetails = $this->notificationSetting->getNotificationSettingEmailTemplate($notificationSettingID);
        $emailSettingID = $notificationSettingDetails['email_setting_id'] ?? null;

        $emailSetting = $this->emailSetting->getEmailSetting($emailSettingID);
        $mailFromName = $emailSetting['mail_from_name'] ?? null;
        $mailFromEmail = $emailSetting['mail_from_email'] ?? null;

        $emailSubject = $notificationSettingDetails['email_notification_subject'] ?? null;
        $emailBody = $notificationSettingDetails['email_notification_body'] ?? null;
        $emailBody = str_replace('#{OTP_CODE}', $otp, $emailBody);
        $emailBody = str_replace('#{OTP_CODE_VALIDITY}', $otpDuration . ' minutes', $emailBody);

        $message = file_get_contents('../../assets/template/default-email.html');
        $message = str_replace('{EMAIL_SUBJECT}', $emailSubject, $message);
        $message = str_replace('{EMAIL_CONTENT}', $emailBody, $message);
    
        $mailer = new PHPMailer();
        $this->configureSMTP($emailSettingID, $mailer);
        
        $mailer->setFrom($mailFromEmail, $mailFromName);
        $mailer->addAddress($email);
        $mailer->Subject = $emailSubject;
        $mailer->Body = $message;
    
        if ($mailer->send()) {
            return true;
        }
        else {
            return 'Failed to send OTP. Error: ' . $mailer->ErrorInfo;
        }
    }
    
    public function sendPasswordReset($email, $userAccountID, $resetToken, $resetPasswordTokenDuration, $notificationSettingID) {
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(3);
        $defaultForgotPasswordLink = $securitySettingDetails['value'] ?? null;

        $notificationSettingDetails = $this->notificationSetting->getNotificationSettingEmailTemplate($notificationSettingID);
        $emailSettingID = $notificationSettingDetails['email_setting_id'] ?? null;

        $emailSetting = $this->emailSetting->getEmailSetting($emailSettingID);
        $mailFromName = $emailSetting['mail_from_name'] ?? MAIL_FROM_NAME;
        $mailFromEmail = $emailSetting['mail_from_email'] ?? MAIL_FROM_EMAIL;

        $emailSubject = $notificationSettingDetails['email_notification_subject'] ?? null;
        $emailBody = $notificationSettingDetails['email_notification_body'] ?? null;
        $emailBody = str_replace('#{RESET_LINK}', $defaultForgotPasswordLink . $userAccountID .'&token=' . $resetToken, $emailBody);
        $emailBody = str_replace('#{RESET_LINK_VALIDITY}', $resetPasswordTokenDuration . ' minute', $emailBody);

        $message = file_get_contents('../../assets/template/default-email.html');
        $message = str_replace('{EMAIL_SUBJECT}', $emailSubject, $message);
        $message = str_replace('{EMAIL_CONTENT}', $emailBody, $message);
    
        $mailer = new PHPMailer();
        $this->configureSMTP($emailSettingID, $mailer);
        
        $mailer->setFrom($mailFromEmail, $mailFromName);
        $mailer->addAddress($email);
        $mailer->Subject = $emailSubject;
        $mailer->Body = $message;
    
        if ($mailer->send()) {
            return true;
        } 
        else {
            return 'Failed to send password reset email. Error: ' . $mailer->ErrorInfo;
        }
    }

    private function configureSMTP($emailSettingID, $mailer, $isHTML = true) {
        $emailSetting = $this->emailSetting->getEmailSetting($emailSettingID);
    
        $mailer->isSMTP();
        $mailer->isHTML($isHTML);
        $mailer->Host = $emailSetting['mail_host'] ?? MAIL_SMTP_SERVER;
        $mailer->SMTPAuth = !empty($emailSetting['smtp_auth']);
        $mailer->Username = $emailSetting['mail_username'] ?? MAIL_USERNAME;
        $mailer->Password = !empty($emailSetting['mail_password']) ? 
                            $this->security->decryptData($emailSetting['mail_password']) : 
                            MAIL_PASSWORD;
        $mailer->SMTPSecure = $emailSetting['mail_encryption'] ?? MAIL_SMTP_SECURE;
        $mailer->Port = $emailSetting['port'] ?? MAIL_SMTP_PORT;
    }

    private function checkPasswordHasExpired($passwordExpiryDate) {
        return (new DateTime() > new DateTime($passwordExpiryDate));
    }

    private function checkPasswordHistory($userAccountID, $currentPassword) {
        $total = 0;
        $passwordHistory = $this->authentication->getPasswordHistory($userAccountID);
    
        foreach ($passwordHistory as $history) {
            $password = $this->security->decryptData($history['password']);
    
            if ($password === $currentPassword) {
                $total++;
            }
        }
    
        return $total;
    }
}
