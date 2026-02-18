<!DOCTYPE html>
<html dir="ltr">

<?php include 'includes/head.php'; ?>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="main-content transition-all flex flex-col overflow-hidden min-h-screen" id="main-content">
        <?php if ($defaultRoleStatus == 'SUA' || $defaultRoleStatus == 'A'): ?>
        <!-- Breadcrumb -->
        <div class="mb-[25px] md:flex items-center justify-between mt-[25px]">
            <h5 class="!mb-0">Dashboard</h5>
            <ol class="breadcrumb mt-[12px] md:mt-0">
                <li class="breadcrumb-item inline-block relative text-sm ltr:first:ml-0 rtl:first:mr-0 ltr:last:mr-0 rtl:last:ml-0 ltr:ml-[11px] rtl:mr-[11px] first:ml-0 first:mr-0">
                    <span class="inline-block relative ltr:pl-[22px] rtl:pr-[22px] text-gray-600 dark:text-gray-400">
                        <i class="material-symbols-outlined absolute ltr:left-0 rtl:right-0 !text-lg -mt-px text-primary-500 top-1/2 -translate-y-1/2">home</i>
                        Dashboard
                    </span>
                </li>
            </ol>
        </div>

        <div class="grid grid-cols-1 gap-[25px] mb-[25px]">
            <div class="trezo-card bg-white dark:bg-[#0c1427] p-[24px] md:p-[30px] rounded-md border border-gray-100 dark:border-[#172036] shadow-sm">
                <h5 class="!mb-2 text-black dark:text-white font-semibold">Welcome to Khawaja Traders</h5>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Quick access to key modules. Click a shortcut below to navigate.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-[20px] md:gap-[24px]">
                    <a href="item_management.php" class="group flex items-center gap-[18px] px-[24px] py-[20px] md:px-[28px] md:py-[24px] rounded-lg border border-gray-100 dark:border-[#172036] bg-white dark:bg-[#0c1427] hover:border-primary-500/50 dark:hover:border-primary-500/50 hover:shadow-lg hover:shadow-primary-500/10 dark:hover:shadow-primary-500/5 transition-all duration-300 hover:-translate-y-0.5">
                        <div class="flex-shrink-0 w-[48px] h-[48px] rounded-xl bg-primary-500/10 dark:bg-primary-500/20 flex items-center justify-center group-hover:bg-primary-500/20 dark:group-hover:bg-primary-500/30 transition-colors">
                            <i class="material-symbols-outlined text-[22px] text-primary-500 leading-none">inventory_2</i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block font-semibold text-black dark:text-white text-base mb-1">Item Management</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 leading-snug">Manage categories, groups, items &amp; attributes</span>
                        </div>
                        <div class="flex-shrink-0 ml-auto">
                            <i class="material-symbols-outlined text-[20px] text-gray-400 dark:text-gray-500 group-hover:text-primary-500 group-hover:translate-x-1 transition-all">arrow_forward</i>
                        </div>
                    </a>
                    <a href="store_opening_stock.php" class="group flex items-center gap-[18px] px-[24px] py-[20px] md:px-[28px] md:py-[24px] rounded-lg border border-gray-100 dark:border-[#172036] bg-white dark:bg-[#0c1427] hover:border-primary-500/50 dark:hover:border-primary-500/50 hover:shadow-lg hover:shadow-primary-500/10 dark:hover:shadow-primary-500/5 transition-all duration-300 hover:-translate-y-0.5">
                        <div class="flex-shrink-0 w-[48px] h-[48px] rounded-xl bg-success-500/10 dark:bg-success-500/20 flex items-center justify-center group-hover:bg-success-500/20 dark:group-hover:bg-success-500/30 transition-colors">
                            <i class="material-symbols-outlined text-[22px] text-success-500 leading-none">warehouse</i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block font-semibold text-black dark:text-white text-base mb-1">Store Opening Stock</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 leading-snug">Record &amp; manage opening stock entries</span>
                        </div>
                        <div class="flex-shrink-0 ml-auto">
                            <i class="material-symbols-outlined text-[20px] text-gray-400 dark:text-gray-500 group-hover:text-success-500 group-hover:translate-x-1 transition-all">arrow_forward</i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php include 'includes/footer.php'; ?>
    </div>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>

    <?php include 'includes/scripts.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var userData = <?php echo json_encode(getCurrentUser()); ?>;
        if (!userData) {
            window.location.href = 'sign-in.php';
            return;
        }
        var userNameEl = document.querySelector('#user-dropdown span');
        if (userNameEl && userData.first_name) {
            userNameEl.textContent = (userData.first_name || '') + ' ' + (userData.last_name || '');
        }
    });
    </script>
</body>
</html>
