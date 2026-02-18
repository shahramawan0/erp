-- =============================================================================
-- Misc Entries Database Schema for Khawaja Traders
-- =============================================================================
-- Run this against the khawaja_traders database.
-- This schema supports the misc_entries.php page (Unit, Site, Unit Type, etc.)
--
-- Note: Units, unit_types, sites, departments, racks already exist in the main
-- schema. This file adds the misc_entries polymorphic table and supporting
-- tables for entities like Cities, Banks, Demand Types, etc.
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+05:00';

-- -----------------------------------------------------------------------------
-- 1. misc_entries - Polymorphic lookup table for various entity types
-- -----------------------------------------------------------------------------
-- status values: C=City, S=Salesman, B=Bank, SH=Shop, G=Goods, U=User,
--                DT=DemandType, PQ=ProductionQuality, SZ=Size
-- Used when a single table stores multiple entity types differentiated by status
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `misc_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Shop/entity logo image path',
  `status` varchar(10) NOT NULL COMMENT 'C=City, S=Salesman, B=Bank, SH=Shop, G=Goods, U=User, DT=DemandType, PQ=ProductionQuality, SZ=Size',
  `code` int NOT NULL DEFAULT 0 COMMENT 'Auto-generated sequence per status',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_misc_entries_company` (`company_id`),
  KEY `idx_misc_entries_status` (`status`),
  KEY `idx_misc_entries_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. sections - For Section tab in misc entries
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sections_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. sub_sections - For Sub Section tab (unit-type level subdivisions)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sub_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `unit_type_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_sections_company` (`company_id`),
  KEY `idx_sub_sections_unit_type` (`unit_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. demand_types - For Demand Type tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `demand_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_demand_types_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. cities - For Cities tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cities_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. production_quality - For Production Quality tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production_quality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_production_quality_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. sizes - For Sizes tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sizes_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. banks - For Banks tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `banks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_banks_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. demanding_persons - For Demanding Person tab (or use users with specific role)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `demanding_persons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `unit_id` int DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT '',
  `name_in_urdu` varchar(255) DEFAULT '',
  `cell` varchar(50) DEFAULT NULL,
  `address` text,
  `ptcl` varchar(50) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_demanding_persons_company` (`company_id`),
  KEY `idx_demanding_persons_unit` (`unit_id`),
  FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. suppliers - Supplier entities (or use users with supplier role)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `first_name` varchar(100) DEFAULT '',
  `last_name` varchar(100) DEFAULT '',
  `company_name` varchar(255) DEFAULT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `cell` varchar(50) DEFAULT NULL,
  `address` text,
  `ptcl` varchar(50) DEFAULT NULL,
  `ntn` varchar(50) DEFAULT NULL,
  `stn` varchar(50) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_suppliers_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10b. company_types - For Company Type tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `company_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_types_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10c. payment_terms - For Payment Terms tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment_terms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payment_terms_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10d. brands - For Brand tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_brands_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10e. shifts - For Shift tab
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shifts_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. Enhance sites table (add name_in_urdu, description - skip if columns exist)
-- -----------------------------------------------------------------------------
-- ALTER TABLE `sites` ADD COLUMN `name_in_urdu` varchar(255) DEFAULT '' AFTER `name`;
-- ALTER TABLE `sites` ADD COLUMN `description` text AFTER `name_in_urdu`;
-- ALTER TABLE `sites` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE `sites` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- -----------------------------------------------------------------------------
-- 12. Enhance departments table (add name_in_urdu, description - skip if columns exist)
-- -----------------------------------------------------------------------------
-- ALTER TABLE `departments` ADD COLUMN `name_in_urdu` varchar(255) DEFAULT '' AFTER `name`;
-- ALTER TABLE `departments` ADD COLUMN `description` text AFTER `name_in_urdu`;
-- ALTER TABLE `departments` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE `departments` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- -----------------------------------------------------------------------------
-- 13. Enhance racks table (add name_in_urdu, description - skip if columns exist)
-- -----------------------------------------------------------------------------
-- ALTER TABLE `racks` ADD COLUMN `name_in_urdu` varchar(255) DEFAULT '' AFTER `name`;
-- ALTER TABLE `racks` ADD COLUMN `description` text AFTER `name_in_urdu`;
