SET foreign_key_checks = 0;

TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;

INSERT INTO `organisation` (`id`) VALUES (1);
INSERT INTO `user` (`id`) VALUES (1);
INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES (1, 1);

SET foreign_key_checks = 1;
