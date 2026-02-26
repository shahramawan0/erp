# Khawaja Traders – Project Index

Complete index of the **Khawaja Traders** ERP codebase: structure, entry points, API, frontend, database, and config.

---

## 1. Project overview

- **Type:** PHP ERP (inventory, items, stock, misc lookups)
- **Auth:** Session-based; roles **SUA** (Super Admin) and **A** (Admin)
- **Entry:** Root `index.php` routes to API or redirects to `frontend/sign-in.php`
- **Docs:** `SETUP.md`, `DEPLOYMENT.md`

---

## 2. Root & config

| Path | Purpose |
|------|--------|
| `index.php` | Router: CORS, API dispatch by path, redirect to frontend |
| `.htaccess` | Rewrite non-file requests to `index.php` |
| `.env` | DB and env config (not in repo; see `.env.example`) |
| `.env.example` | Example env vars |
| `SETUP.md` | Local setup (DB, config, login URL) |
| `DEPLOYMENT.md` | cPanel deploy (erp.kmigroup.online) |

---

## 3. API (`api/`)

### 3.1 API router (root `index.php`)

API base path: `/api/<endpoint>` (or `/<endpoint>` after stripping project folder).  
Endpoints mapped in `index.php` → `api/api/<file>.php`.

### 3.2 API endpoint files (`api/api/`)

| Endpoint (URL segment) | File | Purpose |
|------------------------|------|--------|
| `auth` | `auth.php` | Login / auth |
| `session` | `session.php` | Session check |
| `units` | `units.php` | Units CRUD |
| `sites` | `sites.php` | Sites |
| `departments` | `departments.php` | Departments |
| `sections` | `sections.php` | Sections (misc) |
| `sub-sections` | `sub_sections.php` | Sub-sections |
| `demanding-persons` | `demanding_persons.php` | Demanding persons |
| `suppliers` | `suppliers.php` | Suppliers |
| `items` | `items.php` | Items CRUD |
| `main-heads` | `main-heads.php` | Main heads |
| `control-heads` | `control-heads.php` | Control heads |
| `unit-types` | `unit-types.php` | Unit types |
| `item-types` | `item_types.php` | Item types |
| `racks` | `racks.php` | Racks |
| `unit-racks` | `unit-racks.php` | Unit–rack mapping |
| `demand-types` | `demand_types.php` | Demand types |
| `cities` | `cities.php` | Cities |
| `production-quality` | `production_quality.php` | Production quality |
| `sizes` | `sizes.php` | Sizes |
| `categories` | `categories.php` | Item categories |
| `sub-categories` | `sub_categories.php` | Item sub-categories |
| `banks` | `banks.php` | Banks |
| `company-types` | `company_types.php` | Company types |
| `payment-terms` | `payment_terms.php` | Payment terms |
| `brands` | `brands.php` | Brands |
| `shifts` | `shifts.php` | Shifts |
| `store-opening-stock` | `store_opening_stock.php` | Store opening stock |

### 3.3 App layer (`api/app/`)

**Config (`api/app/config/`)**

| File | Purpose |
|------|--------|
| `Database.php` | PDO connection (reads `.env`) |
| `EnvironmentConfig.php` | Load `.env` |
| `Session.php` | Session handling |

**Controllers (`api/app/controllers/`)**

| File | Purpose |
|------|--------|
| `BaseController.php` | Base API controller |
| `AuthenticationController.php` | Auth logic |
| `ItemController.php` | Item operations |
| `MainHeadController.php` | Main heads |
| `ControlHeadController.php` | Control heads |
| `StoreOpeningStockController.php` | Store opening stock |

**Models (`api/app/models/`)**

| File | Table / concept | Purpose |
|------|-----------------|--------|
| `BaseModel.php` | — | Base DB model |
| `User.php` | `users` | Users, roles |
| `MainHead.php` | `main_heads` | Main heads |
| `ControlHead.php` | `control_heads` | Control heads |
| `Item.php` | `items` | Items |
| `ItemCategory.php` | `item_categories` | Item categories |
| `ItemGroup.php` | `item_groups` | Item groups |
| `ItemSubGroup.php` | `item_sub_groups` | Item sub-groups |
| `ItemAttribute.php` | `item_attributes` | Item attributes |
| `ItemAttributeValue.php` | `item_attribute_values` | Item attribute values |
| `ItemSkuSequence.php` | `item_sku_sequences` | SKU sequences |
| `ItemRackAssignment.php` | `item_rack_assignments` | Item–rack assignments |
| `Rack.php` | `racks` | Racks |
| `UnitType.php` | `unit_types` | Unit types |
| `StoreOpeningStock.php` | `stock_receives` | Store opening stock (voucher, lines) |

---

## 4. Frontend (`frontend/`)

### 4.1 Entry & auth

