START TRANSACTION;

-- Roles

INSERT INTO `role` SET `name` = 'Super User Role', `handle` = 'super_user';
SET @super_user_role_id = LAST_INSERT_ID();
INSERT INTO `role` SET `name` = 'Monion User Role', `handle` = 'minion_user';
SET @minion_user_role_id = LAST_INSERT_ID();

-- Permisssions

INSERT INTO `permission` SET `name` = 'Application View', `handle` = 'application_list';
SET @perm_application_list = LAST_INSERT_ID();
INSERT INTO `permission` SET `name` = 'Application View', `handle` = 'application_view';
SET @perm_application_view = LAST_INSERT_ID();
INSERT INTO `permission` SET `name` = 'Application View', `handle` = 'application_update';
SET @perm_application_update = LAST_INSERT_ID();
INSERT INTO `permission` SET `name` = 'Application View', `handle` = 'application_delete';
SET @perm_application_delete = LAST_INSERT_ID();

-- Match Roles & Permisssions

INSERT INTO `role_permission` SET `role_id` = @super_user_role_id, `permission_id` = @perm_application_list;
INSERT INTO `role_permission` SET `role_id` = @super_user_role_id, `permission_id` = @perm_application_view;
INSERT INTO `role_permission` SET `role_id` = @super_user_role_id, `permission_id` = @perm_application_delete;

INSERT INTO `role_permission` SET `role_id` = @minion_user_role_id, `permission_id` = @perm_application_list;
INSERT INTO `role_permission` SET `role_id` = @minion_user_role_id, `permission_id` = @perm_application_update;
INSERT INTO `role_permission` SET `role_id` = @minion_user_role_id, `permission_id` = @perm_application_view;

-- Insert Users

INSERT INTO `user` (`username`, `password`, `display_name`) VALUES ('superuser1', 'superuser1pw', 'Super User 1');
SET @super_user_id1 = LAST_INSERT_ID();

INSERT INTO `user` (`username`, `password`, `display_name`) VALUES ('minionuser1', 'minionuser1pw', 'Minion User 1');
SET @minion_user_id1 = LAST_INSERT_ID();

-- Super user also has minion user role.

INSERT INTO `user_role` SET `user_id` = @super_user_id1, role_id = @super_user_role_id;
INSERT INTO `user_role` SET `user_id` = @super_user_id1, role_id = @minion_user_role_id;
INSERT INTO `user_role` SET `user_id` = @minion_user_id1, role_id = @minion_user_role_id;

COMMIT;

SELECT u.display_name, u.username, u.password, r.name as role_name, r.handle as role_handle, p.name as permission_name, p.handle as permission_handle FROM user u JOIN user_role ur ON (u.id = ur.user_id) JOIN role r ON (ur.role_id = r.id) JOIN role_permission rp ON (rp.role_id = r.id) JOIN permission p ON (rp.permission_id = p.id);
+---------------+-------------+---------------+------------------+-------------+------------------+--------------------+
| display_name  | username    | password      | role_name        | role_handle | permission_name  | permission_handle  |
+---------------+-------------+---------------+------------------+-------------+------------------+--------------------+
| Super User 1  | superuser1  | superuser1pw  | Super User Role  | super_user  | Application View | application_list   |
| Super User 1  | superuser1  | superuser1pw  | Super User Role  | super_user  | Application View | application_view   |
| Super User 1  | superuser1  | superuser1pw  | Super User Role  | super_user  | Application View | application_delete |
| Super User 1  | superuser1  | superuser1pw  | Monion User Role | minion_user | Application View | application_list   |
| Super User 1  | superuser1  | superuser1pw  | Monion User Role | minion_user | Application View | application_view   |
| Super User 1  | superuser1  | superuser1pw  | Monion User Role | minion_user | Application View | application_update |
| Minion User 1 | minionuser1 | minionuser1pw | Monion User Role | minion_user | Application View | application_list   |
| Minion User 1 | minionuser1 | minionuser1pw | Monion User Role | minion_user | Application View | application_view   |
| Minion User 1 | minionuser1 | minionuser1pw | Monion User Role | minion_user | Application View | application_update |
+---------------+-------------+---------------+------------------+-------------+------------------+--------------------+
9 rows in set (0.00 sec)












