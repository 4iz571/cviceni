--
-- Vložení nových záznamů pro ACL
--
INSERT INTO `resource` (`resource_id`) VALUES ('Front:Product');
INSERT INTO `permission` (`permission_id`, `role_id`, `resource_id`, `action`, `type`) VALUES
  (NULL, 'guest', 'Product', '', 'allow'),
  (NULL, 'guest', 'Front:Product', '', 'allow'),
  (NULL, 'authenticated', 'Product', '', 'allow'),
  (NULL, 'authenticated', 'Front:Product', '', 'allow'); 