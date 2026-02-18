/**
 * Role Management System - Professional Implementation
 * Handles all CRUD operations, search, pagination, sorting, and export functionality
 */




//  Custom Search Dropdown Functionality
    function upgradeSelectToSearchable(selectId, placeholder) {
        const selectEl = document.getElementById(selectId);
        if (!selectEl || selectEl.__enhanced) return;
        selectEl.classList.add('opacity-0','pointer-events-none','absolute');
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
        control.appendChild(controlText); control.appendChild(caret);

        const panel = document.createElement('div');
        panel.className = 'absolute z-[9999] left-0 right-0 top-full mt-1 bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] rounded-md shadow-2xl hidden';
        const search = document.createElement('input');
        search.type = 'text'; search.placeholder = placeholderText;
        search.className = 'w-full h-[30px] px-[12px] text-sm text-black dark:text-white bg-white dark:bg-[#0c1427] border-0 border-b border-gray-200 dark:border-[#172036] outline-0 focus:outline-0 focus:ring-0 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded-t-md';
        const list = document.createElement('div');
        list.className = 'max-h-48 overflow-y-auto py-1';
        list.style.cssText = `
            max-height: 192px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        `;
        panel.appendChild(search); panel.appendChild(list);

        const parent = selectEl.parentNode;
        const next = selectEl.nextElementSibling;
        const beforeNode = (next && next.tagName && next.tagName.toLowerCase()==='label') ? next : next;
        if (beforeNode) parent.insertBefore(wrapper, beforeNode); else parent.appendChild(wrapper);
        wrapper.appendChild(control); wrapper.appendChild(panel);

        let currentItems = [], activeIndex = -1;
        function clearActive(){ if(activeIndex>-1 && currentItems[activeIndex]) currentItems[activeIndex].classList.remove('bg-gray-100','dark:bg-[#15203c]'); }
        function setActive(idx){ clearActive(); activeIndex=idx; if(activeIndex>-1 && currentItems[activeIndex]){ const el=currentItems[activeIndex]; el.classList.add('bg-gray-100','dark:bg-[#15203c]'); try{ el.scrollIntoView({block:'nearest'});}catch(_){} } }
        function moveActive(delta){ if(!currentItems.length){ return; } if(activeIndex===-1){ setActive(delta>0?0:currentItems.length-1); return; } const next=Math.max(0, Math.min(currentItems.length-1, activeIndex+delta)); setActive(next); }
        function selectActive(){ if(activeIndex>-1 && currentItems[activeIndex]) currentItems[activeIndex].click(); }
        function buildItems(filter=''){
            list.innerHTML=''; currentItems=[]; activeIndex=-1;
            const options = Array.from(selectEl.options);
            options.forEach(opt=>{
                if (opt.value==='') return;
                const text = opt.text || '';
                if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;
                const item = document.createElement('div');
                item.className = 'px-[12px] py-[6px] text-sm text-black dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c]';
                item.textContent = text; item.dataset.value = opt.value;
                item.addEventListener('click', (ev)=>{
                    if (ev && ev.stopPropagation) ev.stopPropagation();
                    selectEl.value = opt.value;
                    controlText.textContent = text || placeholderText;
                    controlText.className = text ? '' : 'text-gray-400';
                    panel.classList.add('hidden');
                    try { control.focus(); } catch(_) {}
                    selectEl.dispatchEvent(new Event('change'));
                    ensureLabel();
                });
                item.addEventListener('mouseenter', ()=>{ const i=currentItems.indexOf(item); if(i>-1) setActive(i); });
                list.appendChild(item); currentItems.push(item);
            });
        }
        function open(){ 
            panel.classList.remove('hidden'); 
            buildItems(''); 
            setTimeout(()=>search.focus(),0); 
            if (grp) grp.classList.add('is-focused');
            
            // Add click outside listener
            setTimeout(() => {
                document.addEventListener('click', closeOnClickOutside);
                document.addEventListener('keydown', closeOnEscape);
            }, 0);
        }
        function close(){ 
            if(!panel.classList.contains('hidden')){ 
                panel.classList.add('hidden'); 
                search.value=''; 
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
        control.addEventListener('click', ()=>{ panel.classList.contains('hidden') ? open() : close(); });
        control.addEventListener('keydown',(e)=>{
            if(e.key==='Enter' || e.key==='NumpadEnter'){
                if(panel.classList.contains('hidden')){
                    // Let the global navigation handle this - don't do anything here
                    return;
                } else {
                    e.preventDefault(); selectActive(); return;
                }
            }
            if(e.key===' '){ e.preventDefault(); panel.classList.contains('hidden')?open():close(); return; }
            if(e.key==='ArrowDown'){ e.preventDefault(); if(panel.classList.contains('hidden')) open(); else moveActive(1); return; }
            if(e.key==='ArrowUp'){ e.preventDefault(); if(panel.classList.contains('hidden')) open(); else moveActive(-1); return; }
        });
        // Some browsers don't fire keydown reliably on buttons inside forms for Enter; add keyup fallback
        control.addEventListener('keyup',(e)=>{
            if(!(e.key==='Enter' || e.key==='NumpadEnter')) return;
            if (panel.classList.contains('hidden')) {
                // Let the global navigation handle this - don't do anything here
                return;
            } else {
                e.preventDefault(); if (activeIndex===-1){ moveActive(1); } selectActive();
            }
        });
        search.addEventListener('input',()=>buildItems(search.value));
        search.addEventListener('keydown',(e)=>{ if(e.key==='Escape'){ close(); control.focus(); return; } if(e.key==='ArrowDown'){ e.preventDefault(); moveActive(1); return; } if(e.key==='ArrowUp'){ e.preventDefault(); moveActive(-1); return; } if(e.key==='Enter' || e.key==='NumpadEnter'){ e.preventDefault(); if(activeIndex===-1){ moveActive(1);} selectActive(); return; } });

        const grp = selectEl.closest('.float-group');
        function ensureLabel(){
            if(!grp) return;
            if (selectEl.value && String(selectEl.value).length){ grp.classList.add('is-filled'); selectEl.classList.add('has-value'); }
            else { grp.classList.remove('is-filled'); selectEl.classList.remove('has-value'); }
        }
        selectEl.addEventListener('change', ensureLabel); setTimeout(ensureLabel,0);

        // Focus styling parity with inputs
        if (grp) {
            control.addEventListener('focus', () => grp.classList.add('is-focused'));
            control.addEventListener('blur', () => { if (panel.classList.contains('hidden')) grp.classList.remove('is-focused'); });
            search.addEventListener('focus', () => grp.classList.add('is-focused'));
            search.addEventListener('blur', () => grp.classList.remove('is-focused'));
        }

        selectEl.__enhanced = {
            control, panel, list, search,
            refresh: () => buildItems(search.value||''),
            setDisplayFromValue: () => { const t = selectEl.options[selectEl.selectedIndex]?.text || placeholderText; controlText.textContent=t; controlText.className = t ? '' : 'text-gray-400'; ensureLabel(); }
        };
    }

    function enhanceFloatSelects(){
        const selects = document.querySelectorAll('select[data-float-select]');
        selects.forEach(sel => { try { upgradeSelectToSearchable(sel.id, 'Search...'); } catch(_) {} });
    }

    function refreshSearchableSelectOptions(selectId, opts){
        const el = document.getElementById(selectId); if(!el) return;
        setSelectOptions(el, opts); if (el.__enhanced && el.__enhanced.refresh) el.__enhanced.refresh();
        if (el.__enhanced && el.__enhanced.setDisplayFromValue) el.__enhanced.setDisplayFromValue();
    }

    // Initialize enhanced selects when DOM is loaded
    document.addEventListener('DOMContentLoaded', function(){
        enhanceFloatSelects();
    });
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
    
    /* Hide pagination elements by default since pagination is disabled */
    #permissionsAssignmentPaginationInfo,
    #permissionsAssignmentPaginationControls {
        display: none !important;
    }
`;
document.head.appendChild(style);

// ======================== GLOBAL CONFIGURATION ========================

const API_ENDPOINTS = {
    roles: { list: '../roles', create: '../roles', update: '../roles', delete: '../roles' }
};

// Global state management
const state = {
    currentEditRole: null,
    currentPage: 1,
    itemsPerPage: 10,
    searchTimeout: null,
    searchUserTimeout: null
};

// ======================== UTILITY FUNCTIONS ========================

function closeEditModal(modalId) {
    console.log(`Closing modal: ${modalId}`);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        
        // Reset form if it exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

function showEditModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// ======================== TOAST & ALERT FUNCTIONS ========================

// Unified toast styling
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) { return; }

    const toast = document.createElement('div');

    let bgColor, textColor, iconBg, borderColor, icon, shadowColor;
    switch (type) {
        case 'success':
            bgColor = '#f0fdf4'; textColor = '#166534'; iconBg = '#16a34a'; borderColor = '#bbf7d0'; icon = '✓'; shadowColor = 'rgba(22, 163, 74, 0.15)'; break;
        case 'error':
            bgColor = '#fef2f2'; textColor = '#991b1b'; iconBg = '#dc2626'; borderColor = '#fecaca'; icon = '✗'; shadowColor = 'rgba(220, 38, 38, 0.15)'; break;
        case 'warning':
            bgColor = '#fffbeb'; textColor = '#92400e'; iconBg = '#d97706'; borderColor = '#fed7aa'; icon = '⚠'; shadowColor = 'rgba(217, 119, 6, 0.15)'; break;
        case 'info':
            bgColor = '#eff6ff'; textColor = '#1e40af'; iconBg = '#3b82f6'; borderColor = '#bfdbfe'; icon = 'ℹ'; shadowColor = 'rgba(59, 130, 246, 0.15)'; break;
        default:
            bgColor = '#f0fdf4'; textColor = '#166534'; iconBg = '#16a34a'; borderColor = '#bbf7d0'; icon = '✓'; shadowColor = 'rgba(22, 163, 74, 0.15)';
    }

    toast.style.cssText = `background:${bgColor};color:${textColor};padding:16px 20px;border-radius:12px;box-shadow:0 10px 15px -3px ${shadowColor},0 4px 6px -2px rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;position:relative;z-index:9999;min-width:300px;max-width:400px;font-weight:500;font-size:14px;border:1px solid ${borderColor};transform:translateX(100%);transition:all .3s cubic-bezier(.4,0,.2,1);letter-spacing:.025em;`;

    toast.innerHTML = `
        <div style="width:20px;height:20px;border-radius:50%;background:${iconBg};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;color:white;flex-shrink:0;box-shadow:0 2px 4px rgba(0,0,0,.1)">${icon}</div>
        <span style="flex:1;line-height:1.4">${message}</span>
        <button style="background:none;border:none;color:${textColor};cursor:pointer;font-size:18px;opacity:.6;transition:opacity .2s;padding:0;margin-left:8px;flex-shrink:0;width:20px;height:20px;display:flex;align-items:center;justify-content:center" onclick="this.parentElement.remove()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">×</button>`;

    toastContainer.appendChild(toast);
    setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 100);
    setTimeout(() => {
        if (toastContainer.contains(toast)) {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => { if (toastContainer.contains(toast)) { toastContainer.removeChild(toast); } }, 300);
        }
    }, 5000);
}

function showConfirmDialog(title, message, onConfirm, type = 'warning') {
    console.log(`Confirm Dialog: ${title}`);
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: message,
            icon: type,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                content: 'swal2-content-custom',
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            },
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                onConfirm();
            }
        });
    } else {
        console.warn('SweetAlert not loaded, using confirm fallback');
        if (confirm(`${title}\n\n${message}`)) {
            onConfirm();
        }
    }
}

// ======================== LOADING STATES ========================

function showTableLoading() {
    state.isLoading = true;
    const tableBody = document.getElementById('rolesTableBody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-[20px] py-[20px] text-center">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                        <span class="ml-3 text-gray-600">Loading roles data...</span>
                    </div>
                </td>
            </tr>
        `;
    }
}

