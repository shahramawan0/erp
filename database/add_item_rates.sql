-- Add purchase_rate and sale_rate to items table (Item Master rates)
-- Run on existing database: USE khawaja_traders; SOURCE add_item_rates.sql;

ALTER TABLE `items`
  ADD COLUMN `purchase_rate` DECIMAL(18,4) DEFAULT NULL AFTER `description`,
  ADD COLUMN `sale_rate` DECIMAL(18,4) DEFAULT NULL AFTER `purchase_rate`;
