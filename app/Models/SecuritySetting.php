<?php
namespace App\Models;

use App\Core\Model;

class SecuritySetting extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getSecuritySetting($p_security_setting_id) {
        $sql = 'CALL getSecuritySetting(:p_security_setting_id)';
        
        return $this->fetch($sql, [
            'p_security_setting_id' => $p_security_setting_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Updated methods
    # -------------------------------------------------------------

    public function updateSecuritySetting($p_security_setting_id, $p_value, $p_last_log_by) {
        $sql = 'CALL updateSecuritySetting(:p_security_setting_id, :p_value, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_security_setting_id' => $p_security_setting_id,
            'p_value' => $p_value,
            'p_last_log_by' => $p_last_log_by
        ]);
    }
    
    # -------------------------------------------------------------
}
?>