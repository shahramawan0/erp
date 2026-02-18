# Deploy Khawaja Traders ERP on cPanel (erp.kmigroup.online)

This guide walks you through deploying the Khawaja Traders ERP to **erp.kmigroup.online** on cPanel.

---

## 1. Prerequisites

- cPanel access for the domain **kmigroup.online**
- FTP/SFTP or cPanel File Manager
- MySQL database created in cPanel (or create in step 3)

---

## 2. Create Subdomain in cPanel

1. Log in to **cPanel**.
2. Go to **Domains** → **Subdomains** (or **Create A New Domain** if your host uses that).
3. Create subdomain:
   - **Subdomain:** `erp`
   - **Domain:** `kmigroup.online`
   - **Document Root:** e.g. `public_html/erp` or leave default (e.g. `erp.kmigroup.online`).
4. Click **Create**. Note the **document root** path (e.g. `/home/username/erp.kmigroup.online` or `/home/username/public_html/erp`).

---

## 3. Create MySQL Database

1. In cPanel, go to **Databases** → **MySQL® Databases**.
2. **Create a new database:** e.g. `username_khawaja` (cPanel often prefixes with your username).
3. **Create a MySQL user:** e.g. `username_erpuser` with a strong password.
4. **Add user to database:** Assign the user to the database with **ALL PRIVILEGES**.
5. Note down:
   - **Database name** (e.g. `username_khawaja`)
   - **Username** (e.g. `username_erpuser`)
   - **Password**
   - **Host:** usually `localhost` (cPanel shows this on the same page).

---

## 4. Upload Files

Upload so that the **document root** of **erp.kmigroup.online** contains:

- All **frontend** files (from `khawaja_traders/frontend/`) at the **root** of the document root.
- The **api** folder (from `khawaja_traders/api/`) inside the same document root.

### Target structure on server (document root = erp.kmigroup.online)

```
document_root/
├── index.php          (from frontend/)
├── sign-in.php
├── logout.php
├── change-unit.php
├── item_management.php
├── misc_entries.php
├── store_opening_stock.php
├── config.php
├── includes/
├── assets/
├── modals/
├── api/               (entire api folder: api/*.php and api/app/, etc.)
├── .env               (create in step 5 – do not upload .env from local)
└── .htaccess          (optional, see step 7)
```

### Steps

1. **Frontend:** Upload everything inside `khawaja_traders/frontend/` into the subdomain’s document root (so `index.php`, `sign-in.php`, `includes/`, `assets/`, etc. are at the root).
2. **API:** Upload the entire `khawaja_traders/api/` folder into the same document root so you have `document_root/api/` with `api/auth`, `api/units`, etc. inside it.
3. Do **not** upload your local `.env` (it has local DB credentials). Create a new `.env` on the server (step 5).

---

## 5. Create .env on Server

In the **document root** (same folder as `index.php` and `api/`), create a file named `.env` with:

```env
DB_HOST=localhost
DB_NAME=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_cpanel_database_password
```

Use the database name, username, and password from step 3. `DB_HOST` is usually `localhost` in cPanel.

- **Security:** Set permissions to `640` so only your user and the web server can read it. In File Manager: right‑click `.env` → Change Permissions → `640`.

---

## 6. Import Database Schema

1. In cPanel, go to **Databases** → **phpMyAdmin**.
2. Select the database you created (e.g. `username_khawaja`).
3. Open the **Import** tab.
4. Choose the file: `khawaja_traders/database/khawaja_traders_schema.sql`.
5. Click **Go** and wait until the import finishes.

---

## 7. .htaccess (Optional)

If your server allows `.htaccess`, you can place one in the **document root** to:

- Redirect `http` to `https` (recommended for production).
- Ensure PHP runs correctly (if needed).

Example for document root:

```apache
# Prefer HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Do **not** overwrite any existing `.htaccess` that cPanel or your host added. If there is one, add these rules to it instead.

The project’s existing `.htaccess` (in `khawaja_traders/`) is for routing inside the app; only add the HTTPS redirect if you need it and your host supports it.

---

## 8. Session & HTTPS

The app sets the session cookie as “secure” when the request is over HTTPS, so after you enable SSL (step 9), login and sessions will work correctly on **https://erp.kmigroup.online**.

---

## 9. SSL (HTTPS)

1. In cPanel, go to **Security** → **SSL/TLS** (or **Let’s Encrypt**).
2. Install an SSL certificate for **erp.kmigroup.online** (e.g. AutoSSL or Let’s Encrypt).
3. After installation, open the site as **https://erp.kmigroup.online** and, if you added the redirect in step 7, **http://** should redirect to **https://**.

---

## 10. Verify

1. **Login:**  
   Open **https://erp.kmigroup.online/sign-in.php** and log in with a user that exists in the imported database (e.g. from `users` table).

2. **API:**  
   If login works, the frontend is calling the API correctly (relative paths like `../api` resolve to `https://erp.kmigroup.online/api` when the site is at the document root).

3. **Change unit (SUA):**  
   The frontend calls `change-unit.php` in the document root. Ensure this file is uploaded with the rest of the frontend so the “Choose Unit” dropdown works for Super Admin users.

4. **Permissions:**  
   - Folders: usually `755`.  
   - PHP files: usually `644`.  
   - `uploads` or writable dirs (if any): `755` or `775` as required by the app.

---

## 11. Troubleshooting

| Issue | What to check |
|-------|----------------|
| Blank page / 500 | Enable errors: in document root, temporary `php.ini` or `.user.ini`: `display_errors=1` (remove after debugging). Check cPanel **Errors** log. |
| Database connection failed | `.env` in document root; correct `DB_HOST`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`; user has privileges on that database. |
| 404 on /api/... | `api/` folder is inside the same document root as `index.php`; no typo in folder name. |
| Login fails / session lost | HTTPS is used; cookie_secure is set when on HTTPS (handled in code); same domain for frontend and API (no cross-domain). |
| CSS/JS/images missing | All paths are relative to document root; `assets/` was uploaded; no extra subfolder (e.g. no `frontend/` in URL). |

---

## 12. Quick Checklist

- [ ] Subdomain **erp.kmigroup.online** created and document root noted.
- [ ] MySQL database and user created; user has full privileges on database.
- [ ] Frontend files uploaded to document root; `api/` folder uploaded to document root.
- [ ] `.env` created in document root with production DB credentials; permissions `640`.
- [ ] Database schema imported from `database/khawaja_traders_schema.sql`.
- [ ] SSL installed for erp.kmigroup.online; site opened via **https://**.
- [ ] Login and main flows tested; “Change unit” checked if you use that feature.

After this, the site should be live at **https://erp.kmigroup.online**.
