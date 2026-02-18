/**
 * Misc Entries Management System - Professional Implementation
 * wanandles all CRUD operations, search, pagination, sorting, and export functionality
 */

// ======================== CUSTOM SCROLLBAR STYLES ========================
// Add custom scrollbar styles for dropdown
const style = document.createElement("style");
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

// ======================== GLOBAL CONFIGURATION ========================

const API_BASE = (typeof window !== "undefined" && window.API_BASE) || "../api";

const API_ENDPOINTS = {
  units: {
    list: `${API_BASE}/units`,
    create: `${API_BASE}/units`,
    update: `${API_BASE}/units`,
    delete: `${API_BASE}/units`,
  },
  unitTypes: {
    list: `${API_BASE}/unit-types`,
    create: `${API_BASE}/unit-types`,
    update: `${API_BASE}/unit-types`,
    delete: `${API_BASE}/unit-types`,
  },
  departments: {
    list: `${API_BASE}/departments`,
    create: `${API_BASE}/departments`,
    update: `${API_BASE}/departments`,
    delete: `${API_BASE}/departments`,
  },
  racks: {
    list: `${API_BASE}/racks`,
    create: `${API_BASE}/racks`,
    update: `${API_BASE}/racks`,
    delete: `${API_BASE}/racks`,
  },
  itemTypes: {
    list: `${API_BASE}/item-types`,
    create: `${API_BASE}/item-types`,
    update: `${API_BASE}/item-types`,
    delete: `${API_BASE}/item-types`,
  },
  cities: {
    list: `${API_BASE}/cities`,
    create: `${API_BASE}/cities`,
    update: `${API_BASE}/cities`,
    delete: `${API_BASE}/cities`,
  },
  sizes: {
    list: `${API_BASE}/sizes`,
    create: `${API_BASE}/sizes`,
    update: `${API_BASE}/sizes`,
    delete: `${API_BASE}/sizes`,
  },
  banks: {
    list: `${API_BASE}/banks`,
    create: `${API_BASE}/banks`,
    update: `${API_BASE}/banks`,
    delete: `${API_BASE}/banks`,
  },
  companyTypes: {
    list: `${API_BASE}/company-types`,
    create: `${API_BASE}/company-types`,
    update: `${API_BASE}/company-types`,
    delete: `${API_BASE}/company-types`,
  },
  paymentTerms: {
    list: `${API_BASE}/payment-terms`,
    create: `${API_BASE}/payment-terms`,
    update: `${API_BASE}/payment-terms`,
    delete: `${API_BASE}/payment-terms`,
  },
  brands: {
    list: `${API_BASE}/brands`,
    create: `${API_BASE}/brands`,
    update: `${API_BASE}/brands`,
    delete: `${API_BASE}/brands`,
  },
};

const state = {
  currentTab: "tab1",
  currentEditEntity: null,
  searchTimeouts: {},
  currentPage: {
    unit: 1,
    unitType: 1,
    department: 1,
    rack: 1,
    itemType: 1,
    city: 1,
    sizes: 1,
    bank: 1,
    companyType: 1,
    paymentTerm: 1,
    brand: 1,
  },
  itemsPerPage: 10,
  loadedTabs: [],
  isLoading: {
    unit: false,
    unitType: false,
    department: false,
    rack: false,
    itemType: false,
    city: false,
    sizes: false,
    bank: false,
    companyType: false,
    paymentTerm: false,
    brand: false,
  },
  // Store current unit filters for entities that support filtering
  unitFilters: {
    rack: null,
  },
};

// ======================== UTILITY FUNCTIONS ========================

/**
 * Helper function to reliably get the unit filter value from a select element
 * Works even when the select is disabled or uses enhanced select component
 */
function getUnitFilterValue(selectId) {
  const select = document.getElementById(selectId);
  if (!select) {
    console.warn(`Select element ${selectId} not found`);
    // If select not found but we have a default unit, use it
    if (typeof defaultUnitId !== 'undefined' && defaultUnitId) {
      return defaultUnitId;
    }
    return null;
  }

  // Try multiple methods to get the value
  let value = null;

  // Method 1: Direct value property
  if (select.value && select.value !== '') {
    value = select.value;
  }

  // Method 2: Get from selectedIndex if value is empty
  if ((!value || value === '') && select.selectedIndex >= 0 && select.options.length > 0) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption) {
      // Try value attribute first
      if (selectedOption.value && selectedOption.value !== '') {
        value = selectedOption.value;
      }
      // If still empty, try to extract from text like "[13]-Mankera"
      else if (selectedOption.textContent) {
        const match = selectedOption.textContent.match(/\[(\d+)\]/);
        if (match && match[1]) {
          value = match[1];
        }
      }
    }
  }

  // Method 3: Check all options for a selected one (in case selectedIndex is wrong)
  if ((!value || value === '') && select.options.length > 0) {
    for (let i = 0; i < select.options.length; i++) {
      const option = select.options[i];
      if (option.selected && option.value && option.value !== '') {
        value = option.value;
        break;
      }
    }
  }

  // Method 4: If still empty and this is rack filter, check for default unit
  if ((!value || value === '') && selectId === 'rackUnitFilter') {
    if (typeof defaultUnitId !== 'undefined' && defaultUnitId) {
      // Only use defaultUnitId if the select is disabled (which means it's locked to default unit)
      if (select.disabled) {
        value = String(defaultUnitId); // Ensure it's a string
        console.log(`Using defaultUnitId for disabled ${selectId}: ${value}`);
      }
    }
  }

  // Method 5: Try to extract unit ID from any selected option's text if value is still empty
  if ((!value || value === '') && select.options.length > 0) {
    for (let i = 0; i < select.options.length; i++) {
      const option = select.options[i];
      if (option.selected && option.textContent) {
        const match = option.textContent.match(/\[(\d+)\]/);
        if (match && match[1]) {
          value = match[1];
          console.log(`Extracted unit ID from selected option text: ${value}`);
          break;
        }
      }
    }
  }

  // Return null if empty string, otherwise return the value as string
  const result = value && value !== '' ? String(value) : null;
  console.log(`getUnitFilterValue(${selectId}): select.value="${select.value}", selectedIndex=${select.selectedIndex}, disabled=${select.disabled}, result=${result}`);
  return result;
}

function getEntityFromTab(tabId) {
  const tabMap = {
    tab1: "unit",
    tab3: "unitType",
    tab6: "department",
    tab7: "rack",
    tab8: "itemType",
    tab11: "city",
    tab13: "sizes",
    tab16: "bank",
    tab17: "companyType",
    tab18: "paymentTerm",
    tab19: "brand",
  };
  return tabMap[tabId];
}

function focusFirstFieldForEntity(entity) {
  const formIdMap = {
    unit: "unitForm",
    unitType: "unitTypeForm",
    department: "departmentForm",
    rack: "rackForm",
    itemType: "itemTypeForm",
    city: "cityForm",
    sizes: "sizesForm",
    bank: "bankForm",
    companyType: "companyTypeForm",
    paymentTerm: "paymentTermForm",
    brand: "brandForm",
  };
  const formId = formIdMap[entity];
  if (!formId) return;
  const form = document.getElementById(formId);
  if (!form) return;
  const raw = Array.from(form.querySelectorAll("input, select, textarea, button"));
  const controls = raw
    .map((el) => {
      if (el.tagName === "SELECT" && el.__enhanced && el.__enhanced.control) {
        return el.__enhanced.control;
      }
      return el;
    })
    .filter(
      (el, idx, arr) =>
        !el.disabled &&
        el.type !== "hidden" &&
        el.tabIndex !== -1 &&
        el.offsetParent !== null &&
        arr.indexOf(el) === idx
    );
  const first = controls[0];
  if (first) {
    try {
      first.focus();
      if (typeof first.select === "function") first.select();
    } catch (_) {}
  }
}

function getEndpointForEntity(entity) {
  const entityMap = {
    unit: "units",
    unittype: "unitTypes",
    department: "departments",
    rack: "racks",
    itemtype: "itemTypes",
    itemType: "itemTypes",
    city: "cities",
    sizes: "sizes",
    bank: "banks",
    companytype: "companyTypes",
    companyType: "companyTypes",
    paymentterm: "paymentTerms",
    paymentTerm: "paymentTerms",
    brand: "brands",
  };

  // Handle both lowercase and camelCase versions
  const normalizedEntity = entity.toLowerCase();
  return API_ENDPOINTS[entityMap[normalizedEntity]] || API_ENDPOINTS.units;
}

function getTableBodyId(entity) {
  const entityMap = {
    unit: "unitsTableBody",
    unittype: "unitTypesTableBody",
    department: "departmentsTableBody",
    rack: "racksTableBody",
    itemtype: "itemTypesTableBody",
    itemType: "itemTypesTableBody",
    city: "citiesTableBody",
    sizes: "sizesTableBody",
    bank: "banksTableBody",
    companytype: "companyTypesTableBody",
    paymentterm: "paymentTermsTableBody",
    brand: "brandsTableBody",
  };
  return entityMap[entity.toLowerCase()];
}

function getPaginationInfoId(entity) {
  const entityMap = {
    unit: "unitPaginationInfo",
    unittype: "unitTypePaginationInfo",
    department: "departmentPaginationInfo",
    rack: "rackPaginationInfo",
    itemtype: "itemTypePaginationInfo",
    itemType: "itemTypePaginationInfo",
    city: "cityPaginationInfo",
    sizes: "sizesPaginationInfo",
    bank: "bankPaginationInfo",
    companytype: "companyTypePaginationInfo",
    paymentterm: "paymentTermPaginationInfo",
    brand: "brandPaginationInfo",
  };
  return entityMap[entity.toLowerCase()];
}

function getPaginationControlsId(entity) {
  const entityMap = {
    unit: "unitPaginationControls",
    unittype: "unitTypePaginationControls",
    department: "departmentPaginationControls",
    rack: "rackPaginationControls",
    itemtype: "itemTypePaginationControls",
    itemType: "itemTypePaginationControls",
    city: "cityPaginationControls",
    sizes: "sizesPaginationControls",
    bank: "bankPaginationControls",
    companytype: "companyTypePaginationControls",
    paymentterm: "paymentTermPaginationControls",
    brand: "brandPaginationControls",
  };
  return entityMap[entity.toLowerCase()];
}

// ======================== DUPLICATE CHECKING ========================

async function checkDuplicateName(entity, name, excludeId = null) {
  try {
    const endpoint = getEndpointForEntity(entity);
    const url = `${endpoint.list}?search=${encodeURIComponent(name)}`;

    const response = await fetch(url, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data && data.data.records) {
      const records = data.data.records;
      // Check if any record has the same name (excluding current item if editing)
      return records.some(
        (record) =>
          record.name.toLowerCase() === name.toLowerCase() &&
          (!excludeId || record.id != excludeId)
      );
    }
    return false;
  } catch (error) {
    console.error("Error checking for duplicates:", error);
    return false; // Don't block submission if check fails
  }
}

async function checkDuplicateNameUrdu(entity, nameUrdu, excludeId = null) {
  try {
    const endpoint = getEndpointForEntity(entity);
    const url = `${endpoint.list}?search=${encodeURIComponent(nameUrdu)}`;

    const response = await fetch(url, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data && data.data.records) {
      const records = data.data.records;
      // Check if any record has the same Urdu name (excluding current item if editing)
      return records.some(
        (record) =>
          record.name_in_urdu &&
          record.name_in_urdu.toLowerCase() === nameUrdu.toLowerCase() &&
          (!excludeId || record.id != excludeId)
      );
    }
    return false;
  } catch (error) {
    console.error("Error checking for Urdu duplicates:", error);
    return false; // Don't block submission if check fails
  }
}

// ======================== FOCUS HELPERS ========================

// focus helpers moved to page scope (misc_entries.php)

// ======================== TOAST & ALERT FUNCTIONS ========================

// Unified toast styling
function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toast-container");
  if (!toastContainer) {
    return;
  }

  const toast = document.createElement("div");

  let bgColor, textColor, iconBg, borderColor, icon, shadowColor;
  switch (type) {
    case "success":
      bgColor = "#f0fdf4";
      textColor = "#166534";
      iconBg = "#16a34a";
      borderColor = "#bbf7d0";
      icon = "✓";
      shadowColor = "rgba(22, 163, 74, 0.15)";
      break;
    case "error":
      bgColor = "#fef2f2";
      textColor = "#991b1b";
      iconBg = "#dc2626";
      borderColor = "#fecaca";
      icon = "✗";
      shadowColor = "rgba(220, 38, 38, 0.15)";
      break;
    case "warning":
      bgColor = "#fffbeb";
      textColor = "#92400e";
      iconBg = "#d97706";
      borderColor = "#fed7aa";
      icon = "⚠";
      shadowColor = "rgba(217, 119, 6, 0.15)";
      break;
    case "info":
      bgColor = "#eff6ff";
      textColor = "#1e40af";
      iconBg = "#3b82f6";
      borderColor = "#bfdbfe";
      icon = "ℹ";
      shadowColor = "rgba(59, 130, 246, 0.15)";
      break;
    default:
      bgColor = "#f0fdf4";
      textColor = "#166534";
      iconBg = "#16a34a";
      borderColor = "#bbf7d0";
      icon = "✓";
      shadowColor = "rgba(22, 163, 74, 0.15)";
  }

  toast.style.cssText = `background:${bgColor};color:${textColor};padding:16px 20px;border-radius:12px;box-shadow:0 10px 15px -3px ${shadowColor},0 4px 6px -2px rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;position:relative;z-index:9999;min-width:300px;max-width:400px;font-weight:500;font-size:14px;border:1px solid ${borderColor};transform:translateX(100%);transition:all .3s cubic-bezier(.4,0,.2,1);letter-spacing:.025em;`;

  toast.innerHTML = `
        <div style="width:20px;height:20px;border-radius:50%;background:${iconBg};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;color:white;flex-shrink:0;box-shadow:0 2px 4px rgba(0,0,0,.1)">${icon}</div>
        <span style="flex:1;line-height:1.4">${message}</span>
        <button style="background:none;border:none;color:${textColor};cursor:pointer;font-size:18px;opacity:.6;transition:opacity .2s;padding:0;margin-left:8px;flex-shrink:0;width:20px;height:20px;display:flex;align-items:center;justify-content:center" onclick="this.parentElement.remove()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">×</button>`;

  toastContainer.appendChild(toast);
  setTimeout(() => {
    toast.style.transform = "translateX(0)";
  }, 100);
  setTimeout(() => {
    if (toastContainer.contains(toast)) {
      toast.style.transform = "translateX(100%)";
      setTimeout(() => {
        if (toastContainer.contains(toast)) {
          toastContainer.removeChild(toast);
        }
      }, 300);
    }
  }, 5000);
}