function hideTableLoading() {
    state.isLoading = false;
}

// ======================== ROLE MANAGEMENT FUNCTIONS ========================

async function loadRolesForDropdown() {
    // Show loading state in dropdown
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect) {
        roleSelect.innerHTML = '<option value="" disabled selected hidden>Loading roles...</option>';
    }
    
    try {
        const response = await fetch(`../roles?limit=1000`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            populateRoleDropdown(result.data.records);
            
            // Ensure enhanced select is properly initialized after populating
            setTimeout(() => {
                reinitializeEnhancedSelect();
            }, 200);
        } else {
            throw new Error(result.error || 'Failed to load roles');
        }
    } catch (error) {
        console.error('Error loading roles for dropdown:', error);
        showToast(`Failed to load roles: ${error.message}`, 'error');
        
        // Show error state in dropdown
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect) {
            roleSelect.innerHTML = '<option value="" disabled selected hidden>Error loading roles</option>';
        }
    }
}

// Function to refresh roles dropdown
function refreshRolesDropdown() {
    loadRolesForDropdown();
}

// Function to reinitialize enhanced select after populating options
function reinitializeEnhancedSelect() {
    const roleSelect = document.getElementById('roleSelect');
    if (!roleSelect) return;
    
    // Wait a bit for the DOM to update, then refresh the enhanced select
    setTimeout(() => {
        if (roleSelect.__enhanced && roleSelect.__enhanced.refresh) {
            roleSelect.__enhanced.refresh();
        }
        if (roleSelect.__enhanced && roleSelect.__enhanced.setDisplayFromValue) {
            roleSelect.__enhanced.setDisplayFromValue();
        }
    }, 100);
}

// Function to clear role selection
function clearRoleSelection() {
    const roleSelect = document.getElementById('roleSelect');
    if (!roleSelect) return;
    
    // Clear the selection
    roleSelect.value = '';
    
    // Update the enhanced select display
    if (roleSelect.__enhanced && roleSelect.__enhanced.setDisplayFromValue) {
        roleSelect.__enhanced.setDisplayFromValue();
    }
    
    // Trigger change event
    roleSelect.dispatchEvent(new Event('change'));
}

function populateRoleDropdown(roles) {
    const roleSelect = document.getElementById('roleSelect');
    if (!roleSelect) return;

    // Clear existing options except the first placeholder
    roleSelect.innerHTML = '<option value="" disabled selected hidden></option>';
    
    // Add role options
    if (roles && roles.length > 0) {
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name;
            roleSelect.appendChild(option);
        });
        
        // Show success message if roles were loaded
        console.log(`Loaded ${roles.length} roles for dropdown`);
    } else {
        // Show message if no roles found
        const option = document.createElement('option');
        option.value = "";
        option.textContent = "No roles available";
        option.disabled = true;
        roleSelect.appendChild(option);
        console.log('No roles available for dropdown');
    }
    
    // Reinitialize the enhanced select after populating options
    reinitializeEnhancedSelect();
}

async function loadRoles() {
    const searchTerm = document.getElementById('searchRoleInput').value;
    const params = new URLSearchParams({
        page: state.currentPage,
        limit: state.itemsPerPage,
        search: searchTerm
    });

    showTableLoading();

    try {
        const response = await fetch(`../roles?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            renderRolesTable(result.data);
            renderRolePagination(result.data);
        } else {
            throw new Error(result.error || 'Failed to load data');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to load roles: ${error.message}`, 'error');
        document.getElementById('rolesTableBody').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load data</td></tr>';
    } finally {
        hideTableLoading();
    }
}

function renderRolesTable(data) {
    const tableBody = document.getElementById('rolesTableBody');

    if (!data.records || data.records.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-gray-500">No roles found</td></tr>';
        return;
    }

    tableBody.innerHTML = data.records.map(role => `
        <tr data-role-id="${role.id}">
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                #R-${String(role.id).padStart(3, '0')}
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <span class="block font-medium">
                    ${role.name}
                </span>
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                ${role.description || '-'}
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <span class="px-[8px] py-[3px] inline-block bg-gray-50 dark:bg-[#15203c] text-gray-700 dark:text-gray-300 rounded-sm font-medium text-xs">
                    ${role.status || '-'}
                </span>
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <span class="px-[8px] py-[3px] inline-block bg-primary-50 dark:bg-[#15203c] text-primary-500 rounded-sm font-medium text-xs">
                    ${role.permissions ? JSON.parse(role.permissions).length : 0} permissions
                </span>
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] md:ltr:first:pl-[25px] md:rtl:first:pr-[25px] ltr:first:pr-0 rtl:first:pl-0 border-b border-gray-100 dark:border-[#172036]">
                <div class="flex items-center gap-[9px]">
                    <button type="button" class="text-blue-500" onclick="editRole(${role.id})" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>
                    <button type="button" class="text-red-500" onclick="deleteRole(${role.id})" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderRolePagination(data) {
    const paginationInfo = document.getElementById('rolePaginationInfo');
    const paginationControls = document.getElementById('rolePaginationControls');

    if (!data.total) {
        paginationInfo.textContent = 'No entries';
        paginationControls.innerHTML = '';
        return;
    }

    const total = parseInt(data.total, 10) || 0;
    const limit = parseInt(data.limit, 10) || 10;
    const page = parseInt(data.page, 10) || 1;
    const totalPages = parseInt(data.total_pages, 10) || 1;
    const start = (page - 1) * limit + 1;
    const end = Math.min(start + limit - 1, total);
    
    paginationInfo.textContent = `Showing ${start} to ${end} of ${total} entries`;

    const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
    const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

    let html = '';
    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page - 1}" class="${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_left</i>\n  </a>\n</li>`;

    const maxButtons = 5;
    let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
    let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);

    if (endBtn - startBtn + 1 < maxButtons) {
        startBtn = Math.max(1, endBtn - maxButtons + 1);
    }

    for (let i = startBtn; i <= endBtn; i++) {
        html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${i}" class="${i === page ? activeCls : btnCls}">\n    ${i}\n  </a>\n</li>`;
    }

    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page + 1}" class="${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_right</i>\n  </a>\n</li>`;

    paginationControls.innerHTML = html;

    // Add click event listeners
    paginationControls.querySelectorAll('a[data-page]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageNum = parseInt(this.getAttribute('data-page'));
            if (pageNum >= 1 && pageNum <= totalPages) {
                state.currentPage = pageNum;
                loadRoles();
            }
        });
    });
}

async function editRole(id) {
    try {
        const response = await fetch(`../roles/${id}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            const role = result.data;
            
            // Populate edit form
            document.getElementById('editRoleId').value = role.id || '';
            document.getElementById('editRoleName').value = role.name || '';
            document.getElementById('editRoleDescription').value = role.description || '';
            document.getElementById('editRoleStatus').value = role.status || '';
            
            // Show edit modal
            showEditModal('editRoleModal');
        } else {
            throw new Error(result.error || 'Failed to load role');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to load role: ${error.message}`, 'error');
    }
}

async function deleteRole(id) {
    console.log(`Deleting role ${id}`);
    
    showConfirmDialog(
        'Delete Role',
        'Are you sure you want to delete this role? This action cannot be undone.',
        async () => {
            try {
                const response = await fetch(`${API_ENDPOINTS.roles.delete}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showToast('Role deleted successfully! ✅', 'success');
                    loadRoles();
                    loadRolesForDropdown();
                } else {
                    throw new Error(data.error || 'Failed to delete role');
                }
            } catch (error) {
                console.error('Error deleting role:', error);
                showToast(`Failed to delete role: ${error.message}`, 'error');
            }
        }
    );
}

