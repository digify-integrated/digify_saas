<?php
namespace App\Models;

use App\Core\Model;

class MenuItem extends Model {

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    public function getMenuItem($p_menu_item_id) {
        $sql = 'CALL getMenuItem(:p_menu_item_id)';
        
        return $this->fetch($sql, [
            'p_menu_item_id' => $p_menu_item_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Build methods
    # -------------------------------------------------------------

    public function buildAppModuleStack($p_user_account_id) {
        $sql = 'CALL buildAppModuleStack(:p_user_account_id)';
        
        return $this->fetchAll($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function buildBreadcrumb($p_page_id) {
        $sql = 'CALL buildBreadcrumb(:p_page_id)';
        
        return $this->fetchAll($sql, [
            'p_page_id' => $p_page_id
        ]);
    }

    public function buildMenuItem($p_user_account_id, $p_app_module_id) {
        $sql = 'CALL buildMenuItem(:p_user_account_id, :p_app_module_id)';
        
        return $this->fetchAll($sql, [
            'p_user_account_id' => $p_user_account_id,
            'p_app_module_id' => $p_app_module_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    public function checkMenuItemExist($p_menu_item_id) {
        $sql = 'CALL checkMenuItemExist(:p_menu_item_id)';
        
        return $this->fetch($sql, [
            'p_menu_item_id' => $p_menu_item_id
        ]);
    }

    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    public function saveMenuItem($p_menu_item_id, $p_menu_item_name, $p_menu_item_url, $p_menu_item_icon, $p_app_module_id, $p_app_module_name, $p_parent_id, $p_parent_name, $p_table_name, $p_order_sequence, $p_last_log_by) {
        $sql = 'CALL saveMenuItem(:p_menu_item_id, :p_menu_item_name, :p_menu_item_url, :p_menu_item_icon, :p_app_module_id, :p_app_module_name, :p_parent_id, :p_parent_name, :p_table_name, :p_order_sequence, :p_last_log_by, @p_new_menu_item_id)';
        
        $this->query($sql, [
            'p_menu_item_id' => $p_menu_item_id,
            'p_menu_item_name' => $p_menu_item_name,
            'p_menu_item_url' => $p_menu_item_url,
            'p_menu_item_icon' => $p_menu_item_icon,
            'p_app_module_id' => $p_app_module_id,
            'p_app_module_name' => $p_app_module_name,
            'p_parent_id' => $p_parent_id,
            'p_parent_name' => $p_parent_name,
            'p_table_name' => $p_table_name,
            'p_order_sequence' => $p_order_sequence,
            'p_last_log_by' => $p_last_log_by
        ]);
        
        $result = $this->fetch('SELECT @p_new_menu_item_id AS menu_item_id');

        return $result['menu_item_id'] ?? null;
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    public function deleteMenuItem($p_menu_item_id) {
        $sql = 'CALL deleteMenuItem(:p_menu_item_id)';
        
        return $this->query($sql, [
            'p_menu_item_id' => $p_menu_item_id
        ]);
    }
    
    # -------------------------------------------------------------
}
?>