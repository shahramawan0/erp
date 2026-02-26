<?php
require_once 'includes/session_config.php';
requireAuth();
$currentUser = getCurrentUser();
$defaultRoleStatus = $currentUser['role_status'] ?? null;
?>
<!DOCTYPE html>
<html dir="ltr">
<?php include 'includes/head.php'; ?>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="main-content transition-all flex flex-col overflow-hidden min-h-screen" id="main-content">
        <div class="mb-[25px] md:flex items-center justify-between">
            <h5 class="!mb-0">Account Management</h5>
            <ol class="breadcrumb mt-[12px] md:mt-0">
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    <a href="index.php" class="inline-block relative ltr:pl-[22px] rtl:pr-[22px] transition-all hover:text-primary-500">
                        <i class="material-symbols-outlined absolute ltr:left-0 rtl:right-0 !text-lg -mt-px text-primary-500 top-1/2 -translate-y-1/2">home</i>
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px]">Chart of Code</li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px]">Account Management</li>
            </ol>
        </div>

        <div class="w-full mb-[25px]">
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md w-full">
                <div class="trezo-tabs" id="trezo-tabs">
                    <ul class="navs mb-[20px] border-b border-gray-100 dark:border-[#172036]">
                        <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                            <button type="button" data-tab="tab1" class="nav-link active block pb-[8px] transition-all relative font-medium">Main Head</button>
                        </li>
                        <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                            <button type="button" data-tab="tab2" class="nav-link block pb-[8px] transition-all relative font-medium">Control Head</button>
                        </li>
                        <li class="nav-item inline-block ltr:mr-[20px] rtl:ml-[20px]">
                            <button type="button" data-tab="tab3" class="nav-link block pb-[8px] transition-all relative font-medium">Accounts</button>
                        </li>
                    </ul>

                    <!-- Tab 1: Main Head -->
                    <div class="tab-pane active" id="tab1">
                        <form id="mainHeadForm" class="border-b pb-[15px] mb-[15px]">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-[10px] mb-[10px]">
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="mainHeadName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="mainHeadName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name</label>
                                </div>
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="mainHeadNameUrdu" name="name_in_urdu" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="mainHeadNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                </div>
                                <input type="hidden" id="mainHeadType" name="type" value="account">
                                <input type="hidden" id="mainHeadStatus" name="status" value="A">
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="mainHeadDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="mainHeadDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                </div>
                                <div class="mb-[10px]">
                                    <button type="submit" id="submitMainHeadBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 text-sm">
                                        <span id="submitMainHeadText">Create</span>
                                        <i class="material-symbols-outlined ml-1 text-sm align-middle">add</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                            <div class="trezo-card-content">
                                <div class="flex justify-between items-center mb-[15px]">
                                    <input type="text" id="searchMainHeadInput" placeholder="Search..." class="bg-gray-50 border border-gray-50 h-[36px] text-xs rounded-md w-full max-w-[240px] block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] rtl:pl-[13px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c]">
                                </div>
                                <div class="table-responsive overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="text-black dark:text-white">
                                            <tr>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">ID</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name (Urdu)</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Description</th>
                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black dark:text-white" id="mainHeadsTableBody"></tbody>
                                    </table>
                                </div>
                                <div class="px-[20px] py-[12px] border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                    <p class="!mb-0 text-sm" id="mainHeadPaginationInfo">Loading...</p>
                                    <ol class="mt-[10px] sm:mt-0" id="mainHeadPaginationControls"></ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Control Head -->
                    <div class="tab-pane" id="tab2" style="display:none;">
                        <form id="controlHeadForm" class="border-b pb-[15px] mb-[15px]">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-[10px] mb-[10px]">
                                <div class="mb-[10px] float-group">
                                    <select id="controlHeadMainHead" name="main_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                        <option value="">Main Head *</option>
                                    </select>
                                </div>
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="controlHeadName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="controlHeadName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name</label>
                                </div>
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="controlHeadNameUrdu" name="name_in_urdu" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="controlHeadNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                </div>
                                <input type="hidden" id="controlHeadType" name="type" value="account">
                                <div class="mb-[10px] float-group">
                                    <input type="text" id="controlHeadDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                    <label for="controlHeadDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                </div>
                                <div class="mb-[10px]">
                                    <button type="submit" id="submitControlHeadBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 text-sm">
                                        <span id="submitControlHeadText">Create</span>
                                        <i class="material-symbols-outlined ml-1 text-sm align-middle">add</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                            <div class="trezo-card-content">
                                <div class="flex justify-between items-center mb-[15px]">
                                    <input type="text" id="searchControlHeadInput" placeholder="Search..." class="bg-gray-50 border border-gray-50 h-[36px] text-xs rounded-md w-full max-w-[240px] block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] rtl:pl-[13px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c]">
                                </div>
                                <div class="table-responsive overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="text-black dark:text-white">
                                            <tr>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">ID</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Main Head</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name (Urdu)</th>
                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black dark:text-white" id="controlHeadsTableBody"></tbody>
                                    </table>
                                </div>
                                <div class="px-[20px] py-[12px] border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                    <p class="!mb-0 text-sm" id="controlHeadPaginationInfo">Loading...</p>
                                    <ol class="mt-[10px] sm:mt-0" id="controlHeadPaginationControls"></ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Accounts -->
                    <div class="tab-pane" id="tab3" style="display:none;">
                        <form id="accountForm" class="border-b pb-[15px] mb-[15px]">
                            <div class="mb-[20px]">
                                <h6 class="text-sm font-semibold text-primary-500 dark:text-primary-400 mb-[12px] pb-[8px] border-b border-gray-200 dark:border-[#172036] uppercase">Account Head</h6>
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-[10px] mb-[10px]">
                                    <div class="mb-[10px] float-group">
                                        <select id="accountMainHead" name="main_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Main Head *</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountControlHead" name="control_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Control Head *</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountAccountType" name="account_type" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Party Type</option>
                                            <option value="sale">Sale Party</option>
                                            <option value="purchase">Purchase Party</option>
                                        </select>
                                        <label for="accountAccountType" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Party Type</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-[20px]">
                                <h6 class="text-sm font-semibold text-primary-500 dark:text-primary-400 mb-[12px] pb-[8px] border-b border-gray-200 dark:border-[#172036] uppercase">Details</h6>
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-[10px] mb-[10px]">
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Name *</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountNameUrdu" name="name_in_urdu" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountNameUrdu" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">اردو میں نام</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountCell" name="cell" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountCell" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Cell</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountCity" name="city_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">City</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group lg:col-span-2">
                                        <input type="text" id="accountAddress" name="address" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountAddress" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Address</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountCompanyName" name="company_name" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountCompanyName" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Company Name</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountCompanyAddress" name="company_address" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountCompanyAddress" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Company Address</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountNtn" name="ntn" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountNtn" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">NTN</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountStn" name="stn" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountStn" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">STN</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountBank" name="bank_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Bank</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountCompanyType" name="company_type_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Company Type</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <select id="accountPaymentTerm" name="payment_term_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                                            <option value="">Payment Terms</option>
                                        </select>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="number" id="accountOpeningBalance" name="opening_balance" step="0.01" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountOpeningBalance" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Opening Balance</label>
                                    </div>
                                    <div class="mb-[10px] float-group">
                                        <input type="text" id="accountDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm" placeholder=" ">
                                        <label for="accountDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-[10px]">
                                <button type="submit" id="submitAccountBtn" class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 text-sm">
                                    <span id="submitAccountText">Create Account</span>
                                    <i class="material-symbols-outlined ml-1 text-sm align-middle">add</i>
                                </button>
                            </div>
                        </form>
                        <div class="trezo-card bg-white dark:bg-[#0c1427] rounded-md">
                            <div class="trezo-card-content">
                                <div class="flex justify-between items-center mb-[15px]">
                                    <input type="text" id="searchAccountInput" placeholder="Search by name, company, cell..." class="bg-gray-50 border border-gray-50 h-[36px] text-xs rounded-md w-full max-w-[280px] block text-black pt-[11px] pb-[12px] ltr:pl-[38px] rtl:pr-[38px] ltr:pr-[13px] rtl:pl-[13px] placeholder:text-gray-500 outline-0 dark:bg-[#15203c] dark:text-white dark:border-[#15203c]">
                                    <select id="filterAccountType" class="h-[36px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] text-sm">
                                        <option value="">All types</option>
                                        <option value="sale">Sale Party</option>
                                        <option value="purchase">Purchase Party</option>
                                    </select>
                                </div>
                                <div class="table-responsive overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="text-black dark:text-white">
                                            <tr>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Code</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Head</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Type</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Name</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Contact</th>
                                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Company</th>
                                                <th class="font-medium text-left px-[20px] py-[5px] bg-gray-50 dark:bg-[#15203c] whitespace-nowrap">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black dark:text-white" id="accountsTableBody"></tbody>
                                    </table>
                                </div>
                                <div class="px-[20px] py-[12px] border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                                    <p class="!mb-0 text-sm" id="accountPaginationInfo">Loading...</p>
                                    <ol class="mt-[10px] sm:mt-0" id="accountPaginationControls"></ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grow"></div>
    <?php include 'includes/footer.php'; ?>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>

    <!-- Edit Main Head Modal -->
    <div class="add-new-popup z-[999] fixed inset-0 overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editMainHeadModal">
        <div class="popup-dialog flex max-w-[550px] min-h-full items-center mx-auto">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] -mx-[20px] -mt-[20px] p-[20px] rounded-t-md flex justify-between items-center">
                    <h5 class="mb-0">Edit Main Head</h5>
                    <button type="button" class="text-[23px] leading-none text-black dark:text-white hover:text-primary-500" id="closeEditMainHeadModal"><i class="ri-close-fill"></i></button>
                </div>
                <form id="editMainHeadForm">
                    <input type="hidden" id="editMainHeadId" name="id">
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editMainHeadName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Name</label>
                    </div>
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editMainHeadNameUrdu" name="name_in_urdu" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">اردو میں نام</label>
                    </div>
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editMainHeadDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Description</label>
                    </div>
                </form>
                <div class="trezo-card-footer flex justify-end pt-[20px] border-t border-gray-100 dark:border-[#172036]">
                    <button type="button" id="updateMainHeadBtn" class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white rounded-md hover:bg-primary-400">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Control Head Modal -->
    <div class="add-new-popup z-[999] fixed inset-0 overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editControlHeadModal">
        <div class="popup-dialog flex max-w-[550px] min-h-full items-center mx-auto">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] -mx-[20px] -mt-[20px] p-[20px] rounded-t-md flex justify-between items-center">
                    <h5 class="mb-0">Edit Control Head</h5>
                    <button type="button" class="text-[23px] leading-none text-black dark:text-white hover:text-primary-500" id="closeEditControlHeadModal"><i class="ri-close-fill"></i></button>
                </div>
                <form id="editControlHeadForm">
                    <input type="hidden" id="editControlHeadId" name="id">
                    <div class="mb-[10px] float-group">
                        <select id="editControlHeadMainHead" name="main_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                            <option value="">Main Head *</option>
                        </select>
                    </div>
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editControlHeadName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Name</label>
                    </div>
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editControlHeadNameUrdu" name="name_in_urdu" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">اردو میں نام</label>
                    </div>
                    <div class="mb-[10px] float-group">
                        <input type="text" id="editControlHeadDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                        <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Description</label>
                    </div>
                </form>
                <div class="trezo-card-footer flex justify-end pt-[20px] border-t border-gray-100 dark:border-[#172036]">
                    <button type="button" id="updateControlHeadBtn" class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white rounded-md hover:bg-primary-400">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="add-new-popup z-[999] fixed inset-0 overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editAccountModal">
        <div class="popup-dialog flex max-w-[900px] min-h-full items-start mx-auto my-[20px]">
            <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] rounded-md">
                <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] -mx-[20px] -mt-[20px] p-[20px] rounded-t-md flex justify-between items-center">
                    <h5 class="mb-0">Edit Account</h5>
                    <button type="button" class="text-[23px] leading-none text-black dark:text-white hover:text-primary-500" id="closeEditAccountModal"><i class="ri-close-fill"></i></button>
                </div>
                <form id="editAccountForm">
                    <input type="hidden" id="editAccountId" name="id">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-[10px] mb-[10px]">
                        <div class="float-group">
                            <select id="editAccountMainHead" name="main_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">Main Head *</option></select>
                        </div>
                        <div class="float-group">
                            <select id="editAccountControlHead" name="control_head_id" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">Control Head *</option></select>
                        </div>
                        <div class="float-group">
                            <select id="editAccountAccountType" name="account_type" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm">
                                <option value="">Party Type</option>
                                <option value="sale">Sale Party</option>
                                <option value="purchase">Purchase Party</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-[10px] mb-[10px]">
                        <div class="float-group">
                            <input type="text" id="editAccountName" name="name" required class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Name *</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountNameUrdu" name="name_in_urdu" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">اردو میں نام</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountCell" name="cell" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Cell</label>
                        </div>
                        <div class="float-group">
                            <select id="editAccountCity" name="city_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">City</option></select>
                        </div>
                        <div class="float-group lg:col-span-2">
                            <input type="text" id="editAccountAddress" name="address" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Address</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountCompanyName" name="company_name" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Company Name</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountCompanyAddress" name="company_address" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Company Address</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountNtn" name="ntn" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">NTN</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountStn" name="stn" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">STN</label>
                        </div>
                        <div class="float-group">
                            <select id="editAccountBank" name="bank_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">Bank</option></select>
                        </div>
                        <div class="float-group">
                            <select id="editAccountCompanyType" name="company_type_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">Company Type</option></select>
                        </div>
                        <div class="float-group">
                            <select id="editAccountPaymentTerm" name="payment_term_id" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm"><option value="">Payment Terms</option></select>
                        </div>
                        <div class="float-group">
                            <input type="number" id="editAccountOpeningBalance" name="opening_balance" step="0.01" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Opening Balance</label>
                        </div>
                        <div class="float-group">
                            <input type="text" id="editAccountDescription" name="description" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full text-sm" placeholder=" ">
                            <label class="float-label bg-white dark:bg-[#15203c] text-gray-500">Description</label>
                        </div>
                    </div>
                </form>
                <div class="trezo-card-footer flex justify-end pt-[20px] border-t border-gray-100 dark:border-[#172036]">
                    <button type="button" id="updateAccountBtn" class="inline-block py-[6px] px-[16px] text-sm bg-primary-500 text-white rounded-md hover:bg-primary-400">Update</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script>window.defaultRoleStatus = '<?php echo addslashes($defaultRoleStatus ?? ''); ?>';</script>
    <script src="assets/js/account_management.js"></script>
</body>
</html>
