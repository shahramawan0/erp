// Print voucher function
function printVoucher(voucherId) {
    console.log('Printing voucher:', voucherId);
    // Here you would implement the actual print functionality
    alert('Print voucher #' + voucherId);
}

// Custom searchable dropdown functions (copied from store_demand.php)
function upgradeSelectToSearchable(selectId, placeholder) {
    const selectEl = document.getElementById(selectId);
    if (!selectEl || selectEl.__enhanced) return;
    selectEl.classList.add('opacity-0', 'pointer-events-none', 'absolute');
    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    const control = document.createElement('button');
    control.type = 'button';
    control.className = 'block w-full h-[35px] rounded-md text-left text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] outline-0 transition-all text-sm flex items-center justify-between focus:border-primary-500';
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
    search.className = 'w-full h-[35px] px-[12px] text-sm text-black dark:text-white bg-white dark:bg-[#0c1427] border-0 border-b border-gray-200 dark:border-[#172036] outline-0 focus:outline-0 focus:ring-0 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded-t-md';
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
        activeIndex = -1,
        justSelected = false;

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
        console.log(`buildItems called for ${selectId} with filter: "${filter}"`);
        list.innerHTML = '';
        currentItems = [];
        activeIndex = -1;
        const options = Array.from(selectEl.options);
        console.log(`buildItems - Found ${options.length} options for ${selectId}:`, options.map(opt => ({
            value: opt.value,
            text: opt.textContent
        })));
        options.forEach(opt => {
            if (opt.value === '') return;
            const text = opt.text || '';
            if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;
            const item = document.createElement('div');
            item.className = 'px-[12px] py-[6px] text-sm text-black dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c]';
            item.textContent = text;
            item.dataset.value = opt.value;
            item.addEventListener('click', (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                justSelected = true;
                selectEl.value = opt.value;
                controlText.textContent = text || placeholderText;
                controlText.className = text ? '' : 'text-gray-400';
                panel.classList.add('hidden');
                setTimeout(() => {
                    try {
                        control.focus();
                    } catch (_) {}
                    setTimeout(() => {
                        justSelected = false;
                    }, 100);
                }, 0);
                selectEl.dispatchEvent(new Event('change'));
                try {
                    ensureLabel();
                } catch (_) {}
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
        // For itemSelect, load initial items; for others, build from options
        if (selectId === 'itemSelect') {
            // Check if items are already loaded
            const options = Array.from(selectEl.options);
            if (options.length <= 1) { // Only has the default "Select Item" option
                loadItems();
            } else {
        buildItems('');
            }
        } else {
            buildItems('');
        }
        setTimeout(() => search.focus(), 0);
        if (grp) grp.classList.add('is-focused');

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
    control.addEventListener('keyup', (e) => {
        if (!(e.key === 'Enter' || e.key === 'NumpadEnter')) return;
        if (panel.classList.contains('hidden')) {
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
        const searchValue = search.value;
        // Use searchItems for itemSelect, buildItems for others
        if (selectId === 'itemSelect') {
            searchItems(searchValue);
        } else {
        buildItems(searchValue);
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

// Load units for dropdown (3rd report)
function loadUnits() {
    const unitSelect = document.getElementById('unitSelect');
    if (!unitSelect) return;

    // Show loading state
    unitSelect.innerHTML = '<option value="">Loading units...</option>';

    // Fetch units from our custom pending-demand-reports API
    fetch('../api/pending-demand-reports/units')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                unitSelect.innerHTML = '<option value="">Select Unit</option>';

                // Add units to dropdown
                data.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `[${unit.id}]-${unit.name}`;
                    unitSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} units`);

                // Enhance the select after loading units
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                unitSelect.innerHTML = '<option value="">Error loading units</option>';
                console.error('Error loading units:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
            console.error('Error loading units:', error);
        });
}

// Load units for dropdown (4th report)
function loadUnits4() {
    const unitSelect = document.getElementById('unitSelect4');
    if (!unitSelect) return;

    // Show loading state
    unitSelect.innerHTML = '<option value="">Loading units...</option>';

    // Fetch units from our custom pending-demand-reports API
    fetch('../api/pending-demand-reports/units')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                unitSelect.innerHTML = '<option value="">Select Unit</option>';

                // Add units to dropdown
                data.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `[${unit.id}]-${unit.name}`;
                    unitSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} units for 4th report`);

                // Enhance the select after loading units
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                unitSelect.innerHTML = '<option value="">Error loading units</option>';
                console.error('Error loading units:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
            console.error('Error loading units:', error);
        });
}

// Load units for dropdown (5th report)
function loadUnits5() {
    const unitSelect = document.getElementById('unitSelect5');
    if (!unitSelect) return;

    // Show loading state
    unitSelect.innerHTML = '<option value="">Loading units...</option>';

    // Fetch units from our custom pending-demand-reports API
    fetch('../api/pending-demand-reports/units')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                unitSelect.innerHTML = '<option value="">Select Unit</option>';

                // Add units to dropdown
                data.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `[${unit.id}]-${unit.name}`;
                    unitSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} units for 5th report`);

                // Enhance the select after loading units
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                unitSelect.innerHTML = '<option value="">Error loading units</option>';
                console.error('Error loading units:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
            console.error('Error loading units:', error);
        });
}

// Load sites for dropdown (dependent on unit)
function loadSites(unitId) {
    const siteSelect = document.getElementById('siteSelect');
    if (!siteSelect) return;

    // Show loading state
    siteSelect.innerHTML = '<option value="">Loading sites...</option>';

    // Build URL with unit_id parameter
    const url = unitId ?
        `../api/pending-demand-reports/sites?unit_id=${encodeURIComponent(unitId)}` :
        '../api/pending-demand-reports/sites';

    // Fetch sites from our custom pending-demand-reports API
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                siteSelect.innerHTML = '<option value="">Select Site</option>';

                // Add sites to dropdown
                data.data.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = `[${site.id}]-${site.name}`;
                    siteSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} sites for unit ${unitId}`);

                // Enhance the select after loading sites
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                siteSelect.innerHTML = '<option value="">Error loading sites</option>';
                console.error('Error loading sites:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            siteSelect.innerHTML = '<option value="">Error loading sites</option>';
            console.error('Error loading sites:', error);
        });
}

// Load sites for dropdown (5th report - dependent on unit)
function loadSites5(unitId) {
    const siteSelect = document.getElementById('siteSelect5');
    if (!siteSelect) return;

    // Show loading state
    siteSelect.innerHTML = '<option value="">Loading sites...</option>';

    // Build URL with unit_id parameter
    const url = unitId ?
        `../api/pending-demand-reports/sites?unit_id=${encodeURIComponent(unitId)}` :
        '../api/pending-demand-reports/sites';

    // Fetch sites from our custom pending-demand-reports API
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                siteSelect.innerHTML = '<option value="">Select Site</option>';

                // Add sites to dropdown
                data.data.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = `[${site.id}]-${site.name}`;
                    siteSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} sites for unit ${unitId} in 5th report`);

                // Enhance the select after loading sites
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                siteSelect.innerHTML = '<option value="">Error loading sites</option>';
                console.error('Error loading sites:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            siteSelect.innerHTML = '<option value="">Error loading sites</option>';
            console.error('Error loading sites:', error);
        });
}

// Load departments for dropdown (5th report)
function loadDepartments5(unitId, siteId) {
    const departmentSelect = document.getElementById('departmentSelect5');
    if (!departmentSelect) return;

    // Show loading state
    departmentSelect.innerHTML = '<option value="">Loading departments...</option>';

    // Build URL with unit_id and site_id parameters
    let url = '../api/pending-demand-reports/departments';
    const params = [];
    if (unitId) params.push(`unit_id=${encodeURIComponent(unitId)}`);
    if (siteId) params.push(`site_id=${encodeURIComponent(siteId)}`);
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    // Fetch departments from our custom pending-demand-reports API
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                departmentSelect.innerHTML = '<option value="">Select Department</option>';

                // Add departments to dropdown
                data.data.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = `[${department.id}]-${department.name}`;
                    departmentSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} departments for 5th report (Unit: ${unitId}, Site: ${siteId})`);

                // Enhance the select after loading departments
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                console.error('Error loading departments:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
            console.error('Error loading departments:', error);
        });
}

// Load departments for dropdown (3rd report)
function loadDepartments() {
    const departmentSelect = document.getElementById('departmentSelect');
    if (!departmentSelect) return;

    // Show loading state
    departmentSelect.innerHTML = '<option value="">Loading departments...</option>';

    // Fetch departments from our custom pending-demand-reports API
    fetch('../api/pending-demand-reports/departments')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Clear loading option
                departmentSelect.innerHTML = '<option value="">Select Department</option>';

                // Add departments to dropdown
                data.data.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = `[${department.id}]-${department.name}`;
                    departmentSelect.appendChild(option);
                });

                console.log(`Loaded ${data.data.length} departments`);

                // Enhance the select after loading departments
                setTimeout(() => enhanceFloatSelects(), 100);
            } else {
                // Show error state
                departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                console.error('Error loading departments:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
            console.error('Error loading departments:', error);
        });
}

// Load items for dropdown (initial 50 items)
function loadItems() {
    console.log('Loading initial items...');
    const itemSelect = document.getElementById('itemSelect');
    if (!itemSelect) return;

    // Show loading state
    itemSelect.innerHTML = '<option value="">Loading items...</option>';

    // Fetch initial 50 items from the main items API (same as store_demand.php)
    fetch('../api/items?limit=50')
        .then(response => response.json())
        .then(data => {
            console.log('Items API response:', data);
            if (data.success && data.data && data.data.records) {
                // Clear loading option
                itemSelect.innerHTML = '<option value="">Select Item</option>';

                // Sort items by ID in ascending order (same as store_demand.php)
                let items = data.data.records || [];
                if (items && items.length > 0) {
                    items.sort((a, b) => parseInt(a.id) - parseInt(b.id));
                }

                // Add items to dropdown
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `[${item.id}]-${item.name}`;
                    itemSelect.appendChild(option);
                });

                console.log(`Loaded ${items.length} initial items`);
                console.log('ItemSelect options after loading:', Array.from(itemSelect.options).map(opt => ({
                    value: opt.value,
                    text: opt.textContent
                })));

                // Force refresh the enhanced select
                setTimeout(() => {
                    const enhanced = itemSelect.__enhanced;
                    if (enhanced) {
                        console.log('Refreshing enhanced select...');
                        enhanced.refresh();
                    } else {
                        console.log('Enhanced select not found, re-enhancing...');
                        // Remove existing enhancement and re-apply
                        if (itemSelect.__enhanced) {
                            delete itemSelect.__enhanced;
                        }
                        enhanceFloatSelects();
                    }
                }, 100);
            } else {
                // Show error state
                itemSelect.innerHTML = '<option value="">Error loading items</option>';
                console.error('Error loading items:', data.error);
            }
        })
        .catch(error => {
            // Show error state
            itemSelect.innerHTML = '<option value="">Error loading items</option>';
            console.error('Error loading items:', error);
        });
}

