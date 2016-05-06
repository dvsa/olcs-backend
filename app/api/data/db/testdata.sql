SET foreign_key_checks = 0;

TRUNCATE TABLE `application`;
TRUNCATE TABLE `application_completion`;
TRUNCATE TABLE `application_tracking`;
TRUNCATE TABLE `application_operating_centre`;
TRUNCATE TABLE `bus_reg`;
TRUNCATE TABLE `bus_reg_other_service`;
TRUNCATE TABLE `bus_reg_traffic_area`;
TRUNCATE TABLE `bus_reg_local_auth`;
TRUNCATE TABLE `bus_short_notice`;
TRUNCATE TABLE `bus_notice_period`;
TRUNCATE TABLE `bus_reg_bus_service_type`;
TRUNCATE TABLE `bus_reg_variation_reason`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `companies_house_alert_reason`;
TRUNCATE TABLE `companies_house_alert`;
TRUNCATE TABLE `companies_house_officer`;
TRUNCATE TABLE `companies_house_company`;
TRUNCATE TABLE `change_of_entity`;
TRUNCATE TABLE `disc_sequence`;
TRUNCATE TABLE `event_history`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `fee`;
TRUNCATE TABLE `fee_txn`;
TRUNCATE TABLE `txn`;
TRUNCATE TABLE `grace_period`;
TRUNCATE TABLE `licence`;
TRUNCATE TABLE `licence_vehicle`;
TRUNCATE TABLE `licence_no_gen`;
TRUNCATE TABLE `licence_operating_centre`;
TRUNCATE TABLE `licence_status_rule`;
TRUNCATE TABLE `local_authority`;
TRUNCATE TABLE `legacy_offence`;
TRUNCATE TABLE `note`;
TRUNCATE TABLE `oc_complaint`;
TRUNCATE TABLE `operating_centre`;
TRUNCATE TABLE `operating_centre_opposition`;
TRUNCATE TABLE `opposer`;
TRUNCATE TABLE `opposition`;
TRUNCATE TABLE `opposition_grounds`;
TRUNCATE TABLE `operating_centre_opposition`;
TRUNCATE TABLE `organisation`;
TRUNCATE TABLE `other_licence`;
TRUNCATE TABLE `organisation_person`;
TRUNCATE TABLE `person`;
TRUNCATE TABLE `disqualification`;
TRUNCATE TABLE `pi`;
TRUNCATE TABLE `pi_decision`;
TRUNCATE TABLE `pi_type`;
TRUNCATE TABLE `pi_hearing`;
TRUNCATE TABLE `pi_reason`;
TRUNCATE TABLE `prohibition`;
TRUNCATE TABLE `prohibition_defect`;
TRUNCATE TABLE `presiding_tc`;
TRUNCATE TABLE `previous_conviction`;
TRUNCATE TABLE `psv_disc`;
TRUNCATE TABLE `recipient`;
TRUNCATE TABLE `recipient_traffic_area`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `transport_manager_application`;
TRUNCATE TABLE `transport_manager_licence`;
TRUNCATE TABLE `tm_application_oc`;
TRUNCATE TABLE `tm_licence_oc`;
TRUNCATE TABLE `tm_qualification`;
TRUNCATE TABLE `tm_case_decision`;
TRUNCATE TABLE `tm_case_decision_rehab`;
TRUNCATE TABLE `tm_case_decision_unfitness`;
TRUNCATE TABLE `tm_employment`;
TRUNCATE TABLE `trading_name`;
TRUNCATE TABLE `transport_manager`;
TRUNCATE TABLE `txc_inbox`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `user_role`;
TRUNCATE TABLE `organisation_user`;
TRUNCATE TABLE `vehicle`;
TRUNCATE TABLE `cases`;
TRUNCATE TABLE `case_category`;
TRUNCATE TABLE `impounding`;
TRUNCATE TABLE `impounding_legislation_type`;
TRUNCATE TABLE `team`;
TRUNCATE TABLE `task`;
TRUNCATE TABLE `txc_inbox`;
TRUNCATE TABLE `scan`;
TRUNCATE TABLE `serious_infringement`;
TRUNCATE TABLE `si_category`;
TRUNCATE TABLE `si_category_type`;
TRUNCATE TABLE `si_penalty`;
TRUNCATE TABLE `si_penalty_erru_requested`;
TRUNCATE TABLE `si_penalty_erru_imposed`;
TRUNCATE TABLE `si_penalty_imposed_type`;
TRUNCATE TABLE `si_penalty_requested_type`;
TRUNCATE TABLE `si_penalty_type`;
TRUNCATE TABLE `statement`;
TRUNCATE TABLE `submission_action`;
TRUNCATE TABLE `submission_action_type`;
TRUNCATE TABLE `publication`;
TRUNCATE TABLE `publication_link`;
TRUNCATE TABLE `publication_police_data`;
TRUNCATE TABLE `public_holiday`;
TRUNCATE TABLE `community_lic`;
TRUNCATE TABLE `community_lic_suspension`;
TRUNCATE TABLE `community_lic_suspension_reason`;
TRUNCATE TABLE `community_lic_withdrawal`;
TRUNCATE TABLE `community_lic_withdrawal_reason`;
TRUNCATE TABLE `previous_conviction`;
TRUNCATE TABLE `operating_centre_opposition`;
TRUNCATE TABLE `case_outcome`;
TRUNCATE TABLE `trailer`;
TRUNCATE TABLE `workshop`;
TRUNCATE TABLE `inspection_request`;
TRUNCATE TABLE `user_role`;
TRUNCATE TABLE `correspondence_inbox`; -- no inserts, not sure we need to truncate?
TRUNCATE TABLE `grace_period`;
TRUNCATE TABLE `printer`;
TRUNCATE TABLE `team_printer`;
TRUNCATE TABLE `historic_tm`;
TRUNCATE TABLE `sla_target_date`;

/* Test documents */
INSERT IGNORE INTO document(id,licence_id,bus_reg_id,description,filename,is_external,category_id,sub_category_id,
issued_date,document_store_id) VALUES
    (672,7,null,'Test document digital','testdocument2.doc',1,1,1,'2014-08-25 12:04:35',''),
    (673,7,null,'Test document 3','testdocument3.doc',0,1,2,'2014-08-22 11:01:00',''),
    (674,7,null,'Test document 4','testdocument4.doc',0,2,3,'2014-08-24 16:23:00',''),
    (675,7,null,'Test document 5','testdocument5.xls',0,2,3,'2014-07-01 15:01:00',''),
    (676,7,null,'Test document 6','testdocument6.docx',0,2,3,'2014-07-05 09:00:05',''),
    (677,7,null,'Test document 7','testdocument7.xls',0,2,4,'2014-07-05 10:23:00',''),
    (678,7,null,'Test document 8','testdocument8.doc',1,2,4,'2014-07-05 10:45:00',''),
    (679,7,null,'Test document 9','testdocument9.ppt',1,2,4,'2014-08-05 08:59:40',''),
    (680,7,null,'Test document 10','testdocument10.jpg',0,1,2,'2014-08-08 12:47:00',''),
    (681,7,null,'Test document 11','testdocument11.txt',0,1,1,'2014-08-14 14:00:00',''),
    (682,7,null,'Test document 12','testdocument12.xls',1,1,2,'2014-08-28 14:03:00',''),

    (800,110,2,'Test bus transxchange','transxchange.zip',1,3,107,'2014-08-28 14:03:00','documents/ebsr/2015/09/28/20150928145132__pb00000092-newapp.zip'),
    (801,110,2,'Test bus transxchange PDF','transxchange.pdf',1,3,108,'2014-08-28 14:03:00','documents/ebsr/2015/09/28/20150928155523__transxchange.pdf'),
    (802,110,2,'Test bus route','route.jpg',1,3,36,'2014-08-28 14:03:00','documents/ebsr/2015/09/28/20150928145035__basingstoke2.JPG'),

    (803,110,2,'Test bus transxchange for LA 2','transxchange_LA2.zip',1,3,107,
    '2014-08-28 14:03:00',''),
    (804,110,2,'Test bus transxchange PDF for LA 2','transxchange_LA2.pdf',1,3,108,
    '2014-08-28 14:03:00',
    ''),
    (805,110,2,'Test bus route for LA 2','route_LA2_Org1.jpg',1,3,36,'2014-08-28 14:03:00',''),

    (806,110,2,'Test bus transxchange for LA 1','transxchange_LA1.zip',1,3,107,
    '2014-08-28 14:03:00',''),
    (807,110,2,'Test bus transxchange PDF for LA 1','transxchange_LA1.pdf',1,3,108,
    '2014-08-28 14:03:00',''),
    (808,110,2,'Test bus route for LA 1','route_LA1.jpg',1,3,36,'2014-08-28 14:03:00','');

INSERT INTO txc_inbox (id, pdf_document_id, route_document_id, zip_document_id, bus_reg_id, created_by,
local_authority_id, organisation_id, file_read, variation_no, created_on) VALUES
(1, 801, 802, 800, 2, 1, NULL, 1, 0, 13, '2014-03-24 16:53:00'),
(2, 804, 805, 803, 2, 1, 2, 1, 0, 14, '2014-03-24 16:53:00'),
(3, 807, 808, 806, 2, 1, 1, 1, 0, 15, '2014-03-24 16:53:00'),
(4, 807, 808, 806, 20, 1, 1, 1, 0, 16, '2014-03-24 16:53:00'),
(5, 807, 808, 806, 20, 1, 1, 1, 0, 17, '2014-03-24 16:53:00'),
(6, 807, 808, 806, 20, 1, 1, 1, 0, 18, '2014-03-24 16:53:00'),
(7, 807, 808, 806, 19, 1, 1, 1, 0, 19, '2014-03-24 16:53:00'),
(8, 807, 808, 806, 19, 1, 1, 1, 0, 20, '2014-03-24 16:53:00'),
(9, 807, 808, 806, 19, 1, 1, 1, 0, 21, '2014-03-24 16:53:00');

INSERT INTO `address` (`id`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`,
    `postcode`, `town`, `country_code`, `created_on`, `last_modified_on`, `version`) VALUES
    (1008,NULL,NULL,'Unit 9b','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1021,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1025,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','GB',NOW(),NOW(),1),
    (1026,NULL,NULL,'5 High Street','Harehills','','','LS9 6GN','Leeds','GB',NOW(),NOW(),1),
    (1027,NULL,NULL,'209 Errwood Road','','','','M19 1JB','Manchester','GB',NOW(),NOW(),1),
    (1028,NULL,NULL,'6 High Street','Harehills','','','LS9 6GN','Leeds','GB',NOW(),NOW(),1),
    (1029,NULL,NULL,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (1030,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1031,NULL,NULL,'Solway Business Centre','Kingstown','Westpoint','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1037,NULL,NULL,'Unit 10','10 High Street','Alwoodley','','LS7 9SD','Leeds','GB',NOW(),NOW(),1),
    (1039,NULL,NULL,'15 Avery Street','Harehills','','','LS9 5SS','Leeds','GB',NOW(),NOW(),1),
    (1041,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1042,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1054,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1055,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1063,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (1064,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (1067,NULL,NULL,'Park Cottage','Coldcotes Avenue','','','LS9 6NE','Leeds','GB',NOW(),NOW(),1),
    (1072,NULL,NULL,'38 George Street','Edgbaston','','','B15 1PL','Birmingham','GB',NOW(),NOW(),1),
    (1075,NULL,NULL,'','123 A Street','An Area','','LS12 1BB','Leeds','GB',NOW(),NOW(),1),
    (1076,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (1100,NULL,NULL,'Test Partnership LLP','10 Partnerships street','PartnershipDistrict','Partnership Land','PA7 5IP',
    'Leeds','GB',NOW(),NOW(),1),
    (1104,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1105,NULL,NULL,'Unit 1','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1106,NULL,NULL,'Unit 2','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1107,NULL,NULL,'Unit 3','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1108,NULL,NULL,'Unit 4','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1109,NULL,NULL,'A Place','123 Some Street','Some Area','','WM5 2FA','Birmingham','GB',NOW(),NOW(),1),
    (1110,NULL,NULL,'Park Cottage','Coldcotes Avenue','','','LS9 6NE','Leeds','GB',NOW(),NOW(),1),
    (1111,NULL,NULL,'Unit 4','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (1112,NULL,NULL,'A Place','123 Some Street','Some Area','','WM5 2FA','Birmingham','GB',NOW(),NOW(),1),
    (1113,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (1114,NULL,NULL,'Unit 10','10 High Street','Alwoodley','','LS7 9SD','Leeds','GB',NOW(),NOW(),1),
    (1115,NULL,NULL,'123 House','A Street','An Area','','LS12 1BB','Leeds','GB',NOW(),NOW(),1);

INSERT INTO `application` (
    `id`, `licence_id`, `created_by`, `last_modified_by`, `status`,
    `tot_auth_vehicles`, `tot_community_licences`,
    `tot_auth_trailers`, `bankrupt`, `liquidation`, `receivership`, `administration`,
    `disqualified`, `insolvency_details`, `received_date`,
    `target_completion_date`, `prev_conviction`, `created_on`, `last_modified_on`,
    `version`, `is_variation`, `goods_or_psv`, `ni_flag`, `licence_type`,
    `interim_status`, `interim_reason`, `interim_start`, `interim_end`, `interim_auth_vehicles`, `interim_auth_trailers`, `applied_via`
) VALUES
    (
        1,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2010-12-15 10:48:00',
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, 'ltyp_si',
        'int_sts_requested', 'Interim reason', '2014-01-01', '2015-01-01', 10, 20, 'applied_via_selfserve'
    ),
    (
        2,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,NULL,NULL,
        1,1,'lcat_gv',0, NULL,
        NULL,NULL,NULL,NULL,NULL,NULL, 'applied_via_selfserve'
    ),
    (
        3,210,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, NULL,
        NULL,NULL,NULL,NULL,NULL,NULL, 'applied_via_selfserve'
    ),
    (
        6,114,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,'2014-04-30 12:09:37','2014-04-30 12:09:39',
        1,0,'lcat_psv',1,NULL,
        NULL,NULL,NULL,NULL,NULL,NULL, 'applied_via_selfserve'
    );

INSERT INTO `application_completion` (`application_id`, `created_by`, `last_modified_by`, `last_section`, `created_on`, `last_modified_on`, `version`) VALUES
(1,NULL,NULL,NULL,NULL,NULL,1),
(2,NULL,NULL,NULL,NULL,NULL,1),
(3,NULL,NULL,NULL,NULL,NULL,1);

INSERT INTO `application_tracking` (`application_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`) VALUES
(1,NULL,NULL,NULL,NULL,1),
(2,NULL,NULL,NULL,NULL,1),
(3,NULL,NULL,NULL,NULL,1);

INSERT INTO `application_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `application_id`, `operating_centre_id`, `action`, `is_interim`) VALUES
(1,NULL,NULL,34,23,1,0,NULL,'2014-03-13',1,NULL,NULL,1,1,16, 'A', 1),
(2,NULL,NULL,34,23,1,0,NULL,'2014-03-21',1,NULL,NULL,1,1,16, NULL, 0),
(3,NULL,NULL,34,23,1,0,NULL,'2014-04-01',1,NULL,NULL,1,1,16, 'U', 0);

INSERT INTO `licence_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `licence_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,7,16),
(2,NULL,NULL,10,0,1,0,NULL,NULL,1,NULL,NULL,1,110,16),
(3,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,41,17),
(4,NULL,NULL,32,46,1,0,NULL,NULL,1,NULL,NULL,1,7,72);