function showConfirmDialog(title, message, onConfirm, type = "warning") {
  console.log(`Confirm Dialog: ${title}`);

  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: title,
      text: message,
      icon: type,
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
      customClass: {
        popup: "swal2-popup-custom",
        title: "swal2-title-custom",
        content: "swal2-content-custom",
        confirmButton: "swal2-confirm-custom",
        cancelButton: "swal2-cancel-custom",
      },
      buttonsStyling: true,
    }).then((result) => {
      if (result.isConfirmed) {
        onConfirm();
      }
    });
  } else {
    console.warn("SweetAlert not loaded, using confirm fallback");
    if (confirm(`${title}\n\n${message}`)) {
      onConfirm();
    }
  }
}

// ======================== LOADING STATES ========================

function showTableLoading(entity) {
  console.log(`Showing loading for ${entity}`);
  const tableBody = document.getElementById(getTableBodyId(entity));
  if (tableBody) {
    state.isLoading[entity] = true;
    tableBody.innerHTML = `
            <tr>
                <td colspan="100%" class="px-[20px] py-[20px] text-center">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                        <span class="ml-3 text-gray-600">Loading ${entity} data...</span>
                    </div>
                </td>
            </tr>
        `;
  }
}

function hideTableLoading(entity) {
  state.isLoading[entity] = false;
}

function setButtonLoading(
  buttonId,
  loading = true,
  loadingText = "Processing..."
) {
  const button = document.getElementById(buttonId);
  if (!button) {
    console.warn(`Button ${buttonId} not found`);
    return;
  }

  const textSpan = button.querySelector("span");
  const iconElement = button.querySelector("i");

  if (loading) {
    button.disabled = true;
    button.classList.add("opacity-75", "cursor-not-allowed");
    if (textSpan) textSpan.textContent = loadingText;
    if (iconElement) {
      iconElement.textContent = "hourglass_empty";
      iconElement.classList.add("animate-spin");
    }
  } else {
    button.disabled = false;
    button.classList.remove("opacity-75", "cursor-not-allowed");
    if (iconElement) {
      iconElement.classList.remove("animate-spin");
    }
  }
}

// ======================== DATA LOADING FUNCTIONS ========================

