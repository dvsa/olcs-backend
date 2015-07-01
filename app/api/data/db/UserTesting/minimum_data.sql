SET foreign_key_checks = 0;

TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `person`;
TRUNCATE TABLE `address`;

ALTER TABLE licence_no_gen AUTO_INCREMENT=10000001;
ALTER TABLE application AUTO_INCREMENT=101;

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

INSERT INTO `user` (`id`, `login_id`,`contact_details_id`) VALUES
    (1, 'loggedinuser',101),
    (2, 'johnspellman',105),
    (3, 'stevefox',106),
    (4, 'amywrigg',130),
    (5, 'philjowitt',131),
    (6, 'kevinrooney',132),
    (7, 'sarahthompson',133),
    (8, 'anotheruser',114);

INSERT INTO `organisation_user` (`organisation_id`, `user_id`, `is_administrator`) VALUES
    (1, 1, 1),
    (1, 2, 0),
    (1, 3, 0),
    (1, 4, 0),
    (1, 5, 0),
    (1, 6, 0),
    (1, 7, 0),
    (1, 8, 0);

INSERT INTO `contact_details` (`id`,`contact_type`,`address_id`,`person_id`,
   `last_modified_by`,`created_by`,`fao`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
    (101,'ct_user',26,4,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (105,'ct_user',26,81,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (106,'ct_user',26,82,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (114,'ct_user',26,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (130,'ct_user',26,83,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (131,'ct_user',26,84,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (132,'ct_user',26,85,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (133,'ct_user',26,86,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1);

INSERT INTO `person` (`id`, `birth_place`, `title`, `birth_date`, `forename`, `family_name`) VALUES
    (4, 'Zurich','title_mr','1975-04-15 00:00:00','Terry','Barret-Edgecombe'),
    (81,'Zurich','title_mr','1975-04-15 00:00:00','John','Spellman'),
    (82,'Zurich','title_mr','1975-04-15 00:00:00','Steve','Fox'),
    (83,'Zurich','title_mrs','1975-04-15 00:00:00','Amy','Wrigg'),
    (84,'Zurich','title_mr','1975-04-15 00:00:00','Phil','Jowitt'),
    (85,'Zurich','title_mr','1975-04-15 00:00:00','Kevin','Rooney'),
    (86,'Zurich','title_mrs','1975-04-15 00:00:00','Sarah','Thompson');

INSERT INTO `address` (`id`, `saon_desc`, `paon_desc`, `street`, `locality`, `postcode`, `town`, `country_code`) VALUES
    (26, '5 High Street','Harehills','','','LS9 6GN','Leeds','GB');

SET foreign_key_checks = 1;