// Search items by ID or name (same as store_demand.php)
function searchItems(searchTerm) {
    if (!searchTerm || searchTerm.trim() === '') {
        loadItems(); // Load initial options if search is empty
        return;
    }

    const params = new URLSearchParams({
        search: searchTerm.trim(),
        limit: 50
    });

    fetch(`../api/search/items?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const select = document.getElementById('itemSelect');
                if (select) {
                    select.innerHTML = '<option value="">Select Item</option>';
                    result.data.forEach(item => {
                        if (item && item.id && item.name) {
                            select.innerHTML += `<option value="${item.id}">[${item.id}]-${item.name}</option>`;
                        }
                    });

                    // Update the enhanced select display
                    const enhanced = select.__enhanced;
                    if (enhanced) {
                        enhanced.refresh();
                    }
                }
            } else {
                console.error('Search error:', result.error);
                showToast('Error searching items: ' + (result.error || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            showToast('Error searching items', 'error');
        });
}

// Initialize enhanced selects on page load
document.addEventListener('DOMContentLoaded', function() {
    // Enhance all selects with data-float-select attribute
    setTimeout(() => enhanceFloatSelects(), 100);

    // Add event listener for unit select change in 4th report
    const unitSelect4 = document.getElementById('unitSelect4');
    if (unitSelect4) {
        unitSelect4.addEventListener('change', function() {
            const unitId = this.value;
            if (unitId) {
                loadSites(unitId);
            } else {
                // Clear sites if no unit selected
                const siteSelect = document.getElementById('siteSelect');
                if (siteSelect) {
                    siteSelect.innerHTML = '<option value="">Select Site</option>';
                    setTimeout(() => enhanceFloatSelects(), 100);
                }
            }
        });
    }

    // Add event listener for unit select change in 5th report
    const unitSelect5 = document.getElementById('unitSelect5');
    if (unitSelect5) {
        unitSelect5.addEventListener('change', function() {
            const unitId = this.value;
            if (unitId) {
                loadSites5(unitId);
            } else {
                // Clear sites if no unit selected
                const siteSelect5 = document.getElementById('siteSelect5');
                if (siteSelect5) {
                    siteSelect5.innerHTML = '<option value="">Select Site</option>';
                    setTimeout(() => enhanceFloatSelects(), 100);
                }
            }
        });
    }
});

// View report details function
function viewReportDetails(reportType) {
    console.log('viewReportDetails called with:', reportType);
    
    // Hide the no report message
    const noReportMessage = document.getElementById('noReportMessage');
    if (noReportMessage) {
        noReportMessage.style.display = 'none';
    }

    // Show the report details card
    const reportDetailsCard = document.getElementById('reportDetailsCard');
    if (reportDetailsCard) {
        reportDetailsCard.style.display = 'block';
    }

    // Remove active state from all report cards
    const allCards = document.querySelectorAll('.quotation-item');
    allCards.forEach(card => {
        card.classList.remove('active');
        // Remove blue bar from all cards
        const blueBar = card.querySelector('.blue-accent-bar');
        if (blueBar) {
            blueBar.style.display = 'none';
        }
    });

    // Add active state to clicked card
    const clickedCard = event.currentTarget;
    clickedCard.classList.add('active');

    // Show blue bar only on active card
    const activeBlueBar = clickedCard.querySelector('.blue-accent-bar');
    if (activeBlueBar) {
      activeBlueBar.style.display = 'block';
  }

  // COMPLETELY REFRESH: Clear all previous data and states
  clearAllReportData();

  // Hide all report sections
    const allSections = document.querySelectorAll('.report-section');
    allSections.forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected report section
    const selectedSection = document.getElementById('report-' + reportType);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        
        // Hide default message when any report is selected
        const defaultMessage = document.getElementById('defaultReportMessage');
        if (defaultMessage) {
            defaultMessage.style.display = 'none';
            console.log('Hidden default message');
        }
    }

    // Load data for specific reports
    if (reportType === 'item-wise-pending') {
        // Load items immediately when this report is selected
        loadItems();
        // Auto-focus on first field after items are loaded
        setTimeout(() => {
            const itemSelect = document.getElementById('itemSelect');
            if (itemSelect && itemSelect.__enhanced && itemSelect.__enhanced.control) {
                itemSelect.__enhanced.control.focus();
            } else if (itemSelect) {
                itemSelect.focus();
            }
        }, 300);
    } else if (reportType === 'unit-department-wise') {
        loadUnits();
        loadDepartments();
        // Auto-focus on first field after units are loaded
        setTimeout(() => {
            const unitSelect = document.getElementById('unitSelect');
            if (unitSelect && unitSelect.__enhanced && unitSelect.__enhanced.control) {
                unitSelect.__enhanced.control.focus();
            } else if (unitSelect) {
                unitSelect.focus();
            }
        }, 300);
    } else if (reportType === 'unit-site-wise') {
        loadUnits4();
        // Sites will be loaded when unit is selected
        // Auto-focus on first field after units are loaded
        setTimeout(() => {
            const unitSelect4 = document.getElementById('unitSelect4');
            if (unitSelect4 && unitSelect4.__enhanced && unitSelect4.__enhanced.control) {
                unitSelect4.__enhanced.control.focus();
            } else if (unitSelect4) {
                unitSelect4.focus();
            }
        }, 300);
    } else if (reportType === 'unit-site-department-wise') {
        loadUnits5();
        // Sites and departments will be loaded when unit is selected
        // Auto-focus on first field after units are loaded
        setTimeout(() => {
            const unitSelect5 = document.getElementById('unitSelect5');
            if (unitSelect5 && unitSelect5.__enhanced && unitSelect5.__enhanced.control) {
                unitSelect5.__enhanced.control.focus();
            } else if (unitSelect5) {
                unitSelect5.focus();
            }
        }, 300);
    }
    
    // Set dates to 30-day range for Pending Demand List report
    if (reportType === 'pending-demand-list') {
        setDefaultDateRange();
        // Auto-focus on first field (from date)
        setTimeout(() => {
            const fromDate = document.getElementById('fromDate');
            if (fromDate) {
                fromDate.focus();
            }
        }, 100);
    }
  }

  // Function to completely clear all report data and reset states
  function clearAllReportData() {
      console.log('Clearing all report data and resetting states...');

      // Clear all table bodies
      const tableBodies = [
          'pendingDemandsTableBody',
          'itemWiseDemandsTableBody',
          'unitDepartmentWiseTableBody',
          'unitSiteWiseTableBody',
          'unitSiteDepartmentWiseTableBody'
      ];
      
      tableBodies.forEach(tableBodyId => {
          const tableBody = document.getElementById(tableBodyId);
          if (tableBody) {
              tableBody.innerHTML = '';
          }
      });

      // Hide all loading states
      const loadingStates = [
          'loadingState',
          'itemWiseLoadingState',
          'unitDepartmentWiseLoadingState',
          'unitSiteWiseLoadingState',
          'unitSiteDepartmentWiseLoadingState'
      ];
      
      loadingStates.forEach(loadingStateId => {
          const loadingState = document.getElementById(loadingStateId);
          if (loadingState) {
              loadingState.classList.add('hidden');
          }
      });

      // Hide all empty states
      const emptyStates = [
          'emptyState',
          'itemWiseEmptyState',
          'unitDepartmentWiseEmptyState',
          'unitSiteWiseEmptyState',
          'unitSiteDepartmentWiseEmptyState'
      ];
      
      emptyStates.forEach(emptyStateId => {
          const emptyState = document.getElementById(emptyStateId);
          if (emptyState) {
              emptyState.classList.add('hidden');
          }
      });

      // Hide all pagination containers
      const paginationContainers = [
          'paginationContainer',
          'itemWisePaginationContainer',
          'unitDepartmentWisePaginationContainer',
          'unitSiteWisePaginationContainer',
          'unitSiteDepartmentWisePagination'
      ];
      
      paginationContainers.forEach(paginationId => {
          const pagination = document.getElementById(paginationId);
          if (pagination) {
              pagination.classList.add('hidden');
          }
      });

      // Reset all form inputs
      const formInputs = [
          'fromDate',
          'toDate',
          'itemSelect',
          'unitSelect',
          'unitSelect4',
          'unitSelect5',
          'siteSelect4',
          'siteSelect5',
          'departmentSelect',
          'departmentSelect5'
      ];
      
      formInputs.forEach(inputId => {
          const input = document.getElementById(inputId);
          if (input) {
              input.value = '';
              // Reset enhanced select labels
              const grp = input.closest('.grp');
              if (grp) {
                  grp.classList.remove('is-focused');
                  const label = grp.querySelector('.float-label');
                  if (label) {
                      label.classList.remove('active');
                  }
              }
          }
      });

      // Clear all dependent dropdowns
      const dependentDropdowns = [
          'siteSelect4',
          'siteSelect5'
      ];
      
      dependentDropdowns.forEach(dropdownId => {
          const dropdown = document.getElementById(dropdownId);
          if (dropdown) {
              dropdown.innerHTML = '<option value="">Select Site</option>';
          }
      });

      // Reset all tables to show
      const tables = [
          'pendingDemandsTable',
          'itemWiseDemandsTable',
          'unitDepartmentWiseTable',
          'unitSiteWiseTable',
          'unitSiteDepartmentWiseTable'
      ];
      
      tables.forEach(tableId => {
          const table = document.getElementById(tableId);
          if (table) {
              table.classList.remove('hidden');
          }
      });

      // Clear any active print windows
      if (window.printWindow) {
          try {
              window.printWindow.close();
          } catch (e) {
              console.log('Print window already closed');
          }
          window.printWindow = null;
      }

      console.log('All report data cleared successfully');
  }

  // Function to set default 30-day date range for Pending Demand List
  function setDefaultDateRange() {
      console.log('Setting default 30-day date range...');
      
      // Set dates to 30-day range (from 30 days ago to today)
      const today = new Date();
      const thirtyDaysAgo = new Date();
      thirtyDaysAgo.setDate(today.getDate() - 30);
      
      const fromDateInput = document.getElementById('fromDate');
      const toDateInput = document.getElementById('toDate');
      
      if (fromDateInput) {
          fromDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
          // Reset enhanced select label
          const grp = fromDateInput.closest('.grp');
          if (grp) {
              grp.classList.remove('is-focused');
              const label = grp.querySelector('.float-label');
              if (label) {
                  label.classList.remove('active');
              }
          }
      }
      
      if (toDateInput) {
          toDateInput.value = today.toISOString().split('T')[0];
          // Reset enhanced select label
          const grp = toDateInput.closest('.grp');
          if (grp) {
              grp.classList.remove('is-focused');
              const label = grp.querySelector('.float-label');
              if (label) {
                  label.classList.remove('active');
              }
          }
      }
      
      console.log('Default date range set successfully');
  }

  // Print voucher from modal
function printVoucherFromModal() {
    const voucherInvoice = document.getElementById('voucher-invoice');
    if (voucherInvoice) {
        window.print();
    }
}

// Download PDF from modal - functionality moved to print window
function downloadVoucherPdf() {
    console.log('PDF download is now handled in the print window');
    showToast('Please use the PDF button in the print window', 'info');
}

// Close report modal
function closeReportModal() {
    // Hide the report details card
    const reportDetailsCard = document.getElementById('reportDetailsCard');
    if (reportDetailsCard) {
        reportDetailsCard.style.display = 'none';
    }

    // Show the no report message
    const noReportMessage = document.getElementById('noReportMessage');
    if (noReportMessage) {
        noReportMessage.style.display = 'block';
    }
}

// Array to hold all menu list and chevron IDs
const allMenus = [{
        listId: 'reportsList',
        chevronId: 'reportsChevron'
    },
    {
        listId: 'poReportsList',
        chevronId: 'poReportsChevron'
    },
    {
        listId: 'stockList',
        chevronId: 'stockChevron'
    },
    {
        listId: 'financialList',
        chevronId: 'financialChevron'
    },
    {
        listId: 'inventoryList',
        chevronId: 'inventoryChevron'
    },
    {
        listId: 'analyticsList',
        chevronId: 'analyticsChevron'
    }
];

// Function to close all menus except the one specified
function closeAllMenusExcept(currentListId) {
    allMenus.forEach(menu => {
        if (menu.listId !== currentListId) {
            const list = document.getElementById(menu.listId);
            const chevron = document.getElementById(menu.chevronId);
            if (list && chevron) {
                list.style.maxHeight = '0px';
                chevron.textContent = 'expand_more';
                chevron.style.transform = 'rotate(180deg)';
            }
        }
    });
}

// Make the function globally available for other scripts
window.closeAllMenusExcept = closeAllMenusExcept;

// Toggle reports menu function
function toggleReportsMenu() {
    closeAllMenusExcept('reportsList'); // Close others before toggling this one
    const reportsList = document.getElementById('reportsList');
    const chevron = document.getElementById('reportsChevron');

    if (reportsList && chevron) {
        if (reportsList.style.maxHeight === '0px' || reportsList.style.maxHeight === '') {
            // Expand
            reportsList.style.maxHeight = '400px';
            chevron.textContent = 'expand_less';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            // Collapse
            reportsList.style.maxHeight = '0px';
            chevron.textContent = 'expand_more';
            chevron.style.transform = 'rotate(180deg)';
        }
    }
}

// Setup tab navigation for all reports
function setupTabNavigation() {
    console.log('Setting up tab navigation...');
    
    // Define navigation orders for each report
    const navigationOrders = {
        // Pending Demand List Report
        'pendingDemandsTable': [
            'fromDate',
            'toDate', 
            'btnFilterReport'
        ],
        // Item Wise Report
        'itemWiseDemandsTable': [
            'itemSelect',
            'btnFetchItemWiseReport'
        ],
        // Unit & Department Wise Report
        'unitDepartmentWiseDemandsTable': [
            'unitSelect',
            'departmentSelect',
            'btnFetchUnitDeptWiseReport'
        ],
        // Unit & Site Wise Report
        'unitSiteWiseDemandsTable': [
            'unitSelect4',
            'siteSelect',
            'btnFetchReport4'
        ],
        // Unit, Site & Department Wise Report
        'unitSiteDepartmentWiseDemandsTable': [
            'unitSelect5',
            'siteSelect5',
            'departmentSelect5',
            'btnFetchReport5'
        ]
    };

    // Add enter key navigation to all relevant fields
    Object.keys(navigationOrders).forEach(tableId => {
        const fields = navigationOrders[tableId];
        fields.forEach((fieldId, index) => {
            const field = document.getElementById(fieldId);
            if (field) {
                // Handle enhanced select fields differently
                if (field.hasAttribute('data-float-select')) {
                    console.log(`Setting up navigation for enhanced select: ${fieldId}`);
                    // For enhanced select fields, we need to wait for them to be enhanced
                    const setupEnhancedSelectNavigation = () => {
                        if (field.__enhanced && field.__enhanced.control) {
                            console.log(`Enhanced select ready for: ${fieldId}`);
                            field.__enhanced.control.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter') {
                                    console.log(`Enter pressed on ${fieldId}, moving to next field...`);
                                    e.preventDefault();
                                    
                                    // Move to next field in the sequence
                                    const nextIndex = index + 1;
                                    if (nextIndex < fields.length) {
                                        const nextField = document.getElementById(fields[nextIndex]);
                                        if (nextField) {
                                            console.log(`Moving to next field: ${fields[nextIndex]}`);
                                            if (nextField.hasAttribute('data-float-select') && nextField.__enhanced && nextField.__enhanced.control) {
                                                nextField.__enhanced.control.focus();
                                            } else {
                                                nextField.focus();
                                            }
                                        }
                                    } else {
                                        // If it's the last field (button), trigger the appropriate action
                                        console.log(`Last field reached, triggering action for: ${fieldId}`);
                                        triggerFetchAction(fieldId);
                                    }
                                }
                            });
                        } else {
                            // Retry after a short delay if not enhanced yet
                            console.log(`Enhanced select not ready for ${fieldId}, retrying...`);
                            setTimeout(setupEnhancedSelectNavigation, 100);
                        }
                    };
                    setupEnhancedSelectNavigation();
                } else {
                    // Regular field navigation
                    field.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            console.log(`Enter pressed on regular field: ${fieldId}, moving to next field...`);
                            e.preventDefault();
                            
                            // Move to next field in the sequence
                            const nextIndex = index + 1;
                            if (nextIndex < fields.length) {
                                const nextField = document.getElementById(fields[nextIndex]);
                                if (nextField) {
                                    console.log(`Moving to next field: ${fields[nextIndex]}`);
                                    if (nextField.hasAttribute('data-float-select') && nextField.__enhanced && nextField.__enhanced.control) {
                                        nextField.__enhanced.control.focus();
                                    } else {
                                        nextField.focus();
                                    }
                                }
                            } else {
                                // If it's the last field (button), trigger the appropriate action
                                console.log(`Last field reached, triggering action for: ${fieldId}`);
                                triggerFetchAction(fieldId);
                            }
                        }
                    });
                }
            }
        });
    });

    // Helper function to trigger the appropriate fetch action
    function triggerFetchAction(fieldId) {
        if (fieldId === 'btnFilterReport') {
            fetchPendingDemands();
        } else if (fieldId === 'btnFetchItemWiseReport') {
            fetchItemWisePendingDemands();
        } else if (fieldId === 'btnFetchUnitDeptWiseReport') {
            fetchUnitDepartmentWisePendingDemands();
        } else if (fieldId === 'btnFetchReport4') {
            fetchUnitSiteWisePendingDemands(1);
        } else if (fieldId === 'btnFetchReport5') {
            fetchUnitSiteDepartmentWisePendingDemands(1);
        }
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports page loaded - PENDING DEMANDS SCRIPT RUNNING');

    // Add global keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+P for print functionality
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            console.log('Ctrl+P pressed, determining which report to print...');
            
            // Check which report section is currently visible
            const reportSections = [
                { id: 'report-pending-demand-list', name: 'Pending Demand List', printFunction: 'printAllPendingDemands' },
                { id: 'report-item-wise-pending', name: 'Item Wise', printFunction: 'printItemWisePendingDemands' },
                { id: 'report-unit-department-wise', name: 'Unit Department Wise', printFunction: 'printUnitDepartmentWisePendingDemands' },
                { id: 'report-unit-site-wise', name: 'Unit Site Wise', printFunction: 'printUnitSiteWisePendingDemands' },
                { id: 'report-unit-site-department-wise', name: 'Unit Site Department Wise', printFunction: 'printUnitSiteDepartmentWisePendingDemands' }
            ];
            
            let activeReport = null;
            
            reportSections.forEach(section => {
                const element = document.getElementById(section.id);
                if (element && element.style.display !== 'none') {
                    activeReport = section;
                    console.log(`Active report found: ${section.name}`);
                }
            });
            
            if (activeReport) {
                console.log(`Printing ${activeReport.name}...`);
                // Call the appropriate print function
                if (activeReport.printFunction === 'printAllPendingDemands') {
                    printAllPendingDemands();
                } else if (activeReport.printFunction === 'printItemWisePendingDemands') {
                    printItemWisePendingDemands();
                } else if (activeReport.printFunction === 'printUnitDepartmentWisePendingDemands') {
                    printUnitDepartmentWisePendingDemands();
                } else if (activeReport.printFunction === 'printUnitSiteWisePendingDemands') {
                    printUnitSiteWisePendingDemands();
                } else if (activeReport.printFunction === 'printUnitSiteDepartmentWisePendingDemands') {
                    printUnitSiteDepartmentWisePendingDemands();
                }
            } else {
                console.log('No active report found, defaulting to pending demands...');
                printAllPendingDemands();
            }
        }
    });

    // Initialize reports menu as expanded by default
    const reportsList = document.getElementById('reportsList');
    const chevron = document.getElementById('reportsChevron');
    if (reportsList && chevron) {
        reportsList.style.maxHeight = '400px';
        chevron.textContent = 'expand_less';
    }

    // Set default dates for Pending Demand List Report
    console.log('About to call setDefaultDates...');
    console.log('Current time:', new Date().toISOString());
    setDefaultDates();
    console.log('setDefaultDates called');
    
    // Auto-focus on first field of default report (Pending Demand List)
    setTimeout(() => {
        const fromDate = document.getElementById('fromDate');
        if (fromDate) {
            fromDate.focus();
        }
    }, 500);

    // Add event listener for fetch button
    const fetchBtn = document.getElementById('btnFilterReport');
    if (fetchBtn) {
        fetchBtn.addEventListener('click', fetchPendingDemands);
    }

    // Setup tab navigation for all reports after enhanced selects are ready
    setTimeout(() => {
        setupTabNavigation();
    }, 500);

    // Also setup navigation when enhanced selects are ready
    const checkEnhancedSelects = () => {
        const itemSelect = document.getElementById('itemSelect');
        if (itemSelect && itemSelect.__enhanced && itemSelect.__enhanced.control) {
            console.log('Enhanced selects are ready, setting up navigation...');
            setupTabNavigation();
        } else {
            setTimeout(checkEnhancedSelects, 200);
        }
    };
    checkEnhancedSelects();

    // Add event listener for item-wise fetch button
    const itemWiseFetchBtn = document.getElementById('btnFetchItemWiseReport');
    if (itemWiseFetchBtn) {
        itemWiseFetchBtn.addEventListener('click', fetchItemWisePendingDemands);
    }

    // Add event listener for item-wise print button
    const itemWisePrintBtn = document.getElementById('btnPrintReport2');
    if (itemWisePrintBtn) {
        itemWisePrintBtn.addEventListener('click', printItemWisePendingDemands);
    }

    // Add event listener for unit-department-wise fetch button
    const unitDeptWiseFetchBtn = document.getElementById('btnFetchUnitDeptWiseReport');
    if (unitDeptWiseFetchBtn) {
        unitDeptWiseFetchBtn.addEventListener('click', fetchUnitDepartmentWisePendingDemands);
    }

    // Add event listener for unit-site-wise fetch button
    const unitSiteWiseFetchBtn = document.getElementById('btnFetchReport4');
    if (unitSiteWiseFetchBtn) {
        unitSiteWiseFetchBtn.addEventListener('click', () => fetchUnitSiteWisePendingDemands(1));
    }

    // Add event listener for unit-department-wise print button
    const unitDeptWisePrintBtn = document.getElementById('btnPrintUnitDeptWiseReport');
    if (unitDeptWisePrintBtn) {
        unitDeptWisePrintBtn.addEventListener('click', printUnitDepartmentWisePendingDemands);
    }

    // Add event listener for unit-site-wise print button
    const unitSiteWisePrintBtn = document.getElementById('btnPrintReport4');
    if (unitSiteWisePrintBtn) {
        unitSiteWisePrintBtn.addEventListener('click', printUnitSiteWisePendingDemands);
    }

    // Add event listener for unit-site-department-wise fetch button
    const unitSiteDeptWiseFetchBtn = document.getElementById('btnFetchReport5');
    if (unitSiteDeptWiseFetchBtn) {
        unitSiteDeptWiseFetchBtn.addEventListener('click', () => fetchUnitSiteDepartmentWisePendingDemands(1));
    }

    // Add event listener for unit-site-department-wise print button
    const unitSiteDeptWisePrintBtn = document.getElementById('btnPrintReport5');
    if (unitSiteDeptWisePrintBtn) {
        unitSiteDeptWisePrintBtn.addEventListener('click', printUnitSiteDepartmentWisePendingDemands);
    }

    // Add event listener for unit-site-department-wise PDF button
    const unitSiteDeptWisePdfBtn = document.getElementById('btnDownloadReport5');
    if (unitSiteDeptWisePdfBtn) {
        unitSiteDeptWisePdfBtn.addEventListener('click', printUnitSiteDepartmentWisePendingDemands);
    }

    // Add event listener for unit select change in 5th report (Unit & Site & Department Wise)
    const unitSelect5 = document.getElementById('unitSelect5');
    if (unitSelect5) {
        unitSelect5.addEventListener('change', function() {
            const unitId = this.value;
            if (unitId) {
                loadSites5(unitId);
            } else {
                // Clear sites and departments if no unit selected
                const siteSelect5 = document.getElementById('siteSelect5');
                const departmentSelect5 = document.getElementById('departmentSelect5');
                if (siteSelect5) {
                    siteSelect5.innerHTML = '<option value="">Select Site</option>';
                    setTimeout(() => enhanceFloatSelects(), 100);
                }
                if (departmentSelect5) {
                    departmentSelect5.innerHTML = '<option value="">Select Department</option>';
                    setTimeout(() => enhanceFloatSelects(), 100);
                }
            }
        });
    }

    // Add event listener for site select change in 5th report
    const siteSelect5 = document.getElementById('siteSelect5');
    if (siteSelect5) {
        siteSelect5.addEventListener('change', function() {
            const unitId = document.getElementById('unitSelect5').value;
            const siteId = this.value;
            if (unitId && siteId) {
                loadDepartments5(unitId, siteId);
            } else {
                // Clear departments if no site selected
                const departmentSelect5 = document.getElementById('departmentSelect5');
                if (departmentSelect5) {
                    departmentSelect5.innerHTML = '<option value="">Select Department</option>';
                    setTimeout(() => enhanceFloatSelects(), 100);
                }
            }
        });
    }

    // Add event listeners for modal buttons
    const printBtn = document.getElementById('btnPrintVoucher');
    const pdfBtn = document.getElementById('btnDownloadVoucherPdf');

    if (printBtn) {
        printBtn.addEventListener('click', printVoucherFromModal);
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', downloadVoucherPdf);
    }
});

// Function to set default dates (30 days back from current date)
function setDefaultDates() {
    console.log('Setting default dates for pending demand reports...');
    
    // Wait a bit more to ensure DOM is fully ready
    setTimeout(() => {
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);

    // Format dates as YYYY-MM-DD for input type="date"
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

        const fromDateValue = formatDate(thirtyDaysAgo);
        const toDateValue = formatDate(today);
        
        console.log('From date:', fromDateValue);
        console.log('To date:', toDateValue);

    // Set the date inputs
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');

        console.log('From date input found:', fromDateInput);
        console.log('To date input found:', toDateInput);

    if (fromDateInput) {
            fromDateInput.value = fromDateValue;
            console.log('From date set to:', fromDateInput.value);
        } else {
            console.error('From date input NOT FOUND!');
    }

    if (toDateInput) {
            toDateInput.value = toDateValue;
            console.log('To date set to:', toDateInput.value);
        } else {
            console.error('To date input NOT FOUND!');
    }
    }, 500); // Wait 500ms for DOM to be fully ready
}

// Function to fetch item-wise pending demands
function fetchItemWisePendingDemands(page = 1) {
    const itemId = document.getElementById('itemSelect').value;

    if (!itemId) {
        showToast('Please select an item', 'error');
        return;
    }

    // Show loading state
    showItemWiseLoadingState();

    // Build API URL
    const apiUrl = `../api/pending-demand-reports/item-wise-pending-demands?item_id=${encodeURIComponent(itemId)}&page=${page}&limit=10&company_id=1`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayItemWisePendingDemands(data.data, data.pagination);
            } else {
                showToast(data.message || 'Error fetching item-wise pending demands', 'error');
                showItemWiseEmptyState();
            }
        })
        .catch(error => {
            console.error('Error fetching item-wise pending demands:', error);
            showToast('Error fetching item-wise pending demands', 'error');
            showItemWiseEmptyState();
        });
}

// Function to fetch unit and department wise pending demands
function fetchUnitDepartmentWisePendingDemands(page = 1) {
    const unitId = document.getElementById('unitSelect').value;
    const departmentId = document.getElementById('departmentSelect').value;

    if (!unitId || !departmentId) {
        showToast('Please select both Unit and Department filters', 'error');
        return;
    }

    // Show loading state
    showUnitDeptWiseLoadingState();

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/unit-department-wise-pending-demands?page=${page}&limit=10&company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (departmentId) {
        apiUrl += `&department_id=${encodeURIComponent(departmentId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUnitDepartmentWisePendingDemands(data.data, data.pagination);
            } else {
                showToast(data.message || 'Error fetching unit and department wise pending demands', 'error');
                showUnitDeptWiseEmptyState();
            }
        })
        .catch(error => {
            console.error('Error fetching unit and department wise pending demands:', error);
            showToast('Error fetching unit and department wise pending demands', 'error');
            showUnitDeptWiseEmptyState();
        });
}

