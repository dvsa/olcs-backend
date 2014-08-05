SET foreign_key_checks = 0;

TRUNCATE TABLE case_category;

SET foreign_key_checks = 1;

INSERT INTO `case_category` (`id`, `name`, `group`) VALUES
(1, 'Offences (inc. driver hours)', 'Compliance'),
(2, 'Prohibitions', 'Compliance'),
(3, 'Convictions', 'Compliance'),
(4, 'Penalties', 'Compliance'),
(5, 'ERRU MSI', 'Compliance'),
(6, 'Bus compliance', 'Compliance'),
(7, 'Section 9', 'Compliance'),
(8, 'Section 43', 'Compliance'),
(9, 'Impounding', 'Compliance');

-- Insert Bus registrations here

INSERT INTO `case_category` (`id`, `name`, `group`) VALUES
(10, 'Duplicate TM', 'TM'),
(11, 'Repute / professional competence of TM', 'TM'),
(12, 'TM Hours', 'TM');

INSERT INTO `case_category` (`id`, `name`, `group`) VALUES
(13, 'Interim with / without submission', 'Licensing application'),
(14, 'Representation', 'Licensing application'),
(15, 'Objection', 'Licensing application'),
(16, 'Non-chargeable variation', 'Licensing application'),
(17, 'Regulation 31', 'Licensing application'),
(18, 'Schedule 4', 'Licensing application'),
(19, 'Chargeable variation', 'Licensing application'),
(20, 'New application', 'Licensing application');

INSERT INTO `case_category` (`id`, `name`, `group`) VALUES
(21, 'Surrender', 'Licence referral'),
(22, 'Non application related maintenance issue', 'Licence referral'),
(23, 'Review complaint', 'Licence referral'),
(24, 'Late fee', 'Licence referral'),
(25, 'Financial standing issue (continuation)', 'Licence referral'),
(26, 'Repute fitness of director', 'Licence referral'),
(27, 'Period of grace', 'Licence referral'),
(28, 'Proposal to revoke', 'Licence referral');