| File | Purpose |
|------|--------|
| `index.php` | Dashboard (post-login) |
| `sign-in.php` | Login page |
| `logout.php` | Logout |
| `config.php` | Frontend config (paths, API base) |
| `session_config.php` | Session start / checks |

### 4.2 Main pages (sidebar)

| File | Menu / section | Purpose |
|------|----------------|--------|
| `index.php` | Main → Dashboard | Welcome / dashboard |
| `item_management.php` | Chart of Code → Item Management | Categories, groups, sub-groups, attributes, items |
| `misc_entries.php` | Chart of Code → Misc Entries | Units, sites, sections, suppliers, etc. |
| `store_opening_stock.php` | Stock Management → Store Opening Stock | Opening stock vouchers |

### 4.3 Other pages

| File | Purpose |
|------|--------|
| `change-unit.php` | Change current unit (e.g. for SUA) |

### 4.4 Includes (`frontend/includes/`)

| File | Purpose |
|------|--------|
| `head.php` | Meta, CSS, common head |
| `header.php` | Top bar, user, unit selector |
| `sidebar.php` | Sidebar menu (Dashboard, Chart of Code, Stock Management) |
| `footer.php` | Footer |
| `scripts.php` | Common JS |
| `scripts_lite.php` | Lite scripts (e.g. sign-in) |
| `PermissionHelper.php` | Role/permission checks |

### 4.5 Modals

| File | Purpose |
|------|--------|
| `modals/opening_stock_view_modal.php` | View opening stock details |

### 4.6 Assets

| Path | Purpose |
|------|--------|
| `assets/css/style.css` | Main styles |
| `assets/scss/style.scss` | SCSS source |
| `assets/js/custom.js` | Main custom JS |
| `assets/js/misc-entries.js` | Misc entries page logic |
| `assets/report_js/*.js` | Report scripts (e.g. pending_demands, demand_summarize_reports) |
| `assets/images/` | Images (logo, icons, etc.) |
| `pdf/opening_stock_pdf.js` | Opening stock PDF generation |

---

## 5. Database (`database/`)

### 5.1 Main schema: `khawaja_traders_schema.sql`

| Table | Purpose |
|-------|--------|
| `companies` | Companies |
| `roles` | Roles (SUA, A) |
| `sites` | Sites |
| `departments` | Departments |
| `units` | Units (e.g. Main Store) |
| `unit_types` | Unit types (e.g. Piece) |
| `users` | Users, password_hash, role_id, unit_id |
| `main_heads` | Main heads (item/account/production_item) |
| `control_heads` | Control heads (under main_head) |
| `item_categories` | Item categories |
| `item_groups` | Item groups |
| `item_sub_groups` | Item sub-groups |
| `item_attributes` | Item attributes |
| `item_sku_sequences` | Per-category SKU sequences |
| `items` | Items (category, group, sub_group, main_head, control_head, etc.) |
| `item_attribute_values` | Item ↔ attribute values |
| `racks` | Racks |
| `unit_racks` | Unit–rack mapping |
| `item_rack_assignments` | Item–rack assignments |
| `stock_receives` | Stock receipts (used for store opening stock) |

### 5.2 Misc / lookup schema: `misc_entries_schema.sql`

| Table | Purpose |
|-------|--------|
| `misc_entries` | Polymorphic misc lookups |
| `sections` | Sections |
| `sub_sections` | Sub-sections |
| `demand_types` | Demand types |
| `cities` | Cities |
| `production_quality` | Production quality |
| `sizes` | Sizes |
| `banks` | Banks |
| `demanding_persons` | Demanding persons |
| `suppliers` | Suppliers |
| `company_types` | Company types |
| `payment_terms` | Payment terms |
| `brands` | Brands |
| `shifts` | Shifts |

### 5.3 Migrations / patches

| File | Purpose |
|------|--------|
| `add_is_deleted_to_item_attributes.sql` | Add `is_deleted` to `item_attributes` |

---

## 6. Request flow (summary)

1. **Browser** → `http://localhost/khawaja_traders/` (or deployed root).
2. **.htaccess** → Non-file requests go to `index.php`.
3. **index.php** → If path is `/api/<endpoint>` (or `<endpoint>`), require `api/api/<endpoint>.php`; else redirect to `frontend/sign-in.php`.
4. **Frontend** → Uses `config.php` for API base; pages include `includes/header.php`, `sidebar.php`, etc., and call API for data.
5. **API** → Uses `api/app/config/Database.php`, controllers, and models; returns JSON.

---

## 7. Quick reference

- **Login URL:** `frontend/sign-in.php` (or `/sign-in.php` if frontend is docroot).
- **Default admin:** admin@khawajatraders.com / password (see `SETUP.md`).
- **Roles:** SUA (Super Admin), A (Admin); permissions in `roles.permissions`.
- **Deploy:** See `DEPLOYMENT.md` for cPanel and erp.kmigroup.online.

This file is the **project index** for Khawaja Traders. Use it to locate any part of the codebase quickly.
