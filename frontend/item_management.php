<!DOCTYPE html>
<html dir="ltr">

<?php include 'includes/head.php'; ?>

<body>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    <!-- End Header -->

    <style>
    /* Edit ERP Item Modal Width */
    #editErpItemModal .popup-dialog {
        max-width: 650px !important;
        width: 100%;
        padding: 0 15px;
    }
    
    /* Export to Excel Button Styles */
    #exportErpItemsToExcelBtn {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid #3b82f6;
        background: #3b82f6 !important;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    }

    #exportErpItemsToExcelBtn:hover {
        background: #2563eb !important;
        border-color: #2563eb;
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        transform: translateY(-1px);
    }

    #exportErpItemsToExcelBtn:active {
        transform: translateY(0);
    }

    #exportErpItemsToExcelBtn i {
        font-size: 18px;
        line-height: 1;
    }
    </style>

    <!-- Main Content -->
    <div class="main-content transition-all flex flex-col overflow-hidden min-h-screen" id="main-content">

        <!-- Breadcrumb -->
        <div class="mb-[25px] md:flex items-center justify-between">
            <h5 class="!mb-0">
                Item Management
            </h5>
            <ol class="breadcrumb mt-[12px] md:mt-0">
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    <a href="index.html" class="inline-block relative ltr:pl-[22px] rtl:pr-[22px] transition-all hover:text-primary-500">
                        <i class="material-symbols-outlined absolute ltr:left-0 rtl:right-0 !text-lg -mt-px text-primary-500 top-1/2 -translate-y-1/2">
                            home
                        </i>
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    Chart of Code
                </li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    Item Management
                </li>
            </ol>
        </div>

        <!-- Tabs -->
        <div class="w-full mb-[25px]" id="clickToSeeCode">
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md w-full">
                <div class="trezo-card-content">
                    <div class="trezo-tabs" id="trezo-tabs">
                        <?php 
                            // Access control logic:
                            // - Full access (all tabs + edit): 'SUA', 'A', 'PA', 'PM', 'PRT', 'PRA' (any user) OR 'SA' with user ID 89
                            // - Limited access for SA (Items tab + rack only, NO edit): 'SA' with other user IDs
                            // - Limited access for others (Items tab + rack only): other roles
                            $erpFullAccessRoles = ['SUA', 'A', 'PA', 'PM', 'PRT', 'PRA']; // These roles always have full access
                            $erpFullAccessUserId = 89;
                            $currentUserId = $currentUser['user_id'] ?? 0;
                            
                            // Full access: SUA, A, PA, PM (any user) OR SA with user ID 89
                            $hasErpFullAccess = in_array($defaultRoleStatus, $erpFullAccessRoles) || 
                                               ($defaultRoleStatus == 'SA' && $currentUserId == $erpFullAccessUserId);
                            
                            // SA with limited access (NO edit, only rack assignment)
                            $isSaLimitedAccess = ($defaultRoleStatus == 'SA' && $currentUserId != $erpFullAccessUserId);
                            
                            // Limited access: can only see Items (ERP Structure) tab
                            $hasErpLimitedAccess = !$hasErpFullAccess;
                            
                            // Can edit items: only full access users (SA with other IDs cannot edit)
                            $canEditErpItems = $hasErpFullAccess;
                            // PRT/PRA: do not show Categories tab (they see Groups, Sub-Groups, Attributes, Items only)
                            $showCategoriesTab = $hasErpFullAccess && $defaultRoleStatus !== 'PRT' && $defaultRoleStatus !== 'PRA';
                        ?>
                        <ul class="navs mb-[20px] border-b border-gray-100 dark:border-[#172036]">
                            <?php if ($showCategoriesTab): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab4" class="nav-link active block pb-[20px] transition-all relative font-medium">Categories</button>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasErpFullAccess): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab5" class="nav-link <?php echo $showCategoriesTab ? '' : 'active'; ?> block pb-[20px] transition-all relative font-medium">Groups</button>
                                </li>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab6" class="nav-link block pb-[20px] transition-all relative font-medium">Sub-Groups</button>
                                </li>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab7" class="nav-link block pb-[20px] transition-all relative font-medium">Attributes</button>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                <button type="button" data-tab="tab8" class="nav-link <?php echo $hasErpLimitedAccess ? 'active' : ''; ?> block pb-[20px] transition-all relative font-medium">Items (ERP Structure)</button>
                            </li>
                        </ul>
                  
                        <div class="tab-content">
                            <div class="tab-pane" id="tab1">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <!-- Main Head Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                        <form id="mainHeadForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="mainHeadName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="mainHeadName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name</label>

                                                </div>

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="mainHeadNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="mainHeadNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                                </div>

                                                <input type="hidden" id="mainHeadType" name="type" value="item">

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="mainHeadDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="mainHeadDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>

                                                </div>
                                                <div class="mb-[15px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitMainHeadBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitMainHeadText">Create</span>
                                                        <span id="submitMainHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Main Head Table -->
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchMainHeadInput" placeholder="Search here....."
                                                        class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                                <!-- <div class="flex gap-2">
                                                <button onclick="exportTableData('unit', 'csv')" class="inline-block py-[6px] px-[12px] bg-success-500 text-white transition-all hover:bg-success-400 rounded-md border border-success-500 hover:border-success-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    CSV
                                                </button>
                                                <button onclick="exportTableData('unit', 'json')" class="inline-block py-[6px] px-[12px] bg-info-500 text-white transition-all hover:bg-info-400 rounded-md border border-info-500 hover:border-info-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    JSON
                                                </button>
                                            </div> -->
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">
                                                                Code
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Name
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Name in Urdu
                                                            </th>
                                                            <!-- <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                            Type
                                                        </th> -->
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Description
                                                            </th>
                                                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                    Actions
                                                                </th>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="mainHeadsTableBody">
                                                        <!-- Data will be loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="paginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0" id="paginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8">
                                        <div class="text-red-500 text-lg font-medium mb-2">Access Denied</div>
                                        <div class="text-gray-600 dark:text-gray-400">You don't have permission to view this content.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane" id="tab2">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <!-- Control Head Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                        <form id="controlHeadForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <select id="controlHeadMainHead" name="main_head_id" required data-float-select
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                        <option value="" disabled selected hidden></option>
                                                    </select>
                                                    <label for="controlHeadMainHead" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Main Head</label>

                                                </div>

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="controlHeadName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="controlHeadName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name</label>

                                                </div>

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="controlHeadNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="controlHeadNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                                </div>

                                                <input type="hidden" id="controlHeadType" name="type" value="item">

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">

                                                    <input type="text" id="controlHeadDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="controlHeadDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitControlHeadBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Control Head Table -->
                                    <div class="trezo-card bg-white dark:bg-[#0c1427]  rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">
                                                            search
                                                        </i>
                                                    </label>
                                                    <input type="text" id="searchControlHeadInput" placeholder="Search here....." class="bg-gray-50 border border-gray-50 h-[36px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                                <!-- <div class="trezo-card-dropdown relative">
                                                <button type="button" class="trezo-card-dropdown-btn inline-block rounded-md border border-gray-100 py-[5px] md:py-[6.5px] px-[12px] md:px-[19px] transition-all hover:bg-gray-50 dark:border-[#172036] dark:hover:bg-[#0a0e19]" id="filterControlHeadDropdownBtn">
                                                    <span class="inline-block relative ltr:pr-[17px] ltr:md:pr-[20px] rtl:pl-[17px] rtl:ml:pr-[20px]">
                                                        Show All
                                                    </span>
                                                </button>
                                                <ul class="trezo-card-dropdown-menu transition-all bg-white shadow-3xl rounded-md top-full py-[15px] absolute ltr:right-0 rtl:left-0 w-[195px] z-[5] dark:bg-dark dark:shadow-none hidden" id="filterControlHeadDropdown">
                                                    <li>
                                                        <button type="button" class="filter-control-head-option block w-full transition-all text-black ltr:text-left rtl:text-right relative py-[8px] px-[20px] hover:bg-gray-50 dark:text-white dark:hover:bg-black" data-filter="all">
                                                            All Main Heads
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div> -->
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">
                                                                Code
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Main Head
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Name
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Name in Urdu
                                                            </th>
                                                            <!-- <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                            Type
                                                        </th> -->
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Description
                                                            </th>
                                                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                    Actions
                                                                </th>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="controlHeadsTableBody">
                                                        <!-- Data will be loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="controlHeadPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0" id="controlHeadPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8">
                                        <div class="text-red-500 text-lg font-medium mb-2">Access Denied</div>
                                        <div class="text-gray-600 dark:text-gray-400">You don't have permission to view this content.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane" id="tab3">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <!-- Account Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA' || ($defaultRoleStatus == 'SA' && $defaultUnitId == 1)): ?>
                                        <form id="accountForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <select id="accountMainHead" name="main_head_id" required data-float-select
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                        <option value="" disabled selected hidden></option>
                                                    </select>
                                                    <label for="accountMainHead" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Main Head *</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <select id="accountControlHead" name="control_head_id" required data-float-select
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                        <option value="" disabled selected hidden></option>
                                                    </select>
                                                    <label for="accountControlHead" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Control Head *</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="accountName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="accountName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name *</label>

                                                </div>

                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="accountNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="accountNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام *</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <select id="accountUnitType" name="unit_type_id" required data-float-select
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                        <option value="" disabled selected hidden></option>
                                                    </select>
                                                    <label for="accountUnitType" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit Type *</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="accountDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="accountDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description (Optional)</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="number" id="accountStockLimit" name="stock_limit"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="accountStockLimit" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Stock Limit (Optional)</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitAccountBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitAccountText">Create</span>
                                                        <span id="submitAccountIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Weight Calculation Section (Collapsible) -->
                                            <div class="mt-[15px] mb-[10px]">
                                                <button type="button" id="toggleWeightSection" 
                                                    class="w-full text-left flex items-center justify-between p-[12px] bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                                    <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm flex items-center">
                                                        <i class="material-symbols-outlined mr-2 text-lg">calculate</i>
                                                        Weight Calculation (Optional)
                                                    </span>
                                                    <i class="material-symbols-outlined text-blue-700 dark:text-blue-300 transition-transform" id="weightSectionIcon">expand_more</i>
                                                </button>
                                                
                                                <div id="weightCalculationSection" class="hidden mt-[10px] p-[15px] bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800">
                                                    <!-- Dimensions Row -->
                                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-[10px] mb-[10px]">
                                                        <div class="float-group">
                                                            <input type="number" id="accountLength" name="length" min="0" step="0.01"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Length (mm)</label>
                                                        </div>
                                                        
                                                        <div class="float-group">
                                                            <input type="number" id="accountWidth" name="width" min="0" step="0.01"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Width (mm)</label>
                                                        </div>
                                                        
                                                        <div class="float-group">
                                                            <input type="number" id="accountThickness" name="thickness" min="0" step="0.01"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Thickness (mm)</label>
                                                        </div>
                                                        
                                                        <div class="float-group">
                                                            <input type="number" id="accountDiameter" name="diameter" min="0" step="0.01"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Diameter (mm)</label>
                                                        </div>
                                                        
                                                        <div class="float-group">
                                                            <input type="number" id="accountHeight" name="height" min="0" step="0.01"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Height (mm)</label>
                                                        </div>
                                                        
                                                        <div class="float-group">
                                                            <input type="number" id="accountDensity" name="density" min="0" step="0.01" value="7850"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                                placeholder="">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Density (kg/m³)</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Weight Formula -->
                                                    <div class="mb-[10px]">
                                                        <div class="float-group">
                                                            <input type="text" id="accountWeightFormula" name="weight_formula"
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm font-mono"
                                                                value=""
                                                                placeholder="(length * width * thickness * density) / 1000000000">
                                                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">
                                                                Weight Formula
                                                            </label>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-[5px] ml-[12px]">
                                                            <strong>Available variables:</strong> length, width, thickness, diameter, height, density<br>
                                                            <strong>Note:</strong> Dimensions are in mm, density is in kg/m³. Divide by 1,000,000,000 to convert mm³ to m³.<br>
                                                            <strong>Examples:</strong> 
                                                            <span class="font-mono text-xs">(length * width * thickness * density) / 1000000000</span> | 
                                                            <span class="font-mono text-xs">(3.14159 * (diameter/2) * (diameter/2) * length * density) / 1000000000</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Calculated Weight -->
                                                    <div class="float-group mb-[10px]">
                                                        <input type="number" id="accountCalculatedWeight" name="calculated_weight" step="0.01"
                                                            class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm font-bold"
                                                            placeholder="">
                                                        <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Calculated Weight (kg)</label>
                                                    </div>
                                                    
                                                    <!-- Calculate Button -->
                                                    <button type="button" id="calculateWeightBtn" 
                                                        class="px-[15px] py-[8px] bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors flex items-center">
                                                        <i class="material-symbols-outlined mr-2 text-sm">calculate</i>
                                                        Calculate Weight
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                    <!-- Account Table -->
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">

                                                <form class="relative sm:w-[240px] ltr:sm:mr-[20px] rtl:sm:ml-[20px] my-[13px] sm:my-0">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">
                                                            search
                                                        </i>
                                                    </label>
                                                    <input type="text" id="searchAccountInput" placeholder="Search items by source ID or name..." class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[40px] ltr:md:pr-[40px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    <button type="button" id="clearSearchBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Clear search" style="display: none;">
                                                        <i class="material-symbols-outlined text-sm">close</i>
                                                    </button>
                                                </form>
                                                <!-- <div class="trezo-card-dropdown relative">
                                                <button type="button" class="trezo-card-dropdown-btn inline-block rounded-md border border-gray-100 py-[5px] md:py-[6.5px] px-[12px] md:px-[19px] transition-all hover:bg-gray-50 dark:border-[#172036] dark:hover:bg-[#0a0e19]" id="filterAccountDropdownBtn">
                                                    <span class="inline-block relative ltr:pr-[17px] ltr:md:pr-[20px] rtl:pl-[17px] rtl:ml:pr-[20px]">
                                                        Show All
                                                    </span>
                                                </button>
                                                <ul class="trezo-card-dropdown-menu transition-all bg-white shadow-3xl rounded-md top-full py-[15px] absolute ltr:right-0 rtl:left-0 w-[195px] z-[5] dark:bg-dark dark:shadow-none hidden" id="filterAccountDropdown">
                                                    <li>
                                                        <button type="button" class="filter-account-option block w-full transition-all text-black ltr:text-left rtl:text-right relative py-[8px] px-[20px] hover:bg-gray-50 dark:text-white dark:hover:bg-black" data-filter="all">
                                                            All Accounts
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div> -->
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">
                                                                Code
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Main Head<br />Control Head
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Name
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Rack
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Unit Type
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Description
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">
                                                                Stock Limit
                                                            </th>
                                                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                    Actions
                                                                </th>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="accountsTableBody">
                                                        <!-- Data will be loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="accountPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0" id="accountPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <div class="text-red-500 text-lg font-medium mb-2">Access Denied</div>
                                <div class="text-gray-600 dark:text-gray-400">You don't have permission to view this content.</div>
                            </div>
                        <?php endif; ?>
                            <!-- Tab 4: Categories (ERP) - hidden for PRT/PRA -->
                            <div class="tab-pane <?php echo $showCategoriesTab ? 'active' : ''; ?>" id="tab4">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <form id="erpCategoryForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpCatName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpCatName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpCatCode" name="code" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpCatCode" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Code</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpCatDesc" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpCatDesc" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                            </div>
                                            <div class="mb-[15px] md:mb-[10px] last:mb-0">
                                                <button type="submit" id="submitErpCategoryBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitErpCategoryText">Create</span>
                                                    <span class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchErpCategoriesInput" placeholder="Search here....." class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">ID</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Code</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Description</th>
                                                            <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="erpCategoriesTableBody"></tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="erpCategoriesPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0 list-none flex items-center" id="erpCategoriesPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Tab 5: Groups (ERP) - active by default for PRT/PRA when Categories tab is hidden -->
                            <div class="tab-pane <?php echo ($hasErpFullAccess && !$showCategoriesTab) ? 'active' : ''; ?>" id="tab5">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <form id="erpGroupForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpGroupCategory" name="category_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpGroupCategory" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Category *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpGroupName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpGroupName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpGroupCode" name="code" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpGroupCode" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Code</label>
                                            </div>
                                            <div class="mb-[15px] md:mb-[10px] last:mb-0">
                                                <button type="submit" id="submitErpGroupBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitErpGroupText">Create</span>
                                                    <span class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchErpGroupsInput" placeholder="Search here....." class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">ID</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Category</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Code</th>
                                                            <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="erpGroupsTableBody"></tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="erpGroupsPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0 list-none flex items-center" id="erpGroupsPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Tab 6: Sub-Groups (ERP) -->
                            <div class="tab-pane" id="tab6">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <form id="erpSubGroupForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpSubGroupCategory" name="category_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpSubGroupCategory" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Category</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpSubGroupGroup" name="group_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpSubGroupGroup" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Group *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpSubGroupName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpSubGroupName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpSubGroupCode" name="code" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpSubGroupCode" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Code</label>
                                            </div>
                                            <div class="mb-[15px] md:mb-[10px] last:mb-0">
                                                <button type="submit" id="submitErpSubGroupBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitErpSubGroupText">Create</span>
                                                    <span class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchErpSubGroupsInput" placeholder="Search here....." class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">ID</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Group</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Code</th>
                                                            <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="erpSubGroupsTableBody"></tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="erpSubGroupsPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0 list-none flex items-center" id="erpSubGroupsPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Tab 7: Attributes (ERP) -->
                            <div class="tab-pane" id="tab7">
                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'PM' || $defaultRoleStatus == 'PRT' || $defaultRoleStatus == 'PRA'): ?>
                                    <form id="erpAttributeForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpAttrCategory" name="category_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpAttrCategory" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Category</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpAttrGroup" name="group_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpAttrGroup" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Group</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <select id="erpAttrSubGroup" name="sub_group_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                                    <option value="" disabled selected hidden></option>
                                                </select>
                                                <label for="erpAttrSubGroup" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Sub-Group *</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="erpAttrName" name="attribute_name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm" placeholder="">
                                                <label for="erpAttrName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Attribute Name *</label>
                                            </div>
                                            <div class="mb-[15px] md:mb-[10px] last:mb-0 flex items-center gap-2">
                                                <label class="flex items-center gap-1 text-sm text-black dark:text-white"><input type="checkbox" id="erpAttrRequired" name="is_required" value="1" class="rounded"> Required</label>
                                                <button type="submit" id="submitErpAttributeBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitErpAttributeText">Create</span>
                                                    <span class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchErpAttributesInput" placeholder="Search here....." class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">ID</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Sub-Group</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Attribute</th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Required</th>
                                                            <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="erpAttributesTableBody"></tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="erpAttributesPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0 list-none flex items-center" id="erpAttributesPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Tab 8: Items (ERP Structure) -->
                            <div class="tab-pane <?php echo $hasErpLimitedAccess ? 'active' : ''; ?>" id="tab8">
                                <?php if ($hasErpFullAccess): ?>
                                    <!-- Item creation form - only for full access users -->
                                    <form id="erpItemForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] float-group">
                                                <select id="erpCategory" name="category_id" required data-float-select
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                    <option value="">Select Category</option>
                                                </select>
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Category *</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <select id="erpGroup" name="group_id" required data-float-select disabled
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                    <option value="">Select Group</option>
                                                </select>
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Group *</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <select id="erpSubGroup" name="sub_group_id" required data-float-select disabled
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                    <option value="">Select Sub-Group</option>
                                                </select>
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Sub-Group *</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <input type="text" id="erpItemName" name="name" required
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item Name *</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <input type="text" id="erpItemNameUrdu" name="name_in_urdu" required
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام *</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <select id="erpUnitType" name="unit_type_id" required data-float-select
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                    <option value="">Select Unit Type</option>
                                                </select>
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit Type *</label>
                                            </div>
                                            <div class="mb-[10px] float-group" id="erpSkuFieldWrapper">
                                                <input type="text" id="erpNormalizedSku" name="normalized_sku" readonly
                                                    class="h-[30px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full text-sm outline-0 transition-all focus:border-primary-500"
                                                    placeholder="Select Category, Group & Sub-Group to see next SKU">
                                                
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <input type="number" id="erpItemCalculatedWeight" name="calculated_weight" min="0" step="0.000001"
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Weight (kg)</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <input type="number" id="erpItemMinOrderLevel" name="min_order_level" min="0" step="1"
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Min Order Level</label>
                                            </div>
                                            <div class="mb-[10px] float-group">
                                                <input type="number" id="erpItemMaxOrderLevel" name="max_order_level" min="0" step="1"
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Max Order Level</label>
                                            </div>
                                            <div id="erpAttributeFields" class="md:col-span-2"></div>
                                            <div class="mb-[10px]">
                                                <button type="submit" id="submitErpItemBtn"
                                                    class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitErpItemText">Create Item</span>
                                                    <i class="material-symbols-outlined ml-1 text-sm align-middle">add</i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                                <!-- Items table - visible for all users -->
                                <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                    <div class="trezo-card-content">
                                        <div class="flex justify-between items-center mb-[15px]">
                                            <form class="relative sm:w-[240px]">
                                                <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                    <i class="material-symbols-outlined !text-[20px]">search</i>
                                                </label>
                                                <input type="text" id="searchErpItemsInput" placeholder="Search by name, SKU, legacy code..."
                                                    class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                            </form>
                                            <?php if ($hasErpFullAccess): ?>
                                            <button type="button" id="exportErpItemsToExcelBtn" class="inline-flex items-center gap-2 rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm font-semibold">
                                                <i class="material-symbols-outlined text-sm" style="vertical-align: middle;">download</i>
                                                <span>Export to Excel</span>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="table-responsive overflow-x-auto">
                                            <table class="w-full">
                                                <thead class="text-black dark:text-white">
                                                    <tr>
                                                        <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md">Code / SKU</th>
                                                        <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                        <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Category</th>
                                                        <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Group</th>
                                                        <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Sub-Group</th>
                                                        <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap last:rounded-tr-md">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-black dark:text-white" id="erpItemsTableBody"></tbody>
                                            </table>
                                        </div>
                                        <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                            <p class="!mb-0 text-sm" id="erpItemsPaginationInfo">Loading...</p>
                                            <ol class="mt-[10px] sm:mt-0 list-none flex items-center" id="erpItemsPaginationControls"></ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grow"></div>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>

        <!-- Toast Container -->
        <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>

        <!-- Edit Main Head Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editMainHeadModal">
            <div class="popup-dialog flex transition-all max-w-[550px] min-h-full items-center mx-auto">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <div class="trezo-card-title">
                            <h5 class="mb-0">
                                Edit Main Head
                            </h5>
                        </div>
                        <div class="trezo-card-subtitle">
                            <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" id="closeEditModal">
                                <i class="ri-close-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <form id="editMainHeadForm">
                            <input type="hidden" id="editMainHeadId" name="id">

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <input type="text" id="editMainHeadName" name="name" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editMainHeadName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>

                                </div>
                                <div class="relative float-group">

                                    <input type="text" id="editMainHeadNameUrdu" name="name_in_urdu" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="اردو میں نام">
                                    <label for="editMainHeadNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                </div>
                            </div>

                            <div class="relative float-group">

                                <input id="editMainHeadDescription" name="description"
                                    class="rounded-md h-[30px] text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                    placeholder="">
                                <label for="editMainHeadDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>

                            </div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400 ltr:mr-[11px] rtl:ml-[11px] mb-[5px]" type="button" id="updateMainHeadBtn">
                            <i class="ri-save-line mr-[5px]"></i>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Control Head Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editControlHeadModal">
            <div class="popup-dialog flex transition-all max-w-[550px] min-h-full items-center mx-auto">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <div class="trezo-card-title">
                            <h5 class="mb-0">
                                Edit Control Head
                            </h5>
                        </div>
                        <div class="trezo-card-subtitle">
                            <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" id="closeEditControlHeadModal">
                                <i class="ri-close-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <form id="editControlHeadForm">
                            <input type="hidden" id="editControlHeadId" name="id">

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <select id="editControlHeadMainHead" name="main_head_id" required data-float-select
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label for="editControlHeadMainHead" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Main Head</label>

                                </div>
                                <div class="relative float-group">

                                    <input type="text" id="editControlHeadName" name="name" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editControlHeadName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>
                                </div>
                            </div>

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <input type="text" id="editControlHeadNameUrdu" name="name_in_urdu" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="اردو میں نام">
                                    <label for="editControlHeadNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                </div>
                                <div class="relative float-group">

                                    <input type="text" id="editControlHeadType" name="type" value="account" readonly
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-gray-100 dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editControlHeadType" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Account</label>
                                </div>
                            </div>

                            <div class="relative float-group">

                                <input id="editControlHeadDescription" name="description"
                                    class="rounded-md h-[30px] text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                    placeholder="Enter description">
                                <label for="editControlHeadDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>
                            </div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400 ltr:mr-[11px] rtl:ml-[11px] mb-[5px]" type="button" id="updateControlHeadBtn">
                            <i class="ri-save-line mr-[5px]"></i>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Account Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editAccountModal">
            <div class="popup-dialog flex transition-all max-w-[700px] min-h-full items-center mx-auto">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <div class="trezo-card-title">
                            <h5 class="mb-0">
                                Edit Item
                            </h5>
                        </div>
                        <div class="trezo-card-subtitle">
                            <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" id="closeEditAccountModal">
                                <i class="ri-close-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <form id="editAccountForm">
                            <input type="hidden" id="editAccountId" name="id">

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <select id="editAccountMainHead" name="main_head_id" required data-float-select
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label for="editAccountMainHead" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Main Head</label>

                                </div>
                                <div class="relative float-group">

                                    <select id="editAccountControlHead" name="control_head_id" required data-float-select
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label for="editAccountControlHead" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Control Head</label>

                                </div>
                            </div>

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <input type="text" id="editAccountName" name="name" required
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editAccountName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>

                                </div>
                                <div class="relative float-group">

                                    <input type="text" id="editAccountNameUrdu" name="name_in_urdu" required
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editAccountNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>


                                </div>
                            </div>

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <select id="editAccountUnitType" name="unit_type_id" required data-float-select
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label for="editAccountUnitType" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Unit Type</label>

                                </div>
                            </div>

                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">

                                    <input type="text" id="editAccountDescription" name="description"
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editAccountDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>

                                </div>
                                <div class="relative float-group">

                                    <input type="number" id="editAccountAddress" name="stock_limit"
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                        placeholder="">
                                    <label for="editAccountAddress" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Stock Limit</label>

                                </div>
                            </div>
                            
                            <!-- Weight Calculation Section (Collapsible) -->
                            <div class="mt-[15px] mb-[10px]">
                                <button type="button" id="toggleEditWeightSection" 
                                    class="w-full text-left flex items-center justify-between p-[12px] bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm flex items-center">
                                        <i class="material-symbols-outlined mr-2 text-lg">calculate</i>
                                        Weight Calculation (Optional)
                                    </span>
                                    <i class="material-symbols-outlined text-blue-700 dark:text-blue-300 transition-transform" id="editWeightSectionIcon">expand_more</i>
                                </button>
                                
                                <div id="editWeightCalculationSection" class="hidden mt-[10px] p-[15px] bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800">
                                    <!-- Dimensions Row -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-[10px] mb-[10px]">
                                        <div class="float-group">
                                            <input type="number" id="editAccountLength" name="length" min="0" step="0.01"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Length (mm)</label>
                                        </div>
                                        
                                        <div class="float-group">
                                            <input type="number" id="editAccountWidth" name="width" min="0" step="0.01"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Width (mm)</label>
                                        </div>
                                        
                                        <div class="float-group">
                                            <input type="number" id="editAccountThickness" name="thickness" min="0" step="0.01"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Thickness (mm)</label>
                                        </div>
                                        
                                        <div class="float-group">
                                            <input type="number" id="editAccountDiameter" name="diameter" min="0" step="0.01"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Diameter (mm)</label>
                                        </div>
                                        
                                        <div class="float-group">
                                            <input type="number" id="editAccountHeight" name="height" min="0" step="0.01"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Height (mm)</label>
                                        </div>
                                        
                                        <div class="float-group">
                                            <input type="number" id="editAccountDensity" name="density" min="0" step="0.01" value="7850"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm"
                                                placeholder="">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Density (kg/m³)</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Weight Formula -->
                                    <div class="mb-[10px]">
                                        <div class="float-group">
                                            <input type="text" id="editAccountWeightFormula" name="weight_formula"
                                                class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm font-mono"
                                                value=""
                                                placeholder="(length * width * thickness * density) / 1000000000">
                                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">
                                                Weight Formula
                                            </label>
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-[5px] ml-[12px]">
                                            <strong>Available variables:</strong> length, width, thickness, diameter, height, density<br>
                                            <strong>Note:</strong> Dimensions are in mm, density is in kg/m³. Divide by 1,000,000 to convert mm³ to m³.<br>
                                            <strong>Examples:</strong> 
                                            <span class="font-mono text-xs">(length * width * thickness * density) / 1000000000</span> | 
                                            <span class="font-mono text-xs">(3.14159 * (diameter/2) * (diameter/2) * length * density) / 1000000000</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Calculated Weight -->
                                    <div class="float-group mb-[10px]">
                                        <input type="number" id="editAccountCalculatedWeight" name="calculated_weight" step="0.01"
                                            class="h-[30px] rounded-md text-black dark:text-white border border-blue-200 dark:border-blue-700 bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all focus:border-blue-500 text-sm font-bold"
                                            placeholder="">
                                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Calculated Weight (kg)</label>
                                    </div>
                                    
                                    <!-- Calculate Button -->
                                    <button type="button" id="editCalculateWeightBtn" 
                                        class="px-[15px] py-[8px] bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="material-symbols-outlined mr-2 text-sm">calculate</i>
                                        Calculate Weight
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400 ltr:mr-[11px] rtl:ml-[11px] mb-[5px]" type="button" id="updateAccountBtn">
                            <i class="ri-save-line mr-[5px]"></i>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit ERP Item Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editErpItemModal">
            <div class="popup-dialog flex transition-all min-h-full items-center mx-auto" style="max-width: 500px;">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <h5 class="mb-0">Edit Item (ERP Structure)</h5>
                        <button type="button" class="text-[23px] leading-none text-black dark:text-white hover:text-primary-500" id="closeEditErpItemModal"><i class="ri-close-fill"></i></button>
                    </div>
                    <div class="trezo-card-content pb-[20px]">
                        <form id="editErpItemForm">
                            <input type="hidden" id="editErpItemId" name="id">
                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">
                                    <select id="editErpCategory" name="category_id" required data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                                        <option value="">Category *</option>
                                    </select>
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Category</label>
                                </div>
                                <div class="relative float-group">
                                    <select id="editErpGroup" name="group_id" required data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                                        <option value="">Group *</option>
                                    </select>
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Group</label>
                                </div>
                                <div class="relative float-group">
                                    <select id="editErpSubGroup" name="sub_group_id" required data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                                        <option value="">Sub-Group *</option>
                                    </select>
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Sub-Group</label>
                                </div>
                                <div class="relative float-group">
                                    <select id="editErpUnitType" name="unit_type_id" required data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                                        <option value="">Unit Type *</option>
                                    </select>
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Unit Type</label>
                                </div>
                                <div class="relative float-group" id="editErpSkuFieldWrapper">
                                    <input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="Code / SKU">
                                </div>
                            </div>
                            <div class="grid lg:grid-cols-2 gap-[20px] mb-[20px]">
                                <div class="relative float-group">
                                    <input type="text" id="editErpItemName" name="name" required class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="">
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Name</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="text" id="editErpItemNameUrdu" name="name_in_urdu" required class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="">
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">اردو میں نام</label>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-[15px] mt-[15px]">
                                <div class="relative float-group">
                                    <input type="number" id="editErpItemCalculatedWeight" name="calculated_weight" min="0" step="0.000001" class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="">
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Weight (kg)</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="number" id="editErpItemMinOrderLevel" name="min_order_level" min="0" step="1" class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="">
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Min Order Level</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="number" id="editErpItemMaxOrderLevel" name="max_order_level" min="0" step="1" class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="">
                                    <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Max Order Level</label>
                                </div>
                            </div>
                            <!-- Dynamic Attribute Fields Container -->
                            <div id="editErpAttributeFields" class="mt-[15px]"></div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] border-t border-gray-100 dark:border-[#172036]">
                        <button type="button" id="updateErpItemBtn" class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white rounded-md hover:bg-primary-400"><i class="ri-save-line mr-[5px]"></i>Update</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rack Assignment Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="assignRackModal">
            <div class="popup-dialog flex transition-all max-w-[400px] min-h-full items-center mx-auto">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <div class="trezo-card-title">
                            <h5 class="mb-0">
                                Assign/Edit Rack
                            </h5>
                        </div>
                        <div class="trezo-card-subtitle">
                            <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" id="closeAssignRackModal">
                                <i class="ri-close-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <form id="assignRackForm">
                            <input type="hidden" id="assignRackItemId" name="item_id">
                            
                            <div class="mb-[20px]">
                                <div class="relative float-group">
                                    <select id="assignRackSelect" name="rack_id" required data-float-select
                                        class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label for="assignRackSelect" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Rack</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400 ltr:mr-[11px] rtl:ml-[11px] mb-[5px]" type="button" id="saveRackAssignmentBtn">
                            <i class="ri-save-line mr-[5px]"></i>
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
    <?php include 'includes/scripts.php'; ?>

    <script>
        // ======================== USER PERMISSIONS ========================
        // Pass user permissions to JavaScript
        window.userPermissions = <?php echo json_encode($_SESSION['role_permissions'] ?? []); ?>;
        
        // Pass role status and current user id to JavaScript
        window.defaultRoleStatus = '<?php echo $defaultRoleStatus; ?>';
        window.currentUserId = <?php echo json_encode($currentUser['user_id'] ?? null); ?>;
        
        // ERP access control flags
        window.hasErpFullAccess = <?php echo json_encode($hasErpFullAccess ?? false); ?>;
        window.hasErpLimitedAccess = <?php echo json_encode($hasErpLimitedAccess ?? true); ?>;
        window.isSaLimitedAccess = <?php echo json_encode($isSaLimitedAccess ?? false); ?>;
        window.canEditErpItems = <?php echo json_encode($canEditErpItems ?? false); ?>;
        window.erpFullAccessUserId = 89;

        // Client-side permission checking function
        window.hasPermission = function(module, action) {
            if (!window.userPermissions || !Array.isArray(window.userPermissions)) {
                return false;
            }

            if (action === null || action === undefined) {
                // Check if user has any permission for this module
                return window.userPermissions.some(permission =>
                    permission.startsWith(module + '.')
                );
            }

            // Check specific permission
            const permission = module + '.' + action;
            return window.userPermissions.includes(permission);
        };

        // ======================== HELPER FUNCTIONS ========================
        // Helper function to refresh select box displays
        function refreshSelectBox(selectElement) {
            if (!selectElement) return;

            // Handle enhanced select boxes - use the built-in method
            if (selectElement.__enhanced && typeof selectElement.__enhanced.setDisplayFromValue === 'function') {
                selectElement.__enhanced.setDisplayFromValue();
            }

            // Handle Slim Select
            if (selectElement.slim && typeof selectElement.slim.setSelected === 'function') {
                selectElement.slim.setSelected(selectElement.value);
            }

            // Handle Select2
            try {
                const jq = window.jQuery || window.$;
                if (jq && jq.fn && jq.fn.select2 && jq(selectElement).hasClass('select2-hidden-accessible')) {
                    jq(selectElement).trigger('change.select2');
                }
            } catch (e) {}
        }

        // ======================== ROLE-BASED FIELD CONTROL ========================
        // This function is no longer needed as we handle field visibility directly in editAccount()

        // ======================== CUSTOM SCROLLBAR STYLES ========================
        // Add custom scrollbar styles for dropdown
        const style = document.createElement('style');
        style.textContent = `
            /* Webkit browsers (Chrome, Safari, Edge) */
            .max-h-48::-webkit-scrollbar {
                width: 8px;
            }
            .max-h-48::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }
            .max-h-48::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
                border: 1px solid #e2e8f0;
            }
            .max-h-48::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            /* Dark mode */
            .dark .max-h-48::-webkit-scrollbar-track {
                background: #1e293b;
            }
            .dark .max-h-48::-webkit-scrollbar-thumb {
                background: #475569;
                border: 1px solid #334155;
            }
            .dark .max-h-48::-webkit-scrollbar-thumb:hover {
                background: #64748b;
            }
            
            /* Firefox */
            .max-h-48 {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            .dark .max-h-48 {
                scrollbar-color: #475569 #1e293b;
            }
        `;
        document.head.appendChild(style);

        // ======================== UNIFIED ENTER NAVIGATION ========================

        // Single unified navigation system that works for all fields
        function handleEnterNavigation(currentFieldId) {


            // Define the exact sequence for each form
            const formSequences = {
                'mainHeadForm': ['mainHeadName', 'mainHeadNameUrdu', 'mainHeadDescription', 'submitMainHeadBtn'],
                'controlHeadForm': ['controlHeadMainHead', 'controlHeadName', 'controlHeadNameUrdu', 'controlHeadDescription', 'submitControlHeadBtn'],
                'accountForm': ['accountMainHead', 'accountControlHead', 'accountName', 'accountNameUrdu', 'accountUnitType', 'accountDescription', 'accountStockLimit', 'submitAccountBtn'],
                'editMainHeadForm': ['editMainHeadName', 'editMainHeadNameUrdu', 'editMainHeadDescription', 'updateMainHeadBtn'],
                'editControlHeadForm': ['editControlHeadMainHead', 'editControlHeadName', 'editControlHeadNameUrdu', 'editControlHeadType', 'editControlHeadDescription', 'updateControlHeadBtn'],
                'editAccountForm': ['editAccountMainHead', 'editAccountControlHead', 'editAccountName', 'editAccountNameUrdu', 'editAccountUnitType', 'editAccountDescription', 'editAccountAddress', 'updateAccountBtn']
            };

            // Find which form the current field belongs to
            let currentForm = null;
            let currentSequence = null;

            // First check if it's a direct form field
            for (const [formId, sequence] of Object.entries(formSequences)) {
                if (sequence.includes(currentFieldId)) {
                    currentForm = formId;
                    currentSequence = sequence;
                    break;
                }
            }

            // If not found in forms, check if it's a button that belongs to a modal
            if (!currentForm) {
                const buttonToModalMap = {
                    'updateMainHeadBtn': 'editMainHeadForm',
                    'updateControlHeadBtn': 'editControlHeadForm',
                    'updateAccountBtn': 'editAccountForm'
                };

                if (buttonToModalMap[currentFieldId]) {
                    currentForm = buttonToModalMap[currentFieldId];
                    currentSequence = formSequences[currentForm];
                }
            }

            if (!currentForm || !currentSequence) {
                console.log('Field not found in any form sequence:', currentFieldId);
                return;
            }

            // Find current position
            const currentIndex = currentSequence.indexOf(currentFieldId);
            if (currentIndex === -1) {
                console.log('Field not found in sequence:', currentFieldId);
                return;
            }

            // Get next field
            const nextIndex = currentIndex + 1;
            if (nextIndex < currentSequence.length) {
                const nextFieldId = currentSequence[nextIndex];
                console.log('Moving to next field:', nextFieldId);

                const nextField = document.getElementById(nextFieldId);
                if (nextField) {
                    if (nextField.tagName === 'SELECT' && nextField.__enhanced && nextField.__enhanced.control) {
                        nextField.__enhanced.control.focus();
                    } else {
                        nextField.focus();
                        if (nextField.select) {
                            try {
                                nextField.select();
                            } catch (_) {}
                        }
                    }
                }
            } else if (currentFieldId.includes('submit') || currentFieldId.includes('update')) {
                // Submit the form or trigger button click
                if (currentFieldId.includes('update')) {
                    // For update buttons, trigger the click directly
                    const updateBtn = document.getElementById(currentFieldId);
                    if (updateBtn) {
                        updateBtn.click();
                    }
                } else {
                    // For submit buttons, dispatch form submit event
                    const form = document.getElementById(currentForm);
                    if (form) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            }
        }

        // Global event listener for all Enter key presses
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;

            console.log('Enter key pressed on:', e.target.id, 'Tag:', e.target.tagName, 'Type:', e.target.type);

            // Don't interfere with form submissions, textareas, or when dropdown is open
            if (e.target.tagName === 'TEXTAREA' ||
                e.target.closest('.absolute') && !e.target.closest('.absolute').classList.contains('hidden')) {
                console.log('Skipping navigation for:', e.target.id);
                return;
            }

            // Special handling for update buttons - trigger click directly
            if (e.target.id === 'updateMainHeadBtn' || e.target.id === 'updateControlHeadBtn' || e.target.id === 'updateAccountBtn') {
                console.log('Update button pressed, triggering click');
                e.preventDefault();
                e.stopPropagation();
                e.target.click();
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const currentElement = e.target;
            let currentFieldId = currentElement.id;

            // For enhanced selects, get the original select ID
            if (currentElement.tagName === 'BUTTON' && currentElement.closest('.relative')) {
                const selectEl = currentElement.parentNode.querySelector('select');
                if (selectEl) {
                    currentFieldId = selectEl.id;
                    console.log('Enhanced select detected, using ID:', currentFieldId);
                } else {
                    // Try to find select in the same float-group
                    const floatGroup = currentElement.closest('.float-group');
                    if (floatGroup) {
                        const selectInGroup = floatGroup.querySelector('select');
                        if (selectInGroup) {
                            currentFieldId = selectInGroup.id;
                            console.log('Found select in float-group, using ID:', currentFieldId);
                        }
                    }

                    if (!currentFieldId) {
                        console.log('Button found but no select element detected');
                    }
                }
            }

            console.log('Processing navigation for:', currentFieldId);

            // Only proceed if we have a valid field ID
            if (currentFieldId && currentFieldId.trim() !== '') {
                console.log('Using unified navigation for:', currentFieldId);
                handleEnterNavigation(currentFieldId);
            } else {
                console.log('No valid field ID found, skipping navigation');
            }
        });

        // Auto-focus first field on page load and when modals open (non-invasive)
        (function() {
            function focusFirstControlInForm(formId) {
                const form = document.getElementById(formId);
                if (!form) return;
                // Build a normalized, visible controls list that prefers enhanced select controls
                const raw = Array.from(form.querySelectorAll('input, select, textarea, button'));
                const controls = raw
                    .map(el => {
                        // Prefer our custom enhanced select button over the hidden native select
                        if (el.tagName === 'SELECT' && el.__enhanced && el.__enhanced.control) {
                            return el.__enhanced.control;
                        }
                        return el;
                    })
                    .filter((el, idx, arr) => !el.disabled && el.type !== 'hidden' && el.tabIndex !== -1 && el.offsetParent !== null && arr.indexOf(el) === idx);
                const first = controls[0];
                if (first) {
                    setTimeout(() => {
                        try {
                            first.focus();
                            if (first.select) first.select();
                        } catch (_) {}
                    }, 0);
                }
            }

            function focusFirstForEntity(entity) {
                const map = {
                    mainHead: 'mainHeadForm',
                    controlHead: 'controlHeadForm',
                    account: 'accountForm',
                    erpCategory: 'erpCategoryForm',
                    erpGroup: 'erpGroupForm',
                    erpSubGroup: 'erpSubGroupForm',
                    erpAttribute: 'erpAttributeForm',
                    erpItem: 'erpItemForm'
                };
                const formId = map[entity];
                if (formId) focusFirstControlInForm(formId);
            }
            document.addEventListener('DOMContentLoaded', function() {
                // Default focus when page loads
                setTimeout(() => focusFirstForEntity('mainHead'), 120);
            });
            // Focus when switching tabs
            const tabMap = {
                tab1: 'mainHead',
                tab2: 'controlHead',
                tab3: 'account',
                tab4: 'erpCategory',
                tab5: 'erpGroup',
                tab6: 'erpSubGroup',
                tab7: 'erpAttribute',
                tab8: 'erpItem'
            };
            const navLinks = document.querySelectorAll('.nav-link');
            if (navLinks && navLinks.length) {
                navLinks.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        const entity = tabMap[tabId];
                        setTimeout(() => focusFirstForEntity(entity), 80);
                    });
                });
            }
            const modalToForm = {
                editMainHeadModal: 'editMainHeadForm',
                editControlHeadModal: 'editControlHeadForm',
                editAccountModal: 'editAccountForm'
            };
            Object.keys(modalToForm).forEach(mid => {
                const modal = document.getElementById(mid);
                if (!modal) return;
                const obs = new MutationObserver(() => {
                    if (modal.classList.contains('active')) {
                        focusFirstControlInForm(modalToForm[mid]);
                    }
                });
                try {
                    obs.observe(modal, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                } catch (_) {}
            });
        })();

        // Global variables for table management
        let currentPage = 1;
        let currentLimit = 10;
        let currentSearch = '';
        let currentFilter = 'all';
        let mainHeadsData = [];

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');

            if (!toastContainer) {
                // Silently return instead of logging error to avoid console spam
                return;
            }

            const toast = document.createElement('div');

            // Light theme professional color scheme
            let bgColor, textColor, iconBg, borderColor, icon, shadowColor;
            switch (type) {
                case 'success':
                    bgColor = '#f0fdf4';
                    textColor = '#166534';
                    iconBg = '#16a34a';
                    borderColor = '#bbf7d0';
                    icon = '✓';
                    shadowColor = 'rgba(22, 163, 74, 0.15)';
                    break;
                case 'error':
                    bgColor = '#fef2f2';
                    textColor = '#991b1b';
                    iconBg = '#dc2626';
                    borderColor = '#fecaca';
                    icon = '✗';
                    shadowColor = 'rgba(220, 38, 38, 0.15)';
                    break;
                case 'warning':
                    bgColor = '#fffbeb';
                    textColor = '#92400e';
                    iconBg = '#d97706';
                    borderColor = '#fed7aa';
                    icon = '⚠';
                    shadowColor = 'rgba(217, 119, 6, 0.15)';
                    break;
                case 'info':
                    bgColor = '#eff6ff';
                    textColor = '#1e40af';
                    iconBg = '#3b82f6';
                    borderColor = '#bfdbfe';
                    icon = 'ℹ';
                    shadowColor = 'rgba(59, 130, 246, 0.15)';
                    break;
                default:
                    bgColor = '#f0fdf4';
                    textColor = '#166534';
                    iconBg = '#16a34a';
                    borderColor = '#bbf7d0';
                    icon = '✓';
                    shadowColor = 'rgba(22, 163, 74, 0.15)';
            }

            toast.style.cssText = `
                    background: ${bgColor};
                    color: ${textColor};
                    padding: 16px 20px;
                    border-radius: 12px;
                    box-shadow: 0 10px 15px -3px ${shadowColor}, 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    position: relative;
                    z-index: 9999;
                    min-width: 300px;
                    max-width: 400px;
                    font-weight: 500;
                    font-size: 14px;
                    border: 1px solid ${borderColor};
                    transform: translateX(100%);
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    letter-spacing: 0.025em;
                `;

            toast.innerHTML = `
                    <div style="
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        background: ${iconBg};
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 12px;
                        font-weight: bold;
                        color: white;
                        flex-shrink: 0;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    ">${icon}</div>
                    <span style="flex: 1; line-height: 1.4;">${message}</span>
                    <button style="
                        background: none;
                        border: none;
                        color: ${textColor};
                        cursor: pointer;
                        font-size: 18px;
                        opacity: 0.6;
                        transition: opacity 0.2s;
                        padding: 0;
                        margin-left: 8px;
                        flex-shrink: 0;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    " onclick="this.parentElement.remove()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">×</button>
                `;

            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 5 seconds
            setTimeout(() => {
                if (toastContainer.contains(toast)) {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (toastContainer.contains(toast)) {
                            toastContainer.removeChild(toast);
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Load main heads data
        function loadMainHeads() {
            const tableBody = document.getElementById('mainHeadsTableBody');
            const paginationInfo = document.getElementById('paginationInfo');

            // Show loading state
            const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
            const loadingColspan = canShowActions ? '5' : '4';
            tableBody.innerHTML = `<tr><td colspan="${loadingColspan}" class="text-center py-8">Loading...</td></tr>`;
            paginationInfo.textContent = 'Loading...';

            // Build query parameters
            const params = new URLSearchParams({
                page: currentPage,
                limit: currentLimit
            });

            if (currentSearch) {
                params.append('search', currentSearch);
            }

            if (currentFilter !== 'all') {
                params.append('type', currentFilter);
            }

            // Fetch data from API
            fetch(`../api/main-heads?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin', // Include session cookies
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        mainHeadsData = result.data;
                        renderTable();
                        renderPagination();
                    } else {
                        showToast(result.error || 'Failed to load data', 'error');
                        const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
                        const colspan = canShowActions ? '5' : '4';
                        tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-8 text-red-500">Failed to load data</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error. Please try again.', 'error');
                    const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
                    const colspan = canShowActions ? '5' : '4';
                    tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-8 text-red-500">Network error</td></tr>`;
                });
        }

        // Render table with data
        function renderTable() {
            const tableBody = document.getElementById('mainHeadsTableBody');
            const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';

            if (!mainHeadsData.records || mainHeadsData.records.length === 0) {
                const colspan = canShowActions ? '5' : '4';
                tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-5 text-gray-500">No data found</td></tr>`;
                return;
            }

            tableBody.innerHTML = mainHeadsData.records.map(item => `
                    <tr>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            #MH-${String(item.id).padStart(3, '0')}
                        </td>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="block font-medium">
                                ${item.name}
                            </span>
                        </td>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="block font-medium">
                                ${item.name_in_urdu}
                            </span>
                        </td>
                        <!--<td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                        //     <span class="px-[8px] py-[3px] inline-block ${item.type === 'account' ? 'bg-primary-50 dark:bg-[#15203c] text-primary-500' : 'bg-success-100 dark:bg-[#15203c] text-success-600'} rounded-sm font-medium text-xs">
                        //         ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        //     </span>
                        // </td>-->
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            ${item.description || '-'}
                        </td>
                        ${canShowActions ? `
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] md:ltr:first:pl-[25px] md:rtl:first:pr-[25px] ltr:first:pr-0 rtl:first:pl-0 border-b border-gray-100 dark:border-[#172036]">
                            <div class="flex items-center gap-[9px]">
                                <button type="button" class="text-blue-500" onclick="editMainHead(${item.id})" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>
                                <!--<button type="button" class="text-red-500" onclick="deleteMainHead(${item.id})" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>-->
                            </div>
                        </td>
                        ` : ''}
                    </tr>
                `).join('');
        }

        // Render pagination (Recent Orders style)
        function renderPagination() {
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');

            if (!mainHeadsData.total) {
                paginationInfo.textContent = 'No entries';
                paginationControls.innerHTML = '';
                return;
            }

            const total = parseInt(mainHeadsData.total, 10) || 0;
            const limit = parseInt(mainHeadsData.limit, 10) || 10;
            const page = parseInt(mainHeadsData.page, 10) || 1;
            const totalPages = parseInt(mainHeadsData.total_pages, 10) || 1;
            const start = (page - 1) * limit + 1;
            const end = Math.min(start + limit - 1, total);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${total} entries`;

            const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
            const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

            let html = '';
            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page - 1}\" class=\"${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_left<\/i>\n  <\/a>\n<\/li>`;

            const maxButtons = 5;
            let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
            let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);
            startBtn = Math.max(1, Math.min(startBtn, endBtn - maxButtons + 1));
            for (let p = startBtn; p <= endBtn; p++) {
                const cls = p === page ? activeCls : btnCls;
                html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${p}\" class=\"${cls}\">${p}<\/a>\n<\/li>`;
            }
            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page + 1}\" class=\"${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_right<\/i>\n  <\/a>\n<\/li>`;

            paginationControls.innerHTML = html;
            paginationControls.querySelectorAll('a[data-page]').forEach(a => {
                const targetPage = parseInt(a.getAttribute('data-page'), 10);
                a.onclick = () => {
                    if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== page) changePage(targetPage);
                };
            });
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            loadMainHeads();
        }

        // View main head
        function viewMainHead(id) {
            // For now, just show a toast. You can implement a modal or redirect to detail page
            showToast(`Viewing main head with ID: ${id}`, 'info');
        }

        // Edit main head
        function editMainHead(id) {
            // Find the item in the data
            const item = mainHeadsData.records.find(record => record.id == id);
            if (!item) {
                showToast(`❌ Main Head not found. Please refresh the page and try again.`, 'error');
                return;
            }

            // Populate the modal form with existing data
            document.getElementById('editMainHeadId').value = item.id;
            document.getElementById('editMainHeadName').value = item.name;
            document.getElementById('editMainHeadNameUrdu').value = item.name_in_urdu;
            document.getElementById('editMainHeadDescription').value = item.description || '';

            // Show the modal
            const modal = document.getElementById('editMainHeadModal');
            modal.classList.add('active');
            modal.classList.remove('opacity-0', 'pointer-events-none');
        }

        // Delete main head with SweetAlert
        function deleteMainHead(id) {
            const item = mainHeadsData.records.find(record => record.id == id);
            const mainHeadName = item ? item.name : 'this main head';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${mainHeadName}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`../api/main-heads/${id}`, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (!result.success) {
                                throw new Error(result.error || 'Failed to delete main head');
                            }
                            return result;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Main Head has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    showToast(`🗑️ Main Head "${mainHeadName}" has been deleted successfully!`, 'success');
                    loadMainHeads();
                }
            });
        }

        // Main Head Form Handling
        document.addEventListener('DOMContentLoaded', function() {
            const mainHeadForm = document.getElementById('mainHeadForm');
            const submitBtn = document.getElementById('submitMainHeadBtn');
            const submitText = document.getElementById('submitMainHeadText');
            const submitIcon = document.getElementById('submitMainHeadIcon');
            const resetBtn = document.getElementById('resetMainHeadBtn');
            // Support both legacy and new search input IDs
            const searchInput = document.getElementById('searchMainHeadInput') || document.getElementById('searchInput');
            // These may be absent in the simplified UI; guard usage
            const filterDropdownBtn = document.getElementById('filterDropdownBtn');
            const filterDropdown = document.getElementById('filterDropdown');

            // Load initial data
            loadMainHeads();

            // Form submission
            mainHeadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const editId = submitBtn.getAttribute('data-edit-id');
                const isEdit = editId !== null;

                // Get form data
                const formData = new FormData(mainHeadForm);
                const data = {
                    name: formData.get('name'),
                    name_in_urdu: formData.get('name_in_urdu'),
                    type: formData.get('type'),
                    description: formData.get('description')
                };

                // Validate required fields
                if (!data.name || !data.name_in_urdu || !data.type) {
                    showToast(`⚠️ Please fill in all required fields (Name and Name in Urdu)`, 'warning');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitText.textContent = isEdit ? 'Updating...' : 'Creating...';
                submitIcon.textContent = 'hourglass_empty';

                // No loading toast to avoid double toasters

                const url = isEdit ? `../api/main-heads/${editId}` : '../api/main-heads';
                const method = isEdit ? 'PUT' : 'POST';

                // Make API call
                fetch(url, {
                        method: method,
                        credentials: 'same-origin', // Include session cookies
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            if (isEdit) {
                                showToast(`✅ Main Head "${data.name}" has been updated successfully!`, 'success');
                            } else {
                                showToast(`✅ New Main Head "${data.name}" has been created successfully!`, 'success');
                            }
                            mainHeadForm.reset();
                            // Focus back to first field for quick data entry
                            setTimeout(() => {
                                try {
                                    const first = document.getElementById('mainHeadName');
                                    if (first) {
                                        first.focus();
                                        if (first.select) first.select();
                                    }
                                } catch (_) {}
                            }, 50);

                            // Reset form to create mode
                            submitText.textContent = 'Create';
                            submitIcon.textContent = 'add';
                            submitBtn.removeAttribute('data-edit-id');

                            // Reload table
                            loadMainHeads();
                        } else {
                            if (isEdit) {
                                showToast(`❌ Failed to update Main Head "${data.name}". ${result.error || 'Please try again.'}`, 'error');
                            } else {
                                showToast(`❌ Failed to create new Main Head. ${result.error || 'Please try again.'}`, 'error');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitText.textContent = isEdit ? 'Update' : 'Create';
                        submitIcon.textContent = isEdit ? 'save' : 'add';
                    });
            });

            // Reset button
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    mainHeadForm.reset();
                    submitText.textContent = 'Create';
                    submitIcon.textContent = 'add';
                    submitBtn.removeAttribute('data-edit-id');
                    showToast(`🔄 Form has been reset successfully`, 'info');
                });
            }

            // Search functionality
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentSearch = this.value;
                        currentPage = 1; // Reset to first page
                        loadMainHeads();
                    }, 500); // Debounce search
                });
            }

            // Filter functionality
            if (filterDropdownBtn && filterDropdown) {
                filterDropdownBtn.addEventListener('click', function() {
                    filterDropdown.classList.toggle('hidden');
                });
            }

            // Close dropdown when clicking outside
            if (filterDropdownBtn && filterDropdown) {
                document.addEventListener('click', function(e) {
                    if (!filterDropdownBtn.contains(e.target) && !filterDropdown.contains(e.target)) {
                        filterDropdown.classList.add('hidden');
                    }
                });
            }

            // Filter option clicks
            if (filterDropdown) {
                document.querySelectorAll('.filter-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const filter = this.getAttribute('data-filter');
                        currentFilter = filter;
                        currentPage = 1; // Reset to first page

                        if (filterDropdownBtn) {
                            // Update button text
                            filterDropdownBtn.querySelector('span').textContent = this.textContent;
                        }

                        // Hide dropdown
                        filterDropdown.classList.add('hidden');

                        loadMainHeads();
                    });
                });
            }

            // Modal event handlers
            const editModal = document.getElementById('editMainHeadModal');
            const closeEditModal = document.getElementById('closeEditModal');
            const updateMainHeadBtn = document.getElementById('updateMainHeadBtn');
            const editMainHeadForm = document.getElementById('editMainHeadForm');

            // Close modal when clicking close button or cancel button
            closeEditModal.addEventListener('click', function() {
                editModal.classList.remove('active');
                editModal.classList.add('opacity-0', 'pointer-events-none');
            });

            // Close modal when clicking outside
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    editModal.classList.remove('active');
                    editModal.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            // Update main head
            updateMainHeadBtn.addEventListener('click', function() {
                const formData = new FormData(editMainHeadForm);
                const id = formData.get('id');
                const data = {
                    name: formData.get('name'),
                    name_in_urdu: formData.get('name_in_urdu'),
                    description: formData.get('description')
                };

                // Validate required fields
                if (!data.name || !data.name_in_urdu) {
                    showToast(`⚠️ Please fill in all required fields (Name and Name in Urdu)`, 'warning');
                    return;
                }

                // Show loading state
                updateMainHeadBtn.disabled = true;
                updateMainHeadBtn.innerHTML = '<i class="ri-loader-4-line mr-[5px] animate-spin"></i>Updating...';

                // Make API call
                fetch(`../main-heads/${id}`, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showToast(`✅ Main Head "${data.name}" has been updated successfully!`, 'success');
                            editModal.classList.remove('active');
                            editModal.classList.add('opacity-0', 'pointer-events-none');
                            loadMainHeads(); // Reload the table
                        } else {
                            showToast(`❌ Failed to update Main Head "${data.name}". ${result.error || 'Please try again.'}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        updateMainHeadBtn.disabled = false;
                        updateMainHeadBtn.innerHTML = '<i class="ri-save-line mr-[5px]"></i>Update';
                    });
            });
        });

        // Control Head functionality
        // Global variables for control head table management
        let controlHeadCurrentPage = 1;
        let controlHeadCurrentLimit = 10;
        let controlHeadCurrentSearch = '';
        let controlHeadCurrentFilter = 'all';
        let controlHeadsData = [];
        let controlHeadMainHeadsData = [];

        // Load main heads for select box
        function loadMainHeadsForSelect() {
            fetch('../api/control-heads/main-heads?type=item&status=I', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        controlHeadMainHeadsData = Array.isArray(result.data) ? result.data : (result.data && Array.isArray(result.data.records) ? result.data.records : []);
                        const select = document.getElementById('controlHeadMainHead');
                        select.innerHTML = '<option value=""></option>';

                        (controlHeadMainHeadsData || []).forEach(mainHead => {
                            const option = document.createElement('option');
                            option.value = mainHead.id;
                            option.textContent = `[${mainHead.id}]-${mainHead.name}`;
                            select.appendChild(option);
                        });
                    } else {
                        showToast('Failed to load main heads', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error loading main heads', 'error');
                });
        }

        // Load control heads data
        function loadControlHeads() {
            const tableBody = document.getElementById('controlHeadsTableBody');
            const paginationInfo = document.getElementById('controlHeadPaginationInfo');

            // Show loading state
            const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
            const loadingColspan = canShowActions ? '6' : '5';
            tableBody.innerHTML = `<tr><td colspan="${loadingColspan}" class="text-center py-8">Loading...</td></tr>`;
            paginationInfo.textContent = 'Loading...';

            // Build query parameters
            const params = new URLSearchParams({
                page: controlHeadCurrentPage,
                limit: controlHeadCurrentLimit
            });

            if (controlHeadCurrentSearch) {
                params.append('search', controlHeadCurrentSearch);
            }

            if (controlHeadCurrentFilter !== 'all') {
                params.append('main_head_id', controlHeadCurrentFilter);
            }

            // Fetch data from API
            fetch(`../api/control-heads?${params.toString()}&type=item`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        controlHeadsData = result.data;
                        renderControlHeadTable();
                        renderControlHeadPagination();
                    } else {
                        showToast(result.error || 'Failed to load data', 'error');
                        const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
                        const colspan = canShowActions ? '6' : '5';
                        tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-8 text-red-500">Failed to load data</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error. Please try again.', 'error');
                    const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';
                    const colspan = canShowActions ? '6' : '5';
                    tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-8 text-red-500">Network error</td></tr>`;
                });
        }

        // Render control head table with data
        function renderControlHeadTable() {
            const tableBody = document.getElementById('controlHeadsTableBody');
            const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PM';

            if (!controlHeadsData.records || controlHeadsData.records.length === 0) {
                const colspan = canShowActions ? '6' : '5';
                tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-8 text-gray-500">No data found</td></tr>`;
                return;
            }

            tableBody.innerHTML = controlHeadsData.records.map(item => `
                    <tr>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            #CH-${String(item.id).padStart(3, '0')}
                        </td>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="block font-medium">
                                ${item.main_head_name || '-'}
                            </span>
                        </td>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="block font-medium">
                                ${item.name}
                            </span>
                        </td>
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="block font-medium">
                                ${item.name_in_urdu}
                            </span>
                        </td>
                        <!--<td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            <span class="px-[8px] py-[3px] inline-block ${item.type === 'account' ? 'bg-primary-50 dark:bg-[#15203c] text-primary-500' : 'bg-success-100 dark:bg-[#15203c] text-success-600'} rounded-sm font-medium text-xs">
                                ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                            </span>
                        </td>-->
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                            ${item.description || '-'}
                        </td>
                        ${canShowActions ? `
                        <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] md:ltr:first:pl-[25px] md:rtl:first:pr-[25px] ltr:first:pr-0 rtl:first:pl-0 border-b border-gray-100 dark:border-[#172036]">
                            <div class="flex items-center gap-[9px]">
                                <button type="button" class="text-blue-500" onclick="editControlHead(${item.id})" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>
                                <!--<button type="button" class="text-red-500" onclick="deleteControlHead(${item.id})" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>-->
                            </div>
                        </td>
                        ` : ''}
                       
                    </tr>
                `).join('');
        }

        // Render control head pagination (Recent Orders style)
        function renderControlHeadPagination() {
            const paginationInfo = document.getElementById('controlHeadPaginationInfo');
            const paginationControls = document.getElementById('controlHeadPaginationControls');

            if (!controlHeadsData.total) {
                paginationInfo.textContent = 'No entries';
                paginationControls.innerHTML = '';
                return;
            }

            const total = parseInt(controlHeadsData.total, 10) || 0;
            const limit = parseInt(controlHeadsData.limit, 10) || 10;
            const page = parseInt(controlHeadsData.page, 10) || 1;
            const totalPages = parseInt(controlHeadsData.total_pages, 10) || 1;
            const start = (page - 1) * limit + 1;
            const end = Math.min(start + limit - 1, total);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${total} entries`;

            const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
            const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

            let html = '';
            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page - 1}\" class=\"${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_left<\/i>\n  <\/a>\n<\/li>`;

            const maxButtons = 5;
            let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
            let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);
            startBtn = Math.max(1, Math.min(startBtn, endBtn - maxButtons + 1));
            for (let p = startBtn; p <= endBtn; p++) {
                const cls = p === page ? activeCls : btnCls;
                html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${p}\" class=\"${cls}\">${p}<\/a>\n<\/li>`;
            }
            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page + 1}\" class=\"${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_right<\/i>\n  <\/a>\n<\/li>`;

            paginationControls.innerHTML = html;
            paginationControls.querySelectorAll('a[data-page]').forEach(a => {
                const targetPage = parseInt(a.getAttribute('data-page'), 10);
                a.onclick = () => {
                    if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== page) changeControlHeadPage(targetPage);
                };
            });
        }

        // Change control head page
        function changeControlHeadPage(page) {
            controlHeadCurrentPage = page;
            loadControlHeads();
        }

        // View control head
        function viewControlHead(id) {
            showToast(`Viewing control head with ID: ${id}`, 'info');
        }

        // Edit control head
        function editControlHead(id) {
            const item = controlHeadsData.records.find(record => record.id == id);
            if (!item) {
                showToast(`❌ Control Head not found. Please refresh the page and try again.`, 'error');
                return;
            }

            // Show the modal first
            const modal = document.getElementById('editControlHeadModal');
            modal.classList.add('active');
            modal.classList.remove('opacity-0', 'pointer-events-none');

            // Enhance selects in modal
            enhanceModalSelects('editControlHeadModal');

            // Populate the modal form with existing data
            document.getElementById('editControlHeadId').value = item.id;
            // Set enhanced select value properly
            setEnhancedSelectValue('editControlHeadMainHead', item.main_head_id);
            document.getElementById('editControlHeadName').value = item.name;
            document.getElementById('editControlHeadNameUrdu').value = item.name_in_urdu;
            document.getElementById('editControlHeadDescription').value = item.description || '';
        }

        // Delete control head with SweetAlert
        function deleteControlHead(id) {
            const item = controlHeadsData.records.find(record => record.id == id);
            const controlHeadName = item ? item.name : 'this control head';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${controlHeadName}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`../api/control-heads/${id}`, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (!result.success) {
                                throw new Error(result.error || 'Failed to delete control head');
                            }
                            return result;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Control Head has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    showToast(`🗑️ Control Head "${controlHeadName}" has been deleted successfully!`, 'success');
                    loadControlHeads();
                }
            });
        }

        // Control Head Form Handling
        document.addEventListener('DOMContentLoaded', function() {
            const controlHeadForm = document.getElementById('controlHeadForm');
            const submitControlHeadBtn = document.getElementById('submitControlHeadBtn');
            const submitControlHeadText = document.getElementById('submitControlHeadText');
            const submitControlHeadIcon = document.getElementById('submitControlHeadIcon');
            const resetControlHeadBtn = document.getElementById('resetControlHeadBtn');
            const searchControlHeadInput = document.getElementById('searchControlHeadInput');
            const filterControlHeadDropdownBtn = document.getElementById('filterControlHeadDropdownBtn');
            const filterControlHeadDropdown = document.getElementById('filterControlHeadDropdown');

            // Load initial data
            loadMainHeadsForSelect();
            loadControlHeads();

            // Add tab click event handler for Control Head tab
            const controlHeadTab = document.querySelector('[data-tab="tab2"]');
            if (controlHeadTab) {
                controlHeadTab.addEventListener('click', function() {
                    // Load data when Control Head tab is clicked
                    loadMainHeadsForSelect();
                    loadControlHeads();
                });
            }

            // Load main heads for modal select box
            function loadMainHeadsForModal() {
                fetch('../api/control-heads/main-heads?type=item&status=I', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const select = document.getElementById('editControlHeadMainHead');
                            select.innerHTML = '<option value=""></option>';
                            const list = Array.isArray(result.data) ? result.data : (result.data && Array.isArray(result.data.records) ? result.data.records : []);
                            (list || []).forEach(mainHead => {
                                const option = document.createElement('option');
                                option.value = mainHead.id;
                                option.textContent = `[${mainHead.id}]-${mainHead.name}`;
                                select.appendChild(option);
                            });
                            try {
                                const jq = window.jQuery || window.$;
                                if (jq && jq.fn && jq.fn.select2) {
                                    jq(select).trigger('change.select2');
                                }
                            } catch (e) {}
                            if (typeof queueSelect2Reinit === 'function') {
                                try {
                                    queueSelect2Reinit('editControlHeadMainHead');
                                } catch (e) {}
                            }
                        } else {
                            showToast('Failed to load main heads for modal', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error loading main heads for modal', 'error');
                    });
            }

            // Load main heads for modal
            loadMainHeadsForModal();

            // Form submission
            if (controlHeadForm) controlHeadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(controlHeadForm);
                const data = {
                    name: formData.get('name'),
                    name_in_urdu: formData.get('name_in_urdu'),
                    main_head_id: formData.get('main_head_id'),
                    type: formData.get('type'),
                    description: formData.get('description')
                };

                // Validate required fields
                if (!data.name || !data.name_in_urdu || !data.main_head_id || !data.type) {
                    showToast(`⚠️ Please fill in all required fields (Main Head, Name, and Name in Urdu)`, 'warning');
                    return;
                }

                // Show loading state
                submitControlHeadBtn.disabled = true;
                submitControlHeadText.textContent = 'Creating...';
                submitControlHeadIcon.textContent = 'hourglass_empty';

                // No loading toast to avoid double toasters

                // Make API call
                fetch('../api/control-heads', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showToast(`✅ New Control Head "${data.name}" has been created successfully!`, 'success');
                            controlHeadForm.reset();
                            // Focus back to first field (enhanced select if available)
                            setTimeout(() => {
                                try {
                                    const firstSel = document.getElementById('controlHeadMainHead');
                                    if (firstSel) {
                                        if (firstSel.__enhanced && firstSel.__enhanced.control) firstSel.__enhanced.control.focus();
                                        else firstSel.focus();
                                    }
                                } catch (_) {}
                            }, 50);

                            // Reload table
                            loadControlHeads();
                        } else {
                            showToast(`❌ Failed to create new Control Head. ${result.error || 'Please try again.'}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        submitControlHeadBtn.disabled = false;
                        submitControlHeadText.textContent = 'Create';
                        submitControlHeadIcon.textContent = 'add';
                    });
            });

            // Reset button
            if (resetControlHeadBtn) {
                resetControlHeadBtn.addEventListener('click', function() {
                    controlHeadForm.reset();
                    showToast(`🔄 Control Head form has been reset successfully`, 'info');
                });
            }

            // Search functionality
            let searchControlHeadTimeout;
            if (searchControlHeadInput) {
                searchControlHeadInput.addEventListener('input', function() {
                    clearTimeout(searchControlHeadTimeout);
                    searchControlHeadTimeout = setTimeout(() => {
                        controlHeadCurrentSearch = this.value;
                        controlHeadCurrentPage = 1; // Reset to first page
                        loadControlHeads();
                    }, 500); // Debounce search
                });
            }

            // Filter functionality
            if (filterControlHeadDropdownBtn && filterControlHeadDropdown) {
                filterControlHeadDropdownBtn.addEventListener('click', function() {
                    filterControlHeadDropdown.classList.toggle('hidden');
                });
            }

            // Filter option clicks
            if (filterControlHeadDropdown) {
                document.querySelectorAll('.filter-control-head-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const filter = this.getAttribute('data-filter');
                        controlHeadCurrentFilter = filter;
                        controlHeadCurrentPage = 1; // Reset to first page

                        if (filterControlHeadDropdownBtn) {
                            // Update button text
                            filterControlHeadDropdownBtn.querySelector('span').textContent = this.textContent;
                        }

                        // Hide dropdown
                        filterControlHeadDropdown.classList.add('hidden');

                        loadControlHeads();
                    });
                });
            }

            // Control Head Modal event handlers
            const editControlHeadModal = document.getElementById('editControlHeadModal');
            const closeEditControlHeadModal = document.getElementById('closeEditControlHeadModal');
            const updateControlHeadBtn = document.getElementById('updateControlHeadBtn');
            const editControlHeadForm = document.getElementById('editControlHeadForm');

            // Close modal when clicking close button or cancel button
            closeEditControlHeadModal.addEventListener('click', function() {
                editControlHeadModal.classList.remove('active');
                editControlHeadModal.classList.add('opacity-0', 'pointer-events-none');
            });

            // Close modal when clicking outside
            editControlHeadModal.addEventListener('click', function(e) {
                if (e.target === editControlHeadModal) {
                    editControlHeadModal.classList.remove('active');
                    editControlHeadModal.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            // Update control head
            updateControlHeadBtn.addEventListener('click', function() {
                const formData = new FormData(editControlHeadForm);
                const id = formData.get('id');
                const data = {
                    name: formData.get('name'),
                    name_in_urdu: formData.get('name_in_urdu'),
                    main_head_id: formData.get('main_head_id'),
                    type: formData.get('type'),
                    description: formData.get('description')
                };

                // Validate required fields
                if (!data.name || !data.name_in_urdu || !data.main_head_id || !data.type) {
                    showToast(`⚠️ Please fill in all required fields (Main Head, Name, and Name in Urdu)`, 'warning');
                    return;
                }

                // Show loading state
                updateControlHeadBtn.disabled = true;
                updateControlHeadBtn.innerHTML = '<i class="ri-loader-4-line mr-[5px] animate-spin"></i>Updating...';

                // No loading toast to avoid double toasters

                // Make API call
                fetch(`../api/control-heads/${id}`, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showToast(`✅ Control Head "${data.name}" has been updated successfully!`, 'success');
                            editControlHeadModal.classList.remove('active');
                            editControlHeadModal.classList.add('opacity-0', 'pointer-events-none');
                            loadControlHeads(); // Reload the table
                        } else {
                            showToast(`❌ Failed to update Control Head "${data.name}". ${result.error || 'Please try again.'}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        updateControlHeadBtn.disabled = false;
                        updateControlHeadBtn.innerHTML = '<i class="ri-save-line mr-[5px]"></i>Update';
                    });
            });
        });

        // Global variables for account table management
        let accountCurrentPage = 1;
        let accountCurrentLimit = 10;
        let accountCurrentSearch = '';
        let accountCurrentFilter = 'all';
        let accountsData = [];
        let accountMainHeadsData = [];
        let accountControlHeadsData = [];

        // Load main heads for account form
        function loadMainHeadsForAccount() {
            fetch('../api/items/main-heads', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        accountMainHeadsData = Array.isArray(result.data) ? result.data : (result.data?.records || []);
                        const select = document.getElementById('accountMainHead');
                        select.innerHTML = '<option value=""></option>';
                        (accountMainHeadsData || []).forEach(item => {
                            select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                        });
                        // Enhance the select if not already enhanced
                        setTimeout(() => {
                            if (!select.__enhanced) {
                                upgradeSelectToSearchable('accountMainHead', 'Search main heads...');
                            }
                        }, 50);
                        // reinit/select2 refresh
                        try {
                            const jq = window.jQuery || window.$;
                            if (jq && jq.fn && jq.fn.select2) {
                                jq(select).trigger('change.select2');
                            }
                            if (select && select.slim && typeof select.slim.setData === 'function') {
                                const data = [{
                                    text: '',
                                    value: ''
                                }].concat((accountMainHeadsData || []).map(r => ({
                                    text: `[${r.id}]-${r.name}`,
                                    value: String(r.id)
                                })));
                                select.slim.setData(data);
                            }
                        } catch (e) {}
                        if (typeof queueSelect2Reinit === 'function') {
                            try {
                                queueSelect2Reinit('accountMainHead');
                            } catch (e) {}
                        }
                        // If a main head is already selected, load control heads immediately
                        try {
                            const currentVal = (select.value || '').trim();
                            if (currentVal) loadControlHeadsForAccount(currentVal);
                        } catch (e) {}
                    } else {
                        showToast(`❌ Failed to load main heads. ${result.error || 'Please try again.'}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error. Please try again.', 'error');
                });
        }

        // Load control heads based on selected main head
        function loadControlHeadsForAccount(mainHeadId) {
            const select = document.getElementById('accountControlHead');
            const id = String(mainHeadId || '').trim();
            if (!id) {
                if (select) select.innerHTML = '<option value=""></option>';
                return;
            }

            const toArray = (res) => {
                if (!res || !res.data) return [];
                return Array.isArray(res.data.records) ? res.data.records : (Array.isArray(res.data) ? res.data : []);
            };

            const fillOptions = (list) => {
                if (!select) return;
                select.innerHTML = '<option value=""></option>';
                list.forEach(item => {
                    if (item && item.id) select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                });
                // Enhance the select if not already enhanced
                setTimeout(() => {
                    if (!select.__enhanced) {
                        upgradeSelectToSearchable('accountControlHead', 'Search control heads...');
                    }
                }, 50);
                try {
                    const jq = window.jQuery || window.$;
                    if (jq && jq.fn && jq.fn.select2) {
                        jq(select).trigger('change.select2');
                    }
                    if (select.slim && typeof select.slim.setData === 'function') {
                        const data = [{
                            text: '',
                            value: ''
                        }].concat(list.map(r => ({
                            text: `[${r.id}]-${r.name}`,
                            value: String(r.id)
                        })));
                        select.slim.setData(data);
                    }
                } catch (e) {}
                if (typeof queueSelect2Reinit === 'function') {
                    try {
                        queueSelect2Reinit('accountControlHead');
                    } catch (e) {}
                }
            };

            // Try items endpoint first
            fetch(`../api/items/control-heads?main_head_id=${encodeURIComponent(id)}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => {
                    if (!r.ok) {
                        throw new Error(`HTTP error! status: ${r.status}`);
                    }
                    return r.json();
                })
                .then(res => {
                    let list = toArray(res);
                    if ((!list || list.length === 0)) {
                        // Fallback to control-heads endpoint (paginated)
                        return fetch(`../api/control-heads?type=item&status=I&limit=1000&main_head_id=${encodeURIComponent(id)}`, {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(rr => {
                                if (!rr.ok) {
                                    throw new Error(`HTTP error! status: ${rr.status}`);
                                }
                                return rr.json();
                            })
                            .then(rr => {
                                fillOptions(toArray(rr));
                            })
                            .catch(err => {
                                console.error('Error loading control heads from fallback endpoint:', err);
                                showToast('Failed to load control heads', 'error');
                            });
                    }
                    fillOptions(list);
                })
                .catch(err => {
                    console.error('Error loading control heads:', err);
                    showToast('Failed to load control heads', 'error');
                });
        }

        // Load accounts
        function loadAccounts() {
            const params = new URLSearchParams({
                page: accountCurrentPage,
                limit: accountCurrentLimit,
                search: accountCurrentSearch,
                filter: accountCurrentFilter,
                sort_by: 'id',
                sort_order: 'desc'
            });

            // Add unit_id parameter if available
            const unitId = document.getElementById('unitId')?.value || 
                          document.querySelector('input[name="unit_id"]')?.value ||
                          window.currentUnitId;
            if (unitId) {
                params.append('unit_id', unitId);
            }

            fetch(`../api/items?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        accountsData = result.data;
                        // Always ensure results are sorted by ID in descending order (5, 4, 3, 2, 1...)
                        if (accountsData.records && accountsData.records.length > 0) {
                            accountsData.records.sort((a, b) => parseInt(b.id) - parseInt(a.id));
                            console.log('Data sorted by ID:', accountsData.records.map(r => r.id));
                        }
                        renderAccountTable();
                        renderAccountPagination();
                    } else {
                        showToast(`❌ Failed to load accounts. ${result.error || 'Please try again.'}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error. Please try again.', 'error');
                });
        }

        // Enhanced search function that searches by source ID or name
        function performSearch(searchTerm) {
            if (!searchTerm || searchTerm.trim() === '') {
                accountCurrentSearch = '';
                accountCurrentPage = 1;
                loadAccounts();
                return;
            }

            // Check if search term is a number (ID search)
            const isIdSearch = /^\d+$/.test(searchTerm.trim());

            // Update search parameters
            accountCurrentSearch = searchTerm.trim();
            accountCurrentPage = 1;

            // Always ensure proper sorting for search results
            const params = new URLSearchParams({
                page: accountCurrentPage,
                limit: accountCurrentLimit,
                search: accountCurrentSearch,
                filter: accountCurrentFilter,
                sort_by: 'id',
                sort_order: 'desc'
            });

            // If searching by source ID, add specific parameter
            if (isIdSearch) {
                params.append('search_by', 'source_id');
            }

            // Add unit_id parameter if available
            const unitId = document.getElementById('unitId')?.value || 
                          document.querySelector('input[name="unit_id"]')?.value ||
                          window.currentUnitId;
            if (unitId) {
                params.append('unit_id', unitId);
            }

            fetch(`../api/items?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        accountsData = result.data;
                        // Always ensure search results are sorted by ID in descending order (5, 4, 3, 2, 1...)
                        if (accountsData.records && accountsData.records.length > 0) {
                            accountsData.records.sort((a, b) => parseInt(b.id) - parseInt(a.id));
                            console.log('Search results sorted by ID:', accountsData.records.map(r => r.id));
                        }
                        renderAccountTable();
                        renderAccountPagination();
                    } else {
                        showToast(`❌ Search failed. ${result.error || 'Please try again.'}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showToast('Search error. Please try again.', 'error');
                });
        }

        // Export items to Excel (ERP Structure)
        async function exportItemsToExcel() {
            const exportBtn = document.getElementById('exportErpItemsToExcelBtn');
            if (!exportBtn) return;

            // Show loading state
            const originalContent = exportBtn.innerHTML;
            exportBtn.disabled = true;
            exportBtn.innerHTML = '<i class="material-symbols-outlined text-sm animate-spin">sync</i><span>Exporting...</span>';

            try {
                // Load XLSX library if not already loaded
                if (typeof XLSX === 'undefined') {
                    await loadSheetJSLibrary();
                }

                // First, get the total count to determine how many pages we need
                const baseParams = new URLSearchParams({
                    page: 1,
                    limit: 1000, // Use a reasonable page size
                    status: 'I',
                    include_attributes: '1' // Include attribute values in response for export
                });

                // Add unit_id parameter if available
                const unitId = document.getElementById('unitId')?.value || 
                              document.querySelector('input[name="unit_id"]')?.value ||
                              window.currentUnitId;
                if (unitId) {
                    baseParams.append('unit_id', unitId);
                }

                // Fetch first page to get total count
                const firstResponse = await fetch(`../api/items?${baseParams.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const firstResult = await firstResponse.json();

                // Debug: Log the API response structure
                console.log('API Response:', firstResult);

                if (!firstResult.success) {
                    throw new Error(firstResult.error || 'Failed to load items data');
                }

                // Handle different possible response structures
                const data = firstResult.data || firstResult;
                const records = data.records || data.items || data || [];
                const totalRecords = data.total || records.length || 0;
                const limit = 1000; // Items per page
                const totalPages = Math.ceil(totalRecords / limit);

                console.log('Export Debug:', {
                    totalRecords,
                    recordsInFirstPage: records.length,
                    totalPages,
                    responseStructure: Object.keys(data)
                });

                // If no records in first page and total is 0, show error
                if (totalRecords === 0 && records.length === 0) {
                    showToast('No items found to export', 'warning');
                    exportBtn.innerHTML = originalContent;
                    exportBtn.disabled = false;
                    return;
                }

                // Update button to show progress
                exportBtn.innerHTML = `<i class="material-symbols-outlined text-sm animate-spin">sync</i><span>Fetching ${totalRecords} items...</span>`;

                // Collect all items from all pages
                let allItems = [...records];

                // Fetch remaining pages
                for (let page = 2; page <= totalPages; page++) {
                    const pageParams = new URLSearchParams({
                        page: page,
                        limit: limit,
                        status: 'I',
                        include_attributes: '1' // Include attribute values in response for export
                    });

                    if (unitId) {
                        pageParams.append('unit_id', unitId);
                    }

                    const pageResponse = await fetch(`../api/items?${pageParams.toString()}`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });

                    const pageResult = await pageResponse.json();

                    if (pageResult.success) {
                        // Handle different possible response structures
                        const pageData = pageResult.data || pageResult;
                        const pageRecords = pageData.records || pageData.items || pageData || [];
                        
                        if (pageRecords.length > 0) {
                            allItems = allItems.concat(pageRecords);
                            // Update progress
                            const progress = Math.min(Math.round((allItems.length / totalRecords) * 100), 100);
                            exportBtn.innerHTML = `<i class="material-symbols-outlined text-sm animate-spin">sync</i><span>Fetching... ${progress}% (${allItems.length}/${totalRecords})</span>`;
                        }
                    }
                }
                
                // The API already filters by status='I' and is_deleted=0 in the query
                const filteredItems = allItems.filter(item => {
                    if (!item) return false;
                    const statusValue = String(item.status || '').trim().toUpperCase();
                    const statusMatch = statusValue === 'I' || statusValue === '';
                    const isDeletedValue = item.is_deleted;
                    const notDeleted = (
                        isDeletedValue === 0 || 
                        isDeletedValue === '0' || 
                        String(isDeletedValue || '').trim() === '0' ||
                        isDeletedValue === false ||
                        isDeletedValue === null ||
                        isDeletedValue === undefined ||
                        isDeletedValue === ''
                    );
                    return (statusMatch || !item.status) && notDeleted;
                });
                
                if (filteredItems.length === 0) {
                    showToast('No items found to export', 'warning');
                    exportBtn.innerHTML = originalContent;
                    exportBtn.disabled = false;
                    return;
                }

                // Process attribute values from API response (already included via include_attributes=1)
                exportBtn.innerHTML = `<i class="material-symbols-outlined text-sm animate-spin">sync</i><span>Processing attributes...</span>`;
                
                // Collect all unique attribute names across all items
                const allAttributeNames = new Set();
                const itemsWithAttributes = [];
                
                // Process attribute values that are already included in the response
                for (const item of filteredItems) {
                    let itemData = { ...item, attributeValues: {} };
                    
                    // Attribute values are now included in the API response
                    if (item.attribute_values && Array.isArray(item.attribute_values)) {
                        item.attribute_values.forEach(av => {
                            const attrName = av.attribute_name;
                            allAttributeNames.add(attrName);
                            itemData.attributeValues[attrName] = av.attribute_value || '';
                        });
                    }
                    
                    itemsWithAttributes.push(itemData);
                }

                // Update button to show generating message
                exportBtn.innerHTML = `<i class="material-symbols-outlined text-sm animate-spin">sync</i><span>Generating Excel...</span>`;

                // Convert attribute names set to sorted array
                const attributeColumns = Array.from(allAttributeNames).sort();

                // Convert to Excel format with ERP structure
                const excelData = convertErpItemsToExcelFormat(itemsWithAttributes, attributeColumns);

                // Generate and download Excel file
                generateErpItemsExcelFile(excelData, attributeColumns, 'ERP_Items_Export_' + new Date().toISOString().split('T')[0] + '.xlsx');

                showToast(`Excel exported successfully with ${filteredItems.length} records`, 'success');
                
            } catch (error) {
                console.error('Error exporting to Excel:', error);
                showToast('Error exporting to Excel: ' + error.message, 'error');
            } finally {
                exportBtn.innerHTML = originalContent;
                exportBtn.disabled = false;
            }
        }

        // Convert ERP items data to Excel format
        function convertErpItemsToExcelFormat(items, attributeColumns) {
            const excelData = [];

            // Fixed columns for ERP structure
            const fixedHeaders = [
                'SKU/Code',
                'Legacy Code',
                'Item Name',
                'Item Name (Urdu)',
                'Category',
                'Group',
                'Sub-Group',
                'Unit Type',
                'Weight (kg)',
                'Min Order Level',
                'Max Order Level',
                'Rack',
                'Description'
            ];

            // Add dynamic attribute columns
            const headers = [...fixedHeaders, ...attributeColumns];
            excelData.push(headers);

            // Add data rows
            items.forEach(item => {
                const row = [
                    item.normalized_sku || item.source_id || '',
                    item.legacy_code || '',
                    item.name || '',
                    item.name_in_urdu || '',
                    item.category_name || '',
                    item.group_name || '',
                    item.sub_group_name || '',
                    item.unit_type_name || '',
                    item.calculated_weight || '',
                    item.min_order_level || '',
                    item.max_order_level || '',
                    item.rack_name || '',
                    item.description || ''
                ];

                // Add attribute values in order
                attributeColumns.forEach(attrName => {
                    row.push(item.attributeValues[attrName] || '');
                });

                excelData.push(row);
            });

            return excelData;
        }

        // Generate Excel file for ERP items
        function generateErpItemsExcelFile(excelData, attributeColumns, filename) {
            try {
                if (typeof XLSX === 'undefined') {
                    throw new Error('XLSX library is not available');
                }

                // Create workbook
                const wb = XLSX.utils.book_new();

                // Create worksheet
                const ws = XLSX.utils.aoa_to_sheet(excelData);

                // Set column widths
                const fixedColWidths = [
                    { wch: 18 }, // SKU/Code
                    { wch: 15 }, // Legacy Code
                    { wch: 35 }, // Item Name
                    { wch: 30 }, // Item Name (Urdu)
                    { wch: 18 }, // Category
                    { wch: 18 }, // Group
                    { wch: 18 }, // Sub-Group
                    { wch: 12 }, // Unit Type
                    { wch: 12 }, // Weight (kg)
                    { wch: 14 }, // Min Order Level
                    { wch: 14 }, // Max Order Level
                    { wch: 15 }, // Rack
                    { wch: 30 }  // Description
                ];
                
                // Add widths for attribute columns
                const attrColWidths = attributeColumns.map(() => ({ wch: 20 }));
                ws['!cols'] = [...fixedColWidths, ...attrColWidths];

                // Format header row
                const headerRange = XLSX.utils.decode_range(ws['!ref']);
                for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                    if (!ws[cellAddress]) ws[cellAddress] = { v: '' };
                    ws[cellAddress].s = {
                        font: { bold: true },
                        fill: { fgColor: { rgb: "2196F3" } },
                        alignment: { horizontal: "center", vertical: "center" }
                    };
                }

                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, 'ERP Items');

                // Download file
                XLSX.writeFile(wb, filename);

            } catch (error) {
                console.error('Error generating Excel file:', error);
                throw error;
            }
        }

        // Generate Excel file using SheetJS
        function generateItemsExcelFile(excelData, filename) {
            try {
                if (typeof XLSX === 'undefined') {
                    throw new Error('XLSX library is not available');
                }

                // Create workbook
                const wb = XLSX.utils.book_new();

                // Create worksheet
                const ws = XLSX.utils.aoa_to_sheet(excelData);

                // Set column widths
                ws['!cols'] = [
                    { wch: 12 }, // Code
                    { wch: 25 }, // Main Head Name
                    { wch: 25 }, // Control Head Name
                    { wch: 30 }, // Item Name
                    { wch: 30 }, // Item Name (Urdu)
                    { wch: 15 }, // Unit Type
                    { wch: 18 }  // Calculated Weight
                ];

                // Format header row
                const headerRange = XLSX.utils.decode_range(ws['!ref']);
                for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                    if (!ws[cellAddress]) ws[cellAddress] = { v: '' };
                    ws[cellAddress].s = {
                        font: { bold: true },
                        fill: { fgColor: { rgb: "2196F3" } },
                        alignment: { horizontal: "center", vertical: "center" }
                    };
                }

                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, 'Items');

                // Download file
                XLSX.writeFile(wb, filename);

            } catch (error) {
                console.error('Error generating Excel file:', error);
                throw error;
            }
        }

        // Load SheetJS library dynamically
        function loadSheetJSLibrary() {
            return new Promise((resolve, reject) => {
                // Check if already loaded
                if (typeof XLSX !== 'undefined') {
                    resolve();
                    return;
                }

                // Try multiple CDN sources
                const cdnSources = [
                    'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
                    'https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js',
                    'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'
                ];

                let currentSourceIndex = 0;

                const tryLoadFromSource = () => {
                    if (currentSourceIndex >= cdnSources.length) {
                        reject(new Error('Failed to load SheetJS library from all sources'));
                        return;
                    }

                    const timeout = setTimeout(() => {
                        currentSourceIndex++;
                        tryLoadFromSource();
                    }, 8000);

                    const script = document.createElement('script');
                    script.src = cdnSources[currentSourceIndex];
                    script.async = true;
                    script.crossOrigin = 'anonymous';

                    script.onload = () => {
                        clearTimeout(timeout);
                        if (typeof XLSX !== 'undefined' && XLSX.utils) {
                            resolve();
                        } else {
                            currentSourceIndex++;
                            tryLoadFromSource();
                        }
                    };

                    script.onerror = () => {
                        clearTimeout(timeout);
                        currentSourceIndex++;
                        tryLoadFromSource();
                    };

                    document.head.appendChild(script);
                };

                tryLoadFromSource();
            });
        }

        // Render account table
        function renderAccountTable() {
            const tbody = document.getElementById('accountsTableBody');
            const canShowActions = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'SA' || window.defaultRoleStatus === 'PA' || window.defaultRoleStatus === 'PM';
            const canShowEdit = window.defaultRoleStatus === 'SUA' || window.defaultRoleStatus === 'A' || window.defaultRoleStatus === 'PA' || window.defaultRoleStatus === 'PM' || window.currentUserId == 89;
            tbody.innerHTML = '';

            if (accountsData.records && accountsData.records.length > 0) {
                accountsData.records.forEach(item => {
                    tbody.innerHTML += `
                            <tr>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.source_id}
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.main_head_name || '-'}<br />${item.control_head_name || '-'}
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.name}<br /><span class="text-right">${item.name_in_urdu}</span>
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.rack_name || '-'}
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.unit_type_name || '-'}
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.description || '-'}
                                </td>
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                                    ${item.stock_limit || '-'}
                                </td>
                                ${canShowActions ? `
                                <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] md:ltr:first:pl-[25px] md:rtl:first:pr-[25px] ltr:first:pr-0 rtl:first:pl-0 border-b border-gray-100 dark:border-[#172036]">
                                    <div class="flex items-center gap-[9px]">
                                       ${canShowEdit ? `<button type="button" class="text-blue-500" onclick="editAccount(${item.id})" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>` : ''}
                                        <!--<button type="button" class="text-red-500" onclick="deleteAccount(${item.id})" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>-->
                                    </div>
                                </td>
                                ` : ''}
                               
                            </tr>
                        `;
                });
            } else {
                const colspan = canShowActions ? '8' : '7';
                tbody.innerHTML = `
                        <tr>
                            <td colspan="${colspan}" class="text-center py-[20px] text-gray-500 dark:text-gray-400">
                                No accounts found
                            </td>
                        </tr>
                    `;
            }
        }

        // Render account pagination
        function renderAccountPagination() {
            const info = document.getElementById('accountPaginationInfo');
            const controls = document.getElementById('accountPaginationControls');

            if (!(accountsData && accountsData.total > 0)) {
                info.textContent = 'No accounts found';
                controls.innerHTML = '';
                return;
            }

            const total = parseInt(accountsData.total, 10) || 0;
            const limit = parseInt(accountsData.limit, 10) || 10;
            const page = parseInt(accountsData.page, 10) || 1;
            const totalPages = parseInt(accountsData.total_pages, 10) || 1;
            const start = (page - 1) * limit + 1;
            const end = Math.min(page * limit, total);
            info.textContent = `Showing ${start} to ${end} of ${total} Items`;

            const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
            const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

            let html = '';
            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page - 1}\" class=\"${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_left<\/i>\n  <\/a>\n<\/li>`;

            const maxButtons = 5;
            let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
            let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);
            startBtn = Math.max(1, Math.min(startBtn, endBtn - maxButtons + 1));
            for (let p = startBtn; p <= endBtn; p++) {
                const cls = p === page ? activeCls : btnCls;
                html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${p}\" class=\"${cls}\">${p}<\/a>\n<\/li>`;
            }

            html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${page + 1}\" class=\"${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0<\/span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_right<\/i>\n  <\/a>\n<\/li>`;

            controls.innerHTML = html;
            controls.querySelectorAll('a[data-page]').forEach(a => {
                const targetPage = parseInt(a.getAttribute('data-page'), 10);
                a.onclick = () => {
                    if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== page) changeAccountPage(targetPage);
                };
            });
        }

        // Change account page
        function changeAccountPage(page) {
            accountCurrentPage = page;
            loadAccounts();
        }

        // View item (placeholder)
        function viewAccount(id) {
            // No toasts/alerts requested
        }

        // Store original item data for edit modal (accessible to update handlers)
        let currentEditingItem = null;

        // Setup main head change listener for edit modal
        function setupEditMainHeadChangeListener() {
            const editAccountMainHead = document.getElementById('editAccountMainHead');
            if (!editAccountMainHead) return;
            
            // Check if listener is already attached
            if (editAccountMainHead.hasAttribute('data-main-head-listener')) {
                return; // Already set up
            }
            
            // Mark as having listener attached
            editAccountMainHead.setAttribute('data-main-head-listener', 'true');
            
            // Listen to native change event
            editAccountMainHead.addEventListener('change', function() {
                handleMainHeadChange(this.value);
            });
            
            // Also listen to enhanced select if available
            setTimeout(() => {
                if (editAccountMainHead.__enhanced && editAccountMainHead.__enhanced.list) {
                    const listElement = editAccountMainHead.__enhanced.list;
                    listElement.addEventListener('click', function(e) {
                        const option = e.target.closest('[data-value]');
                        if (option) {
                            const value = option.getAttribute('data-value');
                            if (editAccountMainHead.value !== value) {
                                editAccountMainHead.value = value;
                                editAccountMainHead.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }
                    });
                }
            }, 300);
        }
        
        // Handle main head change
        function handleMainHeadChange(mainHeadId) {
            const controlHeadSelect = document.getElementById('editAccountControlHead');
            if (!controlHeadSelect) return;
            
            // Clear control head options and value
            controlHeadSelect.innerHTML = '<option value=""></option>';
            controlHeadSelect.value = '';
            
            // Clear enhanced select display
            if (controlHeadSelect.__enhanced && typeof controlHeadSelect.__enhanced.setDisplayFromValue === 'function') {
                controlHeadSelect.__enhanced.setDisplayFromValue();
            }
            
            // Load control heads for the selected main head
            if (mainHeadId) {
                loadControlHeadsForModal(mainHeadId).then(() => {
                    // Refresh the enhanced select after options are loaded
                    setTimeout(() => {
                        if (controlHeadSelect.__enhanced && typeof controlHeadSelect.__enhanced.refresh === 'function') {
                            controlHeadSelect.__enhanced.refresh();
                        }
                        enhanceModalSelects('editAccountModal');
                    }, 100);
                });
            } else {
                // If no main head selected, just refresh the empty select
                setTimeout(() => {
                    if (controlHeadSelect.__enhanced && typeof controlHeadSelect.__enhanced.refresh === 'function') {
                        controlHeadSelect.__enhanced.refresh();
                    }
                    enhanceModalSelects('editAccountModal');
                }, 100);
            }
        }
        
        // Edit account - NEW CLEAN IMPLEMENTATION
        function editAccount(id) {
            const item = accountsData.records.find(record => record.id == id);
            if (!item) {
                showToast('Item not found', 'error');
                return;
            }

            // Store original item data for use in update handlers
            currentEditingItem = {
                id: item.id,
                name: item.name || '',
                name_in_urdu: item.name_in_urdu || '',
                main_head_id: item.main_head_id || null,
                control_head_id: item.control_head_id || null,
                unit_type_id: item.unit_type_id || null
            };

            // Show modal
            const modal = document.getElementById('editAccountModal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('active');
            
            // Setup main head change listener when modal opens (in case it wasn't set up before)
            setupEditMainHeadChangeListener();
            
            // Enable all fields for all users
            const mainHeadField = document.getElementById('editAccountMainHead');
            const controlHeadField = document.getElementById('editAccountControlHead');
            const nameField = document.getElementById('editAccountName');
            const nameUrduField = document.getElementById('editAccountNameUrdu');
            const unitTypeField = document.getElementById('editAccountUnitType');
            
            // Always enable all fields
            if (mainHeadField) {
                mainHeadField.disabled = false;
                mainHeadField.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            if (controlHeadField) {
                controlHeadField.disabled = false;
                controlHeadField.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            if (nameField) {
                nameField.disabled = false;
                nameField.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            if (nameUrduField) {
                nameUrduField.disabled = false;
                nameUrduField.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            if (unitTypeField) {
                unitTypeField.disabled = false;
                unitTypeField.classList.remove('opacity-60', 'cursor-not-allowed');
            }

            // Force enable modal and buttons
            modal.style.pointerEvents = 'auto';
            modal.style.opacity = '1';
            
            // Ensure buttons are enabled when modal opens
            setTimeout(() => {
                const closeBtn = document.getElementById('closeEditAccountModal');
                const updateBtn = document.getElementById('updateAccountBtn');
                
                if (closeBtn) {
                    closeBtn.style.pointerEvents = 'auto';
                    closeBtn.disabled = false;
                    closeBtn.style.cursor = 'pointer';
                    closeBtn.style.opacity = '1';
                    console.log('editAccount: Ensured close button is enabled');
                }
                
                if (updateBtn) {
                    updateBtn.style.pointerEvents = 'auto';
                    updateBtn.disabled = false;
                    updateBtn.style.cursor = 'pointer';
                    updateBtn.style.opacity = '1';
                    console.log('editAccount: Ensured update button is enabled');
                }
                
                // Also ensure the modal content is clickable
                const modalContent = modal.querySelector('.trezo-card');
                if (modalContent) {
                    modalContent.style.pointerEvents = 'auto';
                }
                
                // Add direct event listeners as fallback
                if (closeBtn) {
                    closeBtn.onclick = function() {
                        modal.classList.remove('active');
                        modal.classList.add('opacity-0', 'pointer-events-none');
                    };
                }
                
                if (updateBtn) {
                    updateBtn.onclick = function() {
                        // Trigger the update functionality
                        const form = document.getElementById('editAccountForm');
                        if (form) {
                            const formData = new FormData(form);
                            const id = formData.get('id');
                            
                            if (!id) {
                                showToast('Item ID not found', 'error');
                                return;
                            }
                            
                            // Get user's unit_id from PHP session
                            const userUnitId = <?php echo json_encode(getCurrentUser()['unit_id'] ?? null); ?>;
                            
                            // Prepare data object - always include all fields
                            const data = {
                                description: formData.get('description'),
                                stock_limit: formData.get('stock_limit'),
                                // Weight calculation fields
                                length: formData.get('length') || null,
                                width: formData.get('width') || null,
                                thickness: formData.get('thickness') || null,
                                diameter: formData.get('diameter') || null,
                                height: formData.get('height') || null,
                                density: formData.get('density') || null,
                                weight_formula: formData.get('weight_formula') || null,
                                calculated_weight: formData.get('calculated_weight') || null
                            };
                            
                            // Clean weight calculation fields - only save if they have values
                            // Handle weight_formula - only save if user entered a formula (no default formula)
                            if (data.weight_formula === null || data.weight_formula === '' || !data.weight_formula.trim()) {
                                delete data.weight_formula;
                            } else {
                                // Trim the formula if it exists
                                data.weight_formula = data.weight_formula.trim();
                            }
                            
                            // Handle dimension fields - only save if user provided values
                            const dimensionFields = ['length', 'width', 'thickness', 'diameter', 'height', 'density'];
                            dimensionFields.forEach(key => {
                                if (data[key] === null || data[key] === '' || data[key] === '0') {
                                    delete data[key];
                                } else {
                                    data[key] = parseFloat(data[key]);
                                }
                            });
                            
                            // Handle calculated_weight separately - always save if it has a value (auto-calculated or manually entered)
                            // This should save regardless of whether formula/parameters exist
                            if (data.calculated_weight !== null && data.calculated_weight !== '' && data.calculated_weight !== '0') {
                                data.calculated_weight = parseFloat(data.calculated_weight);
                            } else {
                                // Only delete if no formula exists (backend will auto-calculate if formula exists)
                                if (!data.weight_formula) {
                                    delete data.calculated_weight;
                                }
                            }
                            
                            // Always include name and name_in_urdu - read directly from input
                            const nameInput = document.getElementById('editAccountName');
                            const nameUrduInput = document.getElementById('editAccountNameUrdu');
                            data.name = (nameInput && nameInput.value) || formData.get('name') || currentEditingItem?.name || '';
                            data.name_in_urdu = (nameUrduInput && nameUrduInput.value) || formData.get('name_in_urdu') || currentEditingItem?.name_in_urdu || '';
                            
                            // Always read unit_type_id from the form
                            const unitTypeSelect = document.getElementById('editAccountUnitType');
                            let unitTypeValue = null;
                            if (unitTypeSelect) {
                                unitTypeValue = unitTypeSelect.value || null;
                                if (unitTypeValue === '' || unitTypeValue === '0') {
                                    unitTypeValue = null;
                                }
                            }
                            data.unit_type_id = unitTypeValue || formData.get('unit_type_id') || currentEditingItem?.unit_type_id || null;
                            if (data.unit_type_id === '' || data.unit_type_id === '0') {
                                data.unit_type_id = null;
                            } else if (data.unit_type_id) {
                                data.unit_type_id = parseInt(data.unit_type_id, 10);
                            }
                            
                            // Always include main_head_id and control_head_id
                            const mainHeadSelect = document.getElementById('editAccountMainHead');
                            const controlHeadSelect = document.getElementById('editAccountControlHead');
                            data.main_head_id = (mainHeadSelect && mainHeadSelect.value) || formData.get('main_head_id') || currentEditingItem?.main_head_id || null;
                            data.control_head_id = (controlHeadSelect && controlHeadSelect.value) || formData.get('control_head_id') || currentEditingItem?.control_head_id || null;
                            
                            // Validate required fields
                            const requiredFields = [];
                            if (!data.name) requiredFields.push('Name');
                            if (!data.name_in_urdu) requiredFields.push('Name in Urdu');
                            if (!data.main_head_id) requiredFields.push('Main Head');
                            if (!data.control_head_id) requiredFields.push('Control Head');
                            if (!data.unit_type_id) requiredFields.push('Unit Type');
                            
                            if (requiredFields.length > 0) {
                                const fieldList = requiredFields.join(', ');
                                showToast(`⚠️ Please fill in all required fields (${fieldList})`, 'warning');
                                return;
                            }
                            
                            // Show loading state
                            updateBtn.disabled = true;
                            updateBtn.innerHTML = '<i class="ri-loader-4-line mr-[5px] animate-spin"></i>Updating...';
                            
                            // Make API call
                            fetch(`../items/${id}`, {
                                method: 'PUT',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    showToast(`✅ Item "${data.name}" has been updated successfully!`, 'success');
                                    modal.classList.remove('active');
                                    modal.classList.add('opacity-0', 'pointer-events-none');
                                    loadAccounts(); // Reload the table
                                } else {
                                    if (result.error && result.error.toLowerCase().includes('duplicate')) {
                                        showToast(`❌ ${result.error}`, 'error');
                                    } else {
                                        showToast(`❌ Failed to update Item "${data.name}". ${result.error || 'Please try again.'}`, 'error');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showToast('Network error. Please try again.', 'error');
                            })
                            .finally(() => {
                                // Reset button state
                                updateBtn.disabled = false;
                                updateBtn.innerHTML = '<i class="ri-save-line mr-[5px]"></i>Update';
                            });
                        }
                    };
                }
            }, 50);

            // Clear and populate basic fields
            document.getElementById('editAccountId').value = item.id;
            document.getElementById('editAccountName').value = item.name || '';
            document.getElementById('editAccountNameUrdu').value = item.name_in_urdu || '';
            document.getElementById('editAccountDescription').value = item.description || '';
            document.getElementById('editAccountAddress').value = item.stock_limit || '';
            
            // Populate weight calculation fields
            document.getElementById('editAccountLength').value = item.length || '';
            document.getElementById('editAccountWidth').value = item.width || '';
            document.getElementById('editAccountThickness').value = item.thickness || '';
            document.getElementById('editAccountDiameter').value = item.diameter || '';
            document.getElementById('editAccountHeight').value = item.height || '';
            document.getElementById('editAccountDensity').value = item.density || '7850';
            
            // Set weight formula - use empty string if no formula exists (no default formula)
            // Only load the formula if it exists in the database, never set a default
            let weightFormula = '';
            if (item.weight_formula && item.weight_formula.trim()) {
                weightFormula = item.weight_formula.trim();
                
                // Only fix old formulas with incorrect divisor, but don't add default formula
                const formulaNormalized = weightFormula.replace(/\s/g, '').toLowerCase();
                
                // Check for old formula with /1000000 and update to /1000000000 (only fix, don't add default)
                if (formulaNormalized.includes('/1000000') && !formulaNormalized.includes('/1000000000')) {
                    weightFormula = weightFormula.replace(/\/\s*1000000\b/gi, ' / 1000000000');
                    console.warn('⚠️ Formula using incorrect divisor. Auto-corrected from / 1000000 to / 1000000000');
                }
                // Note: We no longer auto-add default formula - user must enter their own formula
            }
            
            // Always set to empty string if no formula exists (never set default formula)
            document.getElementById('editAccountWeightFormula').value = weightFormula;
            document.getElementById('editAccountCalculatedWeight').value = item.calculated_weight || '';
            
            // Calculate weight only if formula exists (preserve manually entered calculated_weight if no formula)
            if (weightFormula.trim()) {
                setTimeout(function() {
                    calculateEditWeightFromFormula();
                }, 300);
            }

            // Always show all fields including main head and control head
            const mainHeadGroup = document.querySelector('#editAccountModal .grid .relative:has(#editAccountMainHead)');
            const controlHeadGroup = document.querySelector('#editAccountModal .grid .relative:has(#editAccountControlHead)');
            
            if (mainHeadGroup) mainHeadGroup.style.display = 'block';
            if (controlHeadGroup) controlHeadGroup.style.display = 'block';

            // Load all data
            Promise.all([
                loadUnitTypesForModal(item.unit_type_id),
                loadMainHeadsForModal()
            ]).then(() => {
                setTimeout(() => {
                    setEnhancedSelectValue('editAccountUnitType', item.unit_type_id);
                    setEnhancedSelectValue('editAccountMainHead', item.main_head_id);
                    
                    // Load control heads after main head is set
                    loadControlHeadsForModal(item.main_head_id).then(() => {
                        setTimeout(() => {
                            setEnhancedSelectValue('editAccountControlHead', item.control_head_id);
                            enhanceModalSelects('editAccountModal');
                        }, 100);
                    });
                }, 100);
            });
        }

        // Assign/Edit Rack for Item
        function assignRack(itemId) {
            // Check both Items tab data and ERP Items data
            let item = null;
            if (typeof accountsData !== 'undefined' && accountsData.records) {
                item = accountsData.records.find(record => record.id == itemId);
            }
            // If not found in Items tab, the item is from ERP Structure tab - proceed without item object
            // The API will handle the item lookup

            // Get user's unit_id from PHP session
            const userUnitId = <?php echo json_encode(getCurrentUser()['unit_id'] ?? null); ?>;
            
            if (!userUnitId) {
                showToast('Unit ID is required for rack assignment', 'error');
                return;
            }

            // Show modal
            const modal = document.getElementById('assignRackModal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('active');

            // Set item ID
            document.getElementById('assignRackItemId').value = itemId;

            // Function to fetch current rack assignment
            async function fetchCurrentRackAssignment(itemId, unitId) {
                try {
                    const apiUrl = `../api/items/${itemId}/rack-assignment?unit_id=${unitId}`;
                    const response = await fetch(apiUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Rack assignment API response:', result);
                        if (result.success && result.data) {
                            const rackId = result.data.rack_id;
                            const assignmentUnitId = result.data.unit_id;
                            
                            // Verify the assignment is for the correct unit
                            if (assignmentUnitId && String(assignmentUnitId) !== String(unitId)) {
                                console.warn(`Rack assignment unit mismatch: assignment is for unit ${assignmentUnitId}, but current unit is ${unitId}`);
                                return null;
                            }
                            
                            if (rackId) {
                                console.log(`Found rack assignment: rack_id=${rackId}, unit_id=${assignmentUnitId || unitId}`);
                                return rackId;
                            }
                        }
                    } else {
                        console.error('Failed to fetch rack assignment:', response.status, response.statusText);
                    }
                    return null;
                } catch (error) {
                    console.error('Error fetching rack assignment:', error);
                    return null;
                }
            }

            // Function to load racks for the user's unit
            async function loadRacksForAssignment(unitId) {
                try {
                    console.log('Loading racks for unit_id:', unitId);
                    const response = await fetch(`../api/unit-racks?unit_id=${unitId}&limit=100`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Racks API response:', result);
                        if (result.success) {
                            const select = document.getElementById('assignRackSelect');
                            if (select) {
                                select.innerHTML = '<option value="" disabled selected hidden></option>';
                                const list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                                
                                console.log(`Loaded ${list.length} racks for unit ${unitId}:`, list.map(r => ({ id: r.id, name: r.name })));
                                
                                // Store loaded rack IDs for later verification
                                select._loadedRackIds = list.map(r => String(r.id));
                                
                                list.forEach(rack => {
                                    if (rack && rack.id && rack.name) {
                                        const option = document.createElement('option');
                                        option.value = String(rack.id); // Ensure value is string
                                        option.textContent = `[${rack.id}]-${rack.name}`;
                                        select.appendChild(option);
                                    }
                                });
                                
                                // Enhance the select and return a promise that resolves when enhancement is complete
                                return new Promise((resolve) => {
                                    setTimeout(() => {
                                        if (!select.__enhanced) {
                                            upgradeSelectToSearchable('assignRackSelect', 'Search racks...');
                                        }
                                        enhanceModalSelects('assignRackModal');
                                        
                                        // Wait a bit more to ensure enhancement is fully complete
                                        setTimeout(() => {
                                            console.log('Racks loaded and select enhanced. Available rack IDs:', select._loadedRackIds);
                                            resolve(true);
                                        }, 200);
                                    }, 100);
                                });
                            }
                        } else {
                            console.error('API returned success=false:', result);
                        }
                    } else {
                        console.error('Failed to load racks:', response.status, response.statusText);
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                        showToast('Failed to load racks', 'error');
                    }
                } catch (error) {
                    console.error('Error loading racks:', error);
                    showToast('Failed to load racks', 'error');
                }
                return false;
            }

            // Load racks first, then fetch and set current rack assignment
            (async () => {
                const racksLoaded = await loadRacksForAssignment(userUnitId);
                if (racksLoaded) {
                    // After racks are loaded and enhanced, fetch current assignment and set it
                    const currentRackId = await fetchCurrentRackAssignment(itemId, userUnitId);
                    console.log('Current rack assignment fetched:', currentRackId, 'for item:', itemId, 'unit:', userUnitId);
                    
                    if (currentRackId) {
                        const select = document.getElementById('assignRackSelect');
                        if (select) {
                            // Convert to string for comparison
                            const rackIdStr = String(currentRackId);
                            
                            // Function to try setting the value
                            const trySetRackValue = (retryCount = 0) => {
                                // Check if option exists
                                const optionExists = select.querySelector(`option[value="${rackIdStr}"]`);
                                
                                if (optionExists) {
                                    // Set the value directly
                                    select.value = rackIdStr;
                                    
                                    // Update enhanced select display if available
                                    if (select.__enhanced && typeof select.__enhanced.setDisplayFromValue === 'function') {
                                        select.__enhanced.setDisplayFromValue();
                                    }
                                    
                                    // Also dispatch change event
                                    try {
                                        select.dispatchEvent(new Event('change', { bubbles: true }));
                                    } catch (_) {}
                                    
                                    // Refresh the enhanced select
                                    if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                        select.__enhanced.refresh();
                                    }
                                    
                                    enhanceModalSelects('assignRackModal');
                                    console.log('✅ Rack assignment set successfully:', currentRackId);
                                } else if (retryCount < 5) {
                                    // Retry after a delay
                                    console.log(`Rack option not found for ID: ${currentRackId}, retrying... (${retryCount + 1}/5)`);
                                    setTimeout(() => {
                                        trySetRackValue(retryCount + 1);
                                    }, 200);
                                } else {
                                    console.warn('Rack option not found after max retries for ID:', currentRackId);
                                    console.warn('Available options:', Array.from(select.options).map(opt => ({ value: opt.value, text: opt.text })));
                                    // Fallback: use setEnhancedSelectValue which has its own retry logic
                                    setEnhancedSelectValue('assignRackSelect', currentRackId);
                                }
                            };
                            
                            // Check if this rack ID is in the loaded racks
                            const loadedRackIds = select._loadedRackIds || [];
                            console.log('Checking if rack ID', rackIdStr, 'exists in loaded racks:', loadedRackIds);
                            
                            if (!loadedRackIds.includes(rackIdStr)) {
                                console.log(`Rack ID ${rackIdStr} not found in loaded list. Searching for specific rack...`);
                                
                                // Search for the specific rack by ID in the unit-racks API
                                try {
                                    // Use search parameter to find the rack by ID
                                    const rackResponse = await fetch(`../api/unit-racks?unit_id=${userUnitId}&search=${rackIdStr}&limit=1`, {
                                        method: 'GET',
                                        credentials: 'same-origin',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        }
                                    });
                                    
                                    if (rackResponse.ok) {
                                        const rackResult = await rackResponse.json();
                                        console.log('Search result for rack:', rackResult);
                                        
                                        if (rackResult.success && rackResult.data) {
                                            const list = Array.isArray(rackResult.data.records) ? rackResult.data.records : (Array.isArray(rackResult.data) ? rackResult.data : []);
                                            
                                            // Find the rack with matching ID
                                            const rack = list.find(r => String(r.id) === rackIdStr);
                                            
                                            if (rack) {
                                                console.log('Found assigned rack:', rack);
                                                
                                                // Verify the rack belongs to the correct unit
                                                if (rack.unit_id && String(rack.unit_id) === String(userUnitId)) {
                                                    // Add the rack to the select options (at the beginning for visibility)
                                                    const option = document.createElement('option');
                                                    option.value = String(rack.id);
                                                    option.textContent = `[${rack.id}]-${rack.name}`;
                                                    // Insert after the empty option
                                                    const emptyOption = select.querySelector('option[value=""]');
                                                    if (emptyOption && emptyOption.nextSibling) {
                                                        select.insertBefore(option, emptyOption.nextSibling);
                                                    } else {
                                                        select.appendChild(option);
                                                    }
                                                    
                                                    // Update the loaded rack IDs list
                                                    if (!select._loadedRackIds) {
                                                        select._loadedRackIds = [];
                                                    }
                                                    if (!select._loadedRackIds.includes(rackIdStr)) {
                                                        select._loadedRackIds.push(rackIdStr);
                                                    }
                                                    
                                                    // Refresh the enhanced select
                                                    if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                                        select.__enhanced.refresh();
                                                    }
                                                    
                                                    console.log('✅ Assigned rack added to select options');
                                                    
                                                    // Now that the rack is added, proceed to set the value
                                                    setTimeout(() => {
                                                        trySetRackValue(0);
                                                    }, 150);
                                                    return; // Exit early since we'll set the value in the setTimeout
                                                } else {
                                                    console.warn(`Rack ${rackIdStr} belongs to unit ${rack.unit_id}, but current unit is ${userUnitId}`);
                                                    showToast(`Warning: Assigned rack (ID: ${rackIdStr}) belongs to a different unit.`, 'warning');
                                                    return;
                                                }
                                            } else {
                                                console.warn(`Rack ID ${rackIdStr} not found in search results for unit ${userUnitId}`);
                                                // Try using regular racks API as fallback
                                                console.log('Trying regular racks API as fallback...');
                                                const fallbackResponse = await fetch(`../api/racks/${rackIdStr}`, {
                                                    method: 'GET',
                                                    credentials: 'same-origin',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                    }
                                                });
                                                
                                                if (fallbackResponse.ok) {
                                                    const fallbackResult = await fallbackResponse.json();
                                                    if (fallbackResult.success && fallbackResult.data) {
                                                        const fallbackRack = fallbackResult.data;
                                                        if (fallbackRack.unit_id && String(fallbackRack.unit_id) === String(userUnitId)) {
                                                            // Add the rack
                                                            const option = document.createElement('option');
                                                            option.value = String(fallbackRack.id);
                                                            option.textContent = `[${fallbackRack.id}]-${fallbackRack.name}`;
                                                            const emptyOption = select.querySelector('option[value=""]');
                                                            if (emptyOption && emptyOption.nextSibling) {
                                                                select.insertBefore(option, emptyOption.nextSibling);
                                                            } else {
                                                                select.appendChild(option);
                                                            }
                                                            
                                                            if (!select._loadedRackIds) {
                                                                select._loadedRackIds = [];
                                                            }
                                                            if (!select._loadedRackIds.includes(rackIdStr)) {
                                                                select._loadedRackIds.push(rackIdStr);
                                                            }
                                                            
                                                            if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                                                select.__enhanced.refresh();
                                                            }
                                                            
                                                            setTimeout(() => {
                                                                trySetRackValue(0);
                                                            }, 150);
                                                            return;
                                                        }
                                                    }
                                                }
                                                
                                                showToast(`Warning: Assigned rack (ID: ${rackIdStr}) not found for this unit.`, 'warning');
                                                return;
                                            }
                                        } else {
                                            console.warn('Search API returned error or no data');
                                            showToast(`Warning: Could not find assigned rack (ID: ${rackIdStr}).`, 'warning');
                                            return;
                                        }
                                    } else {
                                        const errorText = await rackResponse.text();
                                        console.warn('Failed to search for rack:', rackResponse.status, errorText);
                                        showToast(`Warning: Could not search for assigned rack (ID: ${rackIdStr}).`, 'warning');
                                        return;
                                    }
                                } catch (error) {
                                    console.error('Error searching for specific rack:', error);
                                    showToast(`Warning: Could not load assigned rack (ID: ${rackIdStr}).`, 'warning');
                                    return;
                                }
                            }
                            
                            // Start trying to set the value after a short delay
                            setTimeout(() => {
                                trySetRackValue();
                            }, 150);
                        }
                    } else {
                        console.log('No current rack assignment found for this item in this unit');
                    }
                }
            })();
        }

        // Delete account with SweetAlert
        function deleteAccount(id) {
            const item = accountsData.records.find(record => record.id == id);
            const accountName = item ? item.name : 'this account';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${accountName}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`../api/items/${id}`, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (!result.success) {
                                throw new Error(result.error || 'Failed to delete account');
                            }
                            return result;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast(`🗑️ Item "${accountName}" has been deleted successfully!`, 'success');
                    loadAccounts();
                }
            });
        }

        // Load main heads for modal
        function loadMainHeadsForModal() {
            return fetch('../api/items/main-heads', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const select = document.getElementById('editAccountMainHead');
                        select.innerHTML = '<option value=""></option>';
                        const mainHeadsList = Array.isArray(result.data) ? result.data : (result.data?.records || []);
                        (mainHeadsList || []).forEach(item => {
                            select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                        });
                        try {
                            const jq = window.jQuery || window.$;
                            if (jq && jq.fn && jq.fn.select2) {
                                jq(select).trigger('change.select2');
                            }
                            if (select && select.slim && typeof select.slim.setData === 'function') {
                                const data = [{
                                    text: '',
                                    value: ''
                                }].concat((mainHeadsList || []).map(r => ({
                                    text: String(r.name),
                                    value: String(r.id)
                                })));
                                select.slim.setData(data);
                            }
                        } catch (e) {}
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Load control heads for modal
        function loadControlHeadsForModal(mainHeadId) {
            const select = document.getElementById('editAccountControlHead');
            const id = String(mainHeadId || '').trim();
            if (!id) {
                if (select) select.innerHTML = '<option value=""></option>';
                return Promise.resolve();
            }

            const toArray = (res) => {
                if (!res || !res.data) return [];
                return Array.isArray(res.data.records) ? res.data.records : (Array.isArray(res.data) ? res.data : []);
            };

            const fillOptions = (list) => {
                if (!select) return;
                select.innerHTML = '<option value=""></option>';
                list.forEach(item => {
                    if (item && item.id) select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                });
                
                // Refresh enhanced select if it exists
                setTimeout(() => {
                    try {
                        const jq = window.jQuery || window.$;
                        if (jq && jq.fn && jq.fn.select2) {
                            jq(select).trigger('change.select2');
                        }
                        if (select.slim && typeof select.slim.setData === 'function') {
                            const data = [{
                                text: '',
                                value: ''
                            }].concat(list.map(r => ({
                                text: `[${r.id}]-${r.name}`,
                                value: String(r.id)
                            })));
                            select.slim.setData(data);
                        }
                        
                        // Refresh enhanced select
                        if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                            select.__enhanced.refresh();
                        }
                        if (select.__enhanced && typeof select.__enhanced.setDisplayFromValue === 'function') {
                            select.__enhanced.setDisplayFromValue();
                        }
                        
                        // Enhance modal selects to ensure everything is updated
                        enhanceModalSelects('editAccountModal');
                    } catch (e) {
                        console.error('Error refreshing control head select:', e);
                    }
                }, 50);
                
                if (typeof queueSelect2Reinit === 'function') {
                    try {
                        queueSelect2Reinit('editAccountControlHead');
                    } catch (e) {}
                }
            };

            // Try items endpoint first
            return fetch(`../api/items/control-heads?main_head_id=${encodeURIComponent(id)}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    let list = toArray(res);
                    if ((!list || list.length === 0)) {
                        // Fallback to control-heads endpoint (paginated)
                        return fetch(`../control-heads?type=item&status=I&limit=1000&main_head_id=${encodeURIComponent(id)}`, {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(rr => rr.json())
                            .then(rr => {
                                fillOptions(toArray(rr));
                            });
                    }
                    fillOptions(list);
                })
                .catch(() => {});
        }

        // Event listeners for account form
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadMainHeadsForAccount();
            loadAccounts();
            
            // Setup edit main head change listener on page load
            setupEditMainHeadChangeListener();
            
            // Load creation selects for unit types
            (function initCreateSelects() {
                const accountUnitTypeSelect = document.getElementById('accountUnitType');
                if (accountUnitTypeSelect) {
                    fetch('../api/unit-types', {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json()).then(result => {
                            let list = [];
                            if (result.success && result.data) {
                                list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            }
                            accountUnitTypeSelect.innerHTML = '<option value=""></option>';
                            list.forEach(u => {
                                if (u.id && u.name) accountUnitTypeSelect.innerHTML += `<option value="${u.id}">[${u.id}]-${u.name}</option>`;
                            });
                            // Enhance the select if not already enhanced, then refresh
                            setTimeout(() => {
                                if (!accountUnitTypeSelect.__enhanced) {
                                    upgradeSelectToSearchable('accountUnitType', 'Search unit types...');
                                }
                                refreshSelectBox(accountUnitTypeSelect);
                            }, 100);
                        }).catch(() => {});
                }
            })();

            // Function to refresh all select boxes after page load
            function refreshAllSelectBoxes() {
                setTimeout(() => {
                    const unitTypeSelect = document.getElementById('accountUnitType');
                    const mainHeadSelect = document.getElementById('accountMainHead');
                    const controlHeadSelect = document.getElementById('accountControlHead');

                    if (unitTypeSelect) refreshSelectBox(unitTypeSelect);
                    if (mainHeadSelect) refreshSelectBox(mainHeadSelect);
                    if (controlHeadSelect) refreshSelectBox(controlHeadSelect);
                }, 500);
            }

            // Call refresh function after page load
            document.addEventListener('DOMContentLoaded', refreshAllSelectBoxes);




            // Function to set select box value and refresh display
            function setSelectValue(selectId, value) {
                const select = document.getElementById(selectId);
                if (!select) return;

                select.value = value;

                // Refresh the display using the built-in method
                refreshSelectBox(select);
            }

            // Simple function to refresh select box display
            function forceRefreshSelectBox(selectId) {
                const select = document.getElementById(selectId);
                if (!select) return;

                // Use the built-in refresh method
                if (select.__enhanced && typeof select.__enhanced.setDisplayFromValue === 'function') {
                    select.__enhanced.setDisplayFromValue();
                }

                // Also call the regular refresh
                refreshSelectBox(select);
            }

            // Test function to verify select box functionality
            function testSelectBoxes() {
                console.log('Testing select boxes...');

                const mainHead = document.getElementById('accountMainHead');
                const controlHead = document.getElementById('accountControlHead');
                const unitType = document.getElementById('accountUnitType');

                console.log('Main Head:', mainHead?.value, mainHead?.options[mainHead?.selectedIndex]?.text);
                console.log('Control Head:', controlHead?.value, controlHead?.options[controlHead?.selectedIndex]?.text);
                console.log('Unit Type:', unitType?.value, unitType?.options[unitType?.selectedIndex]?.text);

                // Test setting values
                if (unitType && unitType.options.length > 1) {
                    setSelectValue('accountUnitType', unitType.options[1].value);
                    console.log('Set Unit Type to:', unitType.options[1].value);
                }
            }

            // Expose test function globally for debugging
            window.testSelectBoxes = testSelectBoxes;
            window.forceRefreshSelectBox = forceRefreshSelectBox;
            window.setSelectValue = setSelectValue;

            // Test function specifically for Unit Type
            function testUnitType() {
                console.log('Testing Unit Type dropdown...');

                const unitTypeSelect = document.getElementById('accountUnitType');

                if (unitTypeSelect && unitTypeSelect.options.length > 1) {
                    console.log('Setting Unit Type to first option:', unitTypeSelect.options[1].value);
                    setSelectValue('accountUnitType', unitTypeSelect.options[1].value);
                }
            }

            window.testUnitType = testUnitType;

            // Simple function to manually update select display
            function updateSelectDisplay(selectId) {
                const select = document.getElementById(selectId);
                if (!select) return;

                // Try the enhanced method first
                if (select.__enhanced && typeof select.__enhanced.setDisplayFromValue === 'function') {
                    select.__enhanced.setDisplayFromValue();
                }

                // Also try the regular refresh
                refreshSelectBox(select);
            }

            window.updateSelectDisplay = updateSelectDisplay;

            // Item form submission
            const accountForm = document.getElementById('accountForm');
            if (accountForm) {
                accountForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // For SA users, temporarily re-enable disabled fields to get their values
                const isSAUser = window.defaultRoleStatus === 'SA';
                if (isSAUser) {
                    const mainHeadField = document.getElementById('accountMainHead');
                    const controlHeadField = document.getElementById('accountControlHead');
                    if (mainHeadField) mainHeadField.disabled = false;
                    if (controlHeadField) controlHeadField.disabled = false;
                }

                const formData = new FormData(accountForm);
                
                // Re-disable fields for SA users after getting form data
                if (isSAUser) {
                    const mainHeadField = document.getElementById('accountMainHead');
                    const controlHeadField = document.getElementById('accountControlHead');
                    if (mainHeadField) mainHeadField.disabled = true;
                    if (controlHeadField) controlHeadField.disabled = true;
                }
                // Get user's unit_id from PHP session
                const userUnitId = <?php echo json_encode(getCurrentUser()['unit_id'] ?? null); ?>;
                
                const data = {
                    name: formData.get('name'),
                    name_in_urdu: formData.get('name_in_urdu'),
                    main_head_id: formData.get('main_head_id'),
                    control_head_id: formData.get('control_head_id'),
                    unit_type_id: formData.get('unit_type_id'),
                    stock_limit: formData.get('stock_limit'),
                    description: formData.get('description'),
                    // Weight calculation fields
                    length: formData.get('length') || null,
                    width: formData.get('width') || null,
                    thickness: formData.get('thickness') || null,
                    diameter: formData.get('diameter') || null,
                    height: formData.get('height') || null,
                    density: formData.get('density') || null,
                    weight_formula: formData.get('weight_formula') || null,
                    calculated_weight: formData.get('calculated_weight') || null
                };
                
                // Clean weight calculation fields - only save if they have values
                // Handle weight_formula - only save if user entered a formula (no default formula)
                if (data.weight_formula === null || data.weight_formula === '' || !data.weight_formula.trim()) {
                    delete data.weight_formula;
                } else {
                    // Trim the formula if it exists
                    data.weight_formula = data.weight_formula.trim();
                }
                
                // Handle dimension fields - only save if user provided values
                const dimensionFields = ['length', 'width', 'thickness', 'diameter', 'height', 'density'];
                dimensionFields.forEach(key => {
                    if (data[key] === null || data[key] === '' || data[key] === '0') {
                        delete data[key];
                    } else {
                        data[key] = parseFloat(data[key]);
                    }
                });
                
                // Handle calculated_weight separately - always save if it has a value (auto-calculated or manually entered)
                // This should save regardless of whether formula/parameters exist
                if (data.calculated_weight !== null && data.calculated_weight !== '' && data.calculated_weight !== '0') {
                    data.calculated_weight = parseFloat(data.calculated_weight);
                } else {
                    // Only delete if no formula exists (backend will auto-calculate if formula exists)
                    if (!data.weight_formula) {
                        delete data.calculated_weight;
                    }
                }


                // Validate required fields based on user role
                const isSA = window.defaultRoleStatus === 'SA';
                const requiredFields = [];
                
                if (!data.name) requiredFields.push('Name');
                if (!data.name_in_urdu) requiredFields.push('Name in Urdu');
                if (!data.unit_type_id) requiredFields.push('Unit Type');
                
                // For non-SA users, also validate Main Head and Control Head
                if (!isSA) {
                    if (!data.main_head_id) requiredFields.push('Main Head');
                    if (!data.control_head_id) requiredFields.push('Control Head');
                }
                
                if (requiredFields.length > 0) {
                    const fieldList = requiredFields.join(', ');
                    showToast(`⚠️ Please fill in all required fields (${fieldList})`, 'warning');
                    return;
                }

                // Show loading state
                const submitBtn = document.getElementById('submitAccountBtn');
                const submitText = document.getElementById('submitAccountText');
                const submitIcon = document.getElementById('submitAccountIcon');

                submitBtn.disabled = true;
                submitText.textContent = 'Creating...';
                submitIcon.innerHTML = '<i class="ri-loader-4-line animate-spin"></i>';

                // Show loading toast
                // No loading toast to avoid double toasters

                // Make API call
                fetch('../api/items', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            // Custom form reset: keep Main Head and Control Head, clear other fields
                            const mainHeadSelect = document.getElementById('accountMainHead');
                            const controlHeadSelect = document.getElementById('accountControlHead');
                            const nameInput = document.getElementById('accountName');
                            const nameUrduInput = document.getElementById('accountNameUrdu');
                            const unitTypeSelect = document.getElementById('accountUnitType');
                            const descriptionInput = document.getElementById('accountDescription');
                            const stockLimitInput = document.getElementById('accountStockLimit');

                            // Store current Main Head and Control Head values
                            const currentMainHead = mainHeadSelect.value;
                            const currentControlHead = controlHeadSelect.value;

                            // Clear all fields
                            if (nameInput) nameInput.value = '';
                            if (nameUrduInput) nameUrduInput.value = '';
                            if (unitTypeSelect) setSelectValue('accountUnitType', '');
                            if (descriptionInput) descriptionInput.value = '';
                            if (stockLimitInput) stockLimitInput.value = '';
                            
                            // Clear weight calculation fields
                            const lengthInput = document.getElementById('accountLength');
                            const widthInput = document.getElementById('accountWidth');
                            const thicknessInput = document.getElementById('accountThickness');
                            const diameterInput = document.getElementById('accountDiameter');
                            const heightInput = document.getElementById('accountHeight');
                            const densityInput = document.getElementById('accountDensity');
                            const weightFormulaInput = document.getElementById('accountWeightFormula');
                            const calculatedWeightInput = document.getElementById('accountCalculatedWeight');
                            
                            if (lengthInput) lengthInput.value = '';
                            if (widthInput) widthInput.value = '';
                            if (thicknessInput) thicknessInput.value = '';
                            if (diameterInput) diameterInput.value = '';
                            if (heightInput) heightInput.value = '';
                            if (densityInput) densityInput.value = '7850';
                            if (weightFormulaInput) weightFormulaInput.value = '';
                            if (calculatedWeightInput) calculatedWeightInput.value = '';

                            // Restore Main Head and Control Head selections
                            if (mainHeadSelect) setSelectValue('accountMainHead', currentMainHead);
                            if (controlHeadSelect) setSelectValue('accountControlHead', currentControlHead);

                            // Refresh all select boxes to ensure proper display
                            setTimeout(() => {
                                const unitTypeSelect = document.getElementById('accountUnitType');
                                const mainHeadSelect = document.getElementById('accountMainHead');
                                const controlHeadSelect = document.getElementById('accountControlHead');

                                if (unitTypeSelect) refreshSelectBox(unitTypeSelect);
                                if (mainHeadSelect) refreshSelectBox(mainHeadSelect);
                                if (controlHeadSelect) refreshSelectBox(controlHeadSelect);
                            }, 50);

                            // Focus back to first field (enhanced select if available)
                            setTimeout(() => {
                                try {
                                    const firstSel = document.getElementById('accountMainHead');
                                    if (firstSel) {
                                        if (firstSel.__enhanced && firstSel.__enhanced.control) firstSel.__enhanced.control.focus();
                                        else firstSel.focus();
                                    }
                                } catch (_) {}
                            }, 50);
                            showToast(`✅ New Item "${data.name}" has been created successfully!`, 'success');
                            loadAccounts(); // Reload the table
                        } else {
                            if (result.error && result.error.toLowerCase().includes('duplicate')) {
                                showToast(`❌ ${result.error}`, 'error');
                            } else {
                                showToast(`❌ Failed to create new Item. ${result.error || 'Please try again.'}`, 'error');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitText.textContent = 'Create';
                        submitIcon.innerHTML = '<i class="ri-add-line"></i>';
                    });
            });
            }

            // Weight Calculation Function for Edit Mode
            function calculateEditWeightFromFormula() {
                const lengthInput = document.getElementById('editAccountLength');
                const widthInput = document.getElementById('editAccountWidth');
                const thicknessInput = document.getElementById('editAccountThickness');
                const diameterInput = document.getElementById('editAccountDiameter');
                const heightInput = document.getElementById('editAccountHeight');
                const densityInput = document.getElementById('editAccountDensity');
                const formulaInput = document.getElementById('editAccountWeightFormula');
                const calculatedWeightInput = document.getElementById('editAccountCalculatedWeight');
                
                if (!formulaInput || !calculatedWeightInput) {
                    return;
                }
                
                const length = parseFloat(lengthInput?.value) || 0;
                const width = parseFloat(widthInput?.value) || 0;
                const thickness = parseFloat(thicknessInput?.value) || 0;
                const diameter = parseFloat(diameterInput?.value) || 0;
                const height = parseFloat(heightInput?.value) || 0;
                const density = parseFloat(densityInput?.value) || 7850;
                
                // Get formula from value only (do not use placeholder as default)
                let formula = formulaInput.value.trim() || '';
                
                // If no formula, preserve manually entered calculated weight - don't clear it
                if (!formula) {
                    return;
                }
                
                // Replace variables in formula (case-insensitive)
                let expression = formula;
                
                // Replace full variable names first (case-insensitive)
                expression = expression.replace(/\blength\b/gi, '(' + length + ')');
                expression = expression.replace(/\bwidth\b/gi, '(' + width + ')');
                expression = expression.replace(/\bthickness\b/gi, '(' + thickness + ')');
                expression = expression.replace(/\bdiameter\b/gi, '(' + diameter + ')');
                expression = expression.replace(/\bheight\b/gi, '(' + height + ')');
                expression = expression.replace(/\bdensity\b/gi, '(' + density + ')');
                
                // Replace short names (case-sensitive to avoid conflicts)
                expression = expression.replace(/\bL\b/g, '(' + length + ')');
                expression = expression.replace(/\bW\b/g, '(' + width + ')');
                expression = expression.replace(/\bT\b/g, '(' + thickness + ')');
                expression = expression.replace(/\bD\b/g, '(' + diameter + ')');
                expression = expression.replace(/\bH\b/g, '(' + height + ')');
                expression = expression.replace(/\bDEN\b/gi, '(' + density + ')');
                
                try {
                    // Remove whitespace for validation
                    const expressionNoSpaces = expression.replace(/\s/g, '');
                    
                    // Validate expression contains only safe characters
                    const safePattern = /^[0-9+\-*/().]+$/;
                    if (!safePattern.test(expressionNoSpaces)) {
                        calculatedWeightInput.value = '';
                        return;
                    }
                    
                    // Evaluate the expression
                    let result;
                    try {
                        result = Function('"use strict"; return (' + expression + ')')();
                    } catch (evalError) {
                        calculatedWeightInput.value = '';
                        return;
                    }
                    
                    // Check for valid number
                    if (typeof result !== 'number' || isNaN(result) || !isFinite(result)) {
                        calculatedWeightInput.value = '';
                        return;
                    }
                    
                    // Validate result - if it's unreasonably large (> 1,000,000 kg), likely missing unit conversion
                    // For steel plates, weights should typically be under 100,000 kg
                    if (result > 1000000 && length > 0 && width > 0 && thickness > 0) {
                        // Check if formula is missing division or using wrong divisor
                        const formulaNormalized = formula.replace(/\s/g, '').toLowerCase();
                        if (formulaNormalized.includes('/1000000') && !formulaNormalized.includes('/1000000000')) {
                            // Fix old formula with /1000000 to /1000000000
                            const fixedFormula = formula.replace(/\/\s*1000000\b/gi, ' / 1000000000');
                            formulaInput.value = fixedFormula;
                            console.warn('⚠️ Formula using incorrect divisor. Auto-corrected from / 1000000 to / 1000000000');
                            setTimeout(() => calculateEditWeightFromFormula(), 100);
                            return;
                        } else if (!formulaNormalized.includes('/1000000000')) {
                            // Try to auto-fix by adding division
                            const fixedFormula = formula.trim().endsWith(')') 
                                ? formula + ' / 1000000000'
                                : '(' + formula + ') / 1000000000';
                            
                            // Update formula field
                            formulaInput.value = fixedFormula;
                            
                            // Recalculate with fixed formula
                            setTimeout(() => calculateEditWeightFromFormula(), 100);
                            return;
                        }
                    }
                    
                    // Round to 2 decimal places
                    const finalWeight = Math.round(result * 100) / 100;
                    calculatedWeightInput.value = finalWeight.toFixed(2);
                } catch (e) {
                    calculatedWeightInput.value = '';
                }
            }
            
            // Weight Calculation Function (make it globally accessible)
            function calculateWeightFromFormula() {
                const lengthInput = document.getElementById('accountLength');
                const widthInput = document.getElementById('accountWidth');
                const thicknessInput = document.getElementById('accountThickness');
                const diameterInput = document.getElementById('accountDiameter');
                const heightInput = document.getElementById('accountHeight');
                const densityInput = document.getElementById('accountDensity');
                const formulaInput = document.getElementById('accountWeightFormula');
                const calculatedWeightInput = document.getElementById('accountCalculatedWeight');
                
                if (!formulaInput || !calculatedWeightInput) {
                    return;
                }
                
                const length = parseFloat(lengthInput?.value) || 0;
                const width = parseFloat(widthInput?.value) || 0;
                const thickness = parseFloat(thicknessInput?.value) || 0;
                const diameter = parseFloat(diameterInput?.value) || 0;
                const height = parseFloat(heightInput?.value) || 0;
                const density = parseFloat(densityInput?.value) || 7850;
                
                // Get formula from value only (do not use placeholder as default)
                let formula = formulaInput.value.trim() || '';
                
                // If no formula, preserve manually entered calculated weight - don't clear it
                if (!formula) {
                    return;
                }
                
                // Debug logging (remove in production)
                console.log('Calculating weight:', { length, width, thickness, diameter, height, density, formula });
                
                // Replace variables in formula (case-insensitive)
                // Use word boundaries to avoid partial replacements
                let expression = formula;
                
                // Replace full variable names first (case-insensitive)
                expression = expression.replace(/\blength\b/gi, '(' + length + ')');
                expression = expression.replace(/\bwidth\b/gi, '(' + width + ')');
                expression = expression.replace(/\bthickness\b/gi, '(' + thickness + ')');
                expression = expression.replace(/\bdiameter\b/gi, '(' + diameter + ')');
                expression = expression.replace(/\bheight\b/gi, '(' + height + ')');
                expression = expression.replace(/\bdensity\b/gi, '(' + density + ')');
                
                // Replace short names (case-sensitive to avoid conflicts)
                expression = expression.replace(/\bL\b/g, '(' + length + ')');
                expression = expression.replace(/\bW\b/g, '(' + width + ')');
                expression = expression.replace(/\bT\b/g, '(' + thickness + ')');
                expression = expression.replace(/\bD\b/g, '(' + diameter + ')');
                expression = expression.replace(/\bH\b/g, '(' + height + ')');
                expression = expression.replace(/\bDEN\b/gi, '(' + density + ')');
                
                console.log('Expression after replacement:', expression);
                
                try {
                    // Remove whitespace for validation
                    const expressionNoSpaces = expression.replace(/\s/g, '');
                    
                    // Validate expression contains only safe characters (numbers, operators, parentheses, decimal points)
                    // Allow: 0-9, +, -, *, /, (, ), .
                    const safePattern = /^[0-9+\-*/().]+$/;
                    if (!safePattern.test(expressionNoSpaces)) {
                        console.error('Invalid characters in expression:', expressionNoSpaces);
                        calculatedWeightInput.value = '';
                        return;
                    }
                    
                    console.log('Evaluating expression:', expression);
                    
                    // Evaluate the expression using Function constructor
                    let result;
                    try {
                        result = Function('"use strict"; return (' + expression + ')')();
                    } catch (evalError) {
                        console.error('Evaluation error:', evalError.message);
                        calculatedWeightInput.value = '';
                        return;
                    }
                    
                    console.log('Raw result:', result, 'Type:', typeof result, 'isNaN:', isNaN(result), 'isFinite:', isFinite(result));
                    
                    // Check for valid number
                    if (typeof result !== 'number' || isNaN(result) || !isFinite(result)) {
                        calculatedWeightInput.value = '';
                        console.error('Invalid result - not a valid number:', result);
                        return;
                    }
                    
                    // Validate result - if it's unreasonably large (> 1,000,000 kg), likely missing unit conversion
                    // For steel plates, weights should typically be under 100,000 kg
                    if (result > 1000000 && length > 0 && width > 0 && thickness > 0) {
                        // Check if formula is missing division or using wrong divisor
                        const formulaNormalized = formula.replace(/\s/g, '').toLowerCase();
                        if (formulaNormalized.includes('/1000000') && !formulaNormalized.includes('/1000000000')) {
                            // Fix old formula with /1000000 to /1000000000
                            const fixedFormula = formula.replace(/\/\s*1000000\b/gi, ' / 1000000000');
                            formulaInput.value = fixedFormula;
                            console.warn('⚠️ Formula using incorrect divisor. Auto-corrected from / 1000000 to / 1000000000');
                            setTimeout(() => calculateWeightFromFormula(), 100);
                            return;
                        } else if (!formulaNormalized.includes('/1000000000')) {
                            // Try to auto-fix by adding division
                            const fixedFormula = formula.trim().endsWith(')') 
                                ? formula + ' / 1000000000'
                                : '(' + formula + ') / 1000000000';
                            
                            // Update formula field
                            formulaInput.value = fixedFormula;
                            console.warn('⚠️ Result too large - auto-corrected formula to include / 1000000000');
                            
                            // Recalculate with fixed formula
                            setTimeout(() => calculateWeightFromFormula(), 100);
                            return;
                        }
                    }
                    
                    // Round to 2 decimal places
                    const finalWeight = Math.round(result * 100) / 100;
                    calculatedWeightInput.value = finalWeight.toFixed(2);
                    console.log('✅ Weight calculated successfully:', finalWeight.toFixed(2), 'kg');
                } catch (e) {
                    calculatedWeightInput.value = '';
                    console.error('❌ Calculation error:', e.message, 'Stack:', e.stack, 'Expression:', expression);
                }
            }
            
            // Setup weight calculation event listeners
            function setupWeightCalculation() {
                const calculateBtn = document.getElementById('calculateWeightBtn');
                if (calculateBtn) {
                    calculateBtn.addEventListener('click', function(e) {
                        calculateWeightFromFormula();
                        // Show error if calculation failed
                        const calculatedWeightInput = document.getElementById('accountCalculatedWeight');
                        if (!calculatedWeightInput || !calculatedWeightInput.value) {
                            showToast('Please check your formula and dimensions', 'error');
                        }
                    });
                }
                
                // Auto-calculate when dimensions or formula change
                ['accountLength', 'accountWidth', 'accountThickness', 'accountDiameter', 
                 'accountHeight', 'accountDensity', 'accountWeightFormula'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        // Remove existing listeners to avoid duplicates
                        field.removeEventListener('input', calculateWeightFromFormula);
                        field.addEventListener('input', calculateWeightFromFormula);
                        field.addEventListener('change', calculateWeightFromFormula);
                    }
                });
                
                // Calculate immediately if formula and values exist
                setTimeout(function() {
                    calculateWeightFromFormula();
                }, 300);
            }
            
            // Weight Calculation Section Toggle
            const toggleWeightSection = document.getElementById('toggleWeightSection');
            const weightCalculationSection = document.getElementById('weightCalculationSection');
            const weightSectionIcon = document.getElementById('weightSectionIcon');
            
            if (toggleWeightSection && weightCalculationSection) {
                toggleWeightSection.addEventListener('click', function() {
                    const isHidden = weightCalculationSection.classList.contains('hidden');
                    if (isHidden) {
                        weightCalculationSection.classList.remove('hidden');
                        weightSectionIcon.style.transform = 'rotate(180deg)';
                                // Setup listeners when section is expanded
                        setTimeout(setupWeightCalculation, 50);
                        // Calculate immediately if values exist
                        setTimeout(function() {
                            setupWeightCalculation();
                            calculateWeightFromFormula();
                        }, 150);
                    } else {
                        weightCalculationSection.classList.add('hidden');
                        weightSectionIcon.style.transform = 'rotate(0deg)';
                    }
                });
            }
            
            // Setup weight calculation on page load
            function initWeightCalculation() {
                setupWeightCalculation();
                // Also calculate immediately if section is visible
                const weightSection = document.getElementById('weightCalculationSection');
                if (weightSection && !weightSection.classList.contains('hidden')) {
                    setTimeout(calculateWeightFromFormula, 500);
                }
            }
            
            // Run initialization
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initWeightCalculation, 100);
                });
            } else {
                // DOM already loaded
                setTimeout(initWeightCalculation, 300);
            }
            
            // Also try to setup after a longer delay to catch any late-loading elements
            setTimeout(function() {
                const formulaInput = document.getElementById('accountWeightFormula');
                if (formulaInput && !formulaInput.hasAttribute('data-listener-attached')) {
                    setupWeightCalculation();
                    formulaInput.setAttribute('data-listener-attached', 'true');
                }
            }, 1000);
            
            // Setup Edit Mode Weight Calculation
            function setupEditWeightCalculation() {
                const editCalculateBtn = document.getElementById('editCalculateWeightBtn');
                if (editCalculateBtn) {
                    editCalculateBtn.addEventListener('click', calculateEditWeightFromFormula);
                }
                
                // Auto-calculate when dimensions or formula change in edit mode
                ['editAccountLength', 'editAccountWidth', 'editAccountThickness', 'editAccountDiameter', 
                 'editAccountHeight', 'editAccountDensity', 'editAccountWeightFormula'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.removeEventListener('input', calculateEditWeightFromFormula);
                        field.addEventListener('input', calculateEditWeightFromFormula);
                        field.addEventListener('change', calculateEditWeightFromFormula);
                    }
                });
                
                // Calculate immediately only if formula exists (don't auto-calculate with empty formula)
                setTimeout(function() {
                    const formulaInput = document.getElementById('editAccountWeightFormula');
                    if (formulaInput && formulaInput.value.trim()) {
                        calculateEditWeightFromFormula();
                    }
                }, 300);
            }
            
            // Edit Weight Section Toggle
            const toggleEditWeightSection = document.getElementById('toggleEditWeightSection');
            const editWeightCalculationSection = document.getElementById('editWeightCalculationSection');
            const editWeightSectionIcon = document.getElementById('editWeightSectionIcon');
            
            if (toggleEditWeightSection && editWeightCalculationSection) {
                toggleEditWeightSection.addEventListener('click', function() {
                    const isHidden = editWeightCalculationSection.classList.contains('hidden');
                    if (isHidden) {
                        editWeightCalculationSection.classList.remove('hidden');
                        editWeightSectionIcon.style.transform = 'rotate(180deg)';
                        // Setup listeners when section is expanded
                        setTimeout(setupEditWeightCalculation, 50);
                        // Calculate immediately if values exist
                        setTimeout(function() {
                            setupEditWeightCalculation();
                            calculateEditWeightFromFormula();
                        }, 150);
                    } else {
                        editWeightCalculationSection.classList.add('hidden');
                        editWeightSectionIcon.style.transform = 'rotate(0deg)';
                    }
                });
            }
            
            // Setup edit weight calculation when modal opens
            const editAccountModalForWeight = document.getElementById('editAccountModal');
            if (editAccountModalForWeight) {
                editAccountModalForWeight.addEventListener('click', function(e) {
                    // When modal is opened, setup weight calculation
                    if (editAccountModalForWeight.classList.contains('active')) {
                        setTimeout(setupEditWeightCalculation, 200);
                    }
                });
            }
            
            // Account reset button
            // const resetAccountBtn = document.getElementById('resetAccountBtn');
            // resetAccountBtn.addEventListener('click', function() {
            //     document.getElementById('accountForm').reset();
            // });

            // Account search input
            const searchAccountInput = document.getElementById('searchAccountInput');
            let searchAccountTimeout;
            searchAccountInput.addEventListener('input', function() {
                clearTimeout(searchAccountTimeout);
                searchAccountTimeout = setTimeout(() => {
                    performSearch(this.value);
                }, 500);
            });

            // Enhanced search with Enter key support
            searchAccountInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchAccountTimeout);
                    performSearch(this.value);
                }
            });

            // Clear search button
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            clearSearchBtn.addEventListener('click', function() {
                searchAccountInput.value = '';
                performSearch('');
                this.style.display = 'none';
            });

            // Export to Excel button (ERP Items)
            const exportErpItemsToExcelBtn = document.getElementById('exportErpItemsToExcelBtn');
            if (exportErpItemsToExcelBtn) {
                exportErpItemsToExcelBtn.addEventListener('click', function() {
                    exportItemsToExcel();
                });
            }

            // Import from CSV handler removed

            // Show/hide clear button based on search input
            searchAccountInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    clearSearchBtn.style.display = 'block';
                } else {
                    clearSearchBtn.style.display = 'none';
                }
            });

            // Account filter dropdown
            // const filterAccountDropdownBtn = document.getElementById('filterAccountDropdownBtn');
            const filterAccountDropdown = document.getElementById('filterAccountDropdown');

            // filterAccountDropdownBtn.addEventListener('click', function() {
            //     filterAccountDropdown.classList.toggle('hidden');
            // });

            // Close dropdown when clicking outside
            // document.addEventListener('click', function(e) {
            //     if (!filterAccountDropdownBtn.contains(e.target) && !filterAccountDropdown.contains(e.target)) {
            //         filterAccountDropdown.classList.add('hidden');
            //     }
            // });

            // Account main head change event
            const accountMainHead = document.getElementById('accountMainHead');
            if (accountMainHead) {
                accountMainHead.addEventListener('change', function() {
                    const controlHeadSelect = document.getElementById('accountControlHead');
                    if (!controlHeadSelect) return;
                    
                    controlHeadSelect.innerHTML = '<option value="">Select Control Head</option>';
                    // Clear current selection in enhanced UI
                    try {
                        const jq = window.jQuery || window.$;
                        if (jq && jq.fn && jq.fn.select2) {
                            jq(controlHeadSelect).val('').trigger('change.select2');
                        }
                        if (controlHeadSelect && controlHeadSelect.slim && typeof controlHeadSelect.slim.setSelected === 'function') {
                            controlHeadSelect.slim.setSelected('');
                        }
                    } catch (e) {}

                    // Resolve selected main head id robustly (native/Select2/Slim)
                    let mainHeadId = this.value;
                    const selectedOption = (this.options && this.selectedIndex >= 0) ? this.options[this.selectedIndex] : null;
                    if ((!mainHeadId || mainHeadId === '') && selectedOption) {
                        mainHeadId = selectedOption.value || '';
                    }
                    try {
                        if ((!mainHeadId || mainHeadId === '') && this.slim && typeof this.slim.getSelected === 'function') {
                            const sel = this.slim.getSelected();
                            mainHeadId = Array.isArray(sel) ? (sel[0] || '') : (sel || '');
                        }
                    } catch (e) {}
                    const normalizedId = String(mainHeadId || '').trim();
                    loadControlHeadsForAccount(normalizedId);
                    // Once loaded, reinit is handled; also bump reinit now to ensure dropdown opens under correct parent
                    try {
                        if (typeof queueSelect2Reinit === 'function') queueSelect2Reinit('accountControlHead');
                    } catch (e) {}
                });
            }

            // Account control head change event - refresh Unit Type dropdown
            const accountControlHead = document.getElementById('accountControlHead');
            if (accountControlHead) {
                accountControlHead.addEventListener('change', function() {
                    // Clear Unit Type selection when Control Head changes
                    setSelectValue('accountUnitType', '');

                    // Refresh to ensure proper display
                    setTimeout(() => {
                        const unitTypeSelect = document.getElementById('accountUnitType');

                        if (unitTypeSelect) refreshSelectBox(unitTypeSelect);
                    }, 50);
                });
            }

            // Account modal event handlers
            const editAccountModal = document.getElementById('editAccountModal');
            const closeEditAccountModal = document.getElementById('closeEditAccountModal');
            const editAccountForm = document.getElementById('editAccountForm');
            const updateAccountBtn = document.getElementById('updateAccountBtn');

            // Always attach modal event listeners if modal exists (regardless of form visibility)
            if (editAccountModal && closeEditAccountModal && updateAccountBtn) {
                // Close modal
                closeEditAccountModal.addEventListener('click', function() {
                    editAccountModal.classList.remove('active');
                    editAccountModal.classList.add('opacity-0', 'pointer-events-none');
                });

            // Close modal when clicking outside
            editAccountModal.addEventListener('click', function(e) {
                if (e.target === editAccountModal) {
                    editAccountModal.classList.remove('active');
                    editAccountModal.classList.add('opacity-0', 'pointer-events-none');
                }
            });
            const editErpItemModal = document.getElementById('editErpItemModal');
            if (editErpItemModal) editErpItemModal.addEventListener('click', function(e) {
                if (e.target === editErpItemModal) {
                    editErpItemModal.classList.remove('active');
                    editErpItemModal.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            // Update item - Note: Update is handled by onclick handler in editAccount function
            // This prevents duplicate execution and ensures only one success message is shown

            // Rack Assignment Modal event handlers
            const assignRackModal = document.getElementById('assignRackModal');
            const closeAssignRackModal = document.getElementById('closeAssignRackModal');
            const assignRackForm = document.getElementById('assignRackForm');
            const saveRackAssignmentBtn = document.getElementById('saveRackAssignmentBtn');

            if (assignRackModal && closeAssignRackModal && saveRackAssignmentBtn) {
                // Close modal
                closeAssignRackModal.addEventListener('click', function() {
                    assignRackModal.classList.remove('active');
                    assignRackModal.classList.add('opacity-0', 'pointer-events-none');
                });

                // Close modal when clicking outside
                assignRackModal.addEventListener('click', function(e) {
                    if (e.target === assignRackModal) {
                        assignRackModal.classList.remove('active');
                        assignRackModal.classList.add('opacity-0', 'pointer-events-none');
                    }
                });

                // Save rack assignment
                saveRackAssignmentBtn.addEventListener('click', function() {
                    const formData = new FormData(assignRackForm);
                    const itemId = formData.get('item_id');
                    const rackId = formData.get('rack_id');

                    if (!itemId) {
                        showToast('Item ID not found', 'error');
                        return;
                    }

                    if (!rackId) {
                        showToast('Please select a rack', 'warning');
                        return;
                    }

                    // Get user's unit_id from PHP session
                    const userUnitId = <?php echo json_encode(getCurrentUser()['unit_id'] ?? null); ?>;
                    
                    if (!userUnitId) {
                        showToast('Unit ID is required for rack assignment', 'error');
                        return;
                    }

                    // Show loading state
                    saveRackAssignmentBtn.disabled = true;
                    saveRackAssignmentBtn.innerHTML = '<i class="ri-loader-4-line mr-[5px] animate-spin"></i>Saving...';

                    // Prepare data
                    const data = {
                        rack_id: rackId,
                        unit_id: userUnitId
                    };

                    // Make API call to update rack assignment
                    fetch(`../api/items/${itemId}/rack-assignment`, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showToast('✅ Rack assignment updated successfully!', 'success');
                            assignRackModal.classList.remove('active');
                            assignRackModal.classList.add('opacity-0', 'pointer-events-none');
                            loadAccounts(); // Reload the table
                        } else {
                            showToast(`❌ Failed to update rack assignment. ${result.error || 'Please try again.'}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        saveRackAssignmentBtn.disabled = false;
                        saveRackAssignmentBtn.innerHTML = '<i class="ri-save-line mr-[5px]"></i>Save';
                    });
                });
            }

                // Setup edit account main head change event listener
            setupEditMainHeadChangeListener();
        }

            // Fallback: Global event listeners for modal buttons (works regardless of form visibility)
            document.addEventListener('click', function(e) {
                // Handle close button clicks
                if (e.target && e.target.id === 'closeEditAccountModal') {
                    const modal = document.getElementById('editAccountModal');
                    if (modal) {
                        modal.classList.remove('active');
                        modal.classList.add('opacity-0', 'pointer-events-none');
                    }
                }
                
                // Handle update button clicks
                if (e.target && e.target.id === 'updateAccountBtn') {
                    // Find the update button and trigger its click event
                    const updateBtn = document.getElementById('updateAccountBtn');
                    if (updateBtn && updateBtn.onclick) {
                        updateBtn.onclick(e);
                    }
                }
            });
        });

        // Load racks for modal
        function loadRacksForModal(requiredRackId = null) {
            // Get user's unit_id from PHP session
            const userUnitId = <?php echo json_encode(getCurrentUser()['unit_id'] ?? null); ?>;
            
            // Use unit-specific API if unit_id is available
            const apiUrl = userUnitId ? `../api/racks?unit_id=${userUnitId}` : '../api/racks';
            
            return fetch(apiUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const select = document.getElementById('editAccountRack');
                        if (select) {
                            select.innerHTML = '<option value=""></option>';
                            const list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            console.log('Loaded racks for modal:', list.length, 'items');
                            
                            // Check if required rack ID is in the loaded data
                            const hasRequiredRack = requiredRackId && list.some(item => item.id == requiredRackId);
                            
                            // Add loaded items to dropdown
                            list.forEach(item => {
                                if (item && item.id && item.name) {
                                    select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                                }
                            });
                            
                            // Enhance the select if not already enhanced, then refresh
                            setTimeout(() => {
                                if (!select.__enhanced) {
                                    upgradeSelectToSearchable('editAccountRack', 'Search racks...');
                                }
                                refreshSelectBox(select);
                            }, 100);
                            
                            // If required rack ID is not in loaded data, fetch it specifically
                            if (requiredRackId && !hasRequiredRack) {
                                console.log('Required rack ID', requiredRackId, 'not found in initial data, fetching specifically...');
                                return fetchSpecificRack(requiredRackId, select);
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading racks for modal:', error);
                });
        }
        
        // Fetch specific rack by ID
        function fetchSpecificRack(rackId, selectElement) {
            return fetch(`../api/racks/${rackId}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        const rack = result.data;
                        console.log('Fetched specific rack:', rack);
                        // Add the specific rack to the dropdown
                        selectElement.innerHTML += `<option value="${rack.id}">[${rack.id}]-${rack.name}</option>`;
                        
                        // Refresh enhanced select
                        setTimeout(() => {
                            if (selectElement.__enhanced && typeof selectElement.__enhanced.refresh === 'function') {
                                selectElement.__enhanced.refresh();
                            }
                            refreshSelectBox(selectElement);
                        }, 50);
                    }
                })
                .catch(error => {
                    console.error('Error fetching specific rack:', error);
                });
        }

        // Load unit types for modal
        function loadUnitTypesForModal(requiredUnitTypeId = null) {
            return fetch('../api/unit-types', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const select = document.getElementById('editAccountUnitType');
                        if (select) {
                            select.innerHTML = '<option value=""></option>';
                            const list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            console.log('Loaded unit types for modal:', list.length, 'items');
                            
                            // Check if required unit type ID is in the loaded data
                            const hasRequiredUnitType = requiredUnitTypeId && list.some(item => item.id == requiredUnitTypeId);
                            
                            // Add loaded items to dropdown
                            list.forEach(item => {
                                if (item && item.id && item.name) {
                                    select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                                }
                            });
                            
                            // Enhance the select if not already enhanced, then refresh
                            setTimeout(() => {
                                if (!select.__enhanced) {
                                    upgradeSelectToSearchable('editAccountUnitType', 'Search unit types...');
                                }
                                refreshSelectBox(select);
                            }, 100);
                            
                            // If required unit type ID is not in loaded data, fetch it specifically
                            if (requiredUnitTypeId && !hasRequiredUnitType) {
                                console.log('Required unit type ID', requiredUnitTypeId, 'not found in initial data, fetching specifically...');
                                return fetchSpecificUnitType(requiredUnitTypeId, select);
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading unit types for modal:', error);
                });
        }
        
        // Fetch specific unit type by ID
        function fetchSpecificUnitType(unitTypeId, selectElement) {
            return fetch(`../api/unit-types/${unitTypeId}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        const unitType = result.data;
                        console.log('Fetched specific unit type:', unitType);
                        // Add the specific unit type to the dropdown
                        selectElement.innerHTML += `<option value="${unitType.id}">[${unitType.id}]-${unitType.name}</option>`;
                        
                        // Refresh enhanced select
                        setTimeout(() => {
                            if (selectElement.__enhanced && typeof selectElement.__enhanced.refresh === 'function') {
                                selectElement.__enhanced.refresh();
                            }
                            refreshSelectBox(selectElement);
                        }, 50);
                    }
                })
                .catch(error => {
                    console.error('Error fetching specific unit type:', error);
                });
        }

        // Search racks by ID or name
        function searchRacks(searchTerm, targetSelectId = null) {
            console.log('searchRacks called with:', searchTerm, 'targetSelectId:', targetSelectId);
            
            // Determine which select element to target
            let select;
            if (targetSelectId) {
                select = document.getElementById(targetSelectId);
            } else {
                // Try to detect which select is currently active/focused
                const activeElement = document.activeElement;
                if (activeElement && activeElement.closest('.relative')) {
                    const wrapper = activeElement.closest('.relative');
                    const selectEl = wrapper.previousElementSibling;
                    if (selectEl && selectEl.tagName === 'SELECT') {
                        select = selectEl;
                    }
                }
                
                // Fallback to checking which modal is open
                if (!select) {
                    const editModal = document.getElementById('editAccountModal');
                    const isEditModalOpen = editModal && editModal.classList.contains('active');
                    if (isEditModalOpen) {
                        select = document.getElementById('editAccountRack');
                    } else {
                        select = document.getElementById('assignRackSelect');
                    }
                }
            }
            
            if (!select) {
                console.log('No target select found for rack search');
                return;
            }
            
            console.log('Targeting select:', select.id);
            
            if (!searchTerm || searchTerm.trim() === '') {
                // Load initial options if search is empty
                console.log('Loading initial racks...');
                // Remove limit for edit modal, keep limit for create form
                const url = '../api/racks?limit=20';
                fetch(url, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(r => r.json()).then(result => {
                        let list = [];
                        if (result.success && result.data) {
                            list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                        }
                        select.innerHTML = '<option value=""></option>';
                        list.forEach(r => {
                            if (r.id && r.name) select.innerHTML += `<option value="${r.id}">[${r.id}]-${r.name}</option>`;
                        });
                        // Refresh the enhanced select display
                        setTimeout(() => refreshSelectBox(select), 100);
                    }).catch(() => {});
                return;
            }

            console.log('Searching racks in database for:', searchTerm);
            const params = new URLSearchParams({
                search: searchTerm.trim(),
                limit: 50
            });

            fetch(`../api/search/racks?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    console.log('Racks search result:', result);
                    if (result.success) {
                        select.innerHTML = '<option value=""></option>';
                        
                        // Handle both single object and array responses
                        let searchResults = [];
                        if (Array.isArray(result.data)) {
                            searchResults = result.data;
                        } else if (result.data && typeof result.data === 'object') {
                            // If it's a single object, wrap it in an array
                            searchResults = [result.data];
                        } else if (Array.isArray(result)) {
                            // If result itself is an array
                            searchResults = result;
                        }
                        
                        searchResults.forEach(rack => {
                            if (rack && rack.id && rack.name) {
                                select.innerHTML += `<option value="${rack.id}">[${rack.id}]-${rack.name}</option>`;
                            }
                        });

                        // Refresh enhanced select
                        if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                            select.__enhanced.refresh();
                        }
                        
                        // Force rebuild the enhanced select items with search results only
                        setTimeout(() => {
                            if (select.__enhanced && typeof select.__enhanced.buildItems === 'function') {
                                // Clear the current items list first
                                if (select.__enhanced.list) {
                                    select.__enhanced.list.innerHTML = '';
                                }
                                // Rebuild with all available options (no filter since we already have search results)
                                select.__enhanced.buildItems('');
                            }
                        }, 50);
                    }
                })
                .catch(error => {
                    console.error('Search racks error:', error);
                });
        }

        // Search unit types by ID or name
        function searchUnitTypes(searchTerm) {
            console.log('searchUnitTypes called with:', searchTerm);
            
            if (!searchTerm || searchTerm.trim() === '') {
                // Load initial options if search is empty
                const select = document.getElementById('accountUnitType') || document.getElementById('editAccountUnitType');
                if (select) {
                    console.log('Loading initial unit types...');
                    // Remove limit for edit modal, keep limit for create form
                    const isEditModal = select.id === 'editAccountUnitType';
                    const url = isEditModal ? '../api/unit-types' : '../api/unit-types?limit=20';
                    fetch(url, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json()).then(result => {
                            let list = [];
                            if (result.success && result.data) {
                                list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            }
                            select.innerHTML = '<option value=""></option>';
                            list.forEach(u => {
                                if (u.id && u.name) select.innerHTML += `<option value="${u.id}">[${u.id}]-${u.name}</option>`;
                            });
                            // Refresh the enhanced select display
                            setTimeout(() => refreshSelectBox(select), 100);
                        }).catch(() => {});
                }
                return;
            }

            console.log('Searching unit types in database for:', searchTerm);
            const params = new URLSearchParams({
                search: searchTerm.trim(),
                limit: 50
            });

            fetch(`../api/search/unit-types?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    console.log('Unit types search result:', result);
                    if (result.success) {
                        const select = document.getElementById('accountUnitType') || document.getElementById('editAccountUnitType');
                        if (select) {
                            select.innerHTML = '<option value=""></option>';
                            
                            // Handle both single object and array responses
                            let searchResults = [];
                            if (Array.isArray(result.data)) {
                                searchResults = result.data;
                            } else if (result.data && typeof result.data === 'object') {
                                // If it's a single object, wrap it in an array
                                searchResults = [result.data];
                            } else if (Array.isArray(result)) {
                                // If result itself is an array
                                searchResults = result;
                            }
                            
                            searchResults.forEach(unitType => {
                                if (unitType && unitType.id && unitType.name) {
                                    select.innerHTML += `<option value="${unitType.id}">[${unitType.id}]-${unitType.name}</option>`;
                                }
                            });

                            // Refresh enhanced select
                            if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                select.__enhanced.refresh();
                            }
                            
                            // Force rebuild the enhanced select items with search results only
                            setTimeout(() => {
                                if (select.__enhanced && typeof select.__enhanced.buildItems === 'function') {
                                    // Clear the current items list first
                                    if (select.__enhanced.list) {
                                        select.__enhanced.list.innerHTML = '';
                                    }
                                    // Rebuild with all available options (no filter since we already have search results)
                                    select.__enhanced.buildItems('');
                                }
                            }, 50);
                        }
                    }
                })
                .catch(error => {
                    console.error('Search unit types error:', error);
                });
        }

        // Search main heads by ID or name
        function searchMainHeads(searchTerm) {
            if (!searchTerm || searchTerm.trim() === '') {
                // Load initial options if search is empty
                const select = document.getElementById('accountMainHead') || document.getElementById('editAccountMainHead');
                if (select) {
                    fetch('../api/main-heads?limit=20', {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json()).then(result => {
                            let list = [];
                            if (result.success && result.data) {
                                list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            }
                            select.innerHTML = '<option value=""></option>';
                            list.forEach(item => {
                                if (item.id && item.name) select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                            });
                            // Refresh the enhanced select display
                            setTimeout(() => refreshSelectBox(select), 100);
                        }).catch(() => {});
                }
                return;
            }

            const params = new URLSearchParams({
                search: searchTerm.trim(),
                limit: 50
            });

            fetch(`../api/search/main-heads?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const select = document.getElementById('accountMainHead') || document.getElementById('editAccountMainHead');
                        if (select) {
                            select.innerHTML = '<option value=""></option>';
                            
                            // Handle both single object and array responses
                            let searchResults = [];
                            if (Array.isArray(result.data)) {
                                searchResults = result.data;
                            } else if (result.data && typeof result.data === 'object') {
                                // If it's a single object, wrap it in an array
                                searchResults = [result.data];
                            } else if (Array.isArray(result)) {
                                // If result itself is an array
                                searchResults = result;
                            }
                            
                            searchResults.forEach(item => {
                                if (item && item.id && item.name) {
                                    select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                                }
                            });

                            // Refresh enhanced select
                            if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                select.__enhanced.refresh();
                            }
                            
                            // Force rebuild the enhanced select items with search results only
                            setTimeout(() => {
                                if (select.__enhanced && typeof select.__enhanced.buildItems === 'function') {
                                    // Clear the current items list first
                                    if (select.__enhanced.list) {
                                        select.__enhanced.list.innerHTML = '';
                                    }
                                    // Rebuild with all available options (no filter since we already have search results)
                                    select.__enhanced.buildItems('');
                                }
                            }, 50);
                        }
                    }
                })
                .catch(error => {
                    console.error('Search main heads error:', error);
                });
        }

        // Search control heads by ID or name
        function searchControlHeads(searchTerm, mainHeadId = null) {
            if (!searchTerm || searchTerm.trim() === '') {
                // Load initial options if search is empty
                const select = document.getElementById('accountControlHead') || document.getElementById('editAccountControlHead');
                if (select) {
                    const url = mainHeadId ? 
                        `../api/control-heads?main_head_id=${mainHeadId}&limit=20` : 
                        '../api/control-heads?limit=20';
                    
                    fetch(url, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json()).then(result => {
                            let list = [];
                            if (result.success && result.data) {
                                list = Array.isArray(result.data.records) ? result.data.records : (Array.isArray(result.data) ? result.data : []);
                            }
                            select.innerHTML = '<option value=""></option>';
                            list.forEach(item => {
                                if (item.id && item.name) select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                            });
                            // Refresh the enhanced select display
                            setTimeout(() => refreshSelectBox(select), 100);
                        }).catch(() => {});
                }
                return;
            }

            const params = new URLSearchParams({
                search: searchTerm.trim(),
                limit: 50
            });
            
            if (mainHeadId) {
                params.append('main_head_id', mainHeadId);
            }

            fetch(`../api/search/control-heads?${params.toString()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const select = document.getElementById('accountControlHead') || document.getElementById('editAccountControlHead');
                        if (select) {
                            select.innerHTML = '<option value=""></option>';
                            
                            // Handle both single object and array responses
                            let searchResults = [];
                            if (Array.isArray(result.data)) {
                                searchResults = result.data;
                            } else if (result.data && typeof result.data === 'object') {
                                // If it's a single object, wrap it in an array
                                searchResults = [result.data];
                            } else if (Array.isArray(result)) {
                                // If result itself is an array
                                searchResults = result;
                            }
                            
                            searchResults.forEach(item => {
                                if (item && item.id && item.name) {
                                    select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                                }
                            });

                            // Refresh enhanced select
                            if (select.__enhanced && typeof select.__enhanced.refresh === 'function') {
                                select.__enhanced.refresh();
                            }
                            
                            // Force rebuild the enhanced select items with search results only
                            setTimeout(() => {
                                if (select.__enhanced && typeof select.__enhanced.buildItems === 'function') {
                                    // Clear the current items list first
                                    if (select.__enhanced.list) {
                                        select.__enhanced.list.innerHTML = '';
                                    }
                                    // Rebuild with all available options (no filter since we already have search results)
                                    select.__enhanced.buildItems('');
                                }
                            }, 50);
                        }
                    }
                })
                .catch(error => {
                    console.error('Search control heads error:', error);
                });
        }
    </script>
    <script>
        // Custom searchable dropdown (same implementation used on Misc Entries)
        (function() {
            function setSelectOptions(selectEl, options) {
                if (!selectEl) return;
                const current = selectEl.value || '';
                selectEl.innerHTML = '<option value=""></option>';
                (options || []).forEach(opt => {
                    selectEl.innerHTML += `<option value="${opt.value}">${opt.text}</option>`;
                });
                if (current) selectEl.value = current;
            }

            function upgradeSelectToSearchable(selectId, placeholder) {
                const selectEl = document.getElementById(selectId);
                if (!selectEl || selectEl.__enhanced) return;
                selectEl.classList.add('opacity-0', 'pointer-events-none', 'absolute');
                selectEl.style.position = 'absolute';
                selectEl.style.left = '-9999px';
                selectEl.style.visibility = 'hidden';
                const wrapper = document.createElement('div');
                wrapper.className = 'relative';
                const control = document.createElement('button');
                control.type = 'button';
                control.className = 'block w-full h-[30px] rounded-md text-left text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] outline-0 transition-all text-sm flex items-center justify-between focus:border-primary-500';
                const controlText = document.createElement('span');
                const placeholderText = placeholder || 'Search...';
                const initialText = selectEl.options[selectEl.selectedIndex]?.text || '';
                controlText.textContent = initialText || placeholderText;
                controlText.className = initialText ? '' : 'text-gray-400';
                const caret = document.createElement('span');
                caret.className = 'material-symbols-outlined text-gray-500 text-sm';
                caret.textContent = 'expand_more';
                control.appendChild(controlText);
                control.appendChild(caret);

                const panel = document.createElement('div');
                panel.className = 'absolute z-[9999] left-0 right-0 top-full mt-1 bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] rounded-md shadow-2xl hidden';
                const search = document.createElement('input');
                search.type = 'text';
                search.placeholder = placeholderText;
                search.className = 'w-full h-[30px] px-[12px] text-sm text-black dark:text-white bg-white dark:bg-[#0c1427] border-0 border-b border-gray-200 dark:border-[#172036] outline-0 focus:outline-0 focus:ring-0 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded-t-md';
                const list = document.createElement('div');
                list.className = 'max-h-48 overflow-y-auto py-1';
                list.style.cssText = `
                    max-height: 192px;
                    overflow-y: auto;
                    scrollbar-width: thin;
                    scrollbar-color: #cbd5e1 #f1f5f9;
                `;
                panel.appendChild(search);
                panel.appendChild(list);

                const parent = selectEl.parentNode;
                const next = selectEl.nextElementSibling;
                const beforeNode = (next && next.tagName && next.tagName.toLowerCase() === 'label') ? next : next;
                if (beforeNode) parent.insertBefore(wrapper, beforeNode);
                else parent.appendChild(wrapper);
                wrapper.appendChild(control);
                wrapper.appendChild(panel);

                let currentItems = [],
                    activeIndex = -1;

                function clearActive() {
                    if (activeIndex > -1 && currentItems[activeIndex]) currentItems[activeIndex].classList.remove('bg-gray-100', 'dark:bg-[#15203c]');
                }

                function setActive(idx) {
                    clearActive();
                    activeIndex = idx;
                    if (activeIndex > -1 && currentItems[activeIndex]) {
                        const el = currentItems[activeIndex];
                        el.classList.add('bg-gray-100', 'dark:bg-[#15203c]');
                        try {
                            el.scrollIntoView({
                                block: 'nearest'
                            });
                        } catch (_) {}
                    }
                }

                function moveActive(delta) {
                    if (!currentItems.length) {
                        return;
                    }
                    if (activeIndex === -1) {
                        setActive(delta > 0 ? 0 : currentItems.length - 1);
                        return;
                    }
                    const next = Math.max(0, Math.min(currentItems.length - 1, activeIndex + delta));
                    setActive(next);
                }

                function selectActive() {
                    if (activeIndex > -1 && currentItems[activeIndex]) currentItems[activeIndex].click();
                }

                function buildItems(filter = '') {
                    list.innerHTML = '';
                    currentItems = [];
                    activeIndex = -1;
                    const options = Array.from(selectEl.options);
                    options.forEach(opt => {
                        if (opt.value === '') return;
                        const text = opt.text || '';
                        if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;
                        const item = document.createElement('div');
                        item.className = 'px-[12px] py-[6px] text-sm text-black dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c]';
                        item.textContent = text;
                        item.dataset.value = opt.value;
                        item.addEventListener('click', (ev) => {
                            if (ev && ev.stopPropagation) ev.stopPropagation();
                            selectEl.value = opt.value;
                            controlText.textContent = text || placeholderText;
                            controlText.className = text ? '' : 'text-gray-400';
                            panel.classList.add('hidden');
                            try {
                                control.focus();
                            } catch (_) {}
                            selectEl.dispatchEvent(new Event('change'));
                            ensureLabel();
                        });
                        item.addEventListener('mouseenter', () => {
                            const i = currentItems.indexOf(item);
                            if (i > -1) setActive(i);
                        });
                        list.appendChild(item);
                        currentItems.push(item);
                    });
                }

                function open() {
                    panel.classList.remove('hidden');
                    buildItems('');
                    setTimeout(() => search.focus(), 0);
                    if (grp) grp.classList.add('is-focused');

                    // Add click outside listener
                    setTimeout(() => {
                        document.addEventListener('click', closeOnClickOutside);
                        document.addEventListener('keydown', closeOnEscape);
                    }, 0);
                }

                function close() {
                    if (!panel.classList.contains('hidden')) {
                        panel.classList.add('hidden');
                        search.value = '';
                        if (grp) grp.classList.remove('is-focused');

                        // Remove click outside listener
                        document.removeEventListener('click', closeOnClickOutside);
                        document.removeEventListener('keydown', closeOnEscape);
                    }
                }

                function closeOnClickOutside(e) {
                    if (!wrapper.contains(e.target)) {
                        close();
                    }
                }

                function closeOnEscape(e) {
                    if (e.key === 'Escape') {
                        close();
                    }
                }
                control.addEventListener('click', () => {
                    panel.classList.contains('hidden') ? open() : close();
                });
                control.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === 'NumpadEnter') {
                        if (panel.classList.contains('hidden')) {
                            // Let the global navigation handle this - don't do anything here
                            return;
                        } else {
                            e.preventDefault();
                            selectActive();
                            return;
                        }
                    }
                    if (e.key === ' ') {
                        e.preventDefault();
                        panel.classList.contains('hidden') ? open() : close();
                        return;
                    }
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (panel.classList.contains('hidden')) open();
                        else moveActive(1);
                        return;
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (panel.classList.contains('hidden')) open();
                        else moveActive(-1);
                        return;
                    }
                });
                // Some browsers don't fire keydown reliably on buttons inside forms for Enter; add keyup fallback
                control.addEventListener('keyup', (e) => {
                    if (!(e.key === 'Enter' || e.key === 'NumpadEnter')) return;
                    if (panel.classList.contains('hidden')) {
                        // Let the global navigation handle this - don't do anything here
                        return;
                    } else {
                        e.preventDefault();
                        if (activeIndex === -1) {
                            moveActive(1);
                        }
                        selectActive();
                    }
                });
                search.addEventListener('input', () => {
                    console.log('Enhanced select input event triggered for:', selectEl.id, 'with value:', search.value);
                    buildItems(search.value);

                    // Trigger database search for specific select boxes
                    const selectId = selectEl.id;
                    if (selectId === 'assignRackSelect') {
                        console.log('Calling searchRacks for:', search.value, 'with target:', selectId);
                        searchRacks(search.value, selectId);
                    } else if (selectId === 'accountUnitType' || selectId === 'editAccountUnitType') {
                        console.log('Calling searchUnitTypes for:', search.value);
                        searchUnitTypes(search.value);
                    } else if (selectId === 'accountMainHead' || selectId === 'editAccountMainHead') {
                        console.log('Calling searchMainHeads for:', search.value);
                        searchMainHeads(search.value);
                    } else if (selectId === 'accountControlHead' || selectId === 'editAccountControlHead') {
                        // For control heads, we need to get the main head ID if available
                        const mainHeadSelect = document.getElementById('accountMainHead') || document.getElementById('editAccountMainHead');
                        const mainHeadId = mainHeadSelect ? mainHeadSelect.value : null;
                        console.log('Calling searchControlHeads for:', search.value, 'with mainHeadId:', mainHeadId);
                        searchControlHeads(search.value, mainHeadId);
                    }
                });
                search.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        close();
                        control.focus();
                        return;
                    }
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        moveActive(1);
                        return;
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        moveActive(-1);
                        return;
                    }
                    if (e.key === 'Enter' || e.key === 'NumpadEnter') {
                        e.preventDefault();
                        if (activeIndex === -1) {
                            moveActive(1);
                        }
                        selectActive();
                        return;
                    }
                });

                const grp = selectEl.closest('.float-group');

                function ensureLabel() {
                    if (!grp) return;
                    if (selectEl.value && String(selectEl.value).length) {
                        grp.classList.add('is-filled');
                        selectEl.classList.add('has-value');
                    } else {
                        grp.classList.remove('is-filled');
                        selectEl.classList.remove('has-value');
                    }
                }
                selectEl.addEventListener('change', ensureLabel);
                setTimeout(ensureLabel, 0);

                // Focus styling parity with inputs
                if (grp) {
                    control.addEventListener('focus', () => grp.classList.add('is-focused'));
                    control.addEventListener('blur', () => {
                        if (panel.classList.contains('hidden')) grp.classList.remove('is-focused');
                    });
                    search.addEventListener('focus', () => grp.classList.add('is-focused'));
                    search.addEventListener('blur', () => grp.classList.remove('is-focused'));
                }

                selectEl.__enhanced = {
                    control,
                    panel,
                    list,
                    search,
                    refresh: () => buildItems(search.value || ''),
                    setDisplayFromValue: () => {
                        const t = selectEl.options[selectEl.selectedIndex]?.text || placeholderText;
                        controlText.textContent = t;
                        controlText.className = t ? '' : 'text-gray-400';
                        ensureLabel();
                    }
                };
            }

            function enhanceFloatSelects() {
                const selects = document.querySelectorAll('select[data-float-select]');
                selects.forEach(sel => {
                    try {
                        upgradeSelectToSearchable(sel.id, 'Search...');
                    } catch (_) {}
                });
            }

            function refreshSearchableSelectOptions(selectId, opts) {
                const el = document.getElementById(selectId);
                if (!el) return;
                setSelectOptions(el, opts);
                if (el.__enhanced && el.__enhanced.refresh) el.__enhanced.refresh();
                if (el.__enhanced && el.__enhanced.setDisplayFromValue) el.__enhanced.setDisplayFromValue();
            }

            document.addEventListener('DOMContentLoaded', function() {
                enhanceFloatSelects();

                // Initial page load based on access level
                if (window.hasErpFullAccess) {
                    // Full access: Categories tab is default
                    try {
                        const erpCatNameField = document.getElementById('erpCatName');
                        if (erpCatNameField) {
                            setTimeout(() => {
                                try {
                                    erpCatNameField.focus();
                                    console.log('Initial focus set on Category Name field');
                                } catch (_) {}
                            }, 100);
                        }
                    } catch (e) {
                        console.log('Error setting initial focus:', e);
                    }
                    
                    // Load Categories tab data on page load
                    setTimeout(() => {
                        if (typeof loadErpCategories === 'function') loadErpCategories();
                        if (typeof loadErpMasterDropdowns === 'function') loadErpMasterDropdowns();
                        if (typeof loadErpCategoriesTable === 'function') loadErpCategoriesTable();
                    }, 200);
                } else {
                    // Limited access: Items (ERP Structure) tab is default
                    setTimeout(() => {
                        if (typeof loadErpItems === 'function') loadErpItems();
                        console.log('Limited access: Loading Items (ERP Structure) tab');
                    }, 200);
                }
                // Observe option changes to refresh dropdown items
                document.querySelectorAll('select[data-float-select]').forEach(sel => {
                    try {
                        new MutationObserver(() => {
                            if (sel.__enhanced && sel.__enhanced.refresh) sel.__enhanced.refresh();
                        }).observe(sel, {
                            childList: true
                        });
                    } catch (_) {}
                });
                // Ensure selects in edit modals reflect current value on open and focus first field
                ['editMainHeadModal', 'editControlHeadModal', 'editAccountModal'].forEach(mid => {
                    const modal = document.getElementById(mid);
                    if (!modal) return;
                    try {
                        new MutationObserver(() => {
                            if (modal.classList.contains('active')) {
                                // Refresh enhanced selects
                                modal.querySelectorAll('select[data-float-select]').forEach(sel => {
                                    if (sel.__enhanced && sel.__enhanced.setDisplayFromValue) sel.__enhanced.setDisplayFromValue();
                                });

                                // Focus first field in modal
                                const modalSequences = {
                                    'editMainHeadModal': 'editMainHeadName',
                                    'editControlHeadModal': 'editControlHeadMainHead',
                                    'editAccountModal': 'editAccountMainHead'
                                };
                                const firstFieldId = modalSequences[mid];
                                if (firstFieldId) {
                                    setTimeout(() => {
                                        const firstField = document.getElementById(firstFieldId);
                                        if (firstField) {
                                            if (firstField.tagName === 'SELECT' && firstField.__enhanced && firstField.__enhanced.control) {
                                                firstField.__enhanced.control.focus();
                                            } else {
                                                firstField.focus();
                                                if (firstField.select) {
                                                    try {
                                                        firstField.select();
                                                    } catch (_) {}
                                                }
                                            }
                                            console.log('Modal focus set on:', firstFieldId);
                                        }
                                    }, 100);
                                }
                            }
                        }).observe(modal, {
                            attributes: true,
                            attributeFilter: ['class']
                        });
                    } catch (_) {}
                });

                // Focus first control when switching tabs as well
                document.querySelectorAll('.nav-link').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        const map = {
                            tab1: 'mainHeadForm',
                            tab2: 'controlHeadForm',
                            tab3: 'accountForm',
                            tab4: 'erpCategoryForm',
                            tab5: 'erpGroupForm',
                            tab6: 'erpSubGroupForm',
                            tab7: 'erpAttributeForm',
                            tab8: 'erpItemForm'
                        };
                        const formId = map[tabId] || 'mainHeadForm';
                        const form = document.getElementById(formId);
                        if (form) {
                            setTimeout(() => {
                                // Get the first field in the sequence for this form
                                const sequences = {
                                    'mainHeadForm': ['mainHeadName'],
                                    'controlHeadForm': ['controlHeadMainHead'],
                                    'accountForm': ['accountMainHead'],
                                    'erpCategoryForm': ['erpCatName'],
                                    'erpGroupForm': ['erpGroupCategory'],
                                    'erpSubGroupForm': ['erpSubGroupCategory'],
                                    'erpAttributeForm': ['erpAttrCategory'],
                                    'erpItemForm': ['erpCategory']
                                };
                                const firstFieldId = sequences[formId] ? sequences[formId][0] : null;
                                if (firstFieldId) {
                                    const firstField = document.getElementById(firstFieldId);
                                    if (firstField) {
                                        if (firstField.tagName === 'SELECT' && firstField.__enhanced && firstField.__enhanced.control) {
                                            firstField.__enhanced.control.focus();
                                        } else {
                                            firstField.focus();
                                            if (firstField.select) {
                                                try {
                                                    firstField.select();
                                                } catch (_) {}
                                            }
                                        }
                                        console.log('Tab switch focus set on:', firstFieldId);
                                    }
                                }

                                // Refresh select boxes when switching to Items tab
                                if (tabId === 'tab3') {
                                    setTimeout(() => {
                                        const unitTypeSelect = document.getElementById('accountUnitType');
                                        const mainHeadSelect = document.getElementById('accountMainHead');
                                        const controlHeadSelect = document.getElementById('accountControlHead');

                                        if (unitTypeSelect) refreshSelectBox(unitTypeSelect);
                                        if (mainHeadSelect) refreshSelectBox(mainHeadSelect);
                                        if (controlHeadSelect) refreshSelectBox(controlHeadSelect);
                                    }, 200);
                                }
                                // Load ERP tab data when switching to main tabs
                                if (tabId === 'tab4') {
                                    setTimeout(() => {
                                        if (typeof loadErpCategories === 'function') loadErpCategories();
                                        if (typeof loadErpMasterDropdowns === 'function') loadErpMasterDropdowns();
                                        if (typeof loadErpCategoriesTable === 'function') loadErpCategoriesTable();
                                    }, 150);
                                } else if (tabId === 'tab5') {
                                    setTimeout(() => {
                                        if (typeof loadErpMasterDropdowns === 'function') loadErpMasterDropdowns();
                                        if (typeof loadErpGroupsTable === 'function') loadErpGroupsTable(document.getElementById('erpGroupCategory')?.value);
                                    }, 150);
                                } else if (tabId === 'tab6') {
                                    setTimeout(() => {
                                        if (typeof loadErpMasterDropdowns === 'function') loadErpMasterDropdowns();
                                        if (typeof loadErpSubGroupsTable === 'function') loadErpSubGroupsTable(document.getElementById('erpSubGroupGroup')?.value);
                                    }, 150);
                                } else if (tabId === 'tab7') {
                                    setTimeout(() => {
                                        if (typeof loadErpMasterDropdowns === 'function') loadErpMasterDropdowns();
                                        if (typeof loadErpAttributesTable === 'function') loadErpAttributesTable(document.getElementById('erpAttrSubGroup')?.value);
                                    }, 150);
                                } else if (tabId === 'tab8') {
                                    setTimeout(() => {
                                        if (typeof loadErpCategories === 'function') loadErpCategories();
                                        if (typeof loadErpUnitTypes === 'function') loadErpUnitTypes();
                                        if (typeof loadErpItems === 'function') loadErpItems();
                                    }, 150);
                                }
                            }, 120);
                        }
                    });
                });


            });

            // Expose helpers if needed elsewhere on page
            window.refreshSearchableSelectOptions = window.refreshSearchableSelectOptions || refreshSearchableSelectOptions;
            window.upgradeSelectToSearchable = upgradeSelectToSearchable;
        })();

        // Global helper functions for edit modals
        function setEnhancedSelectValue(selectId, value) {
            const el = document.getElementById(selectId);
            if (!el) {
                console.log('setEnhancedSelectValue: Element not found:', selectId);
                return;
            }
            
            console.log('setEnhancedSelectValue: Setting', selectId, 'to value:', value);
            
            // Handle empty/null values
            if (!value || value === '' || value === '0') {
                el.value = '';
                try {
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (_) {}
                return;
            }
            
            // Check if the option exists in the select
            const optionExists = el.querySelector(`option[value="${value}"]`);
            if (!optionExists) {
                console.log('setEnhancedSelectValue: Option not found for value', value, 'in', selectId, '- will retry');
                // Retry after a short delay (max 3 retries)
                if (!el._retryCount) el._retryCount = 0;
                if (el._retryCount < 3) {
                    el._retryCount++;
                    setTimeout(() => {
                        setEnhancedSelectValue(selectId, value);
                    }, 150);
                } else {
                    console.log('setEnhancedSelectValue: Max retries reached for', selectId);
                }
                return;
            }
            
            // Reset retry count on success
            el._retryCount = 0;
            
            // Set the value
            el.value = String(value);
            
            // Dispatch change event
            try {
                el.dispatchEvent(new Event('change', { bubbles: true }));
            } catch (_) {}
            
            // Update enhanced select display if available
            if (el.__enhanced && typeof el.__enhanced.setDisplayFromValue === 'function') {
                console.log('setEnhancedSelectValue: Calling setDisplayFromValue for', selectId);
                el.__enhanced.setDisplayFromValue();
            }
            
            // Force refresh the enhanced select if it exists
            if (el.__enhanced && typeof el.__enhanced.refresh === 'function') {
                console.log('setEnhancedSelectValue: Calling refresh for', selectId);
                el.__enhanced.refresh();
            }
            
            // Additional trigger for enhanced selects
            try {
                if (typeof jQuery !== 'undefined' && jQuery(el).length) {
                    jQuery(el).trigger('change');
                }
            } catch (_) {}
            
            console.log('setEnhancedSelectValue: Final value for', selectId, ':', el.value);
        }

        function enhanceModalSelects(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            
            // Cache the selects query to avoid repeated DOM queries
            const selects = modal.querySelectorAll('select[data-float-select]');
            if (selects.length === 0) return;
            
            selects.forEach(sel => {
                if (!sel.__enhanced) {
                    try {
                        // Call the upgradeSelectToSearchable function from the global scope
                        if (typeof window.upgradeSelectToSearchable === 'function') {
                            window.upgradeSelectToSearchable(sel.id, 'Search...');
                        }
                    } catch (_) {}
                }
            });

            // Ensure modal buttons are not affected by the enhancement
            setTimeout(() => {
                const closeBtn = document.getElementById('closeEditAccountModal');
                const updateBtn = document.getElementById('updateAccountBtn');
                
                if (closeBtn) {
                    closeBtn.style.pointerEvents = 'auto';
                    closeBtn.disabled = false;
                    console.log('enhanceModalSelects: Ensured close button is enabled');
                }
                
                if (updateBtn) {
                    updateBtn.style.pointerEvents = 'auto';
                    updateBtn.disabled = false;
                    console.log('enhanceModalSelects: Ensured update button is enabled');
                }
            }, 100);
        }

        // Expose functions to global scope
        window.setEnhancedSelectValue = setEnhancedSelectValue;
        window.enhanceModalSelects = enhanceModalSelects;
        window.searchRacks = searchRacks;
        window.searchUnitTypes = searchUnitTypes;
        window.searchMainHeads = searchMainHeads;
        window.searchControlHeads = searchControlHeads;

        // ======================== ERP STRUCTURE TAB (tab4) ========================
        let erpItemsCurrentPage = 1;
        let erpItemsCurrentLimit = 25;
        let erpItemsCurrentSearch = '';
        
        // Pagination state for ERP master tables
        const erpTablePageSize = 10;
        let erpCategoriesAllData = [];
        let erpCategoriesCurrentPage = 1;
        let erpGroupsAllData = [];
        let erpGroupsCurrentPage = 1;
        let erpSubGroupsAllData = [];
        let erpSubGroupsCurrentPage = 1;
        let erpAttributesAllData = [];
        let erpAttributesCurrentPage = 1;
        
        // Function to check if user can delete (only SUA role)
        function canErpDelete() {
            return window.defaultRoleStatus === 'SUA';
        }
        
        // Function to check if user can edit ERP items
        // Full access users OR SA users (with any ID) can edit
        function canErpEdit() {
            return window.canEditErpItems === true;
        }
        
        // Function to check if user can assign rack (all users can assign rack)
        function canErpAssignRack() {
            return window.defaultRoleStatus !== 'PRT' && window.defaultRoleStatus !== 'PRA';
        }
        
        // For PRT/PRA: category dropdowns show only categories with code 'FG' or 'RM'
        function filterCategoriesForRole(list) {
            if (!Array.isArray(list)) return list || [];
            if (window.defaultRoleStatus === 'PRT' || window.defaultRoleStatus === 'PRA') {
                return list.filter(c => { const code = (c.code || '').toString().toUpperCase(); return code === 'FG' || code === 'RM'; });
            }
            return list;
        }
        
        // Generic pagination renderer (matching purchase_entries.php design)
        function renderErpPaginationControls(containerId, currentPage, totalPages, onPageChange) {
            const container = document.getElementById(containerId);
            if (!container) return;
            if (totalPages <= 1) { container.innerHTML = ''; return; }
            
            const safeTotalPages = totalPages < 1 ? 1 : totalPages;
            const safeCurrentPage = currentPage < 1 ? 1 : (currentPage > safeTotalPages ? safeTotalPages : currentPage);
            
            const maxButtons = 5;
            let start = Math.max(1, safeCurrentPage - Math.floor((maxButtons - 1) / 2));
            let end = Math.min(safeTotalPages, start + maxButtons - 1);
            start = Math.max(1, Math.min(start, end - maxButtons + 1));
            
            const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
            const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';
            
            let html = '';
            // First Page
            html += `<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0"><a href="javascript:void(0);" data-page="1" class="${btnCls} ${safeCurrentPage <= 1 ? 'opacity-50 pointer-events-none' : ''}" title="First page"><span class="opacity-0">0</span><i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">first_page</i></a></li>`;
            // Prev
            html += `<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0"><a href="javascript:void(0);" data-page="${safeCurrentPage - 1}" class="${btnCls} ${safeCurrentPage <= 1 ? 'opacity-50 pointer-events-none' : ''}" title="Previous page"><span class="opacity-0">0</span><i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_left</i></a></li>`;
            // Page numbers
            for (let p = start; p <= end; p++) {
                const cls = p === safeCurrentPage ? activeCls : btnCls;
                html += `<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0"><a href="javascript:void(0);" data-page="${p}" class="${cls}">${p}</a></li>`;
            }
            // Next
            html += `<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0"><a href="javascript:void(0);" data-page="${safeCurrentPage + 1}" class="${btnCls} ${safeCurrentPage >= safeTotalPages ? 'opacity-50 pointer-events-none' : ''}" title="Next page"><span class="opacity-0">0</span><i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_right</i></a></li>`;
            // Last Page
            html += `<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0"><a href="javascript:void(0);" data-page="${safeTotalPages}" class="${btnCls} ${safeCurrentPage >= safeTotalPages ? 'opacity-50 pointer-events-none' : ''}" title="Last page"><span class="opacity-0">0</span><i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">last_page</i></a></li>`;
            
            container.innerHTML = html;
            container.querySelectorAll('a[data-page]').forEach(a => {
                const targetPage = parseInt(a.getAttribute('data-page'), 10);
                a.onclick = () => {
                    if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= safeTotalPages && targetPage !== safeCurrentPage) {
                        onPageChange(targetPage);
                    }
                };
            });
        }

        function loadErpCategories() {
            fetch('../api/items/categories', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) { showToast(res.error || 'Failed to load categories', 'error'); return; }
                    const list = filterCategoriesForRole(res.data || []);
                    const sel = document.getElementById('erpCategory');
                    if (!sel) return;
                    sel.innerHTML = '<option value="">Select Category</option>';
                    list.forEach(c => { sel.innerHTML += `<option value="${c.id}">${c.name}</option>`; });
                    sel.disabled = false;
                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpCategory', 'Search categories...');
                })
                .catch(e => { console.error(e); showToast('Failed to load categories', 'error'); });
        }

        function loadErpGroups(categoryId) {
            resetErpSkuField();
            const sel = document.getElementById('erpGroup');
            if (!sel) return;
            sel.innerHTML = '<option value="">Select Group</option>';
            sel.disabled = true;
            const sg = document.getElementById('erpSubGroup');
            if (sg) { sg.innerHTML = '<option value="">Select Sub-Group</option>'; sg.disabled = true; }
            if (!categoryId) return;
            fetch(`../api/items/groups?category_id=${encodeURIComponent(categoryId)}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;
                    (res.data || []).forEach(g => { sel.innerHTML += `<option value="${g.id}">${g.name}</option>`; });
                    sel.disabled = false;
                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpGroup', 'Search groups...');
                })
                .catch(e => { console.error(e); });
        }

        function loadErpSubGroups(groupId) {
            resetErpSkuField();
            const sel = document.getElementById('erpSubGroup');
            if (!sel) return;
            sel.innerHTML = '<option value="">Select Sub-Group</option>';
            sel.disabled = true;
            // Clear attribute fields when group changes
            const attrFields = document.getElementById('erpAttributeFields');
            if (attrFields) attrFields.innerHTML = '';
            if (!groupId) return;
            fetch(`../api/items/sub-groups?group_id=${encodeURIComponent(groupId)}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;
                    (res.data || []).forEach(sg => { sel.innerHTML += `<option value="${sg.id}">${sg.name}</option>`; });
                    sel.disabled = false;
                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpSubGroup', 'Search sub-groups...');
                })
                .catch(e => { console.error(e); });
        }

        // Default SKU field HTML (one readonly input + label) for restoring when category/group change
        const defaultErpSkuFieldHTML = '<input type="text" id="erpNormalizedSku" name="normalized_sku" readonly class="h-[30px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full text-sm outline-0 transition-all focus:border-primary-500" placeholder="Select Category, Group & Sub-Group to see next SKU">';

        function resetErpSkuField() {
            const wrapper = document.getElementById('erpSkuFieldWrapper');
            if (wrapper) {
                wrapper.classList.remove('erp-sku-reel-group');
                wrapper.innerHTML = defaultErpSkuFieldHTML;
            }
        }

        // Fetch and show next generated SKU in the create-item form (before save)
        // For group code 'REEL': show prefix + editable number-only input so user can change the number part
        function fetchAndShowNextErpSku() {
            const categoryId = document.getElementById('erpCategory')?.value;
            const groupId = document.getElementById('erpGroup')?.value;
            const subGroupId = document.getElementById('erpSubGroup')?.value;
            const wrapper = document.getElementById('erpSkuFieldWrapper');
            if (!wrapper) return;
            if (!categoryId || !groupId || !subGroupId) {
                resetErpSkuField();
                return;
            }
            fetch('../api/items/generate-sku', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: categoryId, group_id: groupId, sub_group_id: subGroupId })
            })
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.data || !res.data.normalized_sku) {
                        resetErpSkuField();
                        const skuEl = document.getElementById('erpNormalizedSku');
                        if (skuEl) skuEl.value = '';
                        return;
                    }
                    const sku = res.data.normalized_sku;
                    const parts = sku.split('-');
                    // SKU format: categoryCode-groupCode-subGroupCode-number; index 1 is group code
                    const isReelGroup = parts.length >= 4 && (parts[1] || '').toUpperCase() === 'REEL';
                    if (isReelGroup) {
                        const prefix = parts.slice(0, 3).join('-') + '-';
                        const numberPart = parts[3] || '';
                        wrapper.innerHTML =
                            '<div class="erp-sku-reel-row flex flex-nowrap items-center w-full min-h-[30px] h-[30px] rounded-md border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] overflow-hidden box-border px-0 focus-within:border-primary-500 transition-all">' +
                            '<span id="erpNormalizedSkuPrefix" class="erp-sku-prefix shrink-0 flex items-center h-full pl-[12px] pr-[8px] text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">' + prefix + '</span>' +
                            '<input type="text" id="erpNormalizedSkuNumber" inputmode="numeric" pattern="[0-9]*" maxlength="12" value="' + (numberPart || '') + '" ' +
                            'class="erp-sku-number h-full flex-1 min-w-[60px] rounded-none border-0 bg-transparent text-black dark:text-white pl-0 pr-[12px] text-sm outline-0 focus:ring-0 focus:border-0" ' +
                            'placeholder="Number only">' +
                            '</div>';
                        wrapper.classList.add('erp-sku-reel-group');
                        const numEl = document.getElementById('erpNormalizedSkuNumber');
                        if (numEl) {
                            numEl.addEventListener('input', function() {
                                this.value = (this.value || '').replace(/\D/g, '');
                            });
                        }
                    } else {
                        resetErpSkuField();
                        const skuEl = document.getElementById('erpNormalizedSku');
                        if (skuEl) skuEl.value = sku;
                    }
                })
                .catch(() => { resetErpSkuField(); });
        }

        // Load dynamic attribute fields for a sub-group (Items ERP Structure form)
        function loadErpAttributeFields(subGroupId) {
            const container = document.getElementById('erpAttributeFields');
            if (!container) return;
            container.innerHTML = '';
            if (!subGroupId) return;
            
            fetch(`../api/items/attributes?sub_group_id=${encodeURIComponent(subGroupId)}`, { 
                method: 'GET', 
                credentials: 'same-origin', 
                headers: { 'Content-Type': 'application/json' } 
            })
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.data || res.data.length === 0) {
                        container.innerHTML = '';
                        return;
                    }
                    const attrs = res.data;
                    let html = '<div class="col-span-full border-t border-gray-200 dark:border-[#172036] pt-[10px] mt-[5px]"><p class="text-sm text-gray-600 dark:text-gray-400 mb-[10px] font-medium">Attributes for this Sub-Group:</p></div>';
                    html += '<div class="col-span-full grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-[10px]">';
                    attrs.forEach(attr => {
                        const req = attr.is_required ? 'required' : '';
                        const reqLabel = attr.is_required ? ' *' : '';
                        html += `
                            <div class="mb-[10px] float-group">
                                <input type="text" id="erpAttrVal_${attr.id}" name="attr_${attr.id}" ${req}
                                    data-attribute-id="${attr.id}"
                                    data-attribute-name="${attr.attribute_name}"
                                    class="erp-attribute-input h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm"
                                    placeholder="">
                                <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">${attr.attribute_name}${reqLabel}</label>
                            </div>`;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                })
                .catch(e => { console.error('Failed to load attributes:', e); });
        }

        // Load attribute fields for Edit ERP Item modal with existing values
        function loadEditErpAttributeFields(subGroupId, existingValues) {
            const container = document.getElementById('editErpAttributeFields');
            if (!container) return;
            container.innerHTML = '';
            if (!subGroupId) return;
            
            // Create a map of existing values by attribute_id
            const valueMap = {};
            if (existingValues && Array.isArray(existingValues)) {
                existingValues.forEach(v => { 
                    valueMap[v.attribute_id] = v.value; 
                });
            }
            
            fetch(`../api/items/attributes?sub_group_id=${encodeURIComponent(subGroupId)}`, { 
                method: 'GET', 
                credentials: 'same-origin', 
                headers: { 'Content-Type': 'application/json' } 
            })
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.data || res.data.length === 0) {
                        container.innerHTML = '';
                        return;
                    }
                    const attrs = res.data;
                    let html = '<div class="border-t border-gray-200 dark:border-[#172036] pt-[10px]"><p class="text-sm text-gray-600 dark:text-gray-400 mb-[10px] font-medium">Attributes:</p></div>';
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[15px]">';
                    attrs.forEach(attr => {
                        const req = attr.is_required ? 'required' : '';
                        const reqLabel = attr.is_required ? ' *' : '';
                        const existingVal = valueMap[attr.id] || '';
                        html += `
                            <div class="relative float-group">
                                <input type="text" id="editErpAttrVal_${attr.id}" name="edit_attr_${attr.id}" ${req}
                                    data-attribute-id="${attr.id}"
                                    data-attribute-name="${attr.attribute_name}"
                                    value="${existingVal.replace(/"/g, '&quot;')}"
                                    class="edit-erp-attribute-input h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"
                                    placeholder="">
                                <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">${attr.attribute_name}${reqLabel}</label>
                            </div>`;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                })
                .catch(e => { console.error('Failed to load edit attributes:', e); });
        }

        function loadErpMasterDropdowns() {
            return fetch('../api/items/categories', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;
                    const list = filterCategoriesForRole(res.data || []);
                    const ids = ['erpGroupCategory','erpSubGroupCategory','erpAttrCategory'];
                    ids.forEach(id => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        el.innerHTML = '<option value="">Select Category</option>';
                        list.forEach(c => { el.innerHTML += `<option value="${c.id}">${c.name}</option>`; });
                        // Upgrade to searchable select
                        if (typeof window.upgradeSelectToSearchable === 'function') {
                            window.upgradeSelectToSearchable(id, 'Search categories...');
                        }
                    });
                })
                .catch(() => {});
        }

        function loadErpCategoriesTable() {
            fetch('../api/items/categories', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    erpCategoriesAllData = res.success ? (res.data || []) : [];
                    erpCategoriesCurrentPage = 1;
                    renderErpCategoriesPage();
                })
                .catch(() => {});
        }
        function renderErpCategoriesPage() {
            const tbody = document.getElementById('erpCategoriesTableBody');
            const info = document.getElementById('erpCategoriesPaginationInfo');
            if (!tbody) return;
            const list = erpCategoriesAllData;
            const total = list.length;
            const totalPages = Math.ceil(total / erpTablePageSize);
            const start = (erpCategoriesCurrentPage - 1) * erpTablePageSize;
            const pageData = list.slice(start, start + erpTablePageSize);
            tbody.innerHTML = pageData.length === 0 ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No categories yet.</td></tr>'
                : pageData.map(c => `<tr class="border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]"><td class="px-[20px] py-[10px] text-sm">${c.id}</td><td class="px-[20px] py-[10px] text-sm">${c.name || '-'}</td><td class="px-[20px] py-[10px] text-sm">${c.code || '-'}</td><td class="px-[20px] py-[10px] text-sm">${c.description || '-'}</td><td class="px-[20px] py-[10px]"><button type="button" class="text-blue-500 hover:text-blue-600" onclick="erpEditCategory(${c.id},'${(c.name||'').replace(/'/g,"\\'")}','${(c.code||'').replace(/'/g,"\\'")}','${(c.description||'').replace(/'/g,"\\'")}')"><i class="material-symbols-outlined text-sm">edit</i></button>${canErpDelete() ? ` <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-2 rtl:mr-2" onclick="erpDeleteCategory(${c.id})"><i class="material-symbols-outlined text-sm">delete</i></button>` : ''}</td></tr>`).join('');
            if (info) info.textContent = `Showing ${start+1}-${Math.min(start+erpTablePageSize,total)} of ${total} category(ies)`;
            renderErpPaginationControls('erpCategoriesPaginationControls', erpCategoriesCurrentPage, totalPages, (p)=>{ erpCategoriesCurrentPage=p; renderErpCategoriesPage(); });
        }

        function loadErpGroupsTable(categoryId) {
            const t = document.getElementById('erpGroupsTableBody');
            const info = document.getElementById('erpGroupsPaginationInfo');
            const controls = document.getElementById('erpGroupsPaginationControls');
            if (!categoryId) { if (t) t.innerHTML = '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">Select a category first.</td></tr>'; if (info) info.textContent = 'Select a category to view groups'; if(controls)controls.innerHTML=''; return; }
            fetch(`../api/items/groups?category_id=${categoryId}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    erpGroupsAllData = res.success ? (res.data || []) : [];
                    erpGroupsCurrentPage = 1;
                    renderErpGroupsPage();
                })
                .catch(() => {});
        }
        function renderErpGroupsPage() {
            const t = document.getElementById('erpGroupsTableBody');
            const info = document.getElementById('erpGroupsPaginationInfo');
            if (!t) return;
            const list = erpGroupsAllData;
            const total = list.length;
            const totalPages = Math.ceil(total / erpTablePageSize);
            const start = (erpGroupsCurrentPage - 1) * erpTablePageSize;
            const pageData = list.slice(start, start + erpTablePageSize);
            t.innerHTML = pageData.length === 0 ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No groups for this category.</td></tr>'
                : pageData.map(g => `<tr class="border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]"><td class="px-[20px] py-[10px] text-sm">${g.id}</td><td class="px-[20px] py-[10px] text-sm">${g.category_name||'-'}</td><td class="px-[20px] py-[10px] text-sm">${g.name || '-'}</td><td class="px-[20px] py-[10px] text-sm">${g.code || '-'}</td><td class="px-[20px] py-[10px]"><button type="button" class="text-blue-500 hover:text-blue-600" onclick="erpEditGroup(${g.id},${g.category_id},'${(g.name||'').replace(/'/g,"\\'")}','${(g.code||'').replace(/'/g,"\\'")}')"><i class="material-symbols-outlined text-sm">edit</i></button>${canErpDelete() ? ` <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-2 rtl:mr-2" onclick="erpDeleteGroup(${g.id})"><i class="material-symbols-outlined text-sm">delete</i></button>` : ''}</td></tr>`).join('');
            if (info) info.textContent = total > 0 ? `Showing ${start+1}-${Math.min(start+erpTablePageSize,total)} of ${total} group(s)` : 'No groups';
            renderErpPaginationControls('erpGroupsPaginationControls', erpGroupsCurrentPage, totalPages, (p)=>{ erpGroupsCurrentPage=p; renderErpGroupsPage(); });
        }

        function loadErpSubGroupsTable(groupId) {
            const t = document.getElementById('erpSubGroupsTableBody');
            const info = document.getElementById('erpSubGroupsPaginationInfo');
            const controls = document.getElementById('erpSubGroupsPaginationControls');
            if (!groupId) { if (t) t.innerHTML = '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">Select a group first.</td></tr>'; if (info) info.textContent = 'Select a group to view sub-groups'; if(controls)controls.innerHTML=''; return; }
            fetch(`../api/items/sub-groups?group_id=${groupId}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    erpSubGroupsAllData = res.success ? (res.data || []) : [];
                    erpSubGroupsCurrentPage = 1;
                    renderErpSubGroupsPage();
                })
                .catch(() => {});
        }
        function renderErpSubGroupsPage() {
            const t = document.getElementById('erpSubGroupsTableBody');
            const info = document.getElementById('erpSubGroupsPaginationInfo');
            if (!t) return;
            const list = erpSubGroupsAllData;
            const total = list.length;
            const totalPages = Math.ceil(total / erpTablePageSize);
            const start = (erpSubGroupsCurrentPage - 1) * erpTablePageSize;
            const pageData = list.slice(start, start + erpTablePageSize);
            t.innerHTML = pageData.length === 0 ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No sub-groups for this group.</td></tr>'
                : pageData.map(sg => `<tr class="border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]"><td class="px-[20px] py-[10px] text-sm">${sg.id}</td><td class="px-[20px] py-[10px] text-sm">${sg.group_name||'-'}</td><td class="px-[20px] py-[10px] text-sm">${sg.name || '-'}</td><td class="px-[20px] py-[10px] text-sm">${sg.code || '-'}</td><td class="px-[20px] py-[10px]"><button type="button" class="text-blue-500 hover:text-blue-600" onclick="erpEditSubGroup(${sg.id},${sg.group_id},'${(sg.name||'').replace(/'/g,"\\'")}','${(sg.code||'').replace(/'/g,"\\'")}')"><i class="material-symbols-outlined text-sm">edit</i></button>${canErpDelete() ? ` <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-2 rtl:mr-2" onclick="erpDeleteSubGroup(${sg.id})"><i class="material-symbols-outlined text-sm">delete</i></button>` : ''}</td></tr>`).join('');
            if (info) info.textContent = total > 0 ? `Showing ${start+1}-${Math.min(start+erpTablePageSize,total)} of ${total} sub-group(s)` : 'No sub-groups';
            renderErpPaginationControls('erpSubGroupsPaginationControls', erpSubGroupsCurrentPage, totalPages, (p)=>{ erpSubGroupsCurrentPage=p; renderErpSubGroupsPage(); });
        }

        function loadErpAttributesTable(subGroupId) {
            const t = document.getElementById('erpAttributesTableBody');
            const info = document.getElementById('erpAttributesPaginationInfo');
            const controls = document.getElementById('erpAttributesPaginationControls');
            if (!subGroupId) { if (t) t.innerHTML = '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">Select a sub-group first.</td></tr>'; if (info) info.textContent = 'Select a sub-group to view attributes'; if(controls)controls.innerHTML=''; return; }
            fetch(`../api/items/attributes?sub_group_id=${subGroupId}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    erpAttributesAllData = res.success ? (res.data || []) : [];
                    erpAttributesCurrentPage = 1;
                    renderErpAttributesPage();
                })
                .catch(() => {});
        }
        function renderErpAttributesPage() {
            const t = document.getElementById('erpAttributesTableBody');
            const info = document.getElementById('erpAttributesPaginationInfo');
            if (!t) return;
            const list = erpAttributesAllData;
            const total = list.length;
            const totalPages = Math.ceil(total / erpTablePageSize);
            const start = (erpAttributesCurrentPage - 1) * erpTablePageSize;
            const pageData = list.slice(start, start + erpTablePageSize);
            t.innerHTML = pageData.length === 0 ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No attributes for this sub-group.</td></tr>'
                : pageData.map(a => `<tr class="border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]"><td class="px-[20px] py-[10px] text-sm">${a.id}</td><td class="px-[20px] py-[10px] text-sm">${a.sub_group_name||'-'}</td><td class="px-[20px] py-[10px] text-sm">${a.attribute_name || '-'}</td><td class="px-[20px] py-[10px] text-sm">${a.is_required?'Yes':'No'}</td><td class="px-[20px] py-[10px]"><button type="button" class="text-blue-500 hover:text-blue-600" onclick="erpEditAttribute(${a.id},${a.sub_group_id},'${(a.attribute_name||'').replace(/'/g,"\\'")}',${a.is_required?1:0})"><i class="material-symbols-outlined text-sm">edit</i></button>${canErpDelete() ? ` <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-2 rtl:mr-2" onclick="erpDeleteAttribute(${a.id})"><i class="material-symbols-outlined text-sm">delete</i></button>` : ''}</td></tr>`).join('');
            if (info) info.textContent = total > 0 ? `Showing ${start+1}-${Math.min(start+erpTablePageSize,total)} of ${total} attribute(s)` : 'No attributes';
            renderErpPaginationControls('erpAttributesPaginationControls', erpAttributesCurrentPage, totalPages, (p)=>{ erpAttributesCurrentPage=p; renderErpAttributesPage(); });
        }

        function loadErpUnitTypes() {
            const sel = document.getElementById('erpUnitType');
            if (!sel) return;
            const accSel = document.getElementById('accountUnitType');
            if (accSel && accSel.options.length > 1) {
                sel.innerHTML = accSel.innerHTML;
                return;
            }
            fetch('../api/unit-types?limit=500', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    const list = (res.data && res.data.records) ? res.data.records : (res.records || res.data || res || []);
                    sel.innerHTML = '<option value="">Select Unit Type</option>';
                    (Array.isArray(list) ? list : []).forEach(u => {
                        if (u && u.id) sel.innerHTML += `<option value="${u.id}">${u.name || u.name_in_urdu || u.id}</option>`;
                    });
                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpUnitType', 'Search unit types...');
                })
                .catch(() => {});
        }

        function loadErpItems() {
            const categoryId = document.getElementById('erpCategory')?.value;
            const tbody = document.getElementById('erpItemsTableBody');
            const info = document.getElementById('erpItemsPaginationInfo');
            const controls = document.getElementById('erpItemsPaginationControls');
            if (!categoryId) {
                if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="px-[20px] py-[12px] text-gray-500 text-center">Select a category to view items.</td></tr>';
                if (info) info.textContent = 'Select a category to view items';
                if (controls) controls.innerHTML = '';
                return;
            }
            const params = new URLSearchParams({ page: erpItemsCurrentPage, limit: erpItemsCurrentLimit, sort_by: 'id', sort_order: 'desc', category_id: categoryId });
            if (erpItemsCurrentSearch) params.append('search', erpItemsCurrentSearch);
            const unitId = document.getElementById('unitId')?.value || window.currentUnitId;
            if (unitId) params.append('unit_id', unitId);

            fetch(`../api/items?${params}`, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) { showToast(res.error || 'Failed to load items', 'error'); return; }
                    const data = res.data || {};
                    const records = data.records || [];
                    const total = data.total || 0;
                    const tbody = document.getElementById('erpItemsTableBody');
                    const info = document.getElementById('erpItemsPaginationInfo');
                    const controls = document.getElementById('erpItemsPaginationControls');
                    if (tbody) {
                        tbody.innerHTML = records.length === 0
                            ? '<tr><td colspan="6" class="px-[20px] py-[12px] text-gray-500 text-center">No items found. Run migrations and add items.</td></tr>'
                            : records.map(it => `
                                <tr class="border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]">
                                    <td class="px-[20px] py-[10px] text-sm">${(it.source_id || it.normalized_sku || it.legacy_code || it.code || '-')}</td>
                                    <td class="px-[20px] py-[10px] text-sm">${(it.name || '')}</td>
                                    <td class="px-[20px] py-[10px] text-sm">${(it.category_name || '-')}</td>
                                    <td class="px-[20px] py-[10px] text-sm">${(it.group_name || '-')}</td>
                                    <td class="px-[20px] py-[10px] text-sm">${(it.sub_group_name || '-')}</td>
                                    <td class="px-[20px] py-[10px]">
                                        ${canErpEdit() ? `<button type="button" class="text-blue-500 hover:text-blue-600" onclick="if(window.editErpItem) editErpItem(${it.id});" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>` : ''}
                                        ${canErpAssignRack() ? `<button type="button" class="text-green-500 hover:text-green-600 ${canErpEdit() ? 'ltr:ml-2 rtl:mr-2' : ''}" onclick="assignRack(${it.id});" title="Assign/Edit Rack"><i class="material-symbols-outlined text-sm">location_on</i></button>` : ''}
                                        ${canErpDelete() ? `<button type="button" class="text-red-500 hover:text-red-600 ltr:ml-2 rtl:mr-2" onclick="if(window.deleteErpItem) deleteErpItem(${it.id});" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>` : ''}
                                    </td>
                                </tr>`).join('');
                    }
                    const start = (erpItemsCurrentPage - 1) * erpItemsCurrentLimit;
                    if (info) info.textContent = total > 0 ? `Showing ${start + 1}-${Math.min(start + records.length, total)} of ${total} items` : 'No items';
                    const totalPages = Math.ceil(total / erpItemsCurrentLimit);
                    renderErpPaginationControls('erpItemsPaginationControls', erpItemsCurrentPage, totalPages, (p) => { erpItemsCurrentPage = p; loadErpItems(); });
                })
                .catch(e => { console.error(e); showToast('Failed to load items', 'error'); });
        }

        (function initErpTab() {
            const cat = document.getElementById('erpCategory');
            const grp = document.getElementById('erpGroup');
            const sg = document.getElementById('erpSubGroup');
            if (cat) cat.addEventListener('change', function() {
                loadErpGroups(this.value);
                erpItemsCurrentPage = 1;
                loadErpItems();
            });
            if (grp) grp.addEventListener('change', function() { loadErpSubGroups(this.value); });
            if (sg) sg.addEventListener('change', function() { loadErpAttributeFields(this.value); fetchAndShowNextErpSku(); });

            const form = document.getElementById('erpItemForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const categoryId = document.getElementById('erpCategory')?.value;
                    const groupId = document.getElementById('erpGroup')?.value;
                    const subGroupId = document.getElementById('erpSubGroup')?.value;
                    const name = document.getElementById('erpItemName')?.value;
                    const nameUrdu = document.getElementById('erpItemNameUrdu')?.value;
                    const unitTypeId = document.getElementById('erpUnitType')?.value;
                    if (!categoryId || !groupId || !subGroupId || !name || !nameUrdu || !unitTypeId) {
                        showToast('Please fill all required fields', 'error');
                        return;
                    }
                    const btn = document.getElementById('submitErpItemBtn');
                    const btnText = btn ? btn.querySelector('#submitErpItemText') : null;
                    if (btn) { btn.disabled = true; if (btnText) btnText.textContent = 'Saving...'; }
                    const numInput = document.getElementById('erpNormalizedSkuNumber');
                    const prefixSpan = document.getElementById('erpNormalizedSkuPrefix');
                    const fullSkuFromForm = (numInput && prefixSpan) ? (prefixSpan.textContent + (numInput.value || '').trim()) : (document.getElementById('erpNormalizedSku')?.value || '');
                    const useReelSku = !!(numInput && prefixSpan && fullSkuFromForm);
                    if (useReelSku && (!numInput.value || !String(numInput.value).trim())) {
                        showToast('Please enter the SKU number (numeric part)', 'error');
                        if (btn) { btn.disabled = false; if (btnText) btnText.textContent = 'Create Item'; }
                        return;
                    }
                    function doCreate(payload) {
                        return fetch('../api/items', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                    }
                    function addPayloadExtras(payload) {
                        const wCalc = document.getElementById('erpItemCalculatedWeight')?.value;
                        const minOl = document.getElementById('erpItemMinOrderLevel')?.value;
                        const maxOl = document.getElementById('erpItemMaxOrderLevel')?.value;
                        if (wCalc) payload.calculated_weight = parseFloat(wCalc);
                        if (minOl) payload.min_order_level = parseInt(minOl);
                        if (maxOl) payload.max_order_level = parseInt(maxOl);
                        const attrInputs = document.querySelectorAll('#erpAttributeFields .erp-attribute-input');
                        if (attrInputs.length > 0) {
                            payload.attribute_values = [];
                            attrInputs.forEach(inp => {
                                const attrId = inp.dataset.attributeId;
                                const val = inp.value?.trim();
                                if (attrId && val) payload.attribute_values.push({ attribute_id: parseInt(attrId), value: val });
                            });
                        }
                        return payload;
                    }
                    function onCreateSuccess() {
                        showToast('Item created successfully', 'success');
                        const savedCat = categoryId, savedGrp = groupId, savedSg = subGroupId;
                        form.reset();
                        resetErpSkuField();
                        const attrFieldsContainer = document.getElementById('erpAttributeFields');
                        if (attrFieldsContainer) attrFieldsContainer.innerHTML = '';
                        var catEl = document.getElementById('erpCategory');
                        if (catEl && savedCat) { catEl.value = savedCat; if (catEl.__enhanced && catEl.__enhanced.setDisplayFromValue) catEl.__enhanced.setDisplayFromValue(); }
                        if (!savedCat || !savedGrp || !savedSg) { loadErpItems(); var nameInput = document.getElementById('erpItemName'); if (nameInput) nameInput.focus(); return; }
                        fetch('../api/items/groups?category_id=' + encodeURIComponent(savedCat), { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
                            .then(function(r) { return r.json(); })
                            .then(function(res) {
                                if (!res.success) return;
                                var sel = document.getElementById('erpGroup');
                                if (!sel) return;
                                sel.innerHTML = '<option value="">Select Group</option>';
                                (res.data || []).forEach(function(g) { sel.innerHTML += '<option value="' + g.id + '">' + g.name + '</option>'; });
                                sel.disabled = false;
                                if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpGroup', 'Search groups...');
                                sel.value = savedGrp;
                                if (sel.__enhanced && sel.__enhanced.setDisplayFromValue) sel.__enhanced.setDisplayFromValue();
                                return fetch('../api/items/sub-groups?group_id=' + encodeURIComponent(savedGrp), { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } });
                            })
                            .then(function(r) { return r.json(); })
                            .then(function(res) {
                                if (!res.success) return;
                                var sg = document.getElementById('erpSubGroup');
                                if (!sg) return;
                                sg.innerHTML = '<option value="">Select Sub-Group</option>';
                                (res.data || []).forEach(function(s) { sg.innerHTML += '<option value="' + s.id + '">' + s.name + '</option>'; });
                                sg.disabled = false;
                                if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('erpSubGroup', 'Search sub-groups...');
                                sg.value = savedSg;
                                if (sg.__enhanced && sg.__enhanced.setDisplayFromValue) sg.__enhanced.setDisplayFromValue();
                                loadErpAttributeFields(savedSg);
                                fetchAndShowNextErpSku();
                                loadErpItems();
                                var nameInput = document.getElementById('erpItemName');
                                if (nameInput) nameInput.focus();
                            })
                            .catch(function() { loadErpItems(); var nameInput = document.getElementById('erpItemName'); if (nameInput) nameInput.focus(); });
                    }
                    function onCreateFinally() {
                        const b = document.getElementById('submitErpItemBtn');
                        if (b) { b.disabled = false; const t = b.querySelector('#submitErpItemText'); if (t) t.textContent = 'Create Item'; }
                    }
                    if (useReelSku) {
                        const payload = addPayloadExtras({
                            name, name_in_urdu: nameUrdu,
                            category_id: parseInt(categoryId), group_id: parseInt(groupId), sub_group_id: parseInt(subGroupId),
                            unit_type_id: parseInt(unitTypeId), normalized_sku: fullSkuFromForm,
                            source_id: fullSkuFromForm
                        });
                        doCreate(payload).then(r => r.json())
                            .then(res => {
                                if (res.success) onCreateSuccess();
                                else throw new Error(res.error || 'Failed to create item');
                            })
                            .catch(err => { showToast(err.message || 'Failed to create item', 'error'); })
                            .finally(onCreateFinally);
                    } else {
                        fetch('../api/items/generate-sku', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ category_id: categoryId, group_id: groupId, sub_group_id: subGroupId })
                        })
                            .then(r => r.json())
                            .then(skuRes => {
                                if (!skuRes.success) throw new Error(skuRes.error || 'Failed to generate SKU');
                                const sku = skuRes.data?.normalized_sku || '';
                                const payload = addPayloadExtras({
                                    name, name_in_urdu: nameUrdu,
                                    category_id: parseInt(categoryId), group_id: parseInt(groupId), sub_group_id: parseInt(subGroupId),
                                    unit_type_id: parseInt(unitTypeId), normalized_sku: sku,
                                    source_id: sku
                                });
                                return doCreate(payload);
                            })
                            .then(r => r.json())
                            .then(res => {
                                if (res.success) onCreateSuccess();
                                else throw new Error(res.error || 'Failed to create item');
                            })
                            .catch(err => { showToast(err.message || 'Failed to create item', 'error'); })
                            .finally(onCreateFinally);
                    }
                });
            }

            const searchInput = document.getElementById('searchErpItemsInput');
            if (searchInput) {
                let t;
                searchInput.addEventListener('input', function() {
                    clearTimeout(t);
                    t = setTimeout(() => { erpItemsCurrentSearch = this.value; erpItemsCurrentPage = 1; loadErpItems(); }, 400);
                });
            }

            // Populate group/subgroup dropdowns for master panels
            const erpGroupCat = document.getElementById('erpGroupCategory');
            if (erpGroupCat) erpGroupCat.addEventListener('change', function() {
                const cid = this.value;
                loadErpGroupsTable(cid);
                const sg = document.getElementById('erpSubGroupGroup');
                if (sg) { sg.innerHTML = '<option value="">Select Group</option>'; sg.disabled = !cid; }
                if (cid) fetch(`../api/items/groups?category_id=${cid}`, { method: 'GET', credentials: 'same-origin' }).then(r=>r.json()).then(res=>{ if(res.success) { (res.data||[]).forEach(g=>{ if(sg)sg.innerHTML+=`<option value="${g.id}">${g.name}</option>`; }); sg.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpSubGroupGroup','Search groups...'); } });
            });
            const erpSubGrp = document.getElementById('erpSubGroupGroup');
            if (erpSubGrp) erpSubGrp.addEventListener('change', function() {
                loadErpSubGroupsTable(this.value);
            });
            const erpAttrCat = document.getElementById('erpAttrCategory');
            if (erpAttrCat) erpAttrCat.addEventListener('change', function() {
                const cid = this.value;
                const grp = document.getElementById('erpAttrGroup');
                const sg = document.getElementById('erpAttrSubGroup');
                if (grp) { grp.innerHTML = '<option value="">Select Group</option>'; grp.disabled = !cid; }
                if (sg) { sg.innerHTML = '<option value="">Select Sub-Group</option>'; sg.disabled = true; }
                if (cid) fetch(`../api/items/groups?category_id=${cid}`, { method: 'GET', credentials: 'same-origin' }).then(r=>r.json()).then(res=>{ if(res.success) { (res.data||[]).forEach(g=>{ if(grp)grp.innerHTML+=`<option value="${g.id}">${g.name}</option>`; }); grp.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpAttrGroup','Search groups...'); } });
            });
            const erpAttrGrp = document.getElementById('erpAttrGroup');
            if (erpAttrGrp) erpAttrGrp.addEventListener('change', function() {
                const gid = this.value;
                const sg = document.getElementById('erpAttrSubGroup');
                if (sg) { sg.innerHTML = '<option value="">Select Sub-Group</option>'; sg.disabled = !gid; }
                if (gid) fetch(`../api/items/sub-groups?group_id=${gid}`, { method: 'GET', credentials: 'same-origin' }).then(r=>r.json()).then(res=>{ if(res.success) { (res.data||[]).forEach(s=>{ if(sg)sg.innerHTML+=`<option value="${s.id}">${s.name}</option>`; }); sg.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpAttrSubGroup','Search sub-groups...'); } });
            });
            const erpAttrSg = document.getElementById('erpAttrSubGroup');
            if (erpAttrSg) erpAttrSg.addEventListener('change', function() {
                loadErpAttributesTable(this.value);
            });
            const erpSubCat = document.getElementById('erpSubGroupCategory');
            if (erpSubCat) erpSubCat.addEventListener('change', function() {
                const cid = this.value;
                const grp = document.getElementById('erpSubGroupGroup');
                if (grp) { grp.innerHTML = '<option value="">Select Group</option>'; grp.disabled = !cid; }
                if (cid) fetch(`../api/items/groups?category_id=${cid}`, { method: 'GET', credentials: 'same-origin' }).then(r=>r.json()).then(res=>{ if(res.success) { (res.data||[]).forEach(g=>{ if(grp)grp.innerHTML+=`<option value="${g.id}">${g.name}</option>`; }); grp.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpSubGroupGroup','Search groups...'); } });
            });

            // ERP master CRUD forms
            const erpCatForm = document.getElementById('erpCategoryForm');
            if (erpCatForm) erpCatForm.addEventListener('submit', function(e) { e.preventDefault();
                const name = document.getElementById('erpCatName')?.value;
                if (!name) { showToast('Name is required', 'error'); return; }
                const editId = erpCatForm.dataset.editId;
                const payload = { name, code: document.getElementById('erpCatCode')?.value || undefined, description: document.getElementById('erpCatDesc')?.value || undefined };
                const url = editId ? `../api/items/categories/${editId}` : '../api/items/categories';
                const method = editId ? 'PUT' : 'POST';
                fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                    .then(r=>r.json()).then(res=>{ if(res.success) { showToast(editId ? 'Category updated' : 'Category created', 'success'); erpCatForm.reset(); delete erpCatForm.dataset.editId; const t=document.getElementById('submitErpCategoryText'); if(t)t.textContent='Create'; loadErpCategories(); loadErpCategoriesTable(); loadErpMasterDropdowns(); } else showToast(res.error||'Failed', 'error'); }).catch(()=>showToast('Failed', 'error'));
            });
            const erpGrpForm = document.getElementById('erpGroupForm');
            if (erpGrpForm) erpGrpForm.addEventListener('submit', function(e) { e.preventDefault();
                const catId = document.getElementById('erpGroupCategory')?.value, name = document.getElementById('erpGroupName')?.value;
                if (!catId || !name) { showToast('Category and Name are required', 'error'); return; }
                const editId = erpGrpForm.dataset.editId;
                const payload = { category_id: parseInt(catId), name, code: document.getElementById('erpGroupCode')?.value || undefined };
                const url = editId ? `../api/items/groups/${editId}` : '../api/items/groups';
                const method = editId ? 'PUT' : 'POST';
                fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                    .then(r=>r.json()).then(res=>{ if(res.success) { showToast(editId ? 'Group updated' : 'Group created', 'success'); const savedCatId = catId; erpGrpForm.reset(); delete erpGrpForm.dataset.editId; const t=document.getElementById('submitErpGroupText'); if(t)t.textContent='Create'; loadErpGroupsTable(savedCatId); loadErpCategories(); var p = loadErpMasterDropdowns(); if (p && p.then) p.then(function(){ var el=document.getElementById('erpGroupCategory'); if(el&&savedCatId){ el.value=savedCatId; el.dispatchEvent(new Event('change',{bubbles:true})); if(el.__enhanced&&el.__enhanced.setDisplayFromValue)el.__enhanced.setDisplayFromValue(); } var nameInput=document.getElementById('erpGroupName'); if(nameInput){ nameInput.focus(); } }); } else showToast(res.error||'Failed', 'error'); }).catch(()=>showToast('Failed', 'error'));
            });
            const erpSgForm = document.getElementById('erpSubGroupForm');
            if (erpSgForm) erpSgForm.addEventListener('submit', function(e) { e.preventDefault();
                const gid = document.getElementById('erpSubGroupGroup')?.value, name = document.getElementById('erpSubGroupName')?.value;
                if (!gid || !name) { showToast('Group and Name are required', 'error'); return; }
                const editId = erpSgForm.dataset.editId;
                const payload = { group_id: parseInt(gid), name, code: document.getElementById('erpSubGroupCode')?.value || undefined };
                const url = editId ? `../api/items/sub-groups/${editId}` : '../api/items/sub-groups';
                const method = editId ? 'PUT' : 'POST';
                fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                    .then(r=>r.json()).then(res=>{ if(res.success) { showToast(editId ? 'Sub-group updated' : 'Sub-group created', 'success'); const savedCatId=document.getElementById('erpSubGroupCategory')?.value, savedGid=gid; erpSgForm.reset(); delete erpSgForm.dataset.editId; const t=document.getElementById('submitErpSubGroupText'); if(t)t.textContent='Create'; loadErpSubGroupsTable(savedGid); loadErpCategories(); var p = loadErpMasterDropdowns(); if (p && p.then) p.then(function(){ var catEl=document.getElementById('erpSubGroupCategory'); if(!catEl||!savedCatId)return; catEl.value=savedCatId; if(catEl.__enhanced&&catEl.__enhanced.setDisplayFromValue)catEl.__enhanced.setDisplayFromValue(); fetch('../api/items/groups?category_id='+encodeURIComponent(savedCatId),{method:'GET',credentials:'same-origin'}).then(r=>r.json()).then(grpRes=>{ if(!grpRes.success)return; var grp=document.getElementById('erpSubGroupGroup'); if(!grp)return; grp.innerHTML='<option value="">Select Group</option>'; (grpRes.data||[]).forEach(function(g){ grp.innerHTML+='<option value="'+g.id+'">'+g.name+'</option>'; }); grp.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpSubGroupGroup','Search groups...'); grp.value=savedGid; if(grp.__enhanced&&grp.__enhanced.setDisplayFromValue)grp.__enhanced.setDisplayFromValue(); var nameInput=document.getElementById('erpSubGroupName'); if(nameInput)nameInput.focus(); }); }); } else showToast(res.error||'Failed', 'error'); }).catch(()=>showToast('Failed', 'error'));
            });
            const erpAttrForm = document.getElementById('erpAttributeForm');
            if (erpAttrForm) erpAttrForm.addEventListener('submit', function(e) { e.preventDefault();
                const sgId = document.getElementById('erpAttrSubGroup')?.value, attrName = document.getElementById('erpAttrName')?.value;
                if (!sgId || !attrName) { showToast('Sub-Group and Attribute Name are required', 'error'); return; }
                const editId = erpAttrForm.dataset.editId;
                const payload = { sub_group_id: parseInt(sgId), attribute_name: attrName, is_required: document.getElementById('erpAttrRequired')?.checked ? 1 : 0 };
                const url = editId ? `../api/items/attributes/${editId}` : '../api/items/attributes';
                const method = editId ? 'PUT' : 'POST';
                fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                    .then(r=>r.json()).then(res=>{ if(res.success) { showToast(editId ? 'Attribute updated' : 'Attribute created', 'success'); const savedCatId=document.getElementById('erpAttrCategory')?.value, savedGid=document.getElementById('erpAttrGroup')?.value, savedSgId=sgId; erpAttrForm.reset(); delete erpAttrForm.dataset.editId; const t=document.getElementById('submitErpAttributeText'); if(t)t.textContent='Create'; loadErpAttributesTable(savedSgId); var p = loadErpMasterDropdowns(); if (p && p.then) p.then(function(){ var catEl=document.getElementById('erpAttrCategory'); if(!catEl||!savedCatId)return; catEl.value=savedCatId; if(catEl.__enhanced&&catEl.__enhanced.setDisplayFromValue)catEl.__enhanced.setDisplayFromValue(); fetch('../api/items/groups?category_id='+encodeURIComponent(savedCatId),{method:'GET',credentials:'same-origin'}).then(r=>r.json()).then(grpRes=>{ if(!grpRes.success)return; var grp=document.getElementById('erpAttrGroup'); if(!grp)return; grp.innerHTML='<option value="">Select Group</option>'; (grpRes.data||[]).forEach(function(g){ grp.innerHTML+='<option value="'+g.id+'">'+g.name+'</option>'; }); grp.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpAttrGroup','Search groups...'); grp.value=savedGid; if(grp.__enhanced&&grp.__enhanced.setDisplayFromValue)grp.__enhanced.setDisplayFromValue(); fetch('../api/items/sub-groups?group_id='+encodeURIComponent(savedGid),{method:'GET',credentials:'same-origin'}).then(r=>r.json()).then(sgRes=>{ if(!sgRes.success)return; var sg=document.getElementById('erpAttrSubGroup'); if(!sg)return; sg.innerHTML='<option value="">Select Sub-Group</option>'; (sgRes.data||[]).forEach(function(s){ sg.innerHTML+='<option value="'+s.id+'">'+s.name+'</option>'; }); sg.disabled=false; if(typeof window.upgradeSelectToSearchable==='function')window.upgradeSelectToSearchable('erpAttrSubGroup','Search sub-groups...'); sg.value=savedSgId; if(sg.__enhanced&&sg.__enhanced.setDisplayFromValue)sg.__enhanced.setDisplayFromValue(); var nameInput=document.getElementById('erpAttrName'); if(nameInput)nameInput.focus(); }); }); }); } else showToast(res.error||'Failed', 'error'); }).catch(()=>showToast('Failed', 'error'));
            });


            // Edit ERP Item modal - store current item's attribute values for use in change handlers
            let editErpItemCurrentAttributeValues = [];
            let editErpItemIsInitialLoad = false;
            let editErpSkuNumericTail = ''; // stores numeric tail when cat/grp/sg change so we can rebuild SKU
            
            // Helper function to populate a select element and set its value
            function populateSelect(selectId, options, valueKey, labelKey, selectedValue, placeholder) {
                const sel = document.getElementById(selectId);
                if (!sel) return;
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                (options || []).forEach(opt => {
                    const val = opt[valueKey];
                    const label = opt[labelKey] || opt.name || val;
                    sel.innerHTML += `<option value="${val}">${label}</option>`;
                });
                sel.disabled = false;
                // Set value after options are added
                if (selectedValue) {
                    sel.value = selectedValue;
                }
                // Refresh the searchable wrapper if it exists
                if (sel.__enhanced) {
                    if (sel.__enhanced.refresh) sel.__enhanced.refresh();
                    if (sel.__enhanced.setDisplayFromValue) sel.__enhanced.setDisplayFromValue();
                }
            }
            
            window.editErpItem = async function(id) {
                editErpItemIsInitialLoad = true;
                
                try {
                    // Fetch item data
                    const itemRes = await fetch(`../api/items/${id}`, { 
                        method: 'GET', 
                        credentials: 'same-origin', 
                        headers: { 'Content-Type': 'application/json' } 
                    }).then(r => r.json());
                    
                    if (!itemRes.success || !itemRes.data) { 
                        showToast('Item not found', 'error'); 
                        return; 
                    }
                    
                    const item = itemRes.data;
                    editErpItemCurrentAttributeValues = item.attribute_values || [];
                    
                    // Set basic text fields
                    document.getElementById('editErpItemId').value = item.id || '';
                    document.getElementById('editErpItemName').value = item.name || '';
                    document.getElementById('editErpItemNameUrdu').value = item.name_in_urdu || '';
                    document.getElementById('editErpItemCalculatedWeight').value = item.calculated_weight || '';
                    document.getElementById('editErpItemMinOrderLevel').value = item.min_order_level || '';
                    document.getElementById('editErpItemMaxOrderLevel').value = item.max_order_level || '';
                    
                    // Set SKU: read-only for non-REEL; for REEL show prefix + editable number
                    const editSkuWrapper = document.getElementById('editErpSkuFieldWrapper');
                    const sku = (item.normalized_sku || item.source_id || '').trim();
                    if (editSkuWrapper) {
                        const parts = sku ? sku.split('-') : [];
                        const isReelGroup = parts.length >= 4 && (parts[1] || '').toUpperCase() === 'REEL';
                        if (isReelGroup && sku) {
                            const prefix = parts.slice(0, 3).join('-') + '-';
                            const numberPart = parts[3] || '';
                            editSkuWrapper.classList.add('erp-sku-reel-group');
                            editSkuWrapper.innerHTML =
                                '<div class="erp-sku-reel-row flex flex-nowrap items-center w-full min-h-[40px] h-[40px] rounded-md border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] overflow-hidden box-border px-0 focus-within:border-primary-500 transition-all">' +
                                '<span id="editErpNormalizedSkuPrefix" class="erp-sku-prefix shrink-0 flex items-center h-full pl-[12px] pr-[8px] text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">' + prefix + '</span>' +
                                '<input type="text" id="editErpNormalizedSkuNumber" inputmode="numeric" pattern="[0-9]*" maxlength="12" value="' + (numberPart || '') + '" class="erp-sku-number h-full flex-1 min-w-[60px] rounded-none border-0 bg-transparent text-black dark:text-white pl-0 pr-[12px] text-sm outline-0 focus:ring-0 focus:border-0" placeholder="Number only">' +
                                '</div>';
                            const numEl = document.getElementById('editErpNormalizedSkuNumber');
                            if (numEl) {
                                numEl.addEventListener('input', function() { this.value = (this.value || '').replace(/\D/g, ''); });
                            }
                        } else {
                            editSkuWrapper.classList.remove('erp-sku-reel-group');
                            editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="Code / SKU">';
                            const skuEl = document.getElementById('editErpNormalizedSku');
                            if (skuEl) skuEl.value = sku;
                        }
                    }
                    // Store numeric tail for later rebuild when cat/grp/sg change
                    const skuForTail = (item.normalized_sku || item.source_id || '').trim();
                    const tailMatch = skuForTail.match(/-(\d+)$/);
                    editErpSkuNumericTail = tailMatch ? tailMatch[1] : (skuForTail && /^\d+$/.test(skuForTail) ? skuForTail : '');
                    
                    // Fetch all dropdown data in parallel
                    const [categoriesRes, unitTypesRes] = await Promise.all([
                        fetch('../api/items/categories', { method: 'GET', credentials: 'same-origin' }).then(r => r.json()),
                        fetch('../api/unit-types?limit=500', { method: 'GET', credentials: 'same-origin' }).then(r => r.json())
                    ]);
                    
                    // Populate Categories (filter to FG/RM for PRT/PRA)
                    if (categoriesRes.success) {
                        const editCatList = filterCategoriesForRole(categoriesRes.data || []);
                        populateSelect('editErpCategory', editCatList, 'id', 'name', item.category_id, 'Category *');
                        // Upgrade to searchable if not already
                        if (typeof window.upgradeSelectToSearchable === 'function') {
                            window.upgradeSelectToSearchable('editErpCategory', 'Search categories...');
                        }
                    }
                    
                    // Populate Unit Types
                    const unitTypesList = (unitTypesRes.data && unitTypesRes.data.records) 
                        ? unitTypesRes.data.records 
                        : (unitTypesRes.records || unitTypesRes.data || []);
                    populateSelect('editErpUnitType', unitTypesList, 'id', 'name', item.unit_type_id, 'Unit Type *');
                    // Upgrade to searchable if not already
                    if (typeof window.upgradeSelectToSearchable === 'function') {
                        window.upgradeSelectToSearchable('editErpUnitType', 'Search unit types...');
                    }
                    
                    // Load Groups if category exists
                    if (item.category_id) {
                        const groupsRes = await fetch(`../api/items/groups?category_id=${item.category_id}`, { 
                            method: 'GET', 
                            credentials: 'same-origin' 
                        }).then(r => r.json());
                        
                        if (groupsRes.success) {
                            populateSelect('editErpGroup', groupsRes.data, 'id', 'name', item.group_id, 'Group *');
                            // Upgrade to searchable if not already
                            if (typeof window.upgradeSelectToSearchable === 'function') {
                                window.upgradeSelectToSearchable('editErpGroup', 'Search groups...');
                            }
                        }
                    }
                    
                    // Load Sub-Groups if group exists
                    if (item.group_id) {
                        const subGroupsRes = await fetch(`../api/items/sub-groups?group_id=${item.group_id}`, { 
                            method: 'GET', 
                            credentials: 'same-origin' 
                        }).then(r => r.json());
                        
                        if (subGroupsRes.success) {
                            populateSelect('editErpSubGroup', subGroupsRes.data, 'id', 'name', item.sub_group_id, 'Sub-Group *');
                            // Upgrade to searchable if not already
                            if (typeof window.upgradeSelectToSearchable === 'function') {
                                window.upgradeSelectToSearchable('editErpSubGroup', 'Search sub-groups...');
                            }
                        }
                    }
                    
                    // Load attribute fields with existing values
                    if (item.sub_group_id) {
                        loadEditErpAttributeFields(item.sub_group_id, editErpItemCurrentAttributeValues);
                    }
                    
                    // Show modal
                    const modal = document.getElementById('editErpItemModal');
                    if (modal) { 
                        modal.classList.add('active'); 
                        modal.classList.remove('opacity-0', 'pointer-events-none'); 
                    }
                    
                    // Reset flag after modal is shown
                    setTimeout(() => { editErpItemIsInitialLoad = false; }, 500);
                    
                } catch (error) {
                    console.error('Error loading item for edit:', error);
                    showToast('Failed to load item', 'error');
                }
            };

            const closeEditErpModal = document.getElementById('closeEditErpItemModal');
            if (closeEditErpModal) closeEditErpModal.addEventListener('click', function() {
                const modal = document.getElementById('editErpItemModal');
                if (modal) { modal.classList.remove('active'); modal.classList.add('opacity-0','pointer-events-none'); }
            });

            // When category/group/sub-group change in Edit modal: show "Update SKU Code" or rebuilt SKU
            async function refreshEditModalSkuFromSelection() {
                const editSkuWrapper = document.getElementById('editErpSkuFieldWrapper');
                if (!editSkuWrapper) return;
                const categoryId = document.getElementById('editErpCategory')?.value;
                const groupId = document.getElementById('editErpGroup')?.value;
                const subGroupId = document.getElementById('editErpSubGroup')?.value;
                // Get current numeric tail from field (or keep stored)
                const numInput = document.getElementById('editErpNormalizedSkuNumber');
                const prefixSpan = document.getElementById('editErpNormalizedSkuPrefix');
                const readonlyInput = document.getElementById('editErpNormalizedSku');
                if (numInput && prefixSpan) {
                    editErpSkuNumericTail = (numInput.value || '').trim().replace(/\D/g, '') || editErpSkuNumericTail;
                } else if (readonlyInput && readonlyInput.value && readonlyInput.value !== 'Update SKU Code') {
                    const m = readonlyInput.value.match(/-(\d+)$/);
                    if (m) editErpSkuNumericTail = m[1];
                }
                if (!categoryId || !groupId || !subGroupId) {
                    editSkuWrapper.classList.remove('erp-sku-reel-group');
                    editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="Code / SKU">';
                    const el = document.getElementById('editErpNormalizedSku');
                    if (el) el.value = 'Update SKU Code';
                    return;
                }
                if (!editErpSkuNumericTail) {
                    editSkuWrapper.classList.remove('erp-sku-reel-group');
                    editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="Code / SKU">';
                    return;
                }
                try {
                    const [catListRes, grpListRes, sgListRes] = await Promise.all([
                        fetch('../api/items/categories', { method: 'GET', credentials: 'same-origin' }).then(r => r.json()),
                        fetch('../api/items/groups?category_id=' + encodeURIComponent(categoryId), { method: 'GET', credentials: 'same-origin' }).then(r => r.json()),
                        fetch('../api/items/sub-groups?group_id=' + encodeURIComponent(groupId), { method: 'GET', credentials: 'same-origin' }).then(r => r.json())
                    ]);
                    const catList = Array.isArray(catListRes.data) ? catListRes.data : [];
                    const grpList = Array.isArray(grpListRes.data) ? grpListRes.data : [];
                    const sgList = Array.isArray(sgListRes.data) ? sgListRes.data : [];
                    const cat = catList.find(c => parseInt(c.id) === parseInt(categoryId));
                    const grp = grpList.find(g => parseInt(g.id) === parseInt(groupId));
                    const sg = sgList.find(s => parseInt(s.id) === parseInt(subGroupId));
                    if (!cat || !grp || !sg || !cat.code || !grp.code || !sg.code) {
                        editSkuWrapper.classList.remove('erp-sku-reel-group');
                        editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" value="Update SKU Code">';
                        return;
                    }
                    const newSku = cat.code + '-' + grp.code + '-' + sg.code + '-' + editErpSkuNumericTail;
                    const parts = newSku.split('-');
                    const isReelGroup = parts.length >= 4 && (parts[1] || '').toUpperCase() === 'REEL';
                    if (isReelGroup) {
                        const prefix = parts.slice(0, 3).join('-') + '-';
                        editSkuWrapper.classList.add('erp-sku-reel-group');
                        editSkuWrapper.innerHTML =
                            '<div class="erp-sku-reel-row flex flex-nowrap items-center w-full min-h-[40px] h-[40px] rounded-md border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] overflow-hidden box-border px-0 focus-within:border-primary-500 transition-all">' +
                            '<span id="editErpNormalizedSkuPrefix" class="erp-sku-prefix shrink-0 flex items-center h-full pl-[12px] pr-[8px] text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">' + prefix + '</span>' +
                            '<input type="text" id="editErpNormalizedSkuNumber" inputmode="numeric" pattern="[0-9]*" maxlength="12" value="' + (editErpSkuNumericTail || '') + '" class="erp-sku-number h-full flex-1 min-w-[60px] rounded-none border-0 bg-transparent text-black dark:text-white pl-0 pr-[12px] text-sm outline-0 focus:ring-0 focus:border-0" placeholder="Number only">' +
                            '</div>';
                        const numEl = document.getElementById('editErpNormalizedSkuNumber');
                        if (numEl) numEl.addEventListener('input', function() { this.value = (this.value || '').replace(/\D/g, ''); });
                    } else {
                        editSkuWrapper.classList.remove('erp-sku-reel-group');
                        editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder="Code / SKU">';
                        const skuEl = document.getElementById('editErpNormalizedSku');
                        if (skuEl) skuEl.value = newSku;
                    }
                } catch (e) {
                    editSkuWrapper.classList.remove('erp-sku-reel-group');
                    editSkuWrapper.innerHTML = '<input type="text" id="editErpNormalizedSku" readonly class="h-[40px] rounded-md text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" value="Update SKU Code">';
                }
            }

            // Cascading change handlers for Edit ERP Item modal dropdowns
            const editErpCat = document.getElementById('editErpCategory');
            const editErpGrp = document.getElementById('editErpGroup');
            const editErpSg = document.getElementById('editErpSubGroup');

            if (editErpCat) {
                editErpCat.addEventListener('change', function() {
                    // Skip if this is during initial load
                    if (editErpItemIsInitialLoad) return;
                    const catId = this.value;
                    // Clear stored attribute values since user is changing category
                    editErpItemCurrentAttributeValues = [];
                    // Reset and disable group and sub-group
                    if (editErpGrp) { 
                        editErpGrp.innerHTML = '<option value="">Group *</option>'; 
                        editErpGrp.disabled = !catId;
                        // Refresh the searchable wrapper
                        if (editErpGrp.__enhanced) {
                            if (editErpGrp.__enhanced.refresh) editErpGrp.__enhanced.refresh();
                            if (editErpGrp.__enhanced.setDisplayFromValue) editErpGrp.__enhanced.setDisplayFromValue();
                        }
                    }
                    if (editErpSg) { 
                        editErpSg.innerHTML = '<option value="">Sub-Group *</option>'; 
                        editErpSg.disabled = true;
                        // Refresh the searchable wrapper
                        if (editErpSg.__enhanced) {
                            if (editErpSg.__enhanced.refresh) editErpSg.__enhanced.refresh();
                            if (editErpSg.__enhanced.setDisplayFromValue) editErpSg.__enhanced.setDisplayFromValue();
                        }
                    }
                    // Clear attribute fields
                    const attrContainer = document.getElementById('editErpAttributeFields');
                    if (attrContainer) attrContainer.innerHTML = '';
                    refreshEditModalSkuFromSelection();
                    if (!catId) return;
                    // Fetch groups for selected category
                    fetch(`../api/items/groups?category_id=${encodeURIComponent(catId)}`, { method: 'GET', credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(res => {
                            if (!res.success) return;
                            (res.data || []).forEach(g => { if (editErpGrp) editErpGrp.innerHTML += `<option value="${g.id}">${g.name}</option>`; });
                            if (editErpGrp) {
                                editErpGrp.disabled = false;
                                // Upgrade or refresh the searchable wrapper
                                if (!editErpGrp.__enhanced) {
                                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('editErpGroup', 'Search groups...');
                                } else {
                                    if (editErpGrp.__enhanced.refresh) editErpGrp.__enhanced.refresh();
                                    if (editErpGrp.__enhanced.setDisplayFromValue) editErpGrp.__enhanced.setDisplayFromValue();
                                }
                            }
                        });
                });
            }

            if (editErpGrp) {
                editErpGrp.addEventListener('change', function() {
                    // Skip if this is during initial load
                    if (editErpItemIsInitialLoad) return;
                    const grpId = this.value;
                    // Clear stored attribute values since user is changing group
                    editErpItemCurrentAttributeValues = [];
                    // Reset and disable sub-group
                    if (editErpSg) { 
                        editErpSg.innerHTML = '<option value="">Sub-Group *</option>'; 
                        editErpSg.disabled = !grpId;
                        // Refresh the searchable wrapper
                        if (editErpSg.__enhanced) {
                            if (editErpSg.__enhanced.refresh) editErpSg.__enhanced.refresh();
                            if (editErpSg.__enhanced.setDisplayFromValue) editErpSg.__enhanced.setDisplayFromValue();
                        }
                    }
                    // Clear attribute fields
                    const attrContainer = document.getElementById('editErpAttributeFields');
                    if (attrContainer) attrContainer.innerHTML = '';
                    refreshEditModalSkuFromSelection();
                    if (!grpId) return;
                    // Fetch sub-groups for selected group
                    fetch(`../api/items/sub-groups?group_id=${encodeURIComponent(grpId)}`, { method: 'GET', credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(res => {
                            if (!res.success) return;
                            (res.data || []).forEach(sg => { if (editErpSg) editErpSg.innerHTML += `<option value="${sg.id}">${sg.name}</option>`; });
                            if (editErpSg) {
                                editErpSg.disabled = false;
                                // Upgrade or refresh the searchable wrapper
                                if (!editErpSg.__enhanced) {
                                    if (typeof window.upgradeSelectToSearchable === 'function') window.upgradeSelectToSearchable('editErpSubGroup', 'Search sub-groups...');
                                } else {
                                    if (editErpSg.__enhanced.refresh) editErpSg.__enhanced.refresh();
                                    if (editErpSg.__enhanced.setDisplayFromValue) editErpSg.__enhanced.setDisplayFromValue();
                                }
                            }
                        });
                });
            }

            if (editErpSg) {
                editErpSg.addEventListener('change', function() {
                    // Skip if this is during initial load (values are already being loaded)
                    if (editErpItemIsInitialLoad) return;
                    const sgId = this.value;
                    // Clear stored attribute values since user is changing sub-group
                    editErpItemCurrentAttributeValues = [];
                    // Load attribute fields for this sub-group (with empty values since it's a new sub-group)
                    loadEditErpAttributeFields(sgId, []);
                    refreshEditModalSkuFromSelection();
                });
            }

            const updateErpItemBtn = document.getElementById('updateErpItemBtn');
            if (updateErpItemBtn) updateErpItemBtn.addEventListener('click', function() {
                (async () => {
                    const id = document.getElementById('editErpItemId')?.value;
                    if (!id) return;
                    const name = document.getElementById('editErpItemName')?.value;
                    const nameUrdu = document.getElementById('editErpItemNameUrdu')?.value;
                    const categoryId = document.getElementById('editErpCategory')?.value;
                    const groupId = document.getElementById('editErpGroup')?.value;
                    const subGroupId = document.getElementById('editErpSubGroup')?.value;
                    const unitTypeId = document.getElementById('editErpUnitType')?.value;
                    const payload = { name, name_in_urdu: nameUrdu, category_id: parseInt(categoryId), group_id: parseInt(groupId), sub_group_id: parseInt(subGroupId), unit_type_id: parseInt(unitTypeId) };
                    const cw = document.getElementById('editErpItemCalculatedWeight')?.value;
                    const minOl = document.getElementById('editErpItemMinOrderLevel')?.value;
                    const maxOl = document.getElementById('editErpItemMaxOrderLevel')?.value;
                    if (cw) payload.calculated_weight = parseFloat(cw);
                    if (minOl) payload.min_order_level = parseInt(minOl);
                    if (maxOl) payload.max_order_level = parseInt(maxOl);
                    // Collect attribute values from edit modal
                    const editAttrInputs = document.querySelectorAll('#editErpAttributeFields .edit-erp-attribute-input');
                    if (editAttrInputs.length > 0) {
                        payload.attribute_values = [];
                        editAttrInputs.forEach(inp => {
                            const attrId = inp.dataset.attributeId;
                            const val = inp.value?.trim();
                            if (attrId) {
                                payload.attribute_values.push({ attribute_id: parseInt(attrId), value: val || '' });
                            }
                        });
                    }
                    // For REEL group: use SKU from form (prefix + editable number). Otherwise rebuild SKU from category/group/sub-group + existing numeric tail.
                    try {
                        updateErpItemBtn.disabled = true;
                        
                        const editSkuNum = document.getElementById('editErpNormalizedSkuNumber');
                        const editSkuPrefix = document.getElementById('editErpNormalizedSkuPrefix');
                        if (editSkuNum && editSkuPrefix) {
                            const reelSku = (editSkuPrefix.textContent || '') + (editSkuNum.value || '').trim();
                            if (reelSku) {
                                payload.normalized_sku = reelSku;
                                payload.source_id = reelSku;
                            }
                        }
                        
                        if (!payload.normalized_sku) {
                        // Fetch existing item to get current normalized_sku
                        const existingItemRes = await fetch(`../api/items/${id}`, { method: 'GET', credentials: 'same-origin' }).then(r => r.json());
                        const existingItem = existingItemRes.success ? existingItemRes.data : null;
                        const existingNormalizedSku = existingItem ? (existingItem.normalized_sku || existingItem.source_id || '') : '';
                        
                        // Extract numeric tail from existing SKU.
                        // Support formats: "CAT-GRP-SG-123", numeric-only "123", or any SKU that ends with digits.
                        let existingNumericTail = null;
                        const numericTailMatch = existingNormalizedSku.match(/-(\d+)$/);
                        if (numericTailMatch) {
                            existingNumericTail = numericTailMatch[1];
                        } else if (/^\d+$/.test(String(existingNormalizedSku).trim())) {
                            // source_id stored as numeric-only string
                            existingNumericTail = String(existingNormalizedSku).trim();
                        } else {
                            // Fallback: grab trailing digits if any
                            const fallbackMatch = existingNormalizedSku.match(/(\d+)$/);
                            if (fallbackMatch) existingNumericTail = fallbackMatch[1];
                        }

                        console.log('Rebuild SKU: existingNormalizedSku=' + existingNormalizedSku + ', existingNumericTail=' + existingNumericTail + ', newCat=' + categoryId + ', newGrp=' + groupId + ', newSg=' + subGroupId);

                        if (existingNumericTail) {
                            // Fetch all categories, groups, sub-groups to get their codes
                            const [catListRes, grpListRes, sgListRes] = await Promise.all([
                                fetch(`../api/items/categories`, { method: 'GET', credentials: 'same-origin' }).catch(() => ({ ok: false })),
                                fetch(`../api/items/groups?category_id=${categoryId}`, { method: 'GET', credentials: 'same-origin' }).catch(() => ({ ok: false })),
                                fetch(`../api/items/sub-groups?group_id=${groupId}`, { method: 'GET', credentials: 'same-origin' }).catch(() => ({ ok: false }))
                            ]);
                            
                            const catListData = catListRes.ok ? await catListRes.json() : {};
                            const grpListData = grpListRes.ok ? await grpListRes.json() : {};
                            const sgListData = sgListRes.ok ? await sgListRes.json() : {};
                            
                            // Find selected item from each list by ID
                            const catList = Array.isArray(catListData.data) ? catListData.data : (catListData.data || []);
                            const grpList = Array.isArray(grpListData.data) ? grpListData.data : (grpListData.data || []);
                            const sgList = Array.isArray(sgListData.data) ? sgListData.data : (sgListData.data || []);
                            
                            const cat = catList.find(c => parseInt(c.id) === parseInt(categoryId));
                            const grp = grpList.find(g => parseInt(g.id) === parseInt(groupId));
                            const sg = sgList.find(s => parseInt(s.id) === parseInt(subGroupId));
                            
                            console.log('Found cat:', cat?.code, 'grp:', grp?.code, 'sg:', sg?.code);
                            
                            if (cat && grp && sg && cat.code && grp.code && sg.code) {
                                // Build new SKU with new codes but same numeric tail
                                const newSku = cat.code + '-' + grp.code + '-' + sg.code + '-' + existingNumericTail;
                                payload.normalized_sku = newSku;
                                payload.source_id = newSku;
                                console.log('New SKU:', newSku);
                            }
                        }
                        }
                    } catch (err) {
                        console.error('Failed to rebuild SKU for update', err);
                    }

                    fetch(`../api/items/${id}`, { method: 'PUT', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                        .then(r=>r.json()).then(res=>{ if(res.success) { showToast('Item updated', 'success'); document.getElementById('editErpItemModal')?.classList.remove('active'); document.getElementById('editErpItemModal')?.classList.add('opacity-0','pointer-events-none'); loadErpItems(); } else showToast(res.error||'Failed', 'error'); }).catch(()=>showToast('Failed', 'error')).finally(()=>{ updateErpItemBtn.disabled = false; });
                })();
            });

            // ======================== ENTER NAVIGATION (All Item Management forms) ========================
            function getFocusableForId(id, isEditModal) {
                const el = document.getElementById(id);
                if (!el) return null;
                if (id === 'erpSkuFieldWrapper' || id === 'editErpSkuFieldWrapper') {
                    const input = el.querySelector('input');
                    return input || el;
                }
                if (el.tagName === 'SELECT' && el.__enhanced && el.__enhanced.control) return el.__enhanced.control;
                if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'BUTTON' || el.tagName === 'TEXTAREA') return el;
                const first = el.querySelector('input, select, button, textarea');
                return first || el;
            }
            function handleEnterNavigation(currentElement, currentFieldId, context) {
                let sequence, submitId, attrContainerId, attrInputClass, fixedIds, isCreate;
                if (context === 'category') {
                    sequence = ['erpCatName', 'erpCatCode', 'erpCatDesc', 'submitErpCategoryBtn'];
                    submitId = 'submitErpCategoryBtn';
                } else if (context === 'group') {
                    sequence = ['erpGroupCategory', 'erpGroupName', 'erpGroupCode', 'submitErpGroupBtn'];
                    submitId = 'submitErpGroupBtn';
                } else if (context === 'subgroup') {
                    sequence = ['erpSubGroupCategory', 'erpSubGroupGroup', 'erpSubGroupName', 'erpSubGroupCode', 'submitErpSubGroupBtn'];
                    submitId = 'submitErpSubGroupBtn';
                } else if (context === 'attribute') {
                    sequence = ['erpAttrCategory', 'erpAttrGroup', 'erpAttrSubGroup', 'erpAttrName', 'erpAttrRequired', 'submitErpAttributeBtn'];
                    submitId = 'submitErpAttributeBtn';
                } else {
                    isCreate = context === 'create';
                    fixedIds = isCreate
                        ? ['erpCategory', 'erpGroup', 'erpSubGroup', 'erpItemName', 'erpItemNameUrdu', 'erpUnitType', 'erpSkuFieldWrapper', 'erpItemCalculatedWeight', 'erpItemMinOrderLevel', 'erpItemMaxOrderLevel']
                        : ['editErpCategory', 'editErpGroup', 'editErpSubGroup', 'editErpUnitType', 'editErpSkuFieldWrapper', 'editErpItemName', 'editErpItemNameUrdu', 'editErpItemCalculatedWeight', 'editErpItemMinOrderLevel', 'editErpItemMaxOrderLevel'];
                    attrContainerId = isCreate ? 'erpAttributeFields' : 'editErpAttributeFields';
                    attrInputClass = isCreate ? 'erp-attribute-input' : 'edit-erp-attribute-input';
                    submitId = isCreate ? 'submitErpItemBtn' : 'updateErpItemBtn';
                    const attrContainer = document.getElementById(attrContainerId);
                    const attrInputs = attrContainer ? Array.from(attrContainer.querySelectorAll('.' + attrInputClass)) : [];
                    const attrIds = attrInputs.map(inp => inp.id || inp.name).filter(Boolean);
                    sequence = fixedIds.concat(attrIds).concat([submitId]);
                }
                let currentIndex = sequence.indexOf(currentFieldId);
                if (currentIndex === -1 && currentElement && attrContainerId && currentElement.closest('#' + attrContainerId)) {
                    const attrContainer = document.getElementById(attrContainerId);
                    const attrInputs = attrContainer ? Array.from(attrContainer.querySelectorAll('.' + attrInputClass)) : [];
                    const idx = attrInputs.indexOf(currentElement);
                    if (idx >= 0) currentIndex = fixedIds.length + idx;
                }
                if (currentIndex === -1) return;
                const nextIndex = currentIndex + 1;
                if (nextIndex >= sequence.length) {
                    if (currentFieldId === submitId) {
                        if (context === 'create') document.getElementById('erpItemForm')?.requestSubmit();
                        else if (context === 'edit') document.getElementById('updateErpItemBtn')?.click();
                        else if (context === 'category') document.getElementById('erpCategoryForm')?.requestSubmit();
                        else if (context === 'group') document.getElementById('erpGroupForm')?.requestSubmit();
                        else if (context === 'subgroup') document.getElementById('erpSubGroupForm')?.requestSubmit();
                        else if (context === 'attribute') document.getElementById('erpAttributeForm')?.requestSubmit();
                    }
                    return;
                }
                const nextId = sequence[nextIndex];
                let nextEl = document.getElementById(nextId);
                if (nextId === 'erpSkuFieldWrapper' || nextId === 'editErpSkuFieldWrapper')
                    nextEl = (document.getElementById(nextId) || {}).querySelector?.('input') || nextEl;
                else if (nextEl && nextEl.tagName === 'SELECT' && nextEl.__enhanced && nextEl.__enhanced.control)
                    nextEl = nextEl.__enhanced.control;
                if (nextEl) {
                    nextEl.focus();
                    if (nextEl.select && typeof nextEl.select === 'function') try { nextEl.select(); } catch (_) {}
                }
            }
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter' && e.key !== 'NumpadEnter') return;
                const target = e.target;
                if (target.tagName === 'TEXTAREA') return;
                // Don't steal Enter when a dropdown/select panel is open
                const openPanel = target.closest('.absolute') || target.closest('[class*="dropdown"]') || target.closest('.select2-container');
                if (openPanel && openPanel.offsetParent !== null) return;
                const inCategoryForm = document.getElementById('erpCategoryForm')?.contains(target);
                const inGroupForm = document.getElementById('erpGroupForm')?.contains(target);
                const inSubGroupForm = document.getElementById('erpSubGroupForm')?.contains(target);
                const inAttributeForm = document.getElementById('erpAttributeForm')?.contains(target);
                const inCreateForm = document.getElementById('erpItemForm')?.contains(target);
                const inEditModal = document.getElementById('editErpItemModal')?.contains(target);
                if (!inCategoryForm && !inGroupForm && !inSubGroupForm && !inAttributeForm && !inCreateForm && !inEditModal) return;
                e.preventDefault();
                e.stopPropagation();
                let currentFieldId = target.id;
                if (!currentFieldId && target.closest('#erpSkuFieldWrapper')) currentFieldId = 'erpSkuFieldWrapper';
                if (!currentFieldId && target.closest('#editErpSkuFieldWrapper')) currentFieldId = 'editErpSkuFieldWrapper';
                if (inCreateForm && (target.id === 'erpNormalizedSku' || target.id === 'erpNormalizedSkuNumber')) currentFieldId = 'erpSkuFieldWrapper';
                if (inEditModal && (target.id === 'editErpNormalizedSku' || target.id === 'editErpNormalizedSkuNumber')) currentFieldId = 'editErpSkuFieldWrapper';
                if (target.tagName === 'BUTTON' && target.closest('.relative')) {
                    const sel = target.parentNode.querySelector('select') || target.closest('.float-group')?.querySelector('select');
                    if (sel) currentFieldId = sel.id;
                }
                let context;
                if (inEditModal) context = 'edit';
                else if (inCreateForm) context = 'create';
                else if (inCategoryForm) context = 'category';
                else if (inGroupForm) context = 'group';
                else if (inSubGroupForm) context = 'subgroup';
                else if (inAttributeForm) context = 'attribute';
                if (currentFieldId && context) handleEnterNavigation(target, currentFieldId, context);
            });

            // Edit/Delete helpers for ERP master tables (inline - reload after)
            window.erpEditCategory = function(id, name, code, desc) { document.getElementById('erpCatName').value = name; document.getElementById('erpCatCode').value = code||''; document.getElementById('erpCatDesc').value = desc||''; const t=document.getElementById('submitErpCategoryText'); if(t)t.textContent='Update'; document.getElementById('erpCategoryForm').dataset.editId = id; };
            window.erpDeleteCategory = function(id) { if (!confirm('Delete this category?')) return; fetch(`../api/items/categories/${id}`, { method: 'DELETE', credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error(r.statusText); return r.json(); }).then(res=>{ if(res.success) { showToast('Deleted', 'success'); loadErpCategoriesTable(); loadErpCategories(); loadErpMasterDropdowns(); } else showToast(res.error||'Failed', 'error'); }).catch(e=>{ showToast('Delete failed: ' + (e.message||'Network error'), 'error'); }); };
            window.erpEditGroup = function(id, catId, name, code) { document.getElementById('erpGroupCategory').value = catId; document.getElementById('erpGroupCategory').dispatchEvent(new Event('change')); setTimeout(()=>{ document.getElementById('erpGroupName').value = name; document.getElementById('erpGroupCode').value = code||''; const t=document.getElementById('submitErpGroupText'); if(t)t.textContent='Update'; document.getElementById('erpGroupForm').dataset.editId = id; }, 300); };
            window.erpDeleteGroup = function(id) { if (!confirm('Delete this group?')) return; fetch(`../api/items/groups/${id}`, { method: 'DELETE', credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error(r.statusText); return r.json(); }).then(res=>{ if(res.success) { showToast('Deleted', 'success'); loadErpGroupsTable(document.getElementById('erpGroupCategory')?.value); loadErpCategories(); } else showToast(res.error||'Failed', 'error'); }).catch(e=>{ showToast('Delete failed: ' + (e.message||'Network error'), 'error'); }); };
            window.erpEditSubGroup = function(id, gid, name, code) { document.getElementById('erpSubGroupGroup').value = gid; document.getElementById('erpSubGroupGroup').dispatchEvent(new Event('change')); setTimeout(()=>{ document.getElementById('erpSubGroupName').value = name; document.getElementById('erpSubGroupCode').value = code||''; const t=document.getElementById('submitErpSubGroupText'); if(t)t.textContent='Update'; document.getElementById('erpSubGroupForm').dataset.editId = id; }, 300); };
            window.erpDeleteSubGroup = function(id) { if (!confirm('Delete this sub-group?')) return; fetch(`../api/items/sub-groups/${id}`, { method: 'DELETE', credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error(r.statusText); return r.json(); }).then(res=>{ if(res.success) { showToast('Deleted', 'success'); loadErpSubGroupsTable(document.getElementById('erpSubGroupGroup')?.value); loadErpCategories(); } else showToast(res.error||'Failed', 'error'); }).catch(e=>{ showToast('Delete failed: ' + (e.message||'Network error'), 'error'); }); };
            window.erpEditAttribute = function(id, sgId, name, required) { document.getElementById('erpAttrSubGroup').value = sgId; document.getElementById('erpAttrSubGroup').dispatchEvent(new Event('change')); setTimeout(()=>{ document.getElementById('erpAttrName').value = name; document.getElementById('erpAttrRequired').checked = !!required; const t=document.getElementById('submitErpAttributeText'); if(t)t.textContent='Update'; document.getElementById('erpAttributeForm').dataset.editId = id; }, 300); };
            window.erpDeleteAttribute = function(id) { if (!confirm('Delete this attribute?')) return; fetch(`../api/items/attributes/${id}`, { method: 'DELETE', credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error(r.statusText); return r.json(); }).then(res=>{ if(res.success) { showToast('Deleted', 'success'); loadErpAttributesTable(document.getElementById('erpAttrSubGroup')?.value); } else showToast(res.error||'Failed', 'error'); }).catch(e=>{ showToast('Delete failed: ' + (e.message||'Network error'), 'error'); }); };
            window.deleteErpItem = function(id) { if (!confirm('Delete this item?')) return; fetch(`../api/items/${id}`, { method: 'DELETE', credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error(r.statusText); return r.json(); }).then(res=>{ if(res.success) { showToast('Item deleted', 'success'); loadErpItems(); } else showToast(res.error||'Failed', 'error'); }).catch(e=>{ showToast('Delete failed: ' + (e.message||'Network error'), 'error'); }); };
        })();
    </script>

</body>

</html>