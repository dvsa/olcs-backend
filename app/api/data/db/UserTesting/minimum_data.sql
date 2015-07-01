SET foreign_key_checks = 0;

TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;

INSERT INTO `organisation`
(
    `id`,
    `lead_tc_area_id`,
    `company_or_llp_no`,
    `name`,
    `type`,
    `allow_email`
)
VALUES
(
    1,
    'B',
    '12345678',
    'Operator Licensing Ltd.',
    'org_t_rc',
    1
);

INSERT INTO `user` (`id`) VALUES (1);
INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES (1, 1);

SET foreign_key_checks = 1;
