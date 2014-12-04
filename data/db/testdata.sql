SET foreign_key_checks = 0;

TRUNCATE TABLE `address`;
TRUNCATE TABLE `application`;
TRUNCATE TABLE `application_completion`;
TRUNCATE TABLE `application_operating_centre`;
TRUNCATE TABLE `bus_reg`;
TRUNCATE TABLE `bus_reg_other_service`;
TRUNCATE TABLE `bus_short_notice`;
TRUNCATE TABLE `bus_notice_period`;
TRUNCATE TABLE `bus_service_type`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `complaint_oc_licence`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `disc_sequence`;
TRUNCATE TABLE `document`;
TRUNCATE TABLE `doc_template`;
TRUNCATE TABLE `doc_bookmark`;
TRUNCATE TABLE `doc_template`;
TRUNCATE TABLE `doc_paragraph`;
TRUNCATE TABLE `doc_template_bookmark`;
TRUNCATE TABLE `doc_paragraph_bookmark`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `fee`;
TRUNCATE TABLE `licence`;
TRUNCATE TABLE `licence_vehicle`;
TRUNCATE TABLE `licence_operating_centre`;
TRUNCATE TABLE `legacy_case_offence`;
TRUNCATE TABLE `legacy_offence`;
TRUNCATE TABLE `note`;
TRUNCATE TABLE `operating_centre`;
TRUNCATE TABLE `opposer`;
TRUNCATE TABLE `opposition`;
TRUNCATE TABLE `opposition_grounds`;
TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `organisation_person`;
TRUNCATE TABLE `person`;
TRUNCATE TABLE `disqualification`;
TRUNCATE TABLE `phone_contact`;
TRUNCATE TABLE `pi`;
TRUNCATE TABLE `pi_decision`;
TRUNCATE TABLE `pi_type`;
TRUNCATE TABLE `pi_hearing`;
TRUNCATE TABLE `pi_reason`;
TRUNCATE TABLE `pi_venue`;
TRUNCATE TABLE `prohibition`;
TRUNCATE TABLE `prohibition_defect`;
TRUNCATE TABLE `presiding_tc`;
TRUNCATE TABLE `psv_disc`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `transport_manager_licence`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `trading_name`;
TRUNCATE TABLE `transport_manager`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;
TRUNCATE TABLE `vehicle`;
TRUNCATE TABLE `cases`;
TRUNCATE TABLE `case_category`;
TRUNCATE TABLE `impounding`;
TRUNCATE TABLE `impounding_legislation_type`;
TRUNCATE TABLE `team`;
TRUNCATE TABLE `task`;
TRUNCATE TABLE `licence`;
TRUNCATE TABLE `serious_infringement`;
TRUNCATE TABLE `si_category`;
TRUNCATE TABLE `si_category_type`;
TRUNCATE TABLE `si_penalty`;
TRUNCATE TABLE `si_penalty_erru_requested`;
TRUNCATE TABLE `si_penalty_erru_imposed`;
TRUNCATE TABLE `si_penalty_imposed_type`;
TRUNCATE TABLE `si_penalty_requested_type`;
TRUNCATE TABLE `si_penalty_type`;
TRUNCATE TABLE `serious_infringement`;
TRUNCATE TABLE `sla`;
TRUNCATE TABLE `submission_action`;
TRUNCATE TABLE `publication`;
TRUNCATE TABLE `publication_section`;
TRUNCATE TABLE `publication_link`;
TRUNCATE TABLE `public_holiday`;

