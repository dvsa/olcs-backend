-- Organisations    1**
-- Licences         2**
-- OperatingCentres 3**
-- Address          4**
SET foreign_key_checks = 0;

INSERT IGNORE INTO `organisation` (`id`) VALUES (100);
INSERT IGNORE INTO `user` (`id`) VALUES (1);
INSERT IGNORE INTO `organisation_user` (`organisation_id`, `user_id`) VALUES (100, 1);

SET foreign_key_checks = 1;