// ======================== FORM HANDLING ========================

async function submitRoleForm(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = document.getElementById('submitRoleBtn');
    const submitText = document.getElementById('submitRoleText');
    const submitIcon = document.getElementById('submitRoleIcon');

    const editId = submitBtn.getAttribute('data-edit-id');
    const isEdit = editId !== null;

    // Get form data
    const formData = new FormData(form);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        status: formData.get('status')
    };

    // Validate required fields
    if (!data.name) {
        showToast('⚠️ Please fill in the role name', 'warning');
        return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = isEdit ? 'Updating...' : 'Adding...';
    submitIcon.textContent = 'hourglass_empty';

    try {
        const url = isEdit ? `../roles/${editId}` : '../roles';
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            if (isEdit) {
                showToast(`✅ Role "${data.name}" has been updated successfully!`, 'success');
            } else {
                showToast(`✅ New Role "${data.name}" has been created successfully!`, 'success');
            }
            
            form.reset();
            
            // Focus back to first field for quick data entry
            setTimeout(() => {
                try {
                    const first = document.getElementById('roleName');
                    if (first) { first.focus(); if (first.select) first.select(); }
                } catch(_) {}
            }, 50);

            // Reset form to create mode
            submitText.textContent = 'Create';
            submitIcon.textContent = 'add';
            submitBtn.removeAttribute('data-edit-id');

            // Reload table and dropdown
            loadRoles();
            loadRolesForDropdown();
        } else {
            throw new Error(result.error || 'Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        if (isEdit) {
            showToast(`❌ Failed to update Role "${data.name}". ${error.message}`, 'error');
        } else {
            showToast(`❌ Failed to create new Role. ${error.message}`, 'error');
        }
            } finally {
            // Reset button state but preserve text if role is selected
            submitBtn.disabled = false;
            
            // Check if a role is currently selected
            const roleSelect = document.getElementById('roleSelect');
            if (roleSelect && roleSelect.value) {
                // Keep "Assign Permissions" text if role is selected
                submitText.textContent = 'Assign Permissions';
                submitIcon.textContent = 'save';
            } else {
                // Reset to "Create" only if no role is selected
                submitText.textContent = 'Create';
                submitIcon.textContent = 'add';
            }
        }
    }

    async function submitEditRoleForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        status: formData.get('status')
    };

    const roleId = formData.get('id');

    try {
        const response = await fetch(`../roles/${roleId}`, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            showToast(`✅ Role "${data.name}" has been updated successfully!`, 'success');
            closeEditModal('editRoleModal');
            loadRoles();
            loadRolesForDropdown();
        } else {
            throw new Error(result.error || 'Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`❌ Failed to update role. ${error.message}`, 'error');
    }
}

// ======================== EVENT LISTENERS ========================

document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadRoles();
    loadRolesForDropdown(); // Load roles for dropdown as well
    
    // Ensure enhanced selects are properly initialized after a delay
    setTimeout(() => {
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect && roleSelect.__enhanced) {
            reinitializeEnhancedSelect();
        }
        // Load permissions for assignment
        loadPermissionsForAssignment();
    }, 500);

    // Add event listeners for permission toggles
    setTimeout(() => {
        // Module toggles
        document.querySelectorAll('.module-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                handleModuleToggle(this);
            });
        });
        
        // Individual permission toggles
        document.querySelectorAll('.permission-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                handlePermissionToggle(this);
            });
        });
    }, 1000);

    // Form submission
    const roleForm = document.getElementById('roleForm');
    roleForm.addEventListener('submit', submitRoleForm);

    // Edit form submission
    const editRoleForm = document.getElementById('editRoleForm');
    editRoleForm.addEventListener('submit', submitEditRoleForm);

    // Search functionality
    const searchInput = document.getElementById('searchRoleInput');
    searchInput.addEventListener('input', function() {
        clearTimeout(state.searchTimeout);
        state.searchTimeout = setTimeout(() => {
            state.currentPage = 1;
            loadRoles();
        }, 500);
    });

    // Initial focus
    setTimeout(() => {
        try {
            const first = document.getElementById('roleName');
            if (first) { first.focus(); if (first.select) first.select(); }
        } catch(_) {}
    }, 100);

    // Load initial data for all tabs
    loadRoles();
    loadPermissions();
    loadRolesForUserAssignment();
    loadUsers();

    // Permission form submission
    const permissionForm = document.getElementById('permissionForm');
    if (permissionForm) {
        permissionForm.addEventListener('submit', submitPermissionForm);
    }

    // Edit permission form submission
    const editPermissionForm = document.getElementById('editPermissionForm');
    if (editPermissionForm) {
        editPermissionForm.addEventListener('submit', submitEditPermissionForm);
    }

    // Edit user form submission
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', submitEditUserForm);
    }

    // Assign permission to role form submission
    const accountForm = document.getElementById('accountForm');
    if (accountForm) {
        accountForm.addEventListener('submit', submitAssignPermissionForm);
    }

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('[data-tab]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and panes
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                targetPane.classList.add('active');
                
                // Load data based on active tab
                if (tabId === 'tab1') {
                    // Roles tab - data is already loaded
                } else if (tabId === 'tab2') {
                    // Permissions tab
                    loadPermissions();
                } else if (tabId === 'tab3') {
                    // Load permissions for assignment
                    loadPermissionsForAssignment();
                } else if (tabId === 'tab4') {
                    // Assign Role to User tab
                    loadRolesForUserAssignment();
                    loadUsers();
                }
            }
        });
    });

    // Role select change event to load existing permissions
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const selectedRoleId = this.value;
            const submitBtn = document.getElementById('submitAccountBtn');
            const submitText = document.getElementById('submitAccountText');
            const submitIcon = document.getElementById('submitAccountIcon');
            
            if (selectedRoleId) {
                // Change button text to "Assign Permissions"
                submitText.textContent = 'Assign Permissions';
                submitIcon.textContent = 'save';
                
                // Load permissions for the selected role
                loadRolePermissions(selectedRoleId);
            } else {
                // Reset button text to "Create"
                submitText.textContent = 'Create';
                submitIcon.textContent = 'add';
                
                // Clear all permissions
                clearAllPermissions(true);
            }
        });
    }

    // Permission search functionality
    const searchPermissionInput = document.getElementById('searchPermissionInput');
    if (searchPermissionInput) {
        searchPermissionInput.addEventListener('input', function() {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.currentPage = 1;
                loadPermissions();
            }, 500);
        });
    }

    // User form submission
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', submitUserForm);
    }

    // User search functionality
    const searchUserInput = document.getElementById('searchUserInput');
    if (searchUserInput) {
        searchUserInput.addEventListener('input', function() {
            clearTimeout(state.searchUserTimeout);
            state.searchUserTimeout = setTimeout(() => {
                searchUsers();
            }, 500);
        });
    }
});

// ======================== ENTER NAVIGATION ========================

// Enter-to-next navigation for Role form
(function(){
    function wireEnterNav(formId, submitBtnId) {
        const form = document.getElementById(formId);
        const submitBtn = document.getElementById(submitBtnId);
        if (!form || !submitBtn) return;

        // Collect tabbable controls in DOM order
        const controls = Array.from(form.querySelectorAll('input, select, textarea, button'))
            .filter((el, idx, arr) => !el.disabled && el.type !== 'hidden' && el.tabIndex !== -1 && arr.indexOf(el) === idx);

        // Key handler to move focus on Enter
        const onKey = (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            
            // If current control is the submit button, trigger form submission
            if (e.currentTarget === submitBtn) {
                form.requestSubmit(submitBtn);
                return;
            }
            
            const idx = controls.indexOf(e.currentTarget);
            
            // If not last input-like control, move to next
            if (idx > -1 && idx < controls.length - 1) {
                const next = controls[idx + 1];
                next?.focus();
                if (next?.select) { try { next.select(); } catch(_){} }
                return;
            }
            // Last control: ensure submit button gets focus
            submitBtn.focus();
        };

        // Attach to all inputs and the submit button
        controls.forEach(el => {
            el.addEventListener('keydown', onKey);
        });

        form.addEventListener('submit', () => {
            // After submission completes, refocus first field shortly
            setTimeout(() => {
                const first = controls[0];
                if (first) {
                    try { first.focus(); if (first.select) first.select(); } catch(_) {}
                }
            }, 80);
        });
    }

    // Wire the role form
    document.addEventListener('DOMContentLoaded', function(){
        wireEnterNav('roleForm', 'submitRoleBtn');
        wireEnterNav('editRoleForm', 'editRoleSubmitBtn');
        wireEnterNav('permissionForm', 'submitPermissionBtn');
        wireEnterNav('editPermissionForm', 'editPermissionSubmitBtn');
        wireEnterNav('editUserForm', 'editUserSubmitBtn');
        wireEnterNav('accountForm', 'submitAccountBtn');
        wireEnterNav('userForm', 'submitUserBtn');
    });
})();