// Function to print unit and department wise pending demands
function printUnitDepartmentWisePendingDemands() {
    const unitId = document.getElementById('unitSelect').value;
    const departmentId = document.getElementById('departmentSelect').value;

    if (!unitId || !departmentId) {
        showToast('Please select both Unit and Department filters first', 'error');
        return;
    }

    // Get the print button and show loading state
    const printBtn = document.getElementById('btnPrintUnitDeptWiseReport');
    const originalContent = printBtn.innerHTML;
    
    printBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating...';
    printBtn.disabled = true;
    printBtn.style.opacity = '0.7';

    // Show loading toast
    // showToast('Generating print data...', 'info');

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/print/unit-department-wise-pending-demands?company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (departmentId) {
        apiUrl += `&department_id=${encodeURIComponent(departmentId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open print window with the HTML content
                openUnitDepartmentWisePrintWindow(data.html, data.total_demands || 0);
                showToast(`Print data generated successfully for ${data.total_demands || 0} demands`, 'success');
            } else {
                showToast(data.message || 'Error generating print data', 'error');
            }
        })
        .catch(error => {
            console.error('Error generating print data:', error);
            showToast('Error generating print data: ' + error.message, 'error');
        })
        .finally(() => {
            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';
        });
}

// Function to fetch unit and site wise pending demands
function fetchUnitSiteWisePendingDemands(page = 1) {
    const unitId = document.getElementById('unitSelect4').value;
    const siteId = document.getElementById('siteSelect').value;

    if (!unitId || !siteId) {
        showToast('Please select both Unit and Site filters', 'error');
        return;
    }

    // Show loading state
    showUnitSiteWiseLoadingState();

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/unit-site-wise-pending-demands?page=${page}&limit=10&company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (siteId) {
        apiUrl += `&site_id=${encodeURIComponent(siteId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUnitSiteWisePendingDemands(data.data, data.pagination);
            } else {
                showToast(data.message || 'Error fetching unit and site wise pending demands', 'error');
                showUnitSiteWiseEmptyState();
            }
        })
        .catch(error => {
            console.error('Error fetching unit and site wise pending demands:', error);
            showToast('Error fetching unit and site wise pending demands', 'error');
            showUnitSiteWiseEmptyState();
        });
}

// Function to print unit and site wise pending demands
function printUnitSiteWisePendingDemands() {
    const unitId = document.getElementById('unitSelect4').value;
    const siteId = document.getElementById('siteSelect').value;

    if (!unitId || !siteId) {
        showToast('Please select both Unit and Site filters first', 'error');
        return;
    }

    // Get the print button and show loading state
    const printBtn = document.getElementById('btnPrintReport4');
    const originalContent = printBtn.innerHTML;
    
    printBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating...';
    printBtn.disabled = true;
    printBtn.style.opacity = '0.7';

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/print/unit-site-wise-pending-demands?company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (siteId) {
        apiUrl += `&site_id=${encodeURIComponent(siteId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open print window with the HTML content
                openUnitSiteWisePrintWindow(data.html, data.total_demands || 0);
                showToast(`Print data generated successfully for ${data.total_demands || 0} demands`, 'success');
            } else {
                showToast(data.message || 'Error generating print data', 'error');
            }
        })
        .catch(error => {
            console.error('Error generating print data:', error);
            showToast('Error generating print data: ' + error.message, 'error');
        })
        .finally(() => {
            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';
        });
}

// Function to display unit and site wise pending demands in grouped format
function displayUnitSiteWisePendingDemands(demands, pagination) {
    const tableBody = document.getElementById('unitSiteWiseDemandsTableBody');
    const table = document.getElementById('unitSiteWiseDemandsTable');

    if (!demands || demands.length === 0) {
        showUnitSiteWiseEmptyState();
        return;
    }

    // Hide loading and empty states
    hideUnitSiteWiseLoadingState();
    hideUnitSiteWiseEmptyState();

    // Show table and pagination
    if (table) {
        table.classList.remove('hidden');
    }
    showUnitSiteWisePagination(pagination);

    // Clear existing content
    if (tableBody) {
        tableBody.innerHTML = '';
    }

    // Group demands by Demand No
    const groupedByDemand = {};
    demands.forEach(demand => {
        const key = demand.demand_number || 'N/A';
        if (!groupedByDemand[key]) {
            groupedByDemand[key] = {
                demand_number: demand.demand_number,
                demand_date: demand.demand_date,
                demands: []
            };
        }
        groupedByDemand[key].demands.push(demand);
    });

    // Display grouped data
    Object.values(groupedByDemand).forEach(group => {
        // Add demand header row (only once per group)
        const demandHeaderRow = document.createElement('tr');
        demandHeaderRow.className = 'bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-700';
        demandHeaderRow.innerHTML = `
            <td colspan="9" class="px-[20px] py-[15px] font-semibold text-blue-800 dark:text-blue-200 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${formatDate(group.demand_date)} | DM# ${group.demand_number || 'N/A'}
                    </div>
                </div>
            </td>
        `;
        if (tableBody) {
            tableBody.appendChild(demandHeaderRow);
        }

        // Add all demands within this demand number
        group.demands.forEach(demand => {
            // Add items for this demand
            if (demand.items && demand.items.length > 0) {
                let demandTotal = 0;

                demand.items.forEach((item, index) => {
                    const itemRow = document.createElement('tr');
                    itemRow.className = `border-b border-gray-200 dark:border-[#172036] ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''}`;
                    itemRow.innerHTML = `
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_code || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_name || 'N/A'}<br />${item.item_description_detail || item.item_description || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.last_rate ? parseFloat(item.last_rate) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.quotation_rate ? parseFloat(item.quotation_rate) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.total_amount ? parseFloat(item.total_amount) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${demand.department_name || 'N/A'}<br />${demand.demanding_person_name || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">
                            <span class="priority-badge priority-${getPriorityClass(item.priority)}">${item.priority || 'N/A'}</span>
                        </td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">
                            <span class="status-badge status-${getStatusClass(item.item_status)}">${item.item_status || 'Pending'}</span>
                        </td>
                    `;
                    if (tableBody) {
                        tableBody.appendChild(itemRow);
                    }

                    // Add to demand total
                    if (item.total_amount) {
                        demandTotal += parseFloat(item.total_amount);
                    }
                });

                // Add total row for this demand
                const totalRow = document.createElement('tr');
                totalRow.className = 'bg-green-50 dark:bg-green-900/20 border-t-2 border-green-200 dark:border-green-700 font-semibold';
                totalRow.innerHTML = `
                    <td colspan="5" class="px-[20px] py-[8px] text-sm text-green-800 dark:text-green-200 text-right font-bold">
                        Demand Total Amount:
                    </td>
                    <td class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200 font-bold">
                        ${demandTotal > 0 ? parseFloat(demandTotal) : '-'}
                    </td>
                    <td colspan="3" class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200">
                        <!-- Empty cells for remaining columns -->
                    </td>
                `;
                if (tableBody) {
                    tableBody.appendChild(totalRow);
                }
            } else {
                // No items for this demand
                const noItemsRow = document.createElement('tr');
                noItemsRow.className = 'border-b border-gray-200 dark:border-[#172036] bg-gray-50 dark:bg-gray-800/50';
                noItemsRow.innerHTML = `
                    <td colspan="9" class="px-[20px] py-[11px] text-sm text-gray-500 dark:text-gray-400 text-center italic">
                        No items found for this demand
                    </td>
                `;
                if (tableBody) {
                    tableBody.appendChild(noItemsRow);
                }
            }
        });
    });
}

// State management functions for Unit Site Wise report
function showUnitSiteWiseLoadingState() {
    // Create loading state if it doesn't exist
    let loadingState = document.getElementById('unitSiteWiseLoadingState');
    if (!loadingState) {
        loadingState = document.createElement('div');
        loadingState.id = 'unitSiteWiseLoadingState';
        loadingState.className = 'text-center py-8';
        loadingState.innerHTML = `
            <div class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        `;
        const table = document.getElementById('unitSiteWiseDemandsTable');
        if (table && table.parentNode) {
            table.parentNode.insertBefore(loadingState, table);
        }
    }
    loadingState.classList.remove('hidden');
    
    // Hide other elements if they exist
    const table = document.getElementById('unitSiteWiseDemandsTable');
    if (table) {
        table.classList.add('hidden');
    }
    
    const emptyState = document.getElementById('unitSiteWiseEmptyState');
    if (emptyState) {
        emptyState.classList.add('hidden');
    }
    
    const pagination = document.getElementById('unitSiteWisePagination');
    if (pagination) {
        pagination.classList.add('hidden');
    }
}

function hideUnitSiteWiseLoadingState() {
    const loadingState = document.getElementById('unitSiteWiseLoadingState');
    if (loadingState) {
        loadingState.classList.add('hidden');
    }
    
    // Show pagination when hiding loading state
    const pagination = document.getElementById('unitSiteWisePagination');
    if (pagination) {
        pagination.classList.remove('hidden');
        pagination.style.display = 'flex';
    }
}

function showUnitSiteWiseEmptyState() {
    // Create empty state if it doesn't exist
    let emptyState = document.getElementById('unitSiteWiseEmptyState');
    if (!emptyState) {
        emptyState = document.createElement('div');
        emptyState.id = 'unitSiteWiseEmptyState';
        emptyState.className = 'text-center py-8';
        emptyState.innerHTML = `
            <div class="text-gray-500 dark:text-gray-400">
                <i class="material-symbols-outlined text-4xl mb-2">inbox</i>
                <p>No demands found for the selected filters</p>
            </div>
        `;
        const table = document.getElementById('unitSiteWiseDemandsTable');
        if (table && table.parentNode) {
            table.parentNode.insertBefore(emptyState, table);
        }
    }
    emptyState.classList.remove('hidden');
    
    // Hide other elements if they exist
    const table = document.getElementById('unitSiteWiseDemandsTable');
    if (table) {
        table.classList.add('hidden');
    }
    
    const loadingState = document.getElementById('unitSiteWiseLoadingState');
    if (loadingState) {
        loadingState.classList.add('hidden');
    }
    
    const pagination = document.getElementById('unitSiteWisePagination');
    if (pagination) {
        pagination.classList.add('hidden');
    }
}

function hideUnitSiteWiseEmptyState() {
    const emptyState = document.getElementById('unitSiteWiseEmptyState');
    if (emptyState) {
        emptyState.classList.add('hidden');
    }
    
    // Show pagination when hiding empty state
    const pagination = document.getElementById('unitSiteWisePagination');
    if (pagination) {
        pagination.classList.remove('hidden');
        pagination.style.display = 'flex';
    }
}

function showUnitSiteWisePagination(pagination) {
    const container = document.getElementById('unitSiteWisePagination');
    if (!container) {
        return;
    }
    
    const info = container.querySelector('p');
    const buttons = container.querySelector('ol');

    if (!pagination) {
        container.classList.add('hidden');
        return;
    }

    // Update pagination info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    if (info) {
        info.textContent = `Showing ${start}-${end} of ${pagination.total_records} results`;
    }

    // Generate pagination buttons
    if (buttons) {
        buttons.innerHTML = '';

        // Previous button
        const prevBtn = document.createElement('li');
        prevBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_prev ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    ${!pagination.has_prev ? 'disabled' : ''} 
                    onclick="fetchUnitSiteWisePendingDemands(${pagination.current_page - 1})">
                <i class="material-symbols-outlined text-sm">chevron_left</i>
            </button>
        `;
        buttons.appendChild(prevBtn);

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('li');
            pageBtn.innerHTML = `
                <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${i === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                        onclick="fetchUnitSiteWisePendingDemands(${i})">
                    ${i}
                </button>
            `;
            buttons.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('li');
        nextBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_next ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    ${!pagination.has_next ? 'disabled' : ''} 
                    onclick="fetchUnitSiteWisePendingDemands(${pagination.current_page + 1})">
                <i class="material-symbols-outlined text-sm">chevron_right</i>
            </button>
        `;
        buttons.appendChild(nextBtn);
    }

    container.classList.remove('hidden');
    container.style.display = 'flex';
}

