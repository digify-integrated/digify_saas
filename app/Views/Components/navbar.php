<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
    <div class="menu menu-rounded menu-active-bg menu-state-primary menu-column menu-lg-row menu-title-gray-700 menu-icon-gray-500 menu-arrow-gray-500 menu-bullet-gray-500 my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
        <?php

            if (!empty($_GET['page_id']) && !empty($_GET['app_module_id'])) {
                $menu = '';
            
                $navbar = $menuItem->buildMenuItem($userID, $appModuleID);
            
                $menuItems = processMenuItems($navbar, $authentication, $userID);
            
                $rootMenuItems = array_filter($menuItems, function ($item) {
                    return empty($item['PARENT_ID']);
                });
            
                foreach ($rootMenuItems as $rootMenuItem) {
                    $menu .= buildMenuItemHTML($rootMenuItem, $security);
                }
            
                echo $menu;
            }
            
            function processMenuItems(array $navbar, $authentication, $userID) {
                $menuItems = [];
            
                foreach ($navbar as $row) {
                    $menuItems[$row['menu_item_id']] = [
                        'MENU_ITEM_ID' => $row['menu_item_id'],
                        'MENU_ITEM_NAME' => $row['menu_item_name'],
                        'MENU_ITEM_URL' => $row['menu_item_url'] ?? null,
                        'PARENT_ID' => $row['parent_id'],
                        'MENU_ITEM_ICON' => $row['menu_item_icon'] ?? null,
                        'APP_MODULE_ID' => $row['app_module_id'],
                        'CHILDREN' => []
                    ];
                }
                
                foreach ($menuItems as $menuItem) {
                    if (!empty($menuItem['PARENT_ID'])) {
                        if ($authentication->checkAccessRights($userID, $menuItem['PARENT_ID'], 'read')['total'] > 0) {
                            $menuItems[$menuItem['PARENT_ID']]['CHILDREN'][] = &$menuItems[$menuItem['MENU_ITEM_ID']];
                        }
                    }
                }
            
                return $menuItems;
            }

            function buildMenuItemHTML(array $menuItemDetails, $security, int $level = 1) {
                $menuItemID = $security->encryptData($menuItemDetails['MENU_ITEM_ID'] ?? null);
                $appModuleID = $security->encryptData($menuItemDetails['APP_MODULE_ID'] ?? null);
                $menuItemName = htmlspecialchars($menuItemDetails['MENU_ITEM_NAME'] ?? '');
                $menuItemIcon = htmlspecialchars($menuItemDetails['MENU_ITEM_ICON'] ?? '');
                $menuItemURL = $menuItemDetails['MENU_ITEM_URL'] ?? null;
                $children = $menuItemDetails['CHILDREN'] ?? null;
            
                $menuItemURL = !empty($menuItemURL) 
                    ? (strpos($menuItemURL, '?page_id=') !== false 
                        ? $menuItemURL 
                        : $menuItemURL . '?app_module_id=' . $appModuleID . '&page_id=' . $menuItemID) 
                    : 'javascript:void(0)';
            
                $html = '';
            
                if ($level === 1) {
                    $html .= buildTopLevelMenuItemHTML($menuItemName, $menuItemURL, $children, $security, $level);
                }
                else {
                    $icon = ($level === 2) 
                        ? '<span class="menu-icon"><i class="'. $menuItemIcon .' fs-2"></i></span>' 
                        : '<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>';
            
                    $html .= buildSubMenuItemHTML($menuItemName, $menuItemURL, $icon, $children, $security, $level);
                }
            
                return $html;
            }
            
            function buildTopLevelMenuItemHTML(string $menuItemName, string $menuItemURL, ?array $children, $security, int $level) {
                if (empty($children)) {
                    return '<div data-kt-menu-trigger="{default: \'click\', lg: \'hover\'}" data-kt-menu-placement="bottom-start" class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                                <a class="menu-link" href="'. $menuItemURL .'">
                                    <span class="menu-title">'. $menuItemName .'</span>
                                </a>
                            </div>';
                }
            
                $html = '<div data-kt-menu-trigger="{default: \'click\', lg: \'hover\'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                            <span class="menu-link">
                                <span class="menu-title">'. $menuItemName .'</span>
                                <span class="menu-arrow d-lg-none"></span>
                            </span>
                            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">';
            
                foreach ($children as $child) {
                    $html .= buildMenuItemHTML($child, $security, $level + 1);
                }
            
                $html .= '</div></div>';
            
                return $html;
            }
            
            function buildSubMenuItemHTML(string $menuItemName, string $menuItemURL, string $icon, ?array $children, $security, int $level){
                if (empty($children)) {
                    return '<div data-kt-menu-trigger="{default: \'click\', lg: \'hover\'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion">
                                <a class="menu-link" href="'. $menuItemURL .'">
                                    '. $icon .'
                                    <span class="menu-title">'. $menuItemName .'</span>
                                </a>
                            </div>';
                }
            
                $html = '<div data-kt-menu-trigger="{default: \'click\', lg: \'hover\'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
                            <span class="menu-link">
                                '. $icon .'
                                <span class="menu-title">'. $menuItemName .'</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">';
            
                foreach ($children as $child) {
                    $html .= buildMenuItemHTML($child, $security, $level + 1);
                }
            
                $html .= '</div></div>';
            
                return $html;
            }
            
        ?>
    </div>
</div>