// ======================== GLOBAL FUNCTION EXPOSURE ========================

// ======================== PERMISSION MANAGEMENT FUNCTIONS ========================

async function loadPermissions() {
    const searchTerm = document.getElementById('searchPermissionInput').value;
    const params = new URLSearchParams({
        page: state.currentPage,
        limit: state.itemsPerPage,
        search: searchTerm
    });

    showTableLoading();

    try {
        const response = await fetch(`../permissions?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            renderPermissionsTable(result.data);
            renderPermissionPagination(result.data);
        } else {
            throw new Error(result.error || 'Failed to load data');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to load permissions: ${error.message}`, 'error');
        document.getElementById('permissionsTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-8 text-red-500">Failed to load data</td></tr>';
    } finally {
        hideTableLoading();
    }
}

function renderPermissionsTable(data) {
    console.log('Rendering permissions table with data:', data);
    const tableBody = document.getElementById('permissionsTableBody');

    if (!data.records || data.records.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-gray-500">No permissions found</td></tr>';
        return;
    }

    tableBody.innerHTML = data.records.map(permission => {
        console.log('Rendering permission:', permission);
        return `
        <tr data-module="${permission.module}">
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                #M-${String(permission.id).padStart(3, '0')}
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <span class="px-[8px] py-[3px] inline-block bg-primary-50 dark:bg-[#15203c] text-primary-500 rounded-sm font-medium text-xs">
                    ${permission.module || '-'}
                </span>
            </td>
            <td class="ltr:text-left rtl:text-right px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <div class="flex flex-wrap gap-2">
                    ${permission.actions.map((action, index) => `
                        <span class="px-[8px] py-[3px] inline-block bg-primary-50 dark:bg-[#15203c] text-primary-500 rounded-sm font-medium text-xs">
                            ${action.action}
                        </span>${index < permission.actions.length - 1 ? ',' : ''}
                    `).join('')}
                </div>
            </td>
            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] border-b border-gray-100 dark:border-[#172036] ltr:first:border-l ltr:last:border-r rtl:first:border-r rtl:last:border-l">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    ${permission.description || '-'}
                </span>
            </td>

            <td class="ltr:text-left rtl:text-right whitespace-nowrap px-[20px] py-[5px] md:ltr:first:pl-[25px] md:rtl:first:pr-[25px] ltr:first:pr-0 rtl:first:pl-0 border-b border-gray-100 dark:border-[#172036]">
                <div class="flex items-center gap-[9px]">
                    <button type="button" class="text-blue-500" onclick="editModulePermissions('${permission.module}')" title="Edit Module"><i class="material-symbols-outlined text-sm">edit</i></button>
                    <button type="button" class="text-green-500" onclick="addActionToModule('${permission.module}')" title="Add Action"><i class="material-symbols-outlined text-sm">add</i></button>
                    <button type="button" class="text-red-500" onclick="deleteModulePermissions('${permission.module}')" title="Delete Module"><i class="material-symbols-outlined text-sm">delete</i></button>
                </div>
            </td>
        </tr>
    `;
    }).join('');
}

function renderPermissionPagination(data) {
    const paginationInfo = document.getElementById('permissionPaginationInfo');
    const paginationControls = document.getElementById('permissionPaginationControls');

    if (!data.total) {
        paginationInfo.textContent = 'No entries';
        paginationControls.innerHTML = '';
        return;
    }

    const total = parseInt(data.total, 10) || 0;
    const limit = parseInt(data.limit, 10) || 10;
    const page = parseInt(data.page, 10) || 1;
    const totalPages = parseInt(data.total_pages, 10) || 1;
    const start = (page - 1) * limit + 1;
    const end = Math.min(start + limit - 1, total);
    
    paginationInfo.textContent = `Showing ${start} to ${end} of ${total} entries`;

    const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
    const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

    let html = '';
    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page - 1}" class="${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_left</i>\n  </a>\n</li>`;

    const maxButtons = 5;
    let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
    let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);

    if (endBtn - startBtn + 1 < maxButtons) {
        startBtn = Math.max(1, endBtn - maxButtons + 1);
    }

    for (let i = startBtn; i <= endBtn; i++) {
        html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${i}" class="${i === page ? activeCls : btnCls}">\n    ${i}\n  </a>\n</li>`;
    }

    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page + 1}" class="${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_right</i>\n  </a>\n</li>`;

    paginationControls.innerHTML = html;

    // Add click event listeners
    paginationControls.querySelectorAll('a[data-page]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageNum = parseInt(this.getAttribute('data-page'));
            if (pageNum >= 1 && pageNum <= totalPages) {
                state.currentPage = pageNum;
                loadPermissions();
            }
        });
    });
}

async function editPermission(id) {
    try {
        const response = await fetch(`../permissions/${id}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            const permission = result.data;
            
            // Populate edit form
            document.getElementById('editPermissionId').value = permission.id || '';
            document.getElementById('editPermissionModule').value = permission.module || '';
            document.getElementById('editPermissionAction').value = permission.action || '';
            document.getElementById('editPermissionDescription').value = permission.description || '';
            
            // Show edit modal
            showEditModal('editPermissionModal');
        } else {
            throw new Error(result.error || 'Failed to load permission');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to load permission: ${error.message}`, 'error');
    }
}

async function deletePermission(id) {
    console.log(`Deleting permission ${id}`);
    
    showConfirmDialog(
        'Delete Permission',
        'Are you sure you want to delete this permission? This action cannot be undone.',
        async () => {
            try {
                const response = await fetch(`../permissions/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showToast('Permission deleted successfully! ✅', 'success');
                    loadPermissions();
                } else {
                    throw new Error(data.error || 'Failed to delete permission');
                }
            } catch (error) {
                console.error('Error deleting permission:', error);
                showToast(`Failed to delete permission: ${error.message}`, 'error');
            }
        }
    );
}