// Function to open unit and site wise print window
function openUnitSiteWisePrintWindow(html, recordCount) {
    try {
        console.log(`Opening unit and site wise print window for ${recordCount} records`);

        // Create a new window for printing (landscape orientation)
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            showToast('Error: Could not open print window. Please allow popups for this site.', 'error');
            return;
        }

        // Write the HTML directly (buttons will be added via DOM manipulation)
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();

        // Add PDF functions to the print window with custom filename
        addUnitSiteWisePDFFunctionsToWindow(printWindow);

        // Add action buttons after the window loads
        printWindow.onload = function() {
            addUnitSiteWiseActionButtonsToWindow(printWindow, recordCount);
        };

        console.log('Unit and site wise print window opened successfully with action buttons');

    } catch (error) {
        console.error('Error opening unit and site wise print window:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// Function to add PDF generation functions to the print window
function addUnitSiteWisePDFFunctionsToWindow(printWindow) {
    const script = printWindow.document.createElement('script');
    script.textContent = `
        function printReport() {
            console.log('Print button clicked');
            window.print();
        }
        
        function downloadAsPDF() {
            console.log('Download as PDF button clicked');
            
            // Show loading state
            const pdfBtn = document.getElementById('pdf-btn');
            const originalContent = pdfBtn.innerHTML;
            pdfBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating PDF...';
            pdfBtn.disabled = true;
            pdfBtn.style.opacity = '0.7';
            
            // Generate PDF using HTML-to-PDF conversion
            try {
                // Load required libraries dynamically
                loadLibrariesForPDF().then(() => {
                    generateHTMLToPDF(originalContent);
                }).catch(error => {
                    console.error('Error loading libraries:', error);
                    alert('Error loading PDF libraries: ' + error.message);
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF: ' + error.message);
                pdfBtn.innerHTML = originalContent;
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
        
        function loadLibrariesForPDF() {
            return new Promise((resolve, reject) => {
                // Check if libraries are already loaded
                if (window.html2canvas && window.jspdf) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                const totalLibraries = 2;
                
                function checkComplete() {
                    loadedCount++;
                    if (loadedCount === totalLibraries) {
                        resolve();
                    }
                }
                
                // Load html2canvas
                if (!window.html2canvas) {
                    const html2canvasScript = document.createElement('script');
                    html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                    html2canvasScript.onload = checkComplete;
                    html2canvasScript.onerror = () => reject(new Error('Failed to load html2canvas'));
                    document.head.appendChild(html2canvasScript);
                } else {
                    checkComplete();
                }
                
                // Load jsPDF
                if (!window.jspdf) {
                    const jsPDFScript = document.createElement('script');
                    jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                    jsPDFScript.onload = checkComplete;
                    jsPDFScript.onerror = () => reject(new Error('Failed to load jsPDF'));
                    document.head.appendChild(jsPDFScript);
                } else {
                    checkComplete();
                }
            });
        }
        
        function generateHTMLToPDF(originalContent) {
            try {
                console.log('Starting HTML to PDF conversion');
                
                // Get the report content (excluding action buttons)
                const reportContent = document.querySelector('.print-header') || document.body;
                
                if (!reportContent) {
                    throw new Error('Could not find report content to convert');
                }
                
                // Temporarily remove action buttons from DOM for clean PDF
                const actionButtons = document.getElementById('action-buttons');
                let actionButtonsParent = null;
                let actionButtonsNextSibling = null;
                
                if (actionButtons) {
                    actionButtonsParent = actionButtons.parentNode;
                    actionButtonsNextSibling = actionButtons.nextSibling;
                    actionButtonsParent.removeChild(actionButtons);
                }
                
                // Add comprehensive CSS for PDF generation
                const pdfStyles = document.createElement('style');
                pdfStyles.textContent = 
                    '#action-buttons { display: none !important; visibility: hidden !important; }' +
                    '@media print {' +
                        'body { margin: 0 !important; padding: 15px 20px !important; font-size: 11px !important; }' +
                        '.print-header { margin-top: 0 !important; page-break-after: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-group { page-break-inside: avoid !important; break-inside: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-header { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table { page-break-inside: auto !important; margin: 0 !important; }' +
                        '.items-table tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        '.demand-header-row { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table tbody tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        'table { border-collapse: collapse !important; }' +
                        'td, th { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                    '}';
                document.head.appendChild(pdfStyles);
                
                // Configure html2canvas options for better quality and complete capture
                const options = {
                    scale: 2, // Higher resolution for better quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: window.innerWidth,
                    windowHeight: window.innerHeight,
                    logging: false,
                    removeContainer: true
                };
                
                console.log('Converting HTML to canvas with dimensions:', {
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight
                });
                
                html2canvas(reportContent, options).then(canvas => {
                    console.log('Canvas created with dimensions:', canvas.width, 'x', canvas.height);
                    
                    // Create PDF with proper dimensions and margins (Landscape A4)
                    const { jsPDF } = window.jspdf;
                    const pageWidth = 297; // A4 landscape width in mm
                    const pageHeight = 210; // A4 landscape height in mm
                    const marginTop = 15; // Top margin in mm
                    const marginBottom = 15; // Bottom margin in mm
                    const marginLeft = 10; // Left margin in mm
                    const marginRight = 10; // Right margin in mm
                    
                    // Calculate available content area
                    const contentWidth = pageWidth - marginLeft - marginRight;
                    const contentHeight = pageHeight - marginTop - marginBottom;
                    
                    // Calculate image dimensions to fit content area
                    const imgWidth = contentWidth;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    console.log('PDF dimensions - Content Width:', contentWidth, 'mm, Content Height:', contentHeight, 'mm');
                    console.log('Image dimensions - Width:', imgWidth, 'mm, Height:', imgHeight, 'mm');
                    
                    const pdf = new jsPDF('l', 'mm', 'a4'); // 'l' for landscape orientation
                    let heightLeft = imgHeight;
                    let position = 0;
                    
                    // Add image to PDF with margins
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                    heightLeft -= contentHeight;
                    
                    // Add additional pages if content is longer than one page
                    let pageCount = 1;
                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pageCount++;
                        pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                        heightLeft -= contentHeight;
                    }
                    
                    console.log('PDF generated with ' + pageCount + ' pages');
                    
                    // Save the PDF with custom filename for Unit & Site Wise Report
                    const fileName = 'Unit_Site_Wise_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                    pdf.save(fileName);
                    
                    console.log('PDF downloaded successfully');
                    
                    // Show success message
                    alert('PDF downloaded successfully! (' + pageCount + ' pages)');
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    if (pdfBtn) {
                        pdfBtn.innerHTML = originalContent;
                        pdfBtn.disabled = false;
                        pdfBtn.style.opacity = '1';
                    }
                    
                }).catch(error => {
                    console.error('Error generating canvas:', error);
                    alert('Error generating PDF: ' + error.message);
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    if (pdfBtn) {
                        pdfBtn.innerHTML = originalContent;
                        pdfBtn.disabled = false;
                        pdfBtn.style.opacity = '1';
                    }
                });
                
            } catch (error) {
                console.error('Error in generateHTMLToPDF:', error);
                alert('Error generating PDF: ' + error.message);
                
                // Restore button state
                const pdfBtn = document.getElementById('pdf-btn');
                if (pdfBtn) {
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                }
            }
        }
    `;
    printWindow.document.head.appendChild(script);
}

// Function to fetch unit, site, and department wise pending demands
function fetchUnitSiteDepartmentWisePendingDemands(page = 1) {
    const unitId = document.getElementById('unitSelect5').value;
    const siteId = document.getElementById('siteSelect5').value;
    const departmentId = document.getElementById('departmentSelect5').value;

    if (!unitId || !siteId || !departmentId) {
        showToast('Please select Unit, Site, and Department filters', 'error');
        return;
    }

    // Show loading state
    showUnitSiteDepartmentWiseLoadingState();

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/unit-site-department-wise-pending-demands?page=${page}&limit=10&company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (siteId) {
        apiUrl += `&site_id=${encodeURIComponent(siteId)}`;
    }
    if (departmentId) {
        apiUrl += `&department_id=${encodeURIComponent(departmentId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUnitSiteDepartmentWisePendingDemands(data.data, data.pagination);
            } else {
                showToast(data.message || 'Error fetching unit, site, and department wise pending demands', 'error');
                showUnitSiteDepartmentWiseEmptyState();
            }
        })
        .catch(error => {
            console.error('Error fetching unit, site, and department wise pending demands:', error);
            showToast('Error fetching unit, site, and department wise pending demands', 'error');
            showUnitSiteDepartmentWiseEmptyState();
        });
}

// Function to print unit, site, and department wise pending demands
function printUnitSiteDepartmentWisePendingDemands() {
    const unitId = document.getElementById('unitSelect5').value;
    const siteId = document.getElementById('siteSelect5').value;
    const departmentId = document.getElementById('departmentSelect5').value;

    if (!unitId || !siteId || !departmentId) {
        showToast('Please select Unit, Site, and Department filters first', 'error');
        return;
    }

    // Get the print button and show loading state
    const printBtn = document.getElementById('btnPrintReport5');
    const originalContent = printBtn.innerHTML;
    
    printBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating...';
    printBtn.disabled = true;
    printBtn.style.opacity = '0.7';

    // Build API URL
    let apiUrl = `../api/pending-demand-reports/print/unit-site-department-wise-pending-demands?company_id=1`;
    if (unitId) {
        apiUrl += `&unit_id=${encodeURIComponent(unitId)}`;
    }
    if (siteId) {
        apiUrl += `&site_id=${encodeURIComponent(siteId)}`;
    }
    if (departmentId) {
        apiUrl += `&department_id=${encodeURIComponent(departmentId)}`;
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(`Generated print data for ${data.total_demands} demands`, 'success');
                
                // Open print window with action buttons
                openUnitSiteDepartmentWisePrintWindow(data.html, data.total_demands);

                // Restore button state
                printBtn.innerHTML = originalContent;
                printBtn.disabled = false;
                printBtn.style.opacity = '1';
            } else {
                showToast(data.message || 'Error generating print data', 'error');
                
                // Restore button state
                printBtn.innerHTML = originalContent;
                printBtn.disabled = false;
                printBtn.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error generating print data:', error);
            showToast('Error generating print data', 'error');
            
            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';
        });
}

// Function to display unit, site, and department wise pending demands
function displayUnitSiteDepartmentWisePendingDemands(demands, pagination) {
    console.log('displayUnitSiteDepartmentWisePendingDemands called with:', {demands, pagination});
    const tableBody = document.getElementById('unitSiteDepartmentWiseDemandsTableBody');
    const table = document.getElementById('unitSiteDepartmentWiseDemandsTable');

    if (!demands || demands.length === 0) {
        showUnitSiteDepartmentWiseEmptyState();
        return;
    }

    // Hide loading and empty states
    hideUnitSiteDepartmentWiseLoadingState();
    hideUnitSiteDepartmentWiseEmptyState();

    // Show table and pagination
    if (table) {
        table.classList.remove('hidden');
    }
    showUnitSiteDepartmentWisePagination(pagination);

    // Clear existing content
    if (tableBody) {
        tableBody.innerHTML = '';

        // Group demands by Demand No
        const groupedByDemand = {};
        demands.forEach(demand => {
            const key = demand.demand_number || 'N/A';
            if (!groupedByDemand[key]) {
                groupedByDemand[key] = {
                    demand_number: demand.demand_number,
                    demand_date: demand.demand_date,
                    demands: []
                };
            }
            groupedByDemand[key].demands.push(demand);
        });

        // Display grouped data
        Object.values(groupedByDemand).forEach(group => {
            // Add demand header row (only once per group)
            const demandHeaderRow = document.createElement('tr');
            demandHeaderRow.className = 'bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-700';
            demandHeaderRow.innerHTML = `
                <td colspan="9" class="px-[20px] py-[15px] font-semibold text-blue-800 dark:text-blue-200 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            ${formatDate(group.demand_date)} | DM# ${group.demand_number || 'N/A'}
                        </div>
                    </div>
                </td>
            `;
            tableBody.appendChild(demandHeaderRow);

            // Add all demands within this demand number
            group.demands.forEach(demand => {
            // Add items for this demand
            if (demand.items && demand.items.length > 0) {
                let demandTotal = 0;

                demand.items.forEach((item, index) => {
                    const itemRow = document.createElement('tr');
                    itemRow.className = `border-b border-gray-200 dark:border-[#172036] ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''}`;
                    itemRow.innerHTML = `
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_code || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_name || 'N/A'}<br />${item.item_description_detail || item.item_description || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.last_rate ? parseFloat(item.last_rate) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.quotation_rate ? parseFloat(item.quotation_rate) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.total_amount ? parseFloat(item.total_amount) : '-'}</td>
                        <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${demand.demanding_person_name || 'N/A'}</td>
                        <td class="px-[5px] py-[5px] text-sm">
                            <span class="status-badge ${getPriorityClass(item.priority)}">${item.priority}</span>
                        </td>
                        <td class="px-[5px] py-[5px] text-sm">
                            <span class="status-badge ${getStatusClass(item.item_status)}">${item.item_status || 'Pending'}</span>
                        </td>
                    `;
                    tableBody.appendChild(itemRow);

                    // Add to demand total
                    if (item.total_amount) {
                        demandTotal += parseFloat(item.total_amount);
                    }
                });

                // Add total row for this demand
                const totalRow = document.createElement('tr');
                totalRow.className = 'bg-green-50 dark:bg-green-900/20 border-t-2 border-green-200 dark:border-green-700 font-semibold';
                totalRow.innerHTML = `
                    <td colspan="5" class="px-[20px] py-[8px] text-sm text-green-800 dark:text-green-200 text-right font-bold">
                        Demand Total Amount:
                    </td>
                    <td class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200 font-bold">
                        ${demandTotal > 0 ? parseFloat(demandTotal) : '-'}
                    </td>
                    <td colspan="3" class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200">
                        <!-- Empty cells for remaining columns -->
                    </td>
                `;
                tableBody.appendChild(totalRow);
                } else {
                    // No items for this demand
                    const noItemsRow = document.createElement('tr');
                    noItemsRow.innerHTML = `
                        <td colspan="9" class="px-[5px] py-[5px] text-sm text-gray-500 dark:text-gray-400 text-center italic">No items found for this demand</td>
                    `;
                    tableBody.appendChild(noItemsRow);
                }
            });
        });
    }
}

// Function to show loading state for unit, site, and department wise report
function showUnitSiteDepartmentWiseLoadingState() {
    const table = document.getElementById('unitSiteDepartmentWiseDemandsTable');
    const emptyState = document.getElementById('unitSiteDepartmentWiseEmptyState');
    const pagination = document.getElementById('unitSiteDepartmentWisePagination');

    // Hide table, empty state, and pagination
    if (table) {
        table.classList.add('hidden');
    }
    if (emptyState) {
        emptyState.classList.add('hidden');
    }
    if (pagination) {
        pagination.classList.add('hidden');
    }

    // Create or show loading state
    let loadingState = document.getElementById('unitSiteDepartmentWiseLoadingState');
    if (!loadingState) {
        loadingState = document.createElement('div');
        loadingState.id = 'unitSiteDepartmentWiseLoadingState';
        loadingState.className = 'flex items-center justify-center py-8';
        loadingState.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-500"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Loading unit, site, and department wise demands...</span>
            </div>
        `;
        
        const tableContainer = document.querySelector('#report-unit-site-department-wise .table-responsive');
        if (tableContainer) {
            tableContainer.appendChild(loadingState);
        }
    } else {
        loadingState.classList.remove('hidden');
    }
}

// Function to hide loading state for unit, site, and department wise report
function hideUnitSiteDepartmentWiseLoadingState() {
    const loadingState = document.getElementById('unitSiteDepartmentWiseLoadingState');
    const pagination = document.getElementById('unitSiteDepartmentWisePagination');
    
    if (loadingState) {
        loadingState.classList.add('hidden');
    }
    if (pagination) {
        pagination.classList.remove('hidden');
        pagination.style.display = 'flex';
    }
}

// Function to show empty state for unit, site, and department wise report
function showUnitSiteDepartmentWiseEmptyState() {
    const table = document.getElementById('unitSiteDepartmentWiseDemandsTable');
    const loadingState = document.getElementById('unitSiteDepartmentWiseLoadingState');
    const pagination = document.getElementById('unitSiteDepartmentWisePagination');

    // Hide table, loading state, and pagination
    if (table) {
        table.classList.add('hidden');
    }
    if (loadingState) {
        loadingState.classList.add('hidden');
    }
    if (pagination) {
        pagination.classList.add('hidden');
    }

    // Create or show empty state
    let emptyState = document.getElementById('unitSiteDepartmentWiseEmptyState');
    if (!emptyState) {
        emptyState = document.createElement('div');
        emptyState.id = 'unitSiteDepartmentWiseEmptyState';
        emptyState.className = 'flex flex-col items-center justify-center py-12 text-center';
        emptyState.innerHTML = `
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <i class="material-symbols-outlined text-2xl text-gray-400">inbox</i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Demands Found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">No unit, site, and department wise demands found for the selected filters.</p>
        `;
        
        const tableContainer = document.querySelector('#report-unit-site-department-wise .table-responsive');
        if (tableContainer) {
            tableContainer.appendChild(emptyState);
        }
    } else {
        emptyState.classList.remove('hidden');
    }
}

// Function to hide empty state for unit, site, and department wise report
function hideUnitSiteDepartmentWiseEmptyState() {
    const emptyState = document.getElementById('unitSiteDepartmentWiseEmptyState');
    const pagination = document.getElementById('unitSiteDepartmentWisePagination');
    
    if (emptyState) {
        emptyState.classList.add('hidden');
    }
    if (pagination) {
        pagination.classList.remove('hidden');
        pagination.style.display = 'flex';
    }
}

// Function to show pagination for unit, site, and department wise report
function showUnitSiteDepartmentWisePagination(pagination) {
    const container = document.getElementById('unitSiteDepartmentWisePagination');
    const info = document.getElementById('unitSiteDepartmentWisePaginationInfo');
    const buttons = document.getElementById('unitSiteDepartmentWisePaginationButtons');

    if (!pagination) {
        container.style.display = 'none';
        return;
    }

    // Update pagination info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    info.textContent = `Showing ${start}-${end} of ${pagination.total_records} results`;

    // Generate pagination buttons
    buttons.innerHTML = '';

    // Previous button
    if (pagination.has_prev) {
        const prevBtn = document.createElement('li');
        prevBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-gray-700" 
                    onclick="fetchUnitSiteDepartmentWisePendingDemands(${pagination.current_page - 1})">
                <i class="material-symbols-outlined text-sm">chevron_left</i>
            </button>
        `;
        buttons.appendChild(prevBtn);
    }

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('li');
        pageBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${i === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    onclick="fetchUnitSiteDepartmentWisePendingDemands(${i})">
                ${i}
            </button>
        `;
        buttons.appendChild(pageBtn);
    }

    // Next button
    if (pagination.has_next) {
        const nextBtn = document.createElement('li');
        nextBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-gray-700" 
                    onclick="fetchUnitSiteDepartmentWisePendingDemands(${pagination.current_page + 1})">
                <i class="material-symbols-outlined text-sm">chevron_right</i>
            </button>
        `;
        buttons.appendChild(nextBtn);
    }

    // Show pagination container
    container.style.display = 'flex';
}

