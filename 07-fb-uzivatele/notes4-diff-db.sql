ALTER TABLE `user` ADD `role_id` VARCHAR(50) NULL DEFAULT NULL AFTER `email`, ADD INDEX (`role_id`);

ALTER TABLE `user` ADD FOREIGN KEY (`role_id`) REFERENCES `role`(`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE `forgotten_password` ( `forgotten_password_id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `code` VARCHAR(50) NOT NULL , `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`forgotten_password_id`), INDEX (`user_id`)) ENGINE = InnoDB;

ALTER TABLE `forgotten_password` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO `permission` (`permission_id`, `role_id`, `resource_id`, `action`, `type`) VALUES (NULL, 'guest', 'User', 'forgottenPassword', 'allow'), (NULL, 'guest', 'User', 'renewPassword', 'allow')

INSERT INTO `permission` (`permission_id`, `role_id`, `resource_id`, `action`, `type`) VALUES (NULL, 'guest', 'User', 'facebookLogin', 'allow'); 