-- =============================================================================
-- Account Management Schema for Khawaja Traders
-- =============================================================================
-- Run after khawaja_traders_schema.sql and misc_entries_schema.sql.
-- Creates account_codes table for Sale and Purchase party accounts.
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+05:00';

-- -----------------------------------------------------------------------------
-- account_codes - Party accounts (sale and purchase)
-- account_type: 'sale' = Sale Party, 'purchase' = Purchase Party
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL DEFAULT 1,
  `source_id` int DEFAULT NULL COMMENT 'Display code / sequence number',
  `main_head_id` int DEFAULT NULL,
  `control_head_id` int DEFAULT NULL,
  `account_type` enum('sale','purchase') DEFAULT NULL COMMENT 'Sale Party or Purchase Party',
  `code` varchar(100) DEFAULT NULL COMMENT 'Account code string',
  `name` varchar(255) NOT NULL,
  `name_in_urdu` varchar(255) DEFAULT '',
  `description` text,
  `address` text,
  `cell` varchar(50) DEFAULT NULL,
  `ptcl` varchar(50) DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_address` text,
  `ntn` varchar(50) DEFAULT NULL,
  `stn` varchar(50) DEFAULT NULL,
  `bank_id` int DEFAULT NULL,
  `company_type_id` int DEFAULT NULL,
  `payment_term_id` int DEFAULT NULL,
  `opening_balance` decimal(15,2) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'A',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_account_codes_company` (`company_id`),
  KEY `idx_account_codes_main_head` (`main_head_id`),
  KEY `idx_account_codes_control_head` (`control_head_id`),
  KEY `idx_account_codes_account_type` (`account_type`),
  KEY `idx_account_codes_deleted` (`is_deleted`),
  CONSTRAINT `fk_account_codes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_main_head` FOREIGN KEY (`main_head_id`) REFERENCES `main_heads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_control_head` FOREIGN KEY (`control_head_id`) REFERENCES `control_heads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_bank` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_company_type` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_account_codes_payment_term` FOREIGN KEY (`payment_term_id`) REFERENCES `payment_terms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