// Function to open print window for unit, site, and department wise report
function openUnitSiteDepartmentWisePrintWindow(html, recordCount) {
    try {
        console.log('Opening unit, site, and department wise print window with', recordCount, 'records');
        
        // Open new window
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        
        if (!printWindow) {
            showToast('Please allow popups for this site to open the print window', 'error');
            return;
        }

        // Write HTML content
        printWindow.document.write(html);
        printWindow.document.close();

        // Add PDF generation functions
        addUnitSiteDepartmentWisePDFFunctionsToWindow(printWindow);

        // Add action buttons after the window loads
        printWindow.onload = function() {
            addUnitSiteDepartmentWiseActionButtonsToWindow(printWindow, recordCount);
        };

        console.log('Unit, site, and department wise print window opened successfully with action buttons');

    } catch (error) {
        console.error('Error opening unit, site, and department wise print window:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// Function to add PDF generation functions to the print window for unit, site, and department wise report
function addUnitSiteDepartmentWisePDFFunctionsToWindow(printWindow) {
    const script = printWindow.document.createElement('script');
    script.textContent = `
        function printReport() {
            console.log('Print button clicked');
            window.print();
        }
        
        function downloadAsPDF() {
            console.log('Download as PDF button clicked');
            
            // Show loading state
            const pdfBtn = document.getElementById('pdf-btn');
            const originalContent = pdfBtn.innerHTML;
            pdfBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating PDF...';
            pdfBtn.disabled = true;
            pdfBtn.style.opacity = '0.7';
            
            // Generate PDF using HTML-to-PDF conversion
            try {
                // Load required libraries dynamically
                loadLibrariesForPDF().then(() => {
                    generateHTMLToPDF(originalContent);
                }).catch(error => {
                    console.error('Error loading libraries:', error);
                    alert('Error loading PDF libraries: ' + error.message);
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF: ' + error.message);
                pdfBtn.innerHTML = originalContent;
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
        
        function loadLibrariesForPDF() {
            return new Promise((resolve, reject) => {
                // Check if libraries are already loaded
                if (window.html2canvas && window.jspdf) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                const totalLibraries = 2;
                
                function checkComplete() {
                    loadedCount++;
                    if (loadedCount === totalLibraries) {
                        resolve();
                    }
                }
                
                // Load html2canvas
                if (!window.html2canvas) {
                    const html2canvasScript = document.createElement('script');
                    html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                    html2canvasScript.onload = checkComplete;
                    html2canvasScript.onerror = () => reject(new Error('Failed to load html2canvas'));
                    document.head.appendChild(html2canvasScript);
                } else {
                    checkComplete();
                }
                
                // Load jsPDF
                if (!window.jspdf) {
                    const jsPDFScript = document.createElement('script');
                    jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                    jsPDFScript.onload = checkComplete;
                    jsPDFScript.onerror = () => reject(new Error('Failed to load jsPDF'));
                    document.head.appendChild(jsPDFScript);
                } else {
                    checkComplete();
                }
            });
        }
        
        function generateHTMLToPDF(originalContent) {
            try {
                console.log('Starting HTML to PDF conversion');
                
                // Get the report content (excluding action buttons)
                const reportContent = document.querySelector('.print-header') || document.body;
                
                if (!reportContent) {
                    throw new Error('Could not find report content to convert');
                }
                
                // Temporarily remove action buttons from DOM for clean PDF
                const actionButtons = document.getElementById('action-buttons');
                let actionButtonsParent = null;
                let actionButtonsNextSibling = null;
                
                if (actionButtons) {
                    actionButtonsParent = actionButtons.parentNode;
                    actionButtonsNextSibling = actionButtons.nextSibling;
                    actionButtonsParent.removeChild(actionButtons);
                }
                
                // Add comprehensive CSS for PDF generation
                const pdfStyles = document.createElement('style');
                pdfStyles.textContent = 
                    '#action-buttons { display: none !important; visibility: hidden !important; }' +
                    '@media print {' +
                        'body { margin: 0 !important; padding: 15px 20px !important; font-size: 11px !important; }' +
                        '.print-header { margin-top: 0 !important; page-break-after: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-group { page-break-inside: avoid !important; break-inside: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-header { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table { page-break-inside: auto !important; margin: 0 !important; }' +
                        '.items-table tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        '.demand-header-row { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table tbody tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        'table { border-collapse: collapse !important; }' +
                        'td, th { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                    '}';
                document.head.appendChild(pdfStyles);
                
                // Configure html2canvas options for better quality and complete capture
                const options = {
                    scale: 2, // Higher resolution for better quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: window.innerWidth,
                    windowHeight: window.innerHeight,
                    logging: false,
                    removeContainer: true
                };
                
                console.log('Converting HTML to canvas with dimensions:', {
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight
                });
                
                html2canvas(reportContent, options).then(canvas => {
                    console.log('Canvas created with dimensions:', canvas.width, 'x', canvas.height);
                    
                    // Create PDF with proper dimensions and margins (Landscape A4)
                    const { jsPDF } = window.jspdf;
                    const pageWidth = 297; // A4 landscape width in mm
                    const pageHeight = 210; // A4 landscape height in mm
                    const marginTop = 15; // Top margin in mm
                    const marginBottom = 15; // Bottom margin in mm
                    const marginLeft = 10; // Left margin in mm
                    const marginRight = 10; // Right margin in mm
                    
                    // Calculate available content area
                    const contentWidth = pageWidth - marginLeft - marginRight;
                    const contentHeight = pageHeight - marginTop - marginBottom;
                    
                    // Calculate image dimensions to fit content area
                    const imgWidth = contentWidth;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    console.log('PDF dimensions - Content Width:', contentWidth, 'mm, Content Height:', contentHeight, 'mm');
                    console.log('Image dimensions - Width:', imgWidth, 'mm, Height:', imgHeight, 'mm');
                    
                    const pdf = new jsPDF('l', 'mm', 'a4'); // 'l' for landscape orientation
                    let heightLeft = imgHeight;
                    let position = 0;
                    
                    // Add image to PDF with margins
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                    heightLeft -= contentHeight;
                    
                    // Add additional pages if content is longer than one page
                    let pageCount = 1;
                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pageCount++;
                        pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                        heightLeft -= contentHeight;
                    }
                    
                    console.log('PDF generated with ' + pageCount + ' pages');
                    
                    // Save the PDF with custom filename for Unit & Site & Department Wise Report
                    const fileName = 'Unit_Site_Department_Wise_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                    pdf.save(fileName);
                    
                    console.log('PDF downloaded successfully');
                    
                    // Show success message
                    alert('PDF downloaded successfully! (' + pageCount + ' pages)');
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    if (pdfBtn) {
                        pdfBtn.innerHTML = originalContent;
                        pdfBtn.disabled = false;
                        pdfBtn.style.opacity = '1';
                    }
                    
                }).catch(error => {
                    console.error('Error generating canvas:', error);
                    alert('Error generating PDF: ' + error.message);
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    if (pdfBtn) {
                        pdfBtn.innerHTML = originalContent;
                        pdfBtn.disabled = false;
                        pdfBtn.style.opacity = '1';
                    }
                });
                
            } catch (error) {
                console.error('Error in generateHTMLToPDF:', error);
                alert('Error generating PDF: ' + error.message);
                
                // Restore button state
                const pdfBtn = document.getElementById('pdf-btn');
                if (pdfBtn) {
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                }
            }
        }
    `;
    printWindow.document.head.appendChild(script);
}

// Function to add action buttons to the print window for unit, site, and department wise report
function addUnitSiteDepartmentWiseActionButtonsToWindow(printWindow, recordCount) {
    try {
        console.log('Adding print and PDF buttons for unit, site, and department wise report');

        // First, add CSS to exclude buttons from print and position them properly
        const style = printWindow.document.createElement('style');
        style.textContent = `
            #action-buttons {
                position: fixed !important;
                top: 10px !important;
                right: 10px !important;
                z-index: 9999 !important;
                display: flex !important;
                gap: 8px !important;
                background: rgba(255, 255, 255, 0.95) !important;
                padding: 8px !important;
                border-radius: 6px !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
                backdrop-filter: blur(5px) !important;
            }
            
            @media print {
                #action-buttons {
                    display: none !important;
                }
            }
            
            /* Ensure report content starts below buttons */
            .print-header {
                margin-top: 60px !important;
            }
        `;
        printWindow.document.head.appendChild(style);

        // Create small buttons container with Print and PDF buttons
        const actionButtonsDiv = printWindow.document.createElement('div');
        actionButtonsDiv.id = 'action-buttons';

        actionButtonsDiv.innerHTML = `
            <button id="print-btn" onclick="printReport()" style="
                background: #2563eb;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                transition: background-color 0.2s;
            " onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <span style="font-size: 16px;">🖨️</span>
                Print
            </button>
            <button id="pdf-btn" onclick="downloadAsPDF()" style="
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                transition: background-color 0.2s;
            " onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <span style="font-size: 16px;">📄</span>
                PDF
            </button>
        `;

        printWindow.document.body.appendChild(actionButtonsDiv);
        console.log('Action buttons added successfully for unit, site, and department wise report');

    } catch (error) {
        console.error('Error adding action buttons:', error);
    }
}

// Function to fetch pending demands
function fetchPendingDemands(page = 1) {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (!fromDate || !toDate) {
        showToast('Please select both from and to dates', 'error');
        return;
    }

    // Show loading state
    showLoadingState();

    // Build API URL
    const apiUrl = `../api/pending-demand-reports/pending-demands?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&page=${page}&limit=10&company_id=1`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPendingDemands(data.data, data.pagination);
            } else {
                showToast(data.message || 'Error fetching pending demands', 'error');
                showEmptyState();
            }
        })
        .catch(error => {
            console.error('Error fetching pending demands:', error);
            showToast('Error fetching pending demands', 'error');
            showEmptyState();
        });
}

// Function to print item-wise pending demands (same as Pending Demands List)
function printItemWisePendingDemands() {
    const itemId = document.getElementById('itemSelect').value;

    if (!itemId) {
        showToast('Please select an item first', 'error');
        return;
    }

    // Get the print button and show loading state
    const printBtn = document.getElementById('btnPrintReport2');
    const originalContent = printBtn.innerHTML;
    
    printBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating...';
    printBtn.disabled = true;
    printBtn.style.opacity = '0.7';

    // Show loading toast
    // showToast('Generating print data...', 'info');

    // Build API URL
    const apiUrl = `../api/pending-demand-reports/print/item-wise-pending-demands?item_id=${encodeURIComponent(itemId)}&company_id=1`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(`Generated print data for ${data.total_demands} demands`, 'success');
                
                // Open print window with action buttons (same as Pending Demands List)
                openItemWisePrintWindow(data.html, data.total_demands);

                // Restore button state
                printBtn.innerHTML = originalContent;
                printBtn.disabled = false;
                printBtn.style.opacity = '1';
            } else {
                throw new Error(data.message || 'Error generating print data');
            }
        })
        .catch(error => {
            console.error('Error generating print data:', error);
            showToast('Error generating print data: ' + error.message, 'error');
            
            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';
        });
}

