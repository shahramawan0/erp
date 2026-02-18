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

    <!-- Main Content -->
    <div class="main-content transition-all flex flex-col overflow-hidden min-h-screen" id="main-content">

        <!-- Breadcrumb -->
        <div class="mb-[25px] md:flex items-center justify-between">
            <h5 class="!mb-0">
                Misc Entries Management
            </h5>
            <ol class="breadcrumb mt-[12px] md:mt-0">
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    <a href="index.php" class="inline-block relative ltr:pl-[22px] rtl:pr-[22px] transition-all hover:text-primary-500">
                        <i class="material-symbols-outlined absolute ltr:left-0 rtl:right-0 !text-lg -mt-px text-primary-500 top-1/2 -translate-y-1/2">
                            home
                        </i>
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    Misc Entries
                </li>
            </ol>
        </div>

        <!-- Tabs -->
        <div class="w-full mb-[25px]" id="clickToSeeCode">
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md w-full">

                <div class="trezo-card-content">
                    <div class="trezo-tabs" id="trezo-tabs">
                        <ul class="navs mb-[20px] border-b border-gray-100 dark:border-[#172036]">
                            <?php
                            // Track the first accessible tab to mark it as active
                            $firstTabSet = false;
                            ?>
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab1" class="nav-link <?php echo !$firstTabSet ? 'active' : ''; ?> block pb-[8px] transition-all relative font-medium">
                                        Unit
                                    </button>
                                </li>
                                <?php if (!$firstTabSet) {
                                    $firstTabSet = true;
                                    $firstActiveTab = 'tab1';
                                } ?>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab3" class="nav-link <?php echo !$firstTabSet ? 'active' : ''; ?> block pb-[8px] transition-all relative font-medium">
                                        Unit Type
                                    </button>
                                </li>
                                <?php if (!$firstTabSet) {
                                    $firstTabSet = true;
                                    $firstActiveTab = 'tab3';
                                } ?>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab6" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Department
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab7" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Rack
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab8" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Item Type
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab11" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Cities
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab13" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Sizes
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab16" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Banks
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab17" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Company Type
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab18" class="nav-link block pb-[8px] transition-all relative font-medium">
                                        Payment Terms
                                    </button>
                                </li>
                            <?php endif; ?>

                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'SO' || $defaultRoleStatus == 'SM'): ?>
                                <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                                    <button type="button" data-tab="tab19" class="nav-link <?php echo !$firstTabSet ? 'active' : ''; ?> block pb-[8px] transition-all relative font-medium">
                                        Brand
                                    </button>
                                </li>
                                <?php if (!$firstTabSet) {
                                    $firstTabSet = true;
                                    $firstActiveTab = 'tab19';
                                } ?>
                            <?php endif; ?>
                        </ul>

                        <div class="tab-content">
                            <!-- Unit Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                <div class="tab-pane <?php echo (isset($firstActiveTab) && $firstActiveTab == 'tab1') ? 'active' : ''; ?>" id="tab1">
                                    <!-- Unit Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                        <form id="unitForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="unitName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="unitName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit Name</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="unitNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="unitNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 lg:col-span-2 col-span-1 float-group">
                                                    <input type="text" id="unitDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="unitDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitUnitBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Unit Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchUnitInput" placeholder="Search here....."
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
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>

                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>

                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>

                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>

                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>

                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="unitsTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="unitPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="unitPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Unit Type Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                <div class="tab-pane" id="tab3">
                                    <!-- Unit Type Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                        <form id="unitTypeForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="unitTypeName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="unitTypeName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Unit Type</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0  float-group">
                                                    <input type="text" id="unitTypeNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="اردو میں نام">
                                                    <label for="unitTypeNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="unitTypeDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="unitTypeDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitUnitTypeBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add unit types.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Unit Type Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchUnitTypeInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                    <!-- <div class="flex gap-2">
                                                <button onclick="exportTableData('unitType', 'csv')" class="inline-block py-[6px] px-[12px] bg-success-500 text-white transition-all hover:bg-success-400 rounded-md border border-success-500 hover:border-success-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    CSV
                                                </button>
                                                <button onclick="exportTableData('unitType', 'json')" class="inline-block py-[6px] px-[12px] bg-info-500 text-white transition-all hover:bg-info-400 rounded-md border border-info-500 hover:border-info-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    JSON
                                                </button>
                                            </div> -->
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>

                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="unitTypesTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="unitTypePaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="unitTypePaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view unit types.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab3">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access unit types.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Department Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                <div class="tab-pane" id="tab6">
                                    <!-- Department Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                        <form id="departmentForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="departmentName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="departmentName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Department</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="departmentNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="departmentNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="departmentDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="departmentDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <button type="submit" id="submitDepartmentBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add departments.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Department Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchDepartmentInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                    <!-- <div class="flex gap-2">
                                                <button onclick="exportTableData('department', 'csv')" class="inline-block py-[6px] px-[12px] bg-success-500 text-white transition-all hover:bg-success-400 rounded-md border border-success-500 hover:border-success-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    CSV
                                                </button>
                                                <button onclick="exportTableData('department', 'json')" class="inline-block py-[6px] px-[12px] bg-info-500 text-white transition-all hover:bg-info-400 rounded-md border border-info-500 hover:border-info-400 text-sm">
                                                    <i class="material-symbols-outlined text-sm ltr:mr-[4px] rtl:ml-[4px]">download</i>
                                                    JSON
                                                </button>
                                            </div> -->
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
               bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
               bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
               bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] 
               bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>

                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="departmentsTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="departmentPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="departmentPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view departments.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab6">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access departments.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Rack Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                <div class="tab-pane" id="tab7">
                                    <!-- Rack Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                        <form id="rackForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <select id="rackUnitId" name="unit_id" required data-float-select
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                        <option value="" disabled selected hidden></option>
                                                    </select>
                                                    <label for="rackUnitId" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Unit</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="rackName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="rackName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Rack</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="rackNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="rackNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="rackDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="rackDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>

                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitRackBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add racks.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Rack Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <div class="flex gap-[10px] items-center">
                                                        <form class="relative sm:w-[240px]">
                                                            <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                                <i class="material-symbols-outlined !text-[20px]">search</i>
                                                            </label>
                                                            <input type="text" id="searchRackInput" placeholder="Search here....."
                                                                class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                        </form>

                                                        <!-- Unit Filter -->
                                                        <div class="relative sm:w-[300px]">
                                                            <select id="rackUnitFilter" name="unit_filter" data-float-select
                                                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                                                <option value="">All Units</option>
                                                            </select>
                                                            <label for="rackUnitFilter" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Filter by Unit</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tl-md cursor-pointer" onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer" onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Unit
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer" onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer" onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer" onclick="sortTable(4, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] 
                                   bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="racksTableBody"></tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="rackPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="rackPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view racks.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab7">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access racks.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Item Type Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                <div class="tab-pane" id="tab8">
                                    <!-- Item Type Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'PA'): ?>
                                        <form id="itemTypeForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="itemTypeName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="itemTypeName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item Type Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="itemTypeNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="itemTypeNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 lg:col-span-2 col-span-1 float-group">
                                                    <input type="text" id="itemTypeDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="itemTypeDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitItemTypeBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitControlHeadText">Create</span>
                                                        <span id="submitControlHeadIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add item types.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Item Type Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchItemTypeInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="itemTypesTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="itemTypePaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="itemTypePaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view item types.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab8">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access item types.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Cities Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <div class="tab-pane" id="tab11">
                                    <!-- Cities Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <form id="cityForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="cityName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="cityName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">City Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="cityNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="cityNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitCityBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitCityBtnText">Create</span>
                                                        <span id="submitCityBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add cities.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Cities Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchCityInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#172036] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#172036] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA' || $defaultRoleStatus == 'FT'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#172036] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="citiesTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="cityPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="cityPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view cities.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab11">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access cities.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Sizes Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                <div class="tab-pane" id="tab13">
                                    <!-- Sizes Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                        <form id="sizesForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="sizesName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="sizesName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Size Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitSizesBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitSizesBtnText">Create</span>
                                                        <span id="submitSizesBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to add sizes.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Sizes Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'SA'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchSizesInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[13px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#172036] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#172036] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="sizesTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="sizesPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="sizesPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-gray-500">
                                            You don't have permission to view sizes.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="tab13">
                                    <div class="p-4 text-center text-gray-500">
                                        You don't have permission to access sizes.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Banks Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <div class="tab-pane" id="tab16">
                                    <!-- Bank Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <form id="bankForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="bankName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="bankName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Bank Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="bankNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="bankNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 lg:col-span-2 col-span-1 float-group">
                                                    <input type="text" id="bankDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="bankDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitBankBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitBankBtnText">Create</span>
                                                        <span id="submitBankBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Bank Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchBankInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA' || $defaultRoleStatus == 'FT'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="banksTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="bankPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="bankPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Company Type Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <div class="tab-pane" id="tab17">
                                    <!-- Company Type Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <form id="companyTypeForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="companyTypeName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="companyTypeName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Company Type Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="companyTypeNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="companyTypeNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 lg:col-span-2 col-span-1 float-group">
                                                    <input type="text" id="companyTypeDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="companyTypeDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitCompanyTypeBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitCompanyTypeBtnText">Create</span>
                                                        <span id="submitCompanyTypeBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Company Type Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA' || $defaultRoleStatus == 'FT'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchCompanyTypeInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA' || $defaultRoleStatus == 'FT'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="companyTypesTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="companyTypePaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="companyTypePaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Payment Terms Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                <div class="tab-pane" id="tab18">
                                    <!-- Payment Term Form -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <form id="paymentTermForm" class="border-b pb-[15px] mb-[15px]">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="paymentTermName" name="name" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="paymentTermName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Payment Term Name</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                    <input type="text" id="paymentTermNameUrdu" name="name_in_urdu" required
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="paymentTermNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0 lg:col-span-2 col-span-1 float-group">
                                                    <input type="text" id="paymentTermDescription" name="description"
                                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                        placeholder="">
                                                    <label for="paymentTermDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                                </div>
                                                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                    <button type="submit" id="submitPaymentTermBtn"
                                                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                        <span id="submitPaymentTermBtnText">Create</span>
                                                        <span id="submitPaymentTermBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Payment Term Table -->
                                    <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA'  || $defaultRoleStatus == 'FT'): ?>
                                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                            <div class="trezo-card-content">
                                                <div class="flex justify-between items-center mb-[15px]">
                                                    <form class="relative sm:w-[240px]">
                                                        <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                            <i class="material-symbols-outlined !text-[20px]">search</i>
                                                        </label>
                                                        <input type="text" id="searchPaymentTermInput" placeholder="Search here....."
                                                            class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                    </form>
                                                </div>
                                                <div class="table-responsive overflow-x-auto">
                                                    <table class="w-full">
                                                        <thead class="text-black dark:text-white">
                                                            <tr>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(0, this)">
                                                                    <div class="flex items-center">
                                                                        Code
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(1, this)">
                                                                    <div class="flex items-center">
                                                                        Name
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(2, this)">
                                                                    <div class="flex items-center">
                                                                        Name in Urdu
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                    onclick="sortTable(3, this)">
                                                                    <div class="flex items-center">
                                                                        Description
                                                                        <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                    </div>
                                                                </th>
                                                                <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'FA' || $defaultRoleStatus == 'FT'): ?>
                                                                    <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                        Actions
                                                                    </th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-black dark:text-white" id="paymentTermsTableBody">
                                                            <!-- Data will be loaded dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                    <p class="!mb-0 text-sm" id="paymentTermPaginationInfo">Loading...</p>
                                                    <ol class="mt-[10px] sm:mt-0" id="paymentTermPaginationControls"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Brand Tab -->
                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'SO' || $defaultRoleStatus == 'SM'): ?>
                                <div class="tab-pane <?php echo (isset($firstActiveTab) && $firstActiveTab == 'tab19') ? 'active' : ''; ?>" id="tab19">
                                    <!-- Brand Form -->
                                    <form id="brandForm" class="border-b pb-[15px] mb-[15px]">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[10px] mb-[5px]">
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                                <input type="text" id="brandName" name="name" required
                                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                                    placeholder="">
                                                <label for="brandName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Brand Name</label>
                                            </div>
                                            <div class="mb-[10px] md:mb-[10px] last:mb-0">
                                                <button type="submit" id="submitBrandBtn"
                                                    class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                                                    <span id="submitBrandBtnText">Create</span>
                                                    <span id="submitBrandBtnIcon" class="material-symbols-outlined ml-1 text-sm" style="vertical-align: middle;">add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Brand Table -->
                                    <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                                        <div class="trezo-card-content">
                                            <div class="flex justify-between items-center mb-[15px]">
                                                <form class="relative sm:w-[240px]">
                                                    <label class="leading-none absolute ltr:left-[13px] rtl:right-[13px] text-black dark:text-white mt-px top-1/2 -translate-y-1/2">
                                                        <i class="material-symbols-outlined !text-[20px]">search</i>
                                                    </label>
                                                    <input type="text" id="searchBrandInput" placeholder="Search here....."
                                                        class="bg-gray-50 border border-gray-50 h-[30px] text-xs rounded-md w-full block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] ltr:md:pr-[16px] rtl:pl-[13px] rtl:md:pl-[16px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c] dark:placeholder:text-gray-400">
                                                </form>
                                            </div>
                                            <div class="table-responsive overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="text-black dark:text-white">
                                                        <tr>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                onclick="sortTable(0, this)">
                                                                <div class="flex items-center">
                                                                    Code
                                                                    <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                </div>
                                                            </th>
                                                            <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap cursor-pointer"
                                                                onclick="sortTable(1, this)">
                                                                <div class="flex items-center">
                                                                    Name
                                                                    <span class="material-symbols-outlined text-xs ml-1 opacity-50 sort-icon">unfold_more</span>
                                                                </div>
                                                            </th>
                                                            <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A' || $defaultRoleStatus == 'PA' || $defaultRoleStatus == 'SA' || $defaultRoleStatus == 'SO' || $defaultRoleStatus == 'SM'): ?>
                                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap first:rounded-tr-md">
                                                                    Actions
                                                                </th>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-black dark:text-white" id="brandsTableBody">
                                                        <!-- Data will be loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                                <p class="!mb-0 text-sm" id="brandPaginationInfo">Loading...</p>
                                                <ol class="mt-[10px] sm:mt-0" id="brandPaginationControls"></ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->

    <!-- Edit Modals -->

    <!-- Edit Unit Modal -->
    <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto" id="editUnitModal">
        <div class="popup-dialog flex transition-all max-w-[550px] min-h-full items-center mx-auto">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                    <div class="trezo-card-title">
                        <h5 class="mb-0">
                            Edit Unit
                        </h5>
                    </div>
                    <div class="trezo-card-subtitle">
                        <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" onclick="closeEditModal('editUnitModal')">
                            <i class="ri-close-fill"></i>
                        </button>
                    </div>
                </div>
                <form id="editUnitForm">
                    <input type="hidden" id="editUnitId" name="id">
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <div class="mb-[20px] float-group">

                            <input type="text" id="editUnitName" name="name" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editUnitName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>

                        </div>
                        <div class="mb-[20px] float-group">

                            <input type="text" id="editUnitNameUrdu" name="name_in_urdu" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editUnitNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>

                        </div>
                        <div class="mb-[20px] float-group">

                            <input type="text" id="editUnitDescription" name="description"
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editUnitDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>

                        </div>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">

                        <button type="submit"
                            class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400">
                            Update Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Generic Modal (Unit Type, Department, Item Type, City, Sizes, Bank, Company Type, Payment Term, Brand) -->
    <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto" id="editGenericModal">
        <div class="popup-dialog flex transition-all max-w-[550px] min-h-full items-center mx-auto">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                    <div class="trezo-card-title">
                        <h5 class="mb-0" id="editGenericModalTitle">
                            Edit
                        </h5>
                    </div>
                    <div class="trezo-card-subtitle">
                        <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" onclick="closeEditModal('editGenericModal')">
                            <i class="ri-close-fill"></i>
                        </button>
                    </div>
                </div>
                <form id="editGenericForm">
                    <input type="hidden" id="editGenericId" name="id">
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editGenericName" name="name" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editGenericName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Name</label>
                        </div>
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editGenericNameUrdu" name="name_in_urdu" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editGenericNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                        </div>
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editGenericDescription" name="description"
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editGenericDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>
                        </div>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button type="submit"
                            class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Rack Modal -->
    <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto" id="editRackModal">
        <div class="popup-dialog flex transition-all max-w-[550px] min-h-full items-center mx-auto">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                    <div class="trezo-card-title">
                        <h5 class="mb-0">Edit Rack</h5>
                    </div>
                    <div class="trezo-card-subtitle">
                        <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" onclick="closeEditModal('editRackModal')">
                            <i class="ri-close-fill"></i>
                        </button>
                    </div>
                </div>
                <form id="editRackForm">
                    <input type="hidden" id="editRackId" name="id">
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <div class="mb-[20px] float-group">
                            <select id="editRackUnitId" name="unit_id" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                <option value="" disabled selected hidden></option>
                            </select>
                            <label for="editRackUnitId" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Unit</label>
                        </div>
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editRackName" name="name" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editRackName" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Rack Name</label>
                        </div>
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editRackNameUrdu" name="name_in_urdu" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editRackNameUrdu" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                        </div>
                        <div class="mb-[20px] float-group">
                            <input type="text" id="editRackDescription" name="description"
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500">
                            <label for="editRackDescription" class="float-label bg-white dark:bg-[#15203c] text-gray-500 dark:text-gray-400">Description</label>
                        </div>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button type="submit"
                            class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white transition-all hover:bg-primary-400 rounded-md border border-primary-500 hover:border-primary-400">
                            Update Rack
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>

    <?php include 'includes/scripts.php'; ?>

    <!-- Pass user permissions and config to JavaScript -->
    <script>
        window.userPermissions = <?php echo json_encode($defaultRoleStatus); ?>;
        const defaultUnitId = <?php echo json_encode($defaultUnitId ?? null); ?>;
        const defaultUnitName = <?php echo json_encode($defaultUnitName ?? null); ?>;
        // API base path - ensures correct resolution from frontend/
        window.API_BASE = <?php echo json_encode(rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/') . '/api'); ?>;
    </script>
    <script src="assets/js/misc-entries.js"></script>

</body>
</html>
