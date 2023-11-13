ALTER TABLE `aidigu_cn`.`wb_user` 
ADD COLUMN `vip` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT 'vip等级' AFTER `invisible`;