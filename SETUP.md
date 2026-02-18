# Khawaja Traders - Setup Guide

## 1. Database Setup

Create the database and run the schema:

```sql
CREATE DATABASE khawaja_traders CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE khawaja_traders;
SOURCE database/khawaja_traders_schema.sql;
```

Or via phpMyAdmin:
1. Create database `khawaja_traders`
2. Import `database/khawaja_traders_schema.sql`

## 2. Configuration

Ensure `.env` in the project root has:

```
DB_HOST=localhost
DB_NAME=khawaja_traders
DB_USERNAME=root
DB_PASSWORD=
```

## 3. Access

- **Login URL:** http://localhost/khawaja_traders/frontend/sign-in.php
- **Default admin:** admin@khawajatraders.com / password

## 4. Features

- **Authentication:** SUA and A roles only
- **Dashboard:** Default welcome page
- **Item Management:** Categories, Groups, Sub-Groups, Attributes, Items (ERP structure)
- **Stock Management:** Store Opening Stock

## 5. Folder Structure

- `frontend/` - PHP pages (sign-in, index, item_management, store_opening_stock)
- `api/api/` - API routers
- `api/app/` - Controllers, models, config
- `database/` - Schema SQL
