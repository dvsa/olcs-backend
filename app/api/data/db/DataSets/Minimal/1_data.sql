SET foreign_key_checks = 0;

INSERT INTO `organisation` (`id`) VALUES (100);
INSERT INTO `user` (`id`) VALUES (1);
INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES (100, 1);

SET foreign_key_checks = 1;