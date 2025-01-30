<?php
namespace App\Models;

use App\Core\Model;

class EmailSetting extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getEmailSetting($p_email_setting_id) {
        $sql = 'CALL getEmailSetting(:p_email_setting_id)';
        
        return $this->fetch($sql, [
            'p_email_setting_id' => $p_email_setting_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    public function checkEmailSettingExist($p_email_setting_id) {
        $sql = 'CALL checkEmailSettingExist(:p_email_setting_id)';
        
        return $this->fetch($sql, [
            'p_email_setting_id' => $p_email_setting_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    public function saveEmailSetting($p_email_setting_id, $p_email_setting_name, $p_email_setting_description, $p_mail_host, $p_port, $p_smtp_auth, $p_smtp_auto_tls, $p_mail_username, $p_mail_password, $p_mail_encryption, $p_mail_from_name, $p_mail_from_email, $p_last_log_by) {
        $sql = 'CALL saveEmailSetting(:p_email_setting_id, :p_email_setting_name, :p_email_setting_description, :p_mail_host, :p_port, :p_smtp_auth, :p_smtp_auto_tls, :p_mail_username, :p_mail_password, :p_mail_encryption, :p_mail_from_name, :p_mail_from_email, :p_last_log_by, @p_new_email_setting_id)';
        
        $this->query($sql, [
            'p_email_setting_id' => $p_email_setting_id,
            'p_email_setting_name' => $p_email_setting_name,
            'p_email_setting_description' => $p_email_setting_description,
            'p_mail_host' => $p_mail_host,
            'p_port' => $p_port,
            'p_smtp_auth' => $p_smtp_auth,
            'p_smtp_auto_tls' => $p_smtp_auto_tls,
            'p_mail_username' => $p_mail_username,
            'p_mail_password' => $p_mail_password,
            'p_mail_encryption' => $p_mail_encryption,
            'p_mail_from_name' => $p_mail_from_name,
            'p_mail_from_email' => $p_mail_from_email,
            'p_last_log_by' => $p_last_log_by
        ]);
        
        $result = $this->fetch('SELECT @p_new_email_setting_id AS email_setting_id');

        return $result['email_setting_id'] ?? null;
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    public function deleteEmailSetting($p_email_setting_id) {
        $sql = 'CALL deleteEmailSetting(:p_email_setting_id)';
        
        return $this->query($sql, [
            'p_email_setting_id' => $p_email_setting_id
        ]);
    }
    
    # -------------------------------------------------------------
}
?>