async function loadEntityData(entity, searchTerm = "", page = 1, unitFilter = null) {
  console.log(`🟡 loadEntityData called: entity=${entity}, page=${page}, unitFilter=${unitFilter} (type: ${typeof unitFilter}), state.unitFilters.${entity}=${state.unitFilters[entity]} (type: ${typeof state.unitFilters[entity]})`);

  // If unitFilter is not provided, try to get from state
  if (entity === "rack" && (unitFilter === null || unitFilter === undefined)) {
    unitFilter = state.unitFilters[entity] || null;
    console.log(`   After checking state: unitFilter=${unitFilter}`);

    // If still null, try to get from DOM as last resort
    if ((unitFilter === null || unitFilter === undefined) && entity === "rack") {
      unitFilter = getUnitFilterValue("rackUnitFilter");
      console.log(`   After checking DOM as last resort: unitFilter=${unitFilter}`);
    }
  }

  // Convert to string and ensure it's not empty
  if (unitFilter !== null && unitFilter !== undefined) {
    unitFilter = String(unitFilter).trim();
    if (unitFilter === '') {
      unitFilter = null;
    }
  }

  // Update state with the current unit filter (always, to keep state in sync)
  if (entity === "rack") {
    state.unitFilters[entity] = unitFilter;
    console.log(`   Updated state.unitFilters.${entity}=${unitFilter}`);
  }

  console.log(
    `   Final: Loading ${entity} data - page: ${page}, search: "${searchTerm}", unitFilter: "${unitFilter}", state.unitFilters.${entity}: "${state.unitFilters[entity]}"`
  );

  try {
    showTableLoading(entity);

    const endpoint = getEndpointForEntity(entity);
    const params = new URLSearchParams({
      page: page,
      limit: state.itemsPerPage,
      search: searchTerm,
    });

    // Add unit filter for racks
    if (entity === "rack") {
      // Final check: if unitFilter is still null, try state one more time
      if (!unitFilter) {
        unitFilter = state.unitFilters[entity] || null;
        console.log(`   Final state check before params: unitFilter=${unitFilter}`);
      }

      if (unitFilter) {
        params.append("unit_id", unitFilter);
        console.log(`✅ Adding unit_id=${unitFilter} to request params`);
      } else {
        console.warn(`⚠️ WARNING: unitFilter is ${unitFilter} for ${entity}, NOT adding unit_id to params!`);
        console.warn(`   State check: state.unitFilters.${entity}=${state.unitFilters[entity]}`);
        console.warn(`   This means the API will return ALL records instead of filtered ones!`);
      }
    }

    console.log(`🌐 Fetching: ${endpoint.list}?${params}`);
    const response = await fetch(`${endpoint.list}?${params}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "same-origin",
    });

    const data = await response.json();
    console.log(`${entity} response:`, data);

    if (response.ok && data.success && data.data) {
      // Handle different response structures for different entities
      let records = [];
      if (data.data.records) {
        records = data.data.records;
      } else if (data.data.units) {
        records = data.data.units;
      } else if (data.units && Array.isArray(data.units)) {
        records = data.units;
      } else if (data.data.data && Array.isArray(data.data.data)) {
        records = data.data.data;
        console.log(`✅ Found ${entity} records in data.data.data structure`);
      } else if (Array.isArray(data.data)) {
        records = data.data;
      }

      console.log(`📊 Loading ${records.length} records for ${entity}`);
      if (records.length === 0 && data.data) {
        console.log(`⚠️ No records found. Response structure:`, data.data);
      }

      renderEntityTable(entity, records);

      // Handle different pagination structures for different entities
      let paginationData;
      if (data.data.page !== undefined) {
        // Standard paginated response
        paginationData = {
          current_page: data.data.page || 1,
          last_page: data.data.total_pages || 1,
          total: data.data.total || 0,
          from: (data.data.page - 1) * data.data.limit + 1,
          to: Math.min(data.data.page * data.data.limit, data.data.total),
        };
      } else {
        // Simple array response (like suppliers)
        paginationData = {
          current_page: 1,
          last_page: 1,
          total: records.length,
          from: records.length > 0 ? 1 : 0,
          to: records.length,
        };
      }

      console.log(`📄 Pagination data for ${entity}:`, paginationData);
      updatePagination(entity, paginationData);

      // showToast(`${entity.charAt(0).toUpperCase() + entity.slice(1)} data loaded successfully`, 'success');
    } else {
      // Handle both HTTP errors and API errors
      if (!response.ok) {
        // Try to get error message from response body
        if (data && data.error) {
          throw new Error(data.error);
        } else {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
      } else {
        // API returned success: false
        throw new Error(data.message || data.error || "Failed to load data");
      }
    }
  } catch (error) {
    // console.error(`Error loading ${entity} data:`, error);
    // showToast(`Failed to load ${entity} data: ${error.message}`, 'error');
    renderEntityTable(entity, []);
  } finally {
    hideTableLoading(entity);
  }
}

// Specific entity load functions
async function loadUnits(searchTerm = "", page = 1) {
  await loadEntityData("unit", searchTerm, page);
}

async function loadUnitTypes(searchTerm = "", page = 1) {
  await loadEntityData("unitType", searchTerm, page);
}

async function loadDepartments(searchTerm = "", page = 1) {
  await loadEntityData("department", searchTerm, page);
}

async function loadRacks(searchTerm = "", page = 1, unitId = null) {
  console.log(`🔵 loadRacks called: page=${page}, unitId=${unitId} (type: ${typeof unitId}), state.unitFilters.rack=${state.unitFilters.rack} (type: ${typeof state.unitFilters.rack})`);

  // If unitId is not provided (null or undefined), try to get from state first, then DOM
  if (unitId === null || unitId === undefined) {
    unitId = state.unitFilters.rack;
    console.log(`   After checking state: unitId=${unitId}`);
    if (unitId === null || unitId === undefined) {
      unitId = getUnitFilterValue("rackUnitFilter");
      console.log(`   After checking DOM: unitId=${unitId}`);
    }
  }

  // Convert to string and ensure it's not empty
  if (unitId !== null && unitId !== undefined) {
    unitId = String(unitId).trim();
    if (unitId === '') {
      unitId = null;
    }
  }

  // Always update state with the unit filter (even if null, to track the current state)
  state.unitFilters.rack = unitId;
  console.log(`   ✅ Final: unitId=${unitId}, state.unitFilters.rack=${state.unitFilters.rack}`);

  await loadEntityData("rack", searchTerm, page, unitId);
}

async function loadItemTypes(searchTerm = "", page = 1) {
  await loadEntityData("itemType", searchTerm, page);
}

async function loadCities(searchTerm = "", page = 1) {
  console.log("Loading cities...", { searchTerm, page });
  await loadEntityData("city", searchTerm, page);
}

async function loadSizes(searchTerm = "", page = 1) {
  console.log("Loading sizes...", { searchTerm, page });
  await loadEntityData("sizes", searchTerm, page);
}

// Load banks
async function loadBanks(searchTerm = "", page = 1) {
  console.log("Loading banks...", { searchTerm, page });
  await loadEntityData("bank", searchTerm, page);
}

// Load company types
async function loadCompanyTypes(searchTerm = "", page = 1) {
  console.log("Loading company types...", { searchTerm, page });
  await loadEntityData("companyType", searchTerm, page);
}

// Load payment terms
async function loadPaymentTerms(searchTerm = "", page = 1) {
  console.log("Loading payment terms...", { searchTerm, page });
  await loadEntityData("paymentTerm", searchTerm, page);
}

// Load brands
async function loadBrands(searchTerm = "", page = 1) {
  console.log("Loading brands...", { searchTerm, page });
  await loadEntityData("brand", searchTerm, page);
}

// Load units for dropdown
async function loadUnitsForDropdown() {
  try {
    console.log("Loading units for dropdown...");
    const response = await fetch(API_ENDPOINTS.units.list + "?limit=1000", {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();
    console.log("Units dropdown response:", data);

    if (response.ok && data.success && data.data && data.data.records) {
      const select = document.getElementById("siteUnitId");
      if (select) {
        select.innerHTML = '<option value=""></option>';
        data.data.records.forEach((unit) => {
          select.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
        });
        console.log(`Loaded ${data.data.records.length} units for dropdown`);
        // Upgrade to custom searchable select (Tailwind + JS, no libs)
        try {
          const opts = data.data.records.map((u) => ({
            value: String(u.id),
            text: String(u.name),
          }));
          setSelectOptions(select, opts);
          upgradeSelectToSearchable("siteUnitId", "Search units...");
          refreshSearchableSelectOptions("siteUnitId", opts);
        } catch (e) {
          /* no-op */
        }
      }
    }
  } catch (error) {
    // console.error('Error loading units for dropdown:', error);
    showToast("Failed to load units for dropdown", "error");
  }
}

async function loadUnitsForEditModal() {
  try {
    console.log("Loading units for edit modal...");
    const response = await fetch(API_ENDPOINTS.units.list + "?limit=1000", {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data && data.data.records) {
      const select = document.getElementById("editSiteUnitId");
      if (select) {
        select.innerHTML = '<option value=""></option>';
        data.data.records.forEach((unit) => {
          select.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
        });
        // Upgrade edit select to custom searchable dropdown
        try {
          const opts = data.data.records.map((u) => ({
            value: String(u.id),
            text: String(u.name),
          }));
          setSelectOptions(select, opts);
          upgradeSelectToSearchable("editSiteUnitId", "Search units...");
          refreshSearchableSelectOptions("editSiteUnitId", opts);
        } catch (e) {
          /* no-op */
        }
      }
    }
  } catch (error) {
    // console.error('Error loading units for edit modal:', error);
    showToast("Failed to load units for edit modal", "error");
  }
}

async function loadUnitsForDemandingPersonForm() {
  return; // Removed - Demanding Person tab was removed
}

async function loadUnitsForRackForm() {
  try {
    const response = await fetch(`${API_BASE}/units?all=true`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();
    console.log("Units response:", data);

    if (response.ok && data.success && data.data) {
      const units = data.data.records || data.data;
      console.log("Units loaded:", units);

      // Populate form select
      const formUnitSelect = document.getElementById("rackUnitId");
      const editUnitSelect = document.getElementById("editRackUnitId");
      const unitFilterSelect = document.getElementById("rackUnitFilter");

      // Sort units by ID in ascending order
      if (units && units.length > 0) {
        units.sort((a, b) => parseInt(a.id) - parseInt(b.id));
      }

      // Populate form select
      if (formUnitSelect) {
        formUnitSelect.innerHTML = '<option value="" disabled selected hidden></option>';
        units.forEach((unit) => {
          const option = document.createElement("option");
          option.value = unit.id;
          option.textContent = `[${unit.id}]-${unit.name}`;
          formUnitSelect.appendChild(option);
        });

        // Initialize enhanced select first
        try {
          if (formUnitSelect.__enhanced && typeof formUnitSelect.__enhanced.destroy === 'function') {
            formUnitSelect.__enhanced.destroy();
          }
          upgradeSelectToSearchable("rackUnitId", "Search units...");
        } catch (enhancedError) {
          console.warn("Enhanced select initialization failed for rack form:", enhancedError);
        }

        // Set default unit if user has one (after enhanced select is initialized)
        if (defaultUnitId) {
          console.log("Setting default unit for rack form:", defaultUnitId);

          // Use setTimeout to ensure enhanced select is fully ready
          setTimeout(() => {
            formUnitSelect.value = defaultUnitId;
            console.log("Rack form unit select value after setting:", formUnitSelect.value);

            // Make the form unit select read-only when user has a specific unit
            formUnitSelect.disabled = true;

            // Update enhanced select display
            if (formUnitSelect.__enhanced && typeof formUnitSelect.__enhanced.setDisplayFromValue === 'function') {
              formUnitSelect.__enhanced.setDisplayFromValue();
              console.log("Enhanced select display updated");
            }

            // Apply read-only styling to enhanced select if disabled
            if (formUnitSelect.__enhanced && formUnitSelect.__enhanced.control) {
              formUnitSelect.__enhanced.control.style.pointerEvents = 'none';
              formUnitSelect.__enhanced.control.classList.add('bg-gray-50', 'dark:bg-gray-800');
              formUnitSelect.__enhanced.control.style.cursor = 'not-allowed';
              formUnitSelect.__enhanced.control.title = 'Unit is automatically set based on your unit';
            }

            console.log("Final rack form unit select value:", formUnitSelect.value);
          }, 100);
        } else {
          console.log("No default unit ID found for rack form");
        }
      }

      // Populate edit modal select
      if (editUnitSelect) {
        editUnitSelect.innerHTML = '<option value="" disabled selected hidden></option>';
        units.forEach((unit) => {
          const option = document.createElement("option");
          option.value = unit.id;
          option.textContent = `[${unit.id}]-${unit.name}`;
          editUnitSelect.appendChild(option);
        });

        // Initialize enhanced select first
        try {
          if (editUnitSelect.__enhanced && typeof editUnitSelect.__enhanced.destroy === 'function') {
            editUnitSelect.__enhanced.destroy();
          }
          upgradeSelectToSearchable("editRackUnitId", "Search units...");
        } catch (enhancedError) {
          console.warn("Enhanced select initialization failed for rack edit modal:", enhancedError);
        }

        // Set default unit and make edit modal unit select read-only when user has a specific unit
        if (defaultUnitId) {
          editUnitSelect.value = defaultUnitId;
          editUnitSelect.disabled = true;

          // Update enhanced select display
          if (editUnitSelect.__enhanced && typeof editUnitSelect.__enhanced.setDisplayFromValue === 'function') {
            editUnitSelect.__enhanced.setDisplayFromValue();
          }

          // Apply read-only styling to enhanced select if disabled
          if (editUnitSelect.__enhanced && editUnitSelect.__enhanced.control) {
            editUnitSelect.__enhanced.control.style.pointerEvents = 'none';
            editUnitSelect.__enhanced.control.classList.add('bg-gray-50', 'dark:bg-gray-800');
            editUnitSelect.__enhanced.control.style.cursor = 'not-allowed';
            editUnitSelect.__enhanced.control.title = 'Unit is automatically set based on your unit';
          }
        }
      }

      // Populate filter select
      if (unitFilterSelect) {
        unitFilterSelect.innerHTML = '<option value="">All Units</option>';
        units.forEach((unit) => {
          const option = document.createElement("option");
          option.value = unit.id;
          option.textContent = `[${unit.id}]-${unit.name}`;
          unitFilterSelect.appendChild(option);
        });

        // Set default unit filter if user has one
        try {
          console.log("Checking defaultUnitId for rack filter:", defaultUnitId);
          if (defaultUnitId) {
            console.log(`Setting default unit filter for racks: ${defaultUnitId}`);
            unitFilterSelect.value = defaultUnitId;

            // Update enhanced select display
            if (unitFilterSelect.__enhanced && typeof unitFilterSelect.__enhanced.setDisplayFromValue === 'function') {
              unitFilterSelect.__enhanced.setDisplayFromValue();
            }

            // Make the filter read-only
            unitFilterSelect.disabled = true;
            if (unitFilterSelect.__enhanced && unitFilterSelect.__enhanced.control) {
              unitFilterSelect.__enhanced.control.style.pointerEvents = 'none';
              unitFilterSelect.__enhanced.control.classList.add('bg-gray-50', 'dark:bg-gray-800');
              unitFilterSelect.__enhanced.control.style.cursor = 'not-allowed';
              unitFilterSelect.__enhanced.control.title = 'Filter is automatically set based on your unit';
            }

            // Hide the "Filter by Unit" label when read-only
            const filterLabel = document.querySelector('label[for="rackUnitFilter"]');
            if (filterLabel) {
              filterLabel.style.display = 'none';
              console.log("Hidden Filter by Unit label for read-only filter");
            }

            // Load racks with the default unit filter
            loadRacks("", 1, defaultUnitId);
          } else {
            console.log("No default unit - allowing user to filter by any unit");
            unitFilterSelect.disabled = false;
            if (unitFilterSelect.__enhanced && unitFilterSelect.__enhanced.control) {
              unitFilterSelect.__enhanced.control.style.pointerEvents = 'auto';
              unitFilterSelect.__enhanced.control.classList.remove('bg-gray-50', 'dark:bg-gray-800');
              unitFilterSelect.__enhanced.control.style.cursor = 'pointer';
              unitFilterSelect.__enhanced.control.title = 'Filter racks by unit';
            }

            // Show the "Filter by Unit" label when not read-only
            const filterLabel = document.querySelector('label[for="rackUnitFilter"]');
            if (filterLabel) {
              filterLabel.style.display = 'block';
              console.log("Showing Filter by Unit label for editable filter");
            }
          }
        } catch (filterError) {
          console.warn("Rack unit filter setup failed:", filterError);
        }
      }

      console.log("Units loaded successfully for rack form");
    } else {
      console.error("Failed to load units:", data);
      showToast("Failed to load units", "error");
    }
  } catch (error) {
    console.error("Error loading units for rack form:", error);
    showToast("Failed to load units", "error");
  }
}

// ======================== TABLE RENDERING ========================

function renderEntityTable(entity, data) {
  console.log(`Rendering ${entity} table with ${data.length} records`);
  const tableBodyId = getTableBodyId(entity);
  console.log(`Looking for table body with ID: ${tableBodyId}`);
  const tableBody = document.getElementById(tableBodyId);

  if (!tableBody) {
    console.error(`Table body ${tableBodyId} not found`);
    return;
  }

  if (!data || data.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="100%" class="px-[20px] py-[20px] text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="material-symbols-outlined text-4xl text-gray-300 mb-2">inbox</i>
                        <span>No data found</span>
                    </div>
                </td>
            </tr>
        `;
    return;
  }

  tableBody.innerHTML = data
    .map((item) => renderTableRow(entity, item))
    .join("");
}

function renderTableRow(entity, item) {
  console.log("renderTableRow called with entity:", entity, "item:", item);
  const baseClass =
    "border-b border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors";
  const cellClass = "px-[20px] py-[5px] text-sm";

  // Convert entity name to permission format (camelCase to snake_case)
  const getPermissionEntity = (entity) => {
    const entityMap = {
      unit: "unit",
      site: "site",
      unitType: "unit_type",
      unittype: "unit_type",
      section: "section",
      demandingPerson: "demanding_person",
      demandingperson: "demanding_person",
      department: "department",
      rack: "rack",
      itemType: "item_type",
      itemtype: "item_type",
      demandType: "demand_type",
      demandtype: "demand_type",
      city: "city",
      productionQuality: "production_quality",
      productionquality: "production_quality",
      sizes: "sizes",
    };
    return entityMap[entity] || entity;
  };

  const permissionEntity = getPermissionEntity(entity);

  // Show edit and delete buttons based on permissions
  let editBtn = "";
  let deleteBtn = "";

  // Check if user has delete permission (SUA only)
  const canDelete = window.userPermissions === 'SUA';

  // Generate edit button for all entities
  if (entity.toLowerCase() === "unit") {
    editBtn = `
            <button onclick="editUnit(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (
    entity.toLowerCase() === "itemtype" ||
    entity.toLowerCase() === "itemType"
  ) {
    editBtn = `
            <button onclick="editItemType(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "city") {
    editBtn = `
            <button onclick="editCity(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "sizes") {
    editBtn = `
            <button onclick="editSizes(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "bank") {
    editBtn = `
            <button onclick="editBank(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "companytype" || entity.toLowerCase() === "companyType") {
    editBtn = `
            <button onclick="editCompanyType(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "paymentterm" || entity.toLowerCase() === "paymentTerm") {
    editBtn = `
            <button onclick="editPaymentTerm(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else if (entity.toLowerCase() === "brand") {
    editBtn = `
            <button onclick="editBrand(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
  } else {
    // For section, department, unitType - use generic editEntity function
    // For rack - use specific editRack function
    if (entity === "rack") {
      editBtn = `
            <button onclick="editRack(${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
    } else {
      editBtn = `
            <button onclick="editEntity('${entity}', ${item.id})" 
                    class="text-blue-500 hover:text-blue-600" 
                    title="Edit">
                <i class="material-symbols-outlined text-sm">edit</i>
            </button>
        `;
    }
  }

  // Generate delete button for all entities (only if user has delete permission)
  if (canDelete) {
    if (entity.toLowerCase() === "unit") {
      deleteBtn = `
            <!--<button onclick="deleteUnit(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (entity.toLowerCase() === "site") {
      deleteBtn = `
            <!--<button onclick="deleteSite(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (
      entity.toLowerCase() === "demandingperson" ||
      entity.toLowerCase() === "demandingPerson"
    ) {
      deleteBtn = `
            <!--<button onclick="deleteDemandingPerson(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (
      entity.toLowerCase() === "itemtype" ||
      entity.toLowerCase() === "itemType"
    ) {
      deleteBtn = `
            <!--<button onclick="deleteItemType(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (
      entity.toLowerCase() === "demandtype" ||
      entity.toLowerCase() === "demandType"
    ) {
      deleteBtn = `
            <!--<button onclick="deleteDemandType(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (entity.toLowerCase() === "city") {
      deleteBtn = `
            <!--<button onclick="deleteCity(${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    } else if (entity.toLowerCase() === "productionquality") {
      deleteBtn = ``;
    } else if (entity.toLowerCase() === "sizes") {
      deleteBtn = ``;
    } else if (entity.toLowerCase() === "bank") {
      deleteBtn = `
            <button onclick="deleteEntity('bank', ${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>
        `;
    } else if (entity.toLowerCase() === "companytype" || entity.toLowerCase() === "companyType") {
      deleteBtn = `
            <button onclick="deleteEntity('companyType', ${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>
        `;
    } else if (entity.toLowerCase() === "paymentterm" || entity.toLowerCase() === "paymentTerm") {
      deleteBtn = `
            <button onclick="deleteEntity('paymentTerm', ${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>
        `;
    } else if (entity.toLowerCase() === "brand") {
      deleteBtn = `
            <button onclick="deleteEntity('brand', ${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>
        `;
    } else {
      // For section, department, rack, unitType - use generic deleteEntity function
      deleteBtn = `
            <!--<button onclick="deleteEntity('${entity}', ${item.id})" 
                    class="text-red-500 hover:text-red-600" 
                    title="Delete">
                <i class="material-symbols-outlined text-sm">delete</i>
            </button>-->
        `;
    }
  } else {
    // User doesn't have delete permission, don't show delete button
    deleteBtn = ``;
  }

  // Only show action column if there are any action buttons
  const hasAnyActions = editBtn || deleteBtn;
  const actionButtons = hasAnyActions
    ? `<div class="flex items-center gap-[9px]">${editBtn}${deleteBtn}</div>`
    : "";

  switch (entity.toLowerCase()) {
    case "unit":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "unittype":
    case "unitType":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "department":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "rack":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.unit_name || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "itemtype":
    case "itemType":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "city":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "sizes":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${actionButtons || ""}</td>
                </tr>
            `;
    case "bank":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "companytype":
    case "companyType":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "paymentterm":
    case "paymentTerm":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    <td class="${cellClass}">${item.description || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    case "brand":
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
    default:
      return `
                <tr class="${baseClass}">
                    <td class="${cellClass} font-medium">${item.id || ""}</td>
                    <td class="${cellClass}">${item.name || ""}</td>
                    <td class="${cellClass}">${item.name_in_urdu || ""}</td>
                    ${hasAnyActions
          ? `<td class="${cellClass}">${actionButtons}</td>`
          : ""
        }
                </tr>
            `;
  }
}

// ======================== PAGINATION ========================

function updatePagination(entity, pagination) {
  console.log(`🔍 Updating pagination for ${entity}:`, pagination);
  console.log(`📍 Looking for info element: ${getPaginationInfoId(entity)}`);
  console.log(
    `📍 Looking for controls element: ${getPaginationControlsId(entity)}`
  );

  const infoElement = document.getElementById(getPaginationInfoId(entity));
  const controlsElement = document.getElementById(
    getPaginationControlsId(entity)
  );

  console.log("✅ Info element found:", infoElement);
  console.log("✅ Controls element found:", controlsElement);

  if (infoElement) {
    const infoText = `Showing ${pagination.from || 0} to ${pagination.to || 0
      } of ${pagination.total || 0} entries`;
    infoElement.textContent = infoText;
    console.log(`📝 Updated info text: ${infoText}`);
  } else {
    console.error(`❌ Info element not found: ${getPaginationInfoId(entity)}`);
  }

  if (controlsElement) {
    const paginationHTML = generatePaginationControls(entity, pagination);
    console.log(`🎛️ Generated pagination HTML:`, paginationHTML);
    controlsElement.innerHTML = paginationHTML;
    // Attach click handlers
    // Force-active current page styling in case class merging conflicts
    const activeAnchor = controlsElement.querySelector(
      `a[data-page="${pagination.current_page}"]`
    );
    if (activeAnchor) {
      activeAnchor.classList.add(
        "bg-primary-500",
        "text-white",
        "border-primary-500"
      );
      activeAnchor.setAttribute("aria-current", "page");
    }

    controlsElement.querySelectorAll("a[data-page]").forEach((a) => {
      const targetPage = parseInt(a.getAttribute("data-page"), 10);
      a.addEventListener("click", (e) => {
        e.preventDefault();
        if (
          !Number.isNaN(targetPage) &&
          targetPage > 0 &&
          targetPage !== pagination.current_page &&
          targetPage <= pagination.last_page
        ) {
          changePage(entity, targetPage);
        }
      });
    });
    console.log("✅ Pagination controls updated");
  } else {
    console.error(
      `❌ Controls element not found: ${getPaginationControlsId(entity)}`
    );
  }
}

function generatePaginationControls(entity, pagination) {
  let html = "";
  const currentPage = parseInt(pagination.current_page, 10) || 1;
  const lastPage = parseInt(pagination.last_page, 10) || 1;

  const btnCls =
    "w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-gray-100 dark:border-[#172036] transition-all hover:bg-primary-500 hover:text-white hover:border-primary-500";
  const activeCls =
    "w-[31px] h-[31px] block leading-[29px] relative text-center rounded-md border border-primary-500 bg-primary-500 text-white";

  // Single page case
  if (lastPage <= 1) {
    html += `
            <li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <a href="javascript:void(0);" class="${activeCls} pointer-events-none" data-page="1">1</a>
            </li>`;
    return html;
  }

  // Prev (always visible; disabled on first page)
  html += `
        <li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
            <a href="javascript:void(0);" data-page="${currentPage - 1
    }" class="${btnCls} ${currentPage <= 1 ? "opacity-50 pointer-events-none" : ""
    }">
                <span class="opacity-0">0</span>
                <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_left</i>
            </a>
        </li>`;

  // Numbered buttons window
  const maxButtons = 5;
  let startBtn = Math.max(1, currentPage - Math.floor((maxButtons - 1) / 2));
  let endBtn = Math.min(lastPage, startBtn + maxButtons - 1);
  startBtn = Math.max(1, Math.min(startBtn, endBtn - maxButtons + 1));
  for (let p = startBtn; p <= endBtn; p++) {
    const isActive = p === currentPage;
    const cls = isActive ? activeCls : btnCls;
    const aria = isActive ? ' aria-current="page"' : "";
    html += `
            <li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
                <a href="javascript:void(0);" data-page="${p}" class="${cls}"${aria}>${p}</a>
            </li>`;
  }

  // Next (always visible; disabled on last page)
  html += `
        <li class="inline-block mx-[1px] ltr:first:ml-0 ltr:last:mr-0 rtl:first:mr-0 rtl:last:ml-0">
            <a href="javascript:void(0);" data-page="${currentPage + 1
    }" class="${btnCls} ${currentPage >= lastPage ? "opacity-50 pointer-events-none" : ""
    }">
                <span class="opacity-0">0</span>
                <i class="material-symbols-outlined left-0 right-0 absolute top-1/2 -translate-y-1/2">chevron_right</i>
            </a>
        </li>`;

  return html;
}

function changePage(entity, page) {
  console.log(`Changing page for ${entity} to ${page}`);
  state.currentPage[entity] = page;

  const searchInputId = `search${entity.charAt(0).toUpperCase() + entity.slice(1)
    }Input`;
  const searchInput = document.getElementById(searchInputId);
  const searchTerm = searchInput ? searchInput.value : "";

  const loadFunctions = {
    unit: loadUnits,
    unitType: loadUnitTypes,
    department: loadDepartments,
    rack: loadRacks,
    itemType: loadItemTypes,
    city: loadCities,
    sizes: loadSizes,
    bank: loadBanks,
    companyType: loadCompanyTypes,
    paymentTerm: loadPaymentTerms,
    brand: loadBrands,
  };

  if (loadFunctions[entity]) {
    if (entity === "rack") {
      // Get current unit filter for racks - try state first, then DOM
      let unitFilter = state.unitFilters.rack;
      console.log(`🔴 changePage for rack: Initial state.unitFilters.rack=${unitFilter} (type: ${typeof unitFilter})`);
      if (unitFilter === null || unitFilter === undefined) {
        unitFilter = getUnitFilterValue("rackUnitFilter");
        console.log(`   After getUnitFilterValue: unitFilter=${unitFilter} (type: ${typeof unitFilter})`);
        // If we got a value from DOM, update state
        if (unitFilter !== null && unitFilter !== undefined) {
          unitFilter = String(unitFilter).trim();
          if (unitFilter !== '') {
            state.unitFilters.rack = unitFilter;
            console.log(`   Updated state.unitFilters.rack=${unitFilter}`);
          } else {
            unitFilter = null;
          }
        }
      } else {
        // Ensure it's a string
        unitFilter = String(unitFilter).trim();
        if (unitFilter === '') {
          unitFilter = null;
        }
      }
      console.log(`🟢 changePage: Calling loadRacks with page=${page}, unitFilter=${unitFilter}, state.unitFilters.rack=${state.unitFilters.rack}`);
      // Always pass the unitFilter (even if null) - loadRacks will handle it
      loadFunctions[entity](searchTerm, page, unitFilter);
    } else {
      loadFunctions[entity](searchTerm, page);
    }
  }
}

// ======================== FORM HANDLING ========================

async function handleFormSubmit(
  entity,
  formData,
  isEdit = false,
  editId = null
) {
  console.log(`Submitting ${entity} form:`, { formData, isEdit, editId });

  try {
    const endpoint = getEndpointForEntity(entity);
    const url = isEdit ? `${endpoint.update}/${editId}` : endpoint.create;
    const method = isEdit ? "PUT" : "POST";

    console.log(`${method} ${url}`, formData);

    const response = await fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "same-origin",
      body: JSON.stringify(formData),
    });

    const data = await response.json();
    console.log(`${entity} form response:`, data);

    if (response.ok && data.success) {
      showToast(
        `${entity.charAt(0).toUpperCase() + entity.slice(1)} ${isEdit ? "updated" : "created"
        } successfully! 🎉`,
        "success"
      );

      // Reload data
      const loadFunctions = {
        unit: loadUnits,
        unitType: loadUnitTypes,
        department: loadDepartments,
        rack: loadRacks,
        itemType: loadItemTypes,
        city: loadCities,
        sizes: loadSizes,
        bank: loadBanks,
        companyType: loadCompanyTypes,
        paymentTerm: loadPaymentTerms,
        brand: loadBrands,
      };

      if (loadFunctions[entity]) {
        if (entity === "rack") {
          // Get current unit filter for racks - try state first, then DOM
          let unitFilter = state.unitFilters.rack;
          if (!unitFilter) {
            unitFilter = getUnitFilterValue("rackUnitFilter");
          }
          console.log(`Form submit: Reloading racks with unit filter: ${unitFilter} (from state: ${state.unitFilters.rack})`);
          loadFunctions[entity]("", 1, unitFilter);
        } else {
          loadFunctions[entity]();
        }
      }

      return true;
    } else {
      // Handle both HTTP errors and API errors
      if (!response.ok) {
        // Try to get error message from response body
        if (data && data.error) {
          throw new Error(data.error);
        } else {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
      } else {
        // API returned success: false
        throw new Error(
          data.message ||
          data.error ||
          `Failed to ${isEdit ? "update" : "create"} ${entity}`
        );
      }
    }
  } catch (error) {
    // console.error(`Error ${isEdit ? 'updating' : 'creating'} ${entity}:`, error);
    showToast(
      `Failed to ${isEdit ? "update" : "create"} ${entity}: ${error.message}`,
      "error"
    );
    return false;
  }
}

// ======================== CRUD OPERATIONS ========================

async function editUnit(id) {
  console.log(`Editing unit ${id}`);
  try {
    const response = await fetch(`${API_ENDPOINTS.units.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();
    console.log("Edit unit response:", data);

    if (response.ok && data.success && data.data) {
      const unit = data.data;

      document.getElementById("editUnitId").value = unit.id || "";
      document.getElementById("editUnitName").value = unit.name || "";
      document.getElementById("editUnitNameUrdu").value =
        unit.name_in_urdu || "";
      document.getElementById("editUnitDescription").value =
        unit.description || "";

      showEditModal("editUnitModal");
      // showToast('Unit loaded for editing', 'info');
    } else {
      // Handle both HTTP errors and API errors
      if (!response.ok) {
        // Try to get error message from response body
        if (data && data.error) {
          throw new Error(data.error);
        } else {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
      } else {
        // API returned success: false
        throw new Error(
          data.message || data.error || "Failed to load unit data"
        );
      }
    }
  } catch (error) {
    // console.error('Error loading unit for edit:', error);
    showToast(`Failed to load unit data: ${error.message}`, "error");
  }
}

async function editEntity(entity, id) {
  console.log(`Editing ${entity} ${id}`);
  try {
    const endpoint = getEndpointForEntity(entity);
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const item = data.data;
      const entityTitle =
        entity.charAt(0).toUpperCase() +
        entity.slice(1).replace(/([A-Z])/g, " $1");

      document.getElementById(
        "editGenericModalTitle"
      ).textContent = `Edit ${entityTitle}`;
      document.getElementById("editGenericId").value = item.id || "";
      document.getElementById("editGenericName").value = item.name || "";
      document.getElementById("editGenericNameUrdu").value =
        item.name_in_urdu || "";
      const editDescEl = document.getElementById("editGenericDescription");
      if (editDescEl) {
        editDescEl.value = item.description || "";
      }

      state.currentEditEntity = entity;

      showEditModal("editGenericModal");
      // showToast(`${entityTitle} loaded for editing`, 'info');
    } else {
      // Handle both HTTP errors and API errors
      if (!response.ok) {
        // Try to get error message from response body
        if (data && data.error) {
          throw new Error(data.error);
        } else {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
      } else {
        // API returned success: false
        throw new Error(
          data.message || data.error || `Failed to load ${entity} data`
        );
      }
    }
  } catch (error) {
    // console.error(`Error loading ${entity} for edit:`, error);
    showToast(`Failed to load ${entity} data: ${error.message}`, "error");
  }
}

async function editRack(id) {
  console.log(`Editing rack ${id}`);
  try {
    const response = await fetch(`${API_ENDPOINTS.racks.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const rack = data.data;

      document.getElementById("editRackId").value = rack.id || "";
      document.getElementById("editRackName").value = rack.name || "";
      document.getElementById("editRackNameUrdu").value = rack.name_in_urdu || "";
      document.getElementById("editRackDescription").value = rack.description || "";

      // Set unit value
      const unitSelect = document.getElementById("editRackUnitId");
      if (unitSelect && rack.unit_id) {
        unitSelect.value = rack.unit_id;
        // Trigger enhanced select update if it exists
        if (unitSelect.__enhanced && typeof unitSelect.__enhanced.setDisplayFromValue === 'function') {
          unitSelect.__enhanced.setDisplayFromValue();
        }
      } else if (unitSelect && defaultUnitId) {
        // If no unit_id in rack data but user has default unit, set it
        unitSelect.value = defaultUnitId;
        if (unitSelect.__enhanced && typeof unitSelect.__enhanced.setDisplayFromValue === 'function') {
          unitSelect.__enhanced.setDisplayFromValue();
        }
      }

      // Make unit select read-only if user has a default unit
      if (unitSelect && defaultUnitId) {
        unitSelect.disabled = true;

        // Apply read-only styling to enhanced select if disabled
        if (unitSelect.__enhanced && unitSelect.__enhanced.control) {
          unitSelect.__enhanced.control.style.pointerEvents = 'none';
          unitSelect.__enhanced.control.classList.add('bg-gray-50', 'dark:bg-gray-800');
          unitSelect.__enhanced.control.style.cursor = 'not-allowed';
          unitSelect.__enhanced.control.title = 'Unit is automatically set based on your unit';
        }

        console.log("Edit rack modal - unit select disabled for user with default unit:", defaultUnitId);
      } else if (unitSelect) {
        // Make sure it's enabled if user doesn't have a default unit
        unitSelect.disabled = false;

        // Remove read-only styling from enhanced select
        if (unitSelect.__enhanced && unitSelect.__enhanced.control) {
          unitSelect.__enhanced.control.style.pointerEvents = 'auto';
          unitSelect.__enhanced.control.classList.remove('bg-gray-50', 'dark:bg-gray-800');
          unitSelect.__enhanced.control.style.cursor = 'pointer';
          unitSelect.__enhanced.control.title = 'Select unit for this rack';
        }

        console.log("Edit rack modal - unit select enabled for user without default unit");
      }

      showEditModal("editRackModal");
    } else {
      // Handle both HTTP errors and API errors
      if (!response.ok) {
        // Try to get error message from response body
        if (data && data.error) {
          throw new Error(data.error);
        } else {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
      } else {
        // API returned success: false
        throw new Error(
          data.message || data.error || "Failed to load rack data"
        );
      }
    }
  } catch (error) {
    console.error("Error loading rack for edit:", error);
    showToast(
      `Failed to load rack data: ${error.message}`,
      "error"
    );
  }
}

async function editSizes(id) {
  console.log(`Editing size ${id}`);
  try {
    const endpoint = getEndpointForEntity("sizes");
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const size = data.data;

      // Populate the edit form
      document.getElementById("editSizesId").value = size.id;
      document.getElementById("editSizesName").value = size.name;

      // Show the edit modal
      showEditModal("editSizesModal");
    } else {
      showToast(
        data.message || data.error || "Failed to load size data",
        "error"
      );
    }
  } catch (error) {
    console.error("Error loading size for edit:", error);
    showToast(
      `Failed to load size data: ${error.message}`,
      "error"
    );
  }
}

// Edit bank
async function editBank(id) {
  console.log(`Editing bank ${id}`);
  try {
    const endpoint = getEndpointForEntity("bank");
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const bank = data.data;

      // Populate the edit form
      document.getElementById("editBankId").value = bank.id;
      document.getElementById("editBankName").value = bank.name || "";
      document.getElementById("editBankNameUrdu").value = bank.name_in_urdu || "";
      document.getElementById("editBankDescription").value = bank.description || "";

      // Show the modal
      showEditModal("editBankModal");
    } else {
      showToast(
        data.message || data.error || "Failed to load bank data",
        "error"
      );
    }
  } catch (error) {
    console.error("Error loading bank:", error);
    showToast("Failed to load bank data", "error");
  }
}

// Edit company type
async function editCompanyType(id) {
  console.log(`Editing company type ${id}`);
  try {
    const endpoint = getEndpointForEntity("companyType");
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const companyType = data.data;

      // Populate the edit form
      document.getElementById("editCompanyTypeId").value = companyType.id;
      document.getElementById("editCompanyTypeName").value = companyType.name || "";
      document.getElementById("editCompanyTypeNameUrdu").value = companyType.name_in_urdu || "";
      document.getElementById("editCompanyTypeDescription").value = companyType.description || "";

      // Show the modal
      showEditModal("editCompanyTypeModal");
    } else {
      showToast(
        data.message || data.error || "Failed to load company type data",
        "error"
      );
    }
  } catch (error) {
    console.error("Error loading company type:", error);
    showToast("Failed to load company type data", "error");
  }
}

// Edit payment term
async function editPaymentTerm(id) {
  console.log(`Editing payment term ${id}`);
  try {
    const endpoint = getEndpointForEntity("paymentTerm");
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const paymentTerm = data.data;

      // Populate the edit form
      document.getElementById("editPaymentTermId").value = paymentTerm.id;
      document.getElementById("editPaymentTermName").value = paymentTerm.name || "";
      document.getElementById("editPaymentTermNameUrdu").value = paymentTerm.name_in_urdu || "";
      document.getElementById("editPaymentTermDescription").value = paymentTerm.description || "";

      // Show the modal
      showEditModal("editPaymentTermModal");
    } else {
      showToast(
        data.message || data.error || "Failed to load payment term data",
        "error"
      );
    }
  } catch (error) {
    console.error("Error loading payment term:", error);
    showToast("Failed to load payment term data", "error");
  }
}

