(function() {
    'use strict';
    const API = '../api';
    function toast(msg, type) {
        const c = document.getElementById('toast-container');
        if (!c) return;
        const el = document.createElement('div');
        el.className = 'mb-2 px-4 py-2 rounded text-white text-sm ' + (type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500');
        el.textContent = msg;
        c.appendChild(el);
        setTimeout(function() { el.remove(); }, 3000);
    }

    // ---------- Enter key navigation (KMI-style): move to next field; on Create/Update button press Enter to save ----------
    var formSequences = {
        mainHeadForm: ['mainHeadName', 'mainHeadNameUrdu', 'mainHeadDescription', 'submitMainHeadBtn'],
        controlHeadForm: ['controlHeadMainHead', 'controlHeadName', 'controlHeadNameUrdu', 'controlHeadDescription', 'submitControlHeadBtn'],
        accountForm: ['accountMainHead', 'accountControlHead', 'accountAccountType', 'accountName', 'accountNameUrdu', 'accountCell', 'accountCity', 'accountAddress', 'accountCompanyName', 'accountCompanyAddress', 'accountNtn', 'accountStn', 'accountBank', 'accountCompanyType', 'accountPaymentTerm', 'accountOpeningBalance', 'accountDescription', 'submitAccountBtn'],
        editMainHeadForm: ['editMainHeadName', 'editMainHeadNameUrdu', 'editMainHeadDescription', 'updateMainHeadBtn'],
        editControlHeadForm: ['editControlHeadMainHead', 'editControlHeadName', 'editControlHeadNameUrdu', 'editControlHeadDescription', 'updateControlHeadBtn'],
        editAccountForm: ['editAccountMainHead', 'editAccountControlHead', 'editAccountAccountType', 'editAccountName', 'editAccountNameUrdu', 'editAccountCell', 'editAccountCity', 'editAccountAddress', 'editAccountCompanyName', 'editAccountCompanyAddress', 'editAccountNtn', 'editAccountStn', 'editAccountBank', 'editAccountCompanyType', 'editAccountPaymentTerm', 'editAccountOpeningBalance', 'editAccountDescription', 'updateAccountBtn']
    };
    function handleEnterNavigation(currentFieldId) {
        var currentForm = null, currentSequence = null;
        for (var formId in formSequences) {
            if (formSequences[formId].indexOf(currentFieldId) !== -1) {
                currentForm = formId;
                currentSequence = formSequences[formId];
                break;
            }
        }
        if (!currentForm || !currentSequence) return;
        var currentIndex = currentSequence.indexOf(currentFieldId);
        if (currentIndex === -1) return;
        var nextIndex = currentIndex + 1;
        if (nextIndex < currentSequence.length) {
            var nextFieldId = currentSequence[nextIndex];
            var nextField = document.getElementById(nextFieldId);
            if (nextField) {
                nextField.focus();
                if (nextField.select) try { nextField.select(); } catch (_) {}
            }
        } else if (currentFieldId.indexOf('submit') !== -1 || currentFieldId.indexOf('update') !== -1) {
            if (currentFieldId.indexOf('update') !== -1) {
                var updateBtn = document.getElementById(currentFieldId);
                if (updateBtn) updateBtn.click();
            } else {
                var form = document.getElementById(currentForm);
                if (form) form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.key !== 'NumpadEnter') return;
        if (e.target.tagName === 'TEXTAREA') return;
        if (e.target.id === 'updateMainHeadBtn' || e.target.id === 'updateControlHeadBtn' || e.target.id === 'updateAccountBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.target.click();
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var currentElement = e.target;
        var currentFieldId = currentElement.id;
        if (currentElement.tagName === 'BUTTON' && currentElement.type === 'submit') {
            var form = currentElement.closest('form');
            if (form && form.id && formSequences[form.id]) {
                var seq = formSequences[form.id];
                if (seq.indexOf(currentElement.id) !== -1) {
                    currentFieldId = currentElement.id;
                }
            }
        }
        if (currentFieldId && currentFieldId.trim() !== '') handleEnterNavigation(currentFieldId);
    });

    function tabSwitch() {
        document.querySelectorAll('.nav-link').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.nav-link').forEach(function(b) { b.classList.remove('active'); });
                document.querySelectorAll('.tab-pane').forEach(function(p) { p.style.display = 'none'; });
                this.classList.add('active');
                const pane = document.getElementById(tab);
                if (pane) { pane.style.display = 'block'; pane.classList.add('active'); }
                if (tab === 'tab1') loadMainHeads();
                if (tab === 'tab2') { loadMainHeadsForControl(); loadControlHeads(); }
                if (tab === 'tab3') { loadAccountDropdowns(); loadAccounts(); }
            });
        });
    }
    let mainHeadPage = 1, mainHeadLimit = 10, mainHeadSearch = '';
    let controlHeadPage = 1, controlHeadLimit = 10, controlHeadSearch = '';
    let accountPage = 1, accountLimit = 10, accountSearch = '', accountTypeFilter = '';

    function loadMainHeads() {
        const params = new URLSearchParams({ page: mainHeadPage, limit: mainHeadLimit, type: 'account', status: 'A' });
        if (mainHeadSearch) params.append('search', mainHeadSearch);
        fetch(API + '/main-heads?' + params, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) { toast(res.error || 'Failed', 'error'); return; }
                const data = res.data || res;
                const records = data.records || [];
                const total = data.total || 0;
                const tbody = document.getElementById('mainHeadsTableBody');
                const info = document.getElementById('mainHeadPaginationInfo');
                if (tbody) {
                    tbody.innerHTML = records.length === 0
                        ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No main heads. Create one with type Account.</td></tr>'
                        : records.map(function(m) {
                            return '<tr class="border-b border-gray-100 dark:border-[#172036]">' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (m.id || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (m.name || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (m.name_in_urdu || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (m.description || '') + '</td>' +
                                '<td class="px-[20px] py-[10px]">' +
                                '<button type="button" class="text-blue-500 hover:text-blue-600" onclick="window.editMainHead(' + m.id + ')" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>' +
                                (window.defaultRoleStatus === 'SUA' ? ' <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-1 rtl:mr-1" onclick="window.deleteMainHead(' + m.id + ')" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>' : '') + '</td></tr>';
                        }).join('');
                }
                if (info) info.textContent = total > 0 ? 'Total: ' + total : 'No records';
            })
            .catch(function() { toast('Failed to load main heads', 'error'); });
    }
    function loadMainHeadsForControl() {
        fetch(API + '/main-heads?type=account&status=A&limit=500', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = res.data && res.data.records ? res.data.records : (res.data || []);
                [['controlHeadMainHead', 'Main Head *'], ['editControlHeadMainHead', 'Main Head *']].forEach(function(pair) {
                    const sel = document.getElementById(pair[0]);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">' + pair[1] + '</option>';
                    list.forEach(function(m) { sel.innerHTML += '<option value="' + m.id + '">' + (m.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
    }
    function loadControlHeads() {
        const params = new URLSearchParams({ page: controlHeadPage, limit: controlHeadLimit, type: 'account', status: 'A' });
        if (controlHeadSearch) params.append('search', controlHeadSearch);
        fetch(API + '/control-heads?' + params, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) { toast(res.error || 'Failed', 'error'); return; }
                const data = res.data || res;
                const records = data.records || [];
                const total = data.total || 0;
                const tbody = document.getElementById('controlHeadsTableBody');
                const info = document.getElementById('controlHeadPaginationInfo');
                if (tbody) {
                    tbody.innerHTML = records.length === 0
                        ? '<tr><td colspan="5" class="px-[20px] py-[12px] text-gray-500 text-center">No control heads.</td></tr>'
                        : records.map(function(c) {
                            return '<tr class="border-b border-gray-100 dark:border-[#172036]">' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (c.id || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (c.main_head_name || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (c.name || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (c.name_in_urdu || '') + '</td>' +
                                '<td class="px-[20px] py-[10px]">' +
                                '<button type="button" class="text-blue-500 hover:text-blue-600" onclick="window.editControlHead(' + c.id + ')" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>' +
                                (window.defaultRoleStatus === 'SUA' ? ' <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-1 rtl:mr-1" onclick="window.deleteControlHead(' + c.id + ')" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>' : '') + '</td></tr>';
                        }).join('');
                }
                if (info) info.textContent = total > 0 ? 'Total: ' + total : 'No records';
            })
            .catch(function() { toast('Failed to load control heads', 'error'); });
    }
    function loadAccountDropdowns() {
        fetch(API + '/accounts/main-heads', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = res.data || [];
                ['accountMainHead', 'editAccountMainHead'].forEach(function(id) {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">Main Head *</option>';
                    list.forEach(function(m) { sel.innerHTML += '<option value="' + m.id + '">' + (m.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
        fetch(API + '/cities', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = (res.data && res.data.records) ? res.data.records : (res.data || []);
                ['accountCity', 'editAccountCity'].forEach(function(id) {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">City</option>';
                    (Array.isArray(list) ? list : []).forEach(function(c) { sel.innerHTML += '<option value="' + c.id + '">' + (c.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
        fetch(API + '/banks', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = (res.data && res.data.records) ? res.data.records : (res.data || []);
                ['accountBank', 'editAccountBank'].forEach(function(id) {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">Bank</option>';
                    (Array.isArray(list) ? list : []).forEach(function(b) { sel.innerHTML += '<option value="' + b.id + '">' + (b.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
        fetch(API + '/company-types', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = (res.data && res.data.records) ? res.data.records : (res.data || []);
                ['accountCompanyType', 'editAccountCompanyType'].forEach(function(id) {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">Company Type</option>';
                    (Array.isArray(list) ? list : []).forEach(function(ct) { sel.innerHTML += '<option value="' + ct.id + '">' + (ct.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
        fetch(API + '/payment-terms', { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                const list = (res.data && res.data.records) ? res.data.records : (res.data || []);
                ['accountPaymentTerm', 'editAccountPaymentTerm'].forEach(function(id) {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const curr = sel.value;
                    sel.innerHTML = '<option value="">Payment Terms</option>';
                    (Array.isArray(list) ? list : []).forEach(function(pt) { sel.innerHTML += '<option value="' + pt.id + '">' + (pt.name || '') + '</option>'; });
                    if (curr) sel.value = curr;
                });
            });
    }
    document.getElementById('accountMainHead') && document.getElementById('accountMainHead').addEventListener('change', function() {
        const mid = this.value;
        const sel = document.getElementById('accountControlHead');
        if (!sel) return;
        sel.innerHTML = '<option value="">Control Head *</option>';
        if (!mid) return;
        fetch(API + '/accounts/control-heads?main_head_id=' + encodeURIComponent(mid), { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                (res.data || []).forEach(function(c) { sel.innerHTML += '<option value="' + c.id + '">' + (c.name || '') + '</option>'; });
            });
    });
    document.getElementById('editAccountMainHead') && document.getElementById('editAccountMainHead').addEventListener('change', function() {
        const mid = this.value;
        const sel = document.getElementById('editAccountControlHead');
        if (!sel) return;
        sel.innerHTML = '<option value="">Control Head *</option>';
        if (!mid) return;
        fetch(API + '/accounts/control-heads?main_head_id=' + encodeURIComponent(mid), { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) return;
                (res.data || []).forEach(function(c) { sel.innerHTML += '<option value="' + c.id + '">' + (c.name || '') + '</option>'; });
            });
    });
    function loadAccounts() {
        const params = new URLSearchParams({ page: accountPage, limit: accountLimit });
        if (accountSearch) params.append('search', accountSearch);
        if (accountTypeFilter) params.append('account_type', accountTypeFilter);
        fetch(API + '/accounts?' + params, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success) { toast(res.error || 'Failed', 'error'); return; }
                const data = res.data || res;
                const records = data.records || [];
                const total = data.total || 0;
                const tbody = document.getElementById('accountsTableBody');
                const info = document.getElementById('accountPaginationInfo');
                if (tbody) {
                    tbody.innerHTML = records.length === 0
                        ? '<tr><td colspan="7" class="px-[20px] py-[12px] text-gray-500 text-center">No accounts. Run account_management_schema.sql and add accounts.</td></tr>'
                        : records.map(function(a) {
                            const head = (a.main_head_name || '') + ' / ' + (a.control_head_name || '');
                            const typeLabel = a.account_type === 'sale' ? 'Sale' : a.account_type === 'purchase' ? 'Purchase' : '-';
                            return '<tr class="border-b border-gray-100 dark:border-[#172036]">' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (a.source_id || a.code || a.id) + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + head + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + typeLabel + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (a.name || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (a.cell || '') + '</td>' +
                                '<td class="px-[20px] py-[10px] text-sm">' + (a.company_name || '') + '</td>' +
                                '<td class="px-[20px] py-[10px]">' +
                                '<button type="button" class="text-blue-500 hover:text-blue-600" onclick="window.editAccount(' + a.id + ')" title="Edit"><i class="material-symbols-outlined text-sm">edit</i></button>' +
                                (window.defaultRoleStatus === 'SUA' ? ' <button type="button" class="text-red-500 hover:text-red-600 ltr:ml-1 rtl:mr-1" onclick="window.deleteAccount(' + a.id + ')" title="Delete"><i class="material-symbols-outlined text-sm">delete</i></button>' : '') + '</td></tr>';
                        }).join('');
                }
                if (info) info.textContent = total > 0 ? 'Showing ' + records.length + ' of ' + total : 'No records';
            })
            .catch(function() { toast('Failed to load accounts', 'error'); });
    }
    // Main Head form
    document.getElementById('mainHeadForm') && document.getElementById('mainHeadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const payload = { name: document.getElementById('mainHeadName').value.trim(), name_in_urdu: document.getElementById('mainHeadNameUrdu').value.trim(), type: 'account', status: 'A', description: document.getElementById('mainHeadDescription').value.trim() };
        if (!payload.name || !payload.name_in_urdu) { toast('Name and Name (Urdu) required', 'error'); return; }
        const btn = document.getElementById('submitMainHeadBtn');
        if (btn) btn.disabled = true;
        fetch(API + '/main-heads', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Main head created', 'success'); document.getElementById('mainHeadForm').reset(); loadMainHeads(); loadMainHeadsForControl(); } else toast(res.error || 'Failed', 'error');
            })
            .catch(function() { toast('Failed', 'error'); })
            .finally(function() { if (btn) btn.disabled = false; });
    });
    window.editMainHead = function(id) {
        fetch(API + '/main-heads/' + id, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success || !res.data) { toast('Not found', 'error'); return; }
                const m = res.data;
                document.getElementById('editMainHeadId').value = m.id;
                document.getElementById('editMainHeadName').value = m.name || '';
                document.getElementById('editMainHeadNameUrdu').value = m.name_in_urdu || '';
                document.getElementById('editMainHeadDescription').value = m.description || '';
                document.getElementById('editMainHeadModal').classList.remove('opacity-0', 'pointer-events-none');
                document.getElementById('editMainHeadModal').style.display = 'block';
                document.getElementById('editMainHeadModal').classList.add('active');
                setTimeout(function() {
                    var first = document.getElementById('editMainHeadName');
                    if (first) { first.focus(); if (first.select) try { first.select(); } catch (_) {} }
                }, 100);
            });
    };
    window.deleteMainHead = function(id) {
        if (!confirm('Delete this main head?')) return;
        fetch(API + '/main-heads/' + id, { method: 'DELETE', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Deleted', 'success'); loadMainHeads(); loadMainHeadsForControl(); } else toast(res.error || 'Failed', 'error');
            });
    };
    document.getElementById('updateMainHeadBtn') && document.getElementById('updateMainHeadBtn').addEventListener('click', function() {
        const id = document.getElementById('editMainHeadId').value;
        const payload = { name: document.getElementById('editMainHeadName').value.trim(), name_in_urdu: document.getElementById('editMainHeadNameUrdu').value.trim(), description: document.getElementById('editMainHeadDescription').value.trim() };
        if (!id || !payload.name || !payload.name_in_urdu) { toast('Required fields missing', 'error'); return; }
        fetch(API + '/main-heads/' + id, { method: 'PUT', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Updated', 'success'); var m = document.getElementById('editMainHeadModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none'; loadMainHeads(); loadMainHeadsForControl(); } else toast(res.error || 'Failed', 'error');
            });
    });
    document.getElementById('closeEditMainHeadModal') && document.getElementById('closeEditMainHeadModal').addEventListener('click', function() {
        var m = document.getElementById('editMainHeadModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none';
    });
    // Control Head form
    document.getElementById('controlHeadForm') && document.getElementById('controlHeadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const mainHeadId = document.getElementById('controlHeadMainHead').value;
        const payload = { main_head_id: mainHeadId, name: document.getElementById('controlHeadName').value.trim(), name_in_urdu: document.getElementById('controlHeadNameUrdu').value.trim(), type: 'account', status: 'A', description: document.getElementById('controlHeadDescription').value.trim() };
        if (!mainHeadId || !payload.name || !payload.name_in_urdu) { toast('Main Head, Name and Name (Urdu) required', 'error'); return; }
        const btn = document.getElementById('submitControlHeadBtn');
        if (btn) btn.disabled = true;
        fetch(API + '/control-heads', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Control head created', 'success'); document.getElementById('controlHeadForm').reset(); loadControlHeads(); } else toast(res.error || 'Failed', 'error');
            })
            .catch(function() { toast('Failed', 'error'); })
            .finally(function() { if (btn) btn.disabled = false; });
    });
    window.editControlHead = function(id) {
        fetch(API + '/control-heads/' + id, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success || !res.data) { toast('Not found', 'error'); return; }
                const c = res.data;
                document.getElementById('editControlHeadId').value = c.id;
                document.getElementById('editControlHeadMainHead').value = c.main_head_id || '';
                document.getElementById('editControlHeadName').value = c.name || '';
                document.getElementById('editControlHeadNameUrdu').value = c.name_in_urdu || '';
                document.getElementById('editControlHeadDescription').value = c.description || '';
                loadMainHeadsForControl();
                setTimeout(function() { document.getElementById('editControlHeadMainHead').value = c.main_head_id || ''; }, 100);
                document.getElementById('editControlHeadModal').classList.remove('opacity-0', 'pointer-events-none');
                document.getElementById('editControlHeadModal').style.display = 'block';
                document.getElementById('editControlHeadModal').classList.add('active');
                setTimeout(function() {
                    var first = document.getElementById('editControlHeadMainHead');
                    if (first) first.focus();
                }, 150);
            });
    };
    window.deleteControlHead = function(id) {
        if (!confirm('Delete this control head?')) return;
        fetch(API + '/control-heads/' + id, { method: 'DELETE', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Deleted', 'success'); loadControlHeads(); } else toast(res.error || 'Failed', 'error');
            });
    };
    document.getElementById('updateControlHeadBtn') && document.getElementById('updateControlHeadBtn').addEventListener('click', function() {
        const id = document.getElementById('editControlHeadId').value;
        const payload = { main_head_id: document.getElementById('editControlHeadMainHead').value, name: document.getElementById('editControlHeadName').value.trim(), name_in_urdu: document.getElementById('editControlHeadNameUrdu').value.trim(), description: document.getElementById('editControlHeadDescription').value.trim() };
        if (!id || !payload.main_head_id || !payload.name || !payload.name_in_urdu) { toast('Required fields missing', 'error'); return; }
        fetch(API + '/control-heads/' + id, { method: 'PUT', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Updated', 'success'); var m = document.getElementById('editControlHeadModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none'; loadControlHeads(); } else toast(res.error || 'Failed', 'error');
            });
    });
    document.getElementById('closeEditControlHeadModal') && document.getElementById('closeEditControlHeadModal').addEventListener('click', function() {
        var m = document.getElementById('editControlHeadModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none';
    });
    // Account form
    document.getElementById('accountForm') && document.getElementById('accountForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const payload = {
            main_head_id: document.getElementById('accountMainHead').value,
            control_head_id: document.getElementById('accountControlHead').value,
            account_type: document.getElementById('accountAccountType').value || null,
            name: document.getElementById('accountName').value.trim(),
            name_in_urdu: document.getElementById('accountNameUrdu').value.trim(),
            cell: document.getElementById('accountCell').value.trim() || null,
            city_id: document.getElementById('accountCity').value || null,
            address: document.getElementById('accountAddress').value.trim() || null,
            company_name: document.getElementById('accountCompanyName').value.trim() || null,
            company_address: document.getElementById('accountCompanyAddress').value.trim() || null,
            ntn: document.getElementById('accountNtn').value.trim() || null,
            stn: document.getElementById('accountStn').value.trim() || null,
            bank_id: document.getElementById('accountBank').value || null,
            company_type_id: document.getElementById('accountCompanyType').value || null,
            payment_term_id: document.getElementById('accountPaymentTerm').value || null,
            opening_balance: document.getElementById('accountOpeningBalance').value ? parseFloat(document.getElementById('accountOpeningBalance').value) : null,
            description: document.getElementById('accountDescription').value.trim() || null
        };
        if (!payload.main_head_id || !payload.control_head_id || !payload.name) { toast('Main Head, Control Head and Name required', 'error'); return; }
        const btn = document.getElementById('submitAccountBtn');
        if (btn) btn.disabled = true;
        fetch(API + '/accounts', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Account created', 'success'); document.getElementById('accountForm').reset(); loadAccounts(); } else toast(res.error || (res.errors && res.errors.join(', ')) || 'Failed', 'error');
            })
            .catch(function() { toast('Failed', 'error'); })
            .finally(function() { if (btn) btn.disabled = false; });
    });
    window.editAccount = function(id) {
        fetch(API + '/accounts/' + id, { method: 'GET', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.success || !res.data) { toast('Not found', 'error'); return; }
                const a = res.data;
                document.getElementById('editAccountId').value = a.id;
                document.getElementById('editAccountMainHead').value = a.main_head_id || '';
                document.getElementById('editAccountControlHead').value = a.control_head_id || '';
                document.getElementById('editAccountAccountType').value = a.account_type || '';
                document.getElementById('editAccountName').value = a.name || '';
                document.getElementById('editAccountNameUrdu').value = a.name_in_urdu || '';
                document.getElementById('editAccountCell').value = a.cell || '';
                document.getElementById('editAccountCity').value = a.city_id || '';
                document.getElementById('editAccountAddress').value = a.address || '';
                document.getElementById('editAccountCompanyName').value = a.company_name || '';
                document.getElementById('editAccountCompanyAddress').value = a.company_address || '';
                document.getElementById('editAccountNtn').value = a.ntn || '';
                document.getElementById('editAccountStn').value = a.stn || '';
                document.getElementById('editAccountBank').value = a.bank_id || '';
                document.getElementById('editAccountCompanyType').value = a.company_type_id || '';
                document.getElementById('editAccountPaymentTerm').value = a.payment_term_id || '';
                document.getElementById('editAccountOpeningBalance').value = a.opening_balance != null ? a.opening_balance : '';
                document.getElementById('editAccountDescription').value = a.description || '';
                loadAccountDropdowns();
                setTimeout(function() {
                    document.getElementById('editAccountMainHead').value = a.main_head_id || '';
                    fetch(API + '/accounts/control-heads?main_head_id=' + (a.main_head_id || ''), { method: 'GET', credentials: 'same-origin' })
                        .then(function(r2) { return r2.json(); })
                        .then(function(res2) {
                            const sel = document.getElementById('editAccountControlHead');
                            sel.innerHTML = '<option value="">Control Head *</option>';
                            (res2.data || []).forEach(function(c) { sel.innerHTML += '<option value="' + c.id + '">' + (c.name || '') + '</option>'; });
                            sel.value = a.control_head_id || '';
                        });
                }, 200);
                document.getElementById('editAccountModal').classList.remove('opacity-0', 'pointer-events-none');
                document.getElementById('editAccountModal').style.display = 'block';
                document.getElementById('editAccountModal').classList.add('active');
                setTimeout(function() {
                    var first = document.getElementById('editAccountMainHead');
                    if (first) first.focus();
                }, 250);
            });
    };
    window.deleteAccount = function(id) {
        if (!confirm('Delete this account?')) return;
        fetch(API + '/accounts/' + id, { method: 'DELETE', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Deleted', 'success'); loadAccounts(); } else toast(res.error || 'Failed', 'error');
            });
    };
    document.getElementById('updateAccountBtn') && document.getElementById('updateAccountBtn').addEventListener('click', function() {
        const id = document.getElementById('editAccountId').value;
        const payload = {
            main_head_id: document.getElementById('editAccountMainHead').value,
            control_head_id: document.getElementById('editAccountControlHead').value,
            account_type: document.getElementById('editAccountAccountType').value || null,
            name: document.getElementById('editAccountName').value.trim(),
            name_in_urdu: document.getElementById('editAccountNameUrdu').value.trim(),
            cell: document.getElementById('editAccountCell').value.trim() || null,
            city_id: document.getElementById('editAccountCity').value || null,
            address: document.getElementById('editAccountAddress').value.trim() || null,
            company_name: document.getElementById('editAccountCompanyName').value.trim() || null,
            company_address: document.getElementById('editAccountCompanyAddress').value.trim() || null,
            ntn: document.getElementById('editAccountNtn').value.trim() || null,
            stn: document.getElementById('editAccountStn').value.trim() || null,
            bank_id: document.getElementById('editAccountBank').value || null,
            company_type_id: document.getElementById('editAccountCompanyType').value || null,
            payment_term_id: document.getElementById('editAccountPaymentTerm').value || null,
            opening_balance: document.getElementById('editAccountOpeningBalance').value ? parseFloat(document.getElementById('editAccountOpeningBalance').value) : null,
            description: document.getElementById('editAccountDescription').value.trim() || null
        };
        if (!id || !payload.main_head_id || !payload.control_head_id || !payload.name) { toast('Required fields missing', 'error'); return; }
        fetch(API + '/accounts/' + id, { method: 'PUT', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) { toast('Updated', 'success'); var m = document.getElementById('editAccountModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none'; loadAccounts(); } else toast(res.error || 'Failed', 'error');
            });
    });
    document.getElementById('closeEditAccountModal') && document.getElementById('closeEditAccountModal').addEventListener('click', function() {
        var m = document.getElementById('editAccountModal'); m.classList.add('opacity-0', 'pointer-events-none'); m.classList.remove('active'); m.style.display = 'none';
    });
    // Search and filter
    document.getElementById('searchMainHeadInput') && document.getElementById('searchMainHeadInput').addEventListener('input', function() { mainHeadSearch = this.value; mainHeadPage = 1; loadMainHeads(); });
    document.getElementById('searchControlHeadInput') && document.getElementById('searchControlHeadInput').addEventListener('input', function() { controlHeadSearch = this.value; controlHeadPage = 1; loadControlHeads(); });
    document.getElementById('searchAccountInput') && document.getElementById('searchAccountInput').addEventListener('input', function() { accountSearch = this.value; accountPage = 1; loadAccounts(); });
    document.getElementById('filterAccountType') && document.getElementById('filterAccountType').addEventListener('change', function() { accountTypeFilter = this.value; accountPage = 1; loadAccounts(); });
    tabSwitch();
    loadMainHeads();
})();