// Function to open item-wise print window with action buttons (same as Pending Demands List)
function openItemWisePrintWindow(html, recordCount) {
    try {
        console.log(`Opening item-wise print window for ${recordCount} records`);

        // Create a new window for printing (landscape orientation)
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            showToast('Error: Could not open print window. Please allow popups for this site.', 'error');
            return;
        }

        // Write the HTML directly (buttons will be added via DOM manipulation)
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();

        // Add landscape orientation CSS to the print window
        const landscapeCSS = `
            <style>
                @media print {
                    @page {
                        size: A4 landscape;
                        margin: 0.5in;
                    }
                }
            </style>
        `;
        
        // Insert the CSS into the head of the print window
        const head = printWindow.document.head || printWindow.document.getElementsByTagName('head')[0];
        head.insertAdjacentHTML('beforeend', landscapeCSS);

        // Add PDF functions to the print window
        addPDFFunctionsToWindow(printWindow);

        // Add action buttons after the window loads
        printWindow.onload = function() {
            addActionButtonsToWindow(printWindow, recordCount);
        };

        console.log('Item-wise print window opened successfully with action buttons');

    } catch (error) {
        console.error('Error opening item-wise print window:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// Function to print all pending demands with optimized approach
function printAllPendingDemands() {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (!fromDate || !toDate) {
        showToast('Please select both from and to dates', 'error');
        return;
    }

    // Get the print button and show loading state
    const printBtn = document.getElementById('btnPrintReport');
    const originalContent = printBtn.innerHTML;

    // Show loading state on button
    printBtn.innerHTML = `
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
        <span>Loading...</span>
    `;
    printBtn.disabled = true;
    printBtn.style.opacity = '0.7';

    // Show loading toast
    // showToast('Preparing data for printing...', 'info');

    // Try direct print generation first, fallback to chunked if needed
    generatePrintDataDirect(fromDate, toDate, printBtn, originalContent);
}

// Function to load all demands in chunks
async function loadAllDemandsForPrint(fromDate, toDate, printBtn, originalContent) {
    try {
        let allDemands = [];
        let currentPage = 1;
        let totalPages = 1;
        let totalDemands = 0;
        const chunkSize = 50; // Demands per chunk

        // First, get the total count
        const firstChunkUrl = `../api/pending-demand-reports/print-all-demands-chunked?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&company_id=1&page=1&limit=${chunkSize}`;

        console.log('Fetching first chunk:', firstChunkUrl);

        const firstResponse = await fetch(firstChunkUrl);
        const firstData = await firstResponse.json();

        console.log('First chunk response:', firstData);

        if (!firstData.success) {
            throw new Error(firstData.message || 'Error fetching first chunk');
        }

        totalPages = firstData.pagination.total_pages;
        totalDemands = firstData.pagination.total_demands;
        allDemands = [...firstData.data];

        // Show progress modal
        showProgressModal(totalDemands, totalPages);

        // Load remaining chunks
        for (let page = 2; page <= totalPages; page++) {
            // Update progress
            updateProgress(page - 1, totalPages, allDemands.length, totalDemands);

            const chunkUrl = `../api/pending-demand-reports/print-all-demands-chunked?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&company_id=1&page=${page}&limit=${chunkSize}`;

            console.log(`Fetching chunk ${page}:`, chunkUrl);

            const response = await fetch(chunkUrl);
            const data = await response.json();

            console.log(`Chunk ${page} response:`, data);

            if (data.success) {
                allDemands = [...allDemands, ...data.data];
                console.log(`Chunk ${page} loaded successfully. Total demands so far: ${allDemands.length}`);
            } else {
                console.warn(`Error loading chunk ${page}:`, data.message);
            }

            // Small delay to prevent overwhelming the server
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        // Final progress update
        updateProgress(totalPages, totalPages, allDemands.length, totalDemands);

        // Hide progress modal and show print modal
        setTimeout(() => {
            hideProgressModal();
            showPrintModal(allDemands, fromDate, toDate);

            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';

            // Show success toast
            showToast(`Loaded ${allDemands.length} demands for printing`, 'success');
        }, 500);

    } catch (error) {
        console.error('Error loading demands for print:', error);
        showToast('Error loading demands for print: ' + error.message, 'error');

        // Restore button state
        printBtn.innerHTML = originalContent;
        printBtn.disabled = false;
        printBtn.style.opacity = '1';

        // Hide progress modal if it exists
        hideProgressModal();
    }
}

// Function to show progress modal
function showProgressModal(totalDemands, totalPages) {
    // Remove existing progress modal if any
    const existingModal = document.getElementById('progressModal');
    if (existingModal) {
        existingModal.remove();
    }

    const progressModal = document.createElement('div');
    progressModal.id = 'progressModal';
    progressModal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full">
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="material-symbols-outlined text-2xl text-blue-600 dark:text-blue-400 animate-spin">sync</i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Loading Data for Printing</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Please wait while we prepare all demands...</p>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-4">
                            <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        
                        <!-- Progress Text -->
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <div id="progressText">Loading chunk 1 of ${totalPages}...</div>
                            <div id="progressDetails" class="mt-1">0 of ${totalDemands} demands loaded</div>
                        </div>
                        
                        <!-- Cancel Button -->
                        <button onclick="cancelLoading()" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(progressModal);
}

// Function to update progress
function updateProgress(currentPage, totalPages, loadedDemands, totalDemands) {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressDetails = document.getElementById('progressDetails');

    if (progressBar && progressText && progressDetails) {
        const percentage = Math.round((currentPage / totalPages) * 100);
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `Loading chunk ${currentPage} of ${totalPages}...`;
        progressDetails.textContent = `${loadedDemands} of ${totalDemands} demands loaded`;
    }
}

// Function to hide progress modal
function hideProgressModal() {
    const progressModal = document.getElementById('progressModal');
    if (progressModal) {
        progressModal.remove();
    }
}

// Function to cancel loading
function cancelLoading() {
    hideProgressModal();

    // Restore print button state
    const printBtn = document.getElementById('btnPrintReport');
    if (printBtn) {
        printBtn.innerHTML = `
            <i class="material-symbols-outlined">print</i>
            <span>Print All</span>
        `;
        printBtn.disabled = false;
        printBtn.style.opacity = '1';
    }

    showToast('Loading cancelled', 'info');
}

// Function to show print modal with all demands
function showPrintModal(demands, fromDate, toDate) {
    console.log('showPrintModal called with:', {
        demands: demands.length,
        fromDate,
        toDate
    });

    // Remove existing print modal if any
    const existingModal = document.getElementById('printModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create print modal
    const printModal = document.createElement('div');
    printModal.id = 'printModal';
    printModal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="z-index: 9999;">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-7xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Print All Pending Demands (${demands.length} demands)
                    </h3>
                    <div class="flex gap-2">
                        <button id="printBtn" onclick="printDocument()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50" disabled>
                            <i class="material-symbols-outlined mr-2">print</i>Print
                        </button>
                        <button onclick="closePrintModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            <i class="material-symbols-outlined mr-2">close</i>Close
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <div id="printContent">
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-600 dark:text-gray-400">Generating print content...</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Processing ${demands.length} demands</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(printModal);
    console.log('Print modal added to DOM');

    // Generate content asynchronously to avoid blocking UI
    setTimeout(() => {
        console.log('Starting content generation...');
        generatePrintContentAsync(demands, fromDate, toDate);
    }, 100);
}

// Function to generate print content asynchronously
function generatePrintContentAsync(demands, fromDate, toDate) {
    console.log('generatePrintContentAsync called with:', {
        demands: demands.length,
        fromDate,
        toDate
    });

    const printContent = document.getElementById('printContent');
    const printBtn = document.getElementById('printBtn');

    if (!printContent) {
        console.error('printContent element not found!');
        return;
    }

    if (!printBtn) {
        console.error('printBtn element not found!');
        return;
    }

    try {
        // Generate content in chunks to avoid blocking
        const chunkSize = 20; // Process 20 demands at a time
        let currentChunk = 0;
        let content = `
            <div class="print-header" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;">
                <h1 style="margin: 0; color: #333; font-size: 24px;">Pending Demands Report</h1>
                <p style="margin: 5px 0; color: #666;">From: ${formatDate(fromDate)} | To: ${formatDate(toDate)}</p>
                <p style="margin: 5px 0; color: #666;">Total Demands: ${demands.length}</p>
                <p style="margin: 5px 0; color: #666;">Generated on: ${new Date().toLocaleDateString()}</p>
            </div>
        `;

        function processNextChunk() {
            const start = currentChunk * chunkSize;
            const end = Math.min(start + chunkSize, demands.length);
            const chunk = demands.slice(start, end);

            console.log(`Processing chunk ${currentChunk + 1}: demands ${start + 1}-${end}`);

            // Process this chunk
            chunk.forEach((demand, index) => {
                const globalIndex = start + index + 1;
                content += generateDemandPrintContent(demand, globalIndex);
            });

            // Update progress
            const progress = Math.round((end / demands.length) * 100);
            printContent.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400">Generating print content...</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Processing ${end} of ${demands.length} demands (${progress}%)</p>
                        <div class="w-64 bg-gray-200 rounded-full h-2 mt-4">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
                        </div>
                    </div>
                </div>
            `;

            currentChunk++;

            if (end < demands.length) {
                // Process next chunk after a short delay
                setTimeout(processNextChunk, 30);
            } else {
                // All chunks processed, show final content
                console.log('All chunks processed, showing final content');
                printContent.innerHTML = content;
                printBtn.disabled = false;
                printBtn.style.opacity = '1';

                // Show success message
                showToast('Print content ready!', 'success');
                console.log('Print content generation completed');
            }
        }

        // Start processing
        processNextChunk();

    } catch (error) {
        console.error('Error generating print content:', error);
        if (printContent) {
            printContent.innerHTML = `
            <div class="text-center py-8">
                <div class="text-red-600 mb-4">
                    <i class="material-symbols-outlined text-4xl">error</i>
                </div>
                    <p class="text-gray-600 dark:text-gray-400">Error generating print content: ${error.message}</p>
                <button onclick="closePrintModal()" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Close
                </button>
            </div>
        `;
        }
    }
}

// Function to close print modal
function closePrintModal() {
    const printModal = document.getElementById('printModal');
    if (printModal) {
        printModal.remove();
    }
}

// Function to print the document
function printDocument() {
    console.log('printDocument called');

    const printContent = document.getElementById('printContent');
    if (!printContent) {
        console.error('printContent element not found!');
        showToast('Error: Print content not found', 'error');
        return;
    }

    try {
        // Create a new window for printing (landscape orientation)
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            showToast('Error: Could not open print window. Please allow popups for this site.', 'error');
            return;
        }

        console.log('Print window opened, writing content...');

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Pending Demands Report</title>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.4; }
                    .print-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                    .print-header h1 { margin: 0; color: #333; font-size: 24px; }
                    .print-header p { margin: 5px 0; color: #666; }
                    .demand-group { margin-bottom: 30px; page-break-inside: avoid; }
                    .demand-header { background-color: #f0f8ff; padding: 15px; border-left: 4px solid #3b82f6; margin-bottom: 10px; }
                    .demand-title { font-weight: bold; font-size: 16px; color: #1e40af; margin-bottom: 5px; }
                    .demand-info { font-size: 14px; color: #666; }
                    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                    .items-table th { background-color: #f8f9fa; font-weight: bold; }
                    .priority-badge { padding: 2px 6px; border-radius: 12px; font-size: 10px; font-weight: bold; }
                    .priority-critical { background-color: #fecaca; color: #991b1b; }
                    .priority-high { background-color: #fed7aa; color: #9a3412; }
                    .priority-medium { background-color: #fef3c7; color: #92400e; }
                    .priority-low { background-color: #d1fae5; color: #065f46; }
                    .status-badge { padding: 2px 6px; border-radius: 12px; font-size: 10px; font-weight: bold; }
                    .status-pending { background-color: #fef3c7; color: #92400e; }
                    .status-active { background-color: #dcfce7; color: #166534; }
                    .status-completed { background-color: #dbeafe; color: #1e40af; }
                    @media print {
                        body { margin: 0; padding: 15px; }
                        .demand-group { page-break-inside: avoid; }
                        .print-header { page-break-after: avoid; }
                        .items-table { font-size: 10px; }
                        .items-table th, .items-table td { padding: 4px; }
                    }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();

        // Wait for content to load before printing
        printWindow.onload = function() {
            console.log('Print window content loaded, starting print...');
            setTimeout(() => {
                printWindow.print();
                // Close window after printing (optional)
                // printWindow.close();
            }, 500);
        };

        console.log('Print window setup completed');

    } catch (error) {
        console.error('Error in printDocument:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// Function to generate print content with chunking
function generatePrintContent(demands, fromDate, toDate) {
    const chunkSize = 20; // Process 20 demands at a time
    const chunks = [];

    for (let i = 0; i < demands.length; i += chunkSize) {
        chunks.push(demands.slice(i, i + chunkSize));
    }

    let content = `
        <div class="print-header">
            <h1>Pending Demands Report</h1>
            <p>From: ${formatDate(fromDate)} | To: ${formatDate(toDate)}</p>
            <p>Total Demands: ${demands.length}</p>
            <p>Generated on: ${new Date().toLocaleDateString()}</p>
        </div>
    `;

    // Process each chunk
    chunks.forEach((chunk, chunkIndex) => {
        content += `<div class="chunk-${chunkIndex}">`;

        chunk.forEach((demand, index) => {
            const globalIndex = (chunkIndex * chunkSize) + index + 1;
            content += generateDemandPrintContent(demand, globalIndex);
        });

        content += `</div>`;
    });

    return content;
}

// Function to generate individual demand print content
function generateDemandPrintContent(demand, index) {
    return `
        <div class="demand-group" style="margin-bottom: 30px; page-break-inside: avoid;">
            <div class="demand-header" style="background-color: #f0f8ff; padding: 15px; border-left: 4px solid #3b82f6; margin-bottom: 10px;">
                <div class="demand-title" style="font-weight: bold; font-size: 16px; color: #1e40af; margin-bottom: 5px;">Demand #${demand.demand_number}</div>
                <div class="demand-info" style="font-size: 14px; color: #666;">
                    ${formatDate(demand.demand_date)} | ${demand.unit_name || 'N/A'} | ${demand.site_name || 'N/A'} | ${demand.department_name || 'N/A'} | ${demand.demanding_person_name || 'N/A'}
                </div>
            </div>
            
            ${demand.items && demand.items.length > 0 ? `
                <table class="items-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 8%;">Code</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 20%;">Item Name</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 12%;">Description</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 8%;">Qty - UOM</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 8%;">Last Rate</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 8%;">Quotation Rate</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 8%;">Total Amount</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 12%;">Department</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 12%;">Demanding Person</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 6%;">Priority</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; background-color: #f8f9fa; font-weight: bold; width: 6%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${demand.items.map(item => `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.item_code || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.item_name || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.item_description_detail || item.item_description || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.last_rate ? parseFloat(item.last_rate).toFixed(2) : '-'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.quotation_rate ? parseFloat(item.quotation_rate).toFixed(2) : '-'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${item.total_amount ? parseFloat(item.total_amount).toFixed(2) : '-'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${demand.department_name || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">${demand.demanding_person_name || 'N/A'}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">
                                    <span class="priority-badge priority-${getPriorityClass(item.priority)}" style="padding: 2px 6px; border-radius: 12px; font-size: 10px; font-weight: bold;">${item.priority}</span>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">
                                    <span class="status-badge status-${getStatusClass(item.item_status)}" style="padding: 2px 6px; border-radius: 12px; font-size: 10px; font-weight: bold;">${item.item_status || 'Pending'}</span>
                                </td>
                            </tr>
                        `).join('')}
                        ${(() => {
                            let demandTotal = 0;
                            demand.items.forEach(item => {
                                if (item.total_amount) {
                                    demandTotal += parseFloat(item.total_amount);
                                }
                            });
                            return `
                                <tr style="background-color: #f0f9ff; border-top: 2px solid #0ea5e9; font-weight: bold;">
                                    <td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-size: 12px; color: #0c4a6e;">
                                        Demand Total:
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; color: #0c4a6e; font-weight: bold;">
                                        ${demandTotal > 0 ? parseFloat(demandTotal) : '-'}
                                    </td>
                                    <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px;">
                                        <!-- Empty cells for remaining columns -->
                                    </td>
                                </tr>
                            `;
})()
} <
/tbody> < /
table >
    ` : ` <
    div style = "text-align: center; padding: 20px; color: #666; font-style: italic;" >
    No items found
for this demand
    <
    /div>
            `}
        </div>
    `;
}

// Function to display unit and department wise pending demands in grouped format
function displayUnitDepartmentWisePendingDemands(demands, pagination) {
    const tableBody = document.getElementById('unitDeptWiseDemandsTableBody');
    const table = document.getElementById('unitDeptWiseDemandsTable');

    if (!demands || demands.length === 0) {
        showUnitDeptWiseEmptyState();
        return;
    }

    // Hide loading and empty states
    hideUnitDeptWiseLoadingState();
    hideUnitDeptWiseEmptyState();

    // Show table and pagination
    table.classList.remove('hidden');
    showUnitDeptWisePagination(pagination);

    // Clear existing content
    tableBody.innerHTML = '';

    // Group demands by Demand No
    const groupedByDemand = {};
    demands.forEach(demand => {
        const key = demand.demand_number || 'N/A';
        if (!groupedByDemand[key]) {
            groupedByDemand[key] = {
                demand_number: demand.demand_number,
                demand_date: demand.demand_date,
                demands: []
            };
        }
        groupedByDemand[key].demands.push(demand);
    });

    // Display grouped data
    Object.values(groupedByDemand).forEach(group => {
        // Add demand header row (only once per group)
        const demandHeaderRow = document.createElement('tr');
        demandHeaderRow.className = 'bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-700';
        demandHeaderRow.innerHTML = `
            <td colspan="9" class="px-[20px] py-[15px] font-semibold text-blue-800 dark:text-blue-200 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${formatDate(group.demand_date)} | DM# ${group.demand_number || 'N/A'}
                    </div>
                </div>
            </td>
        `;
        tableBody.appendChild(demandHeaderRow);

        // Add all demands within this demand number
        group.demands.forEach(demand => {
        // Add items for this demand
        if (demand.items && demand.items.length > 0) {
            let demandTotal = 0;

            demand.items.forEach((item, index) => {
                const itemRow = document.createElement('tr');
                itemRow.className = `border-b border-gray-200 dark:border-[#172036] ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''}`;
                itemRow.innerHTML = `
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_code || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_name || 'N/A'}<br />${item.item_description_detail || item.item_description || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.last_rate ? parseFloat(item.last_rate) : '-'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.quotation_rate ? parseFloat(item.quotation_rate) : '-'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.total_amount ? parseFloat(item.total_amount) : '-'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${demand.demanding_person_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getPriorityClass(item.priority)}">${item.priority}</span>
                    </td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getStatusClass(item.item_status)}">${item.item_status || 'Pending'}</span>
                    </td>
                `;
                tableBody.appendChild(itemRow);

                // Add to demand total
                if (item.total_amount) {
                    demandTotal += parseFloat(item.total_amount);
                }
            });

            // Add total row for this demand
            const totalRow = document.createElement('tr');
            totalRow.className = 'bg-green-50 dark:bg-green-900/20 border-t-2 border-green-200 dark:border-green-700 font-semibold';
            totalRow.innerHTML = `
                <td colspan="5" class="px-[20px] py-[8px] text-sm text-green-800 dark:text-green-200 text-right font-bold">
                    Demand Total Amount:
                </td>
                <td class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200 font-bold">
                    ${demandTotal > 0 ? parseFloat(demandTotal) : '-'}
                </td>
                <td colspan="3" class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200">
                    <!-- Empty cells for remaining columns -->
                </td>
            `;
            tableBody.appendChild(totalRow);
            } else {
                // No items for this demand
                const noItemsRow = document.createElement('tr');
                noItemsRow.className = 'border-b border-gray-200 dark:border-[#172036] bg-gray-50 dark:bg-gray-800/50';
                noItemsRow.innerHTML = `
                    <td colspan="9" class="px-[20px] py-[11px] text-sm text-gray-500 dark:text-gray-400 text-center italic">
                        No items found for this demand
                    </td>
                `;
                tableBody.appendChild(noItemsRow);
            }
        });
    });
}

// State management functions for Unit Department Wise report
function showUnitDeptWiseLoadingState() {
    document.getElementById('unitDeptWiseLoadingState').classList.remove('hidden');
    document.getElementById('unitDeptWiseDemandsTable').classList.add('hidden');
    document.getElementById('unitDeptWiseEmptyState').classList.add('hidden');
    document.getElementById('unitDeptWisePaginationContainer').classList.add('hidden');
}

function hideUnitDeptWiseLoadingState() {
    document.getElementById('unitDeptWiseLoadingState').classList.add('hidden');
}

function showUnitDeptWiseEmptyState() {
    document.getElementById('unitDeptWiseEmptyState').classList.remove('hidden');
    document.getElementById('unitDeptWiseDemandsTable').classList.add('hidden');
    document.getElementById('unitDeptWiseLoadingState').classList.add('hidden');
    document.getElementById('unitDeptWisePaginationContainer').classList.add('hidden');
}

function hideUnitDeptWiseEmptyState() {
    document.getElementById('unitDeptWiseEmptyState').classList.add('hidden');
}

function showUnitDeptWisePagination(pagination) {
    const container = document.getElementById('unitDeptWisePaginationContainer');
    const info = document.getElementById('unitDeptWisePaginationInfo');
    const buttons = document.getElementById('unitDeptWisePaginationButtons');

    if (!pagination) {
        container.classList.add('hidden');
        return;
    }

    // Update pagination info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    info.textContent = `Showing ${start}-${end} of ${pagination.total_records} results`;

    // Generate pagination buttons
    buttons.innerHTML = '';

    // Previous button
    const prevBtn = document.createElement('li');
    prevBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_prev ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_prev ? 'disabled' : ''} 
                onclick="fetchUnitDepartmentWisePendingDemands(${pagination.current_page - 1})">
            <i class="material-symbols-outlined text-sm">chevron_left</i>
        </button>
    `;
    buttons.appendChild(prevBtn);

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('li');
        pageBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${i === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    onclick="fetchUnitDepartmentWisePendingDemands(${i})">
                ${i}
            </button>
        `;
        buttons.appendChild(pageBtn);
    }

    // Next button
    const nextBtn = document.createElement('li');
    nextBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_next ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_next ? 'disabled' : ''} 
                onclick="fetchUnitDepartmentWisePendingDemands(${pagination.current_page + 1})">
            <i class="material-symbols-outlined text-sm">chevron_right</i>
        </button>
    `;
    buttons.appendChild(nextBtn);

    container.classList.remove('hidden');
}


// Function to display item-wise pending demands in grouped format
function maskRateValueForPM(value) {
    if (!window.hideRatesForPM) {
        return value;
    }
    return (value !== null && value !== undefined && value !== '-' && value !== '')
        ? '*******'
        : '-';
}

function displayItemWisePendingDemands(demands, pagination) {
    const tableBody = document.getElementById('itemWiseDemandsTableBody');
    const table = document.getElementById('itemWiseDemandsTable');

    if (!demands || demands.length === 0) {
        showItemWiseEmptyState();
        return;
    }

    // Hide loading and empty states
    hideItemWiseLoadingState();
    hideItemWiseEmptyState();

    // Show table and pagination
    table.classList.remove('hidden');
    showItemWisePagination(pagination);

    // Clear existing content
    tableBody.innerHTML = '';

    // Group demands and display
    demands.forEach(demand => {
        // Add demand header row
        const demandHeaderRow = document.createElement('tr');
        demandHeaderRow.className = 'bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-700';
        demandHeaderRow.innerHTML = `
            <td colspan="9" class="px-[20px] py-[15px] font-semibold text-blue-800 dark:text-blue-200 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${formatDate(demand.demand_date)} | DM# ${demand.demand_number} | ${demand.unit_name} | ${demand.site_name}
                    </div>
                </div>
            </td>
        `;
        tableBody.appendChild(demandHeaderRow);

        // Add items for this demand
        if (demand.items && demand.items.length > 0) {
            let demandTotal = 0;

            demand.items.forEach((item, index) => {
                const itemRow = document.createElement('tr');
                itemRow.className = `border-b border-gray-200 dark:border-[#172036] ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''}`;
                itemRow.innerHTML = `
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_code || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_name || 'N/A'}<br />${item.item_description_detail || item.item_description || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.last_rate ? parseFloat(item.last_rate) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.quotation_rate ? parseFloat(item.quotation_rate) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.total_amount ? parseFloat(item.total_amount) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${demand.department_name || 'N/A'}<br />${demand.demanding_person_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getPriorityClass(item.priority)}">${item.priority}</span>
                    </td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getStatusClass(item.item_status)}">${item.item_status || 'Pending'}</span>
                    </td>
                `;
                tableBody.appendChild(itemRow);

                // Add to total if amount exists
                if (!window.hideRatesForPM && item.total_amount) {
                    demandTotal += parseFloat(item.total_amount);
                }
            });

            // Add total row for this demand
            const totalRow = document.createElement('tr');
            totalRow.className = 'bg-green-50 dark:bg-green-900/20 border-t-2 border-green-200 dark:border-green-700 font-semibold';
            totalRow.innerHTML = `
                <td colspan="5" class="px-[20px] py-[8px] text-sm text-green-800 dark:text-green-200 text-right font-bold">
                    Demand Total Amount:
                </td>
                <td class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200 font-bold">
                    ${window.hideRatesForPM ? '*******' : (demandTotal > 0 ? parseFloat(demandTotal) : '-')}
                </td>
                <td colspan="3" class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200">
                    <!-- Empty cells for remaining columns -->
                </td>
            `;
            tableBody.appendChild(totalRow);
        } else {
            // No items for this demand
            const noItemsRow = document.createElement('tr');
            noItemsRow.className = 'border-b border-gray-200 dark:border-[#172036] bg-gray-50 dark:bg-gray-800/50';
            noItemsRow.innerHTML = `
                <td colspan="9" class="px-[20px] py-[11px] text-sm text-gray-500 dark:text-gray-400 text-center italic">
                    No items found for this demand
                </td>
            `;
            tableBody.appendChild(noItemsRow);
        }
    });
}

