-- Khawaja Traders - Complete Database Schema
-- Separate database: khawaja_traders
-- Run this after creating the database: CREATE DATABASE khawaja_traders CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET NAMES utf8mb4;
SET time_zone = '+05:00';

-- Companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `companies` (`id`, `name`) VALUES (1, 'Khawaja Traders');

-- Roles (SUA and A only for this project)
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `description` text,
  `permissions` longtext,
  `status` varchar(50) NOT NULL DEFAULT 'U',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_roles_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `company_id`, `name`, `description`, `permissions`, `status`) VALUES
(1, 1, 'Super Admin', 'Full system access', '[\"*\"]', 'SUA'),
(2, 1, 'Admin', 'Company administrator', '[\"users.*\", \"roles.*\", \"units.*\", \"racks.*\", \"main_heads.*\", \"control_heads.*\", \"items.*\", \"store_opening_stock.*\"]', 'A');

-- Sites (minimal - for User JOIN)
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Departments (minimal - for User JOIN)
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Units
CREATE TABLE IF NOT EXISTS `units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) NOT NULL DEFAULT '',
  `short_name` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(10) DEFAULT 'A',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_units_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `units` (`id`, `company_id`, `name`, `name_in_urdu`, `short_name`) VALUES (1, 1, 'Main Store', 'مین اسٹور', 'MS');

-- Unit Types
CREATE TABLE IF NOT EXISTS `unit_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) NOT NULL DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `unit_types` (`id`, `company_id`, `name`, `name_in_urdu`) VALUES (1, 1, 'Piece', 'پیس');

-- Users (password: admin123)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `role_id` int NOT NULL,
  `unit_id` int DEFAULT NULL,
  `site_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `login_token` varchar(255) DEFAULT NULL,
  `login_token_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_company` (`company_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin@khawajatraders.com / password
INSERT INTO `users` (`id`, `company_id`, `role_id`, `unit_id`, `email`, `password_hash`, `first_name`, `last_name`, `status`) VALUES
(1, 1, 1, 1, 'admin@khawajatraders.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'active');

-- Main Heads (for items)
CREATE TABLE IF NOT EXISTS `main_heads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) NOT NULL DEFAULT '',
  `type` enum('item','account','production_item') NOT NULL DEFAULT 'item',
  `description` text,
  `status` varchar(10) DEFAULT 'A',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_main_heads_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Control Heads
CREATE TABLE IF NOT EXISTS `control_heads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `main_head_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) NOT NULL DEFAULT '',
  `type` enum('item','account','production_item') NOT NULL DEFAULT 'item',
  `status` varchar(10) DEFAULT 'A',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_control_heads_main` (`main_head_id`),
  FOREIGN KEY (`main_head_id`) REFERENCES `main_heads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Categories (ERP)
CREATE TABLE IF NOT EXISTS `item_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_item_cat_company_code` (`company_id`, `code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Groups
CREATE TABLE IF NOT EXISTS `item_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_item_groups_category` (`category_id`),
  FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Sub-Groups
CREATE TABLE IF NOT EXISTS `item_sub_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `group_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_item_sub_groups_group` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `item_groups` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Attributes
CREATE TABLE IF NOT EXISTS `item_attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sub_group_id` int NOT NULL,
  `attribute_name` varchar(255) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_item_attributes_sub_group` (`sub_group_id`),
  FOREIGN KEY (`sub_group_id`) REFERENCES `item_sub_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item SKU Sequences
CREATE TABLE IF NOT EXISTS `item_sku_sequences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `group_id` int NOT NULL,
  `sub_group_id` int NOT NULL,
  `last_sequence` int NOT NULL DEFAULT 0,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sku_seq` (`category_id`, `group_id`, `sub_group_id`),
  FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`id`),
  FOREIGN KEY (`group_id`) REFERENCES `item_groups` (`id`),
  FOREIGN KEY (`sub_group_id`) REFERENCES `item_sub_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items (simplified - matches KMI structure)
CREATE TABLE IF NOT EXISTS `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_id` varchar(50) DEFAULT NULL,
  `company_id` int NOT NULL DEFAULT 1,
  `main_head_id` int DEFAULT NULL,
  `control_head_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `group_id` int DEFAULT NULL,
  `sub_group_id` int DEFAULT NULL,
  `unit_type_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `code` varchar(100) DEFAULT NULL,
  `normalized_sku` varchar(100) DEFAULT NULL,
  `description` text,
  `status` enum('I','PI','A') DEFAULT 'I',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_items_company` (`company_id`),
  KEY `idx_items_control_head` (`control_head_id`),
  KEY `idx_items_category` (`category_id`),
  KEY `idx_items_group` (`group_id`),
  KEY `idx_items_sub_group` (`sub_group_id`),
  FOREIGN KEY (`control_head_id`) REFERENCES `control_heads` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`group_id`) REFERENCES `item_groups` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`sub_group_id`) REFERENCES `item_sub_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Attribute Values
CREATE TABLE IF NOT EXISTS `item_attribute_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `value` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_item_attr` (`item_id`, `attribute_id`),
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`attribute_id`) REFERENCES `item_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Racks
CREATE TABLE IF NOT EXISTS `racks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `unit_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_racks_unit` (`unit_id`),
  FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Unit Racks (racks per unit mapping - if used)
CREATE TABLE IF NOT EXISTS `unit_racks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unit_id` int NOT NULL,
  `rack_id` int NOT NULL,
  `company_id` int NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unit_rack` (`unit_id`, `rack_id`),
  FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  FOREIGN KEY (`rack_id`) REFERENCES `racks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item Rack Assignments
CREATE TABLE IF NOT EXISTS `item_rack_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `rack_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `company_id` int NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(10) DEFAULT 'A',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ira_item` (`item_id`),
  KEY `idx_ira_rack` (`rack_id`),
  KEY `idx_ira_unit` (`unit_id`),
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`rack_id`) REFERENCES `racks` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Receives (for store opening stock) - simplified
CREATE TABLE IF NOT EXISTS `stock_receives` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `voucher_no` varchar(50) DEFAULT NULL,
  `voucher_date` date DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `item_id` int NOT NULL,
  `rack_id` int DEFAULT NULL,
  `qty` decimal(18,6) NOT NULL DEFAULT 0,
  `transaction_type` enum('receive','issue','opening','repair','return') NOT NULL DEFAULT 'receive',
  `status` varchar(50) NOT NULL DEFAULT 'opening',
  `remarks` text,
  `narration` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sr_voucher` (`voucher_no`),
  KEY `idx_sr_unit` (`unit_id`),
  KEY `idx_sr_item` (`item_id`),
  KEY `idx_sr_company` (`company_id`),
  FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`rack_id`) REFERENCES `racks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
