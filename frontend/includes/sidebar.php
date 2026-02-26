<?php
function isActivePage($pageName) { return basename($_SERVER['PHP_SELF']) === $pageName; }
function isActiveSection($sectionPages) { return in_array(basename($_SERVER['PHP_SELF']), $sectionPages); }
// Khawaja Traders: SUA and A only
?>
<div class="sidebar-area bg-white dark:bg-[#0c1427] fixed overflow-hidden z-[7] top-0 h-screen transition-all rounded-r-md" id="sidebar-area">
    <div class="logo bg-white dark:bg-[#0c1427] border-b border-gray-100 dark:border-[#172036] px-[25px] pt-[19px] pb-[15px] absolute z-[2] right-0 top-0 left-0">
        <a href="index.php" class="transition-none relative flex items-center">
            <img src="assets/images/logo/icon.webp" alt="logo-icon" width="25px" height="25px" class="ltr:mr-[8px] rtl:ml-[8px]">
            <span class="font-bold text-black dark:text-white relative ltr:ml-[8px] rtl:mr-[8px] top-px text-xl">K. Traders</span>
        </a>
        <button type="button" class="burger-menu inline-block absolute z-[3] top-[24px] ltr:right-[25px] rtl:left-[25px] transition-all hover:text-primary-500" id="hide-sidebar-toggle2">
            <i class="material-symbols-outlined">close</i>
        </button>
    </div>
    <div class="pt-[89px] px-[25px] pb-[20px] h-screen" data-simplebar>
        <div class="accordion">
            <span class="block relative font-medium uppercase text-gray-400 mb-[10px] text-xs">Main</span>
            <div class="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                <a href="index.php" class="accordion-button flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[28px] rtl:pr-[14px] rtl:pl-[28px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c] <?php echo isActivePage('index.php') ? 'active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : ''; ?>">
                    <i class="material-symbols-outlined transition-all <?php echo isActivePage('index.php') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> ltr:mr-[7px] rtl:ml-[7px] !text-[22px] leading-none relative -top-px">dashboard</i>
                    <span class="title leading-none">Dashboard</span>
                </a>
            </div>
            <?php $chartOfCodePages = ['item_management.php', 'misc_entries.php', 'account_management.php']; $isChartOfCodeActive = isActiveSection($chartOfCodePages); ?>
            <div class="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                <button class="accordion-button toggle <?php echo $isChartOfCodeActive ? 'open active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : ''; ?> flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[28px] rtl:pr-[14px] rtl:pl-[28px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c]" type="button">
                    <i class="material-symbols-outlined transition-all <?php echo $isChartOfCodeActive ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> ltr:mr-[7px] rtl:ml-[7px] !text-[22px] leading-none relative -top-px">code</i>
                    <span class="title leading-none">Chart of Code</span>
                </button>
                <div class="accordion-collapse <?php echo $isChartOfCodeActive ? 'show' : ''; ?>" style="<?php echo $isChartOfCodeActive ? 'display: block;' : 'display: none;'; ?>">
                    <div class="pt-[4px]">
                        <ul class="sidebar-sub-menu">
                            <li class="sidemenu-item mb-[4px] last:mb-0">
                                <a href="item_management.php" class="sidemenu-link <?php echo isActivePage('item_management.php') ? 'active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> rounded-md flex items-center relative transition-all font-medium py-[9px] ltr:pl-[38px] ltr:pr-[30px] rtl:pr-[38px] rtl:pl-[30px] hover:text-primary-500 hover:bg-primary-50 w-full text-left dark:hover:bg-[#15203c]">Item Management</a>
                            </li>
                            <li class="sidemenu-item mb-[4px] last:mb-0">
                                <a href="misc_entries.php" class="sidemenu-link <?php echo isActivePage('misc_entries.php') ? 'active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> rounded-md flex items-center relative transition-all font-medium py-[9px] ltr:pl-[38px] ltr:pr-[30px] rtl:pr-[38px] rtl:pl-[30px] hover:text-primary-500 hover:bg-primary-50 w-full text-left dark:hover:bg-[#15203c]">Misc Entries</a>
                            </li>
                            <li class="sidemenu-item mb-[4px] last:mb-0">
                                <a href="account_management.php" class="sidemenu-link <?php echo isActivePage('account_management.php') ? 'active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> rounded-md flex items-center relative transition-all font-medium py-[9px] ltr:pl-[38px] ltr:pr-[30px] rtl:pr-[38px] rtl:pl-[30px] hover:text-primary-500 hover:bg-primary-50 w-full text-left dark:hover:bg-[#15203c]">Account Management</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php $stockManagementPages = ['store_opening_stock.php']; $isStockManagementActive = isActiveSection($stockManagementPages); ?>
            <div class="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                <button class="accordion-button toggle <?php echo $isStockManagementActive ? 'open active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : ''; ?> flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[28px] rtl:pr-[14px] rtl:pl-[28px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c]" type="button">
                    <i class="material-symbols-outlined transition-all <?php echo $isStockManagementActive ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> ltr:mr-[7px] rtl:ml-[7px] !text-[22px] leading-none relative -top-px">inventory_2</i>
                    <span class="title leading-none">Stock Management</span>
                </button>
                <div class="accordion-collapse <?php echo $isStockManagementActive ? 'show' : ''; ?>" style="<?php echo $isStockManagementActive ? 'display: block;' : 'display: none;'; ?>">
                    <div class="pt-[4px]">
                        <ul class="sidebar-sub-menu">
                            <li class="sidemenu-item mb-[4px] last:mb-0">
                                <a href="store_opening_stock.php" class="sidemenu-link <?php echo isActivePage('store_opening_stock.php') ? 'active bg-primary-50 text-primary-500 dark:bg-[#15203c] dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'; ?> rounded-md flex items-center relative transition-all font-medium py-[9px] ltr:pl-[38px] ltr:pr-[30px] rtl:pr-[38px] rtl:pl-[30px] hover:text-primary-500 hover:bg-primary-50 w-full text-left dark:hover:bg-[#15203c]">Store Opening Stock</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