async function submitPermissionForm(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = document.getElementById('submitPermissionBtn');
    const submitText = document.getElementById('submitPermissionText');
    const submitIcon = document.getElementById('submitPermissionIcon');

    const editId = submitBtn.getAttribute('data-edit-id');
    const isEdit = editId !== null;

    // Get form data
    const formData = new FormData(form);
    const data = {
        module: formData.get('module'),
        action: formData.get('action'),
        description: formData.get('description')
    };

    // Validate required fields
    if (!data.module || !data.action) {
        showToast('⚠️ Please fill in Module and Action fields', 'warning');
        return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = isEdit ? 'Updating...' : 'Adding...';
    submitIcon.textContent = 'hourglass_empty';

    try {
        const url = isEdit ? `../permissions/${editId}` : '../permissions';
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            if (isEdit) {
                showToast(`✅ Permission "${data.module}.${data.action}" has been updated successfully!`, 'success');
            } else {
                showToast(`✅ Action "${data.action}" has been added to "${data.module}" module successfully!`, 'success');
            }
            
            form.reset();
            
            // Focus back to first field for quick data entry
            setTimeout(() => {
                try {
                    const first = document.getElementById('permissionModule');
                    if (first) { first.focus(); if (first.select) first.select(); }
                } catch(_) {}
            }, 50);

            // Reset form to create mode
            submitText.textContent = 'Create';
            submitIcon.textContent = 'add';
            submitBtn.removeAttribute('data-edit-id');

            // Reload table
            loadPermissions();
        } else {
            throw new Error(result.error || 'Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        if (isEdit) {
            showToast(`❌ Failed to update Permission "${data.module}.${data.action}". ${error.message}`, 'error');
        } else {
            showToast(`❌ Failed to create new Permission. ${error.message}`, 'error');
        }
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitText.textContent = isEdit ? 'Update' : 'Add Action';
        submitIcon.textContent = isEdit ? 'save' : 'add';
    }
}

    async function submitEditPermissionForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const data = {
        module: formData.get('module'),
        action: formData.get('action'),
        description: formData.get('description')
    };

    const permissionId = formData.get('id');

    // Validate required fields
    if (!data.module || !data.action) {
        showToast('⚠️ Please fill in Module and Action fields', 'warning');
        return;
    }

    try {
        const response = await fetch(`../permissions/${permissionId}`, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            showToast(`✅ Permission "${data.module}.${data.action}" has been updated successfully!`, 'success');
            closeEditModal('editPermissionModal');
            loadPermissions();
        } else {
            throw new Error(result.error || 'Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`❌ Failed to update permission. ${error.message}`, 'error');
    }
}

// ======================== MODULE-BASED PERMISSION FUNCTIONS ========================

async function editModulePermissions(moduleName) {
    try {
        // Get all permissions for this module
        const response = await fetch(`../permissions?search=${encodeURIComponent(moduleName)}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            // Find the module in the grouped data (case-insensitive)
            const moduleData = result.data.records.find(record => 
                record.module.toLowerCase() === moduleName.toLowerCase()
            );
            if (moduleData) {
                // Populate edit form with module data
                document.getElementById('editPermissionModule').value = moduleData.module || '';
                document.getElementById('editPermissionDescription').value = moduleData.description || '';
                
                // Show edit modal
                showEditModal('editPermissionModal');
            } else {
                throw new Error('Module not found');
            }
        } else {
            throw new Error(result.error || 'Failed to load module data');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to load module data: ${error.message}`, 'error');
    }
}

async function addActionToModule(moduleName) {
    try {
        // Pre-fill the form with the module name (preserve original case)
        document.getElementById('permissionModule').value = moduleName;
        document.getElementById('permissionModule').focus();
        
        showToast(`Adding new action to ${moduleName} module`, 'info');
    } catch (error) {
        console.error('Error:', error);
        showToast(`Failed to add action: ${error.message}`, 'error');
    }
}

async function deleteModulePermissions(moduleName) {
    console.log(`Deleting module ${moduleName}`);
    
    showConfirmDialog(
        'Delete Module Permissions',
        `Are you sure you want to delete all permissions for the "${moduleName}" module? This action cannot be undone.`,
        async () => {
            try {
                // Get all permissions for this module
                const response = await fetch(`../permissions?search=${encodeURIComponent(moduleName)}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                        if (result.success) {
            // Find the module in the grouped data (case-insensitive)
            const moduleData = result.data.records.find(record => 
                record.module.toLowerCase() === moduleName.toLowerCase()
            );
            if (moduleData) {
                        // Delete all permissions for this module
                        const deletePromises = moduleData.actions.map(action => 
                            fetch(`../permissions/${action.id}`, {
                                method: 'DELETE',
                                headers: { 'Content-Type': 'application/json' },
                                credentials: 'same-origin'
                            })
                        );

                        await Promise.all(deletePromises);
                        showToast(`All permissions for "${moduleName}" module deleted successfully! ✅`, 'success');
                        loadPermissions();
                    } else {
                        throw new Error('Module not found');
                    }
                } else {
                    throw new Error(result.error || 'Failed to load module data');
                }
            } catch (error) {
                console.error('Error deleting module permissions:', error);
                showToast(`Failed to delete module permissions: ${error.message}`, 'error');
            }
        }
    );
}

// ======================== ASSIGN PERMISSION TO ROLE FUNCTIONS ========================

async function submitAssignPermissionForm(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = document.getElementById('submitAccountBtn');
    const submitText = document.getElementById('submitAccountText');
    const submitIcon = document.getElementById('submitAccountIcon');

    // Get form data
    const formData = new FormData(form);
    const data = {
        role_id: formData.get('role_id')
    };

    // Validate required fields
    if (!data.role_id) {
        showToast('⚠️ Please select a role', 'warning');
        return;
    }

    // Get selected permissions
    const selectedPermissions = getSelectedPermissions();
    
    if (selectedPermissions.length === 0) {
        showToast('⚠️ Please select at least one permission', 'warning');
        return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = 'Saving...';
    submitIcon.textContent = 'hourglass_empty';

    try {
        // Call API to assign permissions to the role
        const response = await fetch(`../roles/${data.role_id}/permissions`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                permissions: selectedPermissions
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            showToast(`✅ Successfully assigned ${selectedPermissions.length} permissions to role`, 'success');
            
            // Log the permissions for debugging
            console.log('Assigned permissions:', selectedPermissions);
            
            // Don't reset the form - keep the role selected
            // form.reset(); // Commented out to preserve role selection
            
            // Don't clear permissions - keep them visible
            // clearAllPermissions(true); // Commented out to preserve current permissions
            
            // Reset button state
            submitBtn.disabled = false;
            submitText.textContent = 'Assign Permissions';
            submitIcon.textContent = 'save';
            
            // Update the summary to reflect current state
            updatePermissionsSummary();
        } else {
            throw new Error(result.error || 'Failed to assign permissions');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showToast(`❌ Failed to assign permissions. ${error.message}`, 'error');
    } finally {
        // Reset button state but preserve text if role is selected
        submitBtn.disabled = false;
        
        // Check if a role is currently selected
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect && roleSelect.value) {
            // Keep "Assign Permissions" text if role is selected
            submitText.textContent = 'Assign Permissions';
            submitIcon.textContent = 'save';
        } else {
            // Reset to "Create" only if no role is selected
            submitText.textContent = 'Create';
            submitIcon.textContent = 'add';
        }
    }
}

// ======================== PERMISSION TOGGLE FUNCTIONS ========================

// Function to toggle individual permission
function togglePermission(button) {
    const isActive = button.classList.contains('active');
    
    if (isActive) {
        // Turn off
        button.classList.remove('active', 'bg-primary-600');
        button.classList.add('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.remove('translate-x-4');
        button.querySelector('span').classList.add('translate-x-0');
    } else {
        // Turn on
        button.classList.add('active', 'bg-primary-600');
        button.classList.remove('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.add('translate-x-4');
        button.querySelector('span').classList.remove('translate-x-0');
    }
    
    // Update permissions summary
    updatePermissionsSummary();
}

// Function to select all permissions
function selectAllPermissions() {
    const permissionToggles = document.querySelectorAll('.permission-toggle[data-module]');
    permissionToggles.forEach(toggle => {
        if (!toggle.classList.contains('active')) {
            toggle.classList.add('active', 'bg-primary-600');
            toggle.classList.remove('bg-gray-200', 'dark:bg-gray-700');
            toggle.querySelector('span').classList.add('translate-x-4');
            toggle.querySelector('span').classList.remove('translate-x-0');
        }
    });
    
    // Update summary
    updatePermissionsSummary();
    showToast('✅ All permissions selected successfully!', 'success');
}

// Function to clear all permissions
function clearAllPermissions(suppressToast = false) {
    const permissionToggles = document.querySelectorAll('.permission-toggle[data-module]');
    permissionToggles.forEach(toggle => {
        if (toggle.classList.contains('active')) {
            toggle.classList.remove('active', 'bg-primary-600');
            toggle.classList.add('bg-gray-200', 'dark:bg-gray-700');
            toggle.querySelector('span').classList.remove('translate-x-4');
            toggle.querySelector('span').classList.add('translate-x-0');
        }
    });
    
    // Update summary
    updatePermissionsSummary();
    
    // Only show toast if not suppressed
    if (!suppressToast) {
        showToast('✅ All permissions cleared successfully!', 'warning');
    }
}

// Function to update module toggles based on individual permissions
function updateModuleToggles() {
    const modules = document.querySelectorAll('.permission-module');
    
    modules.forEach(module => {
        const moduleToggle = module.querySelector('.module-toggle');
        const permissionToggles = module.querySelectorAll('.permission-toggle');
        const totalPermissions = permissionToggles.length;
        const checkedPermissions = Array.from(permissionToggles).filter(toggle => toggle.checked).length;
        
        if (checkedPermissions === 0) {
            moduleToggle.checked = false;
        } else if (checkedPermissions === totalPermissions) {
            moduleToggle.checked = true;
        } else {
            moduleToggle.checked = false;
        }
    });
}

// Function to handle module toggle (select all permissions for a module)
function handleModuleToggle(moduleToggle) {
    const module = moduleToggle.closest('.permission-module');
    const permissionToggles = module.querySelectorAll('.permission-toggle');
    
    permissionToggles.forEach(toggle => {
        toggle.checked = moduleToggle.checked;
    });
    
    updatePermissionsSummary();
    showToast(`✅ ${moduleToggle.checked ? 'All' : 'No'} permissions selected for module`, 'success');
}

// Function to handle individual permission toggle
function handlePermissionToggle(permissionToggle) {
    updateModuleToggles();
    updatePermissionsSummary();
    
    const module = permissionToggle.closest('.permission-module');
    const moduleName = module.querySelector('h6').textContent.trim();
    const action = permissionToggle.dataset.action;
    const isChecked = permissionToggle.checked;
    
    showToast(`${isChecked ? '✅' : '❌'} ${action} permission ${isChecked ? 'enabled' : 'disabled'} for ${moduleName}`, 'info');
}

// Function to get all selected permissions
function getSelectedPermissions() {
    const permissions = [];
    const permissionToggles = document.querySelectorAll('.permission-toggle.active[data-module]');
    
    permissionToggles.forEach(toggle => {
        const module = toggle.dataset.module;
        const action = toggle.dataset.action;
        permissions.push(`${module}.${action}`);
    });
    
    return permissions;
}

// Function to update permissions summary
function updatePermissionsSummary() {
    const permissions = getSelectedPermissions();
    const summaryElement = document.getElementById('permissionsSummary');
    const totalElement = document.getElementById('totalSelected');
    
    if (permissions.length === 0) {
        summaryElement.textContent = 'No permissions selected';
        totalElement.textContent = '0';
    } else {
        // Format permissions nicely
        const formattedPermissions = permissions.map(permission => {
            const [module, action] = permission.split('.');
            return `${module}.${action}`;
        }).join(', ');
        
        summaryElement.textContent = formattedPermissions;
        totalElement.textContent = permissions.length.toString();
    }
}

// Function to load permissions for a specific role
function loadRolePermissions(roleId) {
    // This will be implemented later when we connect to the backend
    console.log(`Loading permissions for role ID: ${roleId}`);
    
    // For now, just show a message
    showToast(`Loading permissions for role ID: ${roleId}`, 'info');
}

// Function to save permissions for a role
function saveRolePermissions(roleId, permissions) {
    // This will be implemented later when we connect to the backend
    console.log(`Saving permissions for role ID: ${roleId}:`, permissions);
    
    // For now, just show a message
    showToast(`Saving ${permissions.length} permissions for role ID: ${roleId}`, 'success');
}

// ======================== GLOBAL FUNCTION EXPOSURE ========================

// Expose functions globally for onclick handlers
window.editRole = editRole;
window.deleteRole = deleteRole;
window.closeEditModal = closeEditModal;
window.editPermission = editPermission;
window.deletePermission = deletePermission;
window.editModulePermissions = editModulePermissions;
window.addActionToModule = addActionToModule;
window.deleteModulePermissions = deleteModulePermissions;
window.refreshRolesDropdown = refreshRolesDropdown;
window.reinitializeEnhancedSelect = reinitializeEnhancedSelect;
window.clearRoleSelection = clearRoleSelection;
window.selectAllPermissions = selectAllPermissions;
window.clearAllPermissions = clearAllPermissions;
window.handleModuleToggle = handleModuleToggle;
window.handlePermissionToggle = handlePermissionToggle;
window.getSelectedPermissions = getSelectedPermissions;
window.loadRolePermissions = loadRolePermissions;
window.saveRolePermissions = saveRolePermissions;
window.togglePermission = togglePermission;
window.toggleSelectAll = toggleSelectAll;
window.toggleDeselectAll = toggleDeselectAll;
window.toggleModulePermissions = toggleModulePermissions;
window.loadPermissionsForAssignment = loadPermissionsForAssignment;
window.renderPermissionsAssignmentTable = renderPermissionsAssignmentTable;
// Pagination function removed - no longer needed

// Function to toggle all permissions for a specific module
function toggleModulePermissions(moduleName) {
    // Get all permission toggles for this module
    const moduleToggles = document.querySelectorAll(`.permission-toggle[data-module="${moduleName}"]`);
    
    if (moduleToggles.length === 0) return;
    
    // Check if all permissions for this module are currently active
    const allActive = Array.from(moduleToggles).every(toggle => toggle.classList.contains('active'));
    
    // Toggle all permissions for this module
    moduleToggles.forEach(toggle => {
        if (allActive) {
            // If all are active, turn them all off
            toggle.classList.remove('active');
            toggle.querySelector('.toggle-slider').style.transform = 'translateX(0px)';
        } else {
            // If not all are active, turn them all on
            toggle.classList.add('active');
            toggle.querySelector('.toggle-slider').style.transform = 'translateX(14px)';
        }
    });
    
    // Update the summary
    updatePermissionsSummary();
    
    // Show toast message
    const action = allActive ? 'cleared' : 'selected';
    const moduleDisplayName = getModuleDisplayName(moduleName);
    showToast(`✅ All permissions for ${moduleDisplayName} ${action} successfully!`, allActive ? 'warning' : 'success');
}

// Function to load and render permissions dynamically for the Assign Permission to Role tab
// NOTE: Pagination is disabled - ALL permissions are loaded on one page to prevent permission loss
async function loadPermissionsForAssignment(page = null, limit = null) {
    // Use state if no parameters provided, otherwise update state
    if (page !== null) {
        state.permissionsAssignmentPage = page;
    }
    if (limit !== null) {
        state.permissionsAssignmentLimit = limit;
    }
    
    const currentPage = state.permissionsAssignmentPage;
    const currentLimit = state.permissionsAssignmentLimit;
    
    console.log('Loading permissions for assignment...', { currentPage, currentLimit });
    
    try {
        // Load ALL permissions without pagination to prevent permission loss
        const params = new URLSearchParams({
            page: 1,
            limit: 1000 // Large limit to get all permissions
        });

        const response = await fetch(`../permissions?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Permissions API response:', result);

        if (result.success && result.data && result.data.records) {
            console.log('Rendering permissions table with data:', result.data.records);
            
            renderPermissionsAssignmentTable(result.data.records);
            
            // Hide pagination since we're showing all permissions
            const paginationInfo = document.getElementById('permissionsAssignmentPaginationInfo');
            const paginationControls = document.getElementById('permissionsAssignmentPaginationControls');
            
            if (paginationInfo) {
                paginationInfo.style.display = 'none';
            }
            if (paginationControls) {
                paginationControls.style.display = 'none';
            }
            
            console.log('All permissions loaded successfully without pagination');
        } else {
            throw new Error(result.error || 'Invalid response format');
        }
    } catch (error) {
        console.error('Error loading permissions for assignment:', error);
        showToast(`Failed to load permissions: ${error.message}`, 'error');
        // Fallback to static content if API fails
        renderStaticPermissionsTable();
    }
}

// Function to render the dynamic permissions table for assignment
function renderPermissionsAssignmentTable(permissions) {
    console.log('Rendering permissions assignment table with:', permissions);
    
    const tableBody = document.querySelector('#tab3 .permissions-table tbody');
    
    if (!tableBody) {
        console.error('Permissions table body not found');
        return;
    }

    if (!permissions || permissions.length === 0) {
        console.log('No permissions found, showing empty state');
        tableBody.innerHTML = '<tr><td colspan="2" class="text-center py-8 text-gray-500">No permissions found</td></tr>';
        return;
    }

    console.log(`Rendering ${permissions.length} permission modules`);

    tableBody.innerHTML = permissions.map(permission => {
        const moduleName = permission.module;
        const moduleKey = moduleName.toLowerCase();
        const actions = permission.actions || [];
        
        console.log(`Rendering module: ${moduleName} with ${actions.length} actions`);
        
        if (actions.length === 0) {
            return `
                <tr class="border-b border-gray-100 dark:border-[#172036]">
                    <td class="px-[20px] py-[15px] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors" onclick="toggleModulePermissions('${moduleKey}')">
                        <div class="flex items-center">
                            <i class="material-symbols-outlined text-primary-500 ltr:mr-3 rtl:ml-3">admin_panel_settings</i>
                            <span class="font-medium">${moduleName} Management</span>
                        </div>
                    </td>
                    <td class="px-[20px] py-[15px]">
                        <div class="text-sm text-gray-500 dark:text-gray-400">No actions available</div>
                    </td>
                </tr>
            `;
        }

        const actionsHtml = actions.map(action => `
            <div class="flex items-center">
                <span class="text-sm ltr:mr-2 rtl:ml-2">${action.action}</span>
                <button type="button" class="permission-toggle" data-module="${moduleKey}" data-action="${action.action}" onclick="togglePermission(this)">
                    <span class="toggle-slider"></span>
                </button>
            </div>
        `).join('');

        return `
            <tr class="border-b border-gray-100 dark:border-[#172036]">
                <td class="px-[20px] py-[15px] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors" onclick="toggleModulePermissions('${moduleKey}')">
                    <div class="flex items-center">
                        <i class="material-symbols-outlined text-primary-500 ltr:mr-3 rtl:ml-3">admin_panel_settings</i>
                        <span class="font-medium">${moduleName} Management</span>
                    </div>
                </td>
                <td class="px-[20px] py-[15px]">
                    <div class="flex items-center gap-6">
                        ${actionsHtml}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    console.log('Permissions table rendered successfully');
}

// Pagination removed - all permissions are now shown on one page

// Fallback function to render static permissions table if API fails
function renderStaticPermissionsTable() {
    const tableBody = document.querySelector('#tab3 .permissions-table tbody');
    
    if (!tableBody) {
        console.error('Permissions table body not found');
        return;
    }

    // Keep the original static content as fallback
    tableBody.innerHTML = `
        <!-- Users Management -->
        <tr class="border-b border-gray-100 dark:border-[#172036]">
            <td class="px-[20px] py-[15px] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors" onclick="toggleModulePermissions('users')">
                <div class="flex items-center">
                    <i class="material-symbols-outlined text-primary-500 ltr:mr-3 rtl:ml-3">admin_panel_settings</i>
                    <span class="font-medium">Users Management</span>
                </div>
            </td>
            <td class="px-[20px] py-[15px]">
                <div class="flex items-center gap-6">
                    <div class="flex items-center">
                        <span class="text-sm ltr:mr-2 rtl:ml-2">Create</span>
                        <button type="button" class="permission-toggle" data-module="users" data-action="create" onclick="togglePermission(this)">
                            <span class="toggle-slider"></span>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm ltr:mr-2 rtl:ml-2">View</span>
                        <button type="button" class="permission-toggle" data-module="users" data-action="read" onclick="togglePermission(this)">
                            <span class="toggle-slider"></span>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm ltr:mr-2 rtl:ml-2">Edit</span>
                        <button type="button" class="permission-toggle" data-module="users" data-action="update" onclick="togglePermission(this)">
                            <span class="toggle-slider"></span>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm ltr:mr-2 rtl:ml-2">Delete</span>
                        <button type="button" class="permission-toggle" data-module="users" data-action="delete" onclick="togglePermission(this)">
                            <span class="toggle-slider"></span>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `;
}

// Helper function to get display name for modules
function getModuleDisplayName(moduleName) {
    const moduleNames = {
        'users': 'Users Management',
        'roles': 'Roles Management',
        'items': 'Items Management',
        'units': 'Units Management',
        'sites': 'Sites Management',
        'reports': 'Reports Management'
    };
    return moduleNames[moduleName] || moduleName;
}

// Function to toggle select all
function toggleSelectAll(button) {
    const deselectAllButton = document.querySelector('[onclick="toggleDeselectAll(this)"]');
    const isActive = button.classList.contains('active');
    
    if (isActive) {
        // Turn off select all
        button.classList.remove('active', 'bg-primary-600');
        button.classList.add('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.remove('translate-x-4');
        button.querySelector('span').classList.add('translate-x-0');
    } else {
        // Turn on select all
        button.classList.add('active', 'bg-primary-600');
        button.classList.remove('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.add('translate-x-4');
        button.querySelector('span').classList.remove('translate-x-0');
        
        // Turn off deselect all button
        deselectAllButton.classList.remove('active', 'bg-primary-600');
        deselectAllButton.classList.add('bg-gray-200', 'dark:bg-gray-700');
        deselectAllButton.querySelector('span').classList.remove('translate-x-4');
        deselectAllButton.querySelector('span').classList.add('translate-x-0');
        
        // Select all permissions immediately (only permission toggles, not control toggles)
        const permissionToggles = document.querySelectorAll('.permission-toggle[data-module]');
        permissionToggles.forEach(toggle => {
            if (!toggle.classList.contains('active')) {
                toggle.classList.add('active', 'bg-primary-600');
                toggle.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                toggle.querySelector('span').classList.add('translate-x-4');
                toggle.querySelector('span').classList.remove('translate-x-0');
            }
        });
        
        // Update summary
        updatePermissionsSummary();
        showToast('✅ All permissions selected successfully!', 'success');
    }
}

// Function to toggle deselect all
function toggleDeselectAll(button) {
    const selectAllButton = document.querySelector('[onclick="toggleSelectAll(this)"]');
    const isActive = button.classList.contains('active');
    
    if (isActive) {
        // Turn off deselect all
        button.classList.remove('active', 'bg-primary-600');
        button.classList.add('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.remove('translate-x-4');
        button.querySelector('span').classList.add('translate-x-0');
    } else {
        // Turn on deselect all
        button.classList.add('active', 'bg-primary-600');
        button.classList.remove('bg-gray-200', 'dark:bg-gray-700');
        button.querySelector('span').classList.add('translate-x-4');
        button.querySelector('span').classList.remove('translate-x-0');
        
        // Turn off select all button
        selectAllButton.classList.remove('active', 'bg-primary-600');
        selectAllButton.classList.add('bg-gray-200', 'dark:bg-gray-700');
        selectAllButton.querySelector('span').classList.remove('translate-x-4');
        selectAllButton.querySelector('span').classList.add('translate-x-0');
        
        // Clear all permissions immediately (only permission toggles, not control toggles)
        const permissionToggles = document.querySelectorAll('.permission-toggle[data-module]');
        permissionToggles.forEach(toggle => {
            if (toggle.classList.contains('active')) {
                toggle.classList.remove('active', 'bg-primary-600');
                toggle.classList.add('bg-gray-200', 'dark:bg-gray-700');
                toggle.querySelector('span').classList.remove('translate-x-4');
                toggle.querySelector('span').classList.add('translate-x-0');
            }
        });
        
        // Update summary
        updatePermissionsSummary();
        showToast('✅ All permissions cleared successfully!', 'warning');
    }
}

// Function to load existing permissions for a selected role
async function loadRolePermissions(roleId) {
    if (!roleId) {
        clearAllPermissions(true); // Suppress toast when clearing
        return;
    }

    try {
        const response = await fetch(`../roles/${roleId}/permissions`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            const permissions = result.data.permissions || [];
            console.log('Loaded role permissions:', permissions);
            console.log('API response:', result);
            
            // Clear all permissions first (suppress toast)
            clearAllPermissions(true);
            
            // Set the permissions that are assigned to this role
            permissions.forEach(permission => {
                const [module, action] = permission.split('.');
                console.log(`Looking for toggle: module="${module}", action="${action}"`);
                const toggle = document.querySelector(`.permission-toggle[data-module="${module}"][data-action="${action}"]`);
                if (toggle) {
                    console.log(`Found toggle for ${module}.${action}, activating it`);
                    toggle.classList.add('active', 'bg-primary-600');
                    toggle.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                    toggle.querySelector('span').classList.add('translate-x-4');
                    toggle.querySelector('span').classList.remove('translate-x-0');
                } else {
                    console.log(`No toggle found for ${module}.${action}`);
                }
            });
            
            // Update the summary
            updatePermissionsSummary();
            
            showToast(`✅ Loaded ${permissions.length} permissions for selected role`, 'success');
        } else {
            throw new Error(result.error || 'Failed to load role permissions');
        }
    } catch (error) {
        console.error('Error loading role permissions:', error);
        showToast(`Failed to load role permissions: ${error.message}`, 'error');
        clearAllPermissions(true); // Suppress toast when clearing due to error
    }
}

// ======================== TAB 4: ASSIGN ROLE TO USER FUNCTIONS ========================

// Function to load roles for the user role assignment dropdown
async function loadRolesForUserAssignment() {
    try {
        console.log('Loading roles for user assignment...');
        const response = await fetch('../users/roles', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Roles API response:', result);

        if (result.success) {
            const roles = result.data || [];
            console.log('Roles loaded:', roles);
            const roleSelect = document.getElementById('userRoleSelect');
            
            if (!roleSelect) {
                console.error('userRoleSelect element not found');
                return;
            }
            
            // Clear existing options
            roleSelect.innerHTML = '<option value="" disabled selected hidden></option>';
            
            // Add role options
            roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id;
                option.textContent = role.name;
                roleSelect.appendChild(option);
            });
            
            console.log(`Added ${roles.length} roles to dropdown`);
            
            // Apply enhanced select functionality if not already applied
            if (!roleSelect.__enhanced) {
                console.log('Applying enhanced select to userRoleSelect');
                upgradeSelectToSearchable('userRoleSelect', 'Search roles...');
            }
        } else {
            throw new Error(result.error || 'Failed to load roles');
        }
    } catch (error) {
        console.error('Error loading roles for user assignment:', error);
        showToast(`Failed to load roles: ${error.message}`, 'error');
    }
}

// Function to load users table
async function loadUsers(page = 1, limit = 10) {
    try {
        const response = await fetch(`../users?page=${page}&limit=${limit}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            // Handle nested data structure: result.data contains {data: [], pagination: {}}
            const responseData = result.data || {};
            const users = responseData.data || result.data || [];
            const pagination = responseData.pagination || result.pagination || {};
            
            // Debug: Log the users data to see its structure
            console.log('Raw result.data:', result.data);
            console.log('Extracted users:', users);
            console.log('Users type:', typeof users);
            console.log('Is users array?', Array.isArray(users));
            
            if (Array.isArray(users)) {
                renderUsersTable(users);
                renderUserPagination(pagination);
            } else {
                console.error('Users data is not an array:', users);
                throw new Error('Invalid data format received from server');
            }
        } else {
            throw new Error(result.error || 'Failed to load users');
        }
    } catch (error) {
        console.error('Error loading users:', error);
        showToast(`Failed to load users: ${error.message}`, 'error');
    }
}

// Function to render users table
function renderUsersTable(users) {
    const tbody = document.getElementById('usersTableBody');
    
    // Debug logs
    console.log('renderUsersTable called with:', users);
    console.log('users type:', typeof users);
    console.log('is array:', Array.isArray(users));
    
    if (!tbody) {
        console.error('usersTableBody element not found');
        return;
    }
    
    if (!users || !Array.isArray(users) || users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-8 text-gray-500">
                    No users found
                </td>
            </tr>
        `;
        return;
    }
    
    // Function to truncate address to 2-3 words
    function truncateAddress(address) {
        if (!address || address === 'N/A') return 'N/A';
        const words = address.split(' ').filter(word => word.trim() !== '');
        if (words.length <= 3) return address;
        return words.slice(0, 3).join(' ') + '......';
    }
    
    tbody.innerHTML = users.map(user => `
        <tr class="hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors">
            <td class="px-[20px] py-[12px] text-sm">${user.id}</td>
            <td class="px-[20px] py-[12px] text-sm">${user.first_name+' '+user.last_name|| 'N/A'} <br />${user.name_in_urdu || 'N/A'}</td>
            <td class="px-[20px] py-[12px] text-sm">${user.username || 'N/A'}<br />${user.email || 'N/A'}</td>
            <td class="px-[20px] py-[12px] text-sm">${truncateAddress(user.address)}</td>
            <td class="px-[20px] py-[12px] text-sm">${user.cell || 'N/A'}<br />${user.ptcl || 'N/A'}</td>
            <td class="px-[20px] py-[12px] text-sm">${user.role_name || 'N/A'}</td>
            <td class="px-[20px] py-[12px] text-sm">
                <button type="button" onclick="editUser(${user.id})" 
                    class="text-blue-500 hover:text-blue-700 transition-colors ltr:mr-2 rtl:ml-2">
                    <i class="material-symbols-outlined text-sm">edit</i>
                </button>
                <button type="button" onclick="deleteUser(${user.id})" 
                    class="text-red-500 hover:text-red-700 transition-colors">
                    <i class="material-symbols-outlined text-sm">delete</i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Function to render user pagination
function renderUserPagination(pagination) {
    const paginationInfo = document.getElementById('userPaginationInfo');
    const paginationControls = document.getElementById('userPaginationControls');

    if (!paginationInfo || !paginationControls) {
        console.error('Pagination elements not found');
        return;
    }

    if (!pagination.total) {
        paginationInfo.textContent = 'No entries';
        paginationControls.innerHTML = '';
        return;
    }

    const total = parseInt(pagination.total, 10) || 0;
    const limit = parseInt(pagination.per_page, 10) || 10;
    const page = parseInt(pagination.current_page, 10) || 1;
    const totalPages = parseInt(pagination.last_page, 10) || 1;
    const start = (page - 1) * limit + 1;
    const end = Math.min(start + limit - 1, total);
    
    paginationInfo.textContent = `Showing ${start} to ${end} of ${total} entries`;

    const btnCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500';
    const activeCls = 'w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white';

    let html = '';
    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page - 1}" class="${btnCls} ${page <= 1 ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_left</i>\n  </a>\n</li>`;

    const maxButtons = 5;
    let startBtn = Math.max(1, page - Math.floor((maxButtons - 1) / 2));
    let endBtn = Math.min(totalPages, startBtn + maxButtons - 1);

    if (endBtn - startBtn + 1 < maxButtons) {
        startBtn = Math.max(1, endBtn - maxButtons + 1);
    }

    for (let i = startBtn; i <= endBtn; i++) {
        html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${i}" class="${i === page ? activeCls : btnCls}">\n    ${i}\n  </a>\n</li>`;
    }

    html += `\n<li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">\n  <a href="javascript:void(0);" data-page="${page + 1}" class="${btnCls} ${page >= totalPages ? 'opacity-50 pointer-events-none' : ''}">\n    <span class="opacity-0">0</span>\n    <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_right</i>\n  </a>\n</li>`;

    paginationControls.innerHTML = html;

    // Add click event listeners
    paginationControls.querySelectorAll('a[data-page]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageNum = parseInt(this.getAttribute('data-page'));
            if (pageNum >= 1 && pageNum <= totalPages) {
                loadUsers(pageNum, limit);
            }
        });
    });
}

// Function to handle user form submission
async function submitUserForm(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitUserBtn');
    const submitText = document.getElementById('submitUserText');
    const submitIcon = document.getElementById('submitUserIcon');
    
    // Disable button during submission
    submitBtn.disabled = true;
    submitText.textContent = 'Creating...';
    submitIcon.textContent = 'hourglass_empty';
    
    try {
        const data = {
            first_name: formData.get('first_name'),
            last_name: formData.get('last_name'),
            username: formData.get('username'),
            email: formData.get('email'),
            password: formData.get('password'),
            name_in_urdu: formData.get('name_in_urdu'),
            address: formData.get('address'),
            cell: formData.get('cell'),
            ptcl: formData.get('ptcl'),
            role_id: formData.get('role_id'),
            company_id: 1 // Hardcoded for now
        };
        
        const response = await fetch('../users', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            showToast(`✅ User created successfully!`, 'success');
            
            // Reset form
            form.reset();
            
            // Reload the table
            loadUsers();
            
            // Reset button state
            submitBtn.disabled = false;
            submitText.textContent = 'Create User';
            submitIcon.textContent = 'add';
        } else {
            throw new Error(result.error || 'Failed to create user');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showToast(`❌ Failed to create user. ${error.message}`, 'error');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitText.textContent = 'Create User';
        submitIcon.textContent = 'add';
    }
}

// Function to edit user
async function editUser(userId) {
    try {
        const response = await fetch(`../users/${userId}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            const user = result.data;
            
            // Populate edit form
            document.getElementById('editUserId').value = user.id || '';
            document.getElementById('editUserFirstName').value = user.first_name || '';
            document.getElementById('editUserLastName').value = user.last_name || '';
            document.getElementById('editUserUsername').value = user.username || '';
            document.getElementById('editUserEmail').value = user.email || '';
            document.getElementById('editUserPassword').value = ''; // Don't populate password
            document.getElementById('editUserNameInUrdu').value = user.name_in_urdu || '';
            document.getElementById('editUserAddress').value = user.address || '';
            document.getElementById('editUserCell').value = user.cell || '';
            document.getElementById('editUserPtcl').value = user.ptcl || '';
            // Load roles for the dropdown first
            await loadRolesForEditUserModal();
            
            // Then set the selected role value with debugging
            const roleSelect = document.getElementById('editUserRole');
            console.log('User role_id:', user.role_id);
            console.log('Available options:', Array.from(roleSelect.options).map(opt => ({value: opt.value, text: opt.text})));
            
            roleSelect.value = user.role_id || '';
            console.log('Selected role value after setting:', roleSelect.value);
            
            // Update the enhanced select display
            if (roleSelect.__enhanced && roleSelect.__enhanced.setDisplayFromValue) {
                roleSelect.__enhanced.setDisplayFromValue();
            }
            
            // Force refresh of the select element
            roleSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Show edit modal
            showEditModal('editUserModal');
        } else {
            throw new Error(result.error || 'Failed to load user details');
        }
    } catch (error) {
        console.error('Error loading user details:', error);
        showToast(`Failed to load user details: ${error.message}`, 'error');
    }
}

// Function to load roles for edit user modal
async function loadRolesForEditUserModal() {
    try {
        const response = await fetch('../users/roles', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            const roles = result.data || [];
            const roleSelect = document.getElementById('editUserRole');
            
            console.log('Loading roles for edit modal:', roles);
            
            // Clear existing options
            roleSelect.innerHTML = '<option value="" disabled selected hidden></option>';
            
            // Add role options
            roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id;
                option.textContent = role.name;
                roleSelect.appendChild(option);
            });
            
            console.log('Roles loaded into dropdown, total options:', roleSelect.options.length);
            
            // Apply enhanced select functionality if not already applied
            if (!roleSelect.__enhanced) {
                console.log('Applying enhanced select to editUserRole');
                upgradeSelectToSearchable('editUserRole', 'Search roles...');
            }
            
            // Refresh the enhanced select display
            if (roleSelect.__enhanced && roleSelect.__enhanced.refresh) {
                roleSelect.__enhanced.refresh();
            }
        } else {
            throw new Error(result.error || 'Failed to load roles');
        }
    } catch (error) {
        console.error('Error loading roles for edit modal:', error);
        showToast(`Failed to load roles: ${error.message}`, 'error');
    }
}

// Function to submit edit user form
async function submitEditUserForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const data = {
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        username: formData.get('username'),
        email: formData.get('email'),
        name_in_urdu: formData.get('name_in_urdu'),
        address: formData.get('address'),
        cell: formData.get('cell'),
        ptcl: formData.get('ptcl'),
        role_id: formData.get('role_id')
    };

    // Add password only if provided
    const password = formData.get('password');
    if (password && password.trim() !== '') {
        data.password = password;
    }

    const userId = formData.get('id');

    try {
        const response = await fetch(`../users/${userId}`, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            showToast(`✅ User "${data.first_name} ${data.last_name}" has been updated successfully!`, 'success');
            closeEditModal('editUserModal');
            loadUsers(); // Reload table
        } else {
            throw new Error(result.error || 'Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(`❌ Failed to update user. ${error.message}`, 'error');
    }
}

// Function to delete user
async function deleteUser(userId) {
    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the user.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            const response = await fetch(`../users/${userId}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const deleteResult = await response.json();

            if (deleteResult.success) {
                showToast('✅ User deleted successfully!', 'success');
                loadUsers(); // Reload table
            } else {
                throw new Error(deleteResult.error || 'Failed to delete user');
            }
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showToast(`❌ Failed to delete user. ${error.message}`, 'error');
    }
}

// Function to handle search for users
function searchUsers() {
    const searchInput = document.getElementById('searchUserInput');
    const searchTerm = searchInput.value.toLowerCase();
    
    const rows = document.querySelectorAll('#usersTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ======================== EXPOSE FUNCTIONS TO WINDOW ========================

// Expose Tab 4 functions to window
window.editUser = editUser;
window.deleteUser = deleteUser;
window.submitEditUserForm = submitEditUserForm;
window.searchUsers = searchUsers;
window.submitUserForm = submitUserForm;



