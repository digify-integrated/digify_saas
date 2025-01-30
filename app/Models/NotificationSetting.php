<?php
namespace App\Models;

use App\Core\Model;

class NotificationSetting extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getNotificationSetting($p_notification_setting_id) {
        $sql = 'CALL getNotificationSetting(:p_notification_setting_id)';
        
        return $this->fetch($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }

    public function getNotificationSettingEmailTemplate($p_notification_setting_id) {
        $sql = 'CALL getNotificationSettingEmailTemplate(:p_notification_setting_id)';
        
        return $this->fetch($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }

    public function getNotificationSettingSystemTemplate($p_notification_setting_id) {
        $sql = 'CALL getNotificationSettingSystemTemplate(:p_notification_setting_id)';
        
        return $this->fetch($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }

    public function getNotificationSettingSMSTemplate($p_notification_setting_id) {
        $sql = 'CALL getNotificationSettingSMSTemplate(:p_notification_setting_id)';
        
        return $this->fetch($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    public function checkNotificationSettingExist($p_notification_setting_id) {
        $sql = 'CALL checkNotificationSettingExist(:p_notification_setting_id)';
        
        return $this->fetch($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    public function saveNotificationSetting($p_notification_setting_id, $p_notification_setting_name, $p_notification_setting_description, $p_last_log_by) {
        $sql = 'CALL saveNotificationSetting(:p_notification_setting_id, :p_notification_setting_name, :p_notification_setting_description, :p_last_log_by, @p_new_notification_setting_id)';
        
        $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id,
            'p_notification_setting_name' => $p_notification_setting_name,
            'p_notification_setting_description' => $p_notification_setting_description,
            'p_last_log_by' => $p_last_log_by
        ]);
        
        $result = $this->fetch('SELECT @p_new_notification_setting_id AS notification_setting_id');

        return $result['notification_setting_id'] ?? null;
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Updated methods
    # -------------------------------------------------------------

    public function updateNotificationChannel($p_notification_setting_id, $p_notification_channel, $p_notification_channel_value, $p_last_log_by) {
        $sql = 'CALL updateNotificationChannel(:p_notification_setting_id, :p_notification_channel, :p_notification_channel_value, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id,
            'p_notification_channel' => $p_notification_channel,
            'p_notification_channel_value' => $p_notification_channel_value,
            'p_last_log_by' => $p_last_log_by
        ]);
    }

    public function updateSystemNotificationTemplate($p_notification_setting_id, $p_system_notification_title, $p_system_notification_message, $p_last_log_by) {
        $sql = 'CALL updateSystemNotificationTemplate(:p_notification_setting_id, :p_system_notification_title, :p_system_notification_message, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id,
            'p_system_notification_title' => $p_system_notification_title,
            'p_system_notification_message' => $p_system_notification_message,
            'p_last_log_by' => $p_last_log_by
        ]);
    }

    public function updateEmailNotificationTemplate($p_notification_setting_id, $p_email_notification_subject, $p_email_notification_body, $p_email_setting_id, $p_email_setting_name, $p_last_log_by) {
        $sql = 'CALL updateEmailNotificationTemplate(:p_notification_setting_id, :p_email_notification_subject, :p_email_notification_body, :p_email_setting_id, :p_email_setting_name, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id,
            'p_email_notification_subject' => $p_email_notification_subject,
            'p_email_notification_body' => $p_email_notification_body,
            'p_email_setting_id' => $p_email_setting_id,
            'p_email_setting_name' => $p_email_setting_name,
            'p_last_log_by' => $p_last_log_by
        ]);
    }

    public function updateSMSNotificationTemplate($p_notification_setting_id, $p_sms_notification_message, $p_last_log_by) {
        $sql = 'CALL updateSMSNotificationTemplate(:p_notification_setting_id, :p_sms_notification_message, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id,
            'p_sms_notification_message' => $p_sms_notification_message,
            'p_last_log_by' => $p_last_log_by
        ]);
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    public function deleteNotificationSetting($p_notification_setting_id) {
        $sql = 'CALL deleteNotificationSetting(:p_notification_setting_id)';
        
        return $this->query($sql, [
            'p_notification_setting_id' => $p_notification_setting_id
        ]);
    }
    
    # -------------------------------------------------------------
}
?>