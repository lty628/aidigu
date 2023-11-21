ALTER TABLE `wb_message` 
CHANGE COLUMN `image` `media` varchar(255) NULL DEFAULT NULL AFTER `refrom`,
CHANGE COLUMN `image_info` `media_info` text NULL AFTER `media`;