// Edit brand
async function editBrand(id) {
  console.log(`Editing brand ${id}`);
  try {
    const endpoint = getEndpointForEntity("brand");
    const response = await fetch(`${endpoint.list}/${id}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    });

    const data = await response.json();

    if (response.ok && data.success && data.data) {
      const brand = data.data;

      // Populate the edit form
      document.getElementById("editBrandId").value = brand.id;
      document.getElementById("editBrandName").value = brand.name || "";

      // Show the modal
      showEditModal("editBrandModal");
    } else {
      showToast(
        data.message || data.error || "Failed to load brand data",
        "error"
      );
    }
  } catch (error) {
    console.error("Error loading brand:", error);
    showToast("Failed to load brand data", "error");
  }
}

// Delete functions
async function deleteUnit(id) {
  console.log(`Deleting unit ${id}`);
  showConfirmDialog(
    "Delete Unit",
    "Are you sure you want to delete this unit? This action cannot be undone.",
    async () => {
      try {
        const response = await fetch(`${API_ENDPOINTS.units.delete}/${id}`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        });

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Unit deleted successfully! ✅", "success");
          loadUnits();
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message || data.error || "Failed to delete unit"
            );
          }
        }
      } catch (error) {
        // console.error('Error deleting unit:', error);
        showToast(`Failed to delete unit: ${error.message}`, "error");
      }
    }
  );
}

async function deleteSite(id) {
  console.log(`Deleting site ${id}`);
  showConfirmDialog(
    "Delete Site",
    "Are you sure you want to delete this site? This action cannot be undone.",
    async () => {
      try {
        const response = await fetch(`${API_ENDPOINTS.sites.delete}/${id}`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        });

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Site deleted successfully! ✅", "success");
          loadSites();
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message || data.error || "Failed to delete site"
            );
          }
        }
      } catch (error) {
        // console.error('Error deleting site:', error);
        showToast(`Failed to delete site: ${error.message}`, "error");
      }
    }
  );
}

async function deleteDemandingPerson(id) {
  console.log(`Deleting demanding person ${id}`);

  showConfirmDialog(
    "Delete Demanding Person",
    "Are you sure you want to delete this demanding person? This action cannot be undone.",
    async () => {
      try {
        const response = await fetch(
          `${API_ENDPOINTS.demandingPersons.delete}/${id}`,
          {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
          }
        );

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Demanding person deleted successfully! ✅", "success");
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message || data.error || "Failed to delete demanding person"
            );
          }
        }
      } catch (error) {
        showToast(
          `Failed to delete demanding person: ${error.message}`,
          "error"
        );
      }
    }
  );
}

async function deleteSupplier(id) {
  console.log(`Deleting supplier ${id}`);

  showConfirmDialog(
    "Delete Supplier",
    "Are you sure you want to delete this supplier? This action cannot be undone.",
    async () => {
      try {
        const response = await fetch(
          `${API_ENDPOINTS.suppliers.delete}/${id}`,
          {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
          }
        );

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Supplier deleted successfully! ✅", "success");
          loadSuppliers();
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message || data.error || "Failed to delete supplier"
            );
          }
        }
      } catch (error) {
        showToast(
          `Failed to delete supplier: ${error.message}`,
          "error"
        );
      }
    }
  );
}

async function deleteEntity(entity, id) {
  console.log(`Deleting ${entity} ${id}`);
  const entityTitle =
    entity.charAt(0).toUpperCase() + entity.slice(1).replace(/([A-Z])/g, " $1");

  showConfirmDialog(
    `Delete ${entityTitle}`,
    `Are you sure you want to delete this ${entity.toLowerCase()}? This action cannot be undone.`,
    async () => {
      try {
        const endpoint = getEndpointForEntity(entity);
        const response = await fetch(`${endpoint.delete}/${id}`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        });

        const data = await response.json();

        if (response.ok && data.success) {
          showToast(`${entityTitle} deleted successfully! ✅`, "success");

          const loadFunctions = {
            unitType: loadUnitTypes,
            department: loadDepartments,
            rack: loadRacks,
            itemType: loadItemTypes,
            city: loadCities,
            sizes: loadSizes,
            bank: loadBanks,
            companyType: loadCompanyTypes,
            paymentTerm: loadPaymentTerms,
            brand: loadBrands,
          };

          if (loadFunctions[entity]) {
            loadFunctions[entity]();
          }
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message ||
              data.error ||
              `Failed to delete ${entity.toLowerCase()}`
            );
          }
        }
      } catch (error) {
        // console.error(`Error deleting ${entity}:`, error);
        showToast(
          `Failed to delete ${entity.toLowerCase()}: ${error.message}`,
          "error"
        );
      }
    }
  );
}

// Production Quality delete function
async function deleteProductionQuality(id) {
  console.log(`Deleting production quality ${id}`);
  showConfirmDialog(
    "Delete Production Quality",
    "Are you sure you want to delete this production quality? This action cannot be undone.",
    async () => {
      try {
        const endpoint = getEndpointForEntity("productionQuality");
        const response = await fetch(`${endpoint.delete}/${id}`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        });

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Production Quality deleted successfully! ✅", "success");
          loadProductionQualities();
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message ||
              data.error ||
              "Failed to delete production quality"
            );
          }
        }
      } catch (error) {
        showToast(
          `Failed to delete production quality: ${error.message}`,
          "error"
        );
      }
    }
  );
}

async function deleteSizes(id) {
  console.log(`Deleting size ${id}`);
  showConfirmDialog(
    "Delete Size",
    "Are you sure you want to delete this size? This action cannot be undone.",
    async () => {
      try {
        const endpoint = getEndpointForEntity("sizes");
        const response = await fetch(`${endpoint.delete}/${id}`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        });

        const data = await response.json();

        if (response.ok && data.success) {
          showToast("Size deleted successfully! ✅", "success");
          loadSizes();
        } else {
          // Handle both HTTP errors and API errors
          if (!response.ok) {
            // Try to get error message from response body
            if (data && data.error) {
              throw new Error(data.error);
            } else {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
          } else {
            // API returned success: false
            throw new Error(
              data.message ||
              data.error ||
              "Failed to delete size"
            );
          }
        }
      } catch (error) {
        console.error("Error deleting size:", error);
        showToast(
          `Failed to delete size: ${error.message}`,
          "error"
        );
      }
    }
  );
}

// ======================== MODAL FUNCTIONS ========================

function closeEditModal(modalId) {
  console.log(`Closing modal: ${modalId}`);
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("active");

    // Reset form if it exists
    const form = modal.querySelector("form");
    if (form) {
      form.reset();
    }
  }
}

function showEditModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("active");
    // Ensure enhanced selects inside the modal reflect current values after it opens
    setTimeout(() => {
      try {
        const jq = window.jQuery || window.$;
        const el = document.getElementById("editSiteUnitId");
        if (el) {
          // Skip legacy select2 if we've enhanced with our custom control
          if (!el.__enhanced && jq && jq.fn && jq.fn.select2) {
            if (el.classList.contains("select2-hidden-accessible")) {
              jq(el).select2("destroy");
            }
            const parent = el.closest(".float-group") || modal;
            jq(el).select2({
              width: "100%",
              minimumResultsForSearch: 0,
              dropdownParent: parent,
            });
          }
          if (el.hasAttribute && el.hasAttribute("data-float-select")) {
            if (el.value && String(el.value).length) {
              el.classList.add("has-value");
              const grp = el.closest(".float-group");
              if (grp) grp.classList.add("is-filled");
            }
            // If custom enhanced, ensure display reflects current value
            if (
              el.__enhanced &&
              typeof el.__enhanced.setDisplayFromValue === "function"
            ) {
              el.__enhanced.setDisplayFromValue();
            }
          }
        }
      } catch (e) { }
    }, 50);
  }
}

// ======================== SEARCH FUNCTIONALITY ========================

function handleSearch(entity, searchTerm) {
  console.log(`Searching ${entity} for: "${searchTerm}"`);

  if (state.searchTimeouts[entity]) {
    clearTimeout(state.searchTimeouts[entity]);
  }

  state.searchTimeouts[entity] = setTimeout(() => {
    state.currentPage[entity] = 1;

    const loadFunctions = {
      unit: loadUnits,
      unitType: loadUnitTypes,
      department: loadDepartments,
      rack: loadRacks,
      itemType: loadItemTypes,
      city: loadCities,
      sizes: loadSizes,
      bank: loadBanks,
      companyType: loadCompanyTypes,
      paymentTerm: loadPaymentTerms,
      brand: loadBrands,
    };

    if (loadFunctions[entity]) {
      if (entity === "rack") {
        // Get current unit filter for racks - try state first, then DOM
        let unitFilter = state.unitFilters.rack;
        if (!unitFilter) {
          unitFilter = getUnitFilterValue("rackUnitFilter");
        }
        console.log(`Search: Loading racks with unit filter: ${unitFilter} (from state: ${state.unitFilters.rack})`);
        loadFunctions[entity](searchTerm, 1, unitFilter);
      } else {
        loadFunctions[entity](searchTerm, 1);
      }
    }
  }, 300);
}

// ======================== SORTING FUNCTIONALITY ========================

function sortTable(columnIndex, headerElement) {
  console.log(`Sorting table column ${columnIndex}`);

  const table = headerElement.closest("table");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  if (rows.length === 0 || rows[0].cells.length === 1) return;

  const currentSort = headerElement.getAttribute("data-sort") || "none";
  const newSort = currentSort === "asc" ? "desc" : "asc";

  // Clear all sort indicators
  table.querySelectorAll("th .sort-icon").forEach((icon) => {
    icon.textContent = "unfold_more";
    icon.classList.remove("text-primary-500");
    icon.classList.add("text-gray-400");
  });

  // Update current sort indicator
  const sortIcon = headerElement.querySelector(".sort-icon");
  if (sortIcon) {
    sortIcon.textContent =
      newSort === "asc" ? "keyboard_arrow_up" : "keyboard_arrow_down";
    sortIcon.classList.add("text-primary-500");
    sortIcon.classList.remove("text-gray-400");
  }

  headerElement.setAttribute("data-sort", newSort);

  // Sort rows
  rows.sort((a, b) => {
    const aValue = a.cells[columnIndex].textContent.trim();
    const bValue = b.cells[columnIndex].textContent.trim();

    const aNum = parseFloat(aValue);
    const bNum = parseFloat(bValue);

    if (!isNaN(aNum) && !isNaN(bNum)) {
      return newSort === "asc" ? aNum - bNum : bNum - aNum;
    } else {
      return newSort === "asc"
        ? aValue.localeCompare(bValue)
        : bValue.localeCompare(aValue);
    }
  });

  rows.forEach((row) => tbody.appendChild(row));

  // showToast(`Table sorted by ${headerElement.textContent.trim()} (${newSort === 'asc' ? 'ascending' : 'descending'})`, 'info');
}

// ======================== EXPORT FUNCTIONALITY ========================

function exportTableData(entity, format = "csv") {
  console.log(`Exporting ${entity} data as ${format}`);

  const tableBody = document.getElementById(getTableBodyId(entity));
  const table = tableBody.closest("table");

  if (!table) {
    showToast("No table found to export", "error");
    return;
  }

  const headers = Array.from(table.querySelectorAll("th"))
    .map((th) => th.textContent.trim())
    .filter((text) => !text.toLowerCase().includes("action"));
  const rows = Array.from(tableBody.querySelectorAll("tr")).map((tr) => {
    return Array.from(tr.cells)
      .slice(0, -1)
      .map((cell) => cell.textContent.trim());
  });

  if (
    rows.length === 0 ||
    (rows.length === 1 && rows[0][0].includes("No data found"))
  ) {
    showToast("No data available to export", "warning");
    return;
  }

  if (format === "csv") {
    exportToCSV(entity, headers, rows);
  } else if (format === "json") {
    exportToJSON(entity, headers, rows);
  }
}

function exportToCSV(entity, headers, rows) {
  let csvContent = headers.join(",") + "\n";
  csvContent += rows
    .map((row) => row.map((cell) => `"${cell}"`).join(","))
    .join("\n");

  downloadFile(
    `${entity}_data_${new Date().toISOString().split("T")[0]}.csv`,
    csvContent,
    "text/csv"
  );
  showToast(`${entity} data exported to CSV successfully! 📊`, "success");
}

function exportToJSON(entity, headers, rows) {
  const jsonData = rows.map((row) => {
    const obj = {};
    headers.forEach((header, index) => {
      obj[header] = row[index];
    });
    return obj;
  });

  const jsonContent = JSON.stringify(
    {
      entity: entity,
      exported_at: new Date().toISOString(),
      total_records: jsonData.length,
      data: jsonData,
    },
    null,
    2
  );

  downloadFile(
    `${entity}_data_${new Date().toISOString().split("T")[0]}.json`,
    jsonContent,
    "application/json"
  );
  showToast(`${entity} data exported to JSON successfully! 📋`, "success");
}

function downloadFile(filename, content, mimeType) {
  const blob = new Blob([content], { type: mimeType });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

// ======================== TAB MANAGEMENT ========================

function switchTab(tabId) {
  console.log(`Switching to tab: ${tabId}`);

  document.querySelectorAll(".nav-link").forEach((tab) => {
    tab.classList.remove("active");
  });
  document.querySelectorAll(".tab-pane").forEach((pane) => {
    pane.classList.remove("active");
  });

  document.querySelector(`[data-tab="${tabId}"]`).classList.add("active");
  document.getElementById(tabId).classList.add("active");

  state.currentTab = tabId;

  if (!state.loadedTabs.includes(tabId)) {
    console.log(`Loading data for tab: ${tabId} (not previously loaded)`);
    loadTabData(tabId);
    state.loadedTabs.push(tabId);
    // showToast(`Loading ${getEntityFromTab(tabId)} data...`, 'info');
  } else {
    console.log(`Tab ${tabId} already loaded, skipping data load`);
  }
  // Auto focus the first control in this tab's form
  try {
    const entity = getEntityFromTab(tabId);
    setTimeout(() => focusFirstFieldForEntity(entity), 50);
  } catch (_) { }
}

function loadTabData(tabId) {
  const entity = getEntityFromTab(tabId);
  console.log(`Loading data for tab: ${tabId} (entity: ${entity})`);

  switch (entity) {
    case "unit":
      loadUnits();
      break;
    case "unitType":
      loadUnitTypes();
      break;
    case "department":
      loadDepartments();
      break;
    case "rack":
      console.log("Loading rack tab - calling loadUnitsForRackForm");
      loadUnitsForRackForm();
      // Note: loadUnitsForRackForm() will call loadRacks() with the proper unit filter
      // So we don't need to call loadRacks() here separately
      break;
    case "itemType":
      loadItemTypes();
      break;
    case "city":
      loadCities();
      break;
    case "sizes":
      loadSizes();
      break;
    case "bank":
      loadBanks();
      break;
    case "companyType":
      loadCompanyTypes();
      break;
    case "paymentTerm":
      loadPaymentTerms();
      break;
    case "brand":
      loadBrands();
      break;
  }
}

// ======================== INITIALIZATION ========================

function initializeApp() {
  initializeTabs();
  initializeForms();
  initializeSearch();
  initializeModals();

  // Find the first active tab, or the first available tab if none is active
  let activeTab = document.querySelector('.nav-link.active');
  if (!activeTab) {
    // If no tab is marked as active, find the first available tab
    const firstNavLink = document.querySelector('.nav-link');
    if (firstNavLink) {
      firstNavLink.classList.add('active');
      activeTab = firstNavLink;
      // Also activate the corresponding tab pane
      const tabId = firstNavLink.getAttribute('data-tab');
      const tabPane = document.getElementById(tabId);
      if (tabPane) {
        tabPane.classList.add('active');
      }
    }
  }

  // Load data for the active tab instead of hardcoding "unit"
  if (activeTab) {
    const tabId = activeTab.getAttribute('data-tab');
    const entity = getEntityFromTab(tabId);

    if (entity) {
      console.log(`Loading initial data for active tab: ${tabId} (entity: ${entity})`);
      loadTabData(tabId);
      state.loadedTabs.push(tabId);
      state.currentTab = tabId;

      // Focus first field in the active tab
      setTimeout(() => focusFirstFieldForEntity(entity), 120);

    } else {
      console.warn(`Could not determine entity for tab: ${tabId}`);
    }
  } else {
    console.warn("No active tab found and no tabs available");
  }
}

function initializeTabs() {
  console.log("Initializing tabs...");
  const navLinks = document.querySelectorAll(".nav-link");
  console.log(`Found ${navLinks.length} nav links`);
  navLinks.forEach((button) => {
    const tabId = button.getAttribute("data-tab");
    console.log(`Setting up event listener for tab: ${tabId}`);
    button.addEventListener("click", function () {
      console.log(`Tab clicked: ${tabId}`);
      switchTab(this.getAttribute("data-tab"));
    });
  });
}

function initializeForms() {
  // Unit form
  const unitForm = document.getElementById("unitForm");
  if (unitForm) {
    unitForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitUnitBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("unit", data);
      if (success) {
        e.target.reset();
      }

      setButtonLoading("submitUnitBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitUnitBtn span");
      const iconElement = document.querySelector("#submitUnitBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Site form
  const siteForm = document.getElementById("siteForm");
  if (siteForm) {
    siteForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitSiteBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        unit_id: formData.get("unit_id"),
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
      };

      const success = await handleFormSubmit("site", data);
      if (success) {
        e.target.reset();
        // Return focus to first control for this form
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("site");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitSiteBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitSiteBtn span");
      const iconElement = document.querySelector("#submitSiteBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Unit Type form
  const unitTypeForm = document.getElementById("unitTypeForm");
  if (unitTypeForm) {
    unitTypeForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitUnitTypeBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("unitType", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("unitType");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitUnitTypeBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitUnitTypeBtn span");
      const iconElement = document.querySelector("#submitUnitTypeBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Section form
  const sectionForm = document.getElementById("sectionForm");
  if (sectionForm) {
    sectionForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitSectionBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("section", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("section");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitSectionBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitSectionBtn span");
      const iconElement = document.querySelector("#submitSectionBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Demanding Person form
  const demandingPersonForm = document.getElementById("demandingPersonForm");
  if (demandingPersonForm) {
    demandingPersonForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitDemandingPersonBtn", true, "Creating...");

      const formData = new FormData(e.target);

      // Get unit_id directly from the select element since it might be disabled
      const unitSelect = document.getElementById("demandingPersonUnit");
      const unitId = unitSelect ? unitSelect.value : formData.get("unit_id");

      const data = {
        first_name: formData.get("first_name"),
        last_name: formData.get("last_name"),
        name_in_urdu: formData.get("name_in_urdu"),
        unit_id: unitId,
        cell: formData.get("cell"),
        address: formData.get("address"),
        ptcl: formData.get("ptcl"),
      };

      // Debug logging
      console.log("Demanding Person Form Data:", data);
      console.log("Unit ID from form:", formData.get("unit_id"));
      console.log("Unit ID from select element:", unitId);

      // Check if unit select has a value (unitSelect already declared above)
      if (unitSelect) {
        console.log("Unit select element found:", unitSelect);
        console.log("Unit select value:", unitSelect.value);
        console.log("Unit select selectedIndex:", unitSelect.selectedIndex);
        console.log("Unit select options:", Array.from(unitSelect.options).map(opt => ({ value: opt.value, text: opt.text, selected: opt.selected })));
      } else {
        console.log("Unit select element NOT found!");
      }

      const success = await handleFormSubmit("demandingPerson", data);
      if (success) {
        e.target.reset();
        // Reload units after form reset to ensure unit select is properly populated
        setTimeout(() => {
          try {
            loadUnitsForDemandingPersonForm();
            focusFirstFieldForEntity("demandingPerson");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitDemandingPersonBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitDemandingPersonBtn span");
      const iconElement = document.querySelector("#submitDemandingPersonBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Supplier form
  const supplierForm = document.getElementById("supplierForm");
  if (supplierForm) {
    supplierForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitSupplierBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        first_name: formData.get("first_name"),
        last_name: formData.get("last_name"),
        company_name: formData.get("company_name"),
        name_in_urdu: formData.get("name_in_urdu"),
        cell: formData.get("cell"),
        address: formData.get("address"),
        ptcl: formData.get("ptcl"),
        ntn: formData.get("ntn"),
        stn: formData.get("stn"),
        role_id: 6, // Supplier role ID
      };

      // Debug form submission data
      console.log('🔍 Supplier form submission data:', data);
      console.log('🔍 NTN from form:', data.ntn, 'Type:', typeof data.ntn);
      console.log('🔍 STN from form:', data.stn, 'Type:', typeof data.stn);

      const success = await handleFormSubmit("supplier", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("supplier");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitSupplierBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitSupplierBtn span");
      const iconElement = document.querySelector("#submitSupplierBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Department form
  const departmentForm = document.getElementById("departmentForm");
  if (departmentForm) {
    departmentForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitDepartmentBtn", true, "Creating...");

      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("department", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("department");
          } catch (_) { }
        }, 80);
      }

      setButtonLoading("submitDepartmentBtn", false);
      // Reset icon and text
      const textSpan = document.querySelector("#submitDepartmentBtn span");
      const iconElement = document.querySelector("#submitDepartmentBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Rack form
  const rackForm = document.getElementById("rackForm");
  if (rackForm) {
    rackForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitRackBtn", true, "Creating...");
      const formData = new FormData(e.target);

      // Debug: Check the unit_id value
      const unitIdValue = formData.get("unit_id");
      console.log("Form submission - unit_id from FormData:", unitIdValue);

      // Also check the select element directly
      const unitSelect = document.getElementById("rackUnitId");
      console.log("Form submission - unit select value:", unitSelect ? unitSelect.value : 'select not found');
      console.log("Form submission - unit select disabled:", unitSelect ? unitSelect.disabled : 'select not found');

      // Get unit_id from FormData, but fallback to select element value if FormData doesn't have it
      // Note: Disabled form elements are not included in FormData, so we need to get it directly
      let unitId = formData.get("unit_id");
      if (!unitId && unitSelect) {
        unitId = unitSelect.value;
        console.log("Form submission - using fallback unit_id from select:", unitId);
      }

      // If still no unit_id and the select is disabled, it means it should have a value
      if (!unitId && unitSelect && unitSelect.disabled && defaultUnitId) {
        unitId = defaultUnitId;
        console.log("Form submission - using defaultUnitId as fallback:", unitId);
      }

      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
        unit_id: unitId,
      };

      console.log("Form submission - final data:", data);
      const success = await handleFormSubmit("rack", data);
      if (success) {
        e.target.reset();

        // Reload racks with current filter to show only the selected unit's racks
        const unitFilter = document.getElementById("rackUnitFilter");
        const currentUnitFilter = unitFilter ? unitFilter.value : null;
        console.log("Reloading racks with unit filter:", currentUnitFilter);
        loadRacks("", 1, currentUnitFilter || null);

        setTimeout(() => {
          try {
            focusFirstFieldForEntity("rack");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitRackBtn", false);
      const textSpan = document.querySelector("#submitRackBtn span");
      const iconElement = document.querySelector("#submitRackBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Item Type form
  const itemTypeForm = document.getElementById("itemTypeForm");
  if (itemTypeForm) {
    itemTypeForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitItemTypeBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      // Check for duplicate name before submitting
      const isDuplicate = await checkDuplicateName("itemType", data.name);
      if (isDuplicate) {
        showToast(
          "An item type with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitItemTypeBtn", false);
        const textSpan = document.querySelector("#submitItemTypeBtn span");
        const iconElement = document.querySelector("#submitItemTypeBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      // Check for duplicate Urdu name before submitting
      if (data.name_in_urdu) {
        const isDuplicateUrdu = await checkDuplicateNameUrdu(
          "itemType",
          data.name_in_urdu
        );
        if (isDuplicateUrdu) {
          showToast(
            "An item type with this Urdu name already exists. Please use a different Urdu name.",
            "error"
          );
          setButtonLoading("submitItemTypeBtn", false);
          const textSpan = document.querySelector("#submitItemTypeBtn span");
          const iconElement = document.querySelector("#submitItemTypeBtn i");
          if (textSpan) textSpan.textContent = "Create";
          if (iconElement) iconElement.textContent = "add";
          return;
        }
      }

      const success = await handleFormSubmit("itemType", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("itemType");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitItemTypeBtn", false);
      const textSpan = document.querySelector("#submitItemTypeBtn span");
      const iconElement = document.querySelector("#submitItemTypeBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Demand Type form
  const demandTypeForm = document.getElementById("demandTypeForm");
  if (demandTypeForm) {
    demandTypeForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitDemandTypeBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      // Check for duplicate name before submitting
      const isDuplicate = await checkDuplicateName("demandType", data.name);
      if (isDuplicate) {
        showToast(
          "A demand type with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitDemandTypeBtn", false);
        const textSpan = document.querySelector("#submitDemandTypeBtn span");
        const iconElement = document.querySelector("#submitDemandTypeBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      // Check for duplicate Urdu name before submitting
      if (data.name_in_urdu) {
        const isDuplicateUrdu = await checkDuplicateNameUrdu(
          "demandType",
          data.name_in_urdu
        );
        if (isDuplicateUrdu) {
          showToast(
            "A demand type with this Urdu name already exists. Please use a different Urdu name.",
            "error"
          );
          setButtonLoading("submitDemandTypeBtn", false);
          const textSpan = document.querySelector("#submitDemandTypeBtn span");
          const iconElement = document.querySelector("#submitDemandTypeBtn i");
          if (textSpan) textSpan.textContent = "Create";
          if (iconElement) iconElement.textContent = "add";
          return;
        }
      }

      const success = await handleFormSubmit("demandType", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("demandType");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitDemandTypeBtn", false);
      const textSpan = document.querySelector("#submitDemandTypeBtn span");
      const iconElement = document.querySelector("#submitDemandTypeBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // City form
  const cityForm = document.getElementById("cityForm");
  if (cityForm) {
    cityForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitCityBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
      };

      // Check for duplicate name before submitting
      const isDuplicate = await checkDuplicateName("city", data.name);
      if (isDuplicate) {
        showToast(
          "A city with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitCityBtn", false);
        const textSpan = document.querySelector("#submitCityBtn span");
        const iconElement = document.querySelector("#submitCityBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      // Check for duplicate Urdu name before submitting
      if (data.name_in_urdu) {
        const isDuplicateUrdu = await checkDuplicateNameUrdu(
          "city",
          data.name_in_urdu
        );
        if (isDuplicateUrdu) {
          showToast(
            "A city with this Urdu name already exists. Please use a different Urdu name.",
            "error"
          );
          setButtonLoading("submitCityBtn", false);
          const textSpan = document.querySelector("#submitCityBtn span");
          const iconElement = document.querySelector("#submitCityBtn i");
          if (textSpan) textSpan.textContent = "Create";
          if (iconElement) iconElement.textContent = "add";
          return;
        }
      }

      const success = await handleFormSubmit("city", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("city");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitCityBtn", false);
      const textSpan = document.querySelector("#submitCityBtn span");
      const iconElement = document.querySelector("#submitCityBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Production Quality form
  const productionQualityForm = document.getElementById("productionQualityForm");
  if (productionQualityForm) {
    productionQualityForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitProductionQualityBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      // Check for duplicate name before submitting
      const isDuplicate = await checkDuplicateName("productionQuality", data.name);
      if (isDuplicate) {
        showToast(
          "A production quality with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitProductionQualityBtn", false);
        const textSpan = document.querySelector("#submitProductionQualityBtn span");
        const iconElement = document.querySelector("#submitProductionQualityBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      const success = await handleFormSubmit("productionQuality", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("productionQuality");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitProductionQualityBtn", false);
      const textSpan = document.querySelector("#submitProductionQualityBtn span");
      const iconElement = document.querySelector("#submitProductionQualityBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Sizes form
  const sizesForm = document.getElementById("sizesForm");
  if (sizesForm) {
    sizesForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitSizesBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
      };

      // Check for duplicate name before submitting
      const isDuplicate = await checkDuplicateName("sizes", data.name);
      if (isDuplicate) {
        showToast(
          "A size with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitSizesBtn", false);
        const textSpan = document.querySelector("#submitSizesBtn span");
        const iconElement = document.querySelector("#submitSizesBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      const success = await handleFormSubmit("sizes", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("sizes");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitSizesBtn", false);
      const textSpan = document.querySelector("#submitSizesBtn span");
      const iconElement = document.querySelector("#submitSizesBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Bank form
  const bankForm = document.getElementById("bankForm");
  if (bankForm) {
    bankForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitBankBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("bank", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("bank");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitBankBtn", false);
      const textSpan = document.querySelector("#submitBankBtn span");
      const iconElement = document.querySelector("#submitBankBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Company Type form
  const companyTypeForm = document.getElementById("companyTypeForm");
  if (companyTypeForm) {
    companyTypeForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitCompanyTypeBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("companyType", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("companyType");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitCompanyTypeBtn", false);
      const textSpan = document.querySelector("#submitCompanyTypeBtn span");
      const iconElement = document.querySelector("#submitCompanyTypeBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Payment Term form
  const paymentTermForm = document.getElementById("paymentTermForm");
  if (paymentTermForm) {
    paymentTermForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitPaymentTermBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("paymentTerm", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("paymentTerm");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitPaymentTermBtn", false);
      const textSpan = document.querySelector("#submitPaymentTermBtn span");
      const iconElement = document.querySelector("#submitPaymentTermBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Brand form
  const brandForm = document.getElementById("brandForm");
  if (brandForm) {
    brandForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitBrandBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const name = formData.get("name");

      // Check for duplicates
      const isDuplicate = await checkDuplicateName("brand", name);
      if (isDuplicate) {
        showToast(
          "A brand with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitBrandBtn", false);
        const textSpan = document.querySelector("#submitBrandBtn span");
        const iconElement = document.querySelector("#submitBrandBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      const data = {
        name: name,
      };

      const success = await handleFormSubmit("brand", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("brand");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitBrandBtn", false);
      const textSpan = document.querySelector("#submitBrandBtn span");
      const iconElement = document.querySelector("#submitBrandBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Shift form
  const shiftForm = document.getElementById("shiftForm");
  if (shiftForm) {
    shiftForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      setButtonLoading("submitShiftBtn", true, "Creating...");
      const formData = new FormData(e.target);
      const name = formData.get("name");

      // Check for duplicates
      const isDuplicate = await checkDuplicateName("shift", name);
      if (isDuplicate) {
        showToast(
          "A shift with this name already exists. Please use a different name.",
          "error"
        );
        setButtonLoading("submitShiftBtn", false);
        const textSpan = document.querySelector("#submitShiftBtn span");
        const iconElement = document.querySelector("#submitShiftBtn i");
        if (textSpan) textSpan.textContent = "Create";
        if (iconElement) iconElement.textContent = "add";
        return;
      }

      const data = {
        name: name,
        name_in_urdu: formData.get("name_in_urdu"),
      };

      const success = await handleFormSubmit("shift", data);
      if (success) {
        e.target.reset();
        setTimeout(() => {
          try {
            focusFirstFieldForEntity("shift");
          } catch (_) { }
        }, 80);
      }
      setButtonLoading("submitShiftBtn", false);
      const textSpan = document.querySelector("#submitShiftBtn span");
      const iconElement = document.querySelector("#submitShiftBtn i");
      if (textSpan) textSpan.textContent = "Create";
      if (iconElement) iconElement.textContent = "add";
    });
  }

  // Edit Demanding Person form
  const editDemandingPersonForm = document.getElementById(
    "editDemandingPersonForm"
  );
  if (editDemandingPersonForm) {
    editDemandingPersonForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);

      // Get unit_id directly from the select element since it might be disabled
      const editUnitSelect = document.getElementById("editDemandingPersonUnit");
      const unitId = editUnitSelect ? editUnitSelect.value : formData.get("unit_id");

      const data = {
        first_name: formData.get("first_name"),
        last_name: formData.get("last_name"),
        name_in_urdu: formData.get("name_in_urdu"),
        unit_id: unitId,
        cell: formData.get("cell"),
        address: formData.get("address"),
        ptcl: formData.get("ptcl"),
      };

      // Debug logging
      console.log("Edit Demanding Person Form Data:", data);
      console.log("Unit ID from edit form:", formData.get("unit_id"));
      console.log("Unit ID from edit select element:", unitId);

      const id = formData.get("id");
      const success = await handleFormSubmit("demandingPerson", data, true, id);
      if (success) {
        closeEditModal("editDemandingPersonModal");
      }
    });
  }

  // Edit Supplier form
  const editSupplierForm = document.getElementById("editSupplierForm");
  if (editSupplierForm) {
    editSupplierForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = {
        first_name: formData.get("first_name"),
        last_name: formData.get("last_name"),
        company_name: formData.get("company_name"),
        name_in_urdu: formData.get("name_in_urdu"),
        cell: formData.get("cell"),
        address: formData.get("address"),
        ptcl: formData.get("ptcl"),
        ntn: formData.get("ntn"),
        stn: formData.get("stn"),
        role_id: 6, // Supplier role ID
      };

      // Debug edit form submission data
      console.log('🔍 Supplier edit form submission data:', data);
      console.log('🔍 NTN from edit form:', data.ntn, 'Type:', typeof data.ntn);
      console.log('🔍 STN from edit form:', data.stn, 'Type:', typeof data.stn);

      const id = formData.get("id");
      const success = await handleFormSubmit("supplier", data, true, id);
      if (success) {
        closeEditModal("editSupplierModal");
        loadSuppliers();
      }
    });
  }

  // Production Quality edit form
  const editProductionQualityForm = document.getElementById("editProductionQualityForm");
  if (editProductionQualityForm) {
    editProductionQualityForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("productionQuality", data, true, id);
      if (success) {
        closeEditModal("editProductionQualityModal");
        loadProductionQualities();
      }
    });
  }

  // Edit Sizes form
  const editSizesForm = document.getElementById("editSizesForm");
  if (editSizesForm) {
    editSizesForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
      };

      const success = await handleFormSubmit("sizes", data, true, id);
      if (success) {
        closeEditModal("editSizesModal");
        loadSizes();
      }
    });
  }

  // Edit Bank form
  const editBankForm = document.getElementById("editBankForm");
  if (editBankForm) {
    editBankForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("bank", data, true, id);
      if (success) {
        closeEditModal("editBankModal");
        loadBanks();
      }
    });
  }

  // Edit Company Type form
  const editCompanyTypeForm = document.getElementById("editCompanyTypeForm");
  if (editCompanyTypeForm) {
    editCompanyTypeForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("companyType", data, true, id);
      if (success) {
        closeEditModal("editCompanyTypeModal");
        loadCompanyTypes();
      }
    });
  }

  // Edit Payment Term form
  const editPaymentTermForm = document.getElementById("editPaymentTermForm");
  if (editPaymentTermForm) {
    editPaymentTermForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description") || null,
      };

      const success = await handleFormSubmit("paymentTerm", data, true, id);
      if (success) {
        closeEditModal("editPaymentTermModal");
        loadPaymentTerms();
      }
    });
  }

  // Edit Brand form
  const editBrandForm = document.getElementById("editBrandForm");
  if (editBrandForm) {
    editBrandForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const name = formData.get("name");

      // Check for duplicates
      const isDuplicate = await checkDuplicateName("brand", name, id);
      if (isDuplicate) {
        showToast(
          "A brand with this name already exists. Please use a different name.",
          "error"
        );
        return;
      }

      const data = {
        name: name,
      };

      const success = await handleFormSubmit("brand", data, true, id);
      if (success) {
        closeEditModal("editBrandModal");
        loadBrands();
      }
    });
  }

  // Edit Shift form
  const editShiftForm = document.getElementById("editShiftForm");
  if (editShiftForm) {
    editShiftForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const id = formData.get("id");
      const name = formData.get("name");

      // Check for duplicates
      const isDuplicate = await checkDuplicateName("shift", name, id);
      if (isDuplicate) {
        showToast(
          "A shift with this name already exists. Please use a different name.",
          "error"
        );
        return;
      }

      const data = {
        name: name,
        name_in_urdu: formData.get("name_in_urdu"),
      };

      const success = await handleFormSubmit("shift", data, true, id);
      if (success) {
        closeEditModal("editShiftModal");
        loadShifts();
      }
    });
  }
}

function initializeSearch() {
  console.log("Initializing search...");

  const searchInputs = [
    { id: "searchUnitInput", entity: "unit" },
    { id: "searchUnitTypeInput", entity: "unitType" },
    { id: "searchDepartmentInput", entity: "department" },
    { id: "searchRackInput", entity: "rack" },
    { id: "searchItemTypeInput", entity: "itemType" },
    { id: "searchCityInput", entity: "city" },
    { id: "searchSizesInput", entity: "sizes" },
    { id: "searchBankInput", entity: "bank" },
    { id: "searchCompanyTypeInput", entity: "companyType" },
    { id: "searchPaymentTermInput", entity: "paymentTerm" },
    { id: "searchBrandInput", entity: "brand" },
  ];

  searchInputs.forEach(({ id, entity }) => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("input", function () {
        handleSearch(entity, this.value.trim());
      });
    }
    // Silently skip missing search inputs (they may not exist if user doesn't have access to that tab)
  });

  // Initialize unit filter for racks
  const rackUnitFilterSelect = document.getElementById("rackUnitFilter");
  if (rackUnitFilterSelect) {
    rackUnitFilterSelect.addEventListener("change", function () {
      const selectedUnitId = this.value || null;
      console.log("Rack unit filter changed to:", selectedUnitId);

      // Update state with the new unit filter
      state.unitFilters.rack = selectedUnitId;
      console.log("Updated state.unitFilters.rack to:", selectedUnitId);

      // Get current search term
      const searchInput = document.getElementById("searchRackInput");
      const searchTerm = searchInput ? searchInput.value.trim() : "";

      // Load racks with the selected unit filter
      loadRacks(searchTerm, 1, selectedUnitId);
    });
  } else {
    console.warn("Unit filter select for racks not found");
  }
}

function initializeModals() {
  console.log("Initializing modals...");

  // Unit edit modal
  const editUnitForm = document.getElementById("editUnitForm");
  if (editUnitForm) {
    editUnitForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        id: id, // Include ID in request body as backup
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit("unit", data, true, id);
      if (success) {
        closeEditModal("editUnitModal");
      }
    });
  }

  // Site edit modal
  const editSiteForm = document.getElementById("editSiteForm");
  if (editSiteForm) {
    editSiteForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        unit_id: formData.get("unit_id"),
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
      };

      const success = await handleFormSubmit("site", data, true, id);
      if (success) {
        closeEditModal("editSiteModal");
      }
    });
  }

  // Rack edit modal
  const editRackForm = document.getElementById("editRackForm");
  if (editRackForm) {
    editRackForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(e.target);
      const id = formData.get("id");

      // Debug: Check the unit_id value
      const unitIdValue = formData.get("unit_id");
      console.log("Edit form submission - unit_id from FormData:", unitIdValue);

      // Also check the select element directly
      const unitSelect = document.getElementById("editRackUnitId");
      console.log("Edit form submission - unit select value:", unitSelect ? unitSelect.value : 'select not found');
      console.log("Edit form submission - unit select disabled:", unitSelect ? unitSelect.disabled : 'select not found');

      // Get unit_id from FormData, but fallback to select element value if FormData doesn't have it
      // Note: Disabled form elements are not included in FormData, so we need to get it directly
      let unitId = formData.get("unit_id");
      if (!unitId && unitSelect) {
        unitId = unitSelect.value;
        console.log("Edit form submission - using fallback unit_id from select:", unitId);
      }

      // If still no unit_id and the select is disabled, it means it should have a value
      if (!unitId && unitSelect && unitSelect.disabled && defaultUnitId) {
        unitId = defaultUnitId;
        console.log("Edit form submission - using defaultUnitId as fallback:", unitId);
      }

      const data = {
        unit_id: unitId,
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      console.log("Edit form submission - final data:", data);

      const success = await handleFormSubmit("rack", data, true, id);
      if (success) {
        closeEditModal("editRackModal");

        // Reload racks with current filter to show only the selected unit's racks
        const unitFilter = document.getElementById("rackUnitFilter");
        const currentUnitFilter = unitFilter ? unitFilter.value : null;
        console.log("Reloading racks with unit filter after edit:", currentUnitFilter);
        loadRacks("", 1, currentUnitFilter || null);
      }
    });
  }

  // Generic edit modal
  const editGenericForm = document.getElementById("editGenericForm");
  if (editGenericForm) {
    editGenericForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      if (!state.currentEditEntity) {
        showToast("No entity selected for editing", "error");
        return;
      }

      const formData = new FormData(e.target);
      const id = formData.get("id");
      const data = {
        name: formData.get("name"),
        name_in_urdu: formData.get("name_in_urdu"),
        description: formData.get("description"),
      };

      const success = await handleFormSubmit(
        state.currentEditEntity,
        data,
        true,
        id
      );
      if (success) {
        closeEditModal("editGenericModal");
        state.currentEditEntity = null;
      }
    });
  }
}

// ======================== SIMPLE SELECT SEARCH (NO LIBS) ========================

const __selectOptionsCache = {};

function cacheSelectOptions(selectId, options) {
  __selectOptionsCache[selectId] = Array.isArray(options)
    ? options.slice()
    : [];
}

function setSelectOptions(selectEl, options) {
  if (!selectEl) return;
  const current = selectEl.value || "";
  selectEl.innerHTML = '<option value=""></option>';
  (options || []).forEach((opt) => {
    selectEl.innerHTML += `<option value="${opt.value}">${opt.text}</option>`;
  });
  if (current) {
    selectEl.value = current;
  }
}

function attachSelectSearchInput(selectId, placeholder = "Search...") {
  const selectEl = document.getElementById(selectId);
  if (!selectEl) return;
  // Avoid duplicate input
  const container = selectEl.closest(".float-group") || selectEl.parentElement;
  if (!container) return;
  const existing = container.querySelector(
    `input[data-for-select="${selectId}"]`
  );
  if (existing) return;

  const input = document.createElement("input");
  input.setAttribute("type", "text");
  input.setAttribute("data-for-select", selectId);
  input.setAttribute("placeholder", placeholder);
  input.className =
    "block w-full px-[12px] py-[6px] mb-[6px] text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm";

  container.insertBefore(input, selectEl);

  // If no cache yet, build from current options
  if (!__selectOptionsCache[selectId]) {
    const opts = Array.from(selectEl.options).map((o) => ({
      value: o.value,
      text: o.text,
    }));
    cacheSelectOptions(selectId, opts);
  }

  input.addEventListener("input", () => {
    const filter = input.value.toLowerCase();
    const full = __selectOptionsCache[selectId] || [];
    const filtered = full.filter((o) =>
      (o.text || "").toLowerCase().includes(filter)
    );
    setSelectOptions(selectEl, filtered);
    // Maintain float label state
    const grp = selectEl.closest(".float-group");
    if (grp) {
      if (selectEl.value && String(selectEl.value).length)
        grp.classList.add("is-filled");
      else grp.classList.remove("is-filled");
    }
  });
}

// Convert a native select into a custom, searchable dropdown (Selectize-like) using Tailwind + JS only
function upgradeSelectToSearchable(selectId, placeholder = "Search...") {
  const selectEl = document.getElementById(selectId);
  if (!selectEl) return;
  if (selectEl.__enhanced) return; // prevent double-enhance

  // Hide original select but keep it in layout for label positioning
  selectEl.classList.add("opacity-0", "pointer-events-none", "absolute");

  // Wrapper
  const wrapper = document.createElement("div");
  wrapper.className = "relative";

  // Control (shows selected text and opens menu)
  const control = document.createElement("button");
  control.type = "button";
  control.className =
    "block w-full h-[30px] rounded-md text-left text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[12px] outline-0 transition-all text-sm flex items-center justify-between focus:border-primary-500";
  const controlText = document.createElement("span");
  const placeholderText = placeholder || "Select...";
  const initialText = selectEl.options[selectEl.selectedIndex]?.text || "";
  controlText.textContent = initialText || placeholderText;
  controlText.className = initialText ? "" : "text-gray-400";
  const caret = document.createElement("span");
  caret.className = "material-symbols-outlined text-gray-500 text-sm";
  caret.textContent = "expand_more";
  control.appendChild(controlText);
  control.appendChild(caret);

  // Dropdown panel (absolute within wrapper so it behaves like native dropdown)
  const panel = document.createElement("div");
  panel.className =
    "absolute z-[9999] left-0 right-0 top-full mt-1 bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] rounded-md shadow-2xl hidden";

  // Search input inside panel
  const search = document.createElement("input");
  search.type = "text";
  search.placeholder = placeholder;
  search.className =
    "w-full h-[30px] px-[12px] text-sm text-black dark:text-white bg-white dark:bg-[#0c1427] border-0 border-b border-gray-200 dark:border-[#172036] outline-0 focus:outline-0 focus:ring-0 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded-t-md";

  // Options list
  const list = document.createElement("div");
  list.className = "max-h-48 overflow-y-auto py-1";
  list.style.cssText = `
        max-height: 192px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    `;

  panel.appendChild(search);
  panel.appendChild(list);

  // Insert wrapper after the native select but before its label so CSS like
  // `select.has-value + label` continues to work for floating labels
  const parent = selectEl.parentNode;
  const next = selectEl.nextElementSibling;
  const beforeNode =
    next && next.tagName && next.tagName.toLowerCase() === "label"
      ? next
      : next;
  if (beforeNode) parent.insertBefore(wrapper, beforeNode);
  else parent.appendChild(wrapper);
  wrapper.appendChild(control);
  wrapper.appendChild(panel);

  // Build items
  let currentItems = [];
  let activeIndex = -1;
  function clearActive() {
    if (activeIndex > -1 && currentItems[activeIndex]) {
      currentItems[activeIndex].classList.remove(
        "bg-gray-100",
        "dark:bg-[#15203c]"
      );
    }
  }
  function setActive(idx) {
    clearActive();
    activeIndex = idx;
    if (activeIndex > -1 && currentItems[activeIndex]) {
      const el = currentItems[activeIndex];
      el.classList.add("bg-gray-100", "dark:bg-[#15203c]");
      try {
        el.scrollIntoView({ block: "nearest" });
      } catch (_) { }
    }
  }
  function moveActive(delta) {
    if (!currentItems.length) return;
    if (activeIndex === -1) {
      setActive(delta > 0 ? 0 : currentItems.length - 1);
      return;
    }
    const next = Math.max(
      0,
      Math.min(currentItems.length - 1, activeIndex + delta)
    );
    setActive(next);
  }
  function selectActive() {
    if (activeIndex > -1 && currentItems[activeIndex]) {
      currentItems[activeIndex].click();
    }
  }
  function buildItems(filter = "") {
    list.innerHTML = "";
    currentItems = [];
    activeIndex = -1;
    const options = Array.from(selectEl.options);
    options.forEach((opt) => {
      if (opt.value === "") return; // skip empty
      const text = opt.text || "";
      if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;
      const item = document.createElement("div");
      item.className =
        "px-[12px] py-[6px] text-sm text-black dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-[#15203c]";
      item.textContent = text;
      item.dataset.value = opt.value;
      item.addEventListener("click", () => {
        selectEl.value = opt.value;
        controlText.textContent = text || placeholderText;
        controlText.className = text ? "" : "text-gray-400";
        closePanel();
        // Keep focus on the control so pressing Enter after a selection works
        try {
          control.focus();
        } catch (_) { }
        selectEl.dispatchEvent(new Event("change"));
        // Update floating label state
        try {
          ensureLabel();
        } catch (_) { }
      });
      item.addEventListener("mouseenter", () => {
        const idx = currentItems.indexOf(item);
        if (idx > -1) setActive(idx);
      });
      list.appendChild(item);
      currentItems.push(item);
    });
  }

  let __docClickHandler = null;
  let __docFocusinHandler = null;
  let __hoverTimer = null;
  function openPanel() {
    panel.classList.remove("hidden");
    buildItems("");
    requestAnimationFrame(() => search.focus());
    // Outside click closes
    __docClickHandler = (e) => {
      if (!panel.contains(e.target) && !wrapper.contains(e.target))
        closePanel();
    };
    document.addEventListener("click", __docClickHandler, true);
    // Focus moving outside closes
    __docFocusinHandler = (e) => {
      if (!panel.contains(e.target) && !wrapper.contains(e.target))
        closePanel();
    };
    document.addEventListener("focusin", __docFocusinHandler, true);
    // Hover leaving panel+wrapper closes shortly
    const scheduleClose = () => {
      __hoverTimer = setTimeout(() => closePanel(), 180);
    };
    const cancelClose = () => {
      if (__hoverTimer) {
        clearTimeout(__hoverTimer);
        __hoverTimer = null;
      }
    };
    panel.addEventListener("mouseleave", scheduleClose, { once: true });
    panel.addEventListener("mouseenter", cancelClose, { once: true });
    wrapper.addEventListener("mouseleave", scheduleClose, { once: true });
    wrapper.addEventListener("mouseenter", cancelClose, { once: true });
    // Close on scroll/resize
    window.addEventListener("scroll", closePanel, {
      once: true,
      passive: true,
    });
    window.addEventListener("resize", closePanel, { once: true });
  }
  function closePanel() {
    if (panel.classList.contains("hidden")) return;
    panel.classList.add("hidden");
    search.value = "";
    if (__docClickHandler) {
      document.removeEventListener("click", __docClickHandler, true);
      __docClickHandler = null;
    }
    if (__docFocusinHandler) {
      document.removeEventListener("focusin", __docFocusinHandler, true);
      __docFocusinHandler = null;
    }
  }
  function onDoc(e) {
    if (!wrapper.contains(e.target)) closePanel();
  }

  control.addEventListener("click", () => {
    if (panel.classList.contains("hidden")) openPanel();
    else closePanel();
  });
  // Keyboard: Enter moves focus to next control if panel is closed; Space/Arrow open panel
  control.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      // If panel closed, move to next focusable control in the same form
      if (panel.classList.contains("hidden")) {
        e.preventDefault();
        const form = control.closest("form");
        if (form) {
          const focusables = Array.from(
            form.querySelectorAll("input, select, textarea, button")
          )
            .map((el) =>
              el.tagName === "SELECT" && el.__enhanced && el.__enhanced.control
                ? el.__enhanced.control
                : el
            )
            .filter(
              (el, idx, arr) =>
                !el.disabled &&
                el.type !== "hidden" &&
                el.tabIndex !== -1 &&
                el.offsetParent !== null &&
                arr.indexOf(el) === idx
            );
          const idx = focusables.indexOf(control);
          const next = idx > -1 ? focusables[idx + 1] : null;
          if (next) {
            next.focus();
            if (next.select) {
              try {
                next.select();
              } catch (_) { }
            }
          }
        }
        return;
      }
    }
    if (e.key === " " || e.key === "ArrowDown" || e.key === "ArrowUp") {
      e.preventDefault();
      if (panel.classList.contains("hidden")) {
        openPanel();
      } else {
        if (e.key === "ArrowDown") {
          moveActive(1);
        } else if (e.key === "ArrowUp") {
          moveActive(-1);
        } else {
          closePanel();
        }
      }
    }
  });
  search.addEventListener("input", () => buildItems(search.value));
  search.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closePanel();
      control.focus();
      return;
    }
    if (e.key === "ArrowDown") {
      e.preventDefault();
      moveActive(1);
      return;
    }
    if (e.key === "ArrowUp") {
      e.preventDefault();
      moveActive(-1);
      return;
    }
    if (e.key === "Enter") {
      e.preventDefault();
      if (activeIndex === -1) {
        moveActive(1);
      }
      selectActive();
    }
  });

  // Keep label state
  const grp = selectEl.closest(".float-group");
  const ensureLabel = () => {
    if (!grp) return;
    const hasVal = !!(selectEl.value && String(selectEl.value).length);
    if (hasVal) {
      grp.classList.add("is-filled");
      selectEl.classList.add("has-value");
      // Also set adjacent label CSS state if using sibling selectors
      const label = selectEl.nextElementSibling;
      if (label && label.tagName && label.tagName.toLowerCase() === "label") {
        label.classList.add("is-active");
      }
    } else {
      grp.classList.remove("is-filled");
      selectEl.classList.remove("has-value");
      const label = selectEl.nextElementSibling;
      if (label && label.tagName && label.tagName.toLowerCase() === "label") {
        label.classList.remove("is-active");
      }
    }
  };
  selectEl.addEventListener("change", ensureLabel);
  setTimeout(ensureLabel, 0);

  // Float-label focus state for better UX parity with inputs
  if (grp) {
    control.addEventListener("focus", () => grp.classList.add("is-focused"));
    control.addEventListener("blur", () => {
      if (panel.classList.contains("hidden"))
        grp.classList.remove("is-focused");
    });
    search.addEventListener("focus", () => grp.classList.add("is-focused"));
    search.addEventListener("blur", () => grp.classList.remove("is-focused"));
  }

  // Mark as enhanced and expose refresh
  selectEl.__enhanced = {
    control,
    panel,
    list,
    search,
    refresh: () => buildItems(search.value || ""),
    setDisplayFromValue: () => {
      const text =
        selectEl.options[selectEl.selectedIndex]?.text || placeholder;
      controlText.textContent = text;
      controlText.className = text ? "" : "text-gray-400";
      ensureLabel();
    },
  };
}

function refreshSearchableSelectOptions(selectId, opts) {
  const el = document.getElementById(selectId);
  if (!el) return;
  setSelectOptions(el, opts);
  if (el.__enhanced && typeof el.__enhanced.refresh === "function") {
    el.__enhanced.refresh();
    if (typeof el.__enhanced.setDisplayFromValue === "function") {
      el.__enhanced.setDisplayFromValue();
    }
  }
}

// Enhance every select that opts-in via data-float-select to our custom searchable control
function enhanceFloatSelects() {
  const selects = Array.from(
    document.querySelectorAll("select[data-float-select]")
  );
  selects.forEach((sel) => {
    try {
      upgradeSelectToSearchable(
        sel.id,
        sel.getAttribute("placeholder") || "Search..."
      );
    } catch (_) { }
  });
}

// Programmatically set value on enhanced select and sync visible text/label
function setEnhancedSelectValue(selectId, value) {
  const el = document.getElementById(selectId);
  if (!el) return;
  el.value = value == null ? "" : String(value);
  try {
    el.dispatchEvent(new Event("change"));
  } catch (_) { }
  if (
    el.__enhanced &&
    typeof el.__enhanced.setDisplayFromValue === "function"
  ) {
    el.__enhanced.setDisplayFromValue();
  }
}

// ======================== TEST FUNCTIONS ========================

// Test function to manually load units for demanding person form
window.testLoadUnits = function () {
  console.log("=== Manual test: Loading units for demanding person form ===");
  console.log("defaultUnitId:", defaultUnitId);
  console.log("defaultUnitName:", defaultUnitName);
  loadUnitsForDemandingPersonForm();
};

// Test function to check if unit filter element exists
window.testUnitFilter = function () {
  console.log("=== Manual test: Checking unit filter element ===");
  const unitFilter = document.getElementById("demandingPersonUnitFilter");
  console.log("Unit filter element:", unitFilter);
  if (unitFilter) {
    console.log("Unit filter options:", Array.from(unitFilter.options).map(opt => ({ value: opt.value, text: opt.text })));
    console.log("Unit filter value:", unitFilter.value);
    console.log("Unit filter disabled:", unitFilter.disabled);
  }
};

// ======================== DOCUMENT READY ========================

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM Content Loaded - Starting app initialization...");
  setTimeout(() => {
    initializeApp();

    // Additional check for demanding person tab after initialization
    setTimeout(() => {
      const activeTab = document.querySelector('.nav-link.active');
    }, 300);
  }, 100);
  // Enhance any float selects present at load (options can refresh later)
  try {
    enhanceFloatSelects();
  } catch (_) { }
});

// ======================== GLOBAL EXPORTS ========================

window.editUnit = editUnit;
window.editEntity = editEntity;
window.editRack = editRack;
window.editItemType = (id) => editEntity("itemType", id);
window.editCity = (id) => editEntity("city", id);
window.editBank = editBank;
window.editCompanyType = editCompanyType;
window.editPaymentTerm = editPaymentTerm;
window.editBrand = editBrand;
window.editSizes = editSizes;
window.deleteUnit = deleteUnit;
window.deleteItemType = (id) => deleteEntity("itemType", id);
window.deleteCity = (id) => deleteEntity("city", id);
window.deleteEntity = deleteEntity;
window.closeEditModal = closeEditModal;
window.showEditModal = showEditModal;
window.sortTable = sortTable;
window.exportTableData = exportTableData;
window.changePage = changePage;