INSERT INTO `bus_reg` (`id`, `bus_notice_period_id`, `parent_id`, `revert_status`, `subsidised`, `created_by`, `last_modified_by`, `licence_id`, `status`, `withdrawn_reason`, `application_signed`, `copied_to_la_pte`, `ebsr_refresh`, `finish_point`, `has_manoeuvre`, `has_not_fixed_stop`, `is_quality_contract`, `is_quality_partnership`, `is_short_notice`, `is_txc_app`, `la_short_note`, `manoeuvre_detail`, `map_supplied`, `need_new_stop`, `new_stop_detail`, `not_fixed_stop_detail`, `op_notified_la_pte`, `organisation_email`, `other_details`, `quality_contract_details`, `quality_partnership_details`, `quality_partnership_facilities_used`, `reason_cancelled`, `reason_refused`, `reason_sn_refused`, `received_date`, `reg_no`, `route_description`, `route_no`, `short_notice_refused`, `start_point`, `stopping_arrangements`, `subsidy_detail`, `timetable_acceptable`, `trc_condition_checked`, `trc_notes`, `txc_app_type`, `use_all_stops`, `variation_no`, `via`, `created_on`, `effective_date`, `end_date`, `last_modified_on`, `service_no`, `version`)
VALUES
  (1, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 'breg_s_new', '', 0, 0, 1, 'Sheffield', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-13', 'PD2737280/1', 'Route description', 1, 0, 'Doncaster', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'York', NULL, '2014-03-15', NULL, NULL, '90839', 1),
  (2, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 0, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description', 2, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 0, 'York', NULL, '2014-03-05', NULL, NULL, '46474', 1),
  (3, 1, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 'breg_s_new', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-11', 'PD2737280/3', 'Scotish Route description', 3, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (4, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 'breg_s_new', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-05-24', 'PD2737280/4', 'Non-scottish Route description cancelled', 4, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'York', NULL, '2014-05-31', NULL, NULL, '26453', 1),
  (5, 2, 2, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 1', 2, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 1, 'York', NULL, '2014-03-05', NULL, NULL, '46474', 1),
  (6, 2, 5, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 2', 2, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 2, 'York', NULL, '2014-03-08', NULL, NULL, '46474', 1),
  (7, 2, 6, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 3', 2, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 3, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (8, 1, 3, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-15', 'PD2737280/3', 'Scotish Route description', 3, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 1, 'Dundee', NULL, '2014-03-15', NULL, NULL, '34254', 1),
  (9, 1, 8, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-11', 'PD2737280/3', 'Scotish Route description', 3, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 2, 'Dundee', NULL, '2014-03-11', NULL, NULL, '34254', 1),
  (10, 1, 9, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-12', 'PD2737280/3', 'Scotish Route description', 3, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 3, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (11, 1, 10, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-13', 'PD2737280/3', 'Scotish Route description', 3, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 4, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (12, 2, 4, 'breg_s_new', 'bs_no', 1, 1, 110, 'breg_s_var', '', 0, 0, 0, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-05-27', 'PD2737280/4', 'Non-scottish Route description cancelled', 4, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 1, 'York', NULL, '2014-05-27', NULL, NULL, '26453', 1),
  (13, 2, 7, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 4', 2, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 4, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (14, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 5, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (15, 2, 14, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_cancellation', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 6', 2, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 6, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (16, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0,  '',0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 7, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (17, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0,  '',0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 8, 'York', NULL,'2014-03-10', NULL, NULL, '46474', 1),
  (18, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 9, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (19, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 10, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1),
  (20, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 5', 2, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 11, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1);

INSERT INTO `bus_reg_traffic_area` (`bus_reg_id`, `traffic_area_id`)
VALUES
  (1, 'B'),
  (1, 'G'),
  (2, 'B'),
  (2, 'G'),
  (12, 'B'),
  (12, 'D'),
  (12, 'F'),
  (15, 'B'),
  (15, 'H'),
  (15, 'M');

INSERT INTO `bus_reg_local_auth` (`bus_reg_id`, `local_authority_id`)
VALUES
  (2, 3),
  (2, 6),
  (2, 8),
  (1, 3),
  (1, 6),
  (1, 8);


INSERT INTO `bus_reg_bus_service_type` (`bus_reg_id`, `bus_service_type_id`)
VALUES
 (1, 1),
 (1, 3),
 (1, 4),
 (2, 5),
 (2, 6),
 (2, 9),
 (12, 2),
 (12, 4),
 (12, 10),
 (15, 5);

INSERT INTO `bus_reg_variation_reason` (`bus_reg_id`, `variation_reason_id`)
VALUES
  (12, 'brvr_timetable'),
  (12, 'brvr_start_end'),
  (12, 'brvr_stops');

INSERT INTO `bus_reg_other_service`
(`id`, `bus_reg_id`, `last_modified_by`, `created_by`, `service_no`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 1, 90840, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (2, 1, 1, 1, 90841, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1),
  (3, 2, 1, 1, 46480, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (4, 2, 1, 1, 46496, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1),
  (5, 12, 1, 1, 13249, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (6, 12, 1, 1, 13355, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1),
  (7, 15, 1, 1, 15712, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (8, 15, 1, 1, 15719, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1);

INSERT INTO `bus_short_notice`
(`id`, `last_modified_by`, `created_by`, `bus_reg_id`, `bank_holiday_change`, `unforseen_change`, `unforseen_detail`,
 `timetable_change`, `timetable_detail`, `replacement_change`, `replacement_detail`, `holiday_change`, `holiday_detail`,
 `trc_change`, `trc_detail`, `police_change`, `police_detail`, `special_occasion_change`, `special_occasion_detail`,
 `connection_change`, `connection_detail`, `not_available_change`, `not_available_detail`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 1, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
   'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
   null, null, 1),
  (2, 1, 1, 2, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
   'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
   null, null, 1),
  (3, 1, 1, 3, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (4, 1, 1, 4, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (5, 1, 1, 5, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (6, 1, 1, 6, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (7, 1, 1, 7, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (8, 1, 1, 8, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (9, 1, 1, 9, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (10, 1, 1, 10, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (11, 1, 1, 11, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (12, 1, 1, 12, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (13, 1, 1, 13, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (14, 1, 1, 14, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1),
  (15, 1, 1, 15, 0, 0, 'unforseen detail', 0, 'timetable detail', 0, 'replacement detail', 0, 'holiday detail', 0,
  'trc detail', 0, 'police detail', 0, 'special occasion detail', 0, 'connection detail', 0, 'not available detail',
  null, null, 1);

INSERT INTO `bus_notice_period`
(`id`, `notice_area`, `standard_period`, `cancellation_period`, `created_by`, `last_modified_by`, `created_on`,
 `last_modified_on`,`version`)
VALUES
  (1,'Scotland',42,90,NULL,NULL,NULL,NULL,1),
  (2,'Other',56,0,NULL,NULL,NULL,NULL,1);

INSERT INTO `complaint` (`complainant_contact_details_id`, `status`, `complaint_type`, `is_compliance`, `created_by`,
    `last_modified_by`, `case_id`, `complaint_date`, `driver_forename`, `driver_family_name`, `description`, `vrm`,
    `created_on`, `last_modified_on`, `version`, `closed_date`)
VALUES
    (103, 'cs_ack', 'ct_cov', 1, NULL, NULL, 24, '2015-01-16 10:37:10', 'Driver F John',
    'Driver L Smith', 'Some major complaint about condition of vehicle', 'VRM123T', NOW(), NOW(), 1, NULL),
        (103, 'cs_ack', 'ct_cov', 1,  NULL, NULL, 24, '2015-01-15 10:37:10', 'Driver F Joe',
    'Driver L Bloggs', 'Exhaust fumes from parked vehicles', 'ABC456S', NOW(), NOW(), 1, NULL),
        (107, 'cs_ack', 'ct_cov', 1, NULL, NULL, 24, '2015-01-14 10:37:10', 'Alberto',
    'Van der Groot', 'Speeding', 'SHA123S', NOW(), NOW(), 1, '2015-01-16 10:37:10'),
        (108, 'cs_ack', 'ct_cov', 1, NULL, NULL, 24, '2015-01-13 10:37:10', 'Ian',
    'McDonald', 'Revving engine early in morning', 'PRG426F', NOW(), NOW(), 1, '2015-01-16 10:37:10'),
        (109, 'ecst_closed', 'ct_cov', 0, NULL, NULL, 24, '2015-01-16 10:37:10', 'Driver F John',
    'Driver L Smith', 'Vehicle burning oil', 'VRM123T', '2014-01-01', NOW(), 1, '2015-01-16 10:37:10'),
        (110, 'ecst_closed', 'ct_cov', 0,  NULL, NULL, 24, '2015-01-16 10:37:10', 'Driver F Joe',
    'Driver L Bloggs', 'Exhaust fumes from parked vehicles', 'ABC456S', '2014-02-02', NOW(), 1, '2015-01-16 10:37:10'),
        (111, 'ecst_closed', 'ct_cov', 0, NULL, NULL, 24, '2015-01-15 10:37:10', 'Ian',
    'McDonald', 'Revving engine early in morning', 'PRG426F', '2014-03-03', NOW(), 1, '2015-01-16 10:37:10'),
        (112, 'ecst_closed', 'ct_cov', 0, NULL, NULL, 24, '2015-01-16 10:37:10', 'Ian',
    'McDonald', 'Revving engine early in morning', 'PRG426F', '2014-03-03', NOW(), 1, '2015-01-16 10:37:10'),
        (113, 'ecst_open', 'ct_cov', 0, NULL, NULL, 24, '2015-01-17 10:37:10', 'Ian',
    'McDonald', 'Revving engine early in morning', 'PRG426F', '2014-03-03', NOW(), 1, NULL);

INSERT INTO `oc_complaint` (`complaint_id`, `operating_centre_id`)
VALUES
    (7, 16),
    (7, 21),
    (7, 37),
    (9, 39);

INSERT INTO `condition_undertaking` (`id`, `case_id`, `licence_id`, `application_id`, `operating_centre_id`,
`created_by`,`last_modified_by`, `added_via`, `attached_to`, `condition_type`, `deleted_date`, `is_draft`,
    `is_fulfilled`, `notes`, `action`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,24,NULL,NULL,16,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 1','A',NOW(),NULL,1),
    (2,24,NULL,NULL,16,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 2','A',NOW(),NULL,1),
    (3,24,NULL,NULL,21,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 3','A',NOW(),NULL,1),
    (4,24,7,NULL,NULL,NULL,NULL,'cav_case','cat_lic','cdt_und',NULL,0,1,'Some notes 4','A',NOW(),NULL,1),
    (5,24,7,NULL,NULL,NULL,NULL,'cav_case','cat_lic','cdt_und',NULL,0,1,'Some notes 5','U',NOW(),NULL,1),
    (6,24,7,NULL,NULL,NULL,NULL,'cav_lic','cat_lic','cdt_con',NULL,0,1,'Some notes 6','A',NOW(),NULL,1),
    (7,24,NULL,NULL,48,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 7','D',NOW(),NULL,1),
    (8,24,NULL,NULL,37,NULL,NULL,'cav_case','cat_oc','cdt_und',NULL,0,1,'Some invoice_notes 8','D',NOW(),NULL,1),
    (9,24,7,NULL,NULL,NULL,NULL,'cav_lic','cat_lic','cdt_con',NULL,0,0,'Some notes 9','D',NOW(),NULL,1),
    (10,24,7,NULL,NULL,NULL,NULL,'cav_lic','cat_lic','cdt_con',NULL,0,0,'Some notes 10','A',NOW(),NULL,1),
    (11,24,NULL,1,16,NULL,NULL,'cav_app','cat_lic','cdt_con',NULL,0,0,'Some notes 11','A',NOW(),NULL,1);

INSERT INTO `contact_details` (`id`,`contact_type`,`address_id`,`person_id`,
   `last_modified_by`,`created_by`,`fao`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
    (23,'ct_corr',1109,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (28,'ct_corr',1028,10,2,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (21,'ct_reg',1021,NULL,0,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (30,'ct_reg',1030,NULL,2,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (31,'ct_corr',1031,NULL,0,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (37,'ct_oc',1037,NULL,2,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (39,'ct_oc',1039,NULL,4,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (41,'ct_reg',1041,NULL,2,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (42,'ct_corr',1042,NULL,1,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (54,'ct_reg',1054,NULL,4,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (55,'ct_corr',1055,NULL,3,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (63,'ct_reg',1063,NULL,3,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (64,'ct_corr',1064,NULL,0,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (67,'ct_oc',1067,NULL,4,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (72,'ct_oc',1072,NULL,2,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (75,'',1075,NULL,4,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (76,'ct_corr',1076,46,4,1,'Important Person',0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (77,'ct_corr',1072,46,4,1,'Important Person',0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (100,'ct_reg',1100,44,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (101,'ct_user',1026,4,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (102,'ct_corr',1041,NULL,1,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (103,'ct_complainant',1072,46,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (104,'ct_tm',1110,NULL,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (105,'ct_user',1026,81,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (106,'ct_user',1026,82,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (107,'ct_complainant',1072,33,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (108,'ct_complainant',1072,34,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (109,'ct_complainant',1072,35,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (110,'ct_complainant',1026,60,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (111,'ct_complainant',1026,65,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (112,'ct_complainant',1026,66,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (113,'ct_complainant',1026,77,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (114,'ct_user',1126,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (115,'ct_tm',1111,NULL,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (116,'ct_tm',1112,NULL,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (117,'ct_tm',1113,65,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (118,'ct_tm',1114,66,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (119,'ct_tm',1115,77,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (120,'ct_corr',1105,4,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (121,'ct_corr',1106,9,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (122,'ct_corr',1107,10,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (123,'ct_corr',1108,11,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (130,'ct_user',1026,83,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (131,'ct_user',1026,84,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (132,'ct_user',1026,85,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (133,'ct_user',1026,86,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04', '2014-11-24 10:30:04',1),
    (140,'ct_partner',1,NULL,1,1,NULL,0,NULL,'HMRC',NULL,'2000-04-02 00:00:00',NULL,1),
    (141,'ct_partner',1,NULL,1,1,NULL,0,NULL,'DVSA',NULL,'2000-04-02 00:00:00',NULL,1),
    (142,'ct_partner',1,NULL,1,1,NULL,0,NULL,'Police',NULL,'2000-04-02 00:00:00',NULL,1),
    (143,'ct_partner',1,NULL,1,1,NULL,0,NULL,'Department of Work and Pensions',NULL,'2000-04-02 00:00:00', NULL,1),
    (144,'ct_partner',1,NULL,1,1,NULL,0,NULL,'Home Office',NULL,'2000-04-02 00:00:00',NULL,1),
    (164,'ct_user',1,87,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (165,'ct_user',1,84,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (166,'ct_user',1026,82,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (167,'ct_user',NULL,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (168,'ct_user',NULL,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (169,'ct_user',NULL,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (170,'ct_user',NULL,NULL,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1);

INSERT INTO `conviction` (`id`, `case_id`, `created_by`, `last_modified_by`, `category_text`,
`person_firstname`, `person_lastname`, `birth_date`,
    `offence_date`, `conviction_date`, `court`, `penalty`, `costs`, `msi`, `operator_name`,
    `defendant_type`, `notes`, `taken_into_consideration`, `created_on`, `last_modified_on`, `version`,
    `conviction_category`) VALUES
    (25,24,3,4,'Test Category text 1',NULL,NULL,'1971-11-05','2012-03-10','2012-06-15','FPN','3 points on licence',
    '60',0,
    'John Smith Haulage Ltd.','def_t_op','Some notes 1',NULL, NOW(),NOW(), 1, 'conv_c_cat_1'),
    (26,24,0,4,'Conviction Child Category 1','John','Smith','1980-02-20','2012-04-10','2012-05-15',
    'Leeds Magistrate court',
    '3 points on licence','60',0,'','def_t_owner','Some notes 2',NULL, NOW(),NOW(), 1, 'conv_c_cat_2'),
    (27,24,1,3,'Conviction Child Category 3','Boris','Johnson','1962-08-12','2012-12-17','2013-03-02','FPN',
    '3 points on licence',
    '60',0,'',
    'def_t_owner','Some notes 3',NULL,NOW(),NOW(),1, 'conv_c_cat_4'),
    (29,24,3,3,'Conviction Child Category 4',NULL,NULL,'1976-03-11', '2012-03-10','2012-06-15',
    'Leeds Magistrate court',
    '6 monthly investigation','2000',1,'John Smith Haulage Ltd.','def_t_op','Some notes 4',NULL,NOW(),NOW(),1,
    'conv_c_cat_2');

INSERT INTO `legacy_offence` (`id`, `case_id`,`created_by`, `last_modified_by`, `definition`, `is_trailer`,
`num_of_offences`,
    `offence_authority`, `offence_date`, `offence_to_date`, `offender_name`, `points`, `position`, `offence_type`,
    `notes`, `vrm`, `created_on`, `last_modified_on`, `version`)
VALUES
    (1, 24,1, 1, 'Some Definition', 1, 1, 'Authority 1', '2014-09-26', '2015-09-26', 'Ronnie Biggs', 3,
    'Some Position', 'Some Offence Type', 'Some Notes for Offence (case 24)', 'VRM12', NOW(), NOW(), 1),
    (2, 29,1, 1, 'Some different definition', 1, 1, 'Authority 2', '2012-05-12', '2012-05-26', 'Al Capone', 3,
    'Some Position', 'Some Offence Type', 'Some Notes for Offence (case 29)', 'VRM12', NOW(), NOW(), 1);

INSERT INTO `ebsr_submission` (`id`, `document_id`, `ebsr_submission_type_id`,
    `ebsr_submission_status_id`, `bus_reg_id`, `submitted_date`, `licence_no`, `organisation_email_address`,
    `application_classification`, `variation_no`, `registration_no`, `validation_start`, `validation_end`,
    `publish_start`, `publish_end`, `process_start`, `process_end`, `distribute_start`, `distribute_end`,
    `distribute_expire`, `is_from_ftp`, `organisation_id`) VALUES
  (1, null, 'ebsrt_new', 'ebsrs_processing', 1, '2015-04-11 15:25:34', 'PB12351', null, null, 1, '1111', null,
  null, null, null, null, null, null, null,null, 0, null),
  (2, null, 'ebsrt_new', 'ebsrs_processing', 2, '2015-04-15 23:25:34', 'PB12352', null, null, 2, '1112', null, null, null, null, null,
  null, null, null,null, 0, null),
  (3, null, 'ebsrt_refresh', 'ebsrs_submitted', 3, '2015-03-11 15:25:34', 'PB12353', null, null, 3, '1113',
  null, null, null, null, null, null, null, null,null, 0, null),
  (4, null, 'ebsrt_refresh', 'ebsrs_expired', 4, '2015-02-21 12:35:34', 'PB12354', null, null, 4, '1114', null,
  null, null, null, null, null, null, null,null, 0, null),
  (5, null, 'ebsrt_unknown', 'ebsrs_validated', 5, '2015-02-14 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (6, null, 'ebsrt_new', 'ebsrs_processing', 6, '2013-01-14 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (7, null, 'ebsrt_refresh', 'ebsrs_validated', 7, '2013-08-24 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (8, null, 'ebsrt_unknown', 'ebsrs_expired', 99, '2011-09-14 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (9, null, 'ebsrt_new', 'ebsrs_processing', 99, '2009-11-14 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (10, null, 'ebsrt_refresh', 'ebsrs_validated', 5, '2015-01-04 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (11, null, 'ebsrt_unknown', 'ebsrs_validated', 3, '2014-09-30 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (12, null, 'ebsrt_refresh', 'ebsrs_processing', 3, '2006-06-07 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (13, null, 'ebsrt_new', 'ebsrs_validated', 1, '2010-05-05 11:55:32', 'PB12355', null, null, 5, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (14, null, 'ebsrt_new', 'ebsrs_validated', 20, '2010-05-05 11:55:32', 'PB12355', null, null, 5, '1115',
   null, null, null, null, null, null, null, null,null, 0, null),
  (15, null, 'ebsrt_new', 'ebsrs_validated', 19, '2010-05-05 11:55:32', 'PB12355', null, null, 5, '1115',
   null, null, null, null, null, null, null, null,null, 0, null)

  ;

INSERT INTO `fee` (
`id`,
`fee_type_id`,
`fee_status`,
`parent_fee_id`,
`application_id`,
`bus_reg_id`,
`licence_id`,
`task_id`,
`net_amount`,
`vat_amount`,
`gross_amount`,
`invoice_line_no`,
`invoiced_date`,
`description`,
`irfo_fee_exempt`,
`irfo_gv_permit_id`,
`created_by`,
`last_modified_by`,
`created_on`,
`last_modified_on`,
`version`
)
VALUES
(7,1,'lfs_ot',NULL,NULL,NULL,7,NULL,250.00,0.00,250.00,1,'2013-11-25 00:00:00','Application fee',NULL,NULL,1,NULL,NULL,NULL,1),
(30,1,'lfs_pd',NULL,NULL,NULL,110,NULL,251.00,0.00,251.00,1,'2013-11-22 00:00:00','Application fee',NULL,NULL,1,2,NULL,NULL,1),
(41,1,'lfs_ot',NULL,NULL,NULL,110,NULL,150.00,0.00,150.00,1,'2013-11-21 00:00:00','Grant fee',NULL,NULL,1,NULL,NULL,NULL,1),
(54,1,'lfs_ot',NULL,NULL,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-12 00:00:00','Application fee',NULL,NULL,1,NULL,NULL,NULL,1),
(63,1,'lfs_ot',NULL,NULL,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee',NULL,NULL,1,NULL,NULL,NULL,1),
(75,1,'lfs_ot',NULL,NULL,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee',NULL,NULL,1,NULL,NULL,NULL,1),
(76,1,'lfs_ot',NULL,1,NULL,110,NULL,250.50,0.00,250.50,1,'2013-11-25 00:00:00','Application fee 1',NULL,NULL,1,NULL,NULL,NULL,2),
(77,1,'lfs_ot',NULL,1,NULL,110,NULL,251.75,0.00,251.75,1,'2013-11-22 00:00:00','Application fee 2',NULL,NULL,1,NULL,NULL,NULL,2),
(78,1,'lfs_ot',NULL,1,NULL,110,NULL,150.00,0.00,150.00,1,'2013-11-21 00:00:00','Grant fee',NULL,NULL,1,NULL,NULL,NULL,3),
(79,1,'lfs_ot',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-12 00:00:00','Application fee 3',NULL,NULL,1,NULL,NULL,NULL,2),
(80,1,'lfs_pd',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee 4',NULL,NULL,1,2,NULL,NULL,1),
(81,1,'lfs_ot',NULL,1,NULL,110,NULL,1250.00,0.00,1250.00,1,'2013-11-10 00:00:00','Application fee 5',NULL,NULL,1,NULL,NULL,NULL,2),
(82,1,'lfs_ot',NULL,1,NULL,30,NULL,500.00,0.00,500.00,1,'2013-10-23 00:00:00','Bus route 1',NULL,NULL,1,NULL,NULL,NULL,2),
(83,1,'lfs_ot',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee 4',NULL,NULL,1,NULL,NULL,NULL,1),
(84,1,'lfs_ot',NULL,1,NULL,110,NULL,1250.00,0.00,1250.00,1,'2013-11-10 00:00:00','Application fee 5',NULL,NULL,1,NULL,NULL,NULL,2),
(85,1,'lfs_ot',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee 4',NULL,NULL,1,NULL,NULL,NULL,1),
(86,1,'lfs_ot',NULL,1,NULL,110,NULL,1250.00,0.00,1250.00,1,'2013-11-10 00:00:00','Application fee 5',NULL,NULL,1,NULL,NULL,NULL,2),
(87,1,'lfs_pd',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee 6',NULL,NULL,1,2,NULL,NULL,1),
(88,1,'lfs_cn',NULL,1,NULL,110,NULL,250.00,0.00,250.00,1,'2013-11-10 00:00:00','Application fee 7',NULL,NULL,1,2,NULL,NULL,1),
(89,1,'lfs_pd',NULL,3,NULL,210,NULL,254.40,0.00,254.40,1,'2013-11-10 00:00:00','Application fee 8',NULL,NULL,1,2,NULL,NULL,1),
(90,188,'lfs_ot',NULL,1,3,110,NULL,60.00,0.00,60.00,1,'2013-10-23 00:00:00','Bus Route Application Fee PD2737280/3 Variation 0',NULL,NULL,1,NULL,NULL,NULL,1),
(91,189,'lfs_ot',NULL,1,8,110,NULL,60.00,0.00,60.00,1,'2013-10-23 00:00:00','Bus Route Variation Fee PD2737280/3 Variation 1',NULL,NULL,1,NULL,NULL,NULL,1),
(92,189,'lfs_ot',NULL,1,9,110,NULL,60.00,0.00,60.00,1,'2013-10-23 00:00:00','Bus Route Variation Fee PD2737280/3 Variation 2',NULL,NULL,1,NULL,NULL,NULL,1),
(93,189,'lfs_ot',NULL,1,10,110,NULL,60.00,0.00,60.00,1,'2013-10-23 00:00:00','Bus Route Variation Fee PD2737280/3 Variation 3',NULL,NULL,1,NULL,NULL,NULL,1),
(94,189,'lfs_ot',NULL,1,11,110,NULL,60.00,0.00,60.00,1,'2013-10-23 00:00:00','Bus Route Variation Fee PD2737280/3 Variation 4',NULL,NULL,1,NULL,NULL,NULL,1),
(97,40008,'lfs_ot',NULL,NULL,NULL,NULL,NULL,123.45,0.00,123.45,1,'2015-04-01 12:34:56','Photocopying charge',NULL,NULL,1,NULL,NULL,NULL,1),
(98,40008,'lfs_ot',NULL,NULL,NULL,NULL,NULL,123.45,0.00,123.45,1,'2015-11-01 13:45:01','Court fee',NULL,NULL,1,NULL,NULL,NULL,1),
(99,40008,'lfs_ot',NULL,NULL,NULL,NULL,NULL,100.00,0.00,100.00,1,'2015-11-01 13:45:02','Test fee 99',NULL,NULL,1,NULL,NULL,NULL,1),
(100,40008,'lfs_ot',NULL,NULL,NULL,NULL,NULL,200.00,0.00,200.00,1,'2015-11-01 13:45:03','Test fee 100',NULL,NULL,1,NULL,NULL,NULL,1);

INSERT INTO `txn` (
    `id`,
    `reference`,
    `type`,
    `status`,
    `completed_date`,
    `payment_method`,
    `comment`,
    `waive_recommendation_date`,
    `waive_recommender_user_id`,
    `processed_by_user_id`
)
VALUES
    (10001,'OLCS-1234-2345','trt_payment','pay_s_pd','2015-08-26','fpm_cheque','',NULL,NULL,291),
    (10002,'REVERSAL-1234-2345','trt_reversal','pay_s_pd','2015-09-01','fpm_cheque','',NULL,NULL,291),
    (10003,'OLCS-3456-4567','trt_payment','pay_s_pd','2015-09-02','fpm_cash','',NULL,NULL,291),
    (10004,'OLCS-1234-5678','trt_payment','pay_s_pd','2015-11-04','fpm_card_online','',NULL,NULL,291),
    (10005,'REVERSAL-1234-5678','trt_reversal','pay_s_pd','2015-11-04','fpm_card_online','',NULL,NULL,291);

INSERT INTO `fee_txn`
    (`id`,`fee_id`,`txn_id`,`amount`,`reversed_fee_txn_id`)
VALUES
    (1, 97,10001,100.00,NULL),
    (2, 97,10002,-100.00,1),
    (3, 97,10003,120.00,NULL),
    (4, 99,10004,100.00, NULL),
    (5,100,10004,200.00, NULL),
    (6, 99,10005, -100.00, 4),
    (7,100,10005, -200.00, 5);

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `enforcement_area_id`, `created_by`, `correspondence_cd_id`, `establishment_cd_id`,
    `transport_consultant_cd_id`, `last_modified_by`,
    `goods_or_psv`, `lic_no`, `status`,
    `licence_type`, `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`,
    `tot_auth_trailers`, `tot_auth_vehicles`,
    `safety_ins_vehicles`, `safety_ins_trailers`, `safety_ins_varies`,
    `tachograph_ins`, `tachograph_ins_name`, `created_on`, `last_modified_on`, `version`, `expiry_date`, `tot_community_licences`, `translate_to_welsh`)
VALUES
    (7,1,'B','V048', 1,102,NULL,104,NULL,'lcat_gv','OB1234567','lsts_valid','ltyp_si','2010-01-12','2010-01-12','2010-01-12',
    '',4,12,NULL, NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),

    -- Welsh licence, cloned from licence 7
    (70,1,'G','V059', 1,102,NULL,104,NULL,'lcat_gv','OG7654321','lsts_valid','ltyp_si','2010-01-12','2010-01-12','2010-01-12',
    '',4,12,NULL, NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 1),

    -- Clone of licence 7 but linked to an operator which doesn't allow email
    (700,41,'B','V048', 1,102,NULL,104,NULL,'lcat_gv','OB8484848','lsts_valid','ltyp_si','2010-01-12','2010-01-12','2010-01-12',
    '',4,12,NULL, NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),

    -- extra licence for application 1
    (201,1,'B',NULL,0,NULL,NULL,NULL,1,NULL,'OB4234560','lsts_not_submitted',NULL,'2011-03-16','2011-03-16', '2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (202,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB4234561','lsts_consideration','ltyp_si','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (203,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234562','lsts_surrendered','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (204,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB4234563','lsts_unlicenced','ltyp_si','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (205,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234564','lsts_terminated','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (206,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234565','lsts_withdrawn','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (207,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234566','lsts_suspended','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (208,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234567','lsts_curtailed','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',1,3,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (209,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234568','lsts_revoked','ltyp_sn','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),

    -- extra licence for application 3
    (210,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1,NULL, NULL, 0),

    (30,30,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB1234568','lsts_not_submitted','ltyp_si','2011-03-16','2011-03-16','2011-03-16',
    '',3,9,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (41,41,'B',NULL,2,NULL,NULL,NULL,2,'lcat_gv','OB1234577','lsts_not_submitted','ltyp_sn','2007-01-12','2007-01-12','2007-01-12',
    '',1,21,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (54,54,'B',NULL,2,NULL,NULL,NULL,4,'lcat_gv','OB1234578','lsts_not_submitted','ltyp_r','2007-01-12','2007-01-12','2007-01-12',
    '',0,4,NULL,NULL,NULL,NULL, NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (63,63,'D',NULL,4,NULL,NULL,NULL,0,'lcat_psv','PD1234589','lsts_not_submitted','ltyp_sn','2010-01-12','2010-01-12','2010-01-12',
    '',1,7,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (75,75,'D',NULL,4,NULL,NULL,NULL,4,'lcat_psv','PD2737289','lsts_not_submitted','ltyp_sn','2010-01-12','2010-01-12','2010-01-12',
    '',0,4,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL, 0),
    (100,100,'D',NULL,4,NULL,NULL,NULL,0,'lcat_psv','PD1001001','lsts_not_submitted','ltyp_sn','2010-01-12','2010-01-12','2010-01-12',
    '',0,4,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),2, '2016-01-01 10:00:00', NULL, 0),
    (110,1,'D',NULL,4,28,21,21,4,'lcat_psv','PD2737280','lsts_valid','ltyp_r','2010-01-12','2010-01-12','2010-01-12',
    '',0,10,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', 4, 0),
    (114,104,'B',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'OB1534567','lsts_not_submitted',NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1, '2016-01-01 10:00:00', NULL, 0),
    (115,1,'B',NULL,NULL,NULL,NULL,NULL,NULL,'lcat_psv','TS1234568','lsts_valid','ltyp_sr',NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NULL,1, '2016-01-01 10:00:00', NULL, 0);

INSERT INTO `licence_vehicle` (`id`, `licence_id`, `vehicle_id`, `created_by`, `last_modified_by`,
    `specified_date`, `removal_date`, `created_on`,
    `last_modified_on`, `version`, `application_id`, `interim_application_id`) VALUES
    (1,7,1,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,1,2),
    (2,7,2,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,1,2),
    (3,7,3,NULL,4,'2014-02-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,1,NULL),
    (4,7,4,NULL,4,'2013-02-20 00:00:00','2013-03-20 15:40:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (5,30,4,NULL,4,'2013-04-20 00:00:00','2013-05-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (6,41,4,NULL,4,'2013-05-22 00:00:00','2013-06-10 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (7,54,4,NULL,4,'2013-06-20 00:00:00','2013-07-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (8,63,4,NULL,4,'2013-07-24 00:00:00','2013-09-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (9,75,4,NULL,4,'2013-10-20 00:00:00','2013-11-02 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (10,100,4,NULL,4,'2014-11-14 00:00:00','2013-11-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (11,110,4,NULL,4,'2014-11-25 00:00:00','2013-11-26 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (12,114,4,NULL,4,'2014-02-20 00:00:00','2014-05-20 09:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (13,115,4,NULL,4,'2014-06-20 00:00:00',NULL,'2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (14,208,4,NULL,4,'2014-06-20 00:00:00','2010-01-12 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL),
    (15,208,4,NULL,4,'2014-06-20 00:00:00','2010-01-12 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1,NULL,NULL);

-- dates for suspended/curtailed/revoked licences
INSERT INTO `licence_status_rule` (`licence_id`, `licence_status`, `start_date`, `end_date`, `start_processed_date`) VALUES
(207, 'lsts_suspended', '2015-03-01 00:00:00', '2025-02-28  00:00:00', '2015-03-01  01:00:00'),
(208, 'lsts_curtailed', '2015-03-01 00:00:00', '2025-02-28  00:00:00', '2015-03-01  01:00:00'),
(209, 'lsts_revoked', '2015-03-01 00:00:00', null, '2015-03-01  01:00:00');

INSERT INTO `local_authority` (`id`, `description`, `email_address`, `txc_name`, `naptan_code`, `traffic_area_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`) VALUES
  (1,'Aberdeen City Council' ,'terry.valtech+LA1@gmail.com','Aberdeen','639','M',1,1,NULL,NULL,1),
(2,'Aberdeenshire Council','terry.valtech+LA2@gmail.com','Aberdeenshire','630','M',1,1,NULL,NULL,1),
  (3,'Adur District Council','terry.valtech+LA3@gmail.com','','','K',1,1,NULL,NULL,1),
(4,'Argyll & Bute Council','terry.valtech+LA4@gmail.com','ArgyllAndBute','607','M',1,1,NULL,NULL,1),
  (5,'The Isle of Anglesey County Council','terry.valtech+LA5@gmail.com','IsleOfAnglesey','541','G',1,1,NULL,NULL,1),
(6,'Angus Council','terry.valtech+LA6@gmail.com','Angus','649','M',1,1,NULL,NULL,1),
  (7,'AARON ABAMPERES ABALATION','terry.valtech+LA7@gmail.com','','','K',1,1,NULL,NULL,1),
(8,'AARRGH ABACULI ABALATION','terry.valtech+LA8@gmail.com','','','K',1,1,NULL,NULL,1),
  (9,'ABABUA AARDVARK ABACULI ABALATION','terry.valtech+LA9@gmail.com','','','C',1,1,NULL,NULL,1),
(10,'Bath & North East Somerset Council' ,'terry.valtech+LA10@gmail.com','BathAndNorthEastSomerset','018','H',1,1,NULL,NULL,1),
  (11,'Bedfordshire County Council','terry.valtech+LA11@gmail.com','Bedfordshire','','F',1,1,NULL,NULL,1),
(12,'Blackburn with Darwen Borough Council','terry.valtech+LA12@gmail.com','BlackburnWithdarwen','258','C',1,1,NULL,NULL,1),
  (13,' Blackpool Borough Council','terry.valtech+LA13@gmail.com','Blackpool','259','C',1,1,NULL,NULL,1),
(14,'ABACTION ABATED ABACULI ABALATION','terry.valtech+LA14@gmail.com','','','C',1,1,NULL,NULL,1),
  (15,'Bournemouth Borough Council','terry.valtech+LA15@gmail.com','Bournemouth','129','H',1,1,NULL,NULL,1),
(16,'Bracknell Forest Borough Council','terry.valtech+LA16@gmail.com','BracknellForest','038','H',1,1,NULL,NULL,1),
  (17,'Bridgend County Borough Council','terry.valtech+LA17@gmail.com','Bridgend','551','G',1,1,NULL,NULL,1),
(18,'Brighton & Hove City Council','terry.valtech+LA18@gmail.com','BrightonAndHove','149','K',1,1,NULL,NULL,1),
  (19,' Bristol City Council','terry.valtech+LA19@gmail.com','Bristol','010','H',1,1,NULL,NULL,1),
(21,'ABADDON ABACULI ABALATION','terry.valtech+LA21@gmail.com','','','C',1,1,NULL,NULL,1),
  (22,'ABADEJO ABATED ABACULI ABALATION','terry.valtech+LA22@gmail.com','','','C',1,1,NULL,NULL,1),
(23,'Caerphilly County Borough Council','terry.valtech+LA23@gmail.com','Caerphilly','554','G',1,1,NULL,NULL,1),
  (25,'ABAFT ABAISSE ABALATION','terry.valtech+LA25@gmail.com','','','K',1,1,NULL,NULL,1),
(26,'Cardiff County Council','terry.valtech+LA26@gmail.com','Cardiff','571','G',1,1,NULL,NULL,1),
  (27,'Carmarthenshire Council','terry.valtech+LA27@gmail.com','Carmarthenshire','522','G',1,1,NULL,NULL,1),
(28,'CENTRO','terry.valtech+LA28@gmail.com','CentroWestMidlands','430','D',1,1,NULL,NULL,1),
  (29,' Ceredigion County Council','terry.valtech+LA29@gmail.com','Ceredigion','523','G',1,1,NULL,NULL,1),
(30,'Cheshire County Council','terry.valtech+LA30@gmail.com','Cheshire','060','C',1,1,NULL,NULL,1),
  (31,'ABAISER ABAISSE ABALATION','terry.valtech+LA31@gmail.com','','','C',1,1,NULL,NULL,1),
(32,'Clackmannan Council','terry.valtech+LA32@gmail.com','Clackmannanshire','668','M',1,1,NULL,NULL,1),
  (33,'Conwy County Borough Council','terry.valtech+LA33@gmail.com','Conwy','513','G',1,1,NULL,NULL,1),
(34,'CornwallAndScillies','terry.valtech+LA34@gmail.com','CornwallAndScillies','080','H',1,1,NULL,NULL,1),
  (35,'Cumbria County Council','terry.valtech+LA35@gmail.com','Cumbria','090','C',1,1,NULL,NULL,1),
(36,'Darlington Borough Council','terry.valtech+LA36@gmail.com','Darlington','076','B',1,1,NULL,NULL,1),
  (37,'Denbighshire Council','terry.valtech+LA37@gmail.com','Denbighshire','511','G',1,1,NULL,NULL,1),
(38,'Derbyshire County Council','terry.valtech+LA38@gmail.com','Derbyshire','100','C',1,1,NULL,NULL,1),
  (39,'Devon','terry.valtech+LA39@gmail.com','Devon','110','H',1,1,NULL,NULL,1),
(40,'Dorset County Council','terry.valtech+LA40@gmail.com','Dorset','120','H',1,1,NULL,NULL,1),
  (41,'Dumfries & Galloway Council','terry.valtech+LA41@gmail.com','DumfriesAndGalloway','680','M',1,1,NULL,NULL,1),
(42,'Dundee City Council','terry.valtech+LA42@gmail.com','Dundee','640','M',1,1,NULL,NULL,1),
  (43,'Durham City Council','terry.valtech+LA43@gmail.com','','999','B',1,1,NULL,NULL,1),
(44,'Durham County Council','terry.valtech+LA44@gmail.com','Durham','130','B',1,1,NULL,NULL,1),
  (45,'East Ayrshire Council','terry.valtech+LA45@gmail.com','EastAyrshire','618','M',1,1,NULL,NULL,1),
(46,'East Dunbartonshire Council','terry.valtech+LA46@gmail.com','EastDunbartonshire','611','M',1,1,NULL,NULL,1),
  (47,'ABANDONED ABARAMBO ABBOCCATO ABALATION','terry.valtech+LA47@gmail.com','','','K',1,1,NULL,NULL,1),
(48,'East Lothian Council','terry.valtech+LA48@gmail.com','EastLothian','627','M',1,1,NULL,NULL,1),
  (49,'East Renfrewshire Council','terry.valtech+LA49@gmail.com','EastRenfrewshire','612','M',1,1,NULL,NULL,1),
(50,'East Riding of Yorkshire Council','terry.valtech+LA50@gmail.com','EastRidingOfYorkshire','220','B',1,1,NULL,NULL,1),
  (51,'East Sussex County Council','terry.valtech+LA51@gmail.com','EastSussex','140','K',1,1,NULL,NULL,1),
(52,'The City of Edinburgh Council','terry.valtech+LA52@gmail.com','Edinburgh','620','M',1,1,NULL,NULL,1),
  (53,'Epsom & Ewell Borough Council','terry.valtech+LA53@gmail.com','','','K',1,1,NULL,NULL,1),
(54,'Essex County Council','terry.valtech+LA54@gmail.com','Essex','150','F',1,1,NULL,NULL,1),
  (55,'ABANDONMENT ABAISSE ABALATION','terry.valtech+LA55@gmail.com','','','H',1,1,NULL,NULL,1),
(56,'Falkirk District Council','terry.valtech+LA56@gmail.com','Falkirk','669','M',1,1,NULL,NULL,1),
  (57,'Fife Council','terry.valtech+LA57@gmail.com','Fife','650','M',1,1,NULL,NULL,1),
(58,'Flintshire County Council','terry.valtech+LA58@gmail.com','Flintshire','512','G',1,1,NULL,NULL,1),
  (59,'ABANIC ABACULI ABALATION','terry.valtech+LA59@gmail.com','','','C',1,1,NULL,NULL,1),
(60,'Glasgow City Council','terry.valtech+LA60@gmail.com','Glasgow','609','M',1,1,NULL,NULL,1),
  (61,'Gloucestershire County Council','terry.valtech+LA61@gmail.com','Gloucestershire','160','H',1,1,NULL,NULL,1),
(62,'Greater Manchester pte','terry.valtech+LA62@gmail.com','GMPTE','180','C',1,1,NULL,NULL,1),
  (63,'ABARBAREA ABACULI ABALATION','terry.valtech+LA63@gmail.com','','','K',1,1,NULL,NULL,1),
(64,'ABARIS ABASHEDLY ABATTOIRS ABBOT ABBOTNULLIUS','terry.valtech+LA64@gmail.com','','','G',1,1,NULL,NULL,1),
  (65,'Gwynedd County Council','terry.valtech+LA65@gmail.com','Gwynedd','540','G',1,1,NULL,NULL,1),
(66,'Hampshire Council','terry.valtech+LA66@gmail.com','Hampshire','190','H',1,1,NULL,NULL,1),
  (67,'Hartlepool Borough Council','terry.valtech+LA67@gmail.com','Hartlepool','075','B',1,1,NULL,NULL,1),
(68,'ABASE ABACULI ABALATION','terry.valtech+LA68@gmail.com','','','K',1,1,NULL,NULL,1),
  (69,'Herefordshire Council','terry.valtech+LA69@gmail.com','Herefordshire','209','D',1,1,NULL,NULL,1),
(70,'Hertfordshire County Council','terry.valtech+LA70@gmail.com','Hertfordshire','210','F',1,1,NULL,NULL,1),
  (71,'Highland Council','terry.valtech+LA71@gmail.com','Highland','670','M',1,1,NULL,NULL,1),
(72,'ABASERS ABACULI ABALATION','terry.valtech+LA72@gmail.com','','','C',1,1,NULL,NULL,1),
  (73,'Inverclyde Council','terry.valtech+LA73@gmail.com','Inverclyde','613','M',1,1,NULL,NULL,1),
(74,'Isle of Wight Council','terry.valtech+LA74@gmail.com','IsleOfWight','230','H',1,1,NULL,NULL,1),
  (75,'Kent County Council','terry.valtech+LA75@gmail.com','Kent','240','K',1,1,NULL,NULL,1),
(76,'Lancashire County Council','terry.valtech+LA76@gmail.com','Lancashire','250','C',1,1,NULL,NULL,1),
  (77,'Leicestershire County Council','terry.valtech+LA77@gmail.com','Leicestershire','260','F',1,1,NULL,NULL,1),
(78,'ABASIAS ABAMPERES ABALATION','terry.valtech+LA78@gmail.com','','','K',1,1,NULL,NULL,1),
  (79,'Lincolnshire County Council','terry.valtech+LA79@gmail.com','Lincolnshire','270','F',1,1,NULL,NULL,1),
(80,'ABASING ABAISSE ABALATION','terry.valtech+LA80@gmail.com','','','C',1,1,NULL,NULL,1),
  (81,'Luton Borough Council','terry.valtech+LA81@gmail.com','Luton','29','F',1,1,NULL,NULL,1),
(82,'Medway Council','terry.valtech+LA82@gmail.com','Medway','249','K',1,1,NULL,NULL,1),
  (83,'Merseytravel pte','terry.valtech+LA83@gmail.com','Merseytravel','280','C',1,1,NULL,NULL,1),
(84,'ABATEMENT ABBESS ABAMPERES ABALATION','terry.valtech+LA84@gmail.com','','','K',1,1,NULL,NULL,1),
  (85,'Middlesbrough Borough Council','terry.valtech+LA85@gmail.com','Middlesbrough','079','C',1,1,NULL,NULL,1),
(86,'Midlothian Council','terry.valtech+LA86@gmail.com','Midlothian','628','M',1,1,NULL,NULL,1),
  (87,'Milton Keynes Council','terry.valtech+LA87@gmail.com','MiltonKeynes','049','F',1,1,NULL,NULL,1),
(88,'ABATES ABBOTS ABAMPERES ABALATION','terry.valtech+LA88@gmail.com','','','K',1,1,NULL,NULL,1),
  (89,'Moray Council','terry.valtech+LA89@gmail.com','Moray','638','M',1,1,NULL,NULL,1),
(91,'ABATISED ABAISSE ABALATION','terry.valtech+LA91@gmail.com','','','C',1,1,NULL,NULL,1),
  (92,' Nexus (Tyne & Wear)','terry.valtech+LA92@gmail.com','NexusTynesde','410','B',1,1,NULL,NULL,1),
(93,'North Ayrshire Council','terry.valtech+LA93@gmail.com','NorthAyrshire','617','M',1,1,NULL,NULL,1),
  (94,'North East Lincolnshire Council','terry.valtech+LA94@gmail.com','NorthEastLincolnshire','228','B',1,1,NULL,NULL,1),
(95,'North Lanarkshire Council','terry.valtech+LA95@gmail.com','NorthLanarkshire','616','M',1,1,NULL,NULL,1),
  (96,'North Somerset Council','terry.valtech+LA96@gmail.com','NorthSomerset','019','H',1,1,NULL,NULL,1),
(97,'North Yorkshire County Council','terry.valtech+LA97@gmail.com','NorthYorkshire','320','B',1,1,NULL,NULL,1),
  (98,'Northamptonshire County Council','terry.valtech+LA98@gmail.com','Northamptonshire','300','F',1,1,NULL,NULL,1),
(99,'Norfolk County Council','terry.valtech+LA99@gmail.com','Norfolk','290','F',1,1,NULL,NULL,1),
  (100,'Northumberland County Council','terry.valtech+LA100@gmail.com','Northumberland','310','B',1,1,NULL,NULL,1),
(101,'Nottingham City Council','terry.valtech+LA101@gmail.com','Nottingham','339','B',1,1,NULL,NULL,1),
  (102,'Nottinghamshire County Council','terry.valtech+LA102@gmail.com','Nottinghamshire','330','B',1,1,NULL,NULL,1),
(103,'Orkney Islands Council','terry.valtech+LA103@gmail.com','OrkneyIslands','602','M',1,1,NULL,NULL,1),
  (104,'Oxfordshire County Council','terry.valtech+LA104@gmail.com','Oxfordshire','340','H',1,1,NULL,NULL,1),
(105,' Pembrokeshire Council','terry.valtech+LA105@gmail.com','Pembrokeshire','521','G',1,1,NULL,NULL,1),
  (106,'Perth & Kinross Council','terry.valtech+LA106@gmail.com','PerthAndKinross','648','M',1,1,NULL,NULL,1),
(107,'Peterborough City Council','terry.valtech+LA107@gmail.com','Peterborough','059','F',1,1,NULL,NULL,1),
  (109,'Plymouth City Council','terry.valtech+LA109@gmail.com','Plymouth','118','H',1,1,NULL,NULL,1),
(110,'Portsmouth City Council','terry.valtech+LA110@gmail.com','Portsmouth','199','H',1,1,NULL,NULL,1),
  (111,'Powys County Council','terry.valtech+LA111@gmail.com','Powys','561','G',1,1,NULL,NULL,1),
(112,'Reading Borough Council','terry.valtech+LA112@gmail.com','Reading','039','H',1,1,NULL,NULL,1),
  (113,'Renfrewshire Council','terry.valtech+LA113@gmail.com','Renfrewshire','614','M',1,1,NULL,NULL,1),
(114,'Rhondda Cynon Taff Council','terry.valtech+LA114@gmail.com','RhonddaCynonTaff','552','G',1,1,NULL,NULL,1),
  (115,'Rutland County Council','terry.valtech+LA115@gmail.com','Rutland','268','F',1,1,NULL,NULL,1),
(116,'Scottish Borders Council','terry.valtech+LA116@gmail.com','ScottishBorders','690','M',1,1,NULL,NULL,1),
  (117,'Shetland Islands Council','terry.valtech+LA117@gmail.com','ShetlandIslands','603','M',1,1,NULL,NULL,1),
(118,'Shropshire County Council','terry.valtech+LA118@gmail.com','Shropshire','350','D',1,1,NULL,NULL,1),
  (119,'Slough Borough Council','terry.valtech+LA119@gmail.com','Slough','037','H',1,1,NULL,NULL,1),
(120,'Somerset County Council','terry.valtech+LA120@gmail.com','Somerset','360','H',1,1,NULL,NULL,1),
  (121,'South Ayrshire Council','terry.valtech+LA121@gmail.com','SouthAyrshire','619','M',1,1,NULL,NULL,1),
(122,'South Gloucestershire Council','terry.valtech+LA122@gmail.com','SouthGloucestershire','017','H',1,1,NULL,NULL,1),
  (123,'South Lanarkshire Council','terry.valtech+LA123@gmail.com','SouthLanarkshire','615','M',1,1,NULL,NULL,1),
(124,'South Yorkshire pte','terry.valtech+LA124@gmail.com','SouthYorkshirePTE','370','B',1,1,NULL,NULL,1),
  (125,'Southampton City Council','terry.valtech+LA125@gmail.com','Southampton','198','H',1,1,NULL,NULL,1),
(126,'Southend-on-Sea Borough Council','terry.valtech+LA126@gmail.com','SouthendOnSea','158','F',1,1,NULL,NULL,1),
  (127,'Staffordshire County Council','terry.valtech+LA127@gmail.com','Staffordshire','380','D',1,1,NULL,NULL,1),
(128,' Stirling Council','terry.valtech+LA128@gmail.com','Stirling','660','M',1,1,NULL,NULL,1),
  (129,'ABBATIAL ABATED ABACULI ABALATION','terry.valtech+LA129@gmail.com','','','C',1,1,NULL,NULL,1),
(130,'City of Stoke-on-Trent Council','terry.valtech+LA130@gmail.com','StokeOnTrent','389','D',1,1,NULL,NULL,1),
  (131,'Strathclyde Partnership for Transport','terry.valtech+LA131@gmail.com','StrathclydePTE','610','M',1,1,NULL,NULL,1),
(132,'Suffolk County Council','terry.valtech+LA132@gmail.com','Suffolk','390','F',1,1,NULL,NULL,1),
  (133,'Surrey County Council','terry.valtech+LA133@gmail.com','Surrey','400','K',1,1,NULL,NULL,1),
(135,'City & County of Swansea','terry.valtech+LA135@gmail.com','Swansea','581','G',1,1,NULL,NULL,1),
  (136,'Swindon Borough Council','terry.valtech+LA136@gmail.com','Swindon','468','H',1,1,NULL,NULL,1),
(137,'Telford & Wrekin Council','terry.valtech+LA137@gmail.com','TelfordAndWrekin','359','D',1,1,NULL,NULL,1),
  (138,'Thurrock Council','terry.valtech+LA138@gmail.com','Thurrock','159','F',1,1,NULL,NULL,1),
(139,'ABBOTCIES ABBOTSHIPS ABACULI ABALATION','terry.valtech+LA139@gmail.com','','','K',1,1,NULL,NULL,1),
  (140,'Torbay Borough Council','terry.valtech+LA140@gmail.com','Torbay','119','H',1,1,NULL,NULL,1),
(141,'ABBOTCIES ABBOTSHIPS ABACULI ABALATION','terry.valtech+LA141@gmail.com','','','K',1,1,NULL,NULL,1),
  (142,'Vale of Glamorgan Council','terry.valtech+LA142@gmail.com','ValeOfGlamorgan','572','G',1,1,NULL,NULL,1),
(143,'Warwickshire County Council','terry.valtech+LA143@gmail.com','Warwickshire','420','D',1,1,NULL,NULL,1),
  (144,'West Berkshire District Council','terry.valtech+LA144@gmail.com','WestBerkshire','030','H',1,1,NULL,NULL,1),
(145,'West Dunbartonshire Council','terry.valtech+LA145@gmail.com','WestDunbartonshire','608','M',1,1,NULL,NULL,1),
  (146,'West Lothian Council','terry.valtech+LA146@gmail.com','WestLothian','629','M',1,1,NULL,NULL,1),
(147,'ABBOTSON ABBREVIATED ABBREVIATIONS','terry.valtech+LA147@gmail.com','','','B',1,1,NULL,NULL,1),
  (148,'ABBOTSUN ABASHED ABALATION','terry.valtech+LA148@gmail.com','','','M',1,1,NULL,NULL,1),
(149,'ABBOTT AABERG ABAXIAL ABACULI ABALATION','terry.valtech+LA149@gmail.com','','','H',1,1,NULL,NULL,1),
  (150,'Wiltshire County Council','terry.valtech+LA150@gmail.com','Wiltshire','460','H',1,1,NULL,NULL,1),
(151,'Windsor & Maidenhead Borough Council','terry.valtech+LA151@gmail.com','WindsorAndMaidenhead','036','H',1,1,NULL,NULL,1),
  (152,'Wokingham District Council','terry.valtech+LA152@gmail.com','Wokingham','035','H',1,1,NULL,NULL,1),
(153,'Worcestershire County Council','terry.valtech+LA153@gmail.com','Worcestershire','200','D',1,1,NULL,NULL,1),
  (154,'Wrexham County Borough Council','terry.valtech+LA154@gmail.com','Wrexham','514','G',1,1,NULL,NULL,1),
(155,'York City Council','terry.valtech+LA155@gmail.com','York','329','B',1,1,NULL,NULL,1),
  (156,'West Sussex County Council','terry.valtech+LA156@gmail.com','WestSussex','440','K',1,1,NULL,NULL,1),
(157,'Halton Borough Council','terry.valtech+LA157@gmail.com','Halton','068','K',1,1,NULL,NULL,1),
  (158,' Warrington Borough Council','terry.valtech+LA158@gmail.com','Warrington','069','F',1,1,NULL,NULL,1),
(159,'Redcar & Cleveland Borough Council','terry.valtech+LA159@gmail.com','RedcarAndCleveland','078','F',1,1,NULL,NULL,1),
  (160,'Neath Port Talbot Borough Council','terry.valtech+LA160@gmail.com','NeathPortTalbot','582','C',1,1,NULL,NULL,1),
(161,'Poole Borough Council','terry.valtech+LA161@gmail.com','Poole','128','C',1,1,NULL,NULL,1),
  (162,'Merthyr Tydfil','terry.valtech+LA162@gmail.com','MerthyrTydfil','553','B',1,1,NULL,NULL,1),
(163,'North Lincolnshire Council','terry.valtech+LA163@gmail.com','NorthLincolnshire','227','G',1,1,NULL,NULL,1),
  (164,'Transport For London','terry.valtech+LA164@gmail.com','London','490','B',1,1,NULL,NULL,1),
(165,'Buckinghamshire County Council','terry.valtech+LA165@gmail.com','Buckinghamshire','040','H',1,1,NULL,NULL,1),
  (166,'Cambridgeshire County Council','terry.valtech+LA166@gmail.com','Cambridgeshire','050','G',1,1,NULL,NULL,1),
(167,'Kingston upon Hull','terry.valtech+LA167@gmail.com','KingstonUponHull','229','B',1,1,NULL,NULL,1),
  (168,'Cheshire West and Chester Council','terry.valtech+LA168@gmail.com','CheshireWestAndChester','061','B',1,1,NULL,NULL,1),
(169,'Stockton On Tees County Council','terry.valtech+LA169@gmail.com','StocktonOnTees','077','C',1,1,NULL,NULL,1),
  (170,'Derby City Council','terry.valtech+LA170@gmail.com','Derby','109','F',1,1,NULL,NULL,1),
(171,' Leicester City Council','terry.valtech+LA171@gmail.com','Leicester','269','M',1,1,NULL,NULL,1),
  (172,'Comhairle nan Eilean Siar Council','terry.valtech+LA172@gmail.com','ComhairleNanEileanSiar','601','F',1,1,NULL,NULL,1),
(173,'Havering Council','terry.valtech+LA173@gmail.com','Havering','157','G',1,1,NULL,NULL,1),
  (174,'Blaenau Gwent Council','terry.valtech+LA174@gmail.com','BlaenauGwent','532','G',1,1,NULL,NULL,1),
(175,'Newport Council','terry.valtech+LA175@gmail.com','Newport','531','G',1,1,NULL,NULL,1),
  (176,'Torfaen Council','terry.valtech+LA176@gmail.com','Torfaen','534','G',1,1,NULL,NULL,1),
(177,'Monmouthshire Council','terry.valtech+LA177@gmail.com','Monmouthshire','533','C',1,1,NULL,NULL,1),
  (178,'Metro','terry.valtech+LA178@gmail.com','Metro','450','H',1,1,NULL,NULL,1),
(179,'Channel Islands','terry.valtech+LA179@gmail.com','ChannelIslands','710','B',1,1,NULL,NULL,1),
  (180,'Isle of Man','terry.valtech+LA180@gmail.com','IsleOfMan','720','B',1,1,NULL,NULL,1),
(189,'Translink','terry.valtech+LA189@gmail.com','Translink','700','C',1,1,NULL,NULL,1),
  (190,'Cheshire East Council','terry.valtech+LA190@gmail.com','CheshireEast','060','C',1,1,NULL,NULL,1),
(191,'Bedford County Council','terry.valtech+LA191@gmail.com','Bedford','020','F',1,1,NULL,NULL,1),
  (192,'Central Bedfordshire County Council','terry.valtech+LA192@gmail.com','CentralBedfordshire','021','F',1,1,NULL,NULL,1);

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

INSERT INTO `note` (
  `id`,
  `note_type`,
  `bus_reg_id`,
  `created_by`,
  `licence_id`,
  `case_id`,
  `application_id`,
  `transport_manager_id`,
  `organisation_id`,
  `comment`,
  `priority`,
  `created_on`,
  `version`
)
VALUES
(1,  'note_t_app',  NULL, 2, 7,   28, 1,    NULL, NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(2,  'note_t_lic',  NULL, 4, 7,   28, NULL, NULL, NULL, 'This is a licence note', 1, '2011-10-03 00:00:00', 1),
(3,  'note_t_app',  NULL, 2, 7,   28, 1,    NULL, NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(4,  'note_t_app',  NULL, 3, 7,   28, 1,    NULL, NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(5,  'note_t_lic',  NULL, 5, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-03 00:00:00', 1),
(6,  'note_t_case', NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a case note',    0, '2011-10-03 00:00:00', 1),
(7,  'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', 1),
(8,  'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2012-10-10 00:00:00', 1),
(9,  'note_t_bus',  1,    3, 110, 75, NULL, NULL, NULL, 'This is a bus reg note', 0, '2012-10-10 00:00:00', 1),
(10, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', 1),
(11, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-13 00:00:00', 1),
(12, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-15 00:00:00', 1),
(13, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, NULL, 'This is a licence note', 0, '2011-10-12 00:00:00', 1),
(14, 'note_t_tm',   NULL, 3,NULL,NULL,NULL, 3,    NULL, 'This is a TM note',      0, '2011-10-12 00:00:00', 1),
(15, 'note_t_org',  NULL, 3,NULL,NULL,NULL, NULL, 101,  'This is a organisation note', 0, '2011-10-12 00:00:00', 1);


INSERT INTO `operating_centre` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `address_id`) VALUES
    (16,3,2,NOW(),NOW(),1,1008),
    (21,1,3,NOW(),NOW(),1,1021),
    (37,2,1,NOW(),NOW(),1,1037),
    (39,1,3,NOW(),NOW(),1,1039),
    (48,1,3,NOW(),NOW(),1,1029),
    (67,0,1,NOW(),NOW(),1,1067),
    (72,1,4,NOW(),NOW(),1,1072);

INSERT INTO `opposer`
(`id`, `opposer_type`, `last_modified_by`, `created_by`, `contact_details_id`, `created_on`, `last_modified_on`,
 `version`)
VALUES
  (1, 'obj_t_local_auth', 1, 1, 8, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
  (2, 'obj_t_police', 1, 1, 8, '2014-02-21 00:00:00', '2014-02-21 00:00:00', 1);

INSERT INTO `opposition`
(`id`, `opposition_type`, `case_id`, `opposer_id`, `last_modified_by`, `created_by`, `is_copied`,
 `raised_date`, `is_in_time`, `is_public_inquiry`, `is_withdrawn`, `is_valid`, `valid_notes`, `notes`, `deleted_date`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 'otf_eob', 29, 1, 1, 1, 1, '2014-02-19', 1, 1, 0, 'opp_v_no', 'Valid notes', 'Notes', null,
  '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1),
  (2, 'otf_rep', 29, 1, 1, 1, 1, '2014-02-19', 0, 0, 1, 'opp_v_yes', 'Valid notes', 'Notes', null,
  '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1);

INSERT INTO `opposition_grounds`
(`opposition_id`, `ground_id`)
VALUES
  (1, 'ogf_env'),
  (1, 'ogf_parking'),
  (2, 'ogf_safety'),
  (2, 'ogf_size');

INSERT INTO `operating_centre_opposition`
(`opposition_id`, `operating_centre_id`)
VALUES
  (1, 16),
  (2, 16);

INSERT INTO `organisation` (`id`,`lead_tc_area_id`, `created_by`, `last_modified_by`,`contact_details_id`,`irfo_contact_details_id`,
  `cpid`, `company_or_llp_no`, `name`, `is_irfo`, `type`, `created_on`, `last_modified_on`, `version`, `allow_email`,
  `nature_of_business`) VALUES
    (1,'B',1,3,  21, NULL, NULL, '12345678','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1,1,'Haulage'),
    (30,'C',1,4, 30, NULL, NULL, '98765432','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1,0,NULL),
    (41,'D',0,4, 41, NULL, NULL, '24134123','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1,0,NULL),
    (54,'F',3,4, 54, NULL, NULL, '67567533','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1,0,NULL),
    (63,'G',1,2, 63, NULL, NULL, '35345645','Leeds bus service ltd.',0,'org_t_rc',NOW(),NOW(),1,0,NULL),
    (75,'H',1,0, 75, NULL, NULL, '12345A11','Leeds city council',0,'org_t_pa',NOW(),NOW(),1,0,NULL),
    (100,'K',1,3,100, NULL, NULL, '100100','Test partnership',0,'org_t_p','2014-01-28 16:25:35','2014-01-28 16:25:35',2,0,NULL),
    (101,'K',1,3,100, 102, NULL, '100100','Test IRFO',1,'org_t_ir',NOW(),NOW(),1,0,NULL),
    (104,'M',NULL,NULL,NULL, NULL, NULL, '1234567','Company Name',0,'org_t_rc',NULL,NULL,1,0,NULL),
    (105,'N',1,3,NULL, NULL, NULL, NULL,'SR Organisation',0,'org_t_rc',NOW(),NOW(),1,0,NULL);

INSERT INTO `organisation_person` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `person_id`, `organisation_id`) VALUES
    (1,NULL,NULL,NULL,NULL,1,78,7),
    (2,NULL,NULL,NULL,NULL,1,77,7),
    (3,NULL,NULL,NULL,NULL,1,77,1),
    (4,NULL,NULL,NULL,NULL,1,78,1),
    (5,NULL,NULL,NULL,NULL,1,78,100),
    (6,NULL,NULL,NULL,NULL,1,77,100),
    (7,NULL,NULL,NULL,NULL,1,11,75),
    (8,NULL,NULL,NULL,NULL,1,32,75);

INSERT INTO `person` (`id`, `created_by`, `last_modified_by`, `birth_place`, `title`, `birth_date`, `forename`,
`family_name`, `other_name`, `created_on`, `last_modified_on`, `version`, `deleted_date`) VALUES
    (4,NULL,NULL,'Aldershot','title_mr','1960-02-01 00:00:00','Terry','Barret-Edgecombe',NULL,NULL,NULL,1,NULL),
    (8,NULL,NULL,'Birmingham','title_mr','1960-02-01 00:00:00','Simon','Fish',NULL,NULL,NULL,1,NULL),
    (9,NULL,NULL,'Cheltenham','title_mr','1960-02-15 00:00:00','John','Smith',NULL,NULL,NULL,1,NULL),
    (10,NULL,NULL,'Darlington','title_mr','1965-07-12 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (11,NULL,NULL,'Edinburgh','title_mr','1970-04-14 00:00:00','Joe','Lambert',NULL,NULL,NULL,1,NULL),
    (12,NULL,NULL,'Farnham','title_mr','1975-04-15 00:00:00','Tom','Cooper',NULL,NULL,NULL,1,NULL),
    (13,NULL,NULL,'Godmanchester','title_mr','1973-03-03 00:00:00','Mark','Anthony',NULL,NULL,NULL,1,NULL),
    (14,NULL,NULL,'Hereford','title_mr','1975-02-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (15,NULL,NULL,'Isle of Wight','title_mr','1973-12-09 00:00:00','Tom','Anthony',NULL,NULL,NULL,1,NULL),
    (32,NULL,NULL,'Jamaica','title_mr','1960-04-15 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (33,NULL,NULL,'Kiddiminster','title_mr','1965-04-12 00:00:00','Mark','Jones',NULL,NULL,NULL,1,NULL),
    (34,NULL,NULL,'London','title_mr','1970-06-14 00:00:00','Tim','Lambert',NULL,NULL,NULL,1,NULL),
    (35,NULL,NULL,'Manchester','title_mr','1975-04-18 00:00:00','Joe','Cooper',NULL,NULL,NULL,1,NULL),
    (43,NULL,NULL,'Newcastle','title_mr','1960-02-15 00:00:00','Ted','Smith',NULL,NULL,NULL,1,NULL),
    (44,NULL,NULL,'Otley','title_mr','1970-04-14 00:00:00','Peter','Lambert',NULL,NULL,NULL,1,NULL),
    (45,NULL,NULL,'Peterborough','title_mr','1975-04-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (46,NULL,NULL,'Quatar','title_mr','1973-03-03 00:00:00','David','Anthony',NULL,NULL,NULL,1,NULL),
    (47,NULL,NULL,'Rotherham','title_mr','1975-02-15 00:00:00','Lewis','Howarth',NULL,NULL,NULL,1,NULL),
    (59,NULL,NULL,'Swansea','title_mr','1973-03-03 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (60,NULL,NULL,'Tadcaster','title_mr','1975-02-15 00:00:00','Lewis','Hamilton',NULL,NULL,NULL,1,NULL),
    (65,NULL,NULL,'Upminster','title_mr','1972-02-15 00:00:00','Jonathan','Smith',NULL,NULL,NULL,1,NULL),
    (66,NULL,NULL,'Victoria','title_mr','1975-03-15 00:00:00','Tim','Cooper',NULL,NULL,NULL,1,NULL),
    (77,NULL,NULL,'Leeds','title_mr','1972-02-15 00:00:00','Tom','Jones',NULL,NULL,NULL,1,NULL),
    (78,NULL,NULL,'Xanten','title_mr','1975-03-15 00:00:00','Keith','Winnard',NULL,NULL,NULL,1,NULL),
    (79,NULL,NULL,'York','title_mr','1975-04-15 00:00:00','James','Bond',NULL,NULL,NULL,1,NULL),
    (80,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Dave','Smith',NULL,NULL,NULL,1,NULL),
    (81,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','John','Spellman',NULL,NULL,NULL,1,NULL),
    (82,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Steve','Fox',NULL,NULL,NULL,1,NULL),
    (83,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Amy','Wrigg',NULL,NULL,NULL,1,NULL),
    (84,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Phil','Jowitt',NULL,NULL,NULL,1,NULL),
    (85,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Kevin','Rooney',NULL,NULL,NULL,1,NULL),
    (86,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Sarah','Thompson',NULL,NULL,NULL,1,NULL),
    (87,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Tom','Smith',NULL,NULL,NULL,1,NULL),
    (90,1,1,'Aldershot','title_mr','1960-02-01 00:00:00','ABDOU','BONOMI',NULL,NULL,NULL,1,NULL),

    (91,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Kirstie','Brown',NULL,NULL,NULL,1,NULL),
    (92,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Dianne','Craven',NULL,NULL,NULL,1,NULL),
    (93,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Neil','Chivers',NULL,NULL,NULL,1,NULL),
    (94,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Shakil','Ahmed',NULL,NULL,NULL,1,NULL),
    (95,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Rachael','Evans',NULL,NULL,NULL,1,NULL),
    (96,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Nicola','Field',NULL,NULL,NULL,1,NULL),
    (97,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Julie','Goward',NULL,NULL,NULL,1,NULL),
    (98,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Catherine','Tobin',NULL,NULL,NULL,1,NULL),
    (99,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Philip','Stagg',NULL,NULL,NULL,1,NULL),
    (100,NULL,NULL,'Zurich','title_mrs','1975-04-15 00:00:00','Carole','Ryalls',NULL,NULL,NULL,1,NULL),
    (101,NULL,NULL,'Zurich','title_mr','1975-04-15 00:00:00','Steven','Jones',NULL,NULL,NULL,1,NULL);

INSERT INTO `disqualification` (
    `id`, `created_by`, `last_modified_by`, `is_disqualified`, `period`,
    `notes`, `created_on`, `last_modified_on`, `version`, `person_id`
) VALUES
    (10,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (13,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (15,NULL,NULL,1,6,'TBC',NOW(),NULL,1,NULL),
    (32,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (36,NULL,NULL,1,6,'TBC',NOW(),NULL,1,NULL);

INSERT INTO `pi` (`id`,`agreed_by_tc_id`,`agreed_by_tc_role`,`assigned_to`,`decided_by_tc_id`,`decided_by_tc_role`,
  `pi_status`,`written_outcome`,`case_id`,`created_by`,`last_modified_by`,`brief_to_tc_date`,`call_up_letter_date`,
  `written_decision_letter_date`,`decision_letter_sent_date`,`decision_notes`,`licence_curtailed_at_pi`,
  `licence_revoked_at_pi`,`licence_suspended_at_pi`,`notification_date`,`tc_written_decision_date`,
  `tc_written_reason_date`,`written_reason_date`,`written_reason_letter_date`,`agreed_date`,`closed_date`,`comment`,
  `created_on`,`decision_date`,`deleted_date`,`is_cancelled`,`last_modified_on`,`version`,`witnesses`)
VALUES
  (1,2,'tc_r_dtc',NULL,2,'tc_r_dhtru','pi_s_reg',NULL,24,NULL,NULL,NULL,NULL,NULL,NULL,
   'S13 - Consideration of new application under Section 13',0,0,0,NULL,NULL,NULL,NULL,NULL,'2014-11-24',NULL,
   'Test Pi','2014-11-24 10:06:49',NULL,NULL,0,'2014-12-11 10:49:57',2,0),
   (2,2,'tc_r_dtc',NULL,2,'tc_r_dhtru','pi_s_reg',NULL,84,NULL,NULL,NULL,NULL,NULL,NULL,
   'S13 - Consideration of new application under Section 13',0,0,0,NULL,NULL,NULL,NULL,NULL,'2014-11-24',NULL,
   'Test Pi','2014-11-24 10:06:49',NULL,NULL,0,'2014-12-11 10:49:57',2,0);

INSERT INTO `pi_hearing` (`id`,`pi_id`,`presided_by_role`,`created_by`,`last_modified_by`,`venue_id`,`presiding_tc_id`,`adjourned_date`,`adjourned_reason`,`cancelled_date`,`cancelled_reason`,`details`,`is_adjourned`,`presiding_tc_other`,`created_on`,`hearing_date`,`is_cancelled`,`last_modified_on`,`venue_other`,`version`,`witnesses`)
  VALUES
    (1,1,'tc_r_htru',NULL,NULL,1,1,'2014-03-16 11:30:00','adjourned reason',NULL,NULL,'S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',1,NULL,'2014-11-24 10:22:24','2014-03-16 14:30:00',0,NULL,NULL,1,9),
    (2,1,'tc_r_htru',NULL,NULL,1,1,NULL,NULL,NULL,NULL,'S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',0,NULL,'2014-11-24 10:22:24','2014-04-05 14:30:00',0,NULL,NULL,1,9),
    (3,2,'tc_r_htru',NULL,NULL,1,1,NULL,NULL,NULL,NULL,'S23 - Consider attaching conditions under Section 23\r\nS23 -
     Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',0,NULL,'2014-11-24 10:22:24','2014-04-05 14:30:00',0,NULL,NULL,1,9);

INSERT INTO `pi_decision` (`pi_id`,`decision_id`) VALUES (1,65),(2,65);

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
    (`id`, `venue_id`, `impounding_type`, `case_id`, `vrm`,
    `outcome`, `last_modified_by`, `presiding_tc_id`, `created_by`,
    `application_receipt_date`, `outcome_sent_date`, `close_date`,
    `venue_other`, `hearing_date`, `notes`, `created_on`, `last_modified_on`, `version`)
VALUES
    (17, 3, 'impt_hearing', 24, 'AB23 CDE',
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

INSERT INTO `transport_manager_licence` (`id`, `created_by`, `last_modified_by`, `licence_id`, `tm_type`, `transport_manager_id`, `additional_information`, `created_on`, `deleted_date`, `hours_fri`, `hours_mon`, `hours_sat`, `hours_sun`, `hours_thu`, `hours_tue`, `hours_wed`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, NULL, NULL, 7, 'tm_t_i', 1, NULL, NULL, NULL, 2, 2, 2, 2, 2, NULL, NULL, NULL, NULL, 1),
	(2, NULL, NULL, 207, 'tm_t_i', 2, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, 1),
	(3, NULL, NULL, 208, 'tm_t_i', 3, NULL, NULL, NULL, 2, 2, 2, 2, 2, NULL, NULL, NULL, NULL, 1);

INSERT INTO `transport_manager_application` (`id`, `application_id`, `tm_application_status`, `created_by`, `last_modified_by`, `tm_type`, `transport_manager_id`, `action`, `additional_information`, `created_on`, `deleted_date`, `hours_fri`, `hours_mon`, `hours_sat`, `hours_sun`, `hours_thu`, `hours_tue`, `hours_wed`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, 1, 'tmap_st_incomplete', NULL, NULL, 'tm_t_i', 1, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 1, 1, 1, NULL, NULL, 1),
	(3, 1, 'tmap_st_tm_signed', NULL, NULL, 'tm_t_i', 3, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 3, 4, 5, NULL,
  NULL, 1),
	(4, 7, 'tmap_st_postal_application', NULL, NULL, 'tm_t_i', 3, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 6, 7, 8, NULL,
	NULL, 1),
  (5, 8, 'tmap_st_tm_signed', NULL, NULL, 'tm_t_i', 2, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 3, 4, 5, NULL,
  NULL, 1),
  (6, 8, 'tmap_st_postal_application', NULL, NULL, 'tm_t_i', 3, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 6, 7, 8, NULL,
  NULL, 1);

INSERT INTO `tm_application_oc` (`transport_manager_application_id`, `operating_centre_id`)
VALUES
	(1, 16),
	(3, 21),
	(4, 37),
	(4, 39),
	(4, 48);

INSERT INTO `tm_licence_oc` (`transport_manager_licence_id`, `operating_centre_id`)
VALUES
	(1, 16),
	(3, 16),
	(3, 21),
	(4, 16);

INSERT INTO `tm_employment` (`id`, `transport_manager_id`, `contact_details_id`, `employer_name`, `position`, `hours_per_week`, `created_by`, `created_on`, `version`)
VALUES
    (1, 1, 116, 'TESCO', 'Manager', '10 hrs /  3 days per week', 1, '2014-01-01 10:10:10', 1),
    (2, 1, 117, 'TESCO', 'Director', '15 hours over 4 days/wk', 2, '2014-01-01 10:10:10', 1),
    (3, 3, 118, 'Sainsburys', 'Manager', '4 hours over 2 days / wk', 1, '2014-01-01 10:10:10', 1),
    (4, 3, 119, 'Asda', 'Director', '15 hrs 3 days per week', 2, '2014-01-01 10:10:10', 1);

INSERT INTO `tm_qualification` (`id`, `transport_manager_id`, `created_by`, `last_modified_by`, `country_code`,
    `qualification_type`, `created_on`, `last_modified_on`, `version`, `issued_date`, `serial_no`) VALUES
    (1,1,NULL,NULL,'GB','tm_qt_cpcsi',NULL,NULL,1,'2014-01-01','1'),
    (2,1,NULL,NULL,'GB','tm_qt_cpcsn',NULL,NULL,1,'2014-02-02','2'),
    (3,3,1,1,'GB','tm_qt_cpcsi','2012-01-01',NULL,1,'2012-01-01','3333'),
    (4,3,1,1,'ZA','tm_qt_cpcsn','2013-02-02',NULL,1,'2013-02-02','4444');

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

INSERT INTO `transport_manager` (`id`, `created_by`, `last_modified_by`, `tm_status`, `tm_type`, `work_cd_id`,
 `home_cd_id`, `deleted_date`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,NULL,NULL,'tm_s_cur','tm_t_i',115,117,NULL,NULL,NULL,1),
    (2,NULL,NULL,'tm_s_dis','tm_t_e',116,118,NULL,NULL,NULL,1),
    (3,NULL,NULL,'tm_s_rem','tm_t_i',104,119,NULL,NULL,NULL,1);

INSERT INTO `historic_tm` (`id`, `historic_id`, `forename`, `family_name`, `birth_date`, `qualification_type`,
`certificate_no`, `lic_or_app`, `date_added`, `date_removed`, `lic_no`, `application_id`, `seen_contract`,
`seen_qualification`, `hours_per_week`)
VALUES
(1,1,'ANON1000','Surname1000','1965-10-11','EX2               ',NULL,NULL,NULL,NULL,NULL,NULL,0,0,0),
(2,2,'ANON10000','Surname10000','1920-10-11','EX2               ',NULL,NULL,NULL,NULL,NULL,NULL,0,0,0),
(3,3,'ANON100000','Surname100000','1909-10-11','EX2               ','*********','L',NULL,'2006-12-13','PM0000002 ',NULL,0,0,0),
(4,3,'ANON100001','Surname100001','1959-10-11','EX2               ','*********','A','2006-11-22',NULL,'PM1007722 ',118596,1,1,0),
(5,3,'ANON100002','Surname100002','1900-01-01','EX2               ','*********','L','2006-12-06','2008-11-26','PM1007722 ',NULL,0,0,0),
(6,4,'ANON100003','Surname100003','1912-10-11','RSA2              ',NULL,'L','2004-11-19',NULL,'PM0002188 ',NULL,0,0,0),
(7,4,'ANON100004','Surname100004','1999-10-11','RSA2              ',NULL,'L','2004-12-03',NULL,'PM0001544 ',NULL,0,0,10),
(8,4,'ANON100005','Surname100005','1910-10-11','RSA2              ',NULL,'L','2005-09-02',NULL,'OM0028724 ',NULL,0,0,20),
(9,5,'ANON100006','Surname100006','1916-10-11','OCR2              ','NOT KNOWN','L',NULL,'2011-04-08','PM0000004 ',NULL,0,0,0),
(10,5,'ANON100007','Surname100007','1912-10-11','OCR2              ','NOT KNOWN','L','2005-11-18',NULL,'PM0001031 ',NULL,0,0,10),
(11,5,'ANON100008','Surname100008','1949-10-11','OCR2              ','NOT KNOWN','A','2003-06-11',NULL,'PM0002607 ',44038,1,1,38),
(12,5,'ANON100009','Surname100009','1946-10-11','OCR2              ','NOT KNOWN','A','2005-10-28',NULL,'PM0001031 ',97004,1,1,10),
(13,5,'ANON10001','Surname10001','1921-10-11','OCR2              ','NOT KNOWN','L','2003-08-11',NULL,'PM0002607 ',NULL,1,1,38);

INSERT INTO `other_licence` (`id`, `application_id`,`transport_manager_id`,`lic_no`,`created_by`, `last_modified_by`,
`created_on`, `last_modified_on`, `version`, `role`, `operating_centres`, `total_auth_vehicles`, `hours_per_week`,
`transport_manager_application_id`,`transport_manager_licence_id`,`previous_licence_type`) VALUES
    (1,3,1,'AB123456',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,'prev_has_licence'),
    (2,3,1,'YX654321',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,'prev_has_licence'),
    (3,6,2,'AB123456',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,'prev_has_licence'),
    (4,1,1,'AA111111',1,NULL,'2014-10-01 21:58:52',NULL,1,'ol_role_tm','oc1',2,10,1,NULL,'prev_has_licence'),
    (5,1,1,'AB122222',1,NULL,'2014-10-02 21:58:52',NULL,1,'ol_role_tm','oc2',2,10,1,NULL,'prev_has_licence'),
    (6,2,1,'AA133333',1,NULL,'2014-10-03 21:58:52',NULL,1,'ol_role_tm','oc3',3,11,2,NULL,'prev_has_licence'),
    (7,2,1,'AB144444',1,NULL,'2014-10-04 21:58:52',NULL,1,'ol_role_tm','oc4',3,11,2,NULL,'prev_has_licence'),
    (8,1,3,'AA311111',1,NULL,'2014-10-05 21:58:52',NULL,1,'ol_role_tm','oc5',4,12,3,NULL,'prev_has_licence'),
    (9,1,3,'AB322222',1,NULL,'2014-10-06 21:58:52',NULL,1,'ol_role_tm','oc6',4,12,3,NULL,'prev_has_licence'),
    (10,2,3,'AA333333',1,NULL,'2014-10-07 21:58:52',NULL,1,'ol_role_tm','oc7',5,13,4,NULL,'prev_has_licence'),
    (11,2,3,'AB344444',1,NULL,'2014-10-08 21:58:52',NULL,1,'ol_role_tm','oc8',5,13,4,NULL,'prev_has_licence'),
    (12,NULL,1,'CC11111',1,NULL,'2014-10-09 21:58:52',NULL,1,'ol_role_tm','oc9',6,14,NULL,1,'prev_has_licence'),
    (13,NULL,1,'CD12222',1,NULL,'2014-10-10 21:58:52',NULL,1,'ol_role_tm','oc10',6,14,NULL,1,'prev_has_licence'),
    (14,NULL,2,'CC11111',1,NULL,'2014-10-11 21:58:52',NULL,1,'ol_role_tm','oc11',7,15,NULL,2,'prev_has_licence'),
    (15,NULL,2,'CD12222',1,NULL,'2014-10-12 21:58:52',NULL,1,'ol_role_tm','oc12',7,15,NULL,2,'prev_has_licence'),
    (16,NULL,3,'CC33333',1,NULL,'2014-10-13 21:58:52',NULL,1,'ol_role_tm','oc13',8,16,NULL,3,'prev_has_licence'),
    (17,NULL,3,'CD44444',1,NULL,'2014-10-14 21:58:52',NULL,1,'ol_role_tm','oc14',8,16,NULL,3,'prev_has_licence');

INSERT INTO `tm_case_decision` (`id`,`decision`,`case_id`,`created_by`,`last_modified_by`,`is_msi`,`notified_date`,
  `repute_not_lost_reason`,`no_further_action_reason`,`unfitness_end_date`,`unfitness_start_date`,`created_on`,`decision_date`,`deleted_date`,
  `last_modified_on`,`version`) VALUES
  (1,'tm_decision_rl',82,1,1,0,'2015-01-12',NULL,NULL,'2015-03-31','2015-01-20',NULL,'2015-01-10',NULL,NULL,1),
  (2,'tm_decision_rnl',83,1,1,1,'2014-12-10','Reason why repute not lost',NULL,NULL,NULL,NULL,'2014-12-06',NULL,NULL,1),
  (3,'tm_decision_noa',84,1,1,1,'2014-09-30',NULL,'Reason no further action',NULL,NULL,NULL,'2014-10-06',NULL,NULL,1);

INSERT INTO `tm_case_decision_rehab` (`tm_case_decision_id`,`rehab_measure_id`) VALUES
  (1,'tm_rehab_adc');

INSERT INTO `tm_case_decision_unfitness` (`tm_case_decision_id`,`unfitness_reason_id`) VALUES
  (1,'tm_unfit_inn');

INSERT INTO `user` (`id`, `team_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `login_id`,`contact_details_id`, `local_authority_id`,`transport_manager_id`,`partner_contact_details_id`, `pid`) VALUES
  (273, 13, 2, 2, now(), now(), 'usr273', 105, null, NULL, NULL, '0a041b9462caa4a31bac3567e0b6e6fd9100787db2ab433d96f6d178cabfce90'),
  (291, 14, 2, 2, now(), now(), 'usr291', 106, null, NULL, NULL, '6025d18fe48abd45168528f18a82e265dd98d421a7084aa09f61b341703901a3'), -- ADMIN, System	Internal
  (20, 13, 2, 2, now(), now(), 'usr20', 130, null, NULL, NULL,'5860faf02b6bc6222ba5aca523560f0e364ccd8b67bee486fe8bf7c01d492ccb'), -- CW, Case Worker	Internal
  (21, 13, 2, 2, now(), now(), 'usr21', 131, null, NULL, NULL, '5269ef980de47819ba3d14340f4665262c41e933dc92c1a27dd5d01b047ac80e'), -- CW, Case Worker	Internal
  (528, 13, 2, 2, now(), now(), 'usr528', 132, null, NULL, NULL, '5a39bead318f306939acb1d016647be2e38c6501c58367fdb3e9f52542aa2442'),-- CWRO, Case Worker Read Only	Internal
  (529, 13, 2, 2, now(), now(), 'usr529', 133, null, NULL, NULL, 'ecb48a1cc94f951252ec462fe9ecc55c3ef123fadfe935661396c26a45a5809d'),-- CWRO, Case Worker Read Only	Internal
  (331, 13, 2, 2, now(), now(), 'usr331', 114, null, NULL, NULL,'9400f1b21cb527d7fa3d3eabba93557a18ebe7a2ca4e471cfe5e4c5b4ca7f767'),	-- CWRON, Case Worker Read Only No Documents	Internal
  (342, 13, 2, 2, now(), now(), 'usr342', 101, null, NULL, NULL, 'f5ca38f748a1d6eaf726b8a42fb575c3c71f1864a8143301782de13da2d9202b'),	-- CWRON, Case Worker Read Only No Documents	Internal
  (20131, NULL, 2, 2, now(), now(), 'usr20131', 169, 1, NULL, NULL, '3268151e52d97b4cacf97f5b46a5c76c8416e928e137e3b3dc447696a29afbaa'),-- LA, Local Authority, LA dashboard application	SelfServe
  (20132, NULL, 2, 2, now(), now(), 'usr20132', 170, 1, NULL, NULL, 'f60afa4989a7db13314a2ab9881372634b5402c30ba7257448b13fa388de1b78'),-- LA, Local Authority, LA dashboard application	SelfServe
  (1964, NULL, 2, 2, now(), now(), 'usr1964', 101, null, NULL, 140, '19581e27de7ced00ff1ce50b2047e7a567c76b1cbaebabe5ef03f7c3017bb5b7'),-- PART, Partner, HMRC	Partner
  (1965, NULL, 2, 2, now(), now(), 'usr1965', 168, null, NULL, 140, '4a44dc15364204a80fe80e9039455cc1608281820fe2b24f1e5233ade6af1dd5'),-- PART, Partner, HMRC	Partner
  (778, NULL, 2, 2, now(), now(), 'usr778', 101, null, NULL, 140, '4fc82b26aecb47d2868c4efbe3581732a3e7cbcc6c2efb32062c08170a05eeb8'),-- PARTA, Partner Admin	Partner
  (779, NULL, 2, 2, now(), now(), 'usr779', 167, null, NULL, 140, '6b51d431df5d7f141cbececcf79edf3dd861c3b4069f0b11661a3eefacbba918'),-- PARTA, Partner Admin	Partner
  (542, NULL, 2, 2, now(), now(), 'usr542', 166, null, NULL, NULL,'3fdba35f04dc8c462986c992bcf875546257113072a909c162f7e470e581e278'),-- SS, Self Serve	SelfServe
  (543, NULL, 2, 2, now(), now(), 'usr543', 101, null, 1, NULL, '8527a891e224136950ff32ca212b45bc93f69fbb801c3b1ebedac52775f99e61'),-- SS, Self Serve    SelfServe
  (611, NULL, 2, 2, now(), now(), 'usr611', 165, null, NULL, NULL,'8fab3a60577befd765cde83f2737cd1a9f25a72356c94052c2194e816829b331'),-- SSADMIN, Self Service Administrators NB Does not use a role. Instead see organisation_user link table where is_administrator=1 for users with the self service role	SelfServe
  (612, NULL, 2, 2, now(), now(), 'usr612', 101, null, NULL, NULL, 'b999205cdacd2c4516598d99b420d29786443e9908556a65f583a6fd4765ee4a'), -- SSADMIN, Self Service Administrators NB Does not use a role. Instead see organisation_user link table where is_administrator=1 for users with the self service role	SelfServe
  (1, NULL, 2, 2, now(), now(), 'system', null, null, NULL, NULL, '10236fc8becc3b78f6956e26de661d57bc67d9424424fbdbe584d9736ba6aa38'), -- System User
  /* Kirstie Brown */
  (36047, 17, 2, 2, now(), now(), 'usr36047', 230, NULL, NULL, NULL, NULL),
  /* Dianne Craven */
  (29431, 17, 2, 2, now(), now(), 'usr29431', 231, NULL, NULL, NULL, NULL),
  /* Neil Chivers */
  (76754, 17, 2, 2, now(), now(), 'usr76754', 232, NULL, NULL, NULL, NULL),
  /* Shakil Ahmed */
  (322, 17, 2, 2, now(), now(), 'usr322', 233, NULL, NULL, NULL, NULL),
  /* 	Rachael Evans */
  (73852, 17, 2, 2, now(), now(), 'usr73852', 234, NULL, NULL, NULL, NULL),
  /* 	Nicola Field */
  (68648, 17, 2, 2, now(), now(), 'usr68648', 235, NULL, NULL, NULL, NULL),
  /* 	Julie Goward */
  (1071, 99, 2, 2, now(), now(), 'usr1071', 236, NULL, NULL, NULL, NULL),
  /* 	Catherine Tobin */
  (39158, 99, 2, 2, now(), now(), 'usr39158', 237, NULL, NULL, NULL, NULL),
  /* 	Philip Stagg */
  (455, 99, 2, 2, now(), now(), 'usr455', 238, NULL, NULL, NULL, NULL),
  /* 	Carole Ryalls */
  (76189, 99, 2, 2, now(), now(), 'usr76189', 239, NULL, NULL, NULL, NULL),
  /* 	Steven Jones */
  (59, 21, 2, 2, now(), now(), 'usr59', 240, NULL, NULL, NULL, NULL)
;

UPDATE `user` SET pid = SHA2(login_id, 256);

INSERT INTO `organisation_user` (`organisation_id`, `user_id`, `is_administrator`) VALUES
  (1, 542, 0),
  (1, 543, 0),
  (1, 611, 1),
  (1, 612, 1);

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
  (273, 24),
  (291, 24),
  (20, 23),
  (21, 23),
  (528, 22),
  (529, 22),
  (331, 21),
  (342, 21),
  (20131, 31),
  (20132, 32),
  (1964, 30),
  (1965, 30),
  (778, 29),
  (779, 29),
  (542, 26),
  (543, 27),
  (611, 25),
  (612, 25),
  (1, 24),

  (36047, 24),
  (29431, 24),
  (76754, 24),
  (322, 24),
  (73852, 24),
  (68648, 24),
  (1071, 24),
  (39158, 24),
  (455, 24),
  (76189, 24),
  (59, 24)
  ;

INSERT INTO `vehicle` (`id`, `created_by`, `last_modified_by`, `vrm`, `plated_weight`,
    `certificate_no`, `vi_action`, `created_on`,
    `last_modified_on`, `version`, `section_26`) VALUES
    (1,NULL,4,'VRM1',7200,'CERT10001',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (2,NULL,6,'VRM2',3500,'CERT10002',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (3,NULL,5,'VRM3',3800,'CERT10003',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (4,NULL,1,'VRM4',6800,'CERT10004',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 1),
    (5,NULL,4,'VRM1',7200,'CERT10005',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (6,NULL,6,'VRM2',3500,'CERT10006',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (7,NULL,5,'VRM3',3800,'CERT10007',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0),
    (8,NULL,1,'VRM4',6800,'CERT10008',NULL,'2010-01-12 00:00:00','2014-02-20 00:00:00',1, 0);

-- Cases
INSERT INTO `cases` (`id`,`case_type`,`licence_id`,`application_id`,`transport_manager_id`,
   `last_modified_by`,`created_by`,`ecms_no`,`open_date`,`closed_date`,`description`,`is_impounding`,
   `annual_test_history`,`prohibition_note`,`conviction_note`,`penalties_note`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (24,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123456','2012-03-21',NULL,'Case for convictions against company
  directors',0,'Annual test history for case 24','prohibition test notes','test comments',NULL,NULL,
  '2013-11-12 12:27:33',NULL,1),
  (28,'case_t_app',7,1,NULL,NULL,NULL,'E123444','2012-06-13',NULL,'Convictions against operator',0,'Annual Test History for case 28',NULL,NULL,NULL,NULL,'2014-01-01 11:11:11',NULL,1),
  (29,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,'comment',NULL,'2014-01-11 11:11:11','2014-11-07 12:47:07',3),
  (30,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (31,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-05-25','345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (32,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (33,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-03-29','345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (34,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (35,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-04-15','45645645645',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (36,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (37,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11','2014-04-23','3453g345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (38,'case_t_lic',7,NULL,NULL,NULL,NULL,'2345678','2014-02-13','2014-05-25','MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (39,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14','2014-05-25','new test case 2',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (40,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (41,'case_t_lic',7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (42,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123456','2013-06-01',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (43,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123444','2013-06-02',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (44,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (45,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (46,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (47,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (48,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (49,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (50,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'45645645645',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (51,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (52,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'3453g345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (53,'case_t_lic',7,NULL,NULL,NULL,NULL,'2345678','2014-02-13',NULL,'MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (54,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14',NULL,'new test case 2',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (55,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (56,'case_t_lic',7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (57,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123456','2013-11-01',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (58,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123444','2013-11-02',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (59,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (60,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'werwrew',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (61,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (62,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'weewrerwerw',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (63,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'345345345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (64,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'7656567567',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (65,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'45645645645',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (66,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'56756757',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (67,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'3453g345',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (68,'case_t_lic',7,NULL,NULL,NULL,NULL,'2345678','2014-02-13',NULL,'MWC test case 1',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (69,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops12345','2014-02-14',NULL,'new test case 2',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (70,'case_t_lic',7,NULL,NULL,NULL,NULL,'coops4321','2014-02-14',NULL,'MWC test case 3',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2),
  (71,'case_t_lic',7,NULL,NULL,NULL,NULL,'E647654','2014-02-14',NULL,'MWC test case 4',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (72,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123456','2013-11-02',NULL,'Case for convictions against company directors',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (73,'case_t_lic',7,NULL,NULL,NULL,NULL,'E123444','2013-11-03',NULL,'Convictions against operator Fred',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14),
  (74,'case_t_lic',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (75,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'PSV licence case',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (76,'case_t_app',110,1,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to an application',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (77,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to a licence',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (78,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (79,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI with no response entered',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (80,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to MSI with response not sent',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (81,'case_t_lic',110,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'Case linked to Non-MSI',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (82,'case_t_tm',NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an internal Transport manager',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (83,'case_t_tm',NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an external Transport manager',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (84,'case_t_tm',NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an external Transport manager',0,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1);

INSERT INTO `team` (`id`, `version`, `name`, `traffic_area_id`) VALUES
    (4,1,'SEMTA','B'),
    (6,1,'ETA','B'),
    (12,1,'WMTA','B'),
    (13,1,'Leeds Licensing Team 1','B'),
    (14,1,'Leeds Licensing Team 2','B'),
    (15,1,'Leeds Licensing Team 3','B'),
    (16,1,'Leeds Licensing Team 4','B'),
    (17,1,'Leeds Licensing Team 5','B'),
    (21,1,'STA Compliance Team 1','B'),
    (23,1,'Leeds Compliance Team 1','B'),
    (30,1,'WTA Compliance Team','B'),
    (34,1,'Welsh Compliance Team','B'),
    (44,1,'Leeds Licensing Team 7','B'),
    (71,1,'Leeds Licensing MLH','B'),
    (99,1,'Leeds Licensing Team 5-3','B'),
    (103,1,'Golborne OTC Team','B'),
    (115,1,'NI Licensing Caseworker','B'),
    (118,1,'TRU Team','B');

INSERT INTO `case_category` (`case_id`, `category_id`)
VALUES
    (29, 'case_cat_7');

/**
 * NOTE: These inserts can't be grouped into one as they insert different columns
 */
/* Application task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (1,1,7,9,8,1,13,'A test task','2014-08-12',1);
    /* Licence task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (2,null,110,1,74,1,13,'Another test task','2013-02-11',1);
/* IRFO task */
INSERT INTO task(id,irfo_organisation_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (3,1,8,70,1,13,'An organisation task','2014-05-01',1);
/* Transport Manager task */
INSERT INTO task(id,transport_manager_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (4,2,5,105,6,13,'A transport task','2010-01-01',1);
/* Case task */
INSERT INTO task(id,case_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (5,24,2,44,null,13,'A case task','2010-02-01',1);
/* Unlinked task */
INSERT INTO task(id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (6,7,67,null,13,'Unassigned task','2010-07-03',1);
/* Application, future, urgent task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (7,2,7,9,33,1,13,'A test task','2018-09-27',1,1);
/* Licence, single licence holder */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (8,null,63,1,110,1,13,'Single licence','2012-09-27',0,1);
/* Transport Manager task */
INSERT INTO task(id,transport_manager_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (9,3,5,103,1,13,'A test task for TM 3','2014-12-15',1);
/* Bus Registration task */
INSERT INTO task(id,bus_reg_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (10,1,110,3,39,1,13,'A test Bus Reg task','2014-12-15',1);

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


INSERT INTO `submission_action` (`submission_id`, `last_modified_by`,
    `created_by`, `is_decision`, `comment`, `created_on`, `last_modified_on`)
VALUES
    (12, 1, 1, 0, 'Comment recommendaion testing lorem', NOW(), NOW()),
    (12, 1, 1, 1, 'Comment decision testing lorem', NOW(), NOW());

INSERT INTO `submission_action_type` (`submission_action_id`, `action_type`)
VALUES
    (12, 'sub_st_rec_pi'),
    (12, 'sub_st_dec_agree');

INSERT INTO `serious_infringement`
(`id`, `si_category_type_id`, `created_by`,`last_modified_by`, `si_category_id`, `case_id`, `check_date`,
 `infringement_date`, `reason`, `deleted_date`,`created_on`, `last_modified_on`, `version`)
VALUES
  (1, '101', 1,1, 'MSI', 29, '2014-04-04', '2014-04-05', null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1),
  (2, '202', 1,1, 'MSI', 29, '2014-04-04', '2014-04-05', null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1),
  (3, '401', 1,1, 'MSI', 29, '2014-04-04', '2014-04-05', null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1),
  (4, '603', 1,1, 'MSI', 29, '2014-04-04', '2014-04-05', null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1);

INSERT INTO `erru_request` (`id`, `case_id`, `originating_authority`, `member_state_code`, `transport_undertaking_name`,
    `vrm`, `msi_type`, `notification_number`, `workflow_id`, `response_sent`, `response_user_id`, `response_time`,
    `deleted_date`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 29, 'Polish Transport Authority', 'PL', 'transport undertaking name', 'ABCD 123', 'erru_case_t_msi',
      '12345', 'A3CCBDB1-6C8B-4741-847B-4C6B80AA8608', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `si_penalty` (`id`, `serious_infringement_id`, `imposed`, `start_date`, `end_date`, `reason_not_imposed`,
   `deleted_date`, `si_penalty_type_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
   `olbs_key`)
VALUES
  (1, 1, 1, '2014-06-01', '2015-01-31', NULL, NULL, 101, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (2, 1, 0, NULL, NULL, 'Reason the penalty was not imposed', NULL, 306, 1, 1, '2014-05-21 12:22:09',
   '2014-05-21 12:22:09', 1, NULL),
  (3, 2, 0, NULL, NULL, 'Reason the penalty was not imposed', NULL, 306, 1, 1, '2014-05-21 12:22:09',
   '2014-05-21 12:22:09', 1, NULL),
  (4, 2, 1, '2014-05-01', '2015-01-31', '', NULL, 101, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (5, 2, 1, '2014-04-01', '2015-04-30', '', NULL, 102, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (6, 2, 1, '2014-03-01', '2015-03-31', '', NULL, 301, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1,
   NULL),
  (7, 3, 1, '2014-02-01', '2015-02-28', '', NULL, 302, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09',
   1, NULL),
  (8, 3, 1, '2014-01-01', '2015-01-31', '', NULL, 303, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (9, 3, 1, '2013-12-01', '2014-12-31', '', NULL, 304, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (10, 3, 1, '2013-11-01', '2014-11-30', '', NULL, 305, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (11, 3, 1, '2013-10-01', '2014-10-31', '', NULL, 306, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (12, 3, 1, '2013-09-01', '2014-09-30', '', NULL, 307, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL);


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

INSERT INTO `si_penalty_erru_imposed` (`id`, `final_decision_date`, `start_date`, `end_date`, `deleted_date`,
  `serious_infringement_id`, `si_penalty_imposed_type_id`, `executed`, `created_by`, `last_modified_by`, `created_on`,
  `last_modified_on`, `version`, `olbs_key`)
VALUES
  (1, '2014-08-02', '2014-11-01', '2015-12-01', NULL, 1, 204, 'pen_erru_imposed_executed_yes', 1, 1,
      '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (2, '2014-09-12', '2014-11-01', '2015-12-01', NULL, 2, 202, 'pen_erru_imposed_executed_no', 1, 1,
      '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (3, '2014-10-14', '2014-11-01', '2015-12-01', NULL, 3, 102, 'pen_erru_imposed_executed_un', 1, 1,
      '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (4, '2014-11-22', '2014-11-01', '2015-12-01', NULL, 3, 201, 'pen_erru_imposed_executed_un', 1, 1,
      '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL);


INSERT INTO `si_penalty_erru_requested` (`id`, `duration`, `deleted_date`, `serious_infringement_id`,
  `si_penalty_requested_type_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
  `olbs_key`)
VALUES
  (1, 12, NULL, 1, 305, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (2, 36, NULL, 2, 302, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (3, 60, NULL, 3, 303, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (4, 24, NULL, 4, 306, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL),
  (5, 24, NULL, 4, 307, 1, 1, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1, NULL);


INSERT INTO `public_holiday`(`id`,`public_holiday_date`,`is_england`,`is_wales`,`is_scotland`,`is_ni`)
VALUES
  (1,'2014-01-01 00:00:00',1,1,1,1),
  (2,'2014-01-02 00:00:00',0,0,1,0),
  (3,'2014-03-17 00:00:00',0,0,0,1),
  (4,'2014-04-18 00:00:00',1,1,1,1),
  (5,'2014-04-21 00:00:00',1,1,0,1),
  (6,'2014-05-05 00:00:00',1,1,1,1),
  (7,'2014-05-26 00:00:00',1,1,1,1),
  (8,'2014-07-14 00:00:00',0,0,0,1),
  (9,'2014-08-04 00:00:00',0,0,1,0),
  (10,'2014-08-25 00:00:00',1,1,0,1),
  (11,'2014-12-01 00:00:00',0,0,1,0),
  (12,'2014-12-25 00:00:00',1,1,1,1),
  (13,'2014-12-26 00:00:00',1,1,1,1),
  (14,'2015-01-01 00:00:00',1,1,1,1),
  (15,'2015-01-02 00:00:00',0,0,1,0),
  (16,'2015-03-17 00:00:00',0,0,0,1),
  (17,'2015-04-03 00:00:00',1,1,1,1),
  (18,'2015-04-06 00:00:00',1,1,0,1),
  (19,'2015-05-04 00:00:00',1,1,1,1),
  (20,'2015-05-25 00:00:00',1,1,1,1),
  (21,'2015-07-13 00:00:00',0,0,0,1),
  (22,'2015-08-03 00:00:00',0,0,1,0),
  (23,'2015-08-31 00:00:00',1,1,0,1),
  (24,'2015-11-30 00:00:00',0,0,1,0),
  (25,'2015-12-25 00:00:00',1,1,1,1),
  (26,'2015-12-28 00:00:00',1,1,1,1),
  (27,'2016-01-01 00:00:00',1,1,1,1),
  (28,'2016-01-04 00:00:00',0,0,1,0),
  (29,'2016-03-17 00:00:00',0,0,0,1),
  (30,'2016-03-25 00:00:00',1,1,1,1),
  (31,'2016-03-28 00:00:00',1,1,0,1),
  (32,'2016-05-02 00:00:00',1,1,1,1),
  (33,'2016-05-30 00:00:00',1,1,1,1),
  (34,'2016-07-12 00:00:00',0,0,0,1),
  (35,'2016-08-01 00:00:00',0,0,1,0),
  (36,'2016-08-29 00:00:00',1,1,1,1),
  (37,'2016-12-26 00:00:00',1,1,1,1),
  (38,'2016-12-27 00:00:00',1,1,0,0);

INSERT INTO `publication` (`id`,`pub_status`,`last_modified_by`,`created_by`,`traffic_area_id`,`doc_template_id`,`document_id`,`pub_date`,`doc_name`,`publication_no`,`pub_type`,`created_on`,`last_modified_on`,`version`)
VALUES
  (3,'pub_s_new',1,1,'B',685,NULL,'2014-10-30',NULL,6129,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (4,'pub_s_new',1,1,'B',693,NULL,'2014-10-30',NULL,2156,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (5,'pub_s_new',1,1,'C',686,NULL,'2014-10-30',NULL,6576,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (6,'pub_s_new',1,1,'C',694,NULL,'2014-10-30',NULL,2648,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (7,'pub_s_new',1,1,'D',689,NULL,'2014-10-30',NULL,2624,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (8,'pub_s_new',1,1,'D',696,NULL,'2014-10-30',NULL,2181,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (9,'pub_s_new',1,1,'F',683,NULL,'2014-10-30',NULL,5008,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (10,'pub_s_new',1,1,'F',691,NULL,'2014-10-30',NULL,2160,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (11,'pub_s_new',1,1,'G',698,NULL,'2014-10-30',NULL,8377,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (12,'pub_s_new',1,1,'G',699,NULL,'2014-10-30',NULL,1986,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (13,'pub_s_new',1,1,'H',690,NULL,'2014-10-30',NULL,5379,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (14,'pub_s_new',1,1,'H',697,NULL,'2014-10-30',NULL,2484,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (15,'pub_s_new',1,1,'K',684,NULL,'2014-10-30',NULL,3889,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (16,'pub_s_new',1,1,'K',692,NULL,'2014-10-30',NULL,2283,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (21,'pub_s_new',1,1,'M',688,NULL,'2014-10-30',NULL,6666,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (22,'pub_s_new',1,1,'M',695,NULL,'2014-10-20',NULL,7777,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (23,'pub_s_new',1,1,'N',687,NULL,'2014-10-31',NULL,8888,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1);

INSERT INTO `publication_link` (`id`, `application_id`, `licence_id`, `pi_id`, `publication_id`, `publication_section_id`, `bus_reg_id`, `created_by`, `last_modified_by`, `traffic_area_id`, `transport_manager_id`, `text1`, `text2`, `text3`, `created_on`, `deleted_date`, `last_modified_on`, `version`)
VALUES
  (1, NULL, 7, NULL, 3, 13, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (47574) to be held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF, on 16 May 2014 commencing at 14:00(Previous Publication:(5024)\r)\rOF1093864          R\rCOASTFIELDS LEISURE LIMITED\rDirector(s): LYNDA JOYCE SILVESTER, LLOYD BENNET SILVESTER.\rVICKERS POINT, ROMAN BANK, INGOLDMELLS SKEGNESS PE25 1JU', 'GV - S26 - Consideration of disciplinary action under Section 26\rGV - S28 - Consideration of disciplinary action under Section 28', NULL, '2014-11-25 15:47:03', NULL, NULL, 1),
  (2, NULL, 7, NULL, 3, 14, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (47212) held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF on 25 March 2014 at 10:00(Previous Publication:(5024)\r)OF1100325          SI\rB B TRANSPORT (EAST ANGLIA) LIMITED\rDirector(s): STEVEN CLIVE HARROLD.\rBROCKFORD GARAGE, BROCKFORD , STOWMARKET IP14 5PF', 'GV - S26 - Licence revoked with immediate effect\rGV - S27 - Licence revoked with immediate effect', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (3, 1, 7, 1, 3, 1, NULL, NULL, NULL, 'B', NULL, 'OF1128599 SN', 'A & R DISTRIBUTION (LINCS) LIMITED\rDirector(s): STUART JOHN ROBERTS.', '4 EARLSFIELD, MOULTON SEAS END , SPALDING PE12 6LE\rOperating Centre: EDISON COURT, SOUTH HOLLAND ENTERPRISE PARK, PINCHBECK SPALDING PE11 3FX\rAuthorisation:2 Vehicle(s) and 0 Trailer(s).\rTransport Manager(s): STUART JOHN ROBERTS', '2014-12-11 10:03:15', NULL, NULL, 1),
  (4, 1, 7, 1, 3, 3, NULL, NULL, NULL, 'B', NULL, 'OF1049327 SN', 'A J GRAB HIRE LTD\rDirector(s): JASON MATHEW USHER', 'LANGDALE FARM, CAMBRIDGE ROAD, MELBOURN ROYSTON SG8 6EY\rIncreased authorisation at existing operating centre: LANGDALE FARM, HARD STANDING BEHIND BARN TO LEFT, CAMBRIDGE ROAD, MELBOURN, ROYSTON SG8 6EY ()\rNew authorisation at this operating centre will be: 2 vehicle(s), 1 trailer(s)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (5, 1, 7, 1, 3, 4, NULL, NULL, NULL, 'B', NULL, 'OF1125539 SN\r(5020)', 'A K FREIGHT LTD\rDirector(s): STEPHEN ANTHONY KIDD', '14 HINGLEY CLOSE, GORLESTON , GREAT YARMOUTH NR31 0QH\rOperating Centre: 8 SPEEDWELL WAY  HARLESTON IP20 9EH\rAuthorisation:2 Vehicle(s) and 2 Trailer(s).\rTransport Manager(s): RICHARD ALFRED SPALL\rNew Undertaking: Andrew Kidd will not be involved in the management of the company or the transport operations.. Attached to Licence.\rNew Undertaking: The operator will obtain an independent audit covering all aspects of their operations in January 2015 and January 2016. These audits will be retained and made available to the Traffic Commissioner on request.. Attached to Licence.\rNew Undertaking: The operator will submit original company bank statements showing access to the required financial standing as an average balance by 31 January 2015 and 31 January 2016. These statements will cover the entire months of October, November and December of 2014 and 2015 respectively.. Attached to Licence.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (6, 1, 7, 1, 3, 5, NULL, NULL, NULL, 'B', NULL, 'OF1127481 SN\r(5022)', 'DANIEL CHIRAN', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (7, NULL, 110, NULL, 4, 23, 12, NULL, NULL, 'B', NULL, 'PD2737280/13245', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 26453 / 13249 / 13355 effective from 27 May 2014. To amend Start & finish point and Stopping places.', '2015-03-06 11:47:52', NULL, NULL, 1),
  (8, NULL, 110, NULL, 8, 23, 12, NULL, NULL, 'D', NULL, 'PD2737280/13245', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 26453 / 13249 / 13355 effective from 27 May 2014. To amend Start & finish point and Stopping places.', '2015-03-06 11:47:53', NULL, NULL, 1),
  (9, NULL, 110, NULL, 10, 23, 12, NULL, NULL, 'F', NULL, 'PD2737280/13245', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 26453 / 13249 / 13355 effective from 27 May 2014. To amend Start & finish point and Stopping places.', '2015-03-06 11:47:54', NULL, NULL, 1),
  (13, NULL, 110, NULL, 4, 23, 15, NULL, NULL, 'B', NULL, 'PD2737280/15711', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 46474 / 15712 / 15719 effective from 10 March 2014.', '2015-03-06 13:07:52', NULL, NULL, 1),
  (14, NULL, 110, NULL, 14, 23, 15, NULL, NULL, 'H', NULL, 'PD2737280/15711', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 46474 / 15712 / 15719 effective from 10 March 2014.', '2015-03-06 13:07:53', NULL, NULL, 1),
  (15, NULL, 110, NULL, 22, 23, 15, NULL, NULL, 'M', NULL, 'PD2737280/15711', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'Operating between Leeds and Doncaster given service number 46474 / 15712 / 15719 effective from 10 March 2014.', '2015-03-06 13:07:54', NULL, NULL, 1),
  (16, NULL, 110, NULL, 4, 21, 1, NULL, NULL, 'B', NULL, 'PD2737280/14686', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'From: Doncaster\nTo: Sheffield\nVia: York\nName or No.: 90839 / 90840 / 90841\nService type: Normal Stopping, Frequent Service, Hail & Ride\nEffective date: 15 March 2014\nOther details: Other details', '2015-03-06 13:09:19', NULL, NULL, 1),
  (17, NULL, 110, NULL, 12, 21, 1, NULL, NULL, 'G', NULL, 'PD2737280/14686', 'PD2737280 LEEDS CITY COUNCIL, UNIT 5, 12 ALBERT STREET, WESTPOINT, LEEDS, LS9 6NA', 'From: Doncaster\nTo: Sheffield\nVia: York\nName or No.: 90839 / 90840 / 90841\nService type: Normal Stopping, Frequent Service, Hail & Ride\nEffective date: 15 March 2014\nOther details: Other details', '2015-03-06 13:09:20', NULL, NULL, 1),
  (18, 1, 7, 1, 4, 4, NULL, NULL, NULL, 'B', NULL, 'PF1129560 R\r(2180)', 'ACORN CHILDCARE LTD\rDirector(s): ZOE RAVEN.', '17 SOUTH STREET, CASTLETHORPE , MILTON KEYNES MK19 7EL\rOperating Centre: LINCOLN LODGE FARM, CASTLETHORPE , MILTON KEYNES MK19 7HJ\rAuthorisation:1 Vehicle(s).\rOperating Centre: 17 SOUTH STREET, CASTLETHORPE , MILTON KEYNES MK19 7EL\rAuthorisation:1 Vehicle(s).\rNew Undertaking: Limousines and novelty type vehicles are not to be operated under this operator’s licence.. Attached to Licence.\rNew Undertaking: Should income from, or time spent on, the minibus operation exceed that from all other sources for two consecutive months, the operator will apply for a standard national licence.\r. Attached to Licence.\rNew Undertaking: The operator shall, during the life of the restricted licence, keep records of time spent and income earned from all occupations to enable the primary occupation to be determined. Copies of the record shall be made available to the DVSA and/or OTC Officers on request. . Attached to Licence.\rNew Undertaking: Vehicles with eight passenger seats or less will not be operated under the licence without the prior written agreement of the traffic commissioner who may require you to agree to certain undertakings.. Attached to Licence.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (19, 1, 7, 1, 4, 5, NULL, NULL, NULL, 'B', NULL, 'New applications refused text 1', 'New applications refused text 2', 'New applications refused text 3', '2014-12-11 10:03:15', NULL, NULL, 1),
  (20, NULL, 7, NULL, 4, 10, NULL, NULL, NULL, 'B', NULL, 'PF1125228 R\r(2175)', 'Licence surrendered WEF 20 February 2015\rCP WOBURN (OPERATING COMPANY) LIMITED\rDirector(s): ANDREA VALERI, PETER HUDSON STOLL, ANTHONY MARTIN ROBINSON, MICHAEL JOHN PEGLER, FARHAD MAWJIKARIM, MARTIN PETER DALBY, PAUL INGLETT.', 'CENTRE PARCS, 1 EDISON RISE, NEW OLLERTON NEWARK NG22 9DP\rRegistered Bus Services running under this licence have also been surrendered with immediate effect.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (21, NULL, 7, NULL, 4, 10, NULL, NULL, NULL, 'B', NULL, 'PF1091679 R\r(2065)', 'Licence surrendered WEF 20 February 2015\rISHTIAQ AHMED T/A H I TRAVEL', '5 PREBENDAL AVENUE AYLESBURY HP21 8HZ\rRegistered Bus Services running under this licence have also been surrendered with immediate effect.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (22, NULL, 7, NULL, 4, 11, NULL, NULL, NULL, 'B', NULL, 'Licences terminated text 1 (test data line 1)', 'Licences terminated text 2 (test data line 1)', 'Licences terminated text 3 (test data line 1)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (23, NULL, 7, NULL, 4, 11, NULL, NULL, NULL, 'B', NULL, 'Licences terminated text 1 (test data line 2)', 'Licences terminated text 2 (test data line 2\n)', 'Licences terminated text 3 (test data line 2)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (24, NULL, 7, NULL, 4, 12, NULL, NULL, NULL, 'B', NULL, 'Licence revoked text 1', 'Licence revoked text 2', 'Licence revoked text 3\n', '2014-12-11 10:03:15', NULL, NULL, 1),
  (25, NULL, 7, NULL, 4, 20, NULL, NULL, NULL, 'B', NULL, 'Licence CNS text 1 (test data line 1)', 'Licence CNS text 2 (test data line 1)', 'Licence CNS text 3 (test data line 1)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (26, 1, 7, 1, 3, 1, NULL, NULL, NULL, 'B', NULL, 'OF1128513 R', 'AMA SCAFFOLDING LTD\rDirector(s): MARTIN UNSTEAD', 'UN IT 8, FAIRVIEW INDUSTRIAL CENTRE, MARSH WAY RAINHAM RM13 8UH\rOperating Centre: FOSTER STREET FARM, FOSTER STREET , HARLOW CM17 9HS\rAuthorisation:4 Vehicle(s) and 0 Trailer(s).', '2014-12-11 10:03:15', NULL, NULL, 1),
  (27, 1, 7, 1, 3, 3, NULL, NULL, NULL, 'B', NULL, 'OF1063688 SN', 'ABBEY WASTE CONTROL LTD\rDirector(s): TYSON MARK ALEXANDER BONHAM, PAUL ARTHUR ALEXANDER BONHAM\r', 'VICTORY HOUSE, 245 SOUTHTOWN ROAD , GREAT YARMOUTH NR31 0JJ\rIncreased authorisation at existing operating centre: MARINE BASE, BERTH 28, PORTLAND WHARF, 244, SOUTHTOWN ROAD GREAT YARMOUTH NR31 0JJ ()\rNew authorisation at this operating centre will be: 6 vehicle(s), 2 trailer(s)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (28, 1, 7, 1, 3, 4, NULL, NULL, NULL, 'B', NULL, 'OF1127113 SI\r(5021)\r', 'ANUFRIEW LOGISTICS LIMITED\rDirector(s): SLAMONIR WLODZIMIERZ ANUFRIEW', '115 SIDEGATE LANE  IPSWICH IP4 4JB\rOperating Centre: ROY HUMPHREY, A140 NORWICH ROAD, BROME EYE IP23 8AW\rAuthorisation:2 Vehicle(s) and 2 Trailer(s).\rTransport Manager(s): DAVID AUGUSTUS ARTHUR MYHILL', '2014-12-11 10:03:15', NULL, NULL, 1),
  (29, 1, 7, 1, 3, 5, NULL, NULL, NULL, 'B', NULL, 'OF1126341 R\r(5018)', 'FOSTER INSTALLATIONS LTD\rDirector(s): DAVID ROBERT FOSTER', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (30, 1, 7, 1, 3, 8, NULL, NULL, NULL, 'B', NULL, 'OF1123014 R (5021)', 'ALISTAGE LTD\rDirector(s): PHILIP ROSS CHRISTODOLOU', 'EARLS FARM, EARLS LANE, SOUTH MIMMS POTTERS BAR EN6 3LT\rRemoved operating centre: KENRICH HOUSE, ELIZABETH WAY , HARLOW CM19 5TL\rNew operating centre: EARLS FARM , BESIDE TOP BARN, EARLS LANE, SOUTH MIMMS, POTTERS BAR EN6 3LT ()\rNew authorisation at this operating centre will be: 3 vehicle(s), 0 trailer(s)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (31, 1, 7, 1, 3, 8, NULL, NULL, NULL, 'B', NULL, 'OF1028978 R (5021)', 'ALLTYPE FENCING SPECIALISTS LTD\rDirector(s): STEVEN BRIAN PHILLIPS, CLIVE SHARPE', 'YE WENTES WAYES, HIGH RD, LANGDON HILLS BASILDON SS16 6HY\rIncreased authorisation at existing operating centre: YE WENTE WAYES, HIGH ROAD, LANGDON HILLS BASILDON SS16 6HY ()\rNew authorisation at this operating centre will be: 3 vehicle(s), 2 trailer(s)', '2014-12-11 10:03:15', NULL, NULL, 1),
  (32, 1, 7, 1, 3, 16, NULL, NULL, NULL, 'B', NULL, 'OF1126252 SN', 'AMEYS HIGHWAYS LIMITED\rDirector(s): NICHOLAS GREGG, ANDREW MILNER, ANDREW NELSON, MELVYN EWELL.\rTransport Managers: KEITH DONALD MANDALL, RICHARD STILLMAN', 'APPLEFORD ROAD, SUTTON COURTENAY , ABINGDON OX14 4PP\rOperating Centre: WHITTLESFORD MOTORWAY COMPOUND, 1 MILE EAST OF M11, ON NORTH SIDE OF A505 STATION CAMBRIDGE CB2 4NZ\rAuthorisation: 5 vehicle(s), 0 trailer(s)\rOperating Centre: BREAKSPEARS HIGHWAY COMPOUND, BREAKSPEAR WAY , HEMEL HEMPSTEAD HP2 4UE\rAuthorisation: 6 vehicle(s), 0 trailer(s)\rOperating Centre: UNIT 4, BEAMISH CLOSE , SANDY SG19 1SD\rAuthorisation: 10 vehicle(s), 0 trailer(s)\rOperating Centre: M1 MOTORWAY SERVICES AREA, NEWPORT PAGNALL, MK16 8DS\rAuthorisation: 6 vehicle(s), 0 trailer(s)\rThe Traffic Commissioner has given a direction under paragraph 2 of Schedule 4 that the above operating centre(s) shall be transferred from licence OF0201163, held by CARILLION CONSTRUCTION LTD, with the operating centre(s) being removed from that licence as part of this application.\rOperating Centre:BIRCHANGER MOTORWAY COMPOUND, NORTHBOUND M11, JUNCTION 8, START HILL, BISHOPS STORTFORD, CM22 7TA. \rAuthorisation: 8 vehicles 0 trailers.\rOperating centre: ARDLEIGH DEPOT, OLD IPSWICH ROAD, ARDLEIGH, COLCHESTER, CO7 7QL\rAuthorisation 8 vehicles o trailers.\rThe Traffic Commissioner has given a direction under paragraph 2 of Schedule 4 that the above operating centre(s) shall be transferred from licence OF1074524, held by SKANSKA CONSTRUCTION LTD, with the operating centre(s) being removed from that licence as part of this application.\rOperating Centres: M1 MAINTENANCE OFFICE SERVICE AREA, TODDINGTON DEPOT LU5 6HP\rAuthorisation 3 vehicles 0 trailer.\rFORMER VOLVO SITE, SADDLEBOW ROAD, SADDLEBOW, KINGS LYNN, PE30 5BN.\rAuthotisation: 7 vehicles 0 trailers', '2014-12-11 10:03:15', NULL, NULL, 1),
  (33, 1, 7, 1, 3, 16, NULL, NULL, NULL, 'B', NULL, 'OF1128725 SI', 'C G BULK HAULAGE LTD\rDirector(s): CAROLE ANN GLEGG, STEPHEN PATRICK COOPER.\rTransport Manager: STEPHEN PATRICK COOPER', '2 DOWESHILL CLOSE  BECCLES NR34 9XL\rOperating Centre: OC JEWERS & SONS LTD, NEW GRANARIES, WOOLPIT BURY ST. EDMUNDS IP30 9RH\rAuthorisation: 2 vehicle(s), 2 trailer(s)\rThe Traffic Commissioner has given a direction under paragraph 2 of Schedule 4 that the above operating centre(s) shall be transferred from licence OF1118189, held by C G BULK HAULAGE LTD, with the operating centre(s) being removed from that licence as part of this application, providing that the applicant demonstrates a plan of the operating centre showing separate and distinct parking areas.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (34, 1, 7, 1, 3, 6, NULL, NULL, NULL, 'B', NULL, 'OF1109512 R\r(5024)', 'BLACK EAGLE BREWERY LTD T/A TRUMAN\'S', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (35, 1, 7, 1, 3, 6, NULL, NULL, NULL, 'B', NULL, 'OF1043554 SN\r(5020)', 'J. SINGH TRANSPORT LTD', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (36, NULL, 7, NULL, 3, 13, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (48417) to be held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF, on 23 April 2014 commencing at 09:00(Previous Publication:(5024)\r)\rOF1108443          SN\rTANK AND CONTAINER SERVICES LIMITED\rDirector(s): JOANNE WOOTTEN.\rUNIT, 35B THURROCK COMMERCIAL CENTRE, PURFLEET INDUSTRIAL PARK, AVELEY, SOUTH OCKENDON RM15 4YA', 'GV - S26 - Consideration of disciplinary action under Section 26\rGV - S27 - Consideration of disciplinary action under Section 27\rGV - S28 - Consideration of disciplinary action under Section 28', NULL, '2014-11-25 15:47:03', NULL, NULL, 1),
  (37, NULL, 7, NULL, 3, 14, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (47359) held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF on 21 March 2014 at 10:00(Previous Publication:(5024)\r)OF0220645          SI\rDONALD DESMOND DODD & PARTNERS T/A D D DODD & SON\rPartner(s): JUNE MARY PAMELA DODD, PETER DODD, DONALD DESMOND DODD.\rTHE OLD WAGGON & HORSES, CHAPEL ST, SHIPDHAM THETFORD IP25 7LB', 'GV - S26 - Licence suspended for a period of 3 consecutive days with effect from 2359 hours on 2 May 2013 to 2359 hours on 5 May 2014.\rGV - S26(6) - Direction issued that vehicles may not be specified on another licence during period of suspension', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (38, NULL, 7, NULL, 3, 27, NULL, NULL, NULL, 'B', NULL, 'TM Public Inquiry (EpisodeId:2672 PublicInquiryId:1748) for BEN CHRISTOPHER NORTON to be held at The Court Room Eastern Traffic Area Eastbrook Shaftesbury Road Cambridge CB2 8BF, on 29 April 2014 commencing at 14:00 (Previous Publication:(6093))', 'Article 6 of Regulation (EC) No 1071/2009', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (39, NULL, 7, NULL, 3, 27, NULL, NULL, NULL, 'B', NULL, 'TM Public Inquiry (EpisodeId:2633 PublicInquiryId:1724) for MARIA CRISTINA LUTAC to be held at The Court Room Eastern Traffic Area Eastbrook Shaftesbury Road Cambridge CB2 8BF, on 01 May 2014 commencing at 10:00 (Previous Publication:(6093))', 'Article 6 of Regulation (EC) No 1071/2009', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (40, NULL, 7, NULL, 3, 28, NULL, NULL, NULL, 'B', NULL, 'TM Public Inquiry (EpisodeId:2283 DecisionId:1577) for RUTH PHILLIPS held at Jubilee House Croydon Street Bristol BS5 0GB, on 21 March 2014 at 13:30 (Previous Publication:(6093))', 'Declared Unfit under Article 6 of Regulation (EC) No 1071/2009', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (41, NULL, 7, NULL, 3, 28, NULL, NULL, NULL, 'B', NULL, 'TM Public Inquiry (EpisodeId:1986 DecisionId:1567) for STEVEN CLIVE HARROLD held at The Court Room Eastern Traffic Area Eastbrook Shaftesbury Road Cambridge CB2 8BF, on 25 March 2014 at 10:00 (Previous Publication:(6093))', 'Declared Unfit under Article 6 of Regulation (EC) No 1071/2009', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (42, NULL, 7, NULL, 3, 10, NULL, NULL, NULL, 'B', NULL, 'OF1018204 SN\r(5017)', 'Licence surrendered WEF 21 March 2014\rCCF LTD', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (43, NULL, 7, NULL, 3, 10, NULL, NULL, NULL, 'B', NULL, 'OF1089582 SI\r(4906)', 'Licence surrendered WEF 24 March 2014\rNORBERT DENTRESSANGLE UK LTD', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (44, NULL, 7, NULL, 3, 11, NULL, NULL, NULL, 'B', NULL, 'OF0100971 SN\r(5024)', 'Licence not continued WEF 28 March 2014\rTREVOR JOHN COTTIS T/A T J COTTIS TRANSPORT', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (45, NULL, 7, NULL, 3, 12, NULL, NULL, NULL, 'B', NULL, 'OF0231851 SI\r(4852)', 'B B HAULAGE LTD', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (46, NULL, 7, NULL, 3, 12, NULL, NULL, NULL, 'B', NULL, 'OF1100325 SI\r(5024)', 'B B TRANSPORT (EAST ANGLIA) LIMITED', NULL, '2014-12-11 10:03:15', NULL, NULL, 1),
  (47, 1, 7, 1, 4, 5, NULL, NULL, NULL, 'B', NULL, 'New applications refused text 1', 'New applications refused text 2', 'New applications refused text 3', '2014-12-11 10:03:15', NULL, NULL, 1),
  (48, 1, 7, 1, 4, 4, NULL, NULL, NULL, 'B', NULL, 'PF1134276 SR\r(Previous Publication not found)', 'DAVID SHEPPARD T/A ST MARY\'S GARAGE', '27 NORWICH ROAD, PULHAM ST. MARY , DISS IP21 4QU', '2014-12-11 10:03:15', NULL, NULL, 1),
  (49, 1, 7, 1, 4, 1, NULL, NULL, NULL, 'B', NULL, 'PF1136379 SI', 'JASON DE-VALL T/A STEVENAGE MINIBUS HIRE', '379 RIPON ROAD STEVENAGE SG1 4LU\rOperating Centre: SHANGRI-LA FARM, TODDS GREEN , STEVENAGE SG1 2JE Authorisation:2 Vehicle(s).\rTransport Manager(s): JASON DE-VALL', '2014-12-11 10:03:15', NULL, NULL, 1),
  (50, 1, 7, 1, 4, 1, NULL, NULL, NULL, 'B', NULL, 'PF1136501 SI', 'NOEL HENRY T/A GLENROSE LUXURY TRAVEL', '20 SONNING WAY, GLEN PARVA , LEICESTER LE2 9RU\rOperating Centre: UNIT 84, THE WHITTLE ESTATE, CAMBRIDGE ROAD, WHETSTONE LEICESTER LE8 6LH\rAuthorisation:1 Vehicle(s).\rTransport Manager(s): NOEL HENRY', '2014-12-11 10:03:15', NULL, NULL, 1),
  (51, 1, 7, 1, 4, 8, NULL, NULL, NULL, 'B', NULL, 'PF1129560 R', 'ACORN CHILDCARE LTD\rDirector(s): ZOE RAVEN.', '17 SOUTH STREET, CASTLETHORPE , MILTON KEYNES MK19 7EL\rNew Undertaking: Required to attend a DVSA Restricted Operator Seminar.. Attached to Licence.', '2014-12-11 10:03:15', NULL, NULL, 1),
  (52, 1, 7, 1, 4, 8, NULL, NULL, NULL, 'B', NULL, 'PF1014655 SN', 'BABCOCK AEROSPACE LIMITED\rDirector(s): RICHARD HEWITT TAYLOR, RICHARD DUNCAN STOATE, MICHAEL DAVID PARRY, ALBERT NORMAN DUNGATE, JOHN RICHARD DAVIES, KENNETH LESLIE CORNFIELD, Franco Martinelli.', 'AIRCRAFT HALL, RAF CRANWELL, RAUCEBY LANE, CRANWELL, SLEAFORD NG34 8GR\rRemoved operating centre: RAF BRAMPTON , WYTON, HUNTINGDON PE28 2EA\rNew operating centre: BABCOCK AEROSPACE, RAF WITTERING, WITTERING PETERBOROUGH PE8 6HB ()\rNew authorisation at this operating centre will be: 9 vehicle(s),', '2014-12-11 10:03:15', NULL, NULL, 1),
  (53, 1, 7, 1, 4, 6, NULL, NULL, NULL, 'B', NULL, 'PF1125753 R\r(2199)', 'HTT TRANSPORT LTD\rDirector(s): BALRAJ NOTAY, ANDREW JOHN MUSK.', '62A BRIDGE ROAD EAST WELWYN GARDEN CITY AL7 1JU', '2014-12-11 10:03:15', NULL, NULL, 1),
  (54, 1, 7, 1, 4, 6, NULL, NULL, NULL, 'B', NULL, 'PF1133870 R\r(2191)', 'WARREN ATHERTON', '120 ARAGLEN AVENUE SOUTH OCKENDON RM15 5DD', '2014-12-11 10:03:15', NULL, NULL, 1),
  (56, NULL, 110, NULL, 4, 24, 12, NULL, NULL, 'B', NULL, 'PF0000508/484', 'ARRIVA THE SHIRES LTD T/A ARRIVA THE SHIRES & ESSEX, CENTRAL ENGINEERING DEPT, 487 DUNSTABLE ROAD , LUTON LU4 8DS', 'Operating between Hemel Hempstead Railway Station and Hemel Hempstead Railway Station given service number ML1 effective from 29-Mar-2015. To amend Route and Timetable.', '2015-03-06 11:47:52', NULL, NULL, 1),
  (57, NULL, 110, NULL, 4, 24, 12, NULL, NULL, 'B', NULL, 'PF0001353/14', 'FLAGFINDERS (CTB) LTD, 267 COGGESHALL ROAD BRAINTREE CM7 9EF', 'Operating between Halstead and New Hall School given service number 64 effective from 23-Feb-2015. To amend Stopping Places.', '2015-03-06 11:47:52', NULL, NULL, 1),
  (58, NULL, 110, NULL, 4, 26, 12, NULL, NULL, 'B', NULL, 'PF0000508/48', 'ARRIVA THE SHIRES LTD T/A ARRIVA THE SHIRES & ESSEX, CENTRAL ENGINEERING DEPT, 487 DUNSTABLE ROAD , LUTON LU4 8DS', 'Operating between Hemel Hempstead Railway Station and Hemel Hempstead Railway Station given service number ML2 effective from 29-Mar-2015.', '2015-03-06 11:47:52', NULL, NULL, 1),
  (59, NULL, 110, NULL, 4, 26, 12, NULL, NULL, 'B', NULL, 'PF1018256/59', 'SANDERS COACHES LTD, HEATH FARM, HEMPSTEAD ROAD INDUSTRIAL ESTATE , HOLT NR25 6JU', 'Operating between EDGFIELD GREEN and FAKENHAM FIELD LANE given service number 304 effective from 29-Mar-2015.', '2015-03-06 11:47:52', NULL, NULL, 1),
  (60, NULL, 7, NULL, 4, 13, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (50286) to be held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF, on 27 February 2015 commencing at 10:00(Previous Publication:(2200)\r)\rPF1134484 SI\rVICEROY OF ESSEX LTD\rDirector(s): STEVEN ANDREW MOORE, AARON RICHARD MOORE.\r10-12 BRIDGE STREET SAFFRON WALDEN CB10 1BU', 'PSV - S14 - Consideration of new application under Section 14 (The Public Passenger Vehicles Act 1981)', NULL, '2015-03-06 11:47:52', NULL, NULL, 1),
  (61, NULL, 7, NULL, 4, 13, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (50262) to be held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF, on 17 March 2015 commencing at 10:00(Previous Publication:(2200)\r)\rPF1023898 R\rNICK JOHN ONLEY & DIANE JEAN WHITE T/A EASIBUS\rPartner(s): DIANE JEAN WHITE, NICK JOHN ONLEY.\r35 THE RUNDELS BENFLEET SS7 3QN', 'PSV - S17 - Consideration of disciplinary action under Section 17 (The Public Passenger Vehicles Act 1981)\rPSV - S28 - Consideration of disciplinary action under Section 28 (The Transport Act 1985)', NULL, '2015-03-06 11:47:52', NULL, NULL, 1),
  (62, NULL, 7, NULL, 4, 14, NULL, NULL, NULL, 'B', NULL, 'Public Inquiry (49473) held at The Court Room, Eastern Traffic Area, Eastbrook, Shaftesbury Road, Cambridge, CB2 8BF on 31 October 2014 at 10:00(Previous Publication:(2200)\r)PF0001624 SI\rVICEROY OF ESSEX LTD\rDIRECTOR(s): STEVEN ANDREW MOORE, AARON RICHARD MOORE.\r10 - 12 BRIDGE STREET SAFFRON WALDEN CB10 1BU', 'PSV - S17 - Licence revoked with effect from 20 March 2015.\rPSV - Sch.3 - Steven Moore found to be of good repute', NULL, '2015-03-06 11:47:52', NULL, NULL, 1),
  (63, NULL, 7, NULL, 4, 27, NULL, NULL, NULL, 'B', NULL, 'TM Public Inquiry (EpisodeId:3764 PublicInquiryId:2479) for COLIN RICHARD COLLINS to be held at The Court Room Eastern Traffic Area Eastbrook Shaftesbury Road Cambridge CB2 8BF, on 20 March 2015 commencing at 10:00 (Previous Publication:(6093))', 'Article 6 of Regulation (EC) No 1071/2009', NULL, '2015-03-06 11:47:52', NULL, NULL, 1);

INSERT INTO `publication_police_data` (`id`,`publication_link_id`,`person_id`,`created_by`,`last_modified_by`,`olbs_dob`,`birth_date`,`created_on`,`family_name`,`forename`,`last_modified_on`,`version`)
  VALUES
    (1,1,77,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:00:34','Jones','Tom',NULL,1),
    (2,1,78,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:00:35','Winnard','Keith',NULL,1),
    (3,2,77,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:02:18','Jones','Tom',NULL,1),
    (4,2,78,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:02:19','Winnard','Keith',NULL,1),
    (5,3,77,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:03:15','Jones','Tom',NULL,1),
    (6,3,78,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:03:16','Winnard','Keith',NULL,1);

INSERT INTO `recipient` (`id`, `send_app_decision`, `send_notices_procs`, `is_police`, `is_objector`, `contact_name`, `email_address`, `deleted_date`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`, `olbs_key`)
VALUES
  (1,0,0,1,0,'Police Recipient 1','terry.valtech+police-recipient1@gmail.com',NULL,NULL,NULL,NULL,NULL,1,NULL),
  (2,0,0,1,0,'Police Recipient 2','terry.valtech+police-recipient2@gmail.com',NULL,NULL,NULL,NULL,NULL,1,NULL),
  (3,0,0,0,0,'Non Police Recipient 1','terry.valtech+non-police-recipient1@gmail.com',NULL,NULL,NULL,NULL,NULL,1,NULL),
  (4,0,0,0,0,'Non Police Recipient 2','terry.valtech+non-police-recipient2@gmail.com',NULL,NULL,NULL,NULL,NULL,1,NULL);

INSERT INTO `recipient_traffic_area` (`recipient_id`, `traffic_area_id`)
VALUES
  (1,'B'),
  (1,'D'),
  (1,'G'),
  (1,'K'),
  (1,'N'),
  (2,'B'),
  (2,'C'),
  (2,'F'),
  (2,'H'),
  (2,'M'),
  (3,'B'),
  (3,'C'),
  (3,'D'),
  (3,'F'),
  (3,'G'),
  (3,'H'),
  (4,'C'),
  (4,'K'),
  (4,'M'),
  (4,'N');

INSERT INTO `irfo_gv_permit` (`id`, `organisation_id`, `irfo_gv_permit_type_id`, `irfo_permit_status`, `year_required`,
                              `in_force_date`, `expiry_date`, `no_of_copies`, `created_on`)
VALUES
    (1, 101, 17, 'irfo_perm_s_pending', 2015, '2015-03-10', '2016-03-09', 1, NOW()),
    (2, 101, 1, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 2, NOW()),
    (3, 101, 2, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 3, NOW()),
    (4, 101, 3, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 4, NOW()),
    (5, 101, 4, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 5, NOW()),
    (6, 101, 5, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 6, NOW()),
    (7, 101, 6, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 7, NOW()),
    (8, 101, 7, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 8, NOW()),
    (9, 101, 8, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 9, NOW()),
    (10, 101, 9, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 10, NOW()),
    (11, 101, 10, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 1, NOW()),
    (12, 101, 11, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 2, NOW()),
    (13, 101, 12, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 3, NOW()),
    (14, 101, 13, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 4, NOW()),
    (15, 101, 14, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 5, NOW()),
    (16, 101, 15, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 6, NOW()),
    (17, 101, 16, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 7, NOW()),
    (18, 101, 17, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 8, NOW()),
    (19, 101, 18, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 9, NOW()),
    (20, 101, 19, 'irfo_perm_s_approved', 2015, '2015-03-10', '2016-03-09', 10, NOW());

INSERT INTO `irfo_psv_auth` (`organisation_id`, `irfo_psv_auth_type_id`, `status`, `journey_frequency`,
                             `irfo_file_no`, `service_route_from`, `service_route_to`, `validity_period`,
                             `in_force_date`, `created_on`, `expiry_date`, `renewal_date`, `irfo_fee_id`)
VALUES
    (101, 4, 'irfo_auth_s_renew', 'psv_freq_daily', '17/2', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-29', '2016-10-19', 'FeeId'),
    (101, 2, 'irfo_auth_s_pending', 'psv_freq_daily', '19A/3', 'From', 'To', 1, '2015-10-30',  NOW(), '2016-10-19', '2016-10-19', 'FeeId'),
    (101, 3, 'irfo_auth_s_granted', 'psv_freq_daily', '18/4', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-01', '2016-10-19', 'FeeId'),
    (101, 4, 'irfo_auth_s_cns', 'psv_freq_daily', '19/5', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-01', '2016-10-19', 'FeeId'),
    (101, 5, 'irfo_auth_s_renew', 'psv_freq_daily', '19/5', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-01', '2016-10-19', 'FeeId'),
    (101, 6, 'irfo_auth_s_renew', 'psv_freq_daily', '20/5', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-01', '2016-10-19', 'FeeId'),
    (101, 7, 'irfo_auth_s_pending', 'psv_freq_daily', '21/6', 'From', 'To', 1, '2015-10-20',  NOW(), '2016-10-01', '2016-10-19', 'FeeId'),
    (101, 1, 'irfo_auth_s_approved', 'psv_freq_daily', '17/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 2, 'irfo_auth_s_approved', 'psv_freq_daily', '19A/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 3, 'irfo_auth_s_approved', 'psv_freq_daily', '18/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 4, 'irfo_auth_s_approved', 'psv_freq_daily', '19/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 5, 'irfo_auth_s_approved', 'psv_freq_daily', '19/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 6, 'irfo_auth_s_approved', 'psv_freq_daily', '20/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId'),
    (101, 7, 'irfo_auth_s_approved', 'psv_freq_daily', '21/1', 'From', 'To', 3, '2015-03-10',  NOW(), '2016-10-31', '2018-03-09', 'FeeId');

INSERT INTO `irfo_permit_stock` (`serial_no`, `irfo_country_id`, `status`, `valid_for_year`, `irfo_gv_permit_id`, `created_on`)
VALUES
    (1, 1, 'irfo_perm_s_s_in_stock', 2015, NULL, NOW()),
    (2, 1, 'irfo_perm_s_s_in_stock', 2015, NULL, NOW()),
    (3, 2, 'irfo_perm_s_s_in_stock', 2015, NULL, NOW()),
    (4, 2, 'irfo_perm_s_s_in_stock', 2016, NULL, NOW()),
    (5, 1, 'irfo_perm_s_s_in_stock', 2016, 1, NOW()),
    (6, 1, 'irfo_perm_s_s_ret', 2016, NULL, NOW());

INSERT INTO `community_lic` (
    `id`, `status`, `licence_id`, `expired_date`, `issue_no`, `serial_no`,
    `serial_no_prefix`, `specified_date`, `licence_expired_date`
) VALUES
    (1, 'cl_sts_active', 110, NULL, 0, NULL, 'UKGB', '2015-01-01', NULL),
    (2, 'cl_sts_active', 110, NULL, 1, NULL, 'UKGB', '2015-01-01', NULL),
    (3, 'cl_sts_expired', 110, '2014-01-01', 2, NULL, 'UKGB', '2015-01-01', NULL),
    (4, 'cl_sts_withdrawn', 110, NULL, 3, NULL, 'UKGB', NULL, NULL),
    (5, 'cl_sts_suspended', 110, NULL, 4, NULL, 'UKGB', '2015-01-01', NULL),
    (6, 'cl_sts_void', 110, '2014-09-20', 5, NULL, 'UKNI', '2015-01-01', NULL),
    (7, 'cl_sts_returned', 110, '2014-01-18', 6, NULL, 'UKNI', '2015-01-01', NULL),
    (8, 'cl_sts_pending', 110, NULL, 7, NULL, 'UKNI', NULL, NULL),
    (9, 'cl_sts_pending', 7, NULL, 7, NULL, 'UKNI', NULL, NULL),
    (10, 'cl_sts_pending', 7, NULL, 7, NULL, 'UKNI', NULL, NULL);

INSERT INTO `community_lic_suspension` (`id`, `community_lic_id`, `created_by`,
    `last_modified_by`, `is_actioned`, `created_on`, `end_date`, `last_modified_on`, `start_date`, `version`, `deleted_date`)
VALUES
	(1, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(2, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

INSERT INTO `community_lic_suspension_reason` (`id`, `community_lic_suspension_id`, `type_id`, `created_by`,
    `last_modified_by`, `created_on`, `deleted_date`, `last_modified_on`, `version`)
VALUES
	(1, 1, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1),
	(2, 2, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `community_lic_withdrawal` (`id`, `community_lic_id`, `created_by`, `last_modified_by`,
    `created_on`, `end_date`, `last_modified_on`, `start_date`, `version`, `deleted_date`)
VALUES
	(1, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(2, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

INSERT INTO `community_lic_withdrawal_reason` (`id`, `community_lic_withdrawal_id`, `type_id`,
    `created_by`, `last_modified_by`, `created_on`, `deleted_date`, `last_modified_on`, `version`)
VALUES
	(1, 1, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1),
	(2, 2, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `statement`
(`id`, `contact_type`, `requestors_contact_details_id`, `statement_type`, `case_id`, `created_by`,`last_modified_by`,
`authorisers_decision`, `stopped_date`, `requested_date`, `requestors_body`, `issued_date`,
`created_on`, `last_modified_on`, `version`, `vrm`)
VALUES
  (1, 'cm_letter', 120, 'statement_t_43', 24, 1, 1, 'Authorisers decision 1', '2014-05-01',
  '2014-01-01', 'Requestors body 1', '2014-01-08', '2013-01-01', '2013-01-02', 1, 'VRM 1'),
  (2, 'cm_fax', 121, 'statement_t_9', 24, 1, 1, 'Authorisers decision 2', '2014-06-02',
  '2014-02-02', 'Requestors body 2', '2014-01-09', '2013-01-02', '2013-01-03', 1, 'VRM 2'),
  (3, 'cm_email', 122, 'statement_t_ni', 24, 1, 1, 'Authorisers decision 3', '2014-07-03',
  '2014-03-03', 'Requestors body 3', '2014-01-10', '2013-01-03', '2013-01-04', 1, 'VRM 3'),
  (4, 'cm_tel', 123, 'statement_t_ni', 24, 1, 1, 'Authorisers decision 4', '2014-08-04',
  '2014-04-04', 'Requestors body 4', '2014-01-11', '2013-01-04', '2013-01-05', 1, 'VRM 4');

INSERT INTO `previous_conviction` (`id`, `conviction_date`, `transport_manager_id`, `category_text`, `notes`,
   `court_fpn`, `penalty`, `version`)
VALUES
  (1, '2014-10-30 10:00:00', 1, 'Offence 1', 'Offence 1 details', 'Court 1', 'Penalty 1', 1),
  (2, '2014-11-30 11:00:00', 1, 'Offence 2', 'Offence 2 details', 'Court 2', 'Penalty 2', 1),
  (3, '2012-10-30 10:00:00', 3, 'Offence 3', 'Offence 3 details', 'Court 3', 'Penalty 3', 1),
  (4, '2011-11-30 11:00:00', 3, 'Offence 4', 'Offence 4 details', 'Court 4', 'Penalty 4', 1);

INSERT INTO `trailer` (`id`, `created_by`, `last_modified_by`, `licence_id`, `trailer_no`, `created_on`,
  `deleted_date`, `last_modified_on`, `specified_date`, `version`)
VALUES
  (1, 1, 1, 7, "A0001", "2015-01-01", NULL, "2015-01-04", "2015-01-04", 1),
  (2, 1, 1, 7, "B0020", "2015-01-01", NULL, "2015-02-03", "2015-02-03", 1),
  (3, 1, 1, 7, "C0300", "2015-01-01", NULL, "2015-03-02", "2015-03-02", 1),
  (4, 1, 1, 7, "D4000", "2015-01-01", NULL, "2015-04-01", "2015-04-01", 1);

-- Start: Event History Test Data
INSERT INTO `event_history` (`id`, `event_history_type_id`, `application_id`, `bus_reg_id`, `case_id`, `licence_id`, `organisation_id`, `transport_manager_id`, `user_id`, `entity_pk`, `entity_type`, `entity_version`, `event_data`, `event_datetime`, `event_description`)
VALUES
	(8, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Event Data', '2015-03-24 11:02:49', 'Event Description 1'),
	(9, 1, NULL, NULL, NULL, 30, NULL, NULL, 273, 30, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 2'),
	(10, 1, NULL, NULL, NULL, 110, NULL, NULL, 273, 110, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 3'),
	(11, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 4'),
	(12, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 5'),
	(13, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 6'),
	(14, 1, NULL, NULL, NULL, 30, NULL, NULL, 273, 30, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 7'),
	(15, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 8'),
	(16, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 9'),
	(17, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 10'),
	(18, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 11'),
	(19, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 12'),
	(20, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 13'),
	(21, 1, NULL, NULL, NULL, 110, NULL, NULL, 273, 110, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 14'),
	(22, 1, NULL, NULL, NULL, 110, NULL, NULL, 273, 110, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 15'),
	(23, 1, NULL, NULL, NULL, 110, NULL, NULL, 273, 110, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 16'),
	(24, 1, NULL, NULL, NULL, 110, NULL, NULL, 273, 110, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 17'),
	(25, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 18'),
	(26, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 19'),
	(27, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 20'),
	(28, 1, NULL, NULL, NULL, 7, NULL, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 21'),
	(29, 1, NULL, NULL, NULL, 7, 1, NULL, 273, 7, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 22'),
	(39, 102, NULL, NULL, 29, NULL, 1, NULL, 273, 29, NULL, NULL, 'Case Event Data', '2015-03-24 11:02:49', 'Not used'),
	(30, 131, NULL, NULL, NULL, NULL, NULL, 1, 273, 1, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(33, 131, 1, NULL, NULL, NULL, NULL, 1, 273, 1, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(34, 131, 1, NULL, NULL, NULL, NULL, 1, 273, 1, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(35, 131, 1, NULL, NULL, NULL, 1, 1, 273, 1, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(36, 131, 1, NULL, NULL, NULL, 1, 1, 273, 1, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(40, 15, 7, NULL, NULL, NULL, NULL, NULL, 273, 7, NULL, NULL, 'Application Event Data', '2015-03-16 10:30:18', 'Event Description 40'),
	(41, 15, 7, NULL, NULL, NULL, NULL, NULL, 273, 7, NULL, NULL, 'Application Event Data', '2015-03-16 10:30:18', 'Event Description 41'),
	(42, 1001, NULL, 12, NULL, NULL, NULL, NULL, 273, 12, NULL, NULL, 'Bus Reg Event Data', '2015-03-16 10:30:18', 'Event Description 42'),
	(43, 1001, NULL, 12, NULL, NULL, NULL, NULL, 273, 12, NULL, NULL, 'Bus Reg Event Data', '2015-03-16 10:30:18', 'Event Description 43'),
	(44, 102, NULL, NULL, 74, NULL, 1, NULL, 273, 74, NULL, NULL, 'Case Event Data', '2015-03-24 11:02:49', 'Event description'),
	(45, 102, NULL, NULL, 74, NULL, 1, NULL, 273, 74, NULL, NULL, 'Case Event Data', '2015-03-24 11:02:49', 'Event description');
-- End: Event History Test Data

-- Start: Application 7 - new Goods Vehicle Standard National application ready to submit
BEGIN;
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1116,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1117,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1118,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:30:12',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1119,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (124,1116,'ct_corr',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,'dvsa@stolenegg.com',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (125,1117,'ct_est',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (126,1119,'ct_work',NULL,NULL,NULL,NULL,0,'2015-03-27 12:31:05',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `operating_centre` (`id`, `address_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `olbs_key`, `version`) VALUES (73,1118,NULL,NULL,'2015-03-27 12:30:12',NULL,NULL,1);
INSERT INTO `licence`
    (`id`, `correspondence_cd_id`, `enforcement_area_id`, `establishment_cd_id`, `organisation_id`,
    `tachograph_ins`, `transport_consultant_cd_id`, `created_by`, `goods_or_psv`, `last_modified_by`,
    `licence_type`, `status`, `traffic_area_id`, `fabs_reference`, `fee_date`, `psv_discs_to_be_printed_no`,
    `review_date`, `safety_ins`, `safety_ins_trailers`, `safety_ins_varies`, `safety_ins_vehicles`,
    `surrendered_date`, `tachograph_ins_name`, `trailers_in_possession`, `translate_to_welsh`, `created_on`, `deleted_date`,
    `expiry_date`, `granted_date`, `in_force_date`, `is_maintenance_suitable`, `last_modified_on`, `lic_no`, `olbs_key`,
    `tot_auth_trailers`, `tot_auth_vehicles`,
    `tot_community_licences`, `version`, `vi_action`)
    VALUES
    (211,124,NULL,125,1,
    'tach_internal',NULL,NULL,NULL,NULL,
    NULL,'lsts_not_submitted','B',NULL,NULL,NULL,
    NULL,0,1,0,1,
    NULL,'Dan',NULL,0,'2015-03-27 12:28:05',NULL,
    NULL,NULL,NULL,NULL,'2015-03-27 12:31:10','OB1',NULL,
    NULL,NULL,
    NULL,7,NULL);
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES (1,211);
INSERT INTO `application` (
    `id`, `interim_status`, `licence_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`,
    `withdrawn_reason`, `administration`, `bankrupt`, `convictions_confirmation`, `declaration_confirmation`, `disqualified`,
    `financial_evidence_uploaded`, `has_entered_reg`, `insolvency_confirmation`, `insolvency_details`, `interim_auth_trailers`,
    `interim_auth_vehicles`, `interim_end`, `interim_reason`, `interim_start`, `is_variation`, `liquidation`, `override_ooo`,
    `prev_been_at_pi`, `prev_been_disqualified_tc`, `prev_been_refused`, `prev_been_revoked`, `prev_conviction`, `prev_had_licence`,
    `prev_has_licence`, `prev_purchased_assets`, `psv_limousines`, `psv_medium_vhl_confirmation`, `psv_medium_vhl_notes`,
    `psv_no_limousine_confirmation`, `psv_no_small_vhl_confirmation`, `psv_only_limousines_confirmation`, `psv_operate_small_vhl`,
    `psv_small_vhl_confirmation`, `psv_small_vhl_notes`, `receivership`, `refused_date`, `safety_confirmation`, `target_completion_date`,
    `created_on`, `deleted_date`, `granted_date`, `is_maintenance_suitable`, `last_modified_on`, `ni_flag`, `received_date`,
    `tot_auth_trailers`, `tot_auth_vehicles`,
    `tot_community_licences`, `version`, `withdrawn_date`, `applied_via`)
VALUES (
    7,NULL,211,NULL,'lcat_gv',NULL,'ltyp_sn','apsts_not_submitted',
    NULL,0,0,1,1,0,0,0,1,'',NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,'2015-03-27 12:28:06',NULL,NULL,NULL,'2015-03-27 12:32:04',0,NULL,
    1,1,
    NULL,10,NULL,'applied_via_selfserve');
INSERT INTO `application_completion` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `last_section`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `undertakings_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (4,7,NULL,NULL,2,2,2,NULL,NULL,2,NULL,2,2,NULL,2,2,2,2,NULL,2,2,2,NULL,NULL,2,'2015-03-27 12:28:07','2015-03-27 12:32:04',19);
INSERT INTO `application_operating_centre` (`id`, `application_id`, `created_by`, `last_modified_by`, `operating_centre_id`, `s4_id`, `ad_placed`, `publication_appropriate`, `sufficient_parking`, `action`, `ad_placed_date`, `ad_placed_in`, `created_on`, `deleted_date`, `is_interim`, `last_modified_on`, `no_of_trailers_required`, `no_of_vehicles_required`, `olbs_key`, `permission`, `version`, `vi_action`) VALUES (4,7,NULL,NULL,73,NULL,0,0,1,'A',NULL,'','2015-03-27 12:30:12',NULL,0,NULL,1,1,NULL,1,1,NULL);
INSERT INTO `application_tracking` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `declarations_internal_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (4,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:28:07',NULL,1);
INSERT INTO `fee`
    (`id`,`fee_status`,`fee_type_id`,`application_id`,`licence_id`,`net_amount`,`vat_amount`,`gross_amount`,`invoiced_date`,`description`)
    VALUES
    (95,'lfs_ot',338,7,211,254.40,0.00,254.40,'2015-03-27 00:00:00','GV/SN Application Fee for application 7');
INSERT INTO `phone_contact` (`id`, `contact_details_id`, `phone_contact_type`, `created_by`, `last_modified_by`, `details`, `phone_number`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (111,124,'phone_t_tel',NULL,NULL,NULL,'01234 567890','2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `workshop` (`id`, `licence_id`, `contact_details_id`, `created_by`, `last_modified_by`, `is_external`, `maintenance`, `safety_inspection`, `created_on`, `last_modified_on`, `olbs_key`, `removed_date`, `version`) VALUES (1,211,126,NULL,NULL,0,0,0,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
COMMIT;
-- End: Application 7

-- Start: Application 8 - new Goods Vehicle Standard National application with tracking status completed (i.e. ready to grant)
BEGIN;
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1120,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1121,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1122,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:30:12',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (1123,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (127,1120,'ct_corr',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,'dvsa@stolenegg.com',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (128,1121,'ct_est',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (129,1123,'ct_work',NULL,NULL,NULL,NULL,0,'2015-03-27 12:31:05',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `operating_centre` (`id`, `address_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `olbs_key`, `version`) VALUES (74,1122,NULL,NULL,'2015-03-27 12:30:12',NULL,NULL,1);
INSERT INTO `licence` (
    `id`, `correspondence_cd_id`, `enforcement_area_id`, `establishment_cd_id`, `organisation_id`, `tachograph_ins`, `transport_consultant_cd_id`,
    `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`, `traffic_area_id`, `fabs_reference`, `fee_date`,
    `psv_discs_to_be_printed_no`, `review_date`, `safety_ins`, `safety_ins_trailers`, `safety_ins_varies`, `safety_ins_vehicles`,
    `surrendered_date`, `tachograph_ins_name`, `trailers_in_possession`, `translate_to_welsh`, `created_on`, `deleted_date`, `expiry_date`,
    `granted_date`, `in_force_date`, `is_maintenance_suitable`, `last_modified_on`, `lic_no`, `olbs_key`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`,
    `version`, `vi_action`)
VALUES
    (212,127,'NI01',127,1,'tach_internal',NULL,
    NULL,NULL,NULL,NULL,'lsts_consideration','N',NULL,NULL,
    NULL,NULL,0,1,0,1,
    NULL,'Dan',NULL,0,'2015-03-27 12:28:05',NULL,NULL,
    NULL,NULL,NULL,'2015-03-27 12:31:10','ON2',NULL,
    NULL,NULL,NULL,
    7,NULL);
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES (2,212);
INSERT INTO `application` (
    `id`, `interim_status`, `licence_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`,
    `withdrawn_reason`, `administration`, `bankrupt`, `convictions_confirmation`, `declaration_confirmation`, `disqualified`,
    `financial_evidence_uploaded`, `has_entered_reg`, `insolvency_confirmation`, `insolvency_details`,
    `interim_auth_trailers`, `interim_auth_vehicles`, `interim_end`, `interim_reason`, `interim_start`, `is_variation`, `liquidation`,
    `override_ooo`, `prev_been_at_pi`, `prev_been_disqualified_tc`, `prev_been_refused`, `prev_been_revoked`, `prev_conviction`, `prev_had_licence`,
    `prev_has_licence`, `prev_purchased_assets`, `psv_limousines`, `psv_medium_vhl_confirmation`, `psv_medium_vhl_notes`,
    `psv_no_limousine_confirmation`, `psv_no_small_vhl_confirmation`, `psv_only_limousines_confirmation`, `psv_operate_small_vhl`,
    `psv_small_vhl_confirmation`, `psv_small_vhl_notes`, `receivership`, `refused_date`, `safety_confirmation`, `target_completion_date`,
    `created_on`, `deleted_date`, `granted_date`, `is_maintenance_suitable`, `last_modified_on`, `ni_flag`, `received_date`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`,
    `version`, `withdrawn_date`, `applied_via`)
VALUES
    (8,NULL,212,NULL,'lcat_gv',NULL,'ltyp_sn','apsts_consideration',
    NULL,0,0,1,1,0,
    0,0,1,'',
    NULL,NULL,NULL,NULL,NULL,0,0,
    0,0,0,0,0,0,0,
    0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,0,NULL,1,NULL,
    '2015-03-27 12:28:06',NULL,NULL,NULL,'2015-03-27 12:32:04',1,'2015-03-27 12:34:56',
    1,1,NULL,
    10,NULL,'applied_via_selfserve');
INSERT INTO `application_completion` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `last_section`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `undertakings_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (5,8,NULL,NULL,2,2,2,NULL,NULL,2,NULL,2,2,NULL,2,2,2,2,NULL,2,2,2,NULL,NULL,2,'2015-03-27 12:28:07','2015-03-27 12:32:04',19);
INSERT INTO `application_operating_centre` (`id`, `application_id`, `created_by`, `last_modified_by`, `operating_centre_id`, `s4_id`, `ad_placed`, `publication_appropriate`, `sufficient_parking`, `action`, `ad_placed_date`, `ad_placed_in`, `created_on`, `deleted_date`, `is_interim`, `last_modified_on`, `no_of_trailers_required`, `no_of_vehicles_required`, `olbs_key`, `permission`, `version`, `vi_action`) VALUES (5,8,NULL,NULL,74,NULL,0,0,1,'A',NULL,'','2015-03-27 12:30:12',NULL,0,NULL,1,1,NULL,1,1,NULL);
INSERT INTO `application_tracking` (
    `id`, `application_id`, `created_by`, `last_modified_by`,
    `addresses_status`,
    `business_details_status`,
    `business_type_status`,
    `community_licences_status`,
    `conditions_undertakings_status`,
    `convictions_penalties_status`,
    `discs_status`,
    `financial_evidence_status`,
    `financial_history_status`,
    `licence_history_status`,
    `operating_centres_status`,
    `people_status`,
    `safety_status`,
    `taxi_phv_status`,
    `transport_managers_status`,
    `type_of_licence_status`,
    `declarations_internal_status`,
    `vehicles_declarations_status`,
    `vehicles_psv_status`,
    `vehicles_status`,
    `created_on`, `last_modified_on`, `version`)
VALUES (
    5,8,NULL,NULL,
    1,
    1,
    1,
    NULL,
    3,
    1,
    NULL,
    1,
    1,
    1,
    1,
    1,
    1,
    NULL,
    1,
    1,
    1,
    NULL,
    NULL,
    1,
    '2015-03-27 12:28:07',NULL,1
);

INSERT INTO `fee`
    (`id`,`fee_status`,`fee_type_id`,`application_id`,`licence_id`,`net_amount`,`vat_amount`,`gross_amount`,`invoiced_date`,`description`)
    VALUES
    (96,'lfs_pd',338,8,212,254.40,0.00,254.40,'2015-03-27 00:00:00','GV/SN Application Fee for application 8');
INSERT INTO `txn` (
    `id`,
    `reference`,
    `type`,
    `status`,
    `completed_date`,
    `payment_method`,
    `comment`,
    `waive_recommendation_date`,
    `waive_recommender_user_id`,
    `processed_by_user_id`
) VALUES
    (10010,'','trt_waive','pay_s_pd','2015-08-26','fpm_waive','Waive OK for fee 96','2015-08-25',1,291);
INSERT INTO `fee_txn`
    (`fee_id`,`txn_id`,`amount`)
    VALUES
    (96,10010,254.40);
INSERT INTO `phone_contact` (`id`,    `contact_details_id`, `phone_contact_type`, `created_by`, `last_modified_by`, `details`, `phone_number`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (112,127,'phone_t_tel',NULL,NULL,NULL,'01234 567890','2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `workshop` (`id`, `licence_id`, `contact_details_id`, `created_by`, `last_modified_by`, `is_external`, `maintenance`, `safety_inspection`, `created_on`, `last_modified_on`, `olbs_key`, `removed_date`, `version`) VALUES (2,212,129,NULL,NULL,0,0,0,'2015-03-27 12:31:05',NULL,NULL,NULL,1);

COMMIT;
-- End: Application 8

INSERT INTO `change_of_entity` (`id`, `licence_id`, `old_licence_no`, `old_organisation_name`, `created_on`, `version`)
VALUES
    ('1', '7', '0000000', 'Old Organisation Name', '2015-03-27 12:28:07', '1');

INSERT INTO `inspection_request` (`id`, `report_type`, `request_type`, `requestor_user_id`, `result_type`, `application_id`,
`case_id`, `created_by`, `last_modified_by`, `licence_id`, `operating_centre_id`, `task_id`, `deferred_date`, `due_date`, `from_date`,
`inspector_name`, `inspector_notes`, `request_date`, `requestor_notes`, `return_date`, `to_date`, `trailers_examined_no`,
`vehicles_examined_no`, `created_on`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, 'insp_rep_t_maint', 'insp_req_t_coe', 2, 'insp_res_t_new', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-01', NULL, NULL,
    NULL, '2015-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 'insp_rep_t_bus', 'insp_req_t_comp', 2, 'insp_res_t_new_sat', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-02', NULL, NULL,
    NULL, '2015-01-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(3, 'insp_rep_t_TE', 'insp_req_t_new_op', 2, 'insp_res_t_new_unsat', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-03', NULL, NULL,
    NULL, '2015-01-03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `grace_period` (`id`, `created_by`, `last_modified_by`, `licence_id`, `description`, `end_date`, `start_date`, `created_on`,
    `last_modified_on`, `olbs_key`)
    VALUES
      (1, 1, 1, 7, "Grace period description 1", '2015-08-01', '2015-05-01', '2015-04-01', '2015-05-01', NULL),
      (2, 1, 1, 7, "Grace period description 2", '2015-09-01', '2015-06-01', '2015-05-01', '2015-06-01', NULL),
      (3, 1, 1, 7, "Grace period description 3", '2015-10-01', '2015-07-01', '2015-06-01', '2015-07-01', NULL),
      (4, 1, 1, 7, "Grace period description 4", '2015-11-01', '2015-08-01', '2015-07-01', '2015-08-01', NULL);

INSERT INTO `companies_house_company` (
    `id`,
    `address_line_1`,
    `address_line_2`,
    `company_name`,
    `company_number`,
    `company_status`,
    `country`,
    `locality`,
    `po_box`,
    `postal_code`,
    `premises`,
    `region`
)
VALUES
    (1,'Address line 1','Address line 2','JOHN SMITH HAULAGE LIMITED','12345678','active',NULL,'Leeds',NULL,'LS9 6NF',NULL,NULL),
    (2,'Address line 1','Address line 2','TEDDIE STOBBART GROUP LIMITED','67567533','active',NULL,'Leeds',NULL,'LS9 6NF',NULL,NULL);

INSERT INTO `companies_house_alert` (`id`, `organisation_id`, `company_or_llp_no`, `created_on`, `is_closed`)
VALUES
    (1, 1, '12345678', '2015-07-21 15:05:58', 0),
    (2, 41, '67567533', '2015-07-21 15:05:59', 0),
    (3, 41, '67567533', '2015-07-21 15:03:59', 1);

INSERT INTO `companies_house_alert_reason` (`id`, `companies_house_alert_id`, `reason_type`)
VALUES
    (1, 1, 'company_status_change'),
    (2, 1, 'company_people_change'),
    (3, 2, 'company_address_change'),
    (4, 3, 'company_name_change');

-- organisation, licence and contact details for some unlicensed operators --
INSERT INTO `organisation` (`id`,`lead_tc_area_id`,`name`,`type`,`is_unlicensed`) VALUES
    (106,'B','Test Unlicensed Goods Operator','org_t_pa',1),
    (107,'B','Test Unlicensed PSV Operator','org_t_pa',1);
INSERT INTO `licence` (`id`,`correspondence_cd_id`,`organisation_id`,`goods_or_psv`,`licence_type`,`status`,`lic_no`) VALUES
    (701,145,106,'lcat_gv','ltyp_r','lsts_unlicenced','UOB3'),
    (702,145,107,'lcat_psv','ltyp_r','lsts_unlicenced','UOB4');
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES
    (4,701),
    (5,702);
INSERT INTO `contact_details` (`id`,`address_id`,`contact_type`,`email_address`) VALUES
    (145,1124,'ct_corr','unlicensed@foo.bar'),
    (146,1125,'ct_corr','unlicensed@foo.bar');
INSERT INTO `address` (`id`,`saon_desc`,`paon_desc`,`street`,`locality`,`town`,`postcode`,`country_code`) VALUES
    (1124,'Address Line 1','Address Line 2','Address Line 3','Address Line 4','Address Line 5','LS9 6NF','GB'),
    (1125,'Address Line 1','Address Line 2','Address Line 3','Address Line 4','Address Line 5','LS9 6NF','GB'),
    (1126,'Address Line 1','Address Line 2','Address Line 3','Address Line 4','Address Line 5','LS9 6NF','GB');
INSERT INTO `phone_contact` (`id`,`contact_details_id`,`phone_contact_type`,`phone_number`) VALUES
    (113,145,'phone_t_tel','012345'),
    (114,145,'phone_t_home','012346'),
    (115,145,'phone_t_mobile','012347'),
    (116,145,'phone_t_fax','012348'),
    (117,146,'phone_t_tel','012345'),
    (118,146,'phone_t_home','012346'),
    (119,146,'phone_t_mobile','012347'),
    (120,146,'phone_t_fax','012348');
-- vehicles for unlicensed operators
INSERT INTO `vehicle` (`id`,`vrm`,`plated_weight`,`created_on`) VALUES
    (9,'ABC123','750','2015-07-16'),
    (10,'ABC124','895','2015-07-17'),
    (11,'ABC125',NULL,'2015-07-16'),
    (12,'ABC126',NULL,'2015-07-17'),
    (13,'ABC127',NULL,'2015-07-18');

INSERT INTO `licence_vehicle` (`licence_id`,`vehicle_id`) VALUES
    (701, 9),
    (701, 10),
    (702, 11),
    (702, 12),
    (702, 13);

-- OLCS-10506 organisation, licence and contact details for submission test data --
INSERT INTO `organisation` (`id`,`lead_tc_area_id`,`name`,`type`,`is_unlicensed`,`nature_of_business`) VALUES
  (130,'B','Big Trucks Ltd','org_t_rc', 0, 'Traffic Management');

INSERT INTO `organisation_person` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
                                   `person_id`, `organisation_id`) VALUES
  (9,NULL,NULL,NULL,NULL,1,82,130),
  (10,NULL,NULL,NULL,NULL,1,84,130);

INSERT INTO `licence` (`id`,`correspondence_cd_id`,`organisation_id`,`goods_or_psv`,`licence_type`,`status`,`lic_no`,
                       `traffic_area_id`) VALUES
  (99,181,130,'lcat_gv','ltyp_r','lsts_consideration','OB111111', 'B');

INSERT INTO `contact_details` (`id`,`address_id`,`contact_type`,`email_address`) VALUES
  (181,1283,'ct_corr','contact@bigtrucksltd.com');

INSERT INTO `address` (`id`,`saon_desc`,`paon_desc`,`street`,`locality`,`town`,`postcode`,`country_code`) VALUES
  (1283,'Address Line 1','Address Line 2','Address Line 3','Address Line 4','Address Line 5','LS9 6NF','GB');

INSERT INTO `phone_contact` (`id`,`contact_details_id`,`phone_contact_type`,`phone_number`) VALUES
  (130,181,'phone_t_tel','012345'),
  (131,181,'phone_t_home','012346'),
  (132,181,'phone_t_mobile','012347'),
  (133,181,'phone_t_fax','012348');

-- OLCS-10506 vehicles
INSERT INTO `vehicle` (`id`,`vrm`,`plated_weight`,`created_on`) VALUES
  (20,'ABC123','750','2015-07-16'),
  (21,'ABC124','895','2015-07-17'),
  (22,'ABC125',NULL,'2015-07-16'),
  (23,'ABC126',NULL,'2015-07-17'),
  (24,'ABC127',NULL,'2015-07-18');

INSERT INTO `licence_vehicle` (`licence_id`,`vehicle_id`) VALUES
  (99, 20),
  (99, 21),
  (99, 22),
  (99, 23),
  (99, 24);

-- OLCS-10506 Cases
INSERT INTO `cases` (`id`,`case_type`,`licence_id`,`application_id`,`transport_manager_id`,
                     `last_modified_by`,`created_by`,`ecms_no`,`open_date`,`closed_date`,`description`,`is_impounding`,
                     `annual_test_history`,`prohibition_note`,
                     `conviction_note`,`penalties_note`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (85,'case_t_lic',99,NULL,NULL,NULL,NULL,'E123456','2012-03-21',NULL,'New application for Operating Centre',0,
'Annual test history for case 99','prohibition test notes','Nothing found',NULL,NULL,
   '2013-11-12 12:27:33',NULL,1);

-- OLCS-10506 comments
INSERT INTO `submission_section_comment` (`id`, `created_by`, `last_modified_by`, `submission_id`, `submission_section`, `comment`, `created_on`, `last_modified_on`, `version`) VALUES
  (1,NULL,NULL,1,'case-outline','<p><strong>APPLICATION TYPE:</strong></p><br><p>GV79 - Conditions at operating centre</p>','2015-10-05 15:51:20',NULL,1),
  (2,NULL,NULL,1,'conviction-fpn-offence-history','<p>Nothing found</p>','2015-10-05 15:51:20','2015-10-05 16:28:23',3),
  (3,NULL,NULL,1,'introduction','<h1><strong>ENVIRONMENTAL SUBMISSION</strong></h1>
<p><strong>Not for disclosure to any third parties without the specific consent of the traffic commissioner</strong></p>','2015-10-05 15:52:45','2015-10-05 15:55:23',5),
  (4,NULL,NULL,1,'previous-history','<h2><strong>RELEVANT SITE HISTORY:</strong></h2>\n<p>The condition that vehicles will not be left unattended on the A64 York Road has been attached to licences in the past as larger vehicles using the site had to wait on the by-pass if the gates to the operating centre were not manned at the time of their arrival. The conditions related to larger vehicles, and their operating hours were restricted to the time that the gate was manned for safety reasons.</p>\n<p>In 2008 the gates were moved back which allowed any size of authorised vehicle to pull off the road whilst the gates were unattended.</p>\n<p>It was decided that the site should still be subject to the waiting restriction on the by-pass. Operators at the site were flagged for review and in January 2011 all operators at the site became subject to this condition.</p>\n<p>In 2012 two applications from Rob’s Transport Ltd (OB999999, no longerat the site) and John Smith Transport Ltd were granted within several days of each other. Due to the large number of additional vehicles the Traffic Commissioner requested that a TE revisit the site to assess capacity. The TE reported that the land owner allocates each operator a specific number of parking spaces so there is always room for vehicles to safely manoeuvre within the site. The John Smith Transport Ltd application (for 15 vehicles and 15 trailers) was granted subject to the restrictions above.</p>\n<p>Rob’s Transport Ltd had an additional condition undertaking attached in respect of the parking area and the Traffic Commissioner decided that parking plans should be obtained and form part of the authorised o/c description for each operator that came up for review and for new applications. Since then applications granted at the site have specified two conditions and an undertaking in respect of the parking area. The most recent application was granted in March 2015 with the same 2 conditions and undertaking attached.</p>\n<p>As part of the John’s Trucks application it was noted that some of the operators at the site were not parking in the areas detailed in their undertaking. This included the parking area for this applicant as it was the same are as specified on OB2222222 Trucky Truck Transport Services Ltd.</p>\n<p>That has now been resolved as the parking plan for Trucky Truck Transport Services Ltd has been updated under Section 17(4)(f) of the Act to take the form of the master plan provided by the site owner, this is the same as the plan this applicant has provided.</p>','2015-10-05 16:00:39','2015-10-05 16:11:08',2),
  (5,NULL,NULL,1,'local-licence-history','<p>No previous licence history</p>','2015-10-05 16:12:29',NULL,1),
  (6,NULL,NULL,1,'environmental-complaints','<p>We have not received any opposition</p>','2015-10-05 16:14:50',NULL,1),
  (7,NULL,NULL,1,'statements','<p><strong>APPLICANT’S COMMENTS:</strong> </p>\n<p>The applicant has confirmed they would agree to the restrictions in place at the operating centre being attached to the licence, if required by the Traffic Commissioner.</p>','2015-10-05 16:19:10',NULL,1),
  (8,NULL,NULL,1,'maps','<p>Ariel view of the operating centre:</p>','2015-10-05 16:20:07',NULL,1),
  (9,NULL,NULL,1,'advertisement','<p>31 October 2015, Yorkshire Evening Post - The advert is in time, correctly worded and the newspaper circulates in the vicinity of the operating centre.</p>','2015-10-05 16:24:05',NULL,1),
  (10,NULL,NULL,1,'financial-information','<p>£18,400 is required. </p>\n<p>Financial evidence provided shows access to more than the amount required to support the application.</p>','2015-10-05 16:24:30',NULL,1),
  (11,NULL,NULL,1,'fitness-and-repute','<p>Nothing found</p>','2015-10-05 16:25:43',NULL,1),
  (12,NULL,NULL,1,'interim','<p>Interim authority has been requested.</p>\n<p>OOR    21 Nov 2014</p>\n<p>OOO    14 Jan 2015</p>','2015-10-05 16:30:07',NULL,1),
  (13,NULL,NULL,1,'maintenance-tachographs-hours','<p><strong>APPLICANT’S RESPONSES:</strong></p>\n<p><strong>Hours of Operation:</strong></p>\n<p>Monday to Friday:       0800 - 1700    </p>\n<p>Saturday:   
                 no operation               </p>\n<p>Sunday:                      no operation   </p>\n<p>           </p>\n<p><strong>Hours of Maintenance: </strong></p>\n<p>Maintenance work will not be carried out at the ope
rating centre.</p>','2015-10-05 16:31:33',NULL,1),
  (14,NULL,NULL,1,'conditions-and-undertakings','<p><strong>PRESENT USAGE OF O/C:</strong></p>
<p>There are 2 operators currently for this operating centre and 2 ongoing applications:</p>
<p>All the operators are subject to restrictions, the ones detailed below have all the relevant restrictions for this operating centre and there are 4 with only one condition, these have been flagged for review.</p>
<p> </p>
<p><strong>OB1234567     John Smith Transport Ltd                                10V, 10T</strong></p>
<p>Conditions:</p>
<ol><li><strong>Authorised vehicles shall not be left unattended on the A64 York road any time. </strong></li>
<li><strong>Authorised vehicles and trailers must at all times enter and exit the operating centre in a forward gear</strong></li>
</ol><p> Undertaking:</p>
<ol><li><strong>Authorised vehicles and trailers must only be parked in the designated area identified on the plan -
See John Smith Transport Ltd parking area attachement below.</strong></li>
</ol><p> </p>
<p> </p>
<p><strong>OH1021000     Little Trucks Ltd                                               4V, 0T</strong></p>
<p>Conditions:</p>
<ol><li><strong>Authorised vehicles shall not be left unattended on the A64 York Road at any time. </strong></li>
<li><strong>Vehicles authorised under this licence will enter and leave the operating centre in forward gear only.</strong></li>
</ol><p>Undertaking:</p>
<ol><li><strong>The operator will keep vehicles and trailers in that part of Hillcrest House marked “Parking Area” on the plan agreed by the traffic commissioner and not on any other part of Hillcrest House. See Little Trucks Ltd parking area attachement below.</strong></li>
</ol>
<p>The following 3 operators are only subject to the condition detailed below, they have been flagged for review:</p>
<ol><li><strong>Authorised vehicles shall not be left unattended on the A64 Yor Road at any time</strong>.</li>
</ol><p>OB6666666     Steve’s Trucks                         2v 0t    (Review Nov 2015)</p>
<p>OH7777777     John’s Trucks                          6v 0t    (Review Dec 2015)</p>
<p>OH8888888     Dave’s Trucks Limited            12v 2t  (Review Sep 2016)</p>','2015-10-05 16:14:50',NULL,1),
  (16,NULL,NULL,1,'intelligence-unit-check','<p><i>I have completed all IU checks necessary for this new application. There are no matches with the intelligence database to suggest that additional action is required. I am therefore returning the matter to you to process in the normal manner.</i></p>','2015-10-05 16:38:15',NULL,1),
  (17,NULL,NULL,1,'case-summary', '<p><strong>ECMS CHECK:</strong></p>
  <p>No issues</p>','2015-10-05 15:51:20',NULL,1),
  (18,NULL,NULL,1,'annex', '<p><strong>Hours of Operation:</strong></p>
<p>Monday to Friday:       0800 - 1700    </p>
<p>Saturday:                    no operation               </p>
<p>Sunday:                       no operation   </p>
<p>           </p>
<p><strong>Hours of Maintenance: </strong></p>
<p>Maintenance work will not be carried out at the operating centre.</p>','2015-10-05 15:51:20',NULL,1);

-- OLCS-10506 add Operating centre details

INSERT INTO `address` (`id`,`country_code`,`saon_desc`,`paon_desc`, `postcode`, `town`)
VALUES (1284, 'GB', 'Hillcrest House', '386 Harehills Lane', 'LS9 6NF', 'Leeds');

INSERT INTO `operating_centre` (`id`,`address_id`) VALUES ('75', '1284');

INSERT INTO `licence_operating_centre` (`id`,`licence_id`,`operating_centre_id`,`ad_placed`,`ad_placed_date`,
  `ad_placed_in`,`no_of_trailers_required`,`no_of_vehicles_required`, `permission`, `sufficient_parking`)
VALUES (5, 99, 75, 1, '2015-10-31', 'Yorkshire Evening Post', 0, 10, 1, 1);

-- OLCS-10506 conditions and undertakings
INSERT INTO `condition_undertaking` (`id`, `added_via`, `application_id`, `approval_user_id`, `attached_to`, `case_id`, `condition_type`, `created_by`, `last_modified_by`, `lic_condition_variation_id`, `licence_id`, `operating_centre_id`, `s4_id`, `action`, `created_on`, `deleted_date`, `is_draft`, `is_fulfilled`, `last_modified_on`, `notes`, `olbs_key`, `olbs_type`, `version`) VALUES
  (12,'cav_case',NULL,NULL,'cat_oc',85,'cdt_con',NULL,NULL,NULL,99,75,NULL,NULL,'2015-10-06 08:52:32','2015-10-06 08:53:41',0,1,NULL,'Authorised vehicles shall not be left unattended on the A64 York road any time.',NULL,NULL,2),(13,'cav_lic',NULL,NULL,'cat_oc',NULL,'cdt_con',NULL,NULL,NULL,99,75,NULL,NULL, '2015-10-06 08:54:06',NULL,0,1,'2015-10-06 08:56:12','Authorised vehicles shall not be left unattended on the A64 York road any time.',NULL,NULL,2),(14,'cav_lic',NULL,NULL,'cat_oc',NULL,'cdt_con',NULL,NULL,NULL,99,75,NULL,NULL,'2015-10-06 08:55:09',NULL,0,1,NULL,'Authorised vehicles and trailers must at all times enter and exit the operating centre in a forward gear',NULL,NULL,1),
  (15,'cav_lic',NULL,NULL,'cat_oc',NULL,'cdt_und',NULL,NULL,NULL,99,75,NULL,NULL,'2015-10-06 08:57:18',NULL,0,0,NULL, 'Authorised vehicles and trailers must only be parked in the designated area identified on the plan.',NULL,NULL,1),
  (16,'cav_lic',NULL,NULL,'cat_oc',NULL,'cdt_und',NULL,NULL, NULL,99,75,NULL,NULL,'2015-10-06 08:58:48',NULL,0,1,
   NULL,'The operator will keep vehicles and trailers in that part of Hillcrest House marked "Parking Area" on the plan agreed by the traffic commissioner and not on any other part of Hillcrest House.',NULL,NULL,1);

-- OLCS-10506 test submission
INSERT INTO `submission` (`id`,`case_id`, `created_by`, `last_modified_by`, `submission_type`, `created_on`,
                          `data_snapshot`, `last_modified_on`, `version`)
VALUES
  ('1', '85', 3, 3, 'submission_type_o_env', '2015-10-05 15:51:20', '{"introduction":{"data":[]},
  "case-summary":{"data":{"overview":{"id":85,"caseType":"Licence","ecmsNo":"E123456","organisationName":"Big Trucks Ltd","isMlh":false,"organisationType":"Limited Company","businessType":"Traffic Management",
  "disqualificationStatus":"None","licNo":"OB111111","licenceStartDate":"","licenceType":"Restricted",
  "goodsOrPsv":"Goods Vehicle","licenceStatus":"Under Consideration","totAuthorisedVehicles":10,
  "totAuthorisedTrailers":null,"vehiclesInPossession":10,"trailersInPossession":null,"serviceStandardDate":""}}},"case-outline":{"data":{"text":"New application for Operating Centre"}},"most-serious-infringement":{"data":{"overview":{"id":"","notificationNumber":"","siCategory":"","siCategoryType":"","infringementDate":"","checkDate":"","isMemberState":""}}},"outstanding-applications":{"data":{"tables":{"outstanding-applications":[]}}},"people":{"data":{"tables":{"people":[{"id":82,"title":"Mr","familyName":"Fox","forename":"Steve","birthDate":"1994-04-15"},{"id":84,"title":"Mr","familyName":"Jowitt","forename":"Phil","birthDate":"1994-04-15"}]}}},"operating-centres":{"data":{"tables":{"operating-centres":[{"id":75,"version":1,"totAuthVehicles":10,"totAuthTrailers":0,"OcAddress":{"addressLine1":"Hillcrest House","addressLine2":"386 Harehills Lane","addressLine3":null,"addressLine4":null,"town":"Leeds","postcode":"LS9 6NF","countryCode":"GB"}}]}}},"conditions-and-undertakings":{"data":{"tables":{"undertakings":[{"id":16,"version":1,"createdOn":"2015-10-06T08:58:48+0100","parentId":"OB111111","addedVia":"Licence","isFulfilled":"Y","isDraft":"N","attachedTo":"Operating Centre","notes":"The operator will keep vehicles and trailers in that pa\nrt of Hillcrest House marked \u201cParking Area\u201d on the plan agreed by the traffic commissioner and not on any other part of Hillcrest House.","OcAddress":{"addressLine1":"Hillcrest House","addressLine2":"386 Harehills Lane","addressLine3":null,"addressLine4":null,"town":"Leeds","postcode":"LS9 6NF","countryCode":"GB"}},{"id":15,"version":1,"createdOn":"2015-10-06T08:57:18+0100","parentId":"OB111111","addedVia":"Licence","isFulfilled":"N","isDraft":"N","attachedTo":"Operating Centre","notes":"Authorised vehicles and trailers must only be parked in the designated area identified on the plan.","OcAddress":{"addressLine1":"Hillcrest House","addressLine2":"386 Harehills Lane","addressLine3":null,"addressLine4":null,"town":"Leeds","postcode":"LS9 6NF","countryCode":"GB"}}],"conditions":[{"id":14,"version":1,"createdOn":"2015-10-06T08:55:09+0100","parentId":"OB111111","addedVia":"Licence","isFulfilled":"Y","isDraft":"N","attachedTo":"Operating Centre","notes":"Authorised vehicles and trailers must at all times enter and exit the operating centre in a forward gear","OcAddress":{"addressLine1":"Hillcrest House","addressLine2":"386 Harehills Lane","addressLine3":null,"addressLine4":null,"town":"Leeds","postcode":"LS9 6NF","countryCode":"GB"}},{"id":13,"version":2,"createdOn":"2015-10-06T08:54:06+0100","parentId":"OB111111","addedVia":"Licence","isFulfilled":"Y","isDraft":"N","attachedTo":"Operating Centre","notes":"Authorised vehicles shall not be left unattended on the\n A64 York road any time.","OcAddress":{"addressLine1":"Hillcrest House","addressLine2":"386 Harehills Lane","addressLine3":null,"addressLine4":null,"town":"Leeds","postcode":"LS9 6NF","countryCode":"GB"}}]}}},"intelligence-unit-check":{"data":[]},"interim":{"data":[]},"advertisement":{"data":[]},"auth-requested-applied-for":{"data":{"tables":{"auth-requested-applied-for":[]}}},"transport-managers":{"data":{"tables":{"transport-managers":[]}}},"continuous-effective-control":{"data":[]},"fitness-and-repute":{"data":[]},"previous-history":{"data":[]},"local-licence-history":{"data":[]},"conviction-fpn-offence-history":{"data":{"tables":{"conviction-fpn-offence-history":[]},"text":"test comments"}},"other-issues":{"data":[]},"te-reports":{"data":[]},"site-plans":{"data":[]},"planning-permission":{"data":[]},"applicants-comments":{"data":[]},"visibility-access-egress-size":{"data":[]},"environmental-complaints":{"data":{"tables":{"environmental-complaints":[]}}},"oppositions":{"data":{"tables":{"oppositions":[]}}},"financial-information":{"data":[]},"maps":{"data":[]},"annex":{"data":[]}}', '2015-10-05 15:51:20', 1);

-- OLCS-10506 submission section files
INSERT INTO `document` (`id`, `application_id`, `bus_reg_id`, `case_id`, `category_id`, `created_by`,
                        `irfo_organisation_id`, `last_modified_by`, `licence_id`, `operating_centre_id`, `sub_category_id`, `submission_id`, `traffic_area_id`, `transport_manager_id`, `created_on`, `deleted_date`, `description`, `filename`, `document_store_id`, `is_external`, `is_read_only`, `is_scan`, `issued_date`, `last_modified_on`, `metadata`, `olbs_key`, `olbs_type`, `size`, `version`) VALUES
  (809,NULL,NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,148,1,NULL,NULL,'2015-10-06 08:43:20',NULL, 'site-plan-parking-area.png','documents/Submission/Site_plans/2015/10/20151006084312__site-plan-parking-area.png','documents/Submission/Site_plans/2015/10/20151006084312__site-plan-parking-area.png',0,NULL,0,'2015-10-06 08:43:20','2015-10-06 08:43:20',NULL,NULL,NULL,388800,2),
  (810,NULL,NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,122,1,NULL,NULL,'2015-10-06 09:00:15',NULL,
   'John-Smith-Transport-Ltd-parking-area.png',
   'documents/Submission/Conditions_and_undertakings/2015/10/20151006090008__parking-area-undertaking.png','documents/Submission/Conditions_and_undertakings/2015/10/20151006090008__parking-area-undertaking.png',0,NULL,0,'2015-10-06 09:00:15','2015-10-06 09:00:15',NULL,NULL,NULL,112024,2),
  (811,NULL,NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,122,1,NULL,NULL,'2015-10-06 09:01:41',NULL,
   'Little-Trucks-Ltd-parking-area.png','documents/Submission/Conditions_and_undertakings/2015/10
   /20151006090135__parking-area-undertaking2.png','documents/Submission/Conditions_and_undertakings/2015/10/20151006090135__parking-area-undertaking2.png',0,NULL,0,'2015-10-06 09:01:41','2015-10-06 09:01:41',NULL,NULL,NULL,64316,2),
  (812,NULL,NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,156,1,NULL,NULL,'2015-10-06 09:01:41',NULL,'ariel-view-operating-centre.png','documents/Submission/Maps/2015/10/20151006142959__ariel-view-operating-centre.png','documents/Submission/Maps/2015/10/20151006142959__ariel-view-operating-centre.png',0,NULL,0,'2015-10-06 09:01:41','2015-10-06 09:01:41',NULL,NULL,NULL,342832,2);

-- OLCS-10506 recommendation / decision
INSERT INTO `submission_action` (`id`, `created_by`, `last_modified_by`, `submission_id`, `comment`, `created_on`, `is_decision`, `last_modified_on`, `version`) VALUES
  (3,NULL,NULL,1,'CASE WORKER RECOMMENDATION & LEGISLATION:\r\n\r\nThe applicant has provided a site plan that confirms they are not parking in any area which is supposed to be occupied by any other operator.\r\n\r\nI therefore recommend the application is granted under Section 13 of the Goods Vehicle (Licensing of Operators) Act 1995 with the following conditions and undertaking attached:\r\n\r\nConditions under section 21 of the act\r\n\r\n1.	Authorised vehicles shall not be left unattended on the A64 York Road at any time\r\n2.	Vehicles authorised under this licence will enter and leave the operating centre in forward gear only\r\n\r\nUndertaking under section 13C(7) of the Act\r\n\r\nThe Operator will keep vehicles in that part of Hillcrest House marked \"Parking Area\" on the plan agreed by the Traffic Commissioner and not on any other part of Hillcrest House.','2015-10-06 13:38:32',0,'2015-10-06 13:41:23',2),
  (4,NULL,NULL,1,'Team Leaders Recommendation\r\n\r\nI agree with Steve’s recommendation.\r\n\r\nBob Submission\r\n01/05/2015','2015-10-06 13:39:40',0,NULL,1),
  (5,NULL,NULL,1,'TRAFFIC COMMISSIONER’S DECISION:\r\n\r\nPlease proceed as recommended for the reasons you give.\r\nSB/TC/13 5 15','2015-10-06 13:42:17',1,NULL,1);
INSERT INTO `submission_action_reason` (`submission_action_id`, `reason_id`) VALUES (3,122),(4,122),(5,122);

-- Start: Application 9 - new Goods Vehicle Standard National application ready to submit
BEGIN;
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (216,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (217,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (218,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:30:12',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (219,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (224,216,'ct_corr',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,'dvsa@stolenegg.com',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (225,217,'ct_est',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (226,219,'ct_work',NULL,NULL,NULL,NULL,0,'2015-03-27 12:31:05',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `operating_centre` (`id`, `address_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `olbs_key`, `version`) VALUES (273,218,NULL,NULL,'2015-03-27 12:30:12',NULL,NULL,1);
INSERT INTO `licence`
    (`id`, `correspondence_cd_id`, `enforcement_area_id`, `establishment_cd_id`, `organisation_id`,
    `tachograph_ins`, `transport_consultant_cd_id`, `created_by`, `goods_or_psv`, `last_modified_by`,
    `licence_type`, `status`, `traffic_area_id`, `fabs_reference`, `fee_date`, `psv_discs_to_be_printed_no`,
    `review_date`, `safety_ins`, `safety_ins_trailers`, `safety_ins_varies`, `safety_ins_vehicles`,
    `surrendered_date`, `tachograph_ins_name`, `trailers_in_possession`, `translate_to_welsh`, `created_on`, `deleted_date`,
    `expiry_date`, `granted_date`, `in_force_date`, `is_maintenance_suitable`, `last_modified_on`, `lic_no`, `olbs_key`,
    `tot_auth_trailers`, `tot_auth_vehicles`,
    `tot_community_licences`, `version`, `vi_action`)
    VALUES
    (213,224,NULL,225,1,
    'tach_internal',NULL,NULL,NULL,NULL,
    NULL,'lsts_not_submitted','B',NULL,NULL,NULL,
    NULL,0,1,0,1,
    NULL,'Dan',NULL,0,'2015-03-27 12:28:05',NULL,
    NULL,NULL,NULL,NULL,'2015-03-27 12:31:10','OB3',NULL,
    NULL,NULL,
    NULL,7,NULL);
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES (3,213);
INSERT INTO `application` (
    `id`, `interim_status`, `licence_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`,
    `withdrawn_reason`, `administration`, `bankrupt`, `convictions_confirmation`, `declaration_confirmation`, `disqualified`,
    `financial_evidence_uploaded`, `has_entered_reg`, `insolvency_confirmation`, `insolvency_details`, `interim_auth_trailers`,
    `interim_auth_vehicles`, `interim_end`, `interim_reason`, `interim_start`, `is_variation`, `liquidation`, `override_ooo`,
    `prev_been_at_pi`, `prev_been_disqualified_tc`, `prev_been_refused`, `prev_been_revoked`, `prev_conviction`, `prev_had_licence`,
    `prev_has_licence`, `prev_purchased_assets`, `psv_limousines`, `psv_medium_vhl_confirmation`, `psv_medium_vhl_notes`,
    `psv_no_limousine_confirmation`, `psv_no_small_vhl_confirmation`, `psv_only_limousines_confirmation`, `psv_operate_small_vhl`,
    `psv_small_vhl_confirmation`, `psv_small_vhl_notes`, `receivership`, `refused_date`, `safety_confirmation`, `target_completion_date`,
    `created_on`, `deleted_date`, `granted_date`, `is_maintenance_suitable`, `last_modified_on`, `ni_flag`, `received_date`,
    `tot_auth_trailers`, `tot_auth_vehicles`,
    `tot_community_licences`, `version`, `withdrawn_date`, `applied_via`)
VALUES (
    9,NULL,213,NULL,'lcat_gv',NULL,'ltyp_sn','apsts_not_submitted',
    NULL,0,0,1,1,0,0,0,1,'',NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,'2015-03-27 12:28:06',NULL,NULL,NULL,'2015-03-27 12:32:04',0,NULL,
    1,1,
    NULL,10,NULL,'applied_via_selfserve');
INSERT INTO `application_completion` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `last_section`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `undertakings_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (6,9,NULL,NULL,2,2,2,NULL,NULL,2,NULL,2,2,NULL,2,2,2,2,NULL,2,2,2,NULL,NULL,2,'2015-03-27 12:28:07','2015-03-27 12:32:04',19);
INSERT INTO `application_operating_centre` (`id`, `application_id`, `created_by`, `last_modified_by`, `operating_centre_id`, `s4_id`, `ad_placed`, `publication_appropriate`, `sufficient_parking`, `action`, `ad_placed_date`, `ad_placed_in`, `created_on`, `deleted_date`, `is_interim`, `last_modified_on`, `no_of_trailers_required`, `no_of_vehicles_required`, `olbs_key`, `permission`, `version`, `vi_action`) VALUES (6,9,NULL,NULL,273,NULL,0,0,1,'A',NULL,'','2015-03-27 12:30:12',NULL,0,NULL,1,1,NULL,1,1,NULL);
INSERT INTO `application_tracking` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `declarations_internal_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (6,9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:28:07',NULL,1);
INSERT INTO `fee`
    (`id`,`fee_status`,`fee_type_id`,`application_id`,`licence_id`,`net_amount`,`vat_amount`,`gross_amount`,`invoiced_date`,`description`)
    VALUES
    (201,'lfs_ot',344,9,213,100.00,0.00,100.00,'2015-03-27 00:00:00','Interim Fee for application 9'),
    (202,'lfs_ot',338,9,213,100.00,0.00,100.00,'2015-03-27 00:00:00','Application Fee for application 9');
INSERT INTO `phone_contact` (`id`, `contact_details_id`, `phone_contact_type`, `created_by`, `last_modified_by`, `details`, `phone_number`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (211,224,'phone_t_tel',NULL,NULL,NULL,'01234 567890','2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `workshop` (`id`, `licence_id`, `contact_details_id`, `created_by`, `last_modified_by`, `is_external`, `maintenance`, `safety_inspection`, `created_on`, `last_modified_on`, `olbs_key`, `removed_date`, `version`) VALUES (3,213,226,NULL,NULL,0,0,0,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
COMMIT;
-- End: Application 9

INSERT INTO `printer` (`id`, `printer_name`, `description`) VALUES (1, 'TESTING-STUB-LICENCE:7','Test Default Printer');
INSERT INTO `printer` (`id`, `printer_name`) VALUES (2, 'Test Printer');

INSERT INTO `team_printer` (`id`, `version`, `team_id`, `printer_id`, `sub_category_id`, `user_id`) VALUES (1, 1, 13, 1, NULL, NULL);
INSERT INTO `team_printer` (`id`, `version`, `team_id`, `printer_id`, `sub_category_id`, `user_id`) VALUES (2, 1, 13, 2, 1, 1);


/* Test document sla target dates */
INSERT IGNORE INTO sla_target_date(id,document_id,agreed_date, target_date, sent_date, under_delegation, notes,
                                   created_by, last_modified_by, created_on, version) VALUES
  (1,682,'2014-08-25 12:04:35','2014-08-30 12:04:35','2014-08-27 12:04:35', 1,'Passed SLA target',273, 273,
   '2014-08-25 12:04:35', 1),
  (2,672,'2014-08-25 12:04:35','2014-09-23 12:04:35','2014-09-27 12:04:35', 1,'Failed SLA target',273, 273,
     '2014-08-25 12:04:35', 1),
  (3,673,'2014-08-25 12:04:35','2014-08-30 12:04:35','2014-08-27 12:04:35', 1,'Passed SLA target',273, 273,
     '2014-08-25 12:04:35', 1),
  (4,674,'2014-08-25 12:04:35','2014-08-30 12:04:35','2014-08-27 12:04:35', 1,'Passed SLA target',273, 273,
     '2014-08-25 12:04:35', 1);

INSERT INTO `contact_details` (`id`,`contact_type`,`address_id`,`person_id`,
   `last_modified_by`,`created_by`,`fao`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
    (230,'ct_user',1,91,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (231,'ct_user',1,92,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (232,'ct_user',1,93,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (233,'ct_user',1,94,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (234,'ct_user',1,95,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (235,'ct_user',1,96,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (236,'ct_user',1,97,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (237,'ct_user',1,98,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (238,'ct_user',1,99,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (239,'ct_user',1,100,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (240,'ct_user',1,101,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1);

SET foreign_key_checks = 1;
