SET foreign_key_checks = 0;

TRUNCATE TABLE `address`;
TRUNCATE TABLE `application`;
TRUNCATE TABLE `application_completion`;
TRUNCATE TABLE `application_operating_centre`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `complaint_case`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `driver`;
TRUNCATE TABLE `fee`;
TRUNCATE TABLE `licence`;
TRUNCATE TABLE `licence_vehicle`;
TRUNCATE TABLE `note`;
TRUNCATE TABLE `operating_centre`;
TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `organisation_person`;
TRUNCATE TABLE `person`;
TRUNCATE TABLE `disqualification`;
TRUNCATE TABLE `pi`;
TRUNCATE TABLE `pi_hearing`;
TRUNCATE TABLE `reason`;
TRUNCATE TABLE `pi_reason`;
TRUNCATE TABLE `pi_venue`;
TRUNCATE TABLE `presiding_tc`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `transport_manager_licence`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `trading_name`;
TRUNCATE TABLE `traffic_area`;
TRUNCATE TABLE `transport_manager`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;
TRUNCATE TABLE `vehicle`;
TRUNCATE TABLE `cases`;
TRUNCATE TABLE `impounding`;
TRUNCATE TABLE `impounding_legislation_type`;
TRUNCATE TABLE `team`;
TRUNCATE TABLE `task`;

