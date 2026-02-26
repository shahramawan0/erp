<?php
// Include session configuration
require_once 'includes/session_config.php';

// Check if user is authenticated
requireAuth();

// Get current user data for default unit
$currentUser = getCurrentUser();
$defaultUnitId = $currentUser['unit_id'] ?? null;
$defaultUnitName = $currentUser['unit_name'] ?? null;
?>
<!DOCTYPE html>
<html dir="ltr">

<?php include 'includes/head.php'; ?>

<style>
    .store-badge {
        background: linear-gradient(135deg, #059669 0%, #10B981 100%);
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }



    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .status-badge.active {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-badge.completed {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Add item button styling to match account_management.php (primary theme) */
    .add-item-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all .2s ease;
        background: var(--tw-color-primary-500, #6366f1);
        color: #fff;
        border-color: var(--tw-color-primary-500, #6366f1);
    }

    .add-item-button:hover {
        background: var(--tw-color-primary-400, #818cf8);
        border-color: var(--tw-color-primary-400, #818cf8);
        transform: translateY(-1px);
    }

    .add-item-button .add-icon {
        font-size: 18px;
        line-height: 1;
    }
</style>

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
            <h5 class="!mb-0">Store Opening Stock</h5>
            <ol class="breadcrumb mt-[12px] md:mt-0">
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px] ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0">
                    <a href="index.php" class="inline-block relative ltr:pl-[22px] rtl:pr-[22px] transition-all hover:text-primary-500">
                        <i class="material-symbols-outlined absolute ltr:left-0 rtl:right-0 !text-lg -mt-px text-primary-500 top-1/2 -translate-y-1/2">home</i>
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px]">Store Management</li>
                <li class="breadcrumb-item inline-block relative text-sm mx-[11px]">Store Opening Stock</li>
            </ol>
        </div>

        <!-- Edit Mode Controls -->
        <div id="editModeControls" class="trezo-card bg-blue-50 dark:bg-blue-900/20 p-[20px] md:p-[25px] rounded-md w-full mb-4 border border-blue-200 dark:border-blue-800" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">edit</i>
                    <div>
                        <h5 class="!mb-0 text-blue-700 dark:text-blue-300 font-bold">Edit Mode</h5>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">Editing Opening Stock Voucher #<span id="editingVoucherNumber"></span></p>
                    </div>
                </div>
                <div class="mb-[10px] md:mb-[10px] last:mb-0">
                    <button type="button" id="cancelEditBtn"
                        class="inline-block rounded-md border border-primary-500 bg-primary-500 text-white h-[30px] px-[10px] transition-all hover:bg-primary-400 hover:border-primary-400 text-sm">
                        <span>Cancel Edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Interface -->
        <?php if ($defaultRoleStatus != 'PM'): ?>
        <div class="mb-[25px]">
            <!-- Full Width: Store Opening Stock Form and Table -->
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[15px] md:p-[18px] rounded-md h-[500px]">
                <!-- Opening Stock Information Section -->
                <div class="mb-[15px] p-[12px] bg-gray-50 dark:bg-[#15203c] rounded-md">
                    <!-- First Row: Date, and Unit (Voucher # is auto-generated on save) -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-3 gap-[10px]">
                            <!-- Hidden voucher field (not shown to user, auto-generated on save) -->
                            <input type="hidden" id="voucher" name="voucher" value="">
                            <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                <input type="date" id="voucher_date" value="<?php echo date('Y-m-d'); ?>" 
                                    class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                    placeholder="">
                                <label for="voucher_date" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Date</label>
                            </div>

                        <div class="h-[30px] mb-[10px] md:mb-[10px] last:mb-0 float-group">
                                <select id="unit" name="unit" data-float-select class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                <option value=""></option>
                                </select>
                                <label for="unit" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit</label>
                        </div>
                    </div>
                </div>

                <!-- Item Entry Section -->
                <div class="mb-[15px] p-[12px] bg-gray-50 dark:bg-[#15203c] rounded-md">
                    <!-- Item Fields Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 md:grid-cols-3 gap-[10px] mb-[10px]">
                        <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group h-[30px]">
                            <select id="item_id" name="item_id" data-float-select class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                <option value=""></option>
                            </select>
                            <label for="item_id" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item</label>
                        </div>
                        <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group h-[30px]">
                            <select id="rack_id" name="rack_id" data-float-select class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                                <option value=""></option>
                            </select>
                            <label for="rack_id" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Rack</label>
                        </div>
                       
                        <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group h-[30px]">
                            <input type="number" id="qty" name="qty" 
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm"
                                placeholder="" min="0" step="1">
                            <label for="qty" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Quantity</label>
                        </div>
                        <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group h-[30px]">
                            <input type="text" id="narration" name="narration" 
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500 text-sm">
                            <label for="narration" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                        </div>
                        <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group h-[30px]">
                            <button type="button" id="add_item_btn" class="h-[30px] w-full rounded-md border border-primary-500 bg-primary-500 text-white transition-all hover:bg-primary-400 hover:border-primary-400 flex items-center justify-center">
                                <i class="material-symbols-outlined mr-[8px]">add</i>
                                <span>Add Item</span>
                            </button>
                        </div>

                        <!-- <div class="mb-[15px]">
                            <button type="button" onclick="addItemToTable()" class="h-[30px] w-full rounded-md border border-primary-500 bg-primary-500 text-white transition-all hover:bg-primary-400 hover:border-primary-400 flex items-center justify-center">
                                <i class="material-symbols-outlined mr-[8px]">add</i>
                                <span>Add</span>
                            </button>
                        </div> -->
                    </div>
                <!-- Table Container -->
                <div style="overflow-x: auto; overflow-y: auto; max-height: calc(500px - 200px);">
                    <table id="storeOpeningStockTable" style="width: 100%; min-width: 800px; border-collapse: separate; border-spacing: 0; background: white;">
                        <thead>
                            <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 4px 6px; text-align:left; font-weight:700; color:#111827; border-right:1px solid #e5e7eb; min-width: 200px;">Item</th>
                                <th style="padding: 4px 6px; text-align:left; font-weight:700; color:#111827; border-right:1px solid #e5e7eb; min-width: 120px;">Rack</th>
                                <th style="padding: 4px 6px; text-align:center; font-weight:700; color:#111827; border-right:1px solid #e5e7eb; min-width: 100px;">Quantity</th>
                                <th style="padding: 4px 6px; text-align:left; font-weight:700; color:#111827; border-right:1px solid #e5e7eb; min-width: 200px;">Description</th>
                                <th style="padding: 4px 6px; text-align:center; font-weight:700; color:#111827; border-right:1px solid #e5e7eb; min-width: 80px;">Edit</th>
                                <th style="padding: 4px 6px; text-align:center; font-weight:700; color:#111827; min-width: 80px;">Del</th>
                            </tr>
                        </thead>
                            <tbody></tbody>
                    </table>
                    </div>
                </div>



                <!-- Submit Button -->
                <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
                    <button type="button" id="post_btn" class="h-[30px] w-full rounded-md border border-primary-500 bg-primary-500 text-white transition-all hover:bg-primary-400 hover:border-primary-400 flex items-center justify-center">
                        <i class="material-symbols-outlined mr-[8px]">check_circle</i>
                        <span>Post Opening Stock</span>
                    </button>
                </div>


                <!-- <div class="mb-[10px] md:mb-[10px] last:mb-0 float-group">
<button  type="button" id="submitIssuance" onclick="submitIssuance()" class="h-[30px] w-full rounded-md border border-primary-500 bg-primary-500 text-white transition-all hover:bg-primary-400 hover:border-primary-400 flex items-center justify-center">
                                        <i class="material-symbols-outlined">check</i>
                                        <span>Submit Issuance</span>
                                    </button>
                 </div> -->
            </div>
        </div>
        <?php endif; ?>

        <!-- Store Opening Stock Listing Section -->
        <div class="mb-[25px]">
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[15px] md:p-[18px] rounded-md">
                <div class="flex items-center justify-between mb-[20px]">
                    <h5 class="!mb-0">Store Opening Stock Records</h5>
                    <!-- <div class="flex items-center gap-[10px]">
                        <button type="button" id="refresh-listing" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-green-500/50 min-w-[120px]">
                            <i class="material-symbols-outlined text-lg">refresh</i>
                            <span>Refresh</span>
                        </button>
                    </div> -->
                </div>

                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-[15px] mb-[20px]">
                    <div class="float-group">
                        <select id="listing-unit-filter" data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                            <option value="">All Units</option>
                        </select>
                        <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Filter by Unit</label>
                    </div>
                    <div class="float-group">
                        <input type="date" id="listing-date-filter" class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                        <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Filter by Date</label>
                    </div>
                    <div class="float-group">
                        <input type="text" id="listing-search-filter" placeholder="Search Voucher #, Remarks..." class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                        <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Search</label>
                    </div>
                    <div class="float-group">
                        <select id="listing-limit-filter" data-float-select class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] block w-full outline-0 transition-all focus:border-primary-500 text-sm">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                        <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Records per page</label>
                    </div>
                </div>

                <!-- Store Opening Stock Listing Table -->
                <div class="table-responsive overflow-x-auto">
                    <table class="w-full" style="min-width: 1000px;">
                        <thead class="text-black dark:text-white">
                            <tr>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Voucher #</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Date</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Unit</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Items Count</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Total Quantity</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Items</span>
                                    </div>
                                </th>
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Racks</span>
                                    </div>
                                </th>
                                
                                <th class="font-medium ltr:text-left rtl:text-right px-[20px] py-[5px] bg-gray-50 dark:bg-[#0c1427] whitespace-nowrap">
                                    <div class="flex items-center gap-[8px]">
                                        <span>Actions</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="storeOpeningStockListingTableBody">
                            <!-- Table content will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-[20px] py-[12px] md:py-[14px] rounded-b-md border-l border-r border-b border-gray-100 dark:border-[#172036] sm:flex sm:items-center justify-between">
                    <p class="!mb-0 text-sm" id="listing-page-info">Showing 0 of 0 records</p>
                    <ol class="mt-[10px] sm:mt-0" id="openingStockPagination"></ol>
                </div>
            </div>
        </div>



        <!-- Edit Opening Stock Modal -->
        <div class="add-new-popup z-[999] fixed transition-all inset-0 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 opacity-0 pointer-events-none" id="editOpeningStockModal">
            <div class="popup-dialog flex transition-all max-w-[800px] min-h-full items-center mx-auto">
                <div class="trezo-card w-full bg-white dark:bg-[#0c1427] p-[20px] md:p-[25px] rounded-md">
                    <div class="trezo-card-header bg-gray-50 dark:bg-[#15203c] mb-[20px] md:mb-[25px] flex items-center justify-between -mx-[20px] md:-mx-[25px] -mt-[20px] md:-mt-[25px] p-[20px] md:p-[25px] rounded-t-md">
                        <div class="trezo-card-title">
                            <h5 class="mb-0">
                                Edit Opening Stock
                            </h5>
                        </div>
                        <div class="trezo-card-subtitle">
                            <button type="button" class="text-[23px] transition-all leading-none text-black dark:text-white hover:text-primary-500" id="closeEditOpeningStockModal">
                                <i class="ri-close-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trezo-card-content pb-[20px] md:pb-[25px]">
                        <form id="editOpeningStockForm">
                            <input type="hidden" id="editOpeningStockId" name="id">
                            
                            <!-- Header Information -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-[15px] mb-[20px]">
                                <div class="relative float-group">
                                    <input type="text" id="editVoucherNo" name="voucher_no" readonly
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-gray-100 dark:bg-[#1a2332] px-[12px] block w-full outline-0 transition-all text-sm cursor-not-allowed">
                                    <label for="editVoucherNo" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Voucher #</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="date" id="editVoucherDate" name="voucher_date" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                        value="${voucherData.voucher_date || ''}">
                                    <label for="editVoucherDate" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Date</label>
                                </div>
                                <div class="relative float-group">
                                    <select id="editUnit" name="unit_id" required data-float-select
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                        <option value=""></option>
                                    </select>
                                    <label for="editUnit" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit</label>
                                </div>
                            </div>

                            <!-- Item Details -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-[15px] mb-[20px]">
                                <div class="relative float-group">
                                    <select id="editItem" name="item_id" required data-float-select
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                        <option value=""></option>
                                    </select>
                                    <label for="editItem" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item</label>
                                </div>
                                <div class="relative float-group">
                                    <select id="editRack" name="rack_id" required data-float-select
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                        <option value=""></option>
                                    </select>
                                    <label for="editRack" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Rack</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="number" id="editQuantity" name="qty" required
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                        placeholder="" min="0" step="1">
                                    <label for="editQuantity" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Quantity</label>
                                </div>
                                <div class="relative float-group">
                                    <input type="text" id="editDescription" name="narration"
                                        class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                        placeholder="Optional description">
                                    <label for="editDescription" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="trezo-card-footer flex items-center justify-end -mx-[20px] md:-mx-[25px] px-[20px] md:px-[25px] pt-[20px] md:pt-[25px] border-t border-gray-100 dark:border-[#172036]">
                        <button class="inline-flex items-center justify-center gap-2 px-6 py-2 bg-white hover:bg-gray-50 dark:bg-[#0c1427] dark:hover:bg-[#15203c] text-gray-700 dark:text-gray-300 font-medium rounded-md transition-all border border-gray-300 dark:border-[#172036] hover:border-gray-400 dark:hover:border-[#1a2332] min-w-[120px]" type="button" id="cancelEditOpeningStockBtn">
                            <i class="material-symbols-outlined text-lg">close</i>
                            <span>Cancel</span>
                        </button>
                        <button class="inline-flex items-center justify-center gap-2 px-6 py-2 bg-primary-500 hover:bg-primary-400 text-white font-medium rounded-md transition-all focus:outline-none disabled:opacity-75 disabled:cursor-not-allowed min-w-[140px]" type="button" id="updateOpeningStockBtn">
                            <i class="material-symbols-outlined text-lg">save</i>
                            <span>Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
    
    <!-- Include Opening Stock Modal -->
    <?php include 'modals/opening_stock_view_modal.php'; ?>
    
    <!-- Include Opening Stock PDF Generator -->
    <script src="pdf/opening_stock_pdf.js"></script>
    
    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>
    
    <script>
        // Default unit configuration from PHP
        const defaultUnitId = <?php echo json_encode($defaultUnitId); ?>;
        const defaultUnitName = <?php echo json_encode($defaultUnitName); ?>;
        
        console.log('Store Opening Stock - Default unit configuration:', {
            defaultUnitId: defaultUnitId,
            defaultUnitName: defaultUnitName
        });

        // Custom Search Functionality for Dropdowns
        function upgradeSelectToSearchable(selectId, placeholder) {
            const selectEl = document.getElementById(selectId);
            if (!selectEl || selectEl.__enhanced) return;
            selectEl.classList.add('opacity-0', 'pointer-events-none', 'absolute');
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
                // Clear the list completely first
                list.innerHTML = '';
                currentItems = [];
                activeIndex = -1;

                // Special handling for items dropdown - search from API if filter is provided
                if (selectEl.id === 'item_id' && filter && filter.length > 2) {
                    // Check if we're already searching for this term
                    if (selectEl.lastSearchTerm === filter) {
                        // Use existing options instead of searching again
                        buildItemsFromOptions(filter);
                        return;
                    }

                    // Show loading indicator
                    list.innerHTML = '<div class="px-[12px] py-[6px] text-sm text-gray-500">Searching...</div>';

                    // Store the current search term
                    selectEl.lastSearchTerm = filter;

                    // Search items from API
                    searchItems(filter).then(() => {
                        // Rebuild items list with new options
                        buildItemsFromOptions(filter);
                    });
                    return;
                }

                // For other dropdowns or no filter, use existing options
                buildItemsFromOptions(filter);
            }

            function buildItemsFromOptions(filter = '') {
                // Clear the list completely
                list.innerHTML = '';
                currentItems = [];
                activeIndex = -1;

                const options = Array.from(selectEl.options);
                const addedValues = new Set(); // Track added values to prevent duplicates

                options.forEach(opt => {
                    if (opt.value === '') return; // Skip empty options

                    const text = opt.text || '';
                    if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;

                    // Check if this value has already been added to prevent duplicates
                    if (addedValues.has(opt.value)) {
                        return; // Skip duplicate values
                    }
                    addedValues.add(opt.value);

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
                // Debounce search to avoid too many API calls
                clearTimeout(search.debounceTimer);
                search.debounceTimer = setTimeout(() => {
                    // Only search if there's actually a value and it's different from the last search
                    const currentValue = search.value.trim();
                    if (currentValue && currentValue !== search.lastSearchedValue) {
                        search.lastSearchedValue = currentValue;
                        buildItems(currentValue);
                    }
                }, 500); // Increased from 300ms to 500ms for better debouncing
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
                refresh: () => {
                    // Clear the list first to prevent duplicates
                    list.innerHTML = '';
                    currentItems = [];
                    activeIndex = -1;
                    buildItems(search.value || '');
                },
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

        // Function to set default unit for all unit selects
        function setDefaultUnitForAllSelects() {
            if (!defaultUnitId) {
                console.log('No default unit ID available');
                return;
            }

            console.log('Setting default unit for all selects:', defaultUnitId);

            // Set default unit for main form unit select
            const unitSelect = document.getElementById('unit');
            if (unitSelect) {
                console.log('Setting default unit for main unit select');
                unitSelect.value = defaultUnitId;
                unitSelect.disabled = true;
                unitSelect.style.backgroundColor = '#f3f4f6';
                unitSelect.style.cursor = 'not-allowed';
                unitSelect.title = 'Unit is automatically set to your assigned unit';

                // Handle enhanced select control
                if (unitSelect.__enhanced && unitSelect.__enhanced.control) {
                    unitSelect.__enhanced.control.disabled = true;
                    unitSelect.__enhanced.control.style.backgroundColor = '#f3f4f6';
                    unitSelect.__enhanced.control.style.cursor = 'not-allowed';
                    unitSelect.__enhanced.control.title = 'Unit is automatically set to your assigned unit';
                    
                    // Update the display text
                    const selectedOption = unitSelect.options[unitSelect.selectedIndex];
                    if (selectedOption && unitSelect.__enhanced.control.querySelector('span')) {
                        unitSelect.__enhanced.control.querySelector('span').textContent = selectedOption.text;
                        unitSelect.__enhanced.control.querySelector('span').className = ''; // Remove gray text class
                    }
                    
                    // Call setDisplayFromValue to ensure proper display
                    if (unitSelect.__enhanced.setDisplayFromValue) {
                        unitSelect.__enhanced.setDisplayFromValue();
                    }
                }

                console.log('Main unit select final state:', {
                    value: unitSelect.value,
                    disabled: unitSelect.disabled,
                    backgroundColor: unitSelect.style.backgroundColor
                });
            }

            // Set default unit for listing filter
            const listingUnitFilter = document.getElementById('listing-unit-filter');
            if (listingUnitFilter) {
                console.log('Setting default unit for listing filter');
                listingUnitFilter.value = defaultUnitId;
                listingUnitFilter.disabled = true;
                listingUnitFilter.style.backgroundColor = '#f3f4f6';
                listingUnitFilter.style.cursor = 'not-allowed';
                listingUnitFilter.title = 'Unit filter is automatically set to your assigned unit';

                // Handle enhanced select control
                if (listingUnitFilter.__enhanced && listingUnitFilter.__enhanced.control) {
                    listingUnitFilter.__enhanced.control.disabled = true;
                    listingUnitFilter.__enhanced.control.style.backgroundColor = '#f3f4f6';
                    listingUnitFilter.__enhanced.control.style.cursor = 'not-allowed';
                    listingUnitFilter.__enhanced.control.title = 'Unit filter is automatically set to your assigned unit';
                    
                    // Update the display text
                    const selectedOption = listingUnitFilter.options[listingUnitFilter.selectedIndex];
                    if (selectedOption && listingUnitFilter.__enhanced.control.querySelector('span')) {
                        listingUnitFilter.__enhanced.control.querySelector('span').textContent = selectedOption.text;
                        listingUnitFilter.__enhanced.control.querySelector('span').className = ''; // Remove gray text class
                    }
                    
                    // Call setDisplayFromValue to ensure proper display
                    if (listingUnitFilter.__enhanced.setDisplayFromValue) {
                        listingUnitFilter.__enhanced.setDisplayFromValue();
                    }
                }

                console.log('Listing unit filter final state:', {
                    value: listingUnitFilter.value,
                    disabled: listingUnitFilter.disabled,
                    backgroundColor: listingUnitFilter.style.backgroundColor
                });
            }
        }

        // Function to enhance modal selects
        function enhanceModalSelects() {
            // Enhance static selects in the modal header
            const modalHeaderSelects = document.querySelectorAll('#editOpeningStockModal .trezo-card-content > form select[data-float-select]');
            modalHeaderSelects.forEach(sel => {
                try {
                    upgradeSelectToSearchable(sel.id, 'Search...');
                } catch (_) {}
            });
            
            // Enhance dynamically generated selects in the items list
            const dynamicSelects = document.querySelectorAll('#editItemsList select[data-float-select]');
            dynamicSelects.forEach(sel => {
                try {
                    upgradeSelectToSearchable(sel.id, 'Search...');
                } catch (_) {}
            });
        }

        // Global function to search items (accessible from dropdown functionality)
        let isSearching = false; // Flag to prevent multiple simultaneous searches
        let lastSearchTerm = ''; // Track the last search term to prevent duplicate searches

        async function searchItems(searchTerm) {
            // Prevent multiple simultaneous searches
            if (isSearching) {
                return;
            }

            // Prevent searching for the same term multiple times
            if (lastSearchTerm === searchTerm) {
                return;
            }

            isSearching = true;
            lastSearchTerm = searchTerm;

            try {
                // Get unit_id from the unit select field
                const unitSelect = document.getElementById('unit');
                const unitId = unitSelect ? unitSelect.value : (defaultUnitId || '');
                
                // Build URL with unit_id parameter
                let url = `../api/store-opening-stock/search-items?search=${encodeURIComponent(searchTerm)}`;
                if (unitId) {
                    url += `&unit_id=${encodeURIComponent(unitId)}`;
                }
                
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success && data.data.items) {
                    const itemSelect = document.getElementById('item_id');

                    // Clear the select element completely
                    itemSelect.innerHTML = '';

                    // Add the default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Select Item';
                    itemSelect.appendChild(defaultOption);

                    // Add search results, ensuring no duplicates
                    const addedIds = new Set();
                    data.data.items.forEach(item => {
                        // Check if this item ID has already been added
                        if (!addedIds.has(item.id)) {
                            addedIds.add(item.id);
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = `[${item.source_id}]-${item.name}`;
                            // Store rack_id as data attribute for automatic rack selection (only if rack_id exists)
                            if (item.rack_id) {
                                option.dataset.rackId = item.rack_id;
                            }
                            if (item.unit_type_name) {
                                option.dataset.unitTypeName = item.unit_type_name;
                            }
                            itemSelect.appendChild(option);
                        }
                    });

                    // Add search result count note
                    if (data.data.items.length > 0) {
                        const noteOption = document.createElement('option');
                        noteOption.value = '';
                        noteOption.textContent = `--- Found ${data.data.items.length} items matching "${searchTerm}" ---`;
                        noteOption.disabled = true;
                        noteOption.style.fontStyle = 'italic';
                        noteOption.style.color = '#6b7280';
                        itemSelect.appendChild(noteOption);
                    } else {
                        const noteOption = document.createElement('option');
                        noteOption.value = '';
                        noteOption.textContent = `--- No items found matching "${searchTerm}" ---`;
                        noteOption.disabled = true;
                        noteOption.style.color = '#dc2626';
                        itemSelect.appendChild(noteOption);
                    }

                    // Refresh the enhanced dropdown
                    if (itemSelect.__enhanced) {
                        itemSelect.__enhanced.refresh();
                    }
                }
            } catch (error) {
                console.error('Error searching items:', error);
            } finally {
                isSearching = false; // Reset the flag
            }
        }

        // Function to handle item selection and auto-select rack
        async function handleItemSelection() {
            const itemSelect = document.getElementById('item_id');
            const rackSelect = document.getElementById('rack_id');
            const qtyLabel = document.querySelector('label[for="qty"]');
            
            if (!itemSelect || !rackSelect) {
                console.error('Item or rack select not found');
                return;
            }

            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            const rackId = selectedOption ? selectedOption.dataset.rackId : null;
            const unitTypeName = selectedOption && selectedOption.value ? (selectedOption.dataset.unitTypeName || '') : '';

            // Update Quantity label to show unit type e.g. "Quantity (Pcs)"
            if (qtyLabel) {
                qtyLabel.textContent = unitTypeName ? `Quantity (${unitTypeName})` : 'Quantity';
            }

            console.log('Item selected:', selectedOption ? selectedOption.text : 'none');
            console.log('Rack ID from item_rack_assignments:', rackId);

            // Only auto-select rack if rack_id exists and is not null/empty
            // Check if rackId exists and is not empty (handles both string and number)
            if (rackId && rackId !== '' && rackId !== 'null' && rackId !== 'undefined') {
                // Item has a rack assignment for this unit - auto-select and disable rack dropdown
                console.log('Auto-selecting rack from item_rack_assignments:', rackId);
                
                // Set the rack value
                rackSelect.value = rackId;
                rackSelect.disabled = true;
                rackSelect.style.backgroundColor = '#f3f4f6';
                rackSelect.style.cursor = 'not-allowed';
                rackSelect.title = 'Rack is automatically set based on item-rack assignment for this unit';

                // Handle enhanced select control
                if (rackSelect.__enhanced && rackSelect.__enhanced.control) {
                    rackSelect.__enhanced.control.disabled = true;
                    rackSelect.__enhanced.control.style.backgroundColor = '#f3f4f6';
                    rackSelect.__enhanced.control.style.cursor = 'not-allowed';
                    rackSelect.__enhanced.control.title = 'Rack is automatically set based on item-rack assignment for this unit';
                    
                    // Update the display text
                    const selectedRackOption = rackSelect.options[rackSelect.selectedIndex];
                    if (selectedRackOption && rackSelect.__enhanced.control.querySelector('span')) {
                        rackSelect.__enhanced.control.querySelector('span').textContent = selectedRackOption.text;
                        rackSelect.__enhanced.control.querySelector('span').className = ''; // Remove gray text class
                    }
                    
                    // Call setDisplayFromValue to ensure proper display
                    if (rackSelect.__enhanced.setDisplayFromValue) {
                        rackSelect.__enhanced.setDisplayFromValue();
                    }
                }

                console.log('Rack auto-selected and disabled');
                
                // Show a brief toast message to inform user
                showToast('Rack automatically selected based on item-rack assignment', 'info');
                
                // Auto-focus on quantity field since rack is disabled
                setTimeout(() => {
                    const qtyField = document.getElementById('qty');
                    if (qtyField) {
                        qtyField.focus();
                        qtyField.select();
                    }
                }, 100);
            } else {
                // Item has no rack assignment for this unit - enable rack dropdown for manual selection
                console.log('No rack assignment found in item_rack_assignments for this unit - enabling manual selection');
                
                rackSelect.disabled = false;
                rackSelect.style.backgroundColor = '';
                rackSelect.style.cursor = '';
                rackSelect.title = 'Select a rack for this item (no assignment found for this unit)';

                // Handle enhanced select control
                if (rackSelect.__enhanced && rackSelect.__enhanced.control) {
                    rackSelect.__enhanced.control.disabled = false;
                    rackSelect.__enhanced.control.style.backgroundColor = '';
                    rackSelect.__enhanced.control.style.cursor = '';
                    rackSelect.__enhanced.control.title = 'Select a rack for this item (no assignment found for this unit)';
                }

                // Clear rack selection
                rackSelect.value = '';
                if (rackSelect.__enhanced && rackSelect.__enhanced.setDisplayFromValue) {
                    rackSelect.__enhanced.setDisplayFromValue();
                }

                console.log('Rack dropdown enabled for manual selection');
                
                // Auto-focus on rack field since it's enabled for manual selection
                setTimeout(() => {
                    const rackField = document.getElementById('rack_id');
                    if (rackField && rackField.__enhanced && rackField.__enhanced.control) {
                        rackField.__enhanced.control.focus();
                    } else if (rackField) {
                        rackField.focus();
                    }
                }, 100);
            }
        }

        // Function to refresh item select box and load default list
        async function refreshItemSelectBox() {
            try {
                console.log('Refreshing item select box...');
                
                const itemSelect = document.getElementById('item_id');
                if (!itemSelect) {
                    console.error('Item select not found');
                    return;
                }

                // Clear the select element completely
                itemSelect.innerHTML = '';

                // Add the default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select Item';
                itemSelect.appendChild(defaultOption);

                // Get unit_id from the unit select field
                const unitSelect = document.getElementById('unit');
                const unitId = unitSelect ? unitSelect.value : (defaultUnitId || '');
                
                // Build URL with unit_id parameter
                let url = '../api/store-opening-stock/items';
                if (unitId) {
                    url += `?unit_id=${encodeURIComponent(unitId)}`;
                }
                
                // Load initial items from API
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                if (data.success && data.data.items) {
                    // Add initial items, ensuring no duplicates
                    const addedIds = new Set();
                    data.data.items.forEach(item => {
                        // Check if this item ID has already been added
                        if (!addedIds.has(item.id)) {
                            addedIds.add(item.id);
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = `[${item.source_id}]-${item.name}`;
                            // Store rack_id as data attribute for automatic rack selection (only if rack_id exists)
                            if (item.rack_id) {
                                option.dataset.rackId = item.rack_id;
                            }
                            if (item.unit_type_name) {
                                option.dataset.unitTypeName = item.unit_type_name;
                            }
                            itemSelect.appendChild(option);
                        }
                    });

                    // Add a note about the limited items
                    const noteOption = document.createElement('option');
                    noteOption.value = '';
                    noteOption.textContent = `--- Showing first 50 items. Type to search for more ---`;
                    noteOption.disabled = true;
                    noteOption.style.fontStyle = 'italic';
                    noteOption.style.color = '#6b7280';
                    itemSelect.appendChild(noteOption);

                    // Reset the last search term
                    itemSelect.lastSearchTerm = '';

                    // Add event listener for item selection
                    itemSelect.addEventListener('change', handleItemSelection);

                    // Clear search input in enhanced dropdown
                    if (itemSelect.__enhanced && itemSelect.__enhanced.search) {
                        itemSelect.__enhanced.search.value = '';
                        itemSelect.__enhanced.search.lastSearchedValue = '';
                    }

                    // Refresh the enhanced dropdown
                    if (itemSelect.__enhanced) {
                        itemSelect.__enhanced.refresh();
                    }

                    console.log('Item select box refreshed successfully');
                }
            } catch (error) {
                console.error('Error refreshing item select box:', error);
            }
        }

        // Global function to reset items to initial 50
        // Global variables for opening stock management
        let openingStockItems = [];
        let nextItemId = 1;
        let isEditMode = false;
        let currentVoucherId = null;
        let currentEditVoucherData = null;
        let isSubmittingOpeningStock = false;

        // Global function to show toast messages
        function showToast(message, type = 'success') {
            let bgColor, textColor, icon;
            switch (type) {
                case 'success':
                    bgColor = '#f0fdf4';
                    textColor = '#166534';
                    icon = '✓';
                    break;
                case 'error':
                    bgColor = '#fef2f2';
                    textColor = '#991b1b';
                    icon = '✗';
                    break;
                case 'warning':
                    bgColor = '#fffbeb';
                    textColor = '#92400e';
                    icon = '⚠';
                    break;
                case 'info':
                    bgColor = '#eff6ff';
                    textColor = '#1e40af';
                    icon = 'ℹ';
                    break;
                default:
                    bgColor = '#f0fdf4';
                    textColor = '#166534';
                    icon = '✓';
            }
            if (typeof Toastify !== 'undefined') {
                Toastify({
                    text: icon + '  ' + message,
                    duration: 4000,
                    close: true,
                    gravity: 'top',
                    position: 'right',
                    stopOnFocus: true,
                    style: {
                        background: bgColor,
                        color: textColor,
                        borderRadius: '12px',
                        border: '1px solid rgba(0,0,0,0.06)',
                        boxShadow: '0 10px 15px -3px rgba(0,0,0,.07), 0 4px 6px -2px rgba(0,0,0,.05)',
                        fontWeight: '500'
                    }
                }).showToast();
            }
        }

        // Global function to update the table
        function updateTable() {
            const tableBody = document.querySelector('#storeOpeningStockTable tbody');
            if (!tableBody) {
                console.error('Table body not found!');
                return;
            }

            // Clear existing rows
            tableBody.innerHTML = '';

            // Add rows for each item
            openingStockItems.forEach((item, index) => {
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid #e5e7eb';
                
                row.innerHTML = `
                    <td style="padding: 4px 6px; border-right:1px solid #e5e7eb;">${item.item_name}</td>
                    <td style="padding: 4px 6px; border-right:1px solid #e5e7eb;">${item.rack_name}</td>
                    <td style="padding: 4px 6px; border-right:1px solid #e5e7eb; text-align:center;">${Math.round(item.qty)}</td>
                    <td style="padding: 4px 6px; border-right:1px solid #e5e7eb;">${item.narration || '-'}</td>
                    <td style="padding: 4px 6px; border-right:1px solid #e5e7eb; text-align:center;">
                        <button class="text-blue-500 dark:text-blue-400 leading-none" type="button" onclick="editItem(${index})" title="Edit">
                            <i class="material-symbols-outlined text-sm">edit</i>
                        </button>
                    </td>
                    <td style="padding: 4px 6px; text-align:center;">
                        <button class="text-red-500 dark:text-red-400 leading-none" type="button" onclick="deleteItem(${index})" title="Delete">
                            <i class="material-symbols-outlined text-sm">delete</i>
                        </button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        // Store Opening Stock functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize custom searchable dropdowns
            enhanceFloatSelects();
            
            // Initialize modal dropdowns
            enhanceModalSelects();

            // Function to load items
        async function loadItems() {
            try {
                // Get unit_id from the unit select field
                const unitSelect = document.getElementById('unit');
                const unitId = unitSelect ? unitSelect.value : (defaultUnitId || '');
                
                // Build URL with unit_id parameter
                let url = '../api/store-opening-stock/items';
                if (unitId) {
                    url += `?unit_id=${encodeURIComponent(unitId)}`;
                }
                
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success && data.data.items) {
                    const itemSelect = document.getElementById('item_id');

                    // Clear the select element completely
                    itemSelect.innerHTML = '';

                    // Add the default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Select Item';
                    itemSelect.appendChild(defaultOption);

                    // Add initial items, ensuring no duplicates
                    const addedIds = new Set();
                    data.data.items.forEach(item => {
                        // Check if this item ID has already been added
                        if (!addedIds.has(item.id)) {
                            addedIds.add(item.id);
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = `[${item.source_id}]-${item.name}`;
                            // Store rack_id as data attribute for automatic rack selection (only if rack_id exists)
                            if (item.rack_id) {
                                option.dataset.rackId = item.rack_id;
                            }
                            if (item.unit_type_name) {
                                option.dataset.unitTypeName = item.unit_type_name;
                            }
                            itemSelect.appendChild(option);
                        }
                    });

                    // Add a note about the limited items
                    const noteOption = document.createElement('option');
                    noteOption.value = '';
                    noteOption.textContent = `--- Showing first 50 items. Type to search for more ---`;
                    noteOption.disabled = true;
                    noteOption.style.fontStyle = 'italic';
                    noteOption.style.color = '#6b7280';
                    itemSelect.appendChild(noteOption);

                    // Reset the last search term
                    itemSelect.lastSearchTerm = '';

                    // Add event listener for item selection
                    itemSelect.addEventListener('change', handleItemSelection);

                    // Refresh the enhanced dropdown
                    if (itemSelect.__enhanced) {
                        itemSelect.__enhanced.refresh();
                    }
                }
            } catch (error) {
                console.error('Error loading items:', error);
            }
        }

            // Load initial data
            loadUnits();
            loadItems();
            loadRacks();

            // Function to load units
            async function loadUnits() {
                try {
                    const response = await fetch('../api/store-opening-stock/units');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.success && data.data.units) {
                        const unitSelect = document.getElementById('unit');
                        unitSelect.innerHTML = '<option value="">Select Unit</option>';
                        data.data.units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = `[${unit.id}]-${unit.name}`;
                            unitSelect.appendChild(option);
                        });

                        // Refresh the enhanced dropdown
                        if (unitSelect.__enhanced) {
                            unitSelect.__enhanced.refresh();
                        }

                        // Set default unit after units are loaded
                        if (defaultUnitId) {
                            setTimeout(() => {
                                setDefaultUnitForAllSelects();
                                // Reload racks after default unit is set
                                loadRacks();
                            }, 100);
                        }
                        
                        // Add event listener to reload items and racks when unit changes
                        unitSelect.addEventListener('change', function() {
                            console.log('Unit changed, reloading items and racks with new unit_id:', unitSelect.value);
                            // Clear current item selection
                            const itemSelect = document.getElementById('item_id');
                            if (itemSelect) {
                                itemSelect.value = '';
                                if (itemSelect.__enhanced && itemSelect.__enhanced.setDisplayFromValue) {
                                    itemSelect.__enhanced.setDisplayFromValue();
                                }
                            }
                            // Clear rack selection
                            const rackSelect = document.getElementById('rack_id');
                            if (rackSelect) {
                                rackSelect.value = '';
                                rackSelect.disabled = false;
                                rackSelect.style.backgroundColor = '';
                                rackSelect.style.cursor = '';
                                if (rackSelect.__enhanced) {
                                    if (rackSelect.__enhanced.control) {
                                        rackSelect.__enhanced.control.disabled = false;
                                        rackSelect.__enhanced.control.style.backgroundColor = '';
                                        rackSelect.__enhanced.control.style.cursor = '';
                                    }
                                    if (rackSelect.__enhanced.setDisplayFromValue) {
                                        rackSelect.__enhanced.setDisplayFromValue();
                                    }
                                }
                            }
                            // Reload items and racks with new unit_id
                            loadItems();
                            loadRacks();
                        });
                    }
                } catch (error) {
                    console.error('Error loading units:', error);
                }
            }

            // Function to load racks filtered by unit_id
            async function loadRacks() {
                try {
                    // Get unit_id from the unit select field
                    const unitSelect = document.getElementById('unit');
                    const unitId = unitSelect ? unitSelect.value : (defaultUnitId || '');
                    
                    // Build URL with unit_id parameter
                    let url = '../api/store-opening-stock/racks';
                    if (unitId) {
                        url += `?unit_id=${encodeURIComponent(unitId)}`;
                    }
                    
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.success && data.data.racks) {
                        const rackSelect = document.getElementById('rack_id');
                        rackSelect.innerHTML = '<option value="">Select Rack</option>';
                        data.data.racks.forEach(rack => {
                            const option = document.createElement('option');
                            option.value = rack.id;
                            option.textContent = `[${rack.id}]-${rack.name}`;
                            rackSelect.appendChild(option);
                        });

                        // Refresh the enhanced dropdown
                        if (rackSelect.__enhanced) {
                            rackSelect.__enhanced.refresh();
                        }
                    }
                } catch (error) {
                    console.error('Error loading racks:', error);
                }
            }

            // Function to add item to table
            function addItemToTable() {
                const itemSelect = document.getElementById('item_id');
                const rackSelect = document.getElementById('rack_id');
                const qtyInput = document.getElementById('qty');
                const narrationInput = document.getElementById('narration');

                // Validate required fields
                if (!itemSelect.value || !rackSelect.value || !qtyInput.value) {
                    showToast('Please fill in all required fields (Item, Rack, Quantity)', 'error');
                    return;
                }

                // Get display text for selected options
                const itemText = itemSelect.options[itemSelect.selectedIndex]?.text || '';
                const rackText = rackSelect.options[rackSelect.selectedIndex]?.text || '';

                // Check for duplicate item in same voucher (same item + same rack)
                const existingItem = openingStockItems.find(item => 
                    item.item_id === itemSelect.value && item.rack_id === rackSelect.value
                );

                if (existingItem) {
                    showToast(`Item "${itemText}" with rack "${rackText}" already exists in this voucher. Please select a different rack or item.`, 'error');
                    return;
                }

                // Check database for existing opening stock
                checkExistingOpeningStock(itemSelect.value, rackSelect.value, itemText, rackText, qtyInput.value, narrationInput.value || '');
            }

            // Function to check existing opening stock in database
            async function checkExistingOpeningStock(itemId, rackId, itemName, rackName, qty, narration) {
                try {
                    const response = await fetch('../api/store-opening-stock/check-existing', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            item_id: itemId,
                            rack_id: rackId
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        if (data.exists) {
                            showToast(`Item "${itemName}" with rack "${rackName}" already has opening stock in voucher #${data.voucher_no}. Please select a different rack or item.`, 'error');
                            return;
                        } else {
                            // No existing stock found, proceed to add item
                            addItemToTableDirect(itemId, itemName, rackId, rackName, qty, narration);
                        }
                    } else {
                        throw new Error(data.error || 'Failed to check existing stock');
                    }
                    
                } catch (error) {
                    console.error('Error checking existing opening stock:', error);
                    showToast('Error checking existing stock: ' + error.message, 'error');
                }
            }

            // Function to actually add item to table (called after validation passes)
            function addItemToTableDirect(itemId, itemName, rackId, rackName, qty, narration) {
                // Create item object
                const item = {
                    id: nextItemId++,
                    item_id: itemId,
                    item_name: itemName,
                    rack_id: rackId,
                    rack_name: rackName,
                    qty: qty,
                    narration: narration
                };

                // Add to array
                openingStockItems.push(item);

                // Update table
                updateTable();

                // Clear form fields
                clearItemForm();

                // Refresh item select box and load default list
                refreshItemSelectBox();

                // Show success message
                showToast('Item added successfully!', 'success');
                
                // Refresh the listing table to show updated data
                if (typeof loadOpeningStockListing === 'function') {
                    loadOpeningStockListing();
                }

                // Focus on first item field (item_id) after adding item
                setTimeout(() => {
                    const itemSelect = document.getElementById('item_id');
                    if (itemSelect && itemSelect.__enhanced && itemSelect.__enhanced.control) {
                        itemSelect.__enhanced.control.focus();
                    } else if (itemSelect) {
                        itemSelect.focus();
                    }
                }, 100);
            }

            // Function to clear item form fields
            function clearItemForm() {
                document.getElementById('item_id').value = '';
                document.getElementById('rack_id').value = '';
                document.getElementById('qty').value = '';
                document.getElementById('narration').value = '';

                // Reset rack dropdown state
                const rackSelect = document.getElementById('rack_id');
                if (rackSelect) {
                    rackSelect.disabled = false;
                    rackSelect.style.backgroundColor = '';
                    rackSelect.style.cursor = '';
                    rackSelect.title = 'Select a rack for this item';

                    // Handle enhanced select control
                    if (rackSelect.__enhanced && rackSelect.__enhanced.control) {
                        rackSelect.__enhanced.control.disabled = false;
                        rackSelect.__enhanced.control.style.backgroundColor = '';
                        rackSelect.__enhanced.control.style.cursor = '';
                        rackSelect.__enhanced.control.title = 'Select a rack for this item';
                    }
                }

                // Refresh the enhanced dropdowns to show cleared state
                const itemSelect = document.getElementById('item_id');
                const unitSelect = document.getElementById('unit');
                
                if (itemSelect && itemSelect.__enhanced && itemSelect.__enhanced.setDisplayFromValue) {
                    itemSelect.__enhanced.setDisplayFromValue();
                }
                if (rackSelect && rackSelect.__enhanced && rackSelect.__enhanced.setDisplayFromValue) {
                    rackSelect.__enhanced.setDisplayFromValue();
                }
                if (unitSelect && unitSelect.__enhanced && unitSelect.__enhanced.setDisplayFromValue) {
                    unitSelect.__enhanced.setDisplayFromValue();
                }
            }





            // Function to edit item
            window.editItem = function(index) {
                const item = openingStockItems[index];
                if (item) {
                    // Remove item from table immediately so it moves to edit fields
                    openingStockItems.splice(index, 1);
                    updateTable();
                    
                    showToast('Edit mode enabled. Item moved to edit fields.', 'info');
                    
                    // Handle Item Dropdown - Add option if it doesn't exist
                    const itemSelect = document.getElementById('item_id');
                    let itemOption = itemSelect.querySelector(`option[value="${item.item_id}"]`);
                    
                    if (!itemOption) {
                        // Create option if it doesn't exist (e.g. if not in initial 50 items)
                        itemOption = document.createElement('option');
                        itemOption.value = item.item_id;
                        itemOption.textContent = item.item_name; // Use existing item name
                        // Add rack info if available
                        if (item.rack_id) {
                            itemOption.dataset.rackId = item.rack_id;
                        }
                        itemSelect.appendChild(itemOption);
                    }
                    itemSelect.value = item.item_id;

                    // Handle Rack Dropdown - Add option if it doesn't exist
                    const rackSelect = document.getElementById('rack_id');
                    let rackOption = rackSelect.querySelector(`option[value="${item.rack_id}"]`);
                    
                    if (!rackOption) {
                         rackOption = document.createElement('option');
                         rackOption.value = item.rack_id;
                         rackOption.textContent = item.rack_name;
                         rackSelect.appendChild(rackOption);
                    }
                    rackSelect.value = item.rack_id;

                    document.getElementById('qty').value = item.qty;
                    document.getElementById('narration').value = item.narration;

                    // Update the enhanced dropdown displays
                    if (itemSelect.__enhanced) {
                        if (itemSelect.__enhanced.setDisplayFromValue) {
                             itemSelect.__enhanced.setDisplayFromValue();
                        }
                    }
                    if (rackSelect.__enhanced) {
                         if (rackSelect.__enhanced.setDisplayFromValue) {
                            rackSelect.__enhanced.setDisplayFromValue();
                         }
                    }
                    
                    // Focus on first field
                    if (itemSelect.__enhanced && itemSelect.__enhanced.control) {
                        itemSelect.__enhanced.control.focus();
                    } else {
                        itemSelect.focus();
                    }
                }
            };



        // Function to delete item
        window.deleteItem = function(index) {
                if (typeof Swal === 'undefined') {
                    console.error('SweetAlert2 is not loaded!');
                    showToast('Error: SweetAlert2 not available', 'error');
                    return;
                }
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Are you sure you want to delete this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        openingStockItems.splice(index, 1);
                        updateTable();
                        showToast('Item has been deleted successfully.', 'success');
                    }
                }).catch((error) => {
                    console.error('SweetAlert error:', error);
                    showToast('Error showing confirmation dialog', 'error');
                });
            };

            // Function to submit opening stock
            async function submitOpeningStock() {
                const postBtn = document.getElementById('post_btn');
                if (!postBtn) return;
                // Prevent double-clicks: if already submitting, ignore
                if (isSubmittingOpeningStock) return;

                if (openingStockItems.length === 0) {
                    showToast('Please add at least one item before submitting.', 'error');
                    return;
                }

                const voucherDate = document.getElementById('voucher_date').value;
                const unitId = defaultUnitId || document.getElementById('unit').value;

                if (!voucherDate || !unitId) {
                    showToast('Please fill in all required header fields (Date, Unit)', 'error');
                    return;
                }

                // Disable button immediately (before any async work) so it is not clickable
                isSubmittingOpeningStock = true;
                const originalText = postBtn.innerHTML;
                const originalPointerEvents = postBtn.style.pointerEvents;
                const originalCursor = postBtn.style.cursor;
                const originalTabIndex = postBtn.getAttribute('tabindex');
                postBtn.innerHTML = '<i class="material-symbols-outlined mr-[8px]">hourglass_empty</i><span>Processing...</span>';
                postBtn.setAttribute('disabled', 'disabled');
                postBtn.disabled = true;
                postBtn.setAttribute('aria-disabled', 'true');
                postBtn.style.pointerEvents = 'none';
                postBtn.style.cursor = 'not-allowed';
                postBtn.style.opacity = '0.7';
                postBtn.setAttribute('tabindex', '-1');

                try {
                    // Prepare data for submission (voucher_no will be auto-generated by backend)
                    const openingStockData = {
                        voucher_date: voucherDate,
                        unit_id: unitId,
                        items: openingStockItems.map(item => ({
                            item_id: item.item_id,
                            rack_id: item.rack_id,
                            qty: item.qty,
                            narration: item.narration
                        }))
                    };

                    let response;
                    let result;

                    // Check if we're in edit mode
                    if (isEditMode && currentVoucherId) {
                        console.log('Edit mode detected, updating voucher:', currentVoucherId);
                        
                        // Add voucher_no to payload for update
                        openingStockData.voucher_no = currentVoucherId;

                        // Update existing voucher
                        response = await fetch('../api/store-opening-stock/update-voucher', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(openingStockData)
                        });

                        result = await response.json();

                        if (result.success) {
                            showToast('Store opening stock updated successfully!', 'success');
                            
                            // Exit edit mode after successful update
                            exitEditModeAfterUpdate();
                            
                            // Refresh the listing table to show the updated record
                            if (typeof loadOpeningStockListing === 'function') {
                                loadOpeningStockListing();
                            }
                        } else {
                            throw new Error(result.error || 'Failed to update opening stock');
                        }
                    } else {
                        console.log('Create mode detected, creating new voucher');
                        
                        // Create new voucher
                        response = await fetch('../api/store-opening-stock/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(openingStockData)
                    });

                        result = await response.json();

                    if (result.success) {
                            const voucherNo = result.data?.voucher_no || 'N/A';
                            showToast(`Store opening stock saved successfully! Voucher #${voucherNo}`, 'success');
                        
                        // Reset form and table
                        openingStockItems = [];
                        updateTable();
                        
                        // Clear form fields
                        clearItemForm();
                        
                        // Refresh item select box and load default list
                        refreshItemSelectBox();
                        
                        // Also clear and refresh the unit select
                        document.getElementById('unit').value = '';
                        const unitSelect = document.getElementById('unit');
                        if (unitSelect.__enhanced) {
                            unitSelect.__enhanced.setDisplayFromValue();
                        }
                        
                            // Refresh the listing table to show the new record
                            if (typeof loadOpeningStockListing === 'function') {
                                loadOpeningStockListing();
                            }
                            
                            // Focus on first field (item) after successful submission
                            const itemField = document.getElementById('item_id');
                            if (itemField) {
                                if (itemField.__enhanced && itemField.__enhanced.control) {
                                    itemField.__enhanced.control.focus();
                                } else {
                                    itemField.focus();
                                }
                            }
                    } else {
                        throw new Error(result.error || 'Failed to save opening stock');
                        }
                    }
                } catch (error) {
                    console.error('Error submitting opening stock:', error);
                    showToast('Error: ' + error.message, 'error');
                } finally {
                    // Restore button (re-enable and restore appearance)
                    isSubmittingOpeningStock = false;
                    postBtn.innerHTML = originalText;
                    postBtn.removeAttribute('disabled');
                    postBtn.disabled = false;
                    postBtn.removeAttribute('aria-disabled');
                    postBtn.style.pointerEvents = originalPointerEvents || '';
                    postBtn.style.cursor = originalCursor || '';
                    postBtn.style.opacity = '';
                    if (originalTabIndex !== null) postBtn.setAttribute('tabindex', originalTabIndex); else postBtn.removeAttribute('tabindex');
                }
            }

            // Add event listeners
            document.getElementById('add_item_btn').addEventListener('click', addItemToTable);
            document.getElementById('post_btn').addEventListener('click', function(e) {
                if (isSubmittingOpeningStock) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                const btn = document.getElementById('post_btn');
                if (btn && btn.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                submitOpeningStock();
            });

            // Initialize table
            updateTable();
            
            // Refresh the listing table after successful operations
            if (typeof loadOpeningStockListing === 'function') {
                loadOpeningStockListing();
            }

            // ======================== ENTER KEY NAVIGATION ========================
            
            // Function to handle Enter key navigation
            function handleEnterNavigation(currentFieldId) {
                const fieldSequence = [
                    'item_id',
                    'rack_id',
                    'qty',
                    'narration'
                ];

                const currentIndex = fieldSequence.indexOf(currentFieldId);
                if (currentIndex === -1) {
                    return;
                }

                // Get next field
                let nextIndex = currentIndex + 1;
                
                // Check if rack is disabled and skip it
                if (nextIndex < fieldSequence.length && fieldSequence[nextIndex] === 'rack_id') {
                    const rackField = document.getElementById('rack_id');
                    if (rackField && rackField.disabled) {
                        console.log('Rack is disabled, skipping to quantity field');
                        nextIndex = fieldSequence.indexOf('qty');
                    }
                }

                if (nextIndex < fieldSequence.length) {
                    const nextFieldId = fieldSequence[nextIndex];
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
                } else {
                    // After narration field, focus on Add button
                    const addButton = document.getElementById('add_item_btn');
                    if (addButton) {
                        addButton.focus();
                    }
                }
            }

            // Global keydown event listener for Enter key navigation
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;

                // Don't interfere with form submissions, textareas, or when dropdown is open
                if (e.target.tagName === 'TEXTAREA' ||
                    e.target.type === 'submit' ||
                    e.target.closest('.absolute') && !e.target.closest('.absolute').classList.contains('hidden')) {
                    return;
                }

                // Always prevent default and stop propagation for Enter key
                e.preventDefault();
                e.stopPropagation();

                const currentElement = e.target;
                let currentFieldId = currentElement.id;

                // For enhanced selects, get the original select ID
                if (currentElement.tagName === 'BUTTON' && currentElement.closest('.relative')) {
                    const selectEl = currentElement.parentNode.querySelector('select');
                    if (selectEl) {
                        currentFieldId = selectEl.id;
                    } else {
                        // Try to find select in the same float-group
                        const floatGroup = currentElement.closest('.float-group');
                        if (floatGroup) {
                            const selectInGroup = floatGroup.querySelector('select');
                            if (selectInGroup) {
                                currentFieldId = selectInGroup.id;
                            }
                        }
                    }
                }

                // Handle special cases for buttons
                if (currentElement.tagName === 'BUTTON') {
                    if (currentElement.id === 'add_item_btn') {
                        // Special handling: add item and then focus will be handled by addItemToTable function
                        addItemToTable();
                        return; // Don't call handleEnterNavigation for Add button
                    }
                }

                // Only proceed if we have a valid field ID
                if (currentFieldId && currentFieldId.trim() !== '') {
                    handleEnterNavigation(currentFieldId);
                }
            });

            // Auto-focus on item field when page loads
            // Wait for enhanced selects to be initialized
            setTimeout(() => {
                const itemField = document.getElementById('item_id');
                if (itemField) {
                    if (itemField.__enhanced && itemField.__enhanced.control) {
                        itemField.__enhanced.control.focus();
                    } else {
                        itemField.focus();
                    }
                }
            }, 200); // Reduced delay to match store_demand.php approach

            // Fallback: Set default unit after a delay in case API calls fail
            if (defaultUnitId) {
                setTimeout(() => {
                    setDefaultUnitForAllSelects();
                }, 1000);
            }

        });

        // Store Opening Stock Listing functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize listing filters
            initializeListingFilters();
            
            // Load initial listing data
            loadOpeningStockListing();
            
            // Add event listeners for listing
            const refreshBtn = document.getElementById('refresh-listing');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => {
                currentPage = 1; // Reset to first page when refreshing
                loadOpeningStockListing();
            });
            }
            document.getElementById('listing-unit-filter').addEventListener('change', () => {
                currentPage = 1; // Reset to first page when unit filter changes
                loadOpeningStockListing();
            });
            document.getElementById('listing-date-filter').addEventListener('change', () => {
                currentPage = 1; // Reset to first page when date filter changes
                loadOpeningStockListing();
            });
            document.getElementById('listing-limit-filter').addEventListener('change', () => {
                currentPage = 1; // Reset to first page when limit changes
                loadOpeningStockListing();
            });
            
            // Add search functionality with debouncing
            const searchFilter = document.getElementById('listing-search-filter');
            let searchTimeout;
            searchFilter.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentPage = 1; // Reset to first page when searching
                    loadOpeningStockListing();
                }, 500);
            });

            // Add modal event handlers
            const editModal = document.getElementById('editOpeningStockModal');
            const closeEditModal = document.getElementById('closeEditOpeningStockModal');
            const cancelEditBtn = document.getElementById('cancelEditOpeningStockBtn');
            const updateBtn = document.getElementById('updateOpeningStockBtn');



            // Close modal on close button click
            closeEditModal.addEventListener('click', function() {
                editModal.classList.remove('active');
                editModal.classList.add('opacity-0', 'pointer-events-none');
            });

            // Close modal on cancel button click
            cancelEditBtn.addEventListener('click', function() {
                editModal.classList.remove('active');
                editModal.classList.add('opacity-0', 'pointer-events-none');
            });

            // Close modal on outside click
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    editModal.classList.remove('active');
                    editModal.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            // Handle update button click
            updateBtn.addEventListener('click', function() {
                updateOpeningStock();
            });

            // Pagination event listeners - now handled by the new pagination structure
        });

        // Listing variables
        let currentPage = 1;
        let totalPages = 1;
        let totalRecords = 0;
        let recordsPerPage = 10;

        // Function to initialize listing filters
        function initializeListingFilters() {
            // Load units for filter
            loadUnitsForFilter();
        }

        // Function to load units for filter
        async function loadUnitsForFilter() {
            try {
                const response = await fetch('../api/store-opening-stock/units');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success && data.data.units) {
                    const unitFilter = document.getElementById('listing-unit-filter');
                    unitFilter.innerHTML = '<option value="">All Units</option>';
                    data.data.units.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit.id;
                        option.textContent = `[${unit.id}]-${unit.name}`;
                        unitFilter.appendChild(option);
                    });
                    
                    // Refresh the enhanced dropdown
                    if (unitFilter.__enhanced) {
                        unitFilter.__enhanced.refresh();
                    }

                    // Set default unit for filter after units are loaded
                    if (defaultUnitId) {
                        setTimeout(() => {
                            setDefaultUnitForAllSelects();
                        }, 100);
                    }
                }
            } catch (error) {
                console.error('Error loading units for filter:', error);
            }
        }

        // Function to load opening stock listing
        async function loadOpeningStockListing() {
            try {
                // Get filter values
                const unitFilter = document.getElementById('listing-unit-filter').value;
                const dateFilter = document.getElementById('listing-date-filter').value;
                const searchFilter = document.getElementById('listing-search-filter').value;
                const limitFilter = document.getElementById('listing-limit-filter').value;
                
                // Update records per page
                recordsPerPage = parseInt(limitFilter) || 10;
                
                // Build query parameters
                const params = new URLSearchParams({
                    page: currentPage,
                    limit: recordsPerPage,
                    transaction_type: 'opening',
                    status: 'opening'
                });
                
                // Always use default unit_id if user has one, otherwise use the selected unit
                if (defaultUnitId) {
                    params.append('unit_id', defaultUnitId);
                    console.log('Using default unit_id for opening stock listing:', defaultUnitId);
                } else if (unitFilter) {
                    params.append('unit_id', unitFilter);
                }
                
                if (dateFilter) params.append('date', dateFilter);
                if (searchFilter) params.append('search', searchFilter);

                // Show loading
                const tableBody = document.getElementById('storeOpeningStockListingTableBody');
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-500">Loading...</td></tr>';

                // Fetch data
                const response = await fetch(`../api/store-opening-stock/listing?${params.toString()}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Debug: Log the response data
                console.log('API Response:', data);
                
                if (data.success) {
                    displayOpeningStockListing(data.data);
                    updatePagination(data.data.total, data.data.page, data.data.total_pages);
                } else {
                    throw new Error(data.error || 'Failed to load opening stock listing');
                }
                
            } catch (error) {
                console.error('Error loading opening stock listing:', error);
                const tableBody = document.getElementById('storeOpeningStockListingTableBody');
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-red-500">Error loading data: ' + error.message + '</td></tr>';
            }
        }

        // Function to display opening stock listing
        function displayOpeningStockListing(data) {
            const tableBody = document.getElementById('storeOpeningStockListingTableBody');
            tableBody.innerHTML = '';

            if (!data.records || data.records.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-gray-500">No opening stock records found</td></tr>';
                return;
            }

            data.records.forEach(record => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c]';
                
                row.innerHTML = `
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white">
                        <span class="font-medium">${record.voucher_no}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white">
                        ${formatDate(record.voucher_date)}
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white">
                        <span class="font-medium">[${record.unit_id}]-${record.unit_name}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white text-center">
                        <span class="font-medium">${record.item_count}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white text-center">
                        <span class="font-medium">${Math.round(record.total_qty)}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white">
                        <span class="font-medium">${record.items_list || '-'}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-black dark:text-white">
                        <span class="font-medium">${record.racks_list || '-'}</span>
                    </td>
                    <td class="px-[20px] py-[12px] text-sm text-center">
                        <div class="flex items-center justify-center gap-[8px]">
                            <button type="button" onclick="viewOpeningStock(${record.voucher_no})" class="text-blue-500 hover:text-blue-600" title="View">
                                <i class="material-symbols-outlined text-sm">visibility</i>
                            </button>
                            <?php if ($defaultRoleStatus != 'PM'): ?>
                            <button type="button" onclick="editOpeningStock(${record.voucher_no})" class="text-blue-500 hover:text-blue-600" title="Edit">
                                <i class="material-symbols-outlined text-sm">edit</i>
                            </button>
                            <?php endif; ?>
                            <!--<button type="button" onclick="deleteOpeningStock(${record.voucher_no})" class="text-red-500" title="Delete">
                                <i class="material-symbols-outlined text-sm">delete</i>
                            </button>-->
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        // Function to load opening stock listing for a specific page
        function loadOpeningStockListingPage(page) {
            currentPage = page;
            loadOpeningStockListing();
        }

        // Function to update pagination
        function updatePagination(total, current, last) {
            // Debug: Log pagination values
            console.log('Pagination values:', { total, current, last, recordsPerPage });
            
            totalRecords = total;
            currentPage = current;
            totalPages = last;
            
            // Update page info
            const start = ((current - 1) * recordsPerPage) + 1;
            const end = Math.min(current * recordsPerPage, total);
            document.getElementById('listing-page-info').textContent = `Showing ${start} to ${end} of ${total} records`;
            
            // Update pagination controls (match store_demand.php layout)
            const pagEl = document.querySelector('#openingStockPagination');
            if (pagEl) {
                const maxButtons = 5; // number buttons to show
                let start = Math.max(1, current - Math.floor((maxButtons - 1) / 2));
                let end = Math.min(totalPages, start + maxButtons - 1);
                start = Math.max(1, Math.min(start, end - maxButtons + 1));

                const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
                const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

                let html = '';
                // Prev
                html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${current - 1}\" class=\"${btnCls} ${current <= 1 ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0</span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_left<\/i>\n  <\/a>\n<\/li>`;

                for (let p = start; p <= end; p++) {
                    const cls = p === current ? activeCls : btnCls;
                    html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${p}\" class=\"${cls}\">${p}<\/a>\n<\/li>`;
                }

                // Next
                html += `\n<li class=\"inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0\">\n  <a href=\"javascript:void(0);\" data-page=\"${current + 1}\" class=\"${btnCls} ${current >= totalPages ? 'opacity-50 pointer-events-none' : ''}\">\n    <span class=\"opacity-0\">0</span>\n    <i class=\"material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2\">chevron_right<\/i>\n  <\/a>\n<\/li>`;

                pagEl.innerHTML = html;

                pagEl.querySelectorAll('a[data-page]').forEach(a => {
                    const targetPage = parseInt(a.getAttribute('data-page'), 10);
                    a.onclick = () => {
                        if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== current) {
                            loadOpeningStockListingPage(targetPage);
                        }
                    };
                });
            }
        }

        // Function to format date
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Function to format date for modal display (Sep 3, 2025)
        function formatDateForModal(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Function to get status class
        function getStatusClass(status) {
            switch (status?.toLowerCase()) {
                case 'opening':
                    return 'active';
                case 'posted':
                    return 'completed';
                case 'cancelled':
                    return 'pending';
                default:
                    return 'pending';
            }
        }

        // Function to view opening stock details
        window.viewOpeningStock = function(voucherNo) {
            // Load voucher details and open view modal
            loadVoucherDetailsForView(voucherNo);
        };

        // Function to close view opening stock modal
        window.closeViewOpeningStockModal = function() {
            const modal = document.getElementById('viewOpeningStockModal');
            if (modal) {
                modal.classList.remove('active');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
        };

        // Function to edit opening stock
        window.editOpeningStock = async function(voucherNo) {
            try {
                console.log('=== DEBUGGING EDIT FUNCTIONALITY ===');
                console.log('Voucher number:', voucherNo);

                // Load voucher data first
                const response = await fetch(`../api/store-opening-stock/voucher/${voucherNo}`);
                console.log('API Response status:', response.status);
                
                const res = await response.json();
                console.log('API Response:', res);
                
                if (!res.success) {
                    showToast(res.error || 'Failed to load opening stock', 'error');
                    return;
                }

                const voucherData = res.data;
                console.log('Voucher data loaded:', voucherData);
                console.log('Voucher items:', voucherData.items);

                // Store current voucher data
                currentEditVoucherData = voucherData;
                currentVoucherId = voucherNo;
                isEditMode = true;

                // ===== 1. SHOW EDIT MODE CONTROLS =====
                console.log('Step 1: Showing edit mode controls');
                const editControls = document.getElementById('editModeControls');
                console.log('Edit controls found:', !!editControls);
                if (editControls) {
                    editControls.style.display = 'block';
                    console.log('Edit controls displayed');
                    
                    const voucherNumberSpan = document.getElementById('editingVoucherNumber');
                    if (voucherNumberSpan) {
                        voucherNumberSpan.textContent = voucherData.voucher_no || '';
                        console.log('Voucher number set in controls');
                    }
                }

                // ===== 2. HIDE LISTING SECTION =====
                console.log('Step 2: Hiding listing section');
                const listingSection = document.querySelector('.mb-\\[25px\\]');
                console.log('Listing section found via querySelector:', !!listingSection);
                
                // Alternative approach - find by heading text
                const headings = document.querySelectorAll('h5');
                let foundListingContainer = null;
                for (let heading of headings) {
                    if (heading.textContent.includes('Store Opening Stock Records')) {
                        foundListingContainer = heading.closest('.mb-\\[25px\\]');
                        console.log('Found listing container via heading:', !!foundListingContainer);
                        break;
                    }
                }
                
                if (foundListingContainer) {
                    console.log('Before hiding - display style:', foundListingContainer.style.display);
                    foundListingContainer.style.display = 'none';
                    console.log('After hiding - display style:', foundListingContainer.style.display);
                    console.log('Listing section hidden successfully');
                } else {
                    console.error('Could not find listing section to hide!');
                }

                // ===== 3. POPULATE MAIN FORM =====
                console.log('Step 3: Populating main form');
                
                // Voucher field
                const voucherField = document.getElementById('voucher');
                console.log('Voucher field found:', !!voucherField);
                if (voucherField) {
                    voucherField.value = voucherData.voucher_no || '';
                    console.log('Voucher field populated:', voucherField.value);
                }

                // Date field
                const voucherDateField = document.getElementById('voucher_date');
                console.log('Date field found:', !!voucherDateField);
                if (voucherDateField) {
                    let formattedDate = voucherData.voucher_date;
                    if (formattedDate && formattedDate.includes('/')) {
                        const parts = formattedDate.split('/');
                        if (parts.length === 3) {
                            formattedDate = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        }
                    }
                    voucherDateField.value = formattedDate || '';
                    console.log('Date field populated:', voucherDateField.value);
                }

                // Unit field
                const unitField = document.getElementById('unit');
                console.log('Unit field found:', !!unitField);
                console.log('Unit field enhanced:', !!unitField?.__enhanced);
                if (unitField && voucherData.unit_id) {
                    unitField.value = voucherData.unit_id;
                    console.log('Unit field value set:', unitField.value);
                    
                    // Handle enhanced select
                    if (unitField.__enhanced) {
                        setTimeout(() => {
                            if (unitField.__enhanced.setDisplayFromValue) {
                                unitField.__enhanced.setDisplayFromValue();
                                console.log('Unit enhanced select display updated');
                            }
                        }, 100);
                    }
                }

                // ===== 4. LOAD ITEMS INTO TABLE =====
                console.log('Step 4: Loading items into table');
                console.log('Items to load:', voucherData.items);
                console.log('Items count:', voucherData.items ? voucherData.items.length : 0);
                
                // Clear existing items
                openingStockItems = [];
                console.log('Cleared existing items');
                
                // Reset nextItemId to a high number to avoid conflicts when editing
                nextItemId = 1000;
                console.log('Reset nextItemId to:', nextItemId);
                
                // Add items from voucher
                if (voucherData.items && Array.isArray(voucherData.items)) {
                    voucherData.items.forEach((item, index) => {
                        console.log(`Processing item ${index}:`, item);
                        
                        if (item.item_id && item.rack_id && item.qty) {
                            const newItem = {
                                id: nextItemId++,
                                item_id: item.item_id,
                                item_name: item.item_name || `Item ${item.item_id}`,
                                rack_id: item.rack_id,
                                rack_name: item.rack_name || `Rack ${item.rack_id}`,
                                qty: item.qty,
                                narration: item.narration || ''
                            };
                            openingStockItems.push(newItem);
                            console.log(`Added item ${index} to array:`, newItem);
                        } else {
                            console.warn(`Item ${index} missing required data:`, item);
                        }
                    });
                }
                
                console.log('Final openingStockItems array:', openingStockItems);
                
                // Update table display using the standard updateTable function
                console.log('Calling updateTable() to refresh display');
                updateTable();
                console.log('Table updated with items');

                // ===== 5. UPDATE BUTTON TEXT =====
                console.log('Step 5: Updating button text');
                const postBtn = document.getElementById('post_btn');
                if (postBtn) {
                    const postBtnText = postBtn.querySelector('span');
                    const postBtnIcon = postBtn.querySelector('i');
                    if (postBtnText) postBtnText.textContent = 'Update Opening Stock';
                    if (postBtnIcon) postBtnIcon.textContent = 'edit';
                    console.log('Button text updated');
                }

                // ===== 6. SCROLL TO FORM =====
                console.log('Step 6: Scrolling to form');
                const formCard = document.querySelector('.trezo-card');
                if (formCard) {
                    formCard.scrollIntoView({ behavior: 'smooth' });
                    console.log('Scrolled to form');
                }

                console.log('=== EDIT MODE ENTERED SUCCESSFULLY ===');
                showToast(`📝 Now editing Opening Stock Voucher #${voucherData.voucher_no}`, 'info');

            } catch (e) {
                console.error('=== ERROR IN EDIT FUNCTIONALITY ===');
                console.error('Error:', e);
                showToast('Failed to load opening stock for editing: ' + e.message, 'error');
            }
        }

        // Function to find opening stock record by ID
        function findOpeningStockRecord(id) {
            // This would typically fetch from the API, but for now we'll use the displayed data
            // In a real implementation, you might want to fetch the full record details
            const tableBody = document.getElementById('storeOpeningStockListingTableBody');
            const rows = tableBody.querySelectorAll('tr');
            
            for (let row of rows) {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 9) {
                    const voucherNo = cells[0].textContent.trim();
                    const date = cells[1].textContent.trim();
                    const unitText = cells[2].textContent.trim();
                    const itemText = cells[3].textContent.trim();
                    const rackText = cells[4].textContent.trim();
                    const quantity = cells[5].textContent.trim();
                    const description = cells[6].textContent.trim();
                    
                    // Extract IDs from the text (e.g., "[1]-Fazal" -> 1)
                    const unitId = unitText.match(/\[(\d+)\]/)?.[1];
                    const itemId = itemText.match(/\[(\d+)\]/)?.[1];
                    const rackId = rackText.match(/\[(\d+)\]/)?.[1];
                    
                    if (unitId && itemId && rackId) {
                        return {
                            id: id,
                            voucher_no: voucherNo,
                            voucher_date: formatDateForInput(date),
                            unit_id: unitId,
                            unit_name: unitText.replace(/\[\d+\]-/, ''),
                            item_id: itemId,
                            item_name: itemText.replace(/\[\d+\]-/, ''),
                            rack_id: rackId,
                            rack_name: rackText.replace(/\[\d+\]-/, ''),
                            qty: quantity,
                            narration: description === '-' ? '' : description
                        };
                    }
                }
            }
            return null;
        };

        // Function to format date for input field
        function formatDateForInput(dateString) {
            if (!dateString || dateString === '-') return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            return date.toISOString().split('T')[0];
        };

        // Function to load voucher details for editing
        async function loadVoucherDetails(voucherNo) {
            try {
                // Show loading
                const modal = document.getElementById('editOpeningStockModal');
                modal.classList.add('active');
                modal.classList.remove('opacity-0', 'pointer-events-none');
                
                // Show loading in modal
                const modalContent = document.querySelector('#editOpeningStockModal .trezo-card-content');
                modalContent.innerHTML = '<div class="text-center py-8 text-gray-500">Loading voucher details...</div>';

                // Fetch voucher details from API
                const response = await fetch(`../api/store-opening-stock/voucher/${voucherNo}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    displayVoucherDetailsInModal(data.data, voucherNo);
                } else {
                    throw new Error(data.error || 'Failed to load voucher details');
                }
                
            } catch (error) {
                console.error('Error loading voucher details:', error);
                showToast('Error: ' + error.message, 'error');
                // Close modal on error
                const modal = document.getElementById('editOpeningStockModal');
                modal.classList.remove('active');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        // Function to display voucher details in modal
        function displayVoucherDetailsInModal(voucherData, voucherNo) {
            const modalContent = document.querySelector('#editOpeningStockModal .trezo-card-content');
            
            // Create form with all items for this voucher
            let formHTML = `
                <form id="editOpeningStockForm">
                    <input type="hidden" id="editVoucherNo" name="voucher_no" value="${voucherNo}">
                    
                    <!-- Header Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-[15px] mb-[20px]">
                        <div class="relative float-group">
                            <input type="text" id="editVoucherNoDisplay" name="voucher_no_display" readonly
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-gray-100 dark:bg-[#1a2332] px-[12px] block w-full outline-0 transition-all text-sm cursor-not-allowed"
                                value="${voucherNo}">
                            <label for="editVoucherNoDisplay" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Voucher #</label>
                        </div>
                        <div class="relative float-group">
                            <input type="date" id="editVoucherDate" name="voucher_date" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                value="${voucherData.voucher_date || ''}">
                            <label for="editVoucherDate" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Date</label>
                        </div>
                        <div class="relative float-group">
                            <select id="editUnit" name="unit_id" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                <option value=""></option>
                            </select>
                            <label for="editUnit" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Unit</label>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="mb-[20px]">
                        <div class="flex items-center justify-between mb-[15px]">
                            <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300">Items in this Voucher</h6>
                            <button type="button" id="addNewItemBtn" class="inline-flex items-center gap-2 rounded-md border border-primary-500 bg-primary-500 text-white px-3 py-1.5 text-sm font-semibold hover:bg-primary-400 hover:border-primary-400 transition-all">
                                <i class="material-symbols-outlined text-lg">add_circle</i>
                                <span>Add Item</span>
                            </button>
                        </div>
                        <div class="space-y-[15px]" id="editItemsList">
            `;

            // Add each item
            voucherData.items.forEach((item, index) => {
                formHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-[15px] p-[15px] border border-gray-200 dark:border-[#172036] rounded-md relative" data-item-index="${index}">
                        <div class="relative float-group">
                            <select name="items[${index}][item_id]" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                <option value=""></option>
                            </select>
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item</label>
                        </div>
                        <div class="relative float-group">
                            <select name="items[${index}][rack_id]" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                <option value=""></option>
                            </select>
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Rack</label>
                        </div>
                        <div class="relative float-group">
                            <input type="number" name="items[${index}][qty]" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                placeholder="" min="0" step="1" value="${Math.round(item.qty)}">
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Quantity</label>
                        </div>
                        <div class="relative float-group">
                            <input type="text" name="items[${index}][narration]"
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                placeholder="Optional description" value="${item.narration || ''}">
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                        </div>
                        <div class="flex items-center">
                            <button type="button" class="text-red-500 dark:text-red-400 leading-none delete-item-btn" onclick="deleteEditModalItem(${index})" title="Delete">
                                <i class="material-symbols-outlined text-sm">delete</i>
                            </button>
                        </div>
                    </div>
                `;
            });

            formHTML += `
                        </div>
                    </div>
                </form>
            `;

            modalContent.innerHTML = formHTML;

            // Load dropdown options and set current values
            loadEditModalDropdownsAndSetValues(voucherData);
            
            // Add event listener for add new item button
            setTimeout(() => {
                const addNewItemBtn = document.getElementById('addNewItemBtn');
                if (addNewItemBtn) {
                    // Remove existing event listeners
                    addNewItemBtn.replaceWith(addNewItemBtn.cloneNode(true));
                    const newAddBtn = document.getElementById('addNewItemBtn');
                    newAddBtn.addEventListener('click', addNewItemToEditModal);
                }
            }, 100);
        }

        // Function to load dropdown options for edit modal
        async function loadEditModalDropdownsAndSetValues(voucherData) {
            try {
                // Load units
                const unitResponse = await fetch('../api/store-opening-stock/units');
                if (unitResponse.ok) {
                    const unitData = await unitResponse.json();
                    if (unitData.success && unitData.data.units) {
                        const editUnit = document.getElementById('editUnit');
                        editUnit.innerHTML = '<option value=""></option>';
                        unitData.data.units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = `[${unit.id}]-${unit.name}`;
                            editUnit.appendChild(option);
                        });
                        editUnit.value = voucherData.unit_id;
                        if (editUnit.__enhanced) editUnit.__enhanced.setDisplayFromValue();
                    }
                }

                // Load items and racks for each item row
                await loadItemsAndRacksForEdit(voucherData);

            } catch (error) {
                console.error('Error loading edit modal dropdowns:', error);
            }
        }

        // Function to load items and racks for edit form
        async function loadItemsAndRacksForEdit(voucherData) {
            try {
                // Load items
                const itemResponse = await fetch('../api/store-opening-stock/items');
                if (itemResponse.ok) {
                    const itemData = await itemResponse.json();
                    if (itemData.success && itemData.data.items) {
                        // Populate all item dropdowns
                        const itemSelects = document.querySelectorAll('select[name*="[item_id]"]');
                        itemSelects.forEach((select, index) => {
                            select.innerHTML = '<option value=""></option>';
                            itemData.data.items.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = `[${item.source_id}]-${item.name}`;
                                select.appendChild(option);
                            });
                            select.value = voucherData.items[index].item_id;
                            if (select.__enhanced) select.__enhanced.setDisplayFromValue();
                        });
                    }
                }

                // Load racks
                const rackResponse = await fetch('../api/store-opening-stock/racks');
                if (rackResponse.ok) {
                    const rackData = await rackResponse.json();
                    if (rackData.success && rackData.data.racks) {
                        // Populate all rack dropdowns
                        const rackSelects = document.querySelectorAll('select[name*="[rack_id]"]');
                        rackSelects.forEach((select, index) => {
                            select.innerHTML = '<option value=""></option>';
                            rackData.data.racks.forEach(rack => {
                                const option = document.createElement('option');
                                option.value = rack.id;
                                option.textContent = `[${rack.id}]-${rack.name}`;
                                select.appendChild(option);
                            });
                            select.value = voucherData.items[index].rack_id;
                            if (select.__enhanced) select.__enhanced.setDisplayFromValue();
                        });
                    }
                }

                // Enhance all selects
                enhanceModalSelects();
                
                // Add event listener for add new item button
                const addNewItemBtn = document.getElementById('addNewItemBtn');
                if (addNewItemBtn) {
                    addNewItemBtn.addEventListener('click', addNewItemToEditModal);
                }

            } catch (error) {
                console.error('Error loading items and racks for edit:', error);
            }
        }

        // Function to add new item to edit modal
        async function addNewItemToEditModal() {
            try {
                const itemsList = document.getElementById('editItemsList');
                const currentItems = itemsList.querySelectorAll('div[data-item-index]');
                const newIndex = currentItems.length;
                
                // Create new item HTML
                const newItemHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-[15px] p-[15px] border border-gray-200 dark:border-[#172036] rounded-md relative" data-item-index="${newIndex}">
                        <div class="relative float-group">
                            <select name="items[${newIndex}][item_id]" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                <option value=""></option>
                            </select>
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Item</label>
                        </div>
                        <div class="relative float-group">
                            <select name="items[${newIndex}][rack_id]" required data-float-select
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm">
                                <option value=""></option>
                            </select>
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Rack</label>
                        </div>
                        <div class="relative float-group">
                            <input type="number" name="items[${newIndex}][qty]" required
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                placeholder="" min="0" step="1" value="">
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Quantity</label>
                        </div>
                        <div class="relative float-group">
                            <input type="text" name="items[${newIndex}][narration]"
                                class="h-[30px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#15203c] px-[12px] block w-full outline-0 transition-all text-sm"
                                placeholder="Optional description" value="">
                            <label class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Description</label>
                        </div>
                        <div class="flex items-center">
                                                    <button type="button" class="text-red-500 dark:text-red-400 leading-none delete-item-btn" onclick="deleteEditModalItem(${newIndex})" title="Delete">
                            <i class="material-symbols-outlined text-sm">delete</i>
                            </button>
                        </div>
                    </div>
                `;
                
                // Add the new item to the list
                itemsList.insertAdjacentHTML('beforeend', newItemHTML);
                
                // Load options for the new selects
                await loadOptionsForNewItem(newIndex);
                
                // Enhance the new selects
                const newItemDiv = itemsList.lastElementChild;
                const newSelects = newItemDiv.querySelectorAll('select[data-float-select]');
                newSelects.forEach(sel => {
                    try {
                        upgradeSelectToSearchable(sel.id, 'Search...');
                    } catch (_) {}
                });
                
                // Refresh all selects to ensure proper functionality
                refreshEditModalSelects();
            } catch (error) {
                console.error('Error adding new item to edit modal:', error);
            }
        }

        // Function to load options for new item
        async function loadOptionsForNewItem(index) {
            try {
                // Load items
                const itemResponse = await fetch('../api/store-opening-stock/items');
                if (itemResponse.ok) {
                    const itemData = await itemResponse.json();
                    if (itemData.success && itemData.data.items) {
                        const itemSelect = document.querySelector(`select[name="items[${index}][item_id]"]`);
                        if (itemSelect) {
                            itemSelect.innerHTML = '<option value=""></option>';
                            itemData.data.items.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = `[${item.source_id}]-${item.name}`;
                                itemSelect.appendChild(option);
                            });
                        }
                    }
                }

                // Load racks
                const rackResponse = await fetch('../api/store-opening-stock/racks');
                if (rackResponse.ok) {
                    const rackData = await rackResponse.json();
                    if (rackData.success && rackData.data.racks) {
                        const rackSelect = document.querySelector(`select[name="items[${index}][rack_id]"]`);
                        if (rackSelect) {
                            rackSelect.innerHTML = '<option value=""></option>';
                            rackData.data.racks.forEach(rack => {
                                const option = document.createElement('option');
                                option.value = rack.id;
                                option.textContent = `[${rack.id}]-${rack.name}`;
                                rackSelect.appendChild(option);
                            });
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading options for new item:', error);
            }
        }

        // Function to delete item from edit modal
        window.deleteEditModalItem = function(index) {
            try {
                const itemsList = document.getElementById('editItemsList');
                const itemToDelete = itemsList.querySelector(`div[data-item-index="${index}"]`);
                
                if (itemToDelete) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Are you sure you want to delete this item?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        itemToDelete.remove();
                        
                        // Reindex remaining items
                        const remainingItems = itemsList.querySelectorAll('div[data-item-index]');
                        remainingItems.forEach((item, newIndex) => {
                            item.setAttribute('data-item-index', newIndex);
                            
                            // Update the name attributes
                            const itemSelect = item.querySelector('select[name*="[item_id]"]');
                            const rackSelect = item.querySelector('select[name*="[rack_id]"]');
                            const qtyInput = item.querySelector('input[name*="[qty]"]');
                            const narrationInput = item.querySelector('input[name*="[narration]"]');
                            const deleteBtn = item.querySelector('.delete-item-btn');
                            
                            if (itemSelect) itemSelect.name = `items[${newIndex}][item_id]`;
                            if (rackSelect) rackSelect.name = `items[${newIndex}][rack_id]`;
                            if (qtyInput) qtyInput.name = `items[${newIndex}][qty]`;
                            if (narrationInput) narrationInput.name = `items[${newIndex}][narration]`;
                            if (deleteBtn) deleteBtn.onclick = () => deleteEditModalItem(newIndex);
                        });
                        
                        // Refresh all selects to ensure proper functionality
                        refreshEditModalSelects();
                            
                            // Show success toaster message
                            showToast('Item has been deleted successfully.', 'success');
                    }
                    });
                }
            } catch (error) {
                console.error('Error deleting item from edit modal:', error);
            }
        };

        // Function to update opening stock
        async function updateOpeningStock() {
            try {
                // Get form data
                const form = document.getElementById('editOpeningStockForm');
                const formData = new FormData(form);
                
                // Validate required fields
                const voucherDate = formData.get('voucher_date');
                const unitId = formData.get('unit_id');
                
                if (!voucherDate || !unitId) {
                    showToast('Please fill in all required header fields', 'error');
                    return;
                }

                // Collect all items data
                const items = [];
                const itemRows = document.querySelectorAll('#editItemsList > div');
                
                itemRows.forEach((row, index) => {
                    const itemId = row.querySelector('select[name*="[item_id]"]').value;
                    const rackId = row.querySelector('select[name*="[rack_id]"]').value;
                    const qty = row.querySelector('input[name*="[qty]"]').value;
                    const narration = row.querySelector('input[name*="[narration]"]').value;
                    
                    if (itemId && rackId && qty) {
                        items.push({
                            item_id: itemId,
                            rack_id: rackId,
                            qty: qty,
                            narration: narration
                        });
                    }
                });

                if (items.length === 0) {
                    showToast('Please add at least one item', 'error');
                    return;
                }

                const updateData = {
                    voucher_no: formData.get('voucher_no'),
                    voucher_date: voucherDate,
                    unit_id: unitId,
                    items: items
                };

                // Debug: Log the data being sent
                console.log('Data being sent to API:', updateData);
                console.log('Items array:', items);
                console.log('Item rows found:', itemRows.length);

                // Show loading
                const updateBtn = document.getElementById('updateOpeningStockBtn');
                const originalText = updateBtn.innerHTML;
                updateBtn.innerHTML = '<i class="ri-loader-4-line mr-[5px] animate-spin"></i>Updating...';
                updateBtn.disabled = true;

                // Submit update to API
                const response = await fetch(`../api/store-opening-stock/update-voucher`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(updateData)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Opening stock updated successfully!', 'success');
                    
                    // Close modal
                    const modal = document.getElementById('editOpeningStockModal');
                    modal.classList.remove('active');
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    
                    // Refresh the listing
                    loadOpeningStockListing();
                } else {
                    throw new Error(result.error || 'Failed to update opening stock');
                }

            } catch (error) {
                console.error('Error updating opening stock:', error);
                showToast('Error: ' + error.message, 'error');
            } finally {
                // Restore button - Store the original text properly
                const updateBtn = document.getElementById('updateOpeningStockBtn');
                updateBtn.innerHTML = '<i class="ri-save-line mr-[5px]"></i>Update';
                updateBtn.disabled = false;
            }
        }

        // Function to delete opening stock voucher
        window.deleteOpeningStock = function(voucherNo) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete voucher #${voucherNo}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call the delete API
                    deleteVoucherFromAPI(voucherNo);
                }
            });
        };

        // Function to delete voucher from API
        async function deleteVoucherFromAPI(voucherNo) {
            try {
                const response = await fetch(`../api/store-opening-stock/delete-voucher`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ voucher_no: voucherNo })
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Voucher deleted successfully!', 'success');
                    // Refresh the listing table
                    if (typeof loadOpeningStockListing === 'function') {
                        loadOpeningStockListing();
                    }
                } else {
                    throw new Error(result.error || 'Failed to delete voucher');
                }
            } catch (error) {
                console.error('Error deleting voucher:', error);
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Function to load voucher details for viewing
        async function loadVoucherDetailsForView(voucherNo) {
            try {
                // Show loading with SweetAlert2
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching voucher details...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Fetch voucher details from API
                const response = await fetch(`../api/store-opening-stock/voucher/${voucherNo}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    displayVoucherDetailsForView(data.data, voucherNo);
                } else {
                    throw new Error(data.error || 'Failed to load voucher details');
                }
                
            } catch (error) {
                console.error('Error loading voucher details for view:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load voucher details: ' + error.message
                });
            }
        }

        // Function to display voucher details for viewing
        function displayVoucherDetailsForView(voucherData, voucherNo) {
            try {
                console.log('Rendering voucher details:', voucherData);
                if (!voucherData) {
                    throw new Error('Voucher data is null or undefined');
                }
                
                const html = buildOpeningStockViewHtml(voucherData, voucherNo);
                if (!html) {
                    throw new Error('Failed to build HTML template');
                }
                
                console.log('HTML template built successfully, length:', html.length);
                
                Swal.fire({
                    title: `Opening Stock Voucher #${voucherNo}`,
                    html: html,
                    width: '90%',
                    showCloseButton: true,
                    showConfirmButton: false,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    customClass: {
                        container: 'opening-stock-view-modal'
                    },
                    didOpen: async () => {
                        try {
                            const node = document.getElementById('opening-stock-view-wrapper');
                            if (!node) {
                                console.error('Modal wrapper not found');
                                return;
                            }

            // Print functionality
            document.getElementById('btnPrintOpeningStock')?.addEventListener('click', () => {
                printHtmlElement(node, `Opening Stock Voucher #${voucherNo}`);
            });

            // PDF functionality
            document.getElementById('btnDownloadOpeningStockPdf')?.addEventListener('click', async () => {
                console.log('PDF button clicked for Opening Stock:', voucherNo);
                try {
                    const wrapper = document.getElementById('opening-stock-view-wrapper');
                    if (wrapper) wrapper.classList.add('generating-pdf');
                    
                                    // Prepare voucher data for PDF
                                    const pdfVoucherData = {
                                        voucher_no: voucherNo,
                                        voucher_date: voucherData.voucher_date,
                                        unit_name: voucherData.unit_name,
                                        total_items: voucherData.items.length,
                                        total_qty: voucherData.items.reduce((sum, item) => sum + parseFloat(item.qty || 0), 0),
                                        status: 'Opening',
                                        created_at: voucherData.created_at || voucherData.voucher_date,
                                        items: voucherData.items
                                    };
                                    
                                    // Generate PDF using the new PDF generator
                                    if (typeof window.generateOpeningStockPdf === 'function') {
                                        await window.generateOpeningStockPdf(pdfVoucherData);
                                        showToast('PDF generated successfully', 'success');
                                    } else {
                                        throw new Error('PDF generation function not available');
                                    }
                                    
                                    if (wrapper) wrapper.classList.remove('generating-pdf');
                } catch (e) {
                    console.error('PDF generation error:', e);
                    showToast('Failed to generate PDF: ' + e.message, 'error');
                                    const wrapper = document.getElementById('opening-stock-view-wrapper');
                    if (wrapper) wrapper.classList.remove('generating-pdf');
                }
            });
                            
                        } catch (error) {
                            console.error('Error setting up modal event listeners:', error);
                        }
                    }
                });
                
            } catch (error) {
                console.error('Error displaying voucher details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to display voucher details: ' + error.message
                });
            }
        }

        // Function to build opening stock view HTML
        function buildOpeningStockViewHtml(voucherData, voucherNo) {
            try {
                // Log what fields are actually available in the voucher data
                console.log('Available voucher fields:', Object.keys(voucherData));
                console.log('Voucher data values:', voucherData);
                
                // Get the template
                const template = document.getElementById('opening-stock-view-template');
                if (!template) {
                    console.error('Template not found');
                    return null;
                }
                
                let html = template.innerHTML;

                // Calculate totals
                const totalItems = voucherData.items.length;
                const totalQty = voucherData.items.reduce((sum, item) => sum + parseFloat(item.qty || 0), 0);

                // Replace placeholders with actual data
                const map = {
                    '{{voucher_no}}': voucherNo,
                    '{{voucher_date}}': formatDateForModal(voucherData.voucher_date) || 'N/A',
                    '{{unit_name}}': voucherData.unit_name || 'N/A',
                    '{{total_items}}': totalItems,
                    '{{total_qty}}': Math.round(totalQty),
                    '{{status}}': 'Opening',
                    '{{created_at}}': formatDateForModal(voucherData.created_at || voucherData.voucher_date) || 'N/A',
                    '{{printed_on}}': new Date().toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                };
                
                // Replace all placeholders
                Object.keys(map).forEach(key => {
                    html = html.replace(new RegExp(key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), map[key]);
                });
                
                // Build item rows
                let rows = '';
                if (voucherData.items && Array.isArray(voucherData.items) && voucherData.items.length > 0) {
                    console.log('Building rows for items:', voucherData.items);
                    rows = voucherData.items.map((item, idx) => {
                        console.log(`Item ${idx + 1} data:`, item);
                        
                        return `
                            <tr>
                                <td>${idx + 1}</td>
                                <td style="text-align:left;">[${item.source_id || ''}] ${item.item_name || '-'}</td>
                                <td>${item.rack_name || '-'}</td>
                                <td class="num">${Math.round(item.qty) || '0'}</td>
                                <td>${item.narration || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                }
                
                html = html.replace('__ITEM_ROWS__', rows);
                
                console.log('HTML template built successfully');
                return html;

            } catch (error) {
                console.error('Error building opening stock view HTML:', error);
                return null;
            }
        }



        // Helper function to load scripts
        function loadScript(url) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = url;
                script.async = true;
                script.onload = resolve;
                script.onerror = () => reject(new Error('Failed to load ' + url));
                document.head.appendChild(script);
            });
        }

        // Helper function to print HTML element
        function printHtmlElement(element, title = 'Document') {
            if (!element) return;
            
            const printWindow = window.open('', '_blank');
            const printContent = element.cloneNode(true);
            
            // Remove action buttons for print
            const actionButtons = printContent.querySelector('.invoice-actions');
            if (actionButtons) actionButtons.remove();
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${title}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        @media print { 
                            body { margin: 0; }
                            @page { size: A4 landscape; margin: 12mm; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent.outerHTML}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        // Function to refresh all searchable selects in edit modal
        function refreshEditModalSelects() {
            // Enhance all selects in the modal
            enhanceModalSelects();
            
            // Also refresh the display values for existing selects
            const allSelects = document.querySelectorAll('#editOpeningStockModal select[data-float-select]');
            allSelects.forEach(sel => {
                if (sel.__enhanced && sel.__enhanced.setDisplayFromValue) {
                    sel.__enhanced.setDisplayFromValue();
                }
            });
        }

        // Exit edit mode function (for cancellation)
        function exitEditMode() {
            console.log('=== EXITING EDIT MODE ===');
            
            // Hide edit mode controls
            const editControls = document.getElementById('editModeControls');
            if (editControls) {
                editControls.style.display = 'none';
                console.log('Edit controls hidden');
            }
            
            // Show listing section
            const headings = document.querySelectorAll('h5');
            let foundListingContainer = null;
            for (let heading of headings) {
                if (heading.textContent.includes('Store Opening Stock Records')) {
                    foundListingContainer = heading.closest('.mb-\\[25px\\]');
                    break;
                }
            }
            
            if (foundListingContainer) {
                foundListingContainer.style.display = 'block';
                console.log('Listing section shown');
            }
            
            // Reset form and state
            isEditMode = false;
            currentVoucherId = null;
            currentEditVoucherData = null;
            openingStockItems = [];
            
            // Reset button text
            const postBtn = document.getElementById('post_btn');
            if (postBtn) {
                const postBtnText = postBtn.querySelector('span');
                const postBtnIcon = postBtn.querySelector('i');
                if (postBtnText) postBtnText.textContent = 'Post Opening Stock';
                if (postBtnIcon) postBtnIcon.textContent = 'check_circle';
            }
            
            // Clear form fields
            document.getElementById('voucher').value = '';
            document.getElementById('voucher_date').value = new Date().toISOString().slice(0, 10);
            
            // Clear unit field and refresh enhanced select
            const unitField = document.getElementById('unit');
            if (unitField) {
                unitField.value = '';
                if (unitField.__enhanced && unitField.__enhanced.setDisplayFromValue) {
                    unitField.__enhanced.setDisplayFromValue();
                }
            }
            
            // Clear item table
            const tableBody = document.querySelector('#storeOpeningStockTable tbody');
            if (tableBody) {
                tableBody.innerHTML = '';
            }
            
            // Refresh item select box and load default list
            refreshItemSelectBox();
            
            console.log('Edit mode exited successfully');
            showToast('✋ Edit mode cancelled', 'info');
        }

        // Exit edit mode function (for successful updates)
        function exitEditModeAfterUpdate() {
            console.log('=== EXITING EDIT MODE AFTER SUCCESSFUL UPDATE ===');
            
            // Hide edit mode controls
            const editControls = document.getElementById('editModeControls');
            if (editControls) {
                editControls.style.display = 'none';
                console.log('Edit controls hidden');
            }
            
            // Show listing section
            const headings = document.querySelectorAll('h5');
            let foundListingContainer = null;
            for (let heading of headings) {
                if (heading.textContent.includes('Store Opening Stock Records')) {
                    foundListingContainer = heading.closest('.mb-\\[25px\\]');
                    break;
                }
            }
            
            if (foundListingContainer) {
                foundListingContainer.style.display = 'block';
                console.log('Listing section shown');
            }
            
            // Reset form and state
            isEditMode = false;
            currentVoucherId = null;
            currentEditVoucherData = null;
            openingStockItems = [];
            
            // Reset button text
            const postBtn = document.getElementById('post_btn');
            if (postBtn) {
                const postBtnText = postBtn.querySelector('span');
                const postBtnIcon = postBtn.querySelector('i');
                if (postBtnText) postBtnText.textContent = 'Post Opening Stock';
                if (postBtnIcon) postBtnIcon.textContent = 'check_circle';
            }
            
            // Clear form fields
            document.getElementById('voucher').value = '';
            document.getElementById('voucher_date').value = new Date().toISOString().slice(0, 10);
            
            // Clear unit field and refresh enhanced select
            const unitField = document.getElementById('unit');
            if (unitField) {
                unitField.value = '';
                if (unitField.__enhanced && unitField.__enhanced.setDisplayFromValue) {
                    unitField.__enhanced.setDisplayFromValue();
                }
            }
            
            // Clear item table
            const tableBody = document.querySelector('#storeOpeningStockTable tbody');
            if (tableBody) {
                tableBody.innerHTML = '';
            }
            
            // Refresh item select box and load default list
            refreshItemSelectBox();
            
            // Don't load next voucher number when exiting after successful update
            // This preserves the current voucher number for the listing display
            
            console.log('Edit mode exited after successful update');
        }

        // Cancel edit button event listener
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', function() {
                exitEditMode();
            });
        }

    </script>
</body>

</html>