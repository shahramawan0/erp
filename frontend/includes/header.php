<div class="header-area bg-white dark:bg-[#0c1427] py-[13px] px-[20px] md:px-[25px] fixed top-0 z-[6] rounded-b-md transition-all" id="header-area">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex items-center justify-center md:justify-normal">
            <div class="relative leading-none top-px ltr:mr-[13px] ltr:md:mr-[18px] ltr:lg:mr-[23px] rtl:ml-[13px] rtl:md:ml-[18px] rtl:lg:ml-[23px]">
                <button type="button" class="hide-sidebar-toggle transition-all inline-block hover:text-primary-500" id="hide-sidebar-toggle">
                    <i class="material-symbols-outlined !text-[20px]">
                        menu
                    </i>
                </button>
            </div>
            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PT'): ?>
            <div class="relative w-[250px] lg:w-[260px]">
                <input type="text" 
                       placeholder="Search items by name or ID..." 
                       class="bg-gray-50 border border-gray-50 h-[44px] rounded-md w-full block text-black pt-[11px] pb-[12px] px-[13px] md:px-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400" 
                       id="header-item-search-input" 
                       onkeyup="handleHeaderItemSearchKeyUp(event)"
                       autocomplete="off">
                <button type="button" 
                        class="absolute text-primary-500 mt-[2px] ltr:right-[13px] ltr:md:right-[15px] rtl:left-[13px] rtl:md:left-[15px] top-1/2 -translate-y-1/2" 
                        id="header-search-btn">
                    <i class="material-symbols-outlined !text-[20px]">
                        search
                    </i>
                </button>
                <button type="button" 
                        class="absolute text-gray-400 mt-[2px] ltr:right-[13px] ltr:md:right-[15px] rtl:left-[13px] rtl:md:left-[15px] top-1/2 -translate-y-1/2 hidden" 
                        id="clear-header-search-btn" 
                        onclick="clearHeaderItemSearch()">
                    <i class="material-symbols-outlined !text-[20px]">
                        close
                    </i>
                </button>
                
                <!-- Search Suggestions Dropdown -->
                <div class="absolute top-full left-0 right-0 bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] rounded-md shadow-lg z-[50] mt-1 max-h-[300px] overflow-y-auto hidden" 
                     id="header-item-search-suggestions">
                    <!-- Suggestions will be populated here -->
                </div>
            </div>
            <?php endif; ?>
            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
            <div class="connected-apps-menu relative ltr:ml-[13px] ltr:md:ml-[18px] ltr:lg:ml-[25px] rtl:ml-[r3px] rtl:md:mr-[18px] rtl:lg:mr-[25px]">
                <button type="button" class="transition-all relative top-[2px] hover:text-primary-500" id="dropdownToggleBtn">
                    <i class="material-symbols-outlined !text-[22px] md:!text-[24px]">
                        apps
                    </i>
                </button>
                <div class="connected-apps-dropdown bg-white dark:bg-[#0c1427] transition-all shadow-3xl dark:shadow-none pt-[20px] md:pt-[25px] px-[10px] md:px-[15px] pb-[5px] md:pb-[8px] absolute mt-[9px] md:mt-[20px] w-[240px] z-[1] top-full right-0 rounded-md">
                    <ul class="grid grid-cols-3 text-center gap-[5px]">
                        <li>
                            <a href="https://www.figma.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/figma.svg" class="inline-block" alt="figma">
                                <span class="block mt-[7px]">
                                    Figma
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://dribbble.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/dribbble.svg" class="inline-block" alt="dribbble">
                                <span class="block mt-[7px]">
                                    Dribbble
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://open.spotify.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/spotify.svg" class="inline-block" alt="spotify">
                                <span class="block mt-[7px]">
                                    Spotify
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://gitlab.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/gitlab.svg" class="inline-block" alt="gitlab">
                                <span class="block mt-[7px]">
                                    Gitlab
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://drive.google.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/google-drive.svg" class="inline-block" alt="google-drive">
                                <span class="block mt-[7px]">
                                    GDrive
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://trello.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/trello.svg" class="inline-block" alt="trello">
                                <span class="block mt-[7px]">
                                    Trello
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://slack.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/slack.svg" class="inline-block" alt="slack">
                                <span class="block mt-[7px]">
                                    Slack
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.pinterest.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/pinterest.svg" class="inline-block" alt="pinterest">
                                <span class="block mt-[7px]">
                                    Pinterest
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/facebook.svg" class="inline-block" alt="facebook">
                                <span class="block mt-[7px]">
                                    Facebook
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/" class="block text-xs mb-[15px] text-black transition-all hover:text-primary-500 dark:text-white" target="_blank">
                                <img src="assets/images/icons/linkedin.svg" class="inline-block" alt="linkedIn">
                                <span class="block mt-[7px]">
                                    LinkedIn
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <ul class="flex items-center justify-center md:justify-normal mt-[13px] md:mt-0">
            <li class="relative mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="light-dark-toggle leading-none inline-block transition-all relative top-[2px] text-[#fe7a36]" id="light-dark-toggle">
                    <i class="material-symbols-outlined !text-[20px] md:!text-[22px]">
                        light_mode
                    </i>
                </button>
            </li>
            <?php if ($defaultRoleStatus == 'SUA'): ?>
            <li class="relative language-menu mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="leading-none pr-[12px] inline-block transition-all relative top-[2px] hover:text-primary-500" id="dropdownToggleBtn">
                    <i class="material-symbols-outlined !text-[20px] md:!text-[22px]">
                        business
                    </i>
                    <i class="ri-arrow-down-s-line text-[15px] absolute -right-[3px] top-1/2 -translate-y-1/2 -mt-[2px]"></i>
                </button>
                <div class="language-menu-dropdown bg-white dark:bg-[#0c1427] transition-all shadow-3xl dark:shadow-none pt-[13px] md:pt-[14px] absolute mt-[18px] md:mt-[21px] w-[200px] md:w-[240px] z-[1] top-full ltr:left-0 ltr:md:left-auto ltr:lg:right-0 rtl:right-0 rtl:md:right-auto rtl:lg:left-0 rounded-md">
                    <span class="block text-black dark:text-white font-semibold px-[20px] pb-[14px] text-sm md:text-[15px]">
                        Choose Unit
                    </span>
                    <ul id="unitsList">
                        <li class="border-t border-dashed border-gray-100 dark:border-[#172036]">
                            <button type="button" class="text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium">
                                <div class="flex items-center">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Loading units...</span>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>
            <li class="relative mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="leading-none inline-block transition-all relative top-[2px] hover:text-primary-500" id="fullscreenBtn">
                    <i class="material-symbols-outlined !text-[22px] md:!text-[24px]" id="fullscreenIcon">
                        fullscreen
                    </i>
                </button>
            </li>
            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
            <li class="relative notifications-menu mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="leading-none inline-block transition-all relative top-[2px] hover:text-primary-500" id="dropdownToggleBtn">
                    <i class="material-symbols-outlined !text-[22px] md:!text-[24px]">
                        notifications
                    </i>
                    <span class="top-[3px] ltr:right-[4px] rtl:left-[4px] w-[6px] h-[6px] rounded-full absolute bg-orange-500"></span>
                </button>
                <div class="notifications-menu-dropdown bg-white dark:bg-[#0c1427] transition-all shadow-3xl dark:shadow-none py-[17px] absolute mt-[17px] md:mt-[20px] w-[290px] md:w-[350px] z-[1] top-full ltr:-right-[120px] ltr:md:right-0 rtl:-left-[120px] rtl:md:left-0 rounded-md">
                    <div class="flex items-center justify-between px-[20px] pb-[17px]">
                        <span class="font-semibold text-black dark:text-white text-[15px]">
                            Notifications
                            <span class="text-gray-500 dark:text-gray-400 font-normal text-base">
                                (03)
                            </span>
                        </span>
                        <a href="javascript:void(0);" class="text-primary-500">
                            Clear All
                        </a>
                    </div>
                    <ul class="mb-[18px]">
                        <li class="relative border-b border-gray-100 dark:border-[#172036] border-dashed py-[17px] ltr:pl-[75px] ltr:pr-[20px] rtl:pr-[75px] rtl:pl-[20px] first:border-t first:border-gray-100 dark:first:border-[#172036]">
                            <div class="rounded-full flex items-center justify-center absolute text-center transition-all top-1/2 -translate-y-1/2 ltr:left-[20px] rtl:right-[20px] w-[44px] h-[44px] text-primary-500 bg-primary-50">
                                <i class="material-symbols-outlined !text-[22px]">
                                    sms
                                </i>
                            </div>
                            <span class="block mb-[3px] text-black dark:text-white">
                                You have requested to <strong class="font-semibold">withdrawal</strong>
                            </span>
                            <span class="block">
                                2 hrs ago
                            </span>
                            <a href="notifications.html" class="block left-0 top-0 right-0 bottom-0 z-[1] absolute"></a>
                        </li>
                        <li class="relative border-b border-gray-100 dark:border-[#172036] border-dashed py-[17px] ltr:pl-[75px] ltr:pr-[20px] rtl:pr-[75px] rtl:pl-[20px] first:border-t first:border-gray-100 dark:first:border-[#172036]">
                            <div class="rounded-full flex items-center justify-center absolute text-center transition-all top-1/2 -translate-y-1/2 ltr:left-[20px] rtl:right-[20px] w-[44px] h-[44px] text-[#39b2de] bg-primary-50">
                                <i class="material-symbols-outlined !text-[22px]">
                                    person
                                </i>
                            </div>
                            <span class="block mb-[3px] text-black dark:text-white">
                                <strong class="font-semibold">A new user</strong> added in Trezo
                            </span>
                            <span class="block">
                                3 hrs ago
                            </span>
                            <a href="notifications.html" class="block left-0 top-0 right-0 bottom-0 z-[1] absolute"></a>
                            <span class="inline-block rounded-full bg-primary-500 absolute w-[6px] h-[6px] right-[20px] top-1/2 -translate-y-1/2"></span>
                        </li>
                        <li class="relative border-b border-gray-100 dark:border-[#172036] border-dashed py-[17px] ltr:pl-[75px] ltr:pr-[20px] rtl:pr-[75px] rtl:pl-[20px] first:border-t first:border-gray-100 dark:first:border-[#172036]">
                            <div class="rounded-full flex items-center justify-center absolute text-center transition-all top-1/2 -translate-y-1/2 ltr:left-[20px] rtl:right-[20px] w-[44px] h-[44px] text-[#00b69b] bg-primary-50">
                                <i class="material-symbols-outlined !text-[22px]">
                                    mark_email_unread
                                </i>
                            </div>
                            <span class="block mb-[3px] text-black dark:text-white">
                                You have requested to <strong class="font-semibold">withdrawal</strong>
                            </span>
                            <span class="block">
                                1 day ago
                            </span>
                            <a href="notifications.html" class="block left-0 top-0 right-0 bottom-0 z-[1] absolute"></a>
                        </li>
                    </ul>
                    <div class="text-center">
                        <a href="notifications.html" class="inline-block font-medium relative text-primary-500 transition-all hover:underline">
                            See All Notifications
                        </a>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            <li class="relative profile-menu mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="flex items-center -mx-[5px] relative ltr:pr-[14px] rtl:pl-[14px] text-black dark:text-white" id="dropdownToggleBtn">
                    <?php 
                    $profileImage = $currentUser['profile_picture'] ?? null;
                    if ($profileImage && file_exists('../' . $profileImage)) {
                        $imageSrc = '../' . $profileImage;
                    } else {
                        $imageSrc = 'assets/images/users/3001764.png'; // Default fallback image
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" class="w-[35px] h-[35px] md:w-[42px] md:h-[42px] rounded-full ltr:md:mr-[2px] ltr:lg:mr-[8px] rtl:md:ml-[2px] rtl:lg:ml-[8px] border-[2px] border-primary-200 inline-block" alt="admin-image" onerror="this.src='assets/images/users/3001764.png'">
                    <span class="block font-semibold text-[0] lg:text-base">
                        <?php echo $currentUser['first_name'] ?? 'Admin'; ?>
                    </span>
                    <i class="ri-arrow-down-s-line text-[15px] absolute ltr:-right-[3px] rtl:-left-[3px] top-1/2 -translate-y-1/2 mt-px"></i>
                </button>
                <div class="profile-menu-dropdown bg-white dark:bg-[#0c1427] transition-all shadow-3xl dark:shadow-none py-[22px] absolute mt-[13px] md:mt-[14px] w-[195px] z-[1] top-full ltr:right-0 rtl:left-0 rounded-md">
                    <div class="flex items-center border-b border-gray-100 dark:border-[#172036] pb-[12px] mx-[20px] mb-[10px]">
                        <img src="<?php echo htmlspecialchars($imageSrc); ?>" class="rounded-full w-[31px] h-[31px] ltr:mr-[9px] rtl:ml-[9px] border-2 border-primary-200 inline-block" alt="admin-image" onerror="this.src='assets/images/users/3001764.png'">
                        <div>
                            <span class="block text-black dark:text-white font-medium">
                                <?php echo $currentUser['role_name'] ?? null; ?>
                            </span>
                            <span class="block text-xs">
                                <?php echo $currentUser['unit_name'] ?? null; ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                    <ul>
                        
                        <li>
                            <a href="chat.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    chat
                                </i>
                                Messages
                            </a>
                        </li>
                        <li>
                            <a href="to-do-list.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    format_list_bulleted
                                </i>
                                My Task
                            </a>
                        </li>
                        <li>
                            <a href="checkout.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    credit_card
                                </i>
                                Billing
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                    <div class="border-t border-gray-100 dark:border-[#172036] mx-[20px] my-[9px]"></div>
                    <ul>
                        <li>
                            <a href="profile.php" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    account_circle
                                </i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="settings.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    settings
                                </i>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a href="faq.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    support
                                </i>
                                Support
                            </a>
                        </li>
                        <li>
                            <a href="lock-screen.html" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    lock
                                </i>
                                Lock Screen
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')" class="block relative py-[7px] ltr:pl-[50px] ltr:pr-[20px] rtl:pr-[50px] rtl:pl-[20px] text-black dark:text-white transition-all hover:text-primary-500">
                                <i class="material-symbols-outlined top-1/2 -translate-y-1/2 !text-[22px] absolute ltr:left-[20px] rtl:right-[20px]">
                                    logout
                                </i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
            <li class="relative settings-menu mx-[8px] md:mx-[10px] lg:mx-[12px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <button type="button" class="leading-none inline-block transition-all relative top-[2px] hover:text-primary-500" id="dropdownToggleBtn">
                    <i class="material-symbols-outlined !text-[22px] md:!text-[24px]">
                        settings
                    </i>
                </button>
                <div class="settings-menu-dropdown bg-white dark:bg-[#0c1427] transition-all shadow-3xl dark:shadow-none p-[20px] absolute mt-[17px] md:mt-[20px] w-[195px] z-[1] top-full ltr:right-0 rtl:left-0 rounded-md">
                    <button type="button" class="rtl-mode-toggle flex items-center text-black dark:text-white font-medium" id="rtl-mode-toggle">
                        RTL Mode:
                        <span class="inline-block relative rounded-full w-[35px] h-[20px] bg-gray-50 dark:bg-[#0a0e19] ltr:ml-[10px] rtl:mr-[10px]">
                            <span class="inline-block transition-all absolute h-[12px] w-[12px] bg-black dark:bg-white rounded-full top-1/2 -translate-y-1/2"></span>
                        </span>
                    </button>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
// Unit dropdown functionality for SUA users
(function() {
    // Find the language-menu dropdown button (using dropdownToggleBtn)
    const languageMenuItems = document.querySelectorAll('.language-menu');
    if (languageMenuItems.length === 0) return;
    
    const languageMenuItem = languageMenuItems[languageMenuItems.length - 1]; // Get the last one (units dropdown)
    const unitDropdownBtn = languageMenuItem.querySelector('#dropdownToggleBtn');
    const unitDropdownMenu = languageMenuItem.querySelector('.language-menu-dropdown');
    const unitsList = document.getElementById('unitsList');
    let unitsLoaded = false;
    let currentUnitId = <?php echo json_encode($currentUser['unit_id'] ?? null); ?>;

    if (!unitDropdownBtn || !unitDropdownMenu || !unitsList) return;

    // Toggle dropdown - override default behavior
    unitDropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        
        // Close other dropdowns
        document.querySelectorAll('.language-menu-dropdown, .notifications-menu-dropdown, .profile-menu-dropdown, .settings-menu-dropdown, .connected-apps-dropdown').forEach(dropdown => {
            if (dropdown !== unitDropdownMenu) {
                dropdown.style.display = 'none';
            }
        });

        // Toggle current dropdown
        if (unitDropdownMenu.style.display === 'none' || !unitDropdownMenu.style.display) {
            unitDropdownMenu.style.display = 'block';
            if (!unitsLoaded) {
                loadUnits();
            }
        } else {
            unitDropdownMenu.style.display = 'none';
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!unitDropdownMenu.contains(e.target) && !unitDropdownBtn.contains(e.target)) {
            unitDropdownMenu.style.display = 'none';
        }
    });

    // Load units from API
    function loadUnits() {
        unitsList.innerHTML = '<li class="border-t border-dashed border-gray-100 dark:border-[#172036]"><button type="button" class="text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium"><div class="flex items-center"><span class="text-gray-500 dark:text-gray-400 text-sm">Loading units...</span></div></button></li>';

        fetch('../api/units?all=true')
            .then(res => res.json())
            .then(result => {
                if (result.success && result.data) {
                    const units = Array.isArray(result.data) ? result.data : (result.data.records || []);
                    renderUnits(units);
                    unitsLoaded = true;
                } else {
                    unitsList.innerHTML = '<li class="border-t border-dashed border-gray-100 dark:border-[#172036]"><button type="button" class="text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium"><div class="flex items-center"><span class="text-gray-500 dark:text-gray-400 text-sm">Failed to load units</span></div></button></li>';
                }
            })
            .catch(error => {
                console.error('Error loading units:', error);
                unitsList.innerHTML = '<li class="border-t border-dashed border-gray-100 dark:border-[#172036]"><button type="button" class="text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium"><div class="flex items-center"><span class="text-gray-500 dark:text-gray-400 text-sm">Error loading units</span></div></button></li>';
            });
    }

    // Render units list - using original language dropdown structure
    function renderUnits(units) {
        if (!units || units.length === 0) {
            unitsList.innerHTML = '<li class="border-t border-dashed border-gray-100 dark:border-[#172036]"><button type="button" class="text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium"><div class="flex items-center"><span class="text-gray-500 dark:text-gray-400 text-sm">No units available</span></div></button></li>';
            return;
        }

        unitsList.innerHTML = '';
        units.forEach((unit, index) => {
            const li = document.createElement('li');
            li.className = 'border-t border-dashed border-gray-100 dark:border-[#172036]';
            
            const isActive = unit.id == currentUnitId;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'text-black dark:text-white px-[20px] py-[12px] d-block w-full font-medium';
            button.innerHTML = `
                <div class="flex items-center">
                    <i class="material-symbols-outlined !text-[20px] ltr:mr-[10px] rtl:ml-[10px]">business</i>
                    ${escapeHtml(unit.name)}
                </div>
            `;
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                changeUnit(unit.id, unit.name, button);
            });
            
            li.appendChild(button);
            unitsList.appendChild(li);
        });
    }

    // Change unit
    function changeUnit(unitId, unitName, buttonElement) {
        if (unitId == currentUnitId) {
            unitDropdownMenu.style.display = 'none';
            return;
        }

        // Show loading
        const originalText = buttonElement.innerHTML;
        buttonElement.innerHTML = '<div class="flex items-center"><span class="text-gray-500 dark:text-gray-400 text-sm">Changing...</span></div>';
        buttonElement.disabled = true;

        fetch('../change-unit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ unit_id: unitId })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Reload page to reflect new unit in session
                window.location.reload();
            } else {
                buttonElement.innerHTML = originalText;
                buttonElement.disabled = false;
                alert(result.error || 'Failed to change unit');
            }
        })
        .catch(error => {
            console.error('Error changing unit:', error);
            buttonElement.innerHTML = originalText;
            buttonElement.disabled = false;
            alert('Error changing unit. Please try again.');
        });
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>