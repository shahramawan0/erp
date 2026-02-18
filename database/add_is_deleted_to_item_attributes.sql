-- Add is_deleted column to item_attributes for soft delete support
ALTER TABLE `item_attributes` ADD COLUMN `is_deleted` tinyint(1) NOT NULL DEFAULT 0 AFTER `sort_order`;