INSERT INTO `address` (`id`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`,
    `postcode`, `town`, `country_code`, `created_on`, `last_modified_on`, `version`) VALUES
    (7,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (8,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (21,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (25,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','',NOW(),NOW(),1),
    (26,NULL,NULL,'5 High Street','Harehills','','','LS9 6GN','Leeds','',NOW(),NOW(),1),
    (27,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','',NOW(),NOW(),1),
    (29,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (30,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (31,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (37,NULL,NULL,'Unit 10','10 High Street','Alwoodley','','LS7 9SD','Leeds','GB',NOW(),NOW(),1),
    (39,NULL,NULL,'15 Avery Street','Harehills','','','LS9 5SS','Leeds','GB',NOW(),NOW(),1),
    (41,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle,','GB',NOW(),NOW(),1),
    (42,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle,','GB',NOW(),NOW(),1),
    (54,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle,','GB',NOW(),NOW(),1),
    (55,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle,','GB',NOW(),NOW(),1),
    (63,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (64,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (67,NULL,NULL,'Park Cottage','Coldcotes Avenue','','','LS9 6NE','Leeds','GB',NOW(),NOW(),1),
    (72,NULL,NULL,'38 George Street','Edgbaston','','','B15 1PL','Birmingham','GB',NOW(),NOW(),1),
    (75,NULL,NULL,'','','','','','','',NOW(),NOW(),1),
    (76,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (100,NULL,NULL,'Test Partnership LLP','10 Partnerships street','PartnershipDistrict','Partnership Land','PA7 5IP',
    'Leeds','GB',NOW(),NOW(),1);

INSERT INTO `application` (`id`, `licence_id`, `created_by`, `last_modified_by`, `status`, `tot_auth_vehicles`,
    `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_large_vehicles`, `tot_community_licences`,
    `tot_auth_trailers`, `bankrupt`, `liquidation`, `receivership`, `administration`, `disqualified`,
    `insolvency_details`, `insolvency_confirmation`, `safety_confirmation`, `received_date`, `target_completion_date`,
    `prev_conviction`, `convictions_confirmation`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,7,NULL,NULL,'apsts_new',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NOW(),NULL,1),
    (2,110,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,1),
    (6,114,NULL,NULL,'apsts_new',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,'2014-04-30 12:09:37','2014-04-30 12:09:39',1);

INSERT INTO `application_completion` (`id`, `created_by`, `last_modified_by`, `section_tol_status`,
    `section_tol_ol_status`, `section_tol_ot_status`, `section_tol_lt_status`, `section_yb_status`,
    `section_yb_bt_status`, `section_yb_bd_status`, `section_yb_add_status`, `section_yb_peo_status`,
    `section_tp_status`, `section_tp_lic_status`, `section_ocs_status`, `section_ocs_auth_status`,
    `section_ocs_fe_status`, `section_tms_status`, `section_veh_status`, `section_veh_v_status`,
    `section_veh_vpsv_status`, `section_veh_s_status`, `section_ph_status`, `section_ph_fh_status`,
    `section_ph_lh_status`, `section_ph_cp_status`, `section_rd_status`, `section_pay_status`, `section_pay_pay_status`,
    `section_pay_summary_status`, `last_section`, `created_on`, `last_modified_on`, `version`) VALUES
(1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);

INSERT INTO `application_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `application_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,34,23,1,0,NULL,NULL,1,NULL,NULL,1,1,16);

INSERT INTO `complaint` (`id`, `complainant_contact_details_id`, `driver_id`, `organisation_id`, `created_by`,
    `last_modified_by`, `complaint_date`, `status`, `value`, `description`, `complaint_type`, `vrm`, `created_on`,
    `last_modified_on`, `version`) VALUES
    (1,8,1,1,3,3,NOW(),'cs_ack','12345678','All tyres bald, broken wing mirror.','ct_cov',
    'VRM1',NOW(),NOW(),1),
    (2,8,1,1,3,3,NOW(),'cs_pin','12345678','Driving in excess of 70mph on dual carriageway',
    'ct_spe','VRM2',NOW(),'2014-08-06 08:50:27',1),
    (3,8,1,1,3,3,NOW(),'cs_rfs','12345678','Vehicle parked on bus stop.','ct_vpo','VRM1',
    NOW(),NOW(),1);

INSERT INTO `complaint_case` (`complaint_id`, `case_id`) VALUES
    (1,24),
    (2,24),
    (3,24);

INSERT INTO `condition_undertaking` (`id`, `case_id`, `licence_id`, `operating_centre_id`, `created_by`,
    `last_modified_by`, `added_via`, `attached_to`, `condition_type`, `condition_date`, `deleted_date`, `is_draft`,
    `is_fulfilled`, `notes`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,24,NULL,16,NULL,NULL,'Case','OC','cdt_con',NULL,NULL,0,0,'Some notes 1',NOW(),NULL,1),
    (2,24,NULL,16,NULL,NULL,'Case','OC','cdt_con',NULL,NULL,0,0,'Some notes 2',NOW(),NULL,1),
    (3,24,NULL,21,NULL,NULL,'Case','OC','cdt_con',NULL,NULL,0,0,'Some notes 3',NOW(),NULL,1),
    (4,24,7,NULL,NULL,NULL,'Case','Licence','cdt_und',NULL,NULL,0,1,'Some notes 4',NOW(),NULL,1),
    (5,24,7,NULL,NULL,NULL,'Case','Licence','cdt_und',NULL,NULL,0,1,'Some notes 5',NOW(),NULL,1),
    (6,24,7,NULL,NULL,NULL,'Case','Licence','cdt_con',NULL,NULL,0,1,'Some notes 6',NOW(),NULL,1),
    (7,24,NULL,48,NULL,NULL,'Case','OC','cdt_con',NULL,NULL,0,0,'Some notes 7',NOW(),NULL,1),
    (8,24,NULL,37,NULL,NULL,'Case','OC','cdt_und',NULL,NULL,0,1,'Some notes 8',NOW(),NULL,1),
    (9,24,7,NULL,NULL,NULL,'Case','Licence','cdt_con',NULL,NULL,0,0,'Some notes 9',NOW(),NULL,1),
    (10,24,7,NULL,NULL,NULL,'Case','Licence','cdt_con',NULL,NULL,0,0,'Some notes 10',NOW(),NULL,1),
    (11,24,7,NULL,NULL,NULL,'Case','Licence','cdt_con',NULL,NULL,0,0,'Some notes 11',NOW(),NULL,1);

INSERT INTO `contact_details` (`id`, `person_id`, `organisation_id`, `licence_id`, `address_id`, `created_by`,
    `last_modified_by`, `description`, `fao`, `contact_type`, `email_address`, `created_on`, `last_modified_on`,
    `version`, `deleted_date`) VALUES
    (7,9,7,NULL,7,0,2,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL),
    (8,10,7,NULL,8,3,2,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (21,NULL,1,NULL,21,2,0,NULL,NULL,'ct_oc',NULL,NOW(),NOW(),1,NULL),
    (25,NULL,1,NULL,25,4,4,NULL,NULL,'ct_def',NULL,NOW(),NOW(),1,NULL),
    (26,NULL,1,NULL,26,3,0,NULL,NULL,'ct_def',NULL,NOW(),NOW(),1,NULL),
    (27,NULL,1,NULL,27,4,2,NULL,NULL,'ct_def',NULL,NOW(),NOW(),1,NULL),
    (29,NULL,7,NULL,29,1,3,NULL,NULL,'ct_def',NULL,NOW(),NOW(),1,NULL),
    (30,NULL,30,NULL,30,3,2,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL),
    (31,NULL,30,NULL,31,1,0,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (37,NULL,30,NULL,37,2,2,NULL,NULL,'ct_oc',NULL,NOW(),NOW(),1,NULL),
    (39,NULL,30,NULL,39,2,4,NULL,NULL,'ct_oc',NULL,NOW(),NOW(),1,NULL),
    (41,NULL,41,NULL,41,1,2,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL),
    (42,NULL,41,NULL,42,4,1,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (54,NULL,54,NULL,54,2,4,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL),
    (55,NULL,54,NULL,55,3,3,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (63,NULL,63,NULL,63,4,3,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL),
    (64,NULL,63,NULL,64,1,0,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (67,NULL,63,NULL,67,4,4,NULL,NULL,'ct_oc',NULL,NOW(),NOW(),1,NULL),
    (72,NULL,63,NULL,72,4,2,NULL,NULL,'ct_oc',NULL,NOW(),NOW(),1,NULL),
    (75,NULL,75,NULL,75,3,4,NULL,NULL,NULL,NULL,NOW(),NOW(),1,NULL),
    (76,46,75,NULL,76,1,4,NULL,NULL,'ct_corr',NULL,NOW(),NOW(),1,NULL),
    (100,44,100,NULL,100,1,4,NULL,NULL,'ct_reg',NULL,NOW(),NOW(),1,NULL);

INSERT INTO `conviction` (`id`, `case_id`, `created_by`, `last_modified_by`, `category_text`, `birth_date`,
    `offence_date`, `conviction_date`, `court`, `penalty`, `costs`, `msi`, `is_declared`, `operator_name`,
    `defendant_type`, `notes`, `taken_into_consideration`, `person_id`, `created_on`, `last_modified_on`, `version`,
    `conviction_category_id`) VALUES
    (25,24,3,4,NULL,NULL,'2012-03-10','2012-06-15','FPN','3 points on licence','60',0,1,'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1,397),
    (26,NULL,0,4,NULL,NULL,'2012-04-10','2012-05-15','Leeds Magistrate court','3 points on licence','60',0,1,'','def_t_owner',NULL,NULL,4,NOW(),NOW(),1,399),
    (27,28,1,3,NULL,NULL,'2012-12-17','2013-03-02','FPN','3 points on licence','60',0,1,'','def_t_owner',NULL,NULL,4,NOW(),NOW(),1,399),
    (29,28,3,3,NULL,NULL,'2012-03-10','2012-06-15','Leeds Magistrate court','6 monthly investigation','2000',1,1,'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1,399);

INSERT INTO `driver` (`id`, `contact_details_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`,
    `version`) VALUES
(1,7,3,3,NOW(),NOW(),1);

INSERT INTO `fee` (`id`, `application_id`, `licence_id`, `created_by`, `last_modified_by`, `description`,
    `invoiced_date`, `amount`, `received_amount`, `created_on`, `last_modified_on`, `version`) VALUES
    (7,NULL,7,NULL,NULL,'Application fee','2013-11-25 00:00:00',250.00,0.00,NULL,NULL,1),
    (30,NULL,30,NULL,NULL,'Application fee','2013-11-22 00:00:00',251.00,0.00,NULL,NULL,1),
    (41,NULL,41,NULL,NULL,'Grant fee','2013-11-21 00:00:00',150.00,0.00,NULL,NULL,1),
    (54,NULL,54,NULL,NULL,'Application fee','2013-11-12 00:00:00',250.00,0.00,NULL,NULL,1),
    (63,NULL,63,NULL,NULL,'Application fee','2013-11-10 00:00:00',250.00,0.00,NULL,NULL,1),
    (75,NULL,75,NULL,NULL,'Application fee','2013-11-10 00:00:00',250.00,0.00,NULL,NULL,1),
    (76,1,7,NULL,NULL,'Application fee 1','2013-11-25 00:00:00',250.50,0.50,NULL,NULL,2),
    (77,1,7,NULL,NULL,'Application fee 2','2013-11-22 00:00:00',251.75,0.00,NULL,NULL,2),
    (78,1,7,NULL,NULL,'Grant fee','2013-11-21 00:00:00',150.00,0.00,NULL,NULL,3),
    (79,1,7,NULL,NULL,'Application fee 3','2013-11-12 00:00:00',250.00,0.00,NULL,NULL,2),
    (80,1,7,NULL,NULL,'Application fee 4','2013-11-10 00:00:00',250.00,0.00,NULL,NULL,1),
    (81,1,7,NULL,NULL,'Application fee 5','2013-11-10 00:00:00',1250.00,0.00,NULL,NULL,2),
    (82,1,30,NULL,NULL,'Bus route 1','2013-10-23 00:00:00',500.00,0.00,NULL,NULL,2);

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `created_by`, `last_modified_by`, `goods_or_psv`, `lic_no`, `status`,
    `ni_flag`, `licence_type`, `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `safety_ins_vehicles`, `safety_ins_trailers`, `safety_ins_varies`,
    `tachograph_ins`, `tachograph_ins_name`, `created_on`, `last_modified_on`, `version`) VALUES
    (7,1,1,1,4,'lcat_gv','OB1234567','lsts_new',1,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',4,12,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (30,30,1,0,1,'lcat_gv','OB1234568','lsts_new',1,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (41,41,1,2,2,'lcat_gv','OB1234577','lsts_new',0,'ltyp_si','2007-01-12','2007-01-12','2007-01-12','',1,
    21,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (54,54,1,2,4,'lcat_gv','OB1234578','lsts_new',0,'ltyp_r','2007-01-12','2007-01-12','2007-01-12','',0,4,NULL,NULL,
    NULL,NULL, NULL,NOW(),NOW(),1),
    (63,63,1,4,0,'lcat_psv','PD1234589','lsts_new',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',1,7,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (75,75,1,4,4,'lcat_psv','PD2737289','lsts_new',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1),
    (100,100,1,4,0,'lcat_psv','PD1001001','lsts_new',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,
    NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),2),
    (110,75,1,4,4,'lcat_psv','PD2737289','lsts_new',0,'ltyp_r','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,
    NULL,NULL,NULL,NOW(),NOW(),1),
    (114,104,1,NULL,NULL,'lcat_psv','OB1234567','lsts_new',1,'ltyp_sn',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1),
    (115,105,1,NULL,NULL,'lcat_psv','TS1234568','lsts_new',0,'ltyp_sr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NOW(),NULL,1);

INSERT INTO `licence_vehicle` (`id`, `licence_id`, `vehicle_id`, `created_by`, `last_modified_by`,
    `application_received_date`, `removal`, `removal_reason`, `specified_date`, `created_on`, `last_modified_on`,
    `version`) VALUES
    (1,7,1,NULL,4,'2014-02-20 00:00:00',1,'removal reason 1','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (2,7,2,NULL,4,'2014-02-20 00:00:00',1,'removal reason 2','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (3,7,3,NULL,4,'2014-02-20 00:00:00',1,'removal reason 3','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (4,7,4,NULL,4,'2014-02-20 00:00:00',1,'removal reason 4','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1);

INSERT INTO `note` (`id`, `case_id`, `licence_id`, `application_id`, `created_by`, `last_modified_by`, `priority`,
    `comment`, `note_type`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,28,7,1,2,NULL,0,'This is the first comment',30,'2011-10-03 00:00:00',NULL,1),
    (2,28,7,NULL,4,NULL,1,'This is the second comment',30,'2011-10-03 00:00:00',NULL,1),
    (3,28,7,1,2,NULL,0,'This is the third comment',30,'2011-10-03 00:00:00',NULL,1),
    (4,28,7,NULL,2,NULL,1,'This is the fourth comment',30,'2011-10-03 00:00:00',NULL,1),
    (5,28,7,1,3,NULL,0,'This is the fifth comment',30,'2011-10-03 00:00:00',NULL,1),
    (6,28,7,NULL,5,NULL,0,'This is the sixth note',30,'2011-10-03 00:00:00',NULL,1),
    (7,28,7,NULL,3,NULL,0,'This is a case note',30,'2011-10-03 00:00:00',NULL,1),
    (8,28,7,NULL,3,NULL,0,'This is the sixth note',30,'2011-10-14 00:00:00',NULL,1),
    (9,28,7,NULL,3,NULL,0,'This is the sixth note',30,'2012-10-10 00:00:00',NULL,1),
    (10,28,7,NULL,3,NULL,0,'This is the sixth note',30,'2012-10-10 00:00:00',NULL,1),
    (11,28,7,NULL,3,NULL,0,'This is the sixth note',30,'2013-11-05 00:00:00',NULL,1);

INSERT INTO `operating_centre` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `address_id`) VALUES
    (16,3,2,NOW(),NOW(),1,8),
    (21,1,3,NOW(),NOW(),1,21),
    (37,2,1,NOW(),NOW(),1,37),
    (39,1,3,NOW(),NOW(),1,39),
    (48,1,3,NOW(),NOW(),1,29),
    (67,0,1,NOW(),NOW(),1,67),
    (72,1,4,NOW(),NOW(),1,72);

INSERT INTO `organisation` (`id`, `created_by`, `last_modified_by`, `company_or_llp_no`, `name`, `is_mlh`, `type`,
    `sic_code`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,3,'1234567','John Smith Haulage Ltd.',0,'org_t_rc','91020',NOW(),NOW(),1),
    (30,1,4,'98765432','John Smith Haulage Ltd.',0,'org_t_rc','91020',NOW(),NOW(),1),
    (41,0,4,'241341234','Teddie Stobbart Group Ltd',0,'org_t_rc','91020',NOW(),NOW(),1),
    (54,3,4,'675675334','Teddie Stobbart Group Ltd',0,'org_t_rc','91020',NOW(),NOW(),1),
    (63,1,2,'353456456','Leeds bus service ltd.',0,'org_t_rc','91020',NOW(),NOW(),1),
    (75,1,0,'12345A1123','Leeds city council',0,'org_t_pa','91020',NOW(),NOW(),1),
    (100,1,3,'100100','Test partnership',0,'org_t_p','91020','2014-01-28 16:25:35','2014-01-28 16:25:35',2),
    (104,NULL,NULL,'1234567','Company Name',0,'org_t_rc','sic_code.01110',NULL,NULL,1),
    (105,1,3,NULL,'SR Orgaisation',0,'org_t_rc','91020',NOW(),NOW(),1);

INSERT INTO `organisation_person` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `person_id`, `organisation_id`) VALUES
    (1,NULL,NULL,NULL,NULL,1,78,7),
    (2,NULL,NULL,NULL,NULL,1,77,7),
    (3,NULL,NULL,NULL,NULL,1,77,1),
    (4,NULL,NULL,NULL,NULL,1,78,1),
    (5,NULL,NULL,NULL,NULL,1,78,100),
    (6,NULL,NULL,NULL,NULL,1,77,100);

INSERT INTO `person` (`id`, `created_by`, `last_modified_by`, `title`, `birth_date`, `forename`, `family_name`,
    `other_name`, `created_on`, `last_modified_on`, `version`, `deleted_date`) VALUES
    (4,NULL,NULL,NULL,'1960-02-01 00:00:00','Jack','Da Ripper',NULL,NULL,NULL,1,NULL),
    (9,NULL,NULL,NULL,'1960-02-15 00:00:00','John','Smith',NULL,NULL,NULL,1,NULL),
    (10,NULL,NULL,NULL,'1965-07-12 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (11,NULL,NULL,NULL,'1970-04-14 00:00:00','Joe','Lambert',NULL,NULL,NULL,1,NULL),
    (12,NULL,NULL,NULL,'1975-04-15 00:00:00','Tom','Cooper',NULL,NULL,NULL,1,NULL),
    (13,NULL,NULL,NULL,'1973-03-03 00:00:00','Mark','Anthony',NULL,NULL,NULL,1,NULL),
    (14,NULL,NULL,NULL,'1975-02-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (15,NULL,NULL,NULL,'1973-12-09 00:00:00','Tom','Anthony',NULL,NULL,NULL,1,NULL),
    (32,NULL,NULL,NULL,'1960-04-15 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (33,NULL,NULL,NULL,'1965-04-12 00:00:00','Mark','Jones',NULL,NULL,NULL,1,NULL),
    (34,NULL,NULL,NULL,'1970-06-14 00:00:00','Tim','Lambert',NULL,NULL,NULL,1,NULL),
    (35,NULL,NULL,NULL,'1975-04-18 00:00:00','Joe','Cooper',NULL,NULL,NULL,1,NULL),
    (43,NULL,NULL,NULL,'1960-02-15 00:00:00','Ted','Smith',NULL,NULL,NULL,1,NULL),
    (44,NULL,NULL,NULL,'1970-04-14 00:00:00','Peter','Lambert',NULL,NULL,NULL,1,NULL),
    (45,NULL,NULL,NULL,'1975-04-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (46,NULL,NULL,NULL,'1973-03-03 00:00:00','David','Anthony',NULL,NULL,NULL,1,NULL),
    (47,NULL,NULL,NULL,'1975-02-15 00:00:00','Lewis','Howarth',NULL,NULL,NULL,1,NULL),
    (59,NULL,NULL,NULL,'1973-03-03 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (60,NULL,NULL,NULL,'1975-02-15 00:00:00','Lewis','Hamilton',NULL,NULL,NULL,1,NULL),
    (65,NULL,NULL,NULL,'1972-02-15 00:00:00','Jonathan','Smith',NULL,NULL,NULL,1,NULL),
    (66,NULL,NULL,NULL,'1975-03-15 00:00:00','Tim','Cooper',NULL,NULL,NULL,1,NULL),
    (77,NULL,NULL,NULL,'1972-02-15 00:00:00','Tom','Jones',NULL,NULL,NULL,1,NULL),
    (78,NULL,NULL,NULL,'1975-03-15 00:00:00','Keith','Winnard',NULL,NULL,NULL,1,NULL);

INSERT INTO `disqualification` (`id`, `created_by`, `last_modified_by`, `is_disqualified`, `period`, `notes`,
    `created_on`, `last_modified_on`, `version`, `person_id`) VALUES
    (10,NULL,NULL,'Y','2 months','TBC',NOW(),NULL,1,10),
    (13,NULL,NULL,'Y','2 months','TBC',NOW(),NULL,1,13),
    (15,NULL,NULL,'Y','6 months','TBC',NOW(),NULL,1,15),
    (32,NULL,NULL,'Y','2 months','TBC',NOW(),NULL,1,32),
    (36,NULL,NULL,'Y','6 months','TBC',NOW(),NULL,1,15);

INSERT INTO `pi` (
    `id`, `created_by`, `last_modified_by`, `case_id`, `presiding_tc_id`, `type_app_new`, `type_app_var`,
    `type_disciplinary`, `type_env_new`, `type_env_var`, `type_oc_review`, `type_impounding`, `witnesses`,
    `agreed_date`, `decision_date`,`created_on`, `last_modified_on`,`version`, `deleted_date`) VALUES
    (1,1,1,73,2,1,0,0,0,0,0,1,20,NOW(),NOW(), NULL,NULL,1,NULL);

INSERT INTO `pi_hearing` (`id`, `pi_id`, `created_by`, `last_modified_by`, `presiding_tc_id`, `is_adjourned`,
    `hearing_date`, `venue`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,NULL,NULL,1,1,NOW(),'Some Venue',NULL,NULL,1),
    (2,1,NULL,NULL,2,0,NOW(),'Some Alt. Venue',NULL,NULL,1);

INSERT INTO `reason` (`id`,`goods_or_psv`,`is_decision`,`section_code`,`description`,`is_read_only`,`is_ni`,`is_propose_to_revoke`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'GV',0,'Section 12','Objection/Representation: New application',1,0,0,NULL,NULL,NULL,NULL,1),
    (2,'GV',0,'Section 13','New application',1,0,0,NULL,NULL,NULL,NULL,1),
    (3,'GV',0,'Section 13 (3)','Good Repute/Financial Standing/Prof Comp',1,0,0,NULL,NULL,NULL,NULL,1),
    (4,'GV',0,'Section 13 (4)','Must not be unfit',1,0,0,NULL,NULL,NULL,NULL,1),
    (5,'GV',0,'Section 13 (5)(a)','Drivers Hours and Tachographs',1,0,0,NULL,NULL,NULL,NULL,1),
    (6,'GV',0,'Section 13 (5)(b)','Loading of Vehicles',1,0,0,NULL,NULL,NULL,NULL,1),
    (7,'GV',0,'Section 13 (5)(c)','Maintenance',1,0,0,NULL,NULL,NULL,NULL,1),
    (8,'GV',0,'Section 13 (5)(d)','Availability/Suitability of Operating Centre',1,0,0,NULL,NULL,NULL,NULL,1),
    (9,'GV',0,'Section 13 (5)(e)','Capacity of Operating Centre',1,0,0,NULL,NULL,NULL,NULL,1),
    (10,'GV',0,'Section 13 (6)','Finance for Maintenance',1,0,0,NULL,NULL,NULL,NULL,1),
    (11,'GV',0,'Section 17','Variation application',1,0,0,NULL,NULL,NULL,NULL,1),
    (12,'GV',0,'Section 19','Objection/Representation: Var. application',1,0,0,NULL,NULL,NULL,NULL,1),
    (13,'GV',0,'Section 21','Conditions: Road Safety',1,0,0,NULL,NULL,NULL,NULL,1),
    (14,'GV',0,'Section 22','Conditions: Matters to be notified',1,0,0,NULL,NULL,NULL,NULL,1),
    (15,'GV',0,'Section 23','Conditions: Operating Centres',1,0,0,NULL,NULL,NULL,NULL,1),
    (16,'GV',0,'Section 24','Interim Licence',1,0,0,NULL,NULL,NULL,NULL,1),
    (17,'GV',0,'Section 25','Interim Variation',1,0,0,NULL,NULL,NULL,NULL,1),
    (18,'GV',0,'Section 26 (1)(a)','Unauthorised use of place as an o/c',1,0,0,NULL,NULL,NULL,NULL,1),
    (19,'GV',0,'Section 26 (1)(b)','Contravention of Licence Condition',1,0,0,NULL,NULL,NULL,NULL,1),
    (20,'GV',0,'Section 26 (1)(c)(i)','Convictions of Licence Holder (Sch 2, Para 5)',1,0,0,NULL,NULL,NULL,NULL,1),
    (21,'GV',0,'Section 26 (1)(c)(ii)','Convictions of Servant/Agent (Sch 2, Para 5)',1,0,0,NULL,NULL,NULL,NULL,1),
    (22,'GV',0,'Section 26 (1)(c)(iii)','Prohibitions',1,0,0,NULL,NULL,NULL,NULL,1),
    (23,'GV',0,'Section 26 (1)(d)','Convictions (Schedule 2, Paragraph 5(j))',1,0,0,NULL,NULL,NULL,NULL,1),
    (24,'GV',0,'Section 26 (1)(e)','False Statement/Fail to fulfil Statement of Exp.',1,0,0,NULL,NULL,NULL,NULL,1),
    (25,'GV',0,'Section 26 (1)(f)','Fail to fulfil Undertaking',1,0,0,NULL,NULL,NULL,NULL,1),
    (26,'GV',0,'Section 26 (1)(g)','Bankruptcy/Liquidation',1,0,0,NULL,NULL,NULL,NULL,1),
    (27,'GV',0,'Section 26 (1)(h)','Material Change',1,0,0,NULL,NULL,NULL,NULL,1),
    (28,'GV',0,'Section 26 (1)(i)','Suspend/Curtail/Revoke under Section 28(4)',1,0,0,NULL,NULL,NULL,NULL,1),
    (29,'GV',0,'Section 27 (1)(a)','Good Repute',1,0,0,NULL,NULL,NULL,NULL,1),
    (30,'GV',0,'Section 27 (1)(b)','Financial Standing',1,0,1,NULL,NULL,NULL,NULL,1),
    (31,'GV',0,'Section 27 (1)(c)','Professional Competence',1,0,1,NULL,NULL,NULL,NULL,1),
    (32,'GV',0,'Section 28','Disqualification',1,0,0,NULL,NULL,NULL,NULL,1),
    (33,'GV',0,'Section 30','Review of Operating Centre',1,0,0,NULL,NULL,NULL,NULL,1),
    (34,'GV',0,'Section 49','Certificate of Qualification',1,0,0,NULL,NULL,NULL,NULL,1),
    (35,'GV',0,'Schedule 3','Transport Manager\'s Good Repute',1,0,0,NULL,NULL,NULL,NULL,1),
    (36,'GV',0,'Art. 8(2)&(3)','Community Authorisations',1,0,0,NULL,NULL,NULL,NULL,1),
    (37,'GV',1,'Section 13','Application Granted',1,0,0,NULL,NULL,NULL,NULL,1),
    (38,'GV',1,'Section 13','Undertakings',1,0,0,NULL,NULL,NULL,NULL,1),
    (39,'GV',1,'Section 13','Application Refused',1,0,0,NULL,NULL,NULL,NULL,1),
    (40,'GV',1,'Section 14','Application Refused',1,0,0,NULL,NULL,NULL,NULL,1),
    (41,'GV',1,'Section 15','Application Granted',1,0,0,NULL,NULL,NULL,NULL,1),
    (42,'GV',1,'Section 17','Application Granted',1,0,0,NULL,NULL,NULL,NULL,1),
    (43,'GV',1,'Section 17','Application Refused',1,0,0,NULL,NULL,NULL,NULL,1),
    (44,'GV',1,'Section 19','Application Refused',1,0,0,NULL,NULL,NULL,NULL,1),
    (45,'GV',1,'Section 21','Application Granted: Conditions: Road Safety',1,0,0,NULL,NULL,NULL,NULL,1),
    (46,'GV',1,'Section 22','Application Granted: Conditions: Notified Matters',1,0,0,NULL,NULL,NULL,NULL,1),
    (47,'GV',1,'Section 23','Application Granted: Conditions: O/Câ€™s',1,0,0,NULL,NULL,NULL,NULL,1),
    (48,'GV',1,'Section 26','No Action',1,0,0,NULL,NULL,NULL,NULL,1),
    (49,'GV',1,'Section 26','Formal Warning',1,0,0,NULL,NULL,NULL,NULL,1),
    (50,'GV',1,'Section 26','Final Warning',1,0,0,NULL,NULL,NULL,NULL,1),
    (51,'GV',1,'Section 26','Curtail',1,0,0,NULL,NULL,NULL,NULL,1),
    (52,'GV',1,'Section 26','Suspend',1,0,0,NULL,NULL,NULL,NULL,1),
    (53,'GV',1,'Section 26','Revoke',1,0,0,NULL,NULL,NULL,NULL,1),
    (54,'GV',1,'Section 26(6)','Direction to suspend vehicles',1,0,0,NULL,NULL,NULL,NULL,1),
    (55,'GV',1,'Section 27','Revoke (Repute, Finance, Prof. Comp.)',1,0,0,NULL,NULL,NULL,NULL,1),
    (56,'GV',1,'Section 28','Disqualification',1,0,0,NULL,NULL,NULL,NULL,1),
    (57,'GV',1,'Section 29','Direction (effective date)',1,0,0,NULL,NULL,NULL,NULL,1),
    (58,'GV',1,'Section 31','Direction (removal of o/c: effective date)',1,0,0,NULL,NULL,NULL,NULL,1),
    (59,'GV',1,'Section 32','Direction (conditions: operating centre)',1,0,0,NULL,NULL,NULL,NULL,1),
    (60,'GV',1,'Section 34','Undertakings (review of operating centre)',1,0,0,NULL,NULL,NULL,NULL,1),
    (61,'GV',1,'Section 49','Certificate of Qualification Granted',1,0,0,NULL,NULL,NULL,NULL,1),
    (62,'GV',1,'Section 49','Certificate of Qualification Refused',1,0,0,NULL,NULL,NULL,NULL,1),
    (63,'GV',1,'Schedule 3','Loss of Good Repute',1,0,0,NULL,NULL,NULL,NULL,1),
    (64,'GV',1,'Art. 8(2)','Withdraw Community Authorisation',1,0,0,NULL,NULL,NULL,NULL,1),
    (65,'GV',1,'Art. 8(3)','Suspend Community Authorisation',1,0,0,NULL,NULL,NULL,NULL,1),
    (66,'PSV',0,'Section 14','New application',0,0,0,NULL,NULL,NULL,NULL,1),
    (67,'PSV',0,'Section 14    (1)(a)','Good Repute',0,0,0,NULL,NULL,NULL,NULL,1),
    (68,'PSV',0,'Section 14    (1)(b)','Financial Standing',0,0,0,NULL,NULL,NULL,NULL,1),
    (69,'PSV',0,'Section 14    (1)(c)','Professional Competence',0,0,0,NULL,NULL,NULL,NULL,1),
    (70,'PSV',0,'Section 14    (3)(a)','Facilities/arrangements for maintenance',0,0,0,NULL,NULL,NULL,NULL,1),
    (71,'PSV',0,'Section 14    (3)(b)','Arrangements for driving/operation of vehicles',0,0,0,NULL,NULL,NULL,NULL,1),
    (72,'PSV',0,'Section 14A','Objection: New application',0,0,0,NULL,NULL,NULL,NULL,1),
    (73,'PSV',0,'Section 16','Application to Increase Authorisation',0,0,0,NULL,NULL,NULL,NULL,1),
    (74,'PSV',0,'Section 17 (1)','Good Repute/Financial Standing',0,0,0,NULL,NULL,NULL,NULL,1),
    (75,'PSV',0,'Section 17 (1)','Professional Competence (Standard Licences)',0,0,0,NULL,NULL,NULL,NULL,1),
    (76,'PSV',0,'Section 17 (3)(a)','False Statement/Fail to fulfil Statement of Exp.',0,0,0,NULL,NULL,NULL,NULL,1),
    (77,'PSV',0,'Section 17 (3)(aa)','Fail to fulfil an Undertaking',0,0,0,NULL,NULL,NULL,NULL,1),
    (78,'PSV',0,'Section 17 (3)(b)','Breach of Licence Condition',0,0,0,NULL,NULL,NULL,NULL,1),
    (79,'PSV',0,'Section 17 (3)(c)','Prohibitions',0,0,0,NULL,NULL,NULL,NULL,1),
    (80,'PSV',0,'Section 17 (3)(d)','Good Repute',0,0,0,NULL,NULL,NULL,NULL,1),
    (81,'PSV',0,'Section 17 (3)(d)','Financial Standing (Restricted Licence)',0,0,0,NULL,NULL,NULL,NULL,1),
    (82,'PSV',0,'Section 17 (3)(e)','Material Change',0,0,0,NULL,NULL,NULL,NULL,1),
    (83,'PSV',0,'Section 21','Certificate of Qualification',0,0,0,NULL,NULL,NULL,NULL,1),
    (84,'PSV',0,'Schedule 3','Transport Managerâ€™s Good Repute',1,0,0,NULL,NULL,NULL,NULL,1),
    (85,'PSV',0,'Section 26','Bus Registration: Local Services',0,0,0,NULL,NULL,NULL,NULL,1),
    (86,'PSV',0,'Section 28','Disqualification',1,0,0,NULL,NULL,NULL,NULL,1),
    (87,'PSV',0,'Section 111','Bus Registration: Fuel Duty',0,0,0,NULL,NULL,NULL,NULL,1),
    (88,'PSV',0,'Art. 8(2)&(3)','Community Authorisations',1,0,0,NULL,NULL,NULL,NULL,1),
    (89,'PSV',1,'Section 14','Application Granted',0,0,0,NULL,NULL,NULL,NULL,1),
    (90,'PSV',1,'Section 14','Application Refused',0,0,0,NULL,NULL,NULL,NULL,1),
    (91,'PSV',1,'Section 16','Application Granted',0,0,0,NULL,NULL,NULL,NULL,1),
    (92,'PSV',1,'Section 16','Application Refused',0,0,0,NULL,NULL,NULL,NULL,1),
    (93,'PSV',1,'Section 16','Conditions',0,0,0,NULL,NULL,NULL,NULL,1),
    (94,'PSV',1,'Section 17','No Action',0,0,0,NULL,NULL,NULL,NULL,1),
    (95,'PSV',1,'Section 17','Formal Warning',0,0,0,NULL,NULL,NULL,NULL,1),
    (96,'PSV',1,'Section 17','Final Warning',0,0,0,NULL,NULL,NULL,NULL,1),
    (97,'PSV',1,'Section 17','Conditions (vehicles)',0,0,0,NULL,NULL,NULL,NULL,1),
    (98,'PSV',1,'Section 17','Suspend',0,0,0,NULL,NULL,NULL,NULL,1),
    (99,'PSV',1,'Section 17','Revoke (Repute, Finance, Prof. Comp.)',0,0,0,NULL,NULL,NULL,NULL,1),
    (100,'PSV',1,'Schedule 3','Loss of Good Repute',1,0,0,NULL,NULL,NULL,NULL,1),
    (101,'PSV',1,'Section 26','Direction to cancel local service',0,0,0,NULL,NULL,NULL,NULL,1),
    (102,'PSV',1,'Section 26','Condition (local services)',0,0,0,NULL,NULL,NULL,NULL,1),
    (103,'PSV',1,'Section 28','Disqualification',1,0,0,NULL,NULL,NULL,NULL,1),
    (104,'PSV',1,'Section 111','Determination (Fuel Duty Rebate)',0,0,0,NULL,NULL,NULL,NULL,1),
    (105,'PSV',1,'Art. 8(2)','Withdraw Community Authorisation',1,0,0,NULL,NULL,NULL,NULL,1),
    (106,'PSV',1,'Art. 8(3)','Suspend Community Authorisation',1,0,0,NULL,NULL,NULL,NULL,1),
    (107,'GV',0,'Other Matters','Transport Manager to attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (108,'GV',0,'Other Matters','Produce Financial Information',1,0,0,NULL,NULL,NULL,NULL,1),
    (109,'GV',0,'Other Matters','Financial Assessor to assist TC at PI',1,0,0,NULL,NULL,NULL,NULL,1),
    (110,'GV',0,'Other Matters','Driver Conduct Action',1,0,0,NULL,NULL,NULL,NULL,1),
    (111,'PSV',0,'Other Matters','Transport Manager to attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (112,'PSV',0,'Other Matters','Produce Financial Information',1,0,0,NULL,NULL,NULL,NULL,1),
    (113,'PSV',0,'Other Matters','Financial Assessor to assist TC at PI',1,0,0,NULL,NULL,NULL,NULL,1),
    (114,'PSV',0,'Other Matters','Driver Conduct Action',1,0,0,NULL,NULL,NULL,NULL,1),
    (115,'GV',0,'Other Matters','Vehicle Examiner to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (116,'GV',0,'Other Matters','Traffic Examiner to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (117,'GV',0,'Other Matters','Police to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (118,'PSV',0,'Other Matters','Vehicle Examiner to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (119,'PSV',0,'Other Matters','Traffic Examiner to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (120,'PSV',0,'Other Matters','Bus Monitor to Attend',0,0,0,NULL,NULL,NULL,NULL,1),
    (121,'PSV',0,'Other Matters','Police to Attend',1,0,0,NULL,NULL,NULL,NULL,1),
    (122,'GV',0,'01 - Section 13','New application',0,0,0,NULL,NULL,NULL,NULL,1),
    (123,'GV',0,'02 - Section 17','Variation application',0,0,0,NULL,NULL,NULL,NULL,1),
    (124,'GV',0,'03 - Section 26 (1)(a)','Unauthorised use of a place as an operating centre',0,0,1,NULL,NULL,NULL,NULL,1),
    (125,'GV',0,'04 - Section 26 (1)(b)','Contravention of a licence condition (specify)',0,0,0,NULL,NULL,NULL,NULL,1),
    (126,'GV',0,'04.1 - Section 26 (1)(b)','(1) Fail to notify of change in maintenance arrangements',0,0,1,NULL,NULL,NULL,NULL,1),
    (127,'GV',0,'04.2 - Section 26 (1)(b)','(2) Fail to notify of change in ownership',0,0,1,NULL,NULL,NULL,NULL,1),
    (128,'GV',0,'04.3 - Section 26 (1)(b)','(3) Fail to notify of notifiable convictions â€“ Sch 2 (restricted)',0,0,1,NULL,NULL,NULL,NULL,1),
    (129,'GV',0,'04.4 - Section 26 (1)(b)','(4) Fail to notify of events which affect good repute â€“ Sch 3 (standard)',0,0,1,NULL,NULL,NULL,NULL,1),
    (130,'GV',0,'04.5 - Section 26 (1)(b)','(5) Fail to notify of events which affect financial standing',0,0,1,NULL,NULL,NULL,NULL,1),
    (131,'GV',0,'04.6 - Section 26 (1)(b)','(6) Fail to notify of events which affect professional competence',0,0,1,NULL,NULL,NULL,NULL,1),
    (132,'GV',0,'04.7 - Section 26 (1)(b)','(7) Breach of road safety condition',0,0,1,NULL,NULL,NULL,NULL,1),
    (133,'GV',0,'04.8 - Section 26 (1)(b)','(8) Breach of environmental condition',0,0,1,NULL,NULL,NULL,NULL,1),
    (134,'GV',0,'04.9 - Section 26 (1)(b)','(9) Other (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (135,'GV',0,'05 - Section 26 (1)(c)(i)','Schedule 2 Paragraph 5 convictions (operator)',0,0,1,NULL,NULL,NULL,NULL,1),
    (136,'GV',0,'06 - Section 26 (1)(c)(ii)','Schedule 2 Paragraph 5 convictions (servants/agents)',0,0,1,NULL,NULL,NULL,NULL,1),
    (137,'GV',0,'07 - Section 26 (1)(c)(iii)','Prohibitions',0,0,1,NULL,NULL,NULL,NULL,1),
    (138,'GV',0,'08 - Section 26(1)(ca)','Fixed Penalty or conditional offer issued',0,0,1,NULL,NULL,NULL,NULL,1),
    (139,'GV',0,'09 - Section 26(1)(d)','Convictions â€“ for Schedule 2 Paragraph 5 (j) offences',0,0,1,NULL,NULL,NULL,NULL,1),
    (140,'GV',0,'10 - Section 26(1)(e)','Failing to fulfil Statement of Expectation/False statement (specify)',0,0,0,NULL,NULL,NULL,NULL,1),
    (141,'GV',0,'10.01 - Section 26(1)(e)','(1) Failure to declare previous refusal or revocation',0,0,1,NULL,NULL,NULL,NULL,1),
    (142,'GV',0,'10.02 - Section 26(1)(e)','(2) Stating that (x) would be the TM responsible for vehicles on licence',0,0,1,NULL,NULL,NULL,NULL,1),
    (143,'GV',0,'10.03 - Section 26(1)(e)','(3) Stating that the TM would not be responsible for any other licence',0,0,1,NULL,NULL,NULL,NULL,1),
    (144,'GV',0,'10.04 - Section 26(1)(e)','(4) Stating that the vehicles would normally be kept at the o/c',0,0,1,NULL,NULL,NULL,NULL,1),
    (145,'GV',0,'10.05 - Section 26(1)(e)','(5) Stating that the nominated o/c is not used by other operators',0,0,1,NULL,NULL,NULL,NULL,1),
    (146,'GV',0,'10.06 - Section 26(1)(e)','(6) Stating that the vehicles would be given inspections at (x) intervals',0,0,1,NULL,NULL,NULL,NULL,1),
    (147,'GV',0,'10.07 - Section 26(1)(e)','(7) Stating that the maintenance would be carried out by own staff',0,0,1,NULL,NULL,NULL,NULL,1),
    (148,'GV',0,'10.08 - Section 26(1)(e)','(8) Stating that the maintenance would be carried out by (x) firm',0,0,1,NULL,NULL,NULL,NULL,1),
    (149,'GV',0,'10.09 - Section 26(1)(e)','(9) Failure to declare that (x) has been made bankrupt',0,0,1,NULL,NULL,NULL,NULL,1),
    (150,'GV',0,'10.10 - Section 26(1)(e)','(10) Failure to declare that (x) has been involved in a liquidated co',0,0,1,NULL,NULL,NULL,NULL,1),
    (151,'GV',0,'10.11 - Section 26(1)(e)','(11) Failure to declare that (x) has been disqualified as a director',0,0,1,NULL,NULL,NULL,NULL,1),
    (152,'GV',0,'10.12 - Section 26(1)(e)','(12) Failure to declare convictions on making the application',0,0,1,NULL,NULL,NULL,NULL,1),
    (153,'GV',0,'10.13 - Section 26(1)(e)','(13) Stating that the holder would abide by any conditions on the licence',0,0,1,NULL,NULL,NULL,NULL,1),
    (154,'GV',0,'11 - Section 26(1)(f)','Failing to fulfil a licence undertaking',0,0,0,NULL,NULL,NULL,NULL,1),
    (155,'GV',0,'11.1 - Section 26(1)(f)','(1) Rules on drivers hours and tachographs would be observed',0,0,1,NULL,NULL,NULL,NULL,1),
    (156,'GV',0,'11.2 - Section 26(1)(f)','(2) Vehicles and trailers not to be overloaded',0,0,1,NULL,NULL,NULL,NULL,1),
    (157,'GV',0,'11.3 - Section 26(1)(f)','(3) Vehicles operate within speed limits',0,0,1,NULL,NULL,NULL,NULL,1),
    (158,'GV',0,'11.4 - Section 26(1)(f)','(4) Vehicles and trailers would be kept fit and serviceable',0,0,1,NULL,NULL,NULL,NULL,1),
    (159,'GV',0,'11.5 - Section 26(1)(f)','(5) Driver reports any defects in writing',0,0,1,NULL,NULL,NULL,NULL,1),
    (160,'GV',0,'11.6 - Section 26(1)(f)','(6) Maintenance records would be kept for 15 months',0,0,1,NULL,NULL,NULL,NULL,1),
    (161,'GV',0,'11.7 - Section 26(1)(f)','(7) Exceeding operating centre authorisation',0,0,1,NULL,NULL,NULL,NULL,1),
    (162,'GV',0,'11.8 - Section 26(1)(f)','(8) Other (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (163,'GV',0,'12 - Section 26(1)(g)','Bankruptcy/liquidation (other than voluntary liquidation)',0,0,1,NULL,NULL,NULL,NULL,1),
    (164,'GV',0,'13 - Section 26(1)(h)','Material Change (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (165,'GV',0,'14 - Section 26 (1)(i)','Licence liable to revocation, suspension or curtailment following a direction under section 28 (4) (director/individual disqualified on another licence)',0,0,1,NULL,NULL,NULL,NULL,1),
    (166,'GV',0,'15 - Section 27(1)(a)','To consider whether the operator has an effective and stable establishment in Great Britain.',0,0,1,NULL,NULL,NULL,NULL,1),
    (167,'GV',0,'16 - Section 27(1)(a)','To consider the operator is still of good repute in accordance with paragraphs 1 to 5 of schedule 3.',0,0,1,NULL,NULL,NULL,NULL,1),
    (168,'GV',0,'17 - Section 27(1)(a)','To consider whether the operator continues to meet the requirement to be of appropriate financial standing.',0,0,1,NULL,NULL,NULL,NULL,1),
    (169,'GV',0,'18 - Section 27(1)(a)','No longer professionally competent.',0,0,1,NULL,NULL,NULL,NULL,1),
    (170,'GV',0,'19 - Section 27(1)(b)','No longer of good repute (section 13A (3)(a))',0,0,1,NULL,NULL,NULL,NULL,1),
    (171,'GV',0,'20 - Section 27(1)(b)','No longer professionally competent (section 13A (3)(b))',0,0,1,NULL,NULL,NULL,NULL,1),
    (172,'GV',0,'21 - Section 27(1)(b)','External TM exceeds the 4/50 rule (section 13A (c))',0,0,1,NULL,NULL,NULL,NULL,1),
    (173,'GV',0,'22 - Section 28','Disqualification to be considered - Operator licence',0,0,0,NULL,NULL,NULL,NULL,1),
    (174,'GV',0,'23 - Schedule 3(15)(1)','TM good repute or professional competence',0,0,0,NULL,NULL,NULL,NULL,1),
    (175,'GV',0,'24 - Section 28','Disqualification to be considered - Transport Manager',0,0,0,NULL,NULL,NULL,NULL,1),
    (176,'GV',0,'24.1 - Section 28','(1) Withdraw/suspend community licence',0,0,0,NULL,NULL,NULL,NULL,1),
    (177,'GV',0,'24.2 - Section 28','(2) Financial assessor required',0,0,0,NULL,NULL,NULL,NULL,1),
    (178,'GV',0,'24.3 - Section 28','(3) Other (please specify)',0,0,0,NULL,NULL,NULL,NULL,1),
    (179,'PSV',0,'01 - Section 14','New application',0,0,0,NULL,NULL,NULL,NULL,1),
    (180,'PSV',0,'02 - Section 16','Variation application to be considered',0,0,0,NULL,NULL,NULL,NULL,1),
    (181,'PSV',0,'03 - Section 17 (1)(a)(Revocation only)','To consider whether operator has an effective and stable establishment in Great Britain. (Standard licence)',0,0,1,NULL,NULL,NULL,NULL,1),
    (182,'PSV',0,'04 - Section 17 (1)(a)(Revocation only)','To consider the operators good repute in accordance with paragraphs 1 of schedule 3 (standard licence):',0,0,1,NULL,NULL,NULL,NULL,1),
    (183,'PSV',0,'04.1 - Section 17 (1)(a)(Revocation only)','(a) relevant convictions have been incurred',0,0,1,NULL,NULL,NULL,NULL,1),
    (184,'PSV',0,'04.2 - Section 17 (1)(a)(Revocation only)','(b) relevant FPN have been incurred',0,0,1,NULL,NULL,NULL,NULL,1),
    (185,'PSV',0,'04.3 - Section 17 (1)(a)(Revocation only)','(c) any other relevant information',0,0,1,NULL,NULL,NULL,NULL,1),
    (186,'PSV',0,'05 - Section 17 (1)(a)(Revocation only)','To consider whether the operator continues to meet the requirement to be of appropriate financial standing (standard licence)',0,0,1,NULL,NULL,NULL,NULL,1),
    (187,'PSV',0,'06 - Section 17 (1)(a)(Revocation only)','No longer professionally competent (Standard only)',0,0,1,NULL,NULL,NULL,NULL,1),
    (188,'PSV',0,'07 - Section 17 (1)(b)(Revocation only)','TM no longer of good repute (section 14ZA (3)(a)) (standard only)',0,0,1,NULL,NULL,NULL,NULL,1),
    (189,'PSV',0,'08 - Section 17 (1)(b)(Revocation only)','TM no longer professionally competent (section 14ZA (3)(b)) (standard only)',0,0,1,NULL,NULL,NULL,NULL,1),
    (190,'PSV',0,'09 - Section 17 (1)(b)(Revocation only)','External TM exceeds the 4/50 rule or prohibited (Section 14ZA (3)(c) (standard only)',0,0,1,NULL,NULL,NULL,NULL,1),
    (191,'PSV',0,'10 - Section 17(3)(a) Revoke/suspend/vary','Operator made a statement of fact on application/variation which was false or expectation has not been fulfilled (specify)',0,0,0,NULL,NULL,NULL,NULL,1),
    (192,'PSV',0,'10.1 - Section 17(3)(a) Revoke/suspend/vary','(a) Stating that the vehicles would be kept at a specified o/c',0,0,1,NULL,NULL,NULL,NULL,1),
    (193,'PSV',0,'10.2 - Section 17(3)(a) Revoke/suspend/vary','(b) Stating that the vehicles would be given inspections at (x) intervals',0,0,1,NULL,NULL,NULL,NULL,1),
    (194,'PSV',0,'10.3 - Section 17(3)(a) Revoke/suspend/vary','(c) Stating that the maintenance would be carried out by (x) firm',0,0,1,NULL,NULL,NULL,NULL,1),
    (195,'PSV',0,'10.4 - Section 17(3)(a) Revoke/suspend/vary','(d) Failure to declare conviction(s) on making the application',0,0,1,NULL,NULL,NULL,NULL,1),
    (196,'PSV',0,'10.5 - Section 17(3)(a) Revoke/suspend/vary','(e) Failure to declare on application involvement in previous licence',0,0,1,NULL,NULL,NULL,NULL,1),
    (197,'PSV',0,'10.6 - Section 17(3)(a) Revoke/suspend/vary','(f) Failure to declare that (x) had been made bankrupt/liquidated/disqualified (delete as applicable)',0,0,1,NULL,NULL,NULL,NULL,1),
    (198,'PSV',0,'10.7 - Section 17(3)(a) Revoke/suspend/vary','(g) Other (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (199,'PSV',0,'11 - Section 17 (3)(aa) Revoke/suspend/vary','An undertaking has not been fulfilled namely',0,0,0,NULL,NULL,NULL,NULL,1),
    (200,'PSV',0,'11.1 - Section 17 (3)(aa) Revoke/suspend/vary','(a) the laws relating to the driving and operation of vehicles used under the licence were observed',0,0,1,NULL,NULL,NULL,NULL,1),
    (201,'PSV',0,'11.2 - Section 17 (3)(aa) Revoke/suspend/vary','(b) the rules on driverâ€™s hours and tachographs are observed and proper records kept',0,0,1,NULL,NULL,NULL,NULL,1),
    (202,'PSV',0,'11.3 - Section 17 (3)(aa) Revoke/suspend/vary','(c) vehicles do not carry more than the permitted number of passengers',0,0,1,NULL,NULL,NULL,NULL,1),
    (203,'PSV',0,'11.4 - Section 17 (3)(aa) Revoke/suspend/vary','(d) vehicles, including hired vehicles, are kept in a fit and serviceable condition',0,0,1,NULL,NULL,NULL,NULL,1),
    (204,'PSV',0,'11.5 - Section 17 (3)(aa) Revoke/suspend/vary','(e) drivers report promptly any defects that could prevent the safe operation of vehicles, and that any defects are promptly recorded in writing',0,0,1,NULL,NULL,NULL,NULL,1),
    (205,'PSV',0,'11.6 - Section 17 (3)(aa) Revoke/suspend/vary','(f) records are kept (for 15 months) of all safety inspections, routine maintenance and repairs to vehicles and made available on request',0,0,1,NULL,NULL,NULL,NULL,1),
    (206,'PSV',0,'11.7 - Section 17 (3)(aa) Revoke/suspend/vary','(g) Other (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (207,'PSV',0,'12 - Section 17 (3)(b) Revoke/suspend/vary','Contravention of any condition attached to the licence i.e. failed to inform the Commissioner within 28 days of the following:',0,0,1,NULL,NULL,NULL,NULL,1),
    (208,'PSV',0,'12.01 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (2)(a) no longer has an effective and stable establishment in GB (S16 A)',0,0,1,NULL,NULL,NULL,NULL,1),
    (209,'PSV',0,'12.02 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (2)(b) requirement to be of good repute',0,0,1,NULL,NULL,NULL,NULL,1),
    (210,'PSV',0,'12.03 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (2)(c) requirement to be of appropriate financial standing (S16A)',0,0,1,NULL,NULL,NULL,NULL,1),
    (211,'PSV',0,'12.04 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (2)(d) the requirement to be of professional competence',0,0,1,NULL,NULL,NULL,NULL,1),
    (212,'PSV',0,'12.05 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (3)(a) TM no longer of good repute',0,0,1,NULL,NULL,NULL,NULL,1),
    (213,'PSV',0,'12.06 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (3)(b) No longer professionally competent',0,0,1,NULL,NULL,NULL,NULL,1),
    (214,'PSV',0,'12.07 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (3)(c)(i) TM is prohibited',0,0,1,NULL,NULL,NULL,NULL,1),
    (215,'PSV',0,'12.08 - Section 17 (3)(b) Revoke/suspend/vary','14ZA (3)(c)(ii) External TM exceeds the 4/50 rule',0,0,1,NULL,NULL,NULL,NULL,1),
    (216,'PSV',0,'12.09 - Section 17 (3)(b) Revoke/suspend/vary','Restricted licence only : No more than two PSV, not adapted to carry more than 16 passengers, can be used',0,0,1,NULL,NULL,NULL,NULL,1),
    (217,'PSV',0,'12.10 - Section 17 (3)(b) Revoke/suspend/vary','Other (please specify)',0,0,1,NULL,NULL,NULL,NULL,1),
    (218,'PSV',0,'13 - Section 17 (3)(c) Revoke/suspend/vary','A prohibition on a vehicle owned or operated by the operator',0,0,1,NULL,NULL,NULL,NULL,1),
    (219,'PSV',0,'14 - Section 17 (3)(d) Revoke/suspend/vary','RESTRICTED Licence holders only â€“ no longer of good repute and/or financial standing',0,0,1,NULL,NULL,NULL,NULL,1),
    (220,'PSV',0,'15 - Section 17 (3)(e) Revoke/suspend/vary','A material change in any of the circumstances relevant to the grant or variation of the licence (incl FPN/Conviction).',0,0,1,NULL,NULL,NULL,NULL,1),
    (221,'PSV',0,'16 - Section 17 (3)(f)','Licence subject to revocation, suspension, variation of condition following a direction under section 28(4) of the 1985 Act (director/individual disqualified on another licence)',0,0,1,NULL,NULL,NULL,NULL,1),
    (222,'PSV',0,'17 - Schedule 3(7A)(1)','TM good repute or professional competence',0,0,0,NULL,NULL,NULL,NULL,1),
    (223,'PSV',0,'18 - Schedule 3(7B)(1)','Disqualification TM',0,0,0,NULL,NULL,NULL,NULL,1),
    (224,'PSV',0,'19 - Schedule 3(7B)(1)','Financial assessor required',0,0,0,NULL,NULL,NULL,NULL,1),
    (225,'PSV',0,'20 - Schedule 3(7B)(1)','Driver(s) to be called',0,0,0,NULL,NULL,NULL,NULL,1),
    (226,'PSV',0,'21 - Section 26 of the Transport Act 1985','Failure to operate a local bus service in accordance with the registered details',0,0,0,NULL,NULL,NULL,NULL,1),
    (227,'PSV',0,'22 - Section 28 of the Transport Act 1985','Disqualification  of operator',0,0,0,NULL,NULL,NULL,NULL,1),
    (228,'PSV',0,'23.1 - Section 155 of the TA 2000','(a) Financial penalty for failure to operate bus service',0,0,0,NULL,NULL,NULL,NULL,1),
    (229,'PSV',0,'23.2 - Section 155 of the TA 2000','(b) Withdraw/suspend community licence',0,0,0,NULL,NULL,NULL,NULL,1),
    (230,'PSV',0,'23.3 - Section 155 of the TA 2000','(c) Other (please specify)',0,0,0,NULL,NULL,NULL,NULL,1),
    (231,'GV',1,'x NI-Section 12','New application',0,1,0,NULL,NULL,NULL,NULL,1),
    (232,'GV',1,'x NI-Section 16','Variation application',0,1,0,NULL,NULL,NULL,NULL,1),
    (233,'GV',1,'x NI-Section 23(1)(a)','Unauthorised use of a place as an operating centre',0,1,1,NULL,NULL,NULL,NULL,1),
    (234,'GV',1,'x NI-Section 23(1)(b)','Contravention of a licence condition (specify)',0,1,1,NULL,NULL,NULL,NULL,1),
    (235,'GV',1,'x NI-Section 23(1)(b)','(1) Fail to notify of change in maintenance arrangements',0,1,1,NULL,NULL,NULL,NULL,1),
    (236,'GV',1,'x NI-Section 23(1)(b)','(2) Fail to notify of change in ownership',0,1,1,NULL,NULL,NULL,NULL,1),
    (237,'GV',1,'x NI-Section 23(1)(b)','(3) Fail to notify of notifiable convictions ',0,1,1,NULL,NULL,NULL,NULL,1),
    (238,'GV',1,'x NI-Section 23(1)(b)','(4) Fail to notify of events which affect good repute ',0,1,1,NULL,NULL,NULL,NULL,1),
    (239,'GV',1,'x NI-Section 23(1)(b)','(5) Fail to notify of events which affect financial standing',0,1,1,NULL,NULL,NULL,NULL,1),
    (240,'GV',1,'x NI-Section 23(1)(b)','(6) Fail to notify of events which affect professional competence',0,1,1,NULL,NULL,NULL,NULL,1),
    (241,'GV',1,'x NI-Section 23(1)(b)','(7) Breach of road safety condition',0,1,1,NULL,NULL,NULL,NULL,1),
    (242,'GV',1,'x NI-Section 23(1)(b)','(8) Breach of environmental condition',0,1,1,NULL,NULL,NULL,NULL,1),
    (243,'GV',1,'x NI-Section 23(1)(b)','(9) Other (please specify)',0,1,1,NULL,NULL,NULL,NULL,1),
    (244,'GV',1,'x NI-Section 23(1)(c)','Schedule 2 Paragraph 5 convictions (operator)',0,1,1,NULL,NULL,NULL,NULL,1),
    (245,'GV',1,'x NI-Section 23(1)(c)','Schedule 2 Paragraph 5 convictions (servants/agents)',0,1,1,NULL,NULL,NULL,NULL,1),
    (246,'GV',1,'x NI-Section 23(1)(c)','Prohibitions',0,1,1,NULL,NULL,NULL,NULL,1),
    (247,'GV',1,'x NI-Section 23(1)(c)','Fixed Penalty or conditional offer issued',0,1,1,NULL,NULL,NULL,NULL,1),
    (248,'GV',1,'x NI-Section 23(1)(c)','Convictions â€“ for Schedule 2 Paragraph 5 (j) offences',0,1,1,NULL,NULL,NULL,NULL,1),
    (249,'GV',1,'x NI-Section 23(1)(d)','Failing to fulfil Statement of Expectation/False statement (specify)',0,1,1,NULL,NULL,NULL,NULL,1),
    (250,'GV',1,'x NI-Section 23(1)(d)','(1) Failure to declare previous refusal or revocation',0,1,1,NULL,NULL,NULL,NULL,1),
    (251,'GV',1,'x NI-Section 23(1)(d)','(2) Stating that (x) would be the TM responsible for vehicles on licence',0,1,1,NULL,NULL,NULL,NULL,1),
    (252,'GV',1,'x NI-Section 23(1)(d)','(3) Stating that the TM would not be responsible for any other licence',0,1,1,NULL,NULL,NULL,NULL,1),
    (253,'GV',1,'x NI-Section 23(1)(d)','(4) Stating that the vehicles would normally be kept at the o/c',0,1,1,NULL,NULL,NULL,NULL,1),
    (254,'GV',1,'x NI-Section 23(1)(d)','(5) Stating that the nominated o/c is not used by other operators',0,1,1,NULL,NULL,NULL,NULL,1),
    (255,'GV',1,'x NI-Section 23(1)(d)','(6) Stating that the vehicles would be given inspections at (x) intervals',0,1,1,NULL,NULL,NULL,NULL,1),
    (256,'GV',1,'x NI-Section 23(1)(d)','(7) Stating that the maintenance would be carried out by own staff',0,1,1,NULL,NULL,NULL,NULL,1),
    (257,'GV',1,'x NI-Section 23(1)(d)','(8) Stating that the maintenance would be carried out by (x) firm',0,1,1,NULL,NULL,NULL,NULL,1),
    (258,'GV',1,'x NI-Section 23(1)(d)','(9) Failure to declare that (x) has been made bankrupt',0,1,1,NULL,NULL,NULL,NULL,1),
    (259,'GV',1,'x NI-Section 23(1)(d)','(10) Failure to declare that (x) has been involved in a liquidated co',0,1,1,NULL,NULL,NULL,NULL,1),
    (260,'GV',1,'x NI-Section 23(1)(d)','(11) Failure to declare that (x) has been disqualified as a director',0,1,1,NULL,NULL,NULL,NULL,1),
    (261,'GV',1,'x NI-Section 23(1)(d)','(12) Failure to declare convictions on making the application',0,1,1,NULL,NULL,NULL,NULL,1),
    (262,'GV',1,'x NI-Section 23(1)(d)','(13) Stating that the holder would abide by any conditions on the licence',0,1,1,NULL,NULL,NULL,NULL,1),
    (263,'GV',1,'x NI-Section 23(1)(e)','Failing to fulfil a licence undertaking',0,1,1,NULL,NULL,NULL,NULL,1),
    (264,'GV',1,'x NI-Section 23(1)(e)','(1) Rules on drivers hours and tachographs would be observed',0,1,1,NULL,NULL,NULL,NULL,1),
    (265,'GV',1,'x NI-Section 23(1)(e)','(2) Vehicles and trailers not to be overloaded',0,1,1,NULL,NULL,NULL,NULL,1),
    (266,'GV',1,'x NI-Section 23(1)(e)','(3) Vehicles operate within speed limits',0,1,1,NULL,NULL,NULL,NULL,1),
    (267,'GV',1,'x NI-Section 23(1)(e)','(4) Vehicles and trailers would be kept fit and serviceable',0,1,1,NULL,NULL,NULL,NULL,1),
    (268,'GV',1,'x NI-Section 23(1)(e)','(5) Driver reports any defects in writing',0,1,1,NULL,NULL,NULL,NULL,1),
    (269,'GV',1,'x NI-Section 23(1)(e)','(6) Maintenance records would be kept for 15 months',0,1,1,NULL,NULL,NULL,NULL,1),
    (270,'GV',1,'x NI-Section 23(1)(e)','(7) Exceeding operating centre authorisation',0,1,1,NULL,NULL,NULL,NULL,1),
    (271,'GV',1,'x NI-Section 23(1)(e)','(8) Other (please specify)',0,1,1,NULL,NULL,NULL,NULL,1),
    (272,'GV',1,'x NI-Section 23(1)(f)','Bankruptcy/liquidation (other than voluntary liquidation)',0,1,1,NULL,NULL,NULL,NULL,1),
    (273,'GV',1,'x NI-Section 23(1)(g)','Material Change (please specify)',0,1,1,NULL,NULL,NULL,NULL,1),
    (274,'GV',1,'x NI-Section 23(1)(h)','Licence liable to revocation, suspension or curtailment following a direction under section 25 (4) (director/individual disqualified on another licence)',0,1,1,NULL,NULL,NULL,NULL,1),
    (275,'GV',1,'x NI-Section 24(1)(a)','To consider whether the operator has an effective and stable establishment in Great Britain.',0,1,1,NULL,NULL,NULL,NULL,1),
    (276,'GV',1,'x NI-Section 24(1)(a)','To consider the operator is still of good repute in accordance with Regulation 5 of the Goods Vehicles (Qualifications of Operators) Regualtions(Northern Ireland) 2012',0,1,1,NULL,NULL,NULL,NULL,1),
    (277,'GV',1,'x NI-Section 24(1)(a)','To consider whether the operator continues to meet the requirement to be of appropriate financial standing.',0,1,1,NULL,NULL,NULL,NULL,1),
    (278,'GV',1,'x NI-Section 24(1)(a)','No longer professionally competent.',0,1,1,NULL,NULL,NULL,NULL,1),
    (279,'GV',1,'x NI-Section 24(1)(b)','No longer of good repute (section 12A (3)(a))',0,1,1,NULL,NULL,NULL,NULL,1),
    (280,'GV',1,'x NI-Section 24(1)(b)','No longer professionally competent (section 12A (3)(b))',0,1,1,NULL,NULL,NULL,NULL,1),
    (281,'GV',1,'x NI-Section 24(1)(b)','External TM exceeds the 4/50 rule (section 12A (3)(c )(ii))',0,1,1,NULL,NULL,NULL,NULL,1),
    (282,'GV',1,'x NI-Section 25','Disqualification to be considered - Operator licence',0,1,0,NULL,NULL,NULL,NULL,1),
    (283,'GV',1,'x NI-Section 12A(3)(a) or (b)','TM good repute or professional competence',0,1,0,NULL,NULL,NULL,NULL,1),
    (284,'GV',1,'x NI-Section 25','Disqualification to be considered - Transport Manager',0,1,0,NULL,NULL,NULL,NULL,1);

INSERT INTO `pi_reason` (`pi_id`, `reason_id`) VALUES
    (1,118),
    (1,227);

INSERT INTO `pi_venue` (`id`, `traffic_area_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`,
    `version`, `name`, `address_id`) VALUES
    (1,0,B,NULL,NULL,NULL,1,'venue_1',21),
    (2,0,B,NULL,NULL,NULL,1,'venue_2',22),
    (3,0,NULL,NULL,NULL,NULL,1,'venue_3',23),
    (4,0,NULL,NULL,NULL,NULL,1,'venue_4',24),
    (5,0,NULL,NULL,NULL,NULL,1,'venue_5',25),
    (6,0,NULL,NULL,NULL,NULL,1,'venue_6',26),
    (7,0,NULL,NULL,NULL,NULL,1,'venue_7',27),
    (8,0,NULL,NULL,NULL,NULL,1,'venue_8',28),
    (9,0,NULL,NULL,NULL,NULL,1,'venue_9',29),
    (10,0,NULL,NULL,NULL,NULL,1,'venue_10',30),
    (11,0,NULL,NULL,NULL,NULL,1,'venue_11',31),
    (12,0,NULL,NULL,NULL,NULL,1,'venue_12',32),
    (13,0,NULL,NULL,NULL,NULL,1,'venue_13',33),
    (14,0,NULL,NULL,NULL,NULL,1,'venue_14',34),
    (15,0,NULL,NULL,NULL,NULL,1,'venue_15',35),
    (16,0,NULL,NULL,NULL,NULL,1,'venue_16',36),
    (17,0,NULL,NULL,NULL,NULL,1,'venue_17',37),
    (18,0,NULL,NULL,NULL,NULL,1,'venue_18',38),
    (19,0,NULL,NULL,NULL,NULL,1,'venue_19',39),
    (20,0,NULL,NULL,NULL,NULL,1,'venue_20',40),
    (21,0,NULL,NULL,NULL,NULL,1,'venue_21',41),
    (22,0,NULL,NULL,NULL,NULL,1,'venue_22',42),
    (23,0,NULL,NULL,NULL,NULL,1,'venue_23',43),
    (24,0,NULL,NULL,NULL,NULL,1,'venue_24',44),
    (25,0,NULL,NULL,NULL,NULL,1,'venue_25',45),
    (26,0,NULL,NULL,NULL,NULL,1,'venue_26',46),
    (27,0,NULL,NULL,NULL,NULL,1,'venue_27',47),
    (28,0,NULL,NULL,NULL,NULL,1,'venue_28',48),
    (29,0,NULL,NULL,NULL,NULL,1,'venue_29',49),
    (32,0,NULL,NULL,NULL,NULL,1,'venue_32',52),
    (33,0,NULL,NULL,NULL,NULL,1,'venue_33',53),
    (34,0,NULL,NULL,NULL,NULL,1,'venue_34',54),
    (35,0,NULL,NULL,NULL,NULL,1,'venue_35',55),
    (36,0,NULL,NULL,NULL,NULL,1,'venue_36',56),
    (37,0,NULL,NULL,NULL,NULL,1,'venue_37',57),
    (38,0,NULL,NULL,NULL,NULL,1,'venue_38',58),
    (39,0,NULL,NULL,NULL,NULL,1,'venue_39',59),
    (40,0,NULL,NULL,NULL,NULL,1,'venue_40',60),
    (41,0,NULL,NULL,NULL,NULL,1,'venue_41',61),
    (42,0,NULL,NULL,NULL,NULL,1,'venue_42',62),
    (43,0,NULL,NULL,NULL,NULL,1,'venue_43',63),
    (44,0,NULL,NULL,NULL,NULL,1,'venue_44',64);

INSERT INTO `presiding_tc` (`id`, `name`) VALUES
    (1,'Presiding TC Name 1'),
    (2,'Presiding TC Name 2'),
    (3,'Presiding TC Name 3');

INSERT INTO `impounding`
    (`id`, `pi_venue_id`, `impounding_type`, `case_id`,
    `outcome`, `last_modified_by`, `presiding_tc_id`, `created_by`,
    `application_receipt_date`, `outcome_sent_date`, `close_date`,
    `pi_venue_other`, `hearing_date`, `notes`, `created_on`, `last_modified_on`, `version`)
VALUES
    (17, 3, 'impt_hearing', 24,
    'impo_returned', NULL, 1, NULL,
    NOW(), NOW(), NOW(),
    NULL, NOW(), 'Some notes - db default', NOW(), NOW(), 1);

INSERT INTO `impounding_legislation_type`
    (`impounding_id`, `impounding_legislation_type_id`)
VALUES
    (17, 'imlgis_type_goods_gb1');

INSERT INTO `transport_manager_licence` (`id`, `licence_id`, `transport_manager_id`, `created_by`, `last_modified_by`,
    `deleted_date`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,7,1,NULL,NULL,NULL,NULL,NULL,1),
    (2,7,2,NULL,NULL,NULL,NULL,NULL,1);

INSERT INTO `tm_qualification` (`id`, `transport_manager_id`, `created_by`, `last_modified_by`, `country_code`,
    `qualification_type`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,NULL,NULL,'GB','CPCSI',NULL,NULL,1),
    (2,2,NULL,NULL,'GB','CPCSN',NULL,NULL,1);

INSERT INTO `trading_name` (`id`, `created_by`, `last_modified_by`, `name`, `created_on`, `last_modified_on`,
    `version`, `licence_id`) VALUES
    (7,3,4,'JSH Logistics',NOW(),NOW(),1,7),
    (30,2,1,'JSH Removals',NOW(),NOW(),1,30),
    (41,1,1,'TSG',NOW(),NOW(),1,41),
    (54,1,1,'TSG',NOW(),NOW(),1,54),
    (63,1,2,'Stagecoach',NOW(),NOW(),1,63),
    (75,0,2,'LCC',NOW(),NOW(),1,75),
    (110,0,2,'test',NOW(),NOW(),1,110);

INSERT INTO `traffic_area` (`created_by`, `last_modified_by`, `id`, `txc_name`, `created_on`, `last_modified_on`,
    `version`, `name`, `contact_details_id`) VALUES
    (2,2,'B','NorthEastern','2001-06-09 11:01:21','2001-06-09 11:01:21',1,'North East of England',1),
    (2,2,'C','NorthWestern','2001-06-09 11:01:21','2001-06-09 11:01:21',1,'North West of England',2),
    (1,1,'D','WestMidlands','2004-11-03 19:06:00','2004-11-03 19:06:00',1,'West Midlands',3),
    (2,2,'F','Eastern','2001-06-09 11:01:21','2001-06-09 11:01:21',1,'East of England',4),
    (1,1,'G','Welsh','2004-11-03 19:06:00','2004-11-03 19:06:00',1,'Wales',5),
    (2,2,'H','Western','2001-06-09 11:01:21','2001-06-09 11:01:21',1,'West of England',6),
    (2,2,'K','SouthEastMetropolitan','2001-06-09 11:01:21','2001-06-09 11:01:21',1,
    'London and the South East of England',7),
    (2,2,'M','Scottish','2001-06-09 11:01:21','2001-06-09 11:01:21',1,'Scotland',8),
    (1,1,'N','NorthernIreland','2012-09-14 00:00:00','2012-09-14 00:00:00',1,'Northern Ireland',9);

INSERT INTO `transport_manager` (`id`, `created_by`, `last_modified_by`, `tm_status`, `tm_type`, `deleted_date`,
    `created_on`, `last_modified_on`, `version`) VALUES
    (1,NULL,NULL,'active','Internal',NULL,NULL,NULL,1),
    (2,NULL,NULL,'active','External',NULL,NULL,NULL,1);

INSERT INTO `user` (`id`, `team_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`, `deleted_date`,
    `name`) VALUES
    (1,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Logged in user'),
    (2,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'John Spellman'),
    (3,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Steve Fox'),
    (4,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Amy Wrigg'),
    (5,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Phil Jowitt'),
    (6,3,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Kevin Rooney'),
    (7,4,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'Sarah Thompson');

INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7);

INSERT INTO `vehicle` (`id`, `created_by`, `last_modified_by`, `vrm`, `plated_weight`, `is_articulated`,
    `certificate_no`, `is_refrigerated`, `is_tipper`, `vi_action`, `psv_type`, `make_model`, `created_on`,
    `last_modified_on`, `version`) VALUES
    (1,NULL,4,'VRM1',7200,0,'CERT10001',0,0,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (2,NULL,6,'VRM2',3500,0,'CERT10002',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (3,NULL,5,'VRM3',3800,0,'CERT10003',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (4,NULL,1,'VRM4',6800,1,'CERT10004',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (5,NULL,4,'VRM1',7200,0,'CERT10005',0,0,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (6,NULL,6,'VRM2',3500,0,'CERT10006',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (7,NULL,5,'VRM3',3800,0,'CERT10007',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1),
    (8,NULL,1,'VRM4',6800,1,'CERT10008',0,1,NULL,NULL,NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1);

INSERT INTO `cases` (`id`, `licence_id`, `created_by`, `last_modified_by`, `description`, `ecms_no`, `open_date`,
    `case_type`, `close_date`, `annual_test_history`, `created_on`, `last_modified_on`, `version`) VALUES
    (24,7,NULL,NULL,'Case for convictions against company directors','E123456','2012-03-21 00:00:00','Compliance',NULL,NULL,'2013-11-12 12:27:33',   NULL,1),
    (28,7,NULL,NULL,'Convictions against operator','E123444','2012-06-13 00:00:00','Compliance',NULL,NULL,'2014-05-25 12:27:33',NULL,1),
    (29,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (30,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (31,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (32,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (33,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence','2014-03-29 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (34,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (35,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence','2014-04-15 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (36,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (37,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence','2014-04-23 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (38,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (39,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (40,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
    (41,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (42,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-06-01 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (43,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-06-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
    (44,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (45,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (46,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (47,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (48,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (49,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (50,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (51,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (52,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (53,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (54,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (55,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
    (56,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (57,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-11-01 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (58,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-11-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
    (59,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (60,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (61,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (62,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (63,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (64,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (65,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (66,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (67,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (68,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (69,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (70,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
    (71,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (72,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-11-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
    (73,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-11-03 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
    (74,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1);

INSERT INTO team(id,version,name) VALUES
    (1,1,'Marketing'),
    (2,1,'Development'),
    (3,1,'Infrastructure'),
    (4,1,'Support');

/* Application task */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (1,2,110,9,32,1,2,'A test task','2014-08-12',1);
    /* Licence task */
INSERT INTO task(id,application_id,licence_id,category_id,task_sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (2,null,110,1,69,2,2,'Another test task','2013-02-11',1);
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

SET foreign_key_checks = 1;