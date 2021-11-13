ALTER TABLE `user` ADD `facebook_id` VARCHAR(100) NULL DEFAULT NULL AFTER `email`, ADD UNIQUE (`facebook_id`);

ALTER TABLE `user` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NULL DEFAULT NULL; 