INSERT INTO `address` (`id`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`,
    `postcode`, `town`, `country_code`, `created_on`, `last_modified_on`, `version`) VALUES
    (7,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (8,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (21,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (25,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','GB',NOW(),NOW(),1),
    (26,NULL,NULL,'5 High Street','Harehills','','','LS9 6GN','Leeds','GB',NOW(),NOW(),1),
    (27,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','GB',NOW(),NOW(),1),
    (29,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (30,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (31,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (37,NULL,NULL,'Unit 10','10 High Street','Alwoodley','','LS7 9SD','Leeds','GB',NOW(),NOW(),1),
    (39,NULL,NULL,'15 Avery Street','Harehills','','','LS9 5SS','Leeds','GB',NOW(),NOW(),1),
    (41,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (42,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (54,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (55,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (63,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (64,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (67,NULL,NULL,'Park Cottage','Coldcotes Avenue','','','LS9 6NE','Leeds','GB',NOW(),NOW(),1),
    (72,NULL,NULL,'38 George Street','Edgbaston','','','B15 1PL','Birmingham','GB',NOW(),NOW(),1),
    (75,NULL,NULL,'','','','','','','GB',NOW(),NOW(),1),
    (76,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (100,NULL,NULL,'Test Partnership LLP','10 Partnerships street','PartnershipDistrict','Partnership Land','PA7 5IP',
    'Leeds','GB',NOW(),NOW(),1),
    (104,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1);

INSERT INTO `application` (`id`, `licence_id`, `created_by`, `last_modified_by`, `status`, `tot_auth_vehicles`,
    `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_large_vehicles`, `tot_community_licences`,
    `tot_auth_trailers`, `bankrupt`, `liquidation`, `receivership`, `administration`, `disqualified`,
    `insolvency_details`, `insolvency_confirmation`, `safety_confirmation`, `received_date`, `target_completion_date`,
    `prev_conviction`, `convictions_confirmation`, `created_on`, `last_modified_on`, `version`, `is_variation`, `goods_or_psv`, `ni_flag`) VALUES
    (1,7,NULL,NULL,'apsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-02-19 09:06:53', '2014-12-25 10:06:53',NULL,
    NULL,NOW(),NULL,1,0,'lcat_gv',0),
    (2,7,NULL,NULL,'apsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,1,1,'lcat_gv',0),
    (6,114,NULL,NULL,'apsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,'2014-04-30 12:09:37','2014-04-30 12:09:39',1,0,'lcat_psv',1);

INSERT INTO `application_completion` (`application_id`, `created_by`, `last_modified_by`, `last_section`, `created_on`, `last_modified_on`, `version`) VALUES
(1,NULL,NULL,NULL,NULL,NULL,1),
(2,NULL,NULL,NULL,NULL,NULL,1);

INSERT INTO `application_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `application_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,34,23,1,0,NULL,'2014-03-13',1,NULL,NULL,1,1,16),
(2,NULL,NULL,34,23,1,0,NULL,'2014-03-21',1,NULL,NULL,1,1,16),
(3,NULL,NULL,34,23,1,0,NULL,'2014-04-01',1,NULL,NULL,1,1,16);

INSERT INTO `licence_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `licence_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,7,16),
(2,NULL,NULL,10,0,1,0,NULL,NULL,1,NULL,NULL,1,110,16),
(3,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,41,17);

INSERT INTO `bus_reg`
(`id`, `bus_notice_period_id`, `subsidised`, `last_modified_by`, `withdrawn_reason`, `licence_id`, `created_by`,
 `operating_centre_id`, `route_no`, `reg_no`, `start_point`, `finish_point`, `via`, `other_details`, `is_short_notice`,
 `use_all_stops`, `has_manoeuvre`, `manoeuvre_detail`, `need_new_stop`, `new_stop_detail`, `has_not_fixed_stop`,
 `not_fixed_stop_detail`, `subsidy_detail`, `timetable_acceptable`, `map_supplied`, `route_description`,
 `copied_to_la_pte`, `la_short_note`, `application_signed`, `completed_date`, `route_seq`, `op_notified_la_pte`,
 `stopping_arrangements`, `trc_condition_checked`, `trc_notes`, `status`, `revert_status`, `organisation_email`,
 `is_txc_app`, `txc_app_type`, `reason_cancelled`, `reason_refused`, `reason_sn_refused`, `short_notice_refused`,
 `service_no`, `received_date`, `effective_date`, `end_date`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 1, 'bs_no', 1, '', 110, 1, 1, 14686, 'PD2737280/14686', 'Doncaster', 'Sheffield', 'York', 'Other details', 0,
   0, 0, '', 0, '', 0, '', '', 0, 0, 'Route description', 0, 0, 0, null, 0, 0, 'Stopping arrangements', 0,
  'Trc notes', 'breg_s_registered', 'revert status', '', 1, '', '', '', '', 0, 90839, null, null, null, null, null, 1),
  (2, 1, 'bs_no', 1, '', 110, 1, 1, 15711, 'PD2737280/15711', 'Leeds', 'Doncaster', 'York', 'Other details', 0,
   0, 0, '', 0, '', 0, '', '', 0, 0, 'Route description', 0, 0, 0, null, 0, 0, 'Stopping arrangements', 0,
   'Trc notes', 'breg_s_registered', 'revert status', '', 1, '', '', '', '', 0, 46474, null, null, null, null, null, 1);

INSERT INTO `bus_reg_other_service`
(`id`, `bus_reg_id`, `last_modified_by`, `created_by`, `service_no`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 1, 90840, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (2, 1, 1, 1, 90841, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1);

INSERT INTO `bus_short_notice`
(`id`, `last_modified_by`, `created_by`, `bus_reg_id`, `bank_holiday_change`, `unforseen_change`, `unforseen_detail`,
 `timetable_change`, `timetable_detail`, `replacement_change`, `replacement_detail`, `holiday_change`, `holiday_detail`,
 `trc_change`, `trc_detail`, `police_change`, `police_detail`, `special_occasion_change`, `special_occasion_detail`,
 `connection_change`, `connection_detail`, `not_available_change`, `not_available_detail`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 2, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
   'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
   null, null, 1),
  (2, 1, 1, 1, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
   'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
   null, null, 1);

INSERT INTO `bus_notice_period`
(`id`, `notice_area`, `standard_period`, `cancellation_period`, `created_by`, `last_modified_by`, `created_on`,
 `last_modified_on`,`version`)
VALUES
  (1,'Scotland',56,90,NULL,NULL,NULL,NULL,1),
  (2,'Other',56,0,NULL,NULL,NULL,NULL,1);

INSERT INTO `bus_service_type` (`id`,`description`,`txc_service_type_name`)
VALUES
  (1,'Normal Stopping','NormalStopping'),
  (2,'Limited Stop','LimitedStops'),
  (3,'Frequent Service',NULL),
  (4,'Hail & Ride','HailAndRide'),
  (5,'Excursion or Tour','ExcursionOrTour'),
  (6,'School or Works','SchoolOrWorks'),
  (7,'Dial-a-ride','DialARide'),
  (8,'Circular',NULL),
  (9,'Rural Bus Service','RuralService'),
  (10,'Flexible Registration','Flexible');

INSERT INTO `complaint` (`complainant_forename`, `complainant_family_name`, `status`, `complaint_type`, `created_by`,
    `last_modified_by`, `case_id`, `complaint_date`, `driver_forename`, `driver_family_name`, `description`, `vrm`,
    `created_on`, `last_modified_on`, `version`)
VALUES
    ('Complainant First Name', 'Complainant Last Name', 'cs_ack', 'ct_cov', NULL, NULL, 24, NOW(), 'Driver F John',
    'Driver L Smith', 'Some major complaint about condition of vehicle', 'VRM123T', NOW(), NOW(), 1),
        ('John', 'Smith', 'cs_ack', 'ct_cov', NULL, NULL, 24, NOW(), 'Driver F Joe',
    'Driver L Bloggs', 'Exhaust fumes from parked vehicles', 'ABC456S', NOW(), NOW(), 1),
        ('Fred', 'Jones', 'cs_ack', 'ct_cov', NULL, NULL, 24, NOW(), 'Alberto',
    'Van der Groot', 'Speeding', 'SHA123S', NOW(), NOW(), 1),
        ('Janet', 'Porter', 'cs_ack', 'ct_cov', NULL, NULL, 24, NOW(), 'Ian',
    'McDonald', 'Revving engine early in morning', 'PRG426F', NOW(), NOW(), 1);

INSERT INTO `condition_undertaking` (`id`, `case_id`, `licence_id`, `operating_centre_id`, `created_by`,
    `last_modified_by`, `added_via`, `attached_to`, `condition_type`, `deleted_date`, `is_draft`,
    `is_fulfilled`, `notes`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,24,NULL,16,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 1',NOW(),NULL,1),
    (2,24,NULL,16,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 2',NOW(),NULL,1),
    (3,24,NULL,21,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 3',NOW(),NULL,1),
    (4,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_und',NULL,0,1,'Some notes 4',NOW(),NULL,1),
    (5,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_und',NULL,0,1,'Some notes 5',NOW(),NULL,1),
    (6,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,1,'Some notes 6',NOW(),NULL,1),
    (7,24,NULL,48,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 7',NOW(),NULL,1),
    (8,24,NULL,37,NULL,NULL,'cav_case','cat_oc','cdt_und',NULL,0,1,'Some ninvoice_nootes 8',NOW(),NULL,1),
    (9,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 9',NOW(),NULL,1),
    (10,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 10',NOW(),NULL,1),
    (11,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 11',NOW(),NULL,1);

INSERT INTO `contact_details` (`id`,`contact_type`,`address_id`,`organisation_id`,`person_id`,`licence_id`,
   `last_modified_by`,`created_by`,`fao`,`forename`,`family_name`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (1,'ct_ta',26,NULL,NULL,NULL,2,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
  (7,'ct_reg',7,7,9,NULL,2,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(8,'ct_corr',8,NULL,10,NULL,2,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(21,'ct_oc',21,1,NULL,NULL,0,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(25,'ct_def',25,1,NULL,NULL,4,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(26,'ct_def',26,1,NULL,NULL,0,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(27,'ct_def',27,1,NULL,NULL,2,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(29,'ct_def',29,7,NULL,NULL,3,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(30,'ct_reg',30,30,NULL,NULL,2,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(31,'ct_corr',31,NULL,NULL,NULL,0,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(37,'ct_oc',37,30,NULL,NULL,2,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(39,'ct_oc',39,30,NULL,NULL,4,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(41,'ct_reg',41,41,NULL,NULL,2,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(42,'ct_corr',42,NULL,NULL,NULL,1,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(54,'ct_reg',54,54,NULL,NULL,4,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(55,'ct_corr',55,NULL,NULL,NULL,3,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(63,'ct_reg',63,63,NULL,NULL,3,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(64,'ct_corr',64,NULL,NULL,NULL,0,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(67,'ct_oc',67,63,NULL,NULL,4,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(72,'ct_oc',72,63,NULL,NULL,2,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(75,'',75,75,NULL,NULL,4,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(76,'ct_corr',76,NULL,46,NULL,4,1,'Important Person',NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(77,'ct_corr',72,NULL,46,NULL,4,1,'Important Person',NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(100,'ct_reg',100,100,44,NULL,4,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(101,'ct_team_user',26,NULL,NULL,NULL,4,1,NULL,'Logged in','User',0,'loggedin@user.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(102,'ct_corr',41,1,NULL,7,1,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
(104,'ct_tm',104,1,77,7,1,1,NULL,NULL,NULL,0,'some@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1);

INSERT INTO `conviction` (`id`, `case_id`, `created_by`, `last_modified_by`, `category_text`,
`person_firstname`, `person_lastname`, `birth_date`,
    `offence_date`, `conviction_date`, `court`, `penalty`, `costs`, `msi`, `operator_name`,
    `defendant_type`, `notes`, `taken_into_consideration`, `person_id`, `created_on`, `last_modified_on`, `version`,
    `conviction_category`) VALUES
    (25,24,3,4,'Test Category text 1',NULL,NULL,'1971-11-05','2012-03-10','2012-06-15','FPN','3 points on licence',
    '60',0,
    'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1, 'conv_c_cat_1'),
    (26,24,0,4,'Conviction Child Category 1','John','Smith','1980-02-20','2012-04-10','2012-05-15',
    'Leeds Magistrate court',
    '3 points on licence','60',0,'','def_t_owner',NULL,NULL,4,NOW(),NOW(),1, 'conv_c_cat_2'),
    (27,24,1,3,'Conviction Child Category 3','Boris','Johnson','1962-08-12','2012-12-17','2013-03-02','FPN',
    '3 points on licence',
    '60',0,'',
    'def_t_owner',NULL,NULL,4,NOW(),NOW(),1, 'conv_c_cat_4'),
    (29,24,3,3,'Conviction Child Category 4',NULL,NULL,'1976-03-11', '2012-03-10','2012-06-15',
    'Leeds Magistrate court',
    '6 monthly investigation','2000',1,'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1, null);

INSERT INTO `legacy_offence` (`id`, `created_by`, `last_modified_by`, `definition`, `is_trailer`, `num_of_offences`,
    `offence_authority`, `offence_date`, `offence_to_date`, `offender_name`, `points`, `position`, `offence_type`,
    `notes`, `vrm`, `created_on`, `last_modified_on`, `version`)
VALUES
    (1, 1, 1, 'Some Definition', 1, 1, 'Authority 1', '2014-09-26', '2015-09-26', 'Some Offender', 3,
    'Some Position', 'Some Offence Type', 'Some Notes for Offence', 'VRM12', NOW(), NOW(), 1);

INSERT INTO `legacy_case_offence` (`case_id`, `legacy_offence_id`)
VALUES
    (24, 1);

INSERT INTO `ebsr_submission` (`id`, `document_id`, `ebsr_submission_type_id`,
    `ebsr_submission_status_id`, `bus_reg_id`, `submitted_date`, `licence_no`, `organisation_email_address`,
    `application_classification`, `variation_no`, `tan_code`, `registration_no`, `validation_start`, `validation_end`,
    `publish_start`, `publish_end`, `process_start`, `process_end`, `distribute_start`, `distribute_end`,
    `distribute_expire`, `is_from_ftp`, `organisation_id`) VALUES
  (1, null, 1, 1, 1, null, 110, null, null, null, null, null, null, null, null, null, null, null, null, null,
   null, 0, null);

INSERT INTO `fee` (`id`, `application_id`, `licence_id`, `fee_status`, `receipt_no`, `created_by`, `last_modified_by`, `description`,
    `invoiced_date`, `received_date`, `amount`, `received_amount`, `created_on`, `last_modified_on`, `version`, `payment_method`, `waive_reason`) VALUES
    (7,NULL,7,'lfs_ot',NULL,1,NULL,'Application fee','2013-11-25 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (30,NULL,110,'lfs_pd','654321',1,2,'Application fee','2013-11-22 00:00:00','2014-01-13 00:00:00',251.00,251.00,NULL,NULL,1,'fpm_card_online',NULL),
    (41,NULL,110,'lfs_wr','345253',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,1,NULL,NULL),
    (54,NULL,110,'lfs_ot','829485',1,NULL,'Application fee','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (63,NULL,110,'lfs_ot','481024',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (75,NULL,110,'lfs_ot','964732',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (76,1,110,'lfs_wr','234343',1,NULL,'Application fee 1','2013-11-25 00:00:00',NULL,250.50,0.50,NULL,NULL,2,NULL,NULL),
    (77,1,110,'lfs_wr','836724',1,NULL,'Application fee 2','2013-11-22 00:00:00',NULL,251.75,0.00,NULL,NULL,2,NULL,NULL),
    (78,1,110,'lfs_wr','561023',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,3,NULL,NULL),
    (79,1,110,'lfs_wr','634820',1,NULL,'Application fee 3','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,2,NULL,NULL),
    (80,1,110,'lfs_pd','458750',1,2,'Application fee 4','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash',NULL),
    (81,1,110,'lfs_ot','837495',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL),
    (82,1,30,'lfs_ot','354784',1,NULL,'Bus route 1','2013-10-23 00:00:00',NULL,500.00,0.00,NULL,NULL,2,NULL,NULL),
    (83,1,110,'lfs_wr','435235',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (84,1,110,'lfs_ot','435563',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL),
    (85,1,110,'lfs_wr','534633',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL),
    (86,1,110,'lfs_ot','426786',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL),
    (87,1,110,'lfs_w','68750',1,2,'Application fee 6','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash','some waive reason'),
    (88,1,110,'lfs_cn','78750',1,2,'Application fee 7','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_card_online',NULL);

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `created_by`, `last_modified_by`, `goods_or_psv`, `lic_no`, `status`,
    `ni_flag`, `licence_type`, `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`,
    `safety_ins_vehicles`, `safety_ins_trailers`, `safety_ins_varies`,
    `tachograph_ins`, `tachograph_ins_name`, `created_on`, `last_modified_on`, `version`) VALUES
    (7,1,'B',1,4,'lcat_gv','OB1234567','lsts_valid',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',4,12,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),

    -- extra licence for application 1
    (201,1,'B',0,1,NULL,'OB4234560','lsts_not_submitted',NULL,NULL,'2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (202,1,'B',0,1,'lcat_gv','OB4234561','lsts_consideration',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (203,1,'B',0,1,'lcat_psv','OB4234562','lsts_surrendered',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (204,1,'B',0,1,'lcat_gv','OB4234563','lsts_unlicenced',1,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (205,1,'B',0,1,'lcat_psv','OB4234564','lsts_terminated',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (206,1,'B',0,1,'lcat_psv','OB4234565','lsts_withdrawn',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (207,1,'B',0,1,'lcat_psv','OB4234566','lsts_suspended',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (208,1,'B',0,1,'lcat_psv','OB4234567','lsts_curtailed',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',1,
    3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (209,1,'B',0,1,'lcat_psv','OB4234568','lsts_revoked',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),

    (30,30,'B',0,1,'lcat_gv','OB1234568','lsts_not_submitted',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (41,41,'B',2,2,'lcat_gv','OB1234577','lsts_not_submitted',0,'ltyp_sn','2007-01-12','2007-01-12','2007-01-12','',1,
    21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (54,54,'B',2,4,'lcat_gv','OB1234578','lsts_not_submitted',0,'ltyp_r','2007-01-12','2007-01-12','2007-01-12','',0,4,NULL,NULL,NULL,NULL,
    NULL,NULL, NULL,NOW(),NOW(),1),
    (63,63,'D',4,0,'lcat_psv','PD1234589','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',1,7,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (75,75,'D',4,4,'lcat_psv','PD2737289','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (100,100,'D',4,0,'lcat_psv','PD1001001','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),2),
    (110,75,'D',4,4,'lcat_psv','PD2737280','lsts_not_submitted',0,'ltyp_r','2010-01-12','2010-01-12','2010-01-12','',0,10,5,5,NULL,NULL,
    NULL,NULL,NULL,NOW(),NOW(),1),
    (114,104,'B',NULL,NULL,NULL,'OB1534567','lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1),
    (115,105,'S',NULL,NULL,'lcat_psv','TS1234568','lsts_not_submitted',0,'ltyp_sr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NOW(),NULL,1);

INSERT INTO `licence_vehicle` (`id`, `licence_id`, `vehicle_id`, `created_by`, `last_modified_by`,
    `specified_date`, `removal_date`, `created_on`,
    `last_modified_on`, `version`) VALUES
    (1,7,1,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (2,7,2,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (3,7,3,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (4,7,4,NULL,4,'2013-02-20 00:00:00','2013-03-20 15:40:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (5,30,4,NULL,4,'2013-04-20 00:00:00','2013-05-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (6,41,4,NULL,4,'2013-05-22 00:00:00','2013-06-10 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (7,54,4,NULL,4,'2013-06-20 00:00:00','2013-07-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (8,63,4,NULL,4,'2013-07-24 00:00:00','2013-09-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (9,75,4,NULL,4,'2013-10-20 00:00:00','2013-11-02 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (10,100,4,NULL,4,'2014-11-14 00:00:00','2013-11-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (11,110,4,NULL,4,'2014-11-25 00:00:00','2013-11-26 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (12,114,4,NULL,4,'2014-02-20 00:00:00','2014-05-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (13,115,4,NULL,4,'2014-06-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (14,208,4,NULL,4,'2014-06-20 00:00:00','2010-01-12 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (15,208,4,NULL,4,'2014-06-20 00:00:00','2010-01-12 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1);

INSERT INTO goods_disc (`licence_vehicle_id`, `is_copy`, `disc_no`, `issued_date`, `is_interim`, `created_on`, `last_modified_on`, `version`) VALUES
    (1, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (2, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (3, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (4, 0, '1234', '2014-02-20 00:00:00', 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (5, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (6, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (7, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (8, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (9, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (10, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (11, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (12, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (13, 0, NULL, NULL, 0, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1);

INSERT INTO psv_disc (`id`, `licence_id`, `disc_no`, `issued_date`, `created_on`, `version`) VALUES
    (1, 63, NULL, NULL, '2014-02-20 00:00:00', 1),
    (2, 75, NULL, NULL, '2014-02-20 00:00:00', 1),
    (3, 100, NULL, NULL, '2014-02-20 00:00:00', 1),
    (4, 110, NULL, NULL, '2014-02-20 00:00:00', 1),
    (5, 63, NULL, NULL, '2014-02-20 00:00:00', 1),
    (6, 75, NULL, NULL, '2014-02-20 00:00:00', 1),
    (7, 100, NULL, NULL, '2014-02-20 00:00:00', 1),
    (8, 110, NULL, NULL, '2014-02-20 00:00:00', 1),
    (9, 63, NULL, NULL, '2014-02-20 00:00:00', 1),
    (10, 75, NULL, NULL, '2014-02-20 00:00:00', 1),
    (11, 100, NULL, NULL, '2014-02-20 00:00:00', 1),
    (12, 110, NULL, NULL, '2014-02-20 00:00:00', 1),
    (13, 110, '1231', '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (14, 110, '1234', '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
    (15, 30, NULL, NULL, '2014-02-20 00:00:00', 1);

INSERT INTO `note` (`id`, `note_type`, `last_modified_by`, `bus_reg_id`, `created_by`,
  `irfo_psv_auth_id`, `licence_id`, `case_id`, `irfo_gv_permit_id`, `application_id`, `comment`,
  `priority`, `created_on`, `last_modified_on`, `version`)
VALUES
(1, 'note_t_app', NULL, NULL, 2, NULL, 7, 28, NULL, 1, 'This is an app note', 0, '2011-10-03 00:00:00', NULL, 1),
(2, 'note_t_lic', NULL, NULL, 4, NULL, 7, 28, NULL, NULL, 'This is a licence note', 1, '2011-10-03 00:00:00', NULL, 1),
(3, 'note_t_app', NULL, NULL, 2, NULL, 7, 28, NULL, 1, 'This is an app note', 0, '2011-10-03 00:00:00', NULL, 1),
(4, 'note_t_app', NULL, NULL, 3, NULL, 7, 28, NULL, 1, 'This is an app note', 0, '2011-10-03 00:00:00', NULL, 1),
(5, 'note_t_lic', NULL, NULL, 5, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-03 00:00:00', NULL, 1),
(6, 'note_t_case', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a case note', 0, '2011-10-03 00:00:00', NULL, 1),
(7, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', NULL, 1),
(8, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2012-10-10 00:00:00', NULL, 1),
(9, 'note_t_bus', NULL, 1, 3, NULL, 110, 75, NULL, NULL, 'This is a bus reg note', 0, '2012-10-10 00:00:00', NULL, 1),
(10, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', NULL, 1),
(11, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-13 00:00:00', NULL, 1),
(12, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-15 00:00:00', NULL, 1),
(13, 'note_t_lic', NULL, NULL, 3, NULL, 7, 28, NULL, NULL, 'This is a licence note', 0, '2011-10-12 00:00:00', NULL, 1);


INSERT INTO `operating_centre` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `address_id`) VALUES
    (16,3,2,NOW(),NOW(),1,8),
    (21,1,3,NOW(),NOW(),1,21),
    (37,2,1,NOW(),NOW(),1,37),
    (39,1,3,NOW(),NOW(),1,39),
    (48,1,3,NOW(),NOW(),1,29),
    (67,0,1,NOW(),NOW(),1,67),
    (72,1,4,NOW(),NOW(),1,72);

INSERT INTO `opposer`
(`id`, `opposer_type`, `last_modified_by`, `created_by`, `contact_details_id`, `created_on`, `last_modified_on`,
 `version`)
VALUES
  (1, 'obj_t_local_auth', 1, 1, 7, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
  (2, 'obj_t_police', 1, 1, 8, '2014-02-21 00:00:00', '2014-02-21 00:00:00', 1);

INSERT INTO `opposition`
(`id`, `opposition_type`, `application_id`, `opposer_id`, `last_modified_by`, `created_by`, `is_copied`,
 `raised_date`, `is_in_time`, `is_public_inquiry`, `is_withdrawn`, `is_valid`, `valid_notes`, `notes`, `deleted_date`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 'otf_eob', 1, 1, 1, 1, 1, '2014-02-19', 1, 1, 0, 1, 'Valid notes', 'Notes', null, '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1),
  (2, 'otf_rep', 1, 1, 1, 1, 1, '2014-02-19', 0, 0, 1, 1, 'Valid notes', 'Notes', null, '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1);

INSERT INTO `opposition_grounds`
(`id`, `grounds`, `last_modified_by`, `created_by`, `opposition_id`, `is_representation`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 'ogf_env', 1, 1, 1, 1, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
  (2, 'ogf_parking', 1, 1, 1, 1, '2014-02-24 00:00:00', '2014-02-24 00:00:00', 1),
  (3, 'ogf_safety', 1, 1, 2, 1, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
  (4, 'ogf_size', 1, 1, 2, 1, '2014-02-24 00:00:00', '2014-02-24 00:00:00', 1);


INSERT INTO `organisation` (`id`,`lead_tc_area_id`, `created_by`, `last_modified_by`, `company_or_llp_no`, `name`,
`is_mlh`, `type`,
    `created_on`, `last_modified_on`, `version`) VALUES
    (1,'B',1,3,'1234567','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (30,'C',1,4,'98765432','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (41,'D',0,4,'241341234','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1),
    (54,'F',3,4,'675675334','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1),
    (63,'G',1,2,'353456456','Leeds bus service ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (75,'H',1,0,'12345A1123','Leeds city council',0,'org_t_pa',NOW(),NOW(),1),
    (100,'K',1,3,'100100','Test partnership',0,'org_t_p','2014-01-28 16:25:35','2014-01-28 16:25:35',2),
    (104,'M',NULL,NULL,'1234567','Company Name',0,'org_t_rc',NULL,NULL,1),
    (105,'N',1,3,NULL,'SR Orgaisation',0,'org_t_rc',NOW(),NOW(),1);

INSERT INTO `organisation_person` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `person_id`, `organisation_id`) VALUES
    (1,NULL,NULL,NULL,NULL,1,78,7),
    (2,NULL,NULL,NULL,NULL,1,77,7),
    (3,NULL,NULL,NULL,NULL,1,77,1),
    (4,NULL,NULL,NULL,NULL,1,78,1),
    (5,NULL,NULL,NULL,NULL,1,78,100),
    (6,NULL,NULL,NULL,NULL,1,77,100);

INSERT INTO `person` (`id`, `created_by`, `last_modified_by`, `title`, `birth_date`, `forename`, `family_name`,
    `other_name`, `created_on`, `last_modified_on`, `version`, `deleted_date`, `birth_place`) VALUES
    (4,NULL,NULL,'Mr','1960-02-01 00:00:00','Jack','Da Ripper',NULL,NULL,NULL,1,NULL, NULL),
    (9,NULL,NULL,'Mr','1960-02-15 00:00:00','John','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (10,NULL,NULL,'Mr','1965-07-12 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (11,NULL,NULL,'Mr','1970-04-14 00:00:00','Joe','Lambert',NULL,NULL,NULL,1,NULL, NULL),
    (12,NULL,NULL,'Mr','1975-04-15 00:00:00','Tom','Cooper',NULL,NULL,NULL,1,NULL, NULL),
    (13,NULL,NULL,'Mr','1973-03-03 00:00:00','Mark','Anthony',NULL,NULL,NULL,1,NULL, NULL),
    (14,NULL,NULL,'Mr','1975-02-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL, NULL),
    (15,NULL,NULL,'Mr','1973-12-09 00:00:00','Tom','Anthony',NULL,NULL,NULL,1,NULL, NULL),
    (32,NULL,NULL,'Mr','1960-04-15 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (33,NULL,NULL,'Mr','1965-04-12 00:00:00','Mark','Jones',NULL,NULL,NULL,1,NULL, NULL),
    (34,NULL,NULL,'Mr','1970-06-14 00:00:00','Tim','Lambert',NULL,NULL,NULL,1,NULL, NULL),
    (35,NULL,NULL,'Mr','1975-04-18 00:00:00','Joe','Cooper',NULL,NULL,NULL,1,NULL, NULL),
    (43,NULL,NULL,'Mr','1960-02-15 00:00:00','Ted','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (44,NULL,NULL,'Mr','1970-04-14 00:00:00','Peter','Lambert',NULL,NULL,NULL,1,NULL, NULL),
    (45,NULL,NULL,'Mr','1975-04-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL, NULL),
    (46,NULL,NULL,'Mr','1973-03-03 00:00:00','David','Anthony',NULL,NULL,NULL,1,NULL, NULL),
    (47,NULL,NULL,'Mr','1975-02-15 00:00:00','Lewis','Howarth',NULL,NULL,NULL,1,NULL, NULL),
    (59,NULL,NULL,'Mr','1973-03-03 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (60,NULL,NULL,'Mr','1975-02-15 00:00:00','Lewis','Hamilton',NULL,NULL,NULL,1,NULL, NULL),
    (65,NULL,NULL,'Mr','1972-02-15 00:00:00','Jonathan','Smith',NULL,NULL,NULL,1,NULL, NULL),
    (66,NULL,NULL,'Mr','1975-03-15 00:00:00','Tim','Cooper',NULL,NULL,NULL,1,NULL, NULL),
    (77,NULL,NULL,'Mr','1972-02-15 00:00:00','Tom','Jones',NULL,NULL,NULL,1,NULL, 'Leeds'),
    (78,NULL,NULL,'Mr','1975-03-15 00:00:00','Keith','Winnard',NULL,NULL,NULL,1,NULL, NULL);

INSERT INTO `disqualification` (`id`, `created_by`, `last_modified_by`, `is_disqualified`, `period`, `notes`,
    `created_on`, `last_modified_on`, `version`, `person_id`) VALUES
    (10,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,10),
    (13,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,13),
    (15,NULL,NULL,1,'6 months','TBC',NOW(),NULL,1,15),
    (32,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,32),
    (36,NULL,NULL,1,'6 months','TBC',NOW(),NULL,1,15);

INSERT INTO `phone_contact` (`id`,`phone_contact_type`,`phone_number`,`details`,
    `contact_details_id`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'phone_t_tel','0113 123 1234','',101,NULL,NULL,NULL,NULL,1);

INSERT INTO `pi` (`id`,`agreed_by_tc_role`,`decided_by_tc_role`,`written_outcome`,`agreed_by_tc_id`,`decided_by_tc_id`,
    `assigned_to`,`pi_status`,`case_id`,`created_by`,`last_modified_by`,`witnesses`,`section_code_text`,
    `licence_revoked_at_pi`,`licence_suspended_at_pi`,`licence_curtailed_at_pi`,
    `notification_date`,`decision_notes`,`call_up_letter_date`,`brief_to_tc_date`,`written_reason_date`,
    `decision_letter_sent_date`,`tc_written_decision_date`,`tc_written_reason_date`,`written_reason_letter_date`,
    `dec_sent_after_written_dec_date`,`agreed_date`,`is_cancelled`,`decision_date`,`deleted_date`,
    `comment`,`closed_date`,`created_on`,`last_modified_on`,`version`)
  VALUES
    (1,'tc_r_dtc',NULL,NULL,2,NULL,NULL,'pi_s_reg',24,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,
     NULL,NULL,NULL,NULL,'2014-11-24',0,NULL,NULL,'Test Pi',NULL,'2014-11-24 10:06:49',NULL,1);

INSERT INTO `pi_venue` (`id`, `traffic_area_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`,
    `version`, `name`, `address_id`) VALUES
    (1,'B',NULL,NULL,NULL,NULL,1,'venue_1',21),
    (2,'B',NULL,NULL,NULL,NULL,1,'venue_2',22),
    (3,'B',NULL,NULL,NULL,NULL,1,'venue_3',23),
    (4,'B',NULL,NULL,NULL,NULL,1,'venue_4',24),
    (5,'B',NULL,NULL,NULL,NULL,1,'venue_5',25),
    (6,'C',NULL,NULL,NULL,NULL,1,'venue_6',26),
    (7,'C',NULL,NULL,NULL,NULL,1,'venue_7',27),
    (8,'C',NULL,NULL,NULL,NULL,1,'venue_8',28),
    (9,'C',NULL,NULL,NULL,NULL,1,'venue_9',29),
    (10,'C',NULL,NULL,NULL,NULL,1,'venue_10',30),
    (11,'C',NULL,NULL,NULL,NULL,1,'venue_11',31),
    (12,'D',NULL,NULL,NULL,NULL,1,'venue_12',32),
    (13,'D',NULL,NULL,NULL,NULL,1,'venue_13',33),
    (14,'D',NULL,NULL,NULL,NULL,1,'venue_14',34),
    (15,'D',NULL,NULL,NULL,NULL,1,'venue_15',35),
    (16,'D',NULL,NULL,NULL,NULL,1,'venue_16',36),
    (17,'F',NULL,NULL,NULL,NULL,1,'venue_17',37),
    (18,'F',NULL,NULL,NULL,NULL,1,'venue_18',38),
    (19,'F',NULL,NULL,NULL,NULL,1,'venue_19',39),
    (20,'F',NULL,NULL,NULL,NULL,1,'venue_20',40),
    (21,'F',NULL,NULL,NULL,NULL,1,'venue_21',41),
    (22,'F',NULL,NULL,NULL,NULL,1,'venue_22',42),
    (23,'G',NULL,NULL,NULL,NULL,1,'venue_23',43),
    (24,'G',NULL,NULL,NULL,NULL,1,'venue_24',44),
    (25,'G',NULL,NULL,NULL,NULL,1,'venue_25',45),
    (26,'G',NULL,NULL,NULL,NULL,1,'venue_26',46),
    (27,'G',NULL,NULL,NULL,NULL,1,'venue_27',47),
    (28,'G',NULL,NULL,NULL,NULL,1,'venue_28',48),
    (29,'H',NULL,NULL,NULL,NULL,1,'venue_29',49),
    (32,'H',NULL,NULL,NULL,NULL,1,'venue_32',52),
    (33,'H',NULL,NULL,NULL,NULL,1,'venue_33',53),
    (34,'H',NULL,NULL,NULL,NULL,1,'venue_34',54),
    (35,'K',NULL,NULL,NULL,NULL,1,'venue_35',55),
    (36,'K',NULL,NULL,NULL,NULL,1,'venue_36',56),
    (37,'K',NULL,NULL,NULL,NULL,1,'venue_37',57),
    (38,'M',NULL,NULL,NULL,NULL,1,'venue_38',58),
    (39,'M',NULL,NULL,NULL,NULL,1,'venue_39',59),
    (40,'M',NULL,NULL,NULL,NULL,1,'venue_40',60),
    (41,'N',NULL,NULL,NULL,NULL,1,'venue_41',61),
    (42,'N',NULL,NULL,NULL,NULL,1,'venue_42',62),
    (43,'N',NULL,NULL,NULL,NULL,1,'venue_43',63),
    (44,'N',NULL,NULL,NULL,NULL,1,'venue_44',64);

INSERT INTO `pi_hearing` (`id`,`presided_by_role`,`pi_id`,`pi_venue_id`,`last_modified_by`,`created_by`,
    `presiding_tc_id`,`presiding_tc_other`,`cancelled_reason`,`adjourned_reason`,`details`,`hearing_date`,
    `pi_venue_other`,`witnesses`,`is_cancelled`,`cancelled_date`,`adjourned_date`,`created_on`,
    `last_modified_on`,`version`)
  VALUES
    (1,'tc_r_htru',1,1,NULL,NULL,1,NULL,NULL,'Test adjourned reason',
     'S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26','2014-03-16 14:30:00',
     NULL,9,0,NULL,'2014-03-16','2014-11-24 10:22:24',NULL,1);

INSERT INTO `presiding_tc` (`id`, `name`) VALUES
    (1,'Presiding TC Name 1'),
    (2,'Presiding TC Name 2'),
    (3,'Presiding TC Name 3');

INSERT INTO `prohibition` (`id`, `prohibition_type`, `last_modified_by`, `created_by`, `case_id`, `prohibition_date`,
 `cleared_date`, `is_trailer`, `imposed_at`, `vrm`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 'pro_t_d', 1, 1, 24, '2014-01-24', '2014-03-11', 1, 'Doncaster', 'AB52 CDE', '2014-06-09 11:01:21',
   '2014-06-09 11:01:21', 1);

INSERT INTO `prohibition_defect` (`id`, `prohibition_id`, `last_modified_by`, `created_by`, `defect_type`, `notes`,
 `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 1, 'defect type', 'defect description', '2014-06-09 12:06:41', '2014-06-09 12:06:41', 1);

