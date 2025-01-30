<?php
namespace App\Models;

use App\Core\Model;

class AppModule extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getAppModule($p_app_module_id) {
        $sql = 'CALL getAppModule(:p_app_module_id)';
        
        return $this->fetch($sql, [
            'p_app_module_id' => $p_app_module_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    public function checkAppModuleExist($p_app_module_id) {
        $sql = 'CALL checkAppModuleExist(:p_app_module_id)';
        
        return $this->fetch($sql, [
            'p_app_module_id' => $p_app_module_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    public function saveAppModule($p_app_module_id, $p_app_module_name, $p_app_module_description, $p_menu_item_id, $p_menu_item_name, $p_order_sequence, $p_last_log_by) {
        $sql = 'CALL saveAppModule(:p_app_module_id, :p_app_module_name, :p_app_module_description, :p_menu_item_id, :p_menu_item_name, :p_order_sequence, :p_last_log_by, @p_new_app_module_id)';
        
        $this->query($sql, [
            'p_app_module_id' => $p_app_module_id,
            'p_app_module_name' => $p_app_module_name,
            'p_app_module_description' => $p_app_module_description,
            'p_menu_item_id' => $p_menu_item_id,
            'p_menu_item_name' => $p_menu_item_name,
            'p_order_sequence' => $p_order_sequence,
            'p_last_log_by' => $p_last_log_by
        ]);
        
        $result = $this->fetch('SELECT @p_new_app_module_id AS app_module_id');

        return $result['app_module_id'] ?? null;
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    public function updateAppLogo($p_app_module_id, $p_app_logo, $p_last_log_by) {
        $sql = 'CALL updateAppLogo(:p_app_module_id, :p_app_logo, :p_last_log_by)';
        
        return $this->query($sql, [
            'p_app_module_id' => $p_app_module_id,
            'p_app_logo' => $p_app_logo,
            'p_last_log_by' => $p_last_log_by
        ]);
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    public function deleteAppModule($p_app_module_id) {
        $sql = 'CALL deleteAppModule(:p_app_module_id)';
        
        return $this->query($sql, [
            'p_app_module_id' => $p_app_module_id
        ]);
    }
    
    # -------------------------------------------------------------
}
?>