// Function to display pending demands in grouped format
function displayPendingDemands(demands, pagination) {
    const tableBody = document.getElementById('pendingDemandsTableBody');
    const table = document.getElementById('pendingDemandsTable');

    if (!demands || demands.length === 0) {
        showEmptyState();
        return;
    }

    // Hide loading and empty states
    hideLoadingState();
    hideEmptyState();

    // Show table and pagination
    table.classList.remove('hidden');
    showPagination(pagination);

    // Clear existing content
    tableBody.innerHTML = '';

    // Group demands and display
    demands.forEach(demand => {
        // Add demand header row
        const demandHeaderRow = document.createElement('tr');
        demandHeaderRow.className = 'bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-200 dark:border-blue-700';
        demandHeaderRow.innerHTML = `
            <td colspan="9" class="px-[20px] py-[15px] font-semibold text-blue-800 dark:text-blue-200 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${formatDate(demand.demand_date)} | DM# ${demand.demand_number} | ${demand.unit_name} | ${demand.site_name}
                    </div>
                </div>
            </td>
        `;
        tableBody.appendChild(demandHeaderRow);

        // Add items for this demand
        if (demand.items && demand.items.length > 0) {
            let demandTotal = 0;
            
            demand.items.forEach((item, index) => {
                const itemRow = document.createElement('tr');
                itemRow.className = `border-b border-gray-200 dark:border-[#172036] ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''}`;
                itemRow.innerHTML = `
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_code || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${item.item_name || 'N/A'}<br />${item.item_description_detail || item.item_description || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.last_rate ? parseFloat(item.last_rate) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${parseFloat(item.quantity) || '0'} - ${item.unit_type_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.quotation_rate ? parseFloat(item.quotation_rate) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${maskRateValueForPM(item.total_amount ? parseFloat(item.total_amount) : '-')}</td>
                    <td class="px-[5px] py-[5px] text-sm text-gray-900 dark:text-white">${demand.department_name || 'N/A'}<br />${demand.demanding_person_name || 'N/A'}</td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getPriorityClass(item.priority)}">${item.priority}</span>
                    </td>
                    <td class="px-[5px] py-[5px] text-sm">
                        <span class="status-badge ${getStatusClass(item.item_status)}">${item.item_status || 'Pending'}</span>
                    </td>
                `;
                tableBody.appendChild(itemRow);
                
                // Add to total if amount exists
                if (!window.hideRatesForPM && item.total_amount) {
                    demandTotal += parseFloat(item.total_amount);
                }
            });
            
            // Add total row for this demand
            const totalRow = document.createElement('tr');
            totalRow.className = 'bg-green-50 dark:bg-green-900/20 border-t-2 border-green-200 dark:border-green-700 font-semibold';
            totalRow.innerHTML = `
                <td colspan="5" class="px-[20px] py-[8px] text-sm text-green-800 dark:text-green-200 text-right font-bold">
                    Demand Total Amount:
                </td>
                <td class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200 font-bold">
                    ${demandTotal > 0 ? parseFloat(demandTotal) : '-'}
                </td>
                <td colspan="3" class="px-[5px] py-[8px] text-sm text-green-800 dark:text-green-200">
                    <!-- Empty cells for remaining columns -->
                </td>
            `;
            tableBody.appendChild(totalRow);
        } else {
            // No items for this demand
            const noItemsRow = document.createElement('tr');
            noItemsRow.className = 'border-b border-gray-200 dark:border-[#172036] bg-gray-50 dark:bg-gray-800/50';
            noItemsRow.innerHTML = `
                <td colspan="9" class="px-[20px] py-[11px] text-sm text-gray-500 dark:text-gray-400 text-center italic">
                    No items found for this demand
                </td>
            `;
            tableBody.appendChild(noItemsRow);
        }
    });
}

// Function to get priority CSS class
function getPriorityClass(priority) {
    switch (priority.toLowerCase()) {
        case 'critical':
            return 'critical';
        case 'high':
            return 'high';
        case 'medium':
            return 'medium';
        case 'low':
            return 'low';
        default:
            return 'low';
    }
}

// Function to get status CSS class
function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'delivered':
            return 'completed';
        case 'pending':
            return 'pending';
        case 'approved':
            return 'active';
        default:
            return 'pending';
    }
}

// Function to show loading state
function showLoadingState() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('pendingDemandsTable').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');
}

// Function to hide loading state
function hideLoadingState() {
    document.getElementById('loadingState').classList.add('hidden');
}

// Function to show empty state
function showEmptyState() {
    document.getElementById('emptyState').classList.remove('hidden');
    document.getElementById('pendingDemandsTable').classList.add('hidden');
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');
}

// Function to hide empty state
function hideEmptyState() {
    document.getElementById('emptyState').classList.add('hidden');
}

// Item-wise loading state functions
function showItemWiseLoadingState() {
    document.getElementById('itemWiseLoadingState').classList.remove('hidden');
    document.getElementById('itemWiseDemandsTable').classList.add('hidden');
    document.getElementById('itemWiseEmptyState').classList.add('hidden');
    document.getElementById('itemWisePaginationContainer').classList.add('hidden');
}

function hideItemWiseLoadingState() {
    document.getElementById('itemWiseLoadingState').classList.add('hidden');
}

function showItemWiseEmptyState() {
    document.getElementById('itemWiseEmptyState').classList.remove('hidden');
    document.getElementById('itemWiseDemandsTable').classList.add('hidden');
    document.getElementById('itemWiseLoadingState').classList.add('hidden');
    document.getElementById('itemWisePaginationContainer').classList.add('hidden');
}

function hideItemWiseEmptyState() {
    document.getElementById('itemWiseEmptyState').classList.add('hidden');
}

// Function to show item-wise pagination
function showItemWisePagination(pagination) {
    const container = document.getElementById('itemWisePaginationContainer');
    const info = document.getElementById('itemWisePaginationInfo');
    const buttons = document.getElementById('itemWisePaginationButtons');

    if (!pagination) {
        container.classList.add('hidden');
        return;
    }

    // Update pagination info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    info.textContent = `Showing ${start}-${end} of ${pagination.total_records} results`;

    // Generate pagination buttons
    buttons.innerHTML = '';

    // Previous button
    const prevBtn = document.createElement('li');
    prevBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_prev ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_prev ? 'disabled' : ''} 
                onclick="fetchItemWisePendingDemands(${pagination.current_page - 1})">
            <i class="material-symbols-outlined text-sm">chevron_left</i>
        </button>
    `;
    buttons.appendChild(prevBtn);

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('li');
        pageBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${i === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    onclick="fetchItemWisePendingDemands(${i})">
                ${i}
            </button>
        `;
        buttons.appendChild(pageBtn);
    }

    // Next button
    const nextBtn = document.createElement('li');
    nextBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_next ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_next ? 'disabled' : ''} 
                onclick="fetchItemWisePendingDemands(${pagination.current_page + 1})">
            <i class="material-symbols-outlined text-sm">chevron_right</i>
        </button>
    `;
    buttons.appendChild(nextBtn);

    container.classList.remove('hidden');
}

// Function to show pagination
function showPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    const info = document.getElementById('paginationInfo');
    const buttons = document.getElementById('paginationButtons');

    if (!pagination) {
        container.classList.add('hidden');
        return;
    }

    // Update pagination info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    info.textContent = `Showing ${start}-${end} of ${pagination.total_records} results`;

    // Generate pagination buttons
    buttons.innerHTML = '';

    // Previous button
    const prevBtn = document.createElement('li');
    prevBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_prev ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_prev ? 'disabled' : ''} 
                onclick="fetchPendingDemands(${pagination.current_page - 1})">
            <i class="material-symbols-outlined text-sm">chevron_left</i>
        </button>
    `;
    buttons.appendChild(prevBtn);

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('li');
        pageBtn.innerHTML = `
            <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${i === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                    onclick="fetchPendingDemands(${i})">
                ${i}
            </button>
        `;
        buttons.appendChild(pageBtn);
    }

    // Next button
    const nextBtn = document.createElement('li');
    nextBtn.innerHTML = `
        <button class="w-[31px] h-[31px] block leading-[29px] text-center rounded-md border border-gray-100 dark:border-[#172036] ${!pagination.has_next ? 'disabled:opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}" 
                ${!pagination.has_next ? 'disabled' : ''} 
                onclick="fetchPendingDemands(${pagination.current_page + 1})">
            <i class="material-symbols-outlined text-sm">chevron_right</i>
        </button>
    `;
    buttons.appendChild(nextBtn);

    container.classList.remove('hidden');
}

// Function to format date from YYYY-MM-DD to "Sep 07, 2025" format
function formatDate(dateString) {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'N/A';

    const months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];

    const month = months[date.getMonth()];
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();

    return `${month} ${day}, ${year}`;
}

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

// Function to generate print data directly (optimized approach)
async function generatePrintDataDirect(fromDate, toDate, printBtn, originalContent) {
    try {
        console.log('Attempting direct print generation...');

        const apiUrl = `../api/pending-demand-reports/print/pending-demands?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&company_id=1`;

        const response = await fetch(apiUrl);
        const data = await response.json();

        console.log('Direct print response:', data);

        if (data.success) {
            // Check if we have a large dataset and need chunked processing
            if (data.recordCount > 2000) {
                console.log('Large dataset detected, switching to chunked processing...');
                showToast('Large dataset detected, using optimized processing...', 'info');
                generatePrintDataChunked(fromDate, toDate, printBtn, originalContent);
                return;
            }

            // Direct print with complete HTML
            showToast(`Generated print data for ${data.recordCount} demands`, 'success');
            openPrintWindow(data.html, data.recordCount);

            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';

        } else {
            throw new Error(data.message || 'Error generating print data');
        }

    } catch (error) {
        console.error('Direct print generation failed:', error);
        console.log('Falling back to chunked processing...');

        // Fallback to chunked processing
        generatePrintDataChunked(fromDate, toDate, printBtn, originalContent);
    }
}