INSERT INTO `impounding`
    (`id`, `pi_venue_id`, `impounding_type`, `case_id`,
    `outcome`, `last_modified_by`, `presiding_tc_id`, `created_by`,
    `application_receipt_date`, `outcome_sent_date`, `close_date`,
    `pi_venue_other`, `hearing_date`, `notes`, `created_on`, `last_modified_on`, `version`)
VALUES
    (17, 3, 'impt_hearing', 24,
    'impo_returned', NULL, 1, NULL,
    '2014-06-09 11:15:00', '2014-06-11 14:30:00', NOW(),
    NULL, '2014-06-10 15:45:00', 'Some notes - db default', NOW(), NOW(), 1);

INSERT INTO `impounding_legislation_type`
    (`impounding_id`, `impounding_legislation_type_id`)
VALUES
    (17, 'imlgis_type_goods_ni1');

INSERT INTO `impounding_legislation_type`
    (`impounding_id`, `impounding_legislation_type_id`)
VALUES
    (17, 'imlgis_type_goods_ni2');

INSERT INTO `transport_manager_licence` (`id`, `licence_id`, `transport_manager_id`, `created_by`, `last_modified_by`,
    `deleted_date`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,7,1,NULL,NULL,NULL,NULL,NULL,1),
    (2,7,2,NULL,NULL,NULL,NULL,NULL,1);

