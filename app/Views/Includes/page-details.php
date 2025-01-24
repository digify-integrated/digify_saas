<?php
    if (!isset($_GET['page_id']) || empty($_GET['page_id']) || !isset($_GET['app_module_id']) || empty($_GET['app_module_id'])) {
        header('location: apps.php');
        exit;
    }

    $appModuleID = $security->decryptData($_GET['app_module_id']);
    $pageID = $security->decryptData($_GET['page_id']);
    
    $pageDetails = $menuItem->getMenuItem($pageID);
    $pageTitle = $pageDetails['menu_item_name'] ?? null;
    $pageURL = $pageDetails['menu_item_url'] ?? null;
    $tableName = $pageDetails['table_name'] ?? null;
    $pageLink = $pageURL . '?app_module_id=' . $security->encryptData($appModuleID) . '&page_id=' . $security->encryptData($pageID);
    
    $appModuleDetails = $appModule->getAppModule($appModuleID);
    $appModuleName = $appModuleDetails['app_module_name'];
    $appLogo = $systemHelper->checkImage($appModuleDetails['app_logo'], 'app module logo');

    $readAccess = $authentication->checkAccessRights($userID, $pageID, 'read');
    $writeAccess = $authentication->checkAccessRights($userID, $pageID, 'write');
    $deleteAccess = $authentication->checkAccessRights($userID, $pageID, 'delete');
    $createAccess = $authentication->checkAccessRights($userID, $pageID, 'create');
    $importAccess = $authentication->checkAccessRights($userID, $pageID, 'import');
    $exportAccess = $authentication->checkAccessRights($userID, $pageID, 'export');
    $logNotesAccess = $authentication->checkAccessRights($userID, $pageID, 'log notes');

    if ($readAccess['total'] == 0) {
        header('location: 404.php');
        exit;
    }

    $newRecord = null;
    $importRecord = null;
    $detailID = null;
    $importTableName = null;

    if(isset($_GET['id'])){
        if(empty($_GET['id'])){
            header('location: apps.php');
            exit;
        }
    
        $detailID = $security->decryptData($_GET['id']);
    }

    if(isset($_GET['new'])){
        if($createAccess['total'] == 0){
            header('location: ' . $pageLink);
            exit;
        }

        $newRecord = isset($_GET['new']);
    }

    if(isset($_GET['import']) && empty($_GET['import'])){
        header('location:' . $pageLink);
        exit;
    }
    else if(isset($_GET['import']) && !empty($_GET['import'])){
        if($importAccess['total'] == 0){
            header('location: ' . $pageLink);
            exit;
        }

        $importRecord = isset($_GET['import']);
        $importTableName = $security->decryptData($_GET['import']);
    }
   
    $disabled = ($writeAccess['total'] == 0) ? 'disabled' : '';
?>