// Function to generate print data in chunks for large datasets
async function generatePrintDataChunked(fromDate, toDate, printBtn, originalContent) {
    try {
        let allHtml = '';
        let currentPage = 1;
        let totalPages = 1;
        let totalDemands = 0;
        const chunkSize = 1000; // Demands per chunk

        // Show progress modal
        showProgressModal(0, 1);

        // Get first chunk to determine total pages
        const firstChunkUrl = `../api/pending-demand-reports/print/pending-demands-chunked?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&company_id=1&page=1&limit=${chunkSize}`;

        const firstResponse = await fetch(firstChunkUrl);
        const firstData = await firstResponse.json();

        if (!firstData.success) {
            throw new Error(firstData.message || 'Error fetching first chunk');
        }

        totalPages = firstData.totalPages;
        totalDemands = firstData.totalDemands;
        allHtml = firstData.html;

        console.log(`Total pages: ${totalPages}, Total demands: ${totalDemands}`);

        // Update progress
        updateProgress(1, totalPages, 1, totalDemands);

        // Load remaining chunks
        for (let page = 2; page <= totalPages; page++) {
            // Update progress
            updateProgress(page - 1, totalPages, page - 1, totalDemands);

            const chunkUrl = `../api/pending-demand-reports/print/pending-demands-chunked?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}&company_id=1&page=${page}&limit=${chunkSize}`;

            console.log(`Fetching chunk ${page}:`, chunkUrl);

            const response = await fetch(chunkUrl);
            const data = await response.json();

            if (data.success) {
                allHtml += data.html;
                console.log(`Chunk ${page} loaded successfully. Progress: ${data.progress}%`);
            } else {
                console.warn(`Error loading chunk ${page}:`, data.message);
            }

            // Small delay to prevent overwhelming the server
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        // Final progress update
        updateProgress(totalPages, totalPages, totalDemands, totalDemands);

        // Hide progress modal and open print window
        setTimeout(() => {
            hideProgressModal();

            // Create complete HTML document
            const completeHtml = createCompletePrintHtml(allHtml, fromDate, toDate, totalDemands);
            openPrintWindow(completeHtml, totalDemands);

            // Restore button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
            printBtn.style.opacity = '1';

            showToast(`Generated print data for ${totalDemands} demands`, 'success');
        }, 500);

    } catch (error) {
        console.error('Error in chunked print generation:', error);
        showToast('Error generating print data: ' + error.message, 'error');

        // Restore button state
        printBtn.innerHTML = originalContent;
        printBtn.disabled = false;
        printBtn.style.opacity = '1';

        // Hide progress modal if it exists
        hideProgressModal();
    }
}

// Function to create complete print HTML document
function createCompletePrintHtml(content, fromDate, toDate, totalDemands) {
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Pending Demands Report</title>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px; 
                    line-height: 1.4;
                    font-size: 12px;
                }
                .print-header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #333; 
                    padding-bottom: 20px; 
                }
                .print-header h1 { 
                    margin: 0; 
                    color: #333; 
                    font-size: 24px; 
                }
                .print-header p { 
                    margin: 5px 0; 
                    color: #666; 
                }
                .demand-group { 
                    margin-bottom: 30px; 
                    page-break-inside: avoid; 
                }
                .demand-header { 
                    background-color: #f0f8ff; 
                    padding: 15px; 
                    border-left: 4px solid #3b82f6; 
                    margin-bottom: 10px; 
                }
                .demand-title { 
                    font-weight: bold; 
                    font-size: 16px; 
                    color: #1e40af; 
                    margin-bottom: 5px; 
                }
                .demand-info { 
                    font-size: 14px; 
                    color: #666; 
                }
                .items-table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 10px; 
                }
                .items-table th, .items-table td { 
                    border: 1px solid #ddd; 
                    padding: 6px; 
                    text-align: left; 
                    font-size: 11px; 
                }
                .items-table th { 
                    background-color: #f8f9fa; 
                    font-weight: bold; 
                }
                .priority-badge { 
                    padding: 2px 6px; 
                    border-radius: 12px; 
                    font-size: 9px; 
                    font-weight: bold; 
                }
                .priority-critical { background-color: #fecaca; color: #991b1b; }
                .priority-high { background-color: #fed7aa; color: #9a3412; }
                .priority-medium { background-color: #fef3c7; color: #92400e; }
                .priority-low { background-color: #d1fae5; color: #065f46; }
                .status-badge { 
                    padding: 2px 6px; 
                    border-radius: 12px; 
                    font-size: 9px; 
                    font-weight: bold; 
                }
                .status-pending { background-color: #fef3c7; color: #92400e; }
                .status-active { background-color: #dcfce7; color: #166534; }
                .status-completed { background-color: #dbeafe; color: #1e40af; }
                @media print {
                    @page {
                        size: A4 landscape;
                        margin: 0.5in;
                    }
                    body { margin: 0; padding: 15px 20px; }
                    .demand-group { page-break-inside: avoid; }
                    .print-header { page-break-after: avoid; margin-top: 0 !important; margin-bottom: 20px !important; }
                    .items-table { font-size: 10px; page-break-inside: auto; width: 100%; }
                    .items-table th, .items-table td { padding: 4px; page-break-inside: avoid; }
                    .items-table tr { page-break-inside: avoid; break-inside: avoid; }
                    .demand-header-row { page-break-after: avoid; break-after: avoid; }
                    #action-buttons { display: none !important; visibility: hidden !important; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>Pending Demands Report</h1>
                <p>From: ${formatDate(fromDate)} | To: ${formatDate(toDate)}</p>
                <p>Total Demands: ${totalDemands}</p>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </div>
            ${content}
        </body>
        </html>
    `;
}

// Function to open print window with HTML content and action buttons
function openPrintWindow(html, recordCount) {
    try {
        console.log(`Opening print window for ${recordCount} records`);

        // Create a new window for printing (landscape orientation)
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            showToast('Error: Could not open print window. Please allow popups for this site.', 'error');
            return;
        }

        // Write the HTML directly (buttons will be added via DOM manipulation)
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();

        // Add PDF functions to the print window
        addPDFFunctionsToWindow(printWindow);

        // Add action buttons after the window loads
        printWindow.onload = function() {
            addActionButtonsToWindow(printWindow, recordCount);
        };

        console.log('Print window opened successfully with action buttons');

    } catch (error) {
        console.error('Error opening print window:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// Function to open unit and department wise print window with custom filename
function openUnitDepartmentWisePrintWindow(html, recordCount) {
    try {
        console.log(`Opening unit and department wise print window for ${recordCount} records`);

        // Create a new window for printing (landscape orientation)
        const printWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            showToast('Error: Could not open print window. Please allow popups for this site.', 'error');
            return;
        }

        // Write the HTML directly (buttons will be added via DOM manipulation)
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();

        // Add PDF functions to the print window with custom filename
        addUnitDepartmentWisePDFFunctionsToWindow(printWindow);

        // Add action buttons after the window loads
        printWindow.onload = function() {
            addActionButtonsToWindow(printWindow, recordCount);
        };

        console.log('Unit and department wise print window opened successfully with action buttons');

    } catch (error) {
        console.error('Error opening unit and department wise print window:', error);
        showToast('Error opening print window: ' + error.message, 'error');
    }
}

// PDF generation functions for the print window
function addPDFFunctionsToWindow(printWindow) {
    const script = printWindow.document.createElement('script');
    script.textContent = `
        function printReport() {
            console.log('Print button clicked');
            window.print();
        }
        
        function downloadAsPDF() {
            console.log('Download as PDF button clicked');
            
            // Show loading state
            const pdfBtn = document.getElementById('pdf-btn');
            const originalContent = pdfBtn.innerHTML;
            pdfBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating PDF...';
            pdfBtn.disabled = true;
            pdfBtn.style.opacity = '0.7';
            
            // Generate PDF using HTML-to-PDF conversion
            try {
                // Load required libraries dynamically
                loadLibrariesForPDF().then(() => {
                    generateHTMLToPDF();
                }).catch(error => {
                    console.error('Error loading libraries:', error);
                    alert('Error loading PDF libraries: ' + error.message);
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF: ' + error.message);
                pdfBtn.innerHTML = originalContent;
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
        
        function loadLibrariesForPDF() {
            return new Promise((resolve, reject) => {
                // Check if libraries are already loaded
                if (window.html2canvas && window.jspdf) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                const totalLibraries = 2;
                
                // Load html2canvas
                const html2canvasScript = document.createElement('script');
                html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                html2canvasScript.onload = () => {
                    loadedCount++;
                    if (loadedCount === totalLibraries) resolve();
                };
                html2canvasScript.onerror = () => reject(new Error('Failed to load html2canvas'));
                document.head.appendChild(html2canvasScript);
                
                // Load jsPDF
                const jsPDFScript = document.createElement('script');
                jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                jsPDFScript.onload = () => {
                    loadedCount++;
                    if (loadedCount === totalLibraries) resolve();
                };
                jsPDFScript.onerror = () => reject(new Error('Failed to load jsPDF'));
                document.head.appendChild(jsPDFScript);
            });
        }
        
        function generateHTMLToPDF() {
            try {
                // Hide action buttons completely and remove from DOM temporarily
                const actionButtons = document.getElementById('action-buttons');
                let actionButtonsParent = null;
                let actionButtonsNextSibling = null;
                
                if (actionButtons) {
                    actionButtonsParent = actionButtons.parentNode;
                    actionButtonsNextSibling = actionButtons.nextSibling;
                    actionButtonsParent.removeChild(actionButtons);
                }
                
                // Get the report content (excluding action buttons)
                const reportContent = document.querySelector('.print-header') || document.body;
                
                // Add comprehensive CSS for PDF generation
                const pdfStyles = document.createElement('style');
                pdfStyles.textContent = 
                    '#action-buttons { display: none !important; visibility: hidden !important; }' +
                    '@media print {' +
                        'body { margin: 0 !important; padding: 15px 20px !important; font-size: 11px !important; }' +
                        '.print-header { margin-top: 0 !important; page-break-after: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-group { page-break-inside: avoid !important; break-inside: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-header { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table { page-break-inside: auto !important; margin: 0 !important; }' +
                        '.items-table tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        '.demand-header-row { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table tbody tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        'table { border-collapse: collapse !important; }' +
                        'td, th { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                    '}';
                document.head.appendChild(pdfStyles);
                
                // Configure html2canvas options for better quality and complete capture
                const options = {
                    scale: 2, // Higher resolution for better quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: window.innerWidth,
                    windowHeight: window.innerHeight,
                    logging: false,
                    removeContainer: true
                };
                
                console.log('Converting HTML to canvas with dimensions:', {
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight
                });
                
                html2canvas(reportContent, options).then(canvas => {
                    console.log('Canvas created with dimensions:', canvas.width, 'x', canvas.height);
                    
                    // Create PDF with proper dimensions and margins (Landscape A4)
                    const { jsPDF } = window.jspdf;
                    const pageWidth = 297; // A4 landscape width in mm
                    const pageHeight = 210; // A4 landscape height in mm
                    const marginTop = 15; // Top margin in mm
                    const marginBottom = 15; // Bottom margin in mm
                    const marginLeft = 10; // Left margin in mm
                    const marginRight = 10; // Right margin in mm
                    
                    // Calculate available content area
                    const contentWidth = pageWidth - marginLeft - marginRight;
                    const contentHeight = pageHeight - marginTop - marginBottom;
                    
                    // Calculate image dimensions to fit content area
                    const imgWidth = contentWidth;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    console.log('PDF dimensions - Content Width:', contentWidth, 'mm, Content Height:', contentHeight, 'mm');
                    console.log('Image dimensions - Width:', imgWidth, 'mm, Height:', imgHeight, 'mm');
                    
                    const pdf = new jsPDF('l', 'mm', 'a4'); // 'l' for landscape orientation
                    let heightLeft = imgHeight;
                    let position = 0;
                    
                    // Add image to PDF with margins
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                    heightLeft -= contentHeight;
                    
                    // Add additional pages if content is longer than one page
                    let pageCount = 1;
                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pageCount++;
                        pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                        heightLeft -= contentHeight;
                    }
                    
                    console.log('PDF generated with ' + pageCount + ' pages');
                    
                    // Save the PDF
                    const fileName = 'Pending_Demands_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                    pdf.save(fileName);
                    
                    console.log('PDF downloaded successfully');
                    
                    // Show success message
                    alert('PDF downloaded successfully! (' + pageCount + ' pages)');
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                    
                }).catch(error => {
                    console.error('Error converting HTML to canvas:', error);
                    alert('Error generating PDF: ' + error.message);
                    
                    // Clean up
                    if (document.head.contains(pdfStyles)) {
                        document.head.removeChild(pdfStyles);
                    }
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
                
            } catch (error) {
                console.error('Error in generateHTMLToPDF:', error);
                alert('Error generating PDF: ' + error.message);
                
                // Restore action buttons to DOM
                if (actionButtons && actionButtonsParent) {
                    if (actionButtonsNextSibling) {
                        actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                    } else {
                        actionButtonsParent.appendChild(actionButtons);
                    }
                }
                
                // Restore button state
                const pdfBtn = document.getElementById('pdf-btn');
                pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
        
    `;
    printWindow.document.head.appendChild(script);
}

// Function to add small action buttons outside the report section
function addActionButtonsToWindow(printWindow, recordCount) {
    try {
        console.log('Adding action buttons outside report section');

        // First, add CSS to exclude buttons from print and position them properly
        const style = printWindow.document.createElement('style');
        style.textContent = `
            #action-buttons {
                position: fixed !important;
                top: 10px !important;
                right: 10px !important;
                z-index: 9999 !important;
                display: flex !important;
                gap: 8px !important;
                background: rgba(255, 255, 255, 0.95) !important;
                padding: 8px !important;
                border-radius: 6px !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
                backdrop-filter: blur(5px) !important;
            }
            
            @media print {
                #action-buttons {
                    display: none !important;
                }
            }
            
            /* Ensure report content starts below buttons */
            .print-header {
                margin-top: 60px !important;
            }
        `;
        printWindow.document.head.appendChild(style);

        // Create small buttons container
        const actionButtonsDiv = printWindow.document.createElement('div');
        actionButtonsDiv.id = 'action-buttons';

        actionButtonsDiv.innerHTML = `
            <button id="print-btn" onclick="printReport()" style="
                background: #2563eb;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
            " onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='translateY(-1px)'" onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(0)'">
                <span style="font-size: 14px;">🖨️</span>
                Print
            </button>
            <button id="pdf-btn" onclick="downloadAsPDF()" style="
                background: #dc2626;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
            " onmouseover="this.style.backgroundColor='#b91c1c'; this.style.transform='translateY(-1px)'" onmouseout="this.style.backgroundColor='#dc2626'; this.style.transform='translateY(0)'">
                <span style="font-size: 14px;">📄</span>
                PDF
            </button>
        `;

        // Add the buttons to the body
        const body = printWindow.document.body;
        if (body) {
            body.appendChild(actionButtonsDiv);
            console.log('Action buttons added outside report section');
        } else {
            console.error('Could not find body element in print window');
        }

    } catch (error) {
        console.error('Error adding action buttons to window:', error);
    }
}

// Function to add Print and PDF buttons to Unit & Site wise print window
function addUnitSiteWiseActionButtonsToWindow(printWindow, recordCount) {
    try {
        console.log('Adding print and PDF buttons for unit and site wise report');

        // First, add CSS to exclude buttons from print and position them properly
        const style = printWindow.document.createElement('style');
        style.textContent = `
            #action-buttons {
                position: fixed !important;
                top: 10px !important;
                right: 10px !important;
                z-index: 9999 !important;
                display: flex !important;
                gap: 8px !important;
                background: rgba(255, 255, 255, 0.95) !important;
                padding: 8px !important;
                border-radius: 6px !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
                backdrop-filter: blur(5px) !important;
            }
            
            @media print {
                #action-buttons {
                    display: none !important;
                }
            }
            
            /* Ensure report content starts below buttons */
            .print-header {
                margin-top: 60px !important;
            }
        `;
        printWindow.document.head.appendChild(style);

        // Create small buttons container with Print and PDF buttons
        const actionButtonsDiv = printWindow.document.createElement('div');
        actionButtonsDiv.id = 'action-buttons';

        actionButtonsDiv.innerHTML = `
            <button id="print-btn" onclick="printReport()" style="
                background: #2563eb;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
            " onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='translateY(-1px)'" onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(0)'">
                <span style="font-size: 14px;">🖨️</span>
                Print
            </button>
            <button id="pdf-btn" onclick="downloadAsPDF()" style="
                background: #dc2626;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
            " onmouseover="this.style.backgroundColor='#b91c1c'; this.style.transform='translateY(-1px)'" onmouseout="this.style.backgroundColor='#dc2626'; this.style.transform='translateY(0)'">
                <span style="font-size: 14px;">📄</span>
                PDF
            </button>
        `;

        // Add the buttons to the body
        const body = printWindow.document.body;
        if (body) {
            body.appendChild(actionButtonsDiv);
            console.log('Print and PDF buttons added for unit and site wise report');
        } else {
            console.error('Could not find body element in print window');
        }

    } catch (error) {
        console.error('Error adding print button to window:', error);
    }
}

// PDF generation functions for unit and department wise print window with custom filename
function addUnitDepartmentWisePDFFunctionsToWindow(printWindow) {
    const script = printWindow.document.createElement('script');
    script.textContent = `
        function printReport() {
            console.log('Print button clicked');
            window.print();
        }
        
        function downloadAsPDF() {
            console.log('Download as PDF button clicked');
            
            // Show loading state
            const pdfBtn = document.getElementById('pdf-btn');
            const originalContent = pdfBtn.innerHTML;
            pdfBtn.innerHTML = '<span style="font-size: 16px;">⏳</span> Generating PDF...';
            pdfBtn.disabled = true;
            pdfBtn.style.opacity = '0.7';
            
            // Generate PDF using HTML-to-PDF conversion
            try {
                // Load required libraries dynamically
                loadLibrariesForPDF().then(() => {
                    generateHTMLToPDF();
                }).catch(error => {
                    console.error('Error loading libraries:', error);
                    alert('Error loading PDF libraries: ' + error.message);
                    pdfBtn.innerHTML = originalContent;
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF: ' + error.message);
                pdfBtn.innerHTML = originalContent;
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
        
        function loadLibrariesForPDF() {
            return new Promise((resolve, reject) => {
                // Check if libraries are already loaded
                if (window.html2canvas && window.jspdf) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                const totalLibraries = 2;
                
                function checkComplete() {
                    loadedCount++;
                    if (loadedCount === totalLibraries) {
                        resolve();
                    }
                }
                
                // Load html2canvas
                if (!window.html2canvas) {
                    const html2canvasScript = document.createElement('script');
                    html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                    html2canvasScript.onload = checkComplete;
                    html2canvasScript.onerror = () => reject(new Error('Failed to load html2canvas'));
                    document.head.appendChild(html2canvasScript);
                } else {
                    checkComplete();
                }
                
                // Load jsPDF
                if (!window.jspdf) {
                    const jsPDFScript = document.createElement('script');
                    jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                    jsPDFScript.onload = checkComplete;
                    jsPDFScript.onerror = () => reject(new Error('Failed to load jsPDF'));
                    document.head.appendChild(jsPDFScript);
                } else {
                    checkComplete();
                }
            });
        }
        
        function generateHTMLToPDF() {
            try {
                console.log('Starting HTML to PDF conversion');
                
                // Get the report content (excluding action buttons)
                const reportContent = document.querySelector('.print-header') || document.body;
                
                if (!reportContent) {
                    throw new Error('Could not find report content to convert');
                }
                
                // Temporarily remove action buttons from DOM for clean PDF
                const actionButtons = document.getElementById('action-buttons');
                let actionButtonsParent = null;
                let actionButtonsNextSibling = null;
                
                if (actionButtons) {
                    actionButtonsParent = actionButtons.parentNode;
                    actionButtonsNextSibling = actionButtons.nextSibling;
                    actionButtonsParent.removeChild(actionButtons);
                }
                
                // Add comprehensive CSS for PDF generation
                const pdfStyles = document.createElement('style');
                pdfStyles.textContent = 
                    '#action-buttons { display: none !important; visibility: hidden !important; }' +
                    '@media print {' +
                        'body { margin: 0 !important; padding: 15px 20px !important; font-size: 11px !important; }' +
                        '.print-header { margin-top: 0 !important; page-break-after: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-group { page-break-inside: avoid !important; break-inside: avoid !important; margin-bottom: 20px !important; }' +
                        '.demand-header { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table { page-break-inside: auto !important; margin: 0 !important; }' +
                        '.items-table tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        '.demand-header-row { page-break-after: avoid !important; break-after: avoid !important; }' +
                        '.items-table tbody tr { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                        'table { border-collapse: collapse !important; }' +
                        'td, th { page-break-inside: avoid !important; break-inside: avoid !important; }' +
                    '}';
                document.head.appendChild(pdfStyles);
                
                // Configure html2canvas options for better quality and complete capture
                const options = {
                    scale: 2, // Higher resolution for better quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: window.innerWidth,
                    windowHeight: window.innerHeight,
                    logging: false,
                    removeContainer: true
                };
                
                console.log('Converting HTML to canvas with dimensions:', {
                    width: reportContent.scrollWidth,
                    height: reportContent.scrollHeight
                });
                
                html2canvas(reportContent, options).then(canvas => {
                    console.log('Canvas created with dimensions:', canvas.width, 'x', canvas.height);
                    
                    // Create PDF with proper dimensions and margins (Landscape A4)
                    const { jsPDF } = window.jspdf;
                    const pageWidth = 297; // A4 landscape width in mm
                    const pageHeight = 210; // A4 landscape height in mm
                    const marginTop = 15; // Top margin in mm
                    const marginBottom = 15; // Bottom margin in mm
                    const marginLeft = 10; // Left margin in mm
                    const marginRight = 10; // Right margin in mm
                    
                    // Calculate available content area
                    const contentWidth = pageWidth - marginLeft - marginRight;
                    const contentHeight = pageHeight - marginTop - marginBottom;
                    
                    // Calculate image dimensions to fit content area
                    const imgWidth = contentWidth;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    console.log('PDF dimensions - Content Width:', contentWidth, 'mm, Content Height:', contentHeight, 'mm');
                    console.log('Image dimensions - Width:', imgWidth, 'mm, Height:', imgHeight, 'mm');
                    
                    const pdf = new jsPDF('l', 'mm', 'a4'); // 'l' for landscape orientation
                    let heightLeft = imgHeight;
                    let position = 0;
                    
                    // Add image to PDF with margins
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                    heightLeft -= contentHeight;
                    
                    // Add additional pages if content is longer than one page
                    let pageCount = 1;
                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pageCount++;
                        pdf.addImage(imgData, 'PNG', marginLeft, marginTop + position, imgWidth, imgHeight);
                        heightLeft -= contentHeight;
                    }
                    
                    console.log('PDF generated with ' + pageCount + ' pages');
                    
                    // Save the PDF with custom filename for Unit & Department Wise Report
                    const fileName = 'Unit_Department_Wise_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                    pdf.save(fileName);
                    
                    console.log('PDF downloaded successfully');
                    
                    // Show success message
                    alert('PDF downloaded successfully! (' + pageCount + ' pages)');
                    
                    // Clean up
                    document.head.removeChild(pdfStyles);
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                    
                }).catch(error => {
                    console.error('Error converting HTML to canvas:', error);
                    alert('Error generating PDF: ' + error.message);
                    
                    // Clean up
                    if (document.head.contains(pdfStyles)) {
                        document.head.removeChild(pdfStyles);
                    }
                    
                    // Restore action buttons to DOM
                    if (actionButtons && actionButtonsParent) {
                        if (actionButtonsNextSibling) {
                            actionButtonsParent.insertBefore(actionButtons, actionButtonsNextSibling);
                        } else {
                            actionButtonsParent.appendChild(actionButtons);
                        }
                    }
                    
                    // Restore button state
                    const pdfBtn = document.getElementById('pdf-btn');
                    pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                    pdfBtn.disabled = false;
                    pdfBtn.style.opacity = '1';
                });
                
            } catch (error) {
                console.error('Error in generateHTMLToPDF:', error);
                alert('Error generating PDF: ' + error.message);
                
                // Restore button state
                const pdfBtn = document.getElementById('pdf-btn');
                pdfBtn.innerHTML = '<span style="font-size: 16px;">📄</span> PDF';
                pdfBtn.disabled = false;
                pdfBtn.style.opacity = '1';
            }
        }
    `;
    printWindow.document.head.appendChild(script);
}