INSERT INTO `tm_qualification` (`id`, `transport_manager_id`, `created_by`, `last_modified_by`, `country_code`,
    `qualification_type`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,NULL,NULL,'GB','CPCSI',NULL,NULL,1),
    (2,2,NULL,NULL,'GB','CPCSN',NULL,NULL,1);

INSERT INTO `trading_name` (`id`,`organisation_id`,`last_modified_by`,`created_by`,`licence_id`,`name`,`deleted_date`,
  `vi_action`,`created_on`,`last_modified_on`,`version`)
VALUES
  (7,1,4,3,7,'JSH Logistics',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (30,NULL,1,2,30,'JSH Removals',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (41,NULL,1,1,41,'TSG',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (54,NULL,1,1,54,'TSG',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (63,NULL,2,1,63,'Stagecoach',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (75,NULL,2,0,75,'LCC',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1),
  (110,NULL,2,0,110,'test',NULL,NULL,'2014-11-23 21:58:52','2014-11-23 21:58:52',1);

INSERT INTO `transport_manager` (`id`, `created_by`, `last_modified_by`, `tm_status`, `tm_type`, `contact_details_id`, `deleted_date`,
    `created_on`, `last_modified_on`, `version`) VALUES
    (1,NULL,NULL,'tm_st_A','tm_t_I',NULL,NULL,NULL,NULL,1),
    (2,NULL,NULL,'tm_st_A','tm_t_E',NULL,NULL,NULL,NULL,1),
    (3,NULL,NULL,'tm_st_A','tm_t_I',104,NULL,NULL,NULL,1);

INSERT INTO `user` (`id`, `team_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`, `deleted_date`,
    `name`,`contact_details_id`,`job_title`,`division_group`,`department_name`) VALUES
    (1,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Logged in user',101,'Accountant','Division 1','Department X'),
    (2,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'John Spellman',NULL,'','',''),
    (3,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Steve Fox',NULL,'','',''),
    (4,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Amy Wrigg',NULL,'','',''),
    (5,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Phil Jowitt',NULL,'','',''),
    (6,3,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Kevin Rooney',NULL,'','',''),
    (7,4,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Sarah Thompson',NULL,'','','');

INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7);

INSERT INTO `vehicle` (`id`, `created_by`, `last_modified_by`, `vrm`, `plated_weight`,
    `certificate_no`, `vi_action`, `psv_type`, `created_on`,
    `last_modified_on`, `version`) VALUES
    (1,NULL,4,'VRM1',7200,'CERT10001',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (2,NULL,6,'VRM2',3500,'CERT10002',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (3,NULL,5,'VRM3',3800,'CERT10003',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (4,NULL,1,'VRM4',6800,'CERT10004',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (5,NULL,4,'VRM1',7200,'CERT10005',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (6,NULL,6,'VRM2',3500,'CERT10006',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (7,NULL,5,'VRM3',3800,'CERT10007',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (8,NULL,1,'VRM4',6800,'CERT10008',NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1);

-- Cases
INSERT INTO `cases` (`id`,`case_type`,`erru_case_type`,`licence_id`,`application_id`,`transport_manager_id`,
   `last_modified_by`,`created_by`,`ecms_no`,`open_date`,`close_date`,`description`,`is_impounding`,
   `erru_originating_authority`,`erru_transport_undertaking_name`,`erru_vrm`,`annual_test_history`,`prohibition_note`,
   `conviction_note`,`penalties_note`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (24,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2012-03-21',NULL,'Case for convictions against company
  directors',0,NULL,NULL,NULL,'Annual test history for case 24',NULL,NULL,NULL,NULL,'2013-11-12 12:27:33',NULL,1),
  (28,'case_t_app',NULL,7,1,NULL,NULL,NULL,'E123444','2012-06-13',NULL,'Convictions against operator',0,NULL,NULL,
  NULL,'Annual Test History for case 28',NULL,NULL,NULL,NULL,'2014-01-01 11:11:11',NULL,1),
  (29,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,'Polish Transport Authority','Polish Transport Authority','GH52 ABC',NULL,NULL,NULL,'comment',NULL,'2014-01-11 11:11:11','2014-11-07 12:47:07',3),
  (30,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (31,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-05-25','345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (32,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (33,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-03-29','345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (34,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (35,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-04-15','45645645645',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (36,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (37,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-04-23','3453g345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (38,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'2345678','2014-02-13','2014-05-25','MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (39,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14','2014-05-25','new test case 2',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (40,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (41,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (42,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2013-06-01',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (43,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123444','2013-06-02',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (44,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (45,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (46,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (47,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (48,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (49,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (50,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'45645645645',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (51,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (52,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'3453g345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (53,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'2345678','2014-02-13',NULL,'MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (54,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14',NULL,'new test case 2',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (55,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (56,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (57,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2013-11-01',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (58,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123444','2013-11-02',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (59,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (60,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (61,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (62,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (63,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (64,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (65,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'45645645645',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (66,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (67,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'3453g345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (68,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'2345678','2014-02-13',NULL,'MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (69,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14',NULL,'new test case 2',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (70,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (71,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (72,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2013-11-02',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (73,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123444','2013-11-03',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (74,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (75,'case_t_lic',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'PSV licence case',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (76,'case_t_app',NULL,110,1,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to an application',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (77,'case_t_lic',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to a licence',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (78,'case_t_msi',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (79,'case_t_msinre',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI with no response entered',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (80,'case_t_msirnys',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI with response not sent',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (81,'case_t_nmsi',NULL,110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to Non-MSI',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (82,'case_t_tm',NULL,110,NULL,1,NULL,NULL,'','2014-02-11',NULL,'Case linked to an internal Transport manager',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (83,'case_t_tm',NULL,110,NULL,2,NULL,NULL,'','2014-02-11',NULL,'Case linked to an external Transport manager',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1);

INSERT INTO team(id,version,name,traffic_area_id) VALUES
    (1,1,'Marketing',''),
    (2,1,'Development','B'),
    (3,1,'Infrastructure',''),
    (4,1,'Support','');

INSERT INTO `case_category` (`case_id`, `category_id`)
VALUES
    (29, 'case_cat_7');

    
/**
 * NOTE: These inserts can't be grouped into one as they insert different columns
 */
/* Application task */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (1,1,110,9,32,1,2,'A test task','2014-08-12',1);
    /* Licence task */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (2,null,110,1,69,1,2,'Another test task','2013-02-11',1);
/* IRFO task */
INSERT INTO task(id,irfo_organisation_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (3,1,8,57,1,2,'An organisation task','2014-05-01',1);
/* Transport Manager task */
INSERT INTO task(id,transport_manager_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (4,2,5,76,6,3,'A transport task','2010-01-01',1);
/* Case task */
INSERT INTO task(id,case_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (5,24,2,44,null,4,'A case task','2010-02-01',1);
/* Unlinked task */
INSERT INTO task(id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (6,7,49,null,null,'Unassigned task','2010-07-03',1);
/* Application, future, urgent task */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (7,2,110,9,32,1,2,'A test task','2018-09-27',1,1);
/* Licence, single licence holder */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (8,null,63,9,32,1,2,'Single licence','2012-09-27',0,1);

INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date,document_store_id) VALUES
    (1,7,'Test document not digital','testdocument1.doc',0,1,1,'doc_doc','2014-08-23 18:00:05',''),
    (2,7,'Test document digital','testdocument2.doc',1,1,1,'doc_doc','2014-08-25 12:04:35',''),
    (3,7,'Test document 3','testdocument3.doc',0,1,2,'doc_doc','2014-08-22 11:01:00',''),
    (4,7,'Test document 4','testdocument4.doc',0,2,3,'doc_doc','2014-08-24 16:23:00',''),
    (5,7,'Test document 5','testdocument5.xls',0,2,3,'doc_xls','2014-07-01 15:01:00',''),
    (6,7,'Test document 6','testdocument6.docx',0,2,3,'doc_docx','2014-07-05 09:00:05',''),
    (7,7,'Test document 7','testdocument7.xls',0,2,4,'doc_xls','2014-07-05 10:23:00',''),
    (8,7,'Test document 8','testdocument8.doc',1,2,4,'doc_doc','2014-07-05 10:45:00',''),
    (9,7,'Test document 9','testdocument9.ppt',1,2,4,'doc_ppt','2014-08-05 08:59:40',''),
    (10,7,'Test document 10','testdocument10.jpg',0,1,2,'doc_jpg','2014-08-08 12:47:00',''),
    (11,7,'Test document 11','testdocument11.txt',0,1,1,'doc_txt','2014-08-14 14:00:00',''),
    (12,7,'Test document 12','testdocument12.xls',1,1,2,'doc_xls','2014-08-28 14:03:00',''),
    (13,null,'GB Goods - New/Var App Incomplete - 1st Request for supporting docs','',1,5,1,'doc_rtf','2014-08-28 15:03:00','/templates/PUB_APPS_SUPP_DOCS_1ST(GB).rtf'),
    (14,null,'NI Goods - New/Var App Incomplete - 1st Request for supporting docs','',1,5,1,'doc_rtf','2014-09-09 12:00:00','/templates/PUB_APPS_SUPP_DOCS_1ST(NI).rtf'),
    (15,null,'GB PSV - New/App incomplete - 1st Request for supporting docs','',1,5,1,'doc_rtf','2014-09-09 12:00:00','/templates/PSV_NEW_APP_SUPP_DOCS_1ST.rtf'),
    (16,null,'GB Goods - New/App incomes - Final Request for supporting docs','',1,5,1,'doc_rtf','2014-09-09 12:00:00','/templates/GV_Application_Incomplete_Final_Request_For_Supporting_Docs.rtf'),
    (17,null,'NI Goods - New/App incomes - Final Request for supporting docs','',1,5,1,'doc_rtf','2014-09-09 12:00:00','/templates/GV_Application_Incomplete_Final_Request_For_Supporting_Docs_(NI).rtf'),
    (18,null,'GB PSV - New/App incomes - Final Request for supporting docs','',1,5,1,'doc_rtf','2014-09-09 12:00:00','/templates/PSV_New_app_incomplete_final_request_for_supporting_docs.rtf');

INSERT INTO doc_template(id,category_id,document_sub_category_id,description,document_id,is_ni,suppress_from_op,version) VALUES
    (1,1,5,'NI Goods - New/Var App Incomplete - 1st Request for supporting docs',14,0,0,1),
    (2,1,5,'GB Goods - New/Var App Incomplete - 1st Request for supporting docs',13,0,0,1),
    (3,1,5,'GB PSV - New/App incomplete - 1st Request for supporting docs',15,0,0,1),
    (4,1,5,'GB Goods - New/App incomes - Final Request for supporting docs',16,0,0,1),
    (5,1,5,'NI Goods - New/App incomes - Final Request for supporting docs',17,0,0,1),
    (6,1,5,'GB PSV - New/App incomes - Final Request for supporting docs',18,0,0,1);

INSERT INTO doc_bookmark(id,name,description,version) VALUES
    (1,'sample_bookmark','A sample bookmark',1),
    (2,'another_sample_bookmark','Another sample bookmark',1),
    (3,'a_third_sample_bookmark','A third sample bookmark',1),
    (4,'application_type','Application type',1),
    (5,'p_unacceptable_advert','Unacceptable advert',1),
    (6,'warning_re_early_operating','Warning RE early operating',1);

INSERT INTO doc_paragraph(id,para_title,para_text,version) VALUES
    (1,'para 1','Sample paragraph 1.',1),
    (2,'para 2','Sample Paragraph 2.',1),
    (3,'para 3','Sample Paragraph 3.',1),
    (4,'para 4','Sample Paragraph 4.',1),
    (5,'app type 1','App type number one.',1),
    (6,'app type 2','App type number two.',1),
    (7,'unacceptable advert','Your advert was unacceptable.',1),
    (8,'early operating 1','Early operating text one.',1),
    (9,'early operating 2','Early operating text two!',1);

INSERT INTO doc_template_bookmark(doc_template_id,doc_bookmark_id,version) VALUES
    (1,1,1),
    (1,3,1),
    (1,2,1),
    (2,4,1),
    (2,5,1),
    (2,6,1),
    (3,1,1),
    (4,1,1),
    (5,1,1),
    (6,1,1);

INSERT INTO doc_paragraph_bookmark(doc_bookmark_id,doc_paragraph_id,version) VALUES
    (1,1,1),
    (1,2,1),
    (1,3,1),
    (2,2,1),
    (2,4,1),
    (3,4,1),
    (4,5,1),
    (4,6,1),
    (5,7,1),
    (6,8,1),
    (6,9,1);

/* Disc sequence dummy data */
INSERT INTO `disc_sequence` (
  `id`,`goods_or_psv`,`restricted`,`r_prefix`,`standard_national`,`sn_prefix`,
  `standard_international`, `si_prefix`, `traffic_area_id`,`version`,`is_self_serve`,
  `is_ni_self_serve`) VALUES
    (1,'lcat_gv',305069,'OK',472557,'OK',293379,'OK','K',1,0,0),
    (2,'lcat_gv',304435,'OF',556843,'OF',396163,'OF','F',1,0,0),
    (3,'lcat_gv',285053,'OH',531439,'OH',266083,'OH','H',1,0,0),
    (4,'lcat_gv',343603,'OD',480707,'OD',301603,'OD','D',1,0,0),
    (5,'lcat_gv',303267,'OC',637663,'OC',325647,'OC','C',1,0,0),
    (6,'lcat_gv',317281,'OB',590773,'OB',343105,'OB','B',1,0,0),
    (7,'lcat_gv',163111,'OG',247449,'OG',116379,'OG','G',1,0,0),
    (8,'lcat_gv',189799,'OM',404255,'OM',157277,'OM','M',1,0,0),
    (9,'lcat_psv',10225,'PK',54253,'PK',154221,'PK','K',1,0,0),
    (10,'lcat_psv',12187,'PF',50965,'PF',92279,'PF','F',1,0,0),
    (11,'lcat_psv',13237,'PH',55701,'PH',105111,'PH','H',1,0,0),
    (12,'lcat_psv',11597,'PD',46619,'PD',58689,'PD','D',1,0,0),
    (13,'lcat_psv',19901,'PC',67481,'PC',115301,'PC','C',1,0,0),
    (14,'lcat_psv',21151,'PB',49251,'PB',127179,'PB','B',1,0,0),
    (15,'lcat_psv',12213,'PG',23499,'PG',61789,'PG','G',1,0,0),
    (16,'lcat_psv',14299,'PM',67675,'PM',86713,'PM','M',1,0,0),
    (18,'lcat_gv',295889,'RS',774299,'NS',455705,'IS','D',1,0,0),
    (19,'lcat_gv',10231,'ON',4003,'ON',14533,'ON','N',1,0,0),
    (21,'lcat_gv',1363,'RX',1051,'NX',3973,'IX','N',1,0,0);

ALTER TABLE companies_house_request AUTO_INCREMENT=53;

/* Test submissions, 1 for each type */
INSERT INTO `submission` (`id`, `submission_type`, `last_modified_by`, `created_by`, `case_id`, `data_snapshot`, `closed_date`, `created_on`, `last_modified_on`, `version`) VALUES
(1,'submission_type_o_mlh',NULL,NULL,24, '{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"intelligence-unit-check":{"data":[]},"interim":{"data":[]},"advertisement":{"data":[]},"linked-licences-app-numbers":{"data":[]},"lead-tc-area":{"data":[]},"auth-requested-applied-for":{"data":[]},"transport-managers":{"data":[]},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"local-licence-history":{"data":[]},"linked-mlh-history":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"other-issues":{"data":[]},"financial-information":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:47:50',NULL,1),
(2,'submission_type_o_clo_g',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"intelligence-unit-check":{"data":[]},"interim":{"data":[]},"advertisement":{"data":[]},"auth-requested-applied-for":{"data":[]},"transport-managers":{"data":[]},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"local-licence-history":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"other-issues":{"data":[]},"financial-information":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:48:58',NULL,1),
(3,'submission_type_o_clo_psv',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"intelligence-unit-check":{"data":[]},"auth-requested-applied-for":{"data":[]},"transport-managers":{"data":[]},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"total-bus-registrations":{"data":[]},"local-licence-history":{"data":[]},"registration-details":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"other-issues":{"data":[]},"financial-information":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:50:04',NULL,1),
(4,'submission_type_o_clo_fep',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"previous-history":{"data":[]},"other-issues":{"data":[]},"waive-fee-late-fee":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:51:19',NULL,1),
(5,'submission_type_o_otc',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"intelligence-unit-check":{"data":[]},"linked-licences-app-numbers":{"data":[]},"lead-tc-area":{"data":[]},"current-submissions":{"data":[]},"transport-managers":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"local-licence-history":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"prohibition-history":{"data":[]},"conviction-fpn-offence-history":{"data":[{"id":25,"offenceDate":"2012-03-10T00:00:00+0000","convictionDate":"2012-06-15T00:00:00+0100","defendantType":{"description":"Operator","id":"def_t_op"},"name":"John Smith Haulage Ltd.","categoryText":"Test Category text 1","court":"FPN","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":26,"offenceDate":"2012-04-10T00:00:00+0100","convictionDate":"2012-05-15T00:00:00+0100","defendantType":{"description":"Owner","id":"def_t_owner"},"name":"John Smith","categoryText":"Conviction Child Category 1","court":"Leeds Magistrate court","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":27,"offenceDate":"2012-12-17T00:00:00+0000","convictionDate":"2013-03-02T00:00:00+0000","defendantType":{"description":"Owner","id":"def_t_owner"},"name":"Boris Johnson","categoryText":"Conviction Child Category 3","court":"FPN","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":29,"offenceDate":"2012-03-10T00:00:00+0000","convictionDate":"2012-06-15T00:00:00+0100","defendantType":{"description":"Operator","id":"def_t_op"},"name":"John Smith Haulage Ltd.","categoryText":"Conviction Child Category 4","court":"Leeds Magistrate court","penalty":"6 monthly investigation","msi":"Y","isDeclared":"N","isDealtWith":"N"}]},"annual-test-history":{"data":[]},"penalties":{"data":[]},"other-issues":{"data":[]},"compliance-complaints":{"data":[]},"financial-information":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:53:13',NULL,1),
(6,'submission_type_o_env',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"intelligence-unit-check":{"data":[]},"interim":{"data":[]},"advertisement":{"data":[]},"auth-requested-applied-for":{"data":[]},"transport-managers":{"data":[]},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"local-licence-history":{"data":[]},"conviction-fpn-offence-history":{"data":[{"id":25,"offenceDate":"2012-03-10T00:00:00+0000","convictionDate":"2012-06-15T00:00:00+0100","defendantType":{"description":"Operator","id":"def_t_op"},"name":"John Smith Haulage Ltd.","categoryText":"Test Category text 1","court":"FPN","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":26,"offenceDate":"2012-04-10T00:00:00+0100","convictionDate":"2012-05-15T00:00:00+0100","defendantType":{"description":"Owner","id":"def_t_owner"},"name":"John Smith","categoryText":"Conviction Child Category 1","court":"Leeds Magistrate court","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":27,"offenceDate":"2012-12-17T00:00:00+0000","convictionDate":"2013-03-02T00:00:00+0000","defendantType":{"description":"Owner","id":"def_t_owner"},"name":"Boris Johnson","categoryText":"Conviction Child Category 3","court":"FPN","penalty":"3 points on licence","msi":"N","isDeclared":"N","isDealtWith":"N"},{"id":29,"offenceDate":"2012-03-10T00:00:00+0000","convictionDate":"2012-06-15T00:00:00+0100","defendantType":{"description":"Operator","id":"def_t_op"},"name":"John Smith Haulage Ltd.","categoryText":"Conviction Child Category 4","court":"Leeds Magistrate court","penalty":"6 monthly investigation","msi":"Y","isDeclared":"N","isDealtWith":"N"}]},"other-issues":{"data":[]},"te-reports":{"data":[]},"site-plans":{"data":[]},"planning-permission":{"data":[]},"applicants-comments":{"data":[]},"visibility-access-egress-size":{"data":[]},"environmental-complaints":{"data":[]},"financial-information":{"data":[]},"maps":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:56:30',NULL,1),
(7,'submission_type_o_irfo',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"transport-managers":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"other-issues":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 14:58:18',NULL,1),
(8,'submission_type_o_bus_reg',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"auth-requested-applied-for":{"data":[]},"transport-managers":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"bus-reg-app-details":{"data":[]},"transport-authority-comments":{"data":[]},"total-bus-registrations":{"data":[]},"local-licence-history":{"data":[]},"registration-details":{"data":[]},"maintenance-tachographs-hours":{"data":[]},"other-issues":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 15:00:54',NULL,1),
(9,'submission_type_o_tm',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"intelligence-unit-check":{"data":[]},"transport-managers":{"data":[]},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"other-issues":{"data":[]},"oppositions":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":["id":77,{"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 15:02:08',NULL,1),
(10,'submission_type_o_schedule_41',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"operating-centres":{"data":[]},"conditions-and-undertakings":{"data":[]},"linked-licences-app-numbers":{"data":[]},"lead-tc-area":{"data":[]},"auth-requested-applied-for":{"data":[]},"previous-history":{"data":[]},"other-issues":{"data":[]},"site-plans":{"data":[]},"applicants-comments":{"data":[]},"environmental-complaints":{"data":[]},"waive-fee-late-fee":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 15:03:19',NULL,1),
(11,'submission_type_o_impounding',NULL,NULL,24,'{"most-serious-infringement":{"data":[]},"previous-history":{"data":[]},"other-issues":{"data":[]},"annex":{"data":[]},"introduction":{"data":[]},"case-summary":{"data":{"id":24,"organisationName":"John Smith Haulage Ltd.","isMlh":"N","organisationType":"Registered Company","businessType":null,"caseType":"case_t_lic","ecmsNo":"E123456","licNo":"OB1234567","licenceStartDate":"2010-01-12T00:00:00+0000","licenceType":"Standard National","goodsOrPsv":"Goods Vehicle","serviceStandardDate":null,"licenceStatus":"New","totAuthorisedVehicles":12,"totAuthorisedTrailers":4,"vehiclesInPossession":4,"trailersInPossession":4}},"case-outline":{"data":{"outline":"Case for convictions against company directors"}},"persons":{"data":[{"id":77,"title":"Mr","familyName":"Jones","forename":"Tom","birthDate":"1972-02-15T00:00:00+0000"},{"id":78,"title":"Mr","familyName":"Winnard","forename":"Keith","birthDate":"1975-03-15T00:00:00+0000"}]}}',NULL,'2014-10-16 15:03:19',NULL,1);

INSERT INTO submission_section_comment (submission_section,submission_id,comment,created_on) VALUES
    ('most-serious-infringement',1,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('lead-tc-area',1,'Placeholder for lead-tc-area','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',1,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('case-outline',1,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',2,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',2,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('case-outline',2,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',3,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',3,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('case-outline',3,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',4,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('case-outline',4,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',5,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('lead-tc-area',5,'Placeholder for lead-tc-area','2014-10-16 15:03:19'),
    ('prohibition-history',5,'Placeholder for prohibition-history','2014-10-16 15:03:19'),
    ('conviction-fpn-offence-history',5,'Placeholder for conviction-fpn-offence-history','2014-10-16 15:03:19'),
    ('annual-test-history',5,'Placeholder for annual-test-history','2014-10-16 15:03:19'),
    ('penalties',5,'Placeholder for penalties','2014-10-16 15:03:19'),
    ('case-outline',5,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',6,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',6,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('conviction-fpn-offence-history',6,'Placeholder for conviction-fpn-offence-history','2014-10-16 15:03:19'),
    ('case-outline',6,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',7,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('case-outline',7,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',8,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',8,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('case-outline',8,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',9,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('case-outline',9,'Case for convictions against company directors','2014-10-16 15:03:19'),

    ('most-serious-infringement',10,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('lead-tc-area',10,'Placeholder for lead-tc-area','2014-10-16 15:03:19'),
    ('auth-requested-applied-for',10,'Placeholder for auth-requested-applied-for','2014-10-16 15:03:19'),
    ('case-outline',10,'Case for convictions against company directors','2014-10-16 15:03:19'),
    
    ('most-serious-infringement',11,'Placeholder for most-serious-infringment','2014-10-16 15:03:19'),
    ('case-outline',11,'Case for convictions against company directors','2014-10-16 15:03:19');

INSERT INTO `submission_action` (`submission_id`, `recipient_user_id`, `sender_user_id`, `last_modified_by`, 
    `created_by`, `is_decision`, `urgent`, `submission_action_status`, `comment`, 
    `created_on`, `last_modified_on`)
VALUES
    (12, 1, 1, 1, 1, 0, 1, 'sub_st_rec_pi', 'Comment recommendaion testing lorem', NOW(), NOW()),
    (12, 1, 1, 1, 1, 1, 1, 'sub_st_dec_agree', 'Comment decision testing lorem', NOW(), NOW());

-- test business rules
INSERT INTO `sla` (`id`, `category`, `field`, `compare_to`, `days`, `weekend`, `public_holiday`, `effective_from`, `effective_to`)
VALUES
    (1, 'pi', 'callUpLetterDate', 'hearingDate', -35, 0, 0, '1900-01-01', NULL),
    (2, 'pi', 'briefToTcDate', 'hearingDate', -14, 1, 1, '1900-01-01', NULL),
    (3, 'pi', 'decisionLetterSentDate', 'hearingDate', 5, 1, 1, '1900-01-01', NULL),
    (4, 'pi', 'tcWrittenDecisionDate', 'hearingDate', 20, 1, 1, '1900-01-01', NULL),
    (5, 'pi', 'tcWrittenReasonDate', 'hearingDate', 5, 1, 1, '1900-01-01', NULL),
    (6, 'pi', 'writtenReasonLetterDate', 'tcWrittenReasonDate', 5, 1, 1, '1900-01-01', NULL),
    (7, 'pi', 'decSentAfterWrittenDecDate', 'hearingDate', 2, 1, 1, '1900-01-01', NULL),
    (8, 'pi_hearing', 'hearingDate', 'agreedDate', 60, 1, 1, '1900-01-01', NULL);


INSERT INTO `serious_infringement`
(`id`, `si_category_type_id`, `erru_response_user_id`, `member_state_code`, `created_by`,`last_modified_by`,
`si_category_id`, `case_id`, `check_date`, `erru_response_sent`,`erru_response_time`, `infringement_date`,
`notification_number`, `reason`, `deleted_date`,`created_on`, `last_modified_on`, `version`)
VALUES
  (1, '101', 1, 'PL', 1,1, 'MSI', 29, '2014-04-04', 0,null, '2014-04-05', 123456, null, null,'2014-05-04 17:50:06',
  '2014-05-04 17:50:06', 1),
  (2, '101', 1, 'PL', 1,1, 'MSI', 24, '2014-04-04', 0,null, '2014-04-05', 123456, null, null,'2014-05-04 17:50:06',
  '2014-05-04 17:50:06', 1);

INSERT INTO `si_penalty`
(`id`, `si_penalty_type_id`, `last_modified_by`, `created_by`, `serious_infringement_id`, `imposed`,
 `reason_not_imposed`, `start_date`, `end_date`, `deleted_date`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, '101', 1, 1, 1, 1, null, '2014-06-01', '2015-01-31', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (2, '306', 1, 1, 1, 0, 'Reason the penalty was not imposed', '2014-06-01', '2015-01-31', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (3, '306', 1, 1, 2, 0, 'Reason the penalty was not imposed', '2014-06-01', '2015-01-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (4, '101', 1, 1, 2, 1, '', '2014-05-01', '2015-01-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (5, '102', 1, 1, 2, 1, '', '2014-04-01', '2015-04-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (6, '301', 1, 1, 2, 1, '', '2014-03-01', '2015-03-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (7, '302', 1, 1, 2, 1, '', '2014-02-01', '2015-02-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (8, '303', 1, 1, 2, 1, '', '2014-01-01', '2015-01-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (9, '304', 1, 1, 2, 1, '', '2013-12-01', '2014-12-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (10, '305', 1, 1, 2, 1, '', '2013-11-01', '2014-11-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (11, '306', 1, 1, 2, 1, '', '2013-10-01', '2014-10-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (12, '307', 1, 1, 2, 1, '', '2013-09-01', '2014-09-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1);

-- si_category
INSERT INTO `si_category` (`id`,`description`,`deleted_date`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`)
VALUES ('MSI','MSI',NULL,1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1);

-- si_category_type
INSERT INTO `si_category_type` (`id`,`description`,`deleted_date`,`si_category_id`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) 
VALUES
  ('101','Exceeding the maximum six-day or fortnightly driving time limits by margins of 25 % or more',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('102','Exceeding, during a daily working period, the maximum daily driving time limit by a margin of 50 % or more without taking a break or without an uninterrupted rest period of at least 4,5 hours',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('201','Not having a tachograph although required by Community law',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('202','Using a fraudulent device able to modify the records of the recording equipment',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('203','Not having a speed limiter although required by Community law',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('204','Using a fraudulent device able to modify the speed limiter',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('205','Falsifying record sheets of the tachograph',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('206','Falsifying data downloaded from the tachograph and/or the driver card',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('301','Driving without a valid roadworthiness certificate if such a document is required under Community law',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('302','Driving with a very serious deficiency of, inter alia, the braking system, the steering linkages, the wheels/tyres, the suspension or chassis that would create such an immediate risk to road safety that it leads to a decision to immobilise the vehicle',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('401','Transporting dangerous goods that are prohibited for transport',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('402','Transporting dangerous goods in a prohibited or non-approved means of containment, thus endangering lives or the environment to such extent that it leads to a decision to immobilise the vehicle',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('403','Transporting dangerous goods without identifying them on the vehicle as dangerous goods, thus endangering lives or the environment to such extent that it leads to a decision to immobilise the vehicle',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('501','Carrying passengers without holding a valid driving licence',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('502','Carrying goods without holding a valid driving licence',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('503','Carrying passengers by an undertaking not holding a valid Community licence',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('504','Carrying goods by an undertaking not holding a valid Community licence',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('601','Driving with a driver card that has been falsified',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('602','Driving with a driver card of which the driver is not the holder',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('603','Driving with a driver card which has been obtained on the basis of false declarations and/or forged documents',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('701','Carrying goods exceeding the maximum permissible laden mass by 20 % or more for vehicles the permissible laden weight of which exceeds 12 tonnes',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1),
  ('702','Carrying goods exceeding the maximum permissible laden mass by 25 % or more for vehicles the permissible laden weight of which does not exceed 12 tonnes',NULL,'MSI',1,1,'2011-11-04 17:50:06','2011-11-04 17:50:06',1);

INSERT INTO `si_penalty_imposed_type` (`id`,`description`,`deleted_date`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) 
VALUES
  ('101','Warning',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('102','Other',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('201','Temporary ban on cabotage operations',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('202','Fine',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('203','Prohibition',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('204','Immobilisation',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1);

INSERT INTO `si_penalty_requested_type` (`id`,`description`,`deleted_date`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) 
VALUES
  ('101','Warning',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('301','Temporary withdrawl of some or all of the certified true copies of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('302','Permanent withdrawl of some or all of the certified true copies of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('303','Temporary withdrawl of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('304','Permanent withdrawl of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('305','Suspension of the issue of driver attestations',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('306','Withdrawl of driver attestations ',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('307','Issue of driver attestations subject to additional conditions in order to prevent misuse',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1);

INSERT INTO `si_penalty_type` (`id`,`description`,`deleted_date`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) 
VALUES
  ('101','Warning',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('102','Other',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('301','Temporary withdrawal of some or all of the certified true copies of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('302','Permanent withdrawal of some or all of the certified true copies of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('303','Temporary withdrawal of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('304','Permanent withdrawal of the Community licence',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('305','Suspension of the issue of driver attestations',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('306','Withdrawal of driver attestations ',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1),
  ('307','Issue of driver attestations subject to additional conditions in order to prevent misuse',NULL,1,1,'2013-03-22 17:30:05','2013-03-22 17:30:05',1);

INSERT INTO `si_penalty_erru_imposed`
(`id`, `si_penalty_imposed_type_id`, `serious_infringement_id`, `last_modified_by`, `created_by`, `final_decision_date`,
 `executed`, `start_date`, `end_date`, `deleted_date`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, '204', 1, 1, 1, '2014-10-02', 1, '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (2, '202', 1, 1, 1, '2014-10-02', 1, '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (3, '201', 1, 1, 1, '2014-10-02', 0, '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1);

INSERT INTO `si_penalty_erru_requested`
(`id`, `si_penalty_requested_type_id`, `serious_infringement_id`, `last_modified_by`, `created_by`, `duration`,
 `deleted_date`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, '305', 1, 1, 1, 12, null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (2, '302', 1, 1, 1, 36, null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (3, '303', 1, 1, 1, 60, null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1);

INSERT INTO `public_holiday`(`id`,`last_modified_by`,`created_by`,`public_holiday_date`,`is_england`,`is_wales`,`is_scotland`,`is_ni`,`created_on`,`last_modified_on`,`version`)
VALUES
  (1,1,1,'2014-01-01 00:00:00',1,1,1,1,now(),now(),1),
  (2,1,1,'2014-01-02 00:00:00',0,0,1,0,now(),now(),1),
  (3,1,1,'2014-03-17 00:00:00',0,0,0,1,now(),now(),1),
  (4,1,1,'2014-04-18 00:00:00',1,1,1,1,now(),now(),1),
  (5,1,1,'2014-04-21 00:00:00',1,1,0,1,now(),now(),1),
  (6,1,1,'2014-05-05 00:00:00',1,1,1,1,now(),now(),1),
  (7,1,1,'2014-05-26 00:00:00',1,1,1,1,now(),now(),1),
  (8,1,1,'2014-07-14 00:00:00',0,0,0,1,now(),now(),1),
  (9,1,1,'2014-08-04 00:00:00',0,0,1,0,now(),now(),1),
  (10,1,1,'2014-08-25 00:00:00',1,1,0,1,now(),now(),1),
  (11,1,1,'2014-12-01 00:00:00',0,0,1,0,now(),now(),1),
  (12,1,1,'2014-12-25 00:00:00',1,1,1,1,now(),now(),1),
  (13,1,1,'2014-12-26 00:00:00',1,1,1,1,now(),now(),1),
  (14,1,1,'2015-01-01 00:00:00',1,1,1,1,now(),now(),1),
  (15,1,1,'2015-01-02 00:00:00',0,0,1,0,now(),now(),1),
  (16,1,1,'2015-03-17 00:00:00',0,0,0,1,now(),now(),1),
  (17,1,1,'2015-04-03 00:00:00',1,1,1,1,now(),now(),1),
  (18,1,1,'2015-04-06 00:00:00',1,1,0,1,now(),now(),1),
  (19,1,1,'2015-05-04 00:00:00',1,1,1,1,now(),now(),1),
  (20,1,1,'2015-05-25 00:00:00',1,1,1,1,now(),now(),1),
  (21,1,1,'2015-07-13 00:00:00',0,0,0,1,now(),now(),1),
  (22,1,1,'2015-08-03 00:00:00',0,0,1,0,now(),now(),1),
  (23,1,1,'2015-08-31 00:00:00',1,1,0,1,now(),now(),1),
  (24,1,1,'2015-11-30 00:00:00',0,0,1,0,now(),now(),1),
  (25,1,1,'2015-12-25 00:00:00',1,1,1,1,now(),now(),1),
  (26,1,1,'2015-12-28 00:00:00',1,1,1,1,now(),now(),1),
  (27,1,1,'2016-01-01 00:00:00',1,1,1,1,now(),now(),1),
  (28,1,1,'2016-01-04 00:00:00',0,0,1,0,now(),now(),1),
  (29,1,1,'2016-03-17 00:00:00',0,0,0,1,now(),now(),1),
  (30,1,1,'2016-03-25 00:00:00',1,1,1,1,now(),now(),1),
  (31,1,1,'2016-03-28 00:00:00',1,1,0,1,now(),now(),1),
  (32,1,1,'2016-05-02 00:00:00',1,1,1,1,now(),now(),1),
  (33,1,1,'2016-05-30 00:00:00',1,1,1,1,now(),now(),1),
  (34,1,1,'2016-07-12 00:00:00',0,0,0,1,now(),now(),1),
  (35,1,1,'2016-08-01 00:00:00',0,0,1,0,now(),now(),1),
  (36,1,1,'2016-08-29 00:00:00',1,1,1,1,now(),now(),1),
  (37,1,1,'2016-12-26 00:00:00',1,1,1,1,now(),now(),1),
  (38,1,1,'2016-12-27 00:00:00',1,1,0,0,now(),now(),1);

INSERT INTO `publication_section` (`id`, `last_modified_by`, `created_by`, `description`, `created_on`,
   `last_modified_on`, `version`)
VALUES
  (13, 1, 1, 'PI Hearing', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1);

INSERT INTO `publication` (`id`,`pub_status`,`last_modified_by`,`created_by`,`traffic_area_id`,`pub_date`,`doc_name`,`publication_no`,`pub_type`,`created_on`,`last_modified_on`,`version`)
VALUES
  (1,'pub_s_printed',1,1,'B','2014-09-30',NULL,6128,'A&D','2014-09-30 00:00:00','2014-09-30 00:00:00',1),
  (2,'pub_s_printed',1,1,'B','2014-09-30',NULL,2155,'N&P','2014-09-30 00:00:00','2014-09-30 00:00:00',1),
  (3,'pub_s_new',1,1,'B','2014-10-30',NULL,6129,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (4,'pub_s_new',1,1,'B','2014-10-30',NULL,2156,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (5,'pub_s_new',1,1,'C','2014-10-30',NULL,6576,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (6,'pub_s_new',1,1,'C','2014-10-30',NULL,2648,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (7,'pub_s_new',1,1,'D','2014-10-30',NULL,2624,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (8,'pub_s_new',1,1,'D','2014-10-30',NULL,2181,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (9,'pub_s_new',1,1,'F','2014-10-30',NULL,5008,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (10,'pub_s_new',1,1,'F','2014-10-30',NULL,2160,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (11,'pub_s_new',1,1,'G','2014-10-30',NULL,8377,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (12,'pub_s_new',1,1,'G','2014-10-30',NULL,1986,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (13,'pub_s_new',1,1,'H','2014-10-30',NULL,5379,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (14,'pub_s_new',1,1,'H','2014-10-30',NULL,2484,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (15,'pub_s_new',1,1,'K','2014-10-30',NULL,3889,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (16,'pub_s_new',1,1,'K','2014-10-30',NULL,2283,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (17,'pub_s_new',1,1,'M','2014-10-30',NULL,1891,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (18,'pub_s_new',1,1,'M','2014-10-30',NULL,2014,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (19,'pub_s_new',1,1,'N','2014-10-30',NULL,30,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (20,'pub_s_new',1,1,'N','2014-10-30',NULL,2,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1);

INSERT INTO `publication_link` (`id`,`publication_section_id`,`tm_pi_hearing_id`,`pi_id`,`publication_id`,`created_by`,
  `last_modified_by`,`bus_reg_id`,`application_id`,`licence_id`,`traffic_area_id`,`text1`,`text2`,`text3`,
  `orig_pub_date`,`publication_no`,`pub_type`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (1,13,NULL,1,3,NULL,NULL,NULL,NULL,7,'B',
   'Public Inquiry (1) to be held at venue_1, Unit 9, Shapely Industrial Estate, Harehills, Leeds, LS9 2FA, on 16 March 2014 commencing at 14:30 \nOB1234567 SN \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY',
   'S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',
   NULL,'2014-10-30',6128,'A&D',NULL,'2014-11-25 15:47:03',NULL,1);

INSERT INTO `organisation_nature_of_business` (`id`, `organisation_id`, `ref_data_id`, `created_on`, `version`)
VALUES
	(1, 1, '01120', '2014-11-26 10:39:46', 1),
	(2, 1, '01150', '2014-11-26 10:39:47', 1),
	(3, 30, '01150', '2014-11-26 10:39:47', 1),
	(4, 41, '01150', '2014-11-26 10:39:47', 1),
	(5, 54, '01150', '2014-11-26 10:39:47', 1),
	(6, 63, '01150', '2014-11-26 10:39:47', 1),
	(7, 75, '01150', '2014-11-26 10:39:47', 1),
	(8, 100, '01150', '2014-11-26 10:39:47', 1),
	(9, 104, '01150', '2014-11-26 10:39:47', 1),
	(10, 105, '01150', '2014-11-26 10:39:47', 1);

SET foreign_key_checks = 1;
