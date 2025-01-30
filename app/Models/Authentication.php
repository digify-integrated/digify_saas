<?php
namespace App\Models;

use App\Core\Model;

class Authentication extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getLoginCredentials($p_user_account_id, $p_credentials) {
        $sql = 'CALL getLoginCredentials(:p_user_account_id, :p_credentials)';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_credentials' => $p_credentials
        ]);
    }

    public function getPasswordHistory($p_user_account_id) {
        $sql = 'CALL getPasswordHistory(:p_user_account_id)';
        
        return $this->fetchAll($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function getInternalNotesAttachment($p_internal_notes_id) {
        $sql = 'CALL getInternalNotesAttachment(:p_internal_notes_id)';
        
        return $this->fetchAll($sql, [
            'p_internal_notes_id' => $p_internal_notes_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    public function checkLoginCredentialsExist($p_user_account_id, $p_credentials) {
        $sql = 'CALL checkLoginCredentialsExist(:p_user_account_id, :p_credentials)';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_credentials' => $p_credentials
        ]);
    }

    public function checkSignUpEmailExist($p_email) {
        $sql = 'CALL checkSignUpEmailExist(:p_email)';
        
        return $this->fetch($sql, [
            'p_email' => $p_email
        ]);
    }

    public function checkSignUpUsernameExist($p_username) {
        $sql = 'CALL checkSignUpUsernameExist(:p_username)';
        
        return $this->fetch($sql, [
            'p_username' => $p_username
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check methods
    # -------------------------------------------------------------

    public function checkAccessRights($p_user_account_id, $p_menu_item_id, $p_access_type) {
        $sql = 'CALL checkAccessRights(:p_user_account_id, :p_menu_item_id, :p_access_type)';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_menu_item_id' => $p_menu_item_id,
            'p_access_type' => $p_access_type
        ]);
    }

    public function checkSystemActionAccessRights($p_user_account_id, $p_system_action_id) {
        $sql = 'CALL checkSystemActionAccessRights(:p_user_account_id, :p_system_action_id)';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_system_action_id' => $p_system_action_id
        ]);
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    public function insertLoginSession($p_user_account_id, $p_location, $p_login_status, $p_device, $p_ip_address) {
        $sql = 'CALL insertLoginSession(:p_user_account_id, :p_location, :p_login_status, :p_device, :p_ip_address)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_location' => $p_location,
            'p_login_status' => $p_login_status,
            'p_device' => $p_device,
            'p_ip_address' => $p_ip_address
        ]);
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    public function updateLoginAttempt($p_user_account_id, $p_failed_login_attempts, $p_last_failed_login_attempt) {
        $sql = "CALL updateLoginAttempt(:p_user_account_id, :p_failed_login_attempts, :p_last_failed_login_attempt)";
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_failed_login_attempts' => $p_failed_login_attempts,
            'p_last_failed_login_attempt' => $p_last_failed_login_attempt
        ]);
    }

    public function updateAccountLock($p_user_account_id, $p_locked, $p_lock_duration) {
        $sql = 'CALL updateAccountLock(:p_user_account_id, :p_locked, :p_lock_duration)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_locked' => $p_locked,
            'p_lock_duration' => $p_lock_duration
        ]);
    }

    public function updateOTP($p_user_account_id, $p_otp, $p_otp_expiry_date, $p_failed_otp_attempts) {
        $sql = 'CALL updateOTP(:p_user_account_id, :p_otp, :p_otp_expiry_date, :p_failed_otp_attempts)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_otp' => $p_otp,
            'p_otp_expiry_date' => $p_otp_expiry_date,
            'p_failed_otp_attempts' => $p_failed_otp_attempts
        ]);
    }

    public function updateLastConnection($p_user_account_id, $p_session_token) {
        $sql = 'CALL updateLastConnection(:p_user_account_id, :p_session_token)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_session_token' => $p_session_token
        ]);
    }

    public function updateOTPAsExpired($p_user_account_id, $p_otp_expiry_date) {
        $sql = 'CALL updateOTPAsExpired(:p_user_account_id, :p_otp_expiry_date)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_otp_expiry_date' => $p_otp_expiry_date
        ]);
    }

    public function updateFailedOTPAttempts($p_user_account_id, $p_failed_otp_attempts) {
        $sql = 'CALL updateFailedOTPAttempts(:p_user_account_id, :p_failed_otp_attempts)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_failed_otp_attempts' => $p_failed_otp_attempts
        ]);
    }

    public function updateResetToken($p_user_account_id, $p_resetToken, $p_resetToken_expiry_date) {
        $sql = 'CALL updateResetToken(:p_user_account_id, :p_resetToken, :p_resetToken_expiry_date)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_resetToken' => $p_resetToken,
            'p_resetToken_expiry_date' => $p_resetToken_expiry_date
        ]);
    }

    public function updateUserPassword($p_user_account_id, $p_password, $p_password_expiry_date, $p_locked, $p_failed_login_attempts, $p_account_lock_duration) {
        $sql = 'CALL updateUserPassword(:p_user_account_id, :p_password, :p_password_expiry_date, :p_locked, :p_failed_login_attempts, :p_account_lock_duration)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_password' => $p_password,
            'p_password_expiry_date' => $p_password_expiry_date,
            'p_locked' => $p_locked,
            'p_failed_login_attempts' => $p_failed_login_attempts,
            'p_account_lock_duration' => $p_account_lock_duration
        ]);
    }

    public function updateResetTokenAsExpired($p_user_account_id, $p_reset_token_expiry_date) {
        $sql = 'CALL updateResetTokenAsExpired(:p_user_account_id, :p_reset_token_expiry_date)';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_reset_token_expiry_date' => $p_reset_token_expiry_date
        ]);
    }
    
    # -------------------------------------------------------------
}
?>