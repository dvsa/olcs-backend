SET foreign_key_checks = 0;

TRUNCATE TABLE `address`;
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
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `change_of_entity`;
TRUNCATE TABLE `disc_sequence`;
TRUNCATE TABLE `event_history_type`;
TRUNCATE TABLE `event_history`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `fee`;
TRUNCATE TABLE `hint_question`;
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
TRUNCATE TABLE `organisation_nature_of_business`;
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
TRUNCATE TABLE `previous_conviction`;
TRUNCATE TABLE `psv_disc`;
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
TRUNCATE TABLE `task_allocation_rule`;
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
TRUNCATE TABLE `sla`;
TRUNCATE TABLE `statement`;
TRUNCATE TABLE `submission_action`;
TRUNCATE TABLE `system_parameter`;
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

/* Test documents */
INSERT IGNORE INTO document(id,licence_id,bus_reg_id,description,filename,is_digital,category_id,sub_category_id,
file_extension, issued_date,document_store_id) VALUES
    (672,7,null,'Test document digital','testdocument2.doc',1,1,1,'doc_doc','2014-08-25 12:04:35',''),
    (673,7,null,'Test document 3','testdocument3.doc',0,1,2,'doc_doc','2014-08-22 11:01:00',''),
    (674,7,null,'Test document 4','testdocument4.doc',0,2,3,'doc_doc','2014-08-24 16:23:00',''),
    (675,7,null,'Test document 5','testdocument5.xls',0,2,3,'doc_xls','2014-07-01 15:01:00',''),
    (676,7,null,'Test document 6','testdocument6.docx',0,2,3,'doc_docx','2014-07-05 09:00:05',''),
    (677,7,null,'Test document 7','testdocument7.xls',0,2,4,'doc_xls','2014-07-05 10:23:00',''),
    (678,7,null,'Test document 8','testdocument8.doc',1,2,4,'doc_doc','2014-07-05 10:45:00',''),
    (679,7,null,'Test document 9','testdocument9.ppt',1,2,4,'doc_ppt','2014-08-05 08:59:40',''),
    (680,7,null,'Test document 10','testdocument10.jpg',0,1,2,'doc_jpg','2014-08-08 12:47:00',''),
    (681,7,null,'Test document 11','testdocument11.txt',0,1,1,'doc_txt','2014-08-14 14:00:00',''),
    (682,7,null,'Test document 12','testdocument12.xls',1,1,2,'doc_xls','2014-08-28 14:03:00',''),

    (800,110,2,'Test bus transxchange','transxchange.zip',1,3,107,'doc_zip','2014-08-28 14:03:00',''),
    (801,110,2,'Test bus transxchange PDF','transxchange.pdf',1,3,108,'doc_pdf','2014-08-28 14:03:00',''),
    (802,110,2,'Test bus route','route.jpg',1,3,36,'doc_jpg','2014-08-28 14:03:00',''),

    (803,110,2,'Test bus transxchange for LA 2','transxchange_LA2.zip',1,3,107,'doc_zip',
    '2014-08-28 14:03:00',''),
    (804,110,2,'Test bus transxchange PDF for LA 2','transxchange_LA2.pdf',1,3,108,'doc_pdf',
    '2014-08-28 14:03:00',
    ''),
    (805,110,2,'Test bus route for LA 2','route_LA2_Org1.jpg',1,3,36,'doc_jpg','2014-08-28 14:03:00',''),

    (806,110,2,'Test bus transxchange for LA 1','transxchange_LA1.zip',1,3,107,'doc_zip',
    '2014-08-28 14:03:00',''),
    (807,110,2,'Test bus transxchange PDF for LA 1','transxchange_LA1.pdf',1,3,108,'doc_pdf',
    '2014-08-28 14:03:00',''),
    (808,110,2,'Test bus route for LA 1','route_LA1.jpg',1,3,36,'doc_jpg','2014-08-28 14:03:00','');

INSERT INTO txc_inbox (id, pdf_document_id, route_document_id, zip_document_id, bus_reg_id, created_by,
local_authority_id, organisation_id, file_read, variation_no, created_on) VALUES
(1, 801, 802, 800, 2, 1, NULL, 1, 0, 13, '2014-03-24 16:53:00'),
(2, 804, 805, 803, 2, 1, 2, 1, 0, 14, '2014-03-24 16:53:00'),
(3, 807, 808, 806, 2, 1, 1, 1, 0, 15, '2014-03-24 16:53:00');

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
    (75,NULL,NULL,'','123 A Street','An Area','','LS12 1BB','Leeds','GB',NOW(),NOW(),1),
    (76,NULL,NULL,'Unit 5','10 High Street','','','LS9 6NA','Leeds','GB',NOW(),NOW(),1),
    (100,NULL,NULL,'Test Partnership LLP','10 Partnerships street','PartnershipDistrict','Partnership Land','PA7 5IP',
    'Leeds','GB',NOW(),NOW(),1),
    (104,NULL,NULL,'Unit 9','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (105,NULL,NULL,'Unit 1','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (106,NULL,NULL,'Unit 2','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (107,NULL,NULL,'Unit 3','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (108,NULL,NULL,'Unit 4','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (109,NULL,NULL,'A Place','123 Some Street','Some Area','','WM5 2FA','Birmingham','GB',NOW(),NOW(),1),
    (110,NULL,NULL,'Park Cottage','Coldcotes Avenue','','','LS9 6NE','Leeds','GB',NOW(),NOW(),1),
    (111,NULL,NULL,'Unit 4','Shapely Industrial Estate','Harehills','','LS9 2FA','Leeds','GB',NOW(),NOW(),1),
    (112,NULL,NULL,'A Place','123 Some Street','Some Area','','WM5 2FA','Birmingham','GB',NOW(),NOW(),1),
    (113,NULL,NULL,'Solway Business Centre','Kingstown','','','CA6 4BY','Carlisle','GB',NOW(),NOW(),1),
    (114,NULL,NULL,'Unit 10','10 High Street','Alwoodley','','LS7 9SD','Leeds','GB',NOW(),NOW(),1),
    (115,NULL,NULL,'123 House','A Street','An Area','','LS12 1BB','Leeds','GB',NOW(),NOW(),1);

INSERT INTO `application` (
    `id`, `licence_id`, `created_by`, `last_modified_by`, `status`,
    `tot_auth_vehicles`, `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_large_vehicles`, `tot_community_licences`,
    `tot_auth_trailers`, `bankrupt`, `liquidation`, `receivership`, `administration`,
    `disqualified`, `insolvency_details`, `received_date`,
    `target_completion_date`, `prev_conviction`, `created_on`, `last_modified_on`,
    `version`, `is_variation`, `goods_or_psv`, `ni_flag`, `licence_type`,
    `interim_status`, `interim_reason`, `interim_start`, `interim_end`, `interim_auth_vehicles`, `interim_auth_trailers`
) VALUES
    (
        1,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2010-12-15 10:48:00',
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, 'ltyp_si',
        'int_sts_requested', 'Interim reason', '2014-01-01', '2015-01-01', 10, 20
    ),
    (
        2,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,NULL,NULL,
        1,1,'lcat_gv',0, NULL,
        NULL,NULL,NULL,NULL,NULL,NULL
    ),
    (
        3,210,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, NULL,
        NULL,NULL,NULL,NULL,NULL,NULL
    ),
    (
        6,114,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,'2014-04-30 12:09:37','2014-04-30 12:09:39',
        1,0,'lcat_psv',1,NULL,
        NULL,NULL,NULL,NULL,NULL,NULL
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
  (15, 2, 14, 'breg_s_var', 'bs_no', 1, 1, 110, 'breg_s_cancellation', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/2', 'Route description change 6', 2, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 6, 'York', NULL, '2014-03-10', NULL, NULL, '46474', 1);

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

INSERT INTO `oc_complaint` (`id`, `complaint_id`, `operating_centre_id`)
VALUES
    (1, 7, 16),
    (2, 7, 21),
    (3, 7, 37),
    (4, 9, 39);

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
    (8,24,NULL,37,NULL,NULL,'cav_case','cat_oc','cdt_und',NULL,0,1,'Some invoice_notes 8',NOW(),NULL,1),
    (9,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 9',NOW(),NULL,1),
    (10,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 10',NOW(),NULL,1),
    (11,24,7,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 11',NOW(),NULL,1),
    (12,75,110,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 12',NOW(),NULL,1),
    (13,75,110,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 13',NOW(),NULL,1),
    (14,75,110,NULL,NULL,NULL,'cav_case','cat_lic','cdt_con',NULL,0,0,'Some notes 14',NOW(),NULL,1),
    (15,75,110,NULL,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 15',NOW(),NULL,1),
    (16,75,110,48,NULL,NULL,'cav_case','cat_oc','cdt_con',NULL,0,0,'Some notes 16',NOW(),NULL,1);

INSERT INTO `contact_details` (`id`,`contact_type`,`address_id`,`person_id`,
   `last_modified_by`,`created_by`,`fao`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
    (1,'ct_ta',26,NULL,2,0,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (3,'ct_corr',109,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (8,'ct_corr',8,10,2,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (21,'ct_reg',21,NULL,0,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (25,'ct_def',25,NULL,4,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (26,'ct_def',26,NULL,0,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (27,'ct_def',27,NULL,2,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (29,'ct_def',29,NULL,3,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (30,'ct_reg',30,NULL,2,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (31,'ct_corr',31,NULL,0,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (37,'ct_oc',37,NULL,2,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (39,'ct_oc',39,NULL,4,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (41,'ct_reg',41,NULL,2,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (42,'ct_corr',42,NULL,1,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (54,'ct_reg',54,NULL,4,2,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (55,'ct_corr',55,NULL,3,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (63,'ct_reg',63,NULL,3,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (64,'ct_corr',64,NULL,0,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (67,'ct_oc',67,NULL,4,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (72,'ct_oc',72,NULL,2,4,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (75,'',75,NULL,4,3,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (76,'ct_corr',76,46,4,1,'Important Person',0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (77,'ct_corr',72,46,4,1,'Important Person',0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (100,'ct_reg',100,44,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (101,'ct_team_user',26,4,4,1,NULL,0,'loggedin@user.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (102,'ct_corr',41,NULL,1,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (103,'ct_complainant',72,46,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (104,'ct_tm',110,NULL,1,1,NULL,0,'one@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (105,'ct_team_user',26,81,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (106,'ct_team_user',26,82,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (107,'ct_complainant',72,33,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (108,'ct_complainant',72,34,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (109,'ct_complainant',72,35,4,1,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),

    (110,'ct_complainant',26,60,4,1,NULL,0,'l.hamilton@mercedes.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (111,'ct_complainant',26,65,4,1,NULL,0,'j.smith@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (112,'ct_complainant',26,66,4,1,NULL,0,'t.cooper@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (113,'ct_complainant',26,77,4,1,NULL,0,'t.jones@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (114,'ct_team_user',26,NULL,4,1,NULL,0,'another@user.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (115,'ct_tm',111,NULL,1,1,NULL,0,'two@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (116,'ct_tm',112,NULL,1,1,NULL,0,'three@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (117,'ct_tm',113,65,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (118,'ct_tm',114,66,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (119,'ct_tm',115,77,1,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (120,'ct_corr',105,4,1,1,NULL,0,'some1@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (121,'ct_corr',106,9,1,1,NULL,0,'some2@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (122,'ct_corr',107,10,1,1,NULL,0,'some3@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (123,'ct_corr',108,11,1,1,NULL,0,'some4@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (130,'ct_team_user',26,83,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (131,'ct_team_user',26,84,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (132,'ct_team_user',26,85,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (133,'ct_team_user',26,86,4,1,NULL,0,'terry.valtech@gmail.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (140,'ct_partner',7,NULL,1,1,NULL,0,NULL,'HMRC',NULL,'2000-04-02 00:00:00',NULL,1),
    (141,'ct_partner',7,NULL,1,1,NULL,0,NULL,'DVSA',NULL,'2000-04-02 00:00:00',NULL,1),
    (142,'ct_partner',7,NULL,1,1,NULL,0,NULL,'Police',NULL,'2000-04-02 00:00:00',NULL,1),
    (143,'ct_partner',7,NULL,1,1,NULL,0,NULL,'Department of Work and Pensions',NULL,'2000-04-02 00:00:00',
    NULL,1),
    (144,'ct_partner',7,NULL,1,1,NULL,0,NULL,'Home Office',NULL,'2000-04-02 00:00:00',NULL,1);

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
    `application_classification`, `variation_no`, `tan_code`, `registration_no`, `validation_start`, `validation_end`,
    `publish_start`, `publish_end`, `process_start`, `process_end`, `distribute_start`, `distribute_end`,
    `distribute_expire`, `is_from_ftp`, `organisation_id`) VALUES
  (1, null, 'ebsrt_new', 'ebsrs_processing', 1, '2015-04-11 15:25:34', 'PB12351', null, null, 1, null, '1111', null,
  null, null, null, null, null, null, null,null, 0, null),
  (2, null, 'ebsrt_new', 'ebsrs_processing', 2, '2015-04-15 23:25:34', 'PB12352', null, null, 2, null, '1112', null, null, null, null, null,
  null, null, null,null, 0, null),
  (3, null, 'ebsrt_refresh', 'ebsrs_submitted', 3, '2015-03-11 15:25:34', 'PB12353', null, null, 3, null, '1113',
  null, null, null, null, null, null, null, null,null, 0, null),
  (4, null, 'ebsrt_refresh', 'ebsrs_expired', 4, '2015-02-21 12:35:34', 'PB12354', null, null, 4, null, '1114', null,
  null, null, null, null, null, null, null,null, 0, null),
  (5, null, 'ebsrt_unknown', 'ebsrs_validated', 5, '2015-02-14 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),

  (6, null, 'ebsrt_new', 'ebsrs_processing', 6, '2013-01-14 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (7, null, 'ebsrt_refresh', 'ebsrs_validated', 7, '2013-08-24 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (8, null, 'ebsrt_unknown', 'ebsrs_expired', 99, '2011-09-14 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (9, null, 'ebsrt_new', 'ebsrs_processing', 99, '2009-11-14 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (10, null, 'ebsrt_refresh', 'ebsrs_validated', 5, '2015-01-04 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (11, null, 'ebsrt_unknown', 'ebsrs_validated', 3, '2014-09-30 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (12, null, 'ebsrt_refresh', 'ebsrs_processing', 3, '2006-06-07 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null),
  (13, null, 'ebsrt_new', 'ebsrs_validated', 1, '2010-05-05 11:55:32', 'PB12355', null, null, 5, null, '1115',
  null, null, null, null, null, null, null, null,null, 0, null)

  ;

INSERT INTO `fee` (`id`, `application_id`, `licence_id`, `bus_reg_id`, `fee_status`, `receipt_no`, `created_by`, `last_modified_by`, `description`,
    `invoiced_date`, `received_date`, `amount`, `received_amount`, `created_on`, `last_modified_on`, `version`, `payment_method`, `waive_reason`, `fee_type_id`) VALUES
    (7,NULL,7,NULL,'lfs_ot',NULL,1,NULL,'Application fee','2013-11-25 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (30,NULL,110,NULL,'lfs_pd','654321',1,2,'Application fee','2013-11-22 00:00:00','2014-01-13 00:00:00',251.00,251.00,NULL,NULL,1,'fpm_card_online',NULL,1),
    (41,NULL,110,NULL,'lfs_wr','345253',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (54,NULL,110,NULL,'lfs_ot','829485',1,NULL,'Application fee','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (63,NULL,110,NULL,'lfs_ot','481024',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (75,NULL,110,NULL,'lfs_ot','964732',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (76,1,110,NULL,'lfs_wr','234343',1,NULL,'Application fee 1','2013-11-25 00:00:00',NULL,250.50,0.50,NULL,NULL,2,NULL,NULL,1),
    (77,1,110,NULL,'lfs_wr','836724',1,NULL,'Application fee 2','2013-11-22 00:00:00',NULL,251.75,0.00,NULL,NULL,2,NULL,NULL,1),
    (78,1,110,NULL,'lfs_wr','561023',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,3,NULL,NULL,1),
    (79,1,110,NULL,'lfs_wr','634820',1,NULL,'Application fee 3','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (80,1,110,NULL,'lfs_pd','458750',1,2,'Application fee 4','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash',NULL,1),
    (81,1,110,NULL,'lfs_ot','837495',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (82,1,30,NULL,'lfs_ot','354784',1,NULL,'Bus route 1','2013-10-23 00:00:00',NULL,500.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (83,1,110,NULL,'lfs_wr','435235',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (84,1,110,NULL,'lfs_ot','435563',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (85,1,110,NULL,'lfs_wr','534633',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (86,1,110,NULL,'lfs_ot','426786',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (87,1,110,NULL,'lfs_w','68750',1,2,'Application fee 6','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash','some waive reason',1),
    (88,1,110,NULL,'lfs_cn','78750',1,2,'Application fee 7','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_card_online',NULL,1),
    (89,3,210,NULL,'lfs_w', '87654',1,2,'Application fee 8','2013-11-10 00:00:00','2015-01-09 00:00:00',254.40,254.40,NULL,NULL,1,'fpm_waive','waived for demo purposes',1),
    (90,1,110,3,'lfs_ot',NULL,1,NULL,'Bus Route Application Fee PD2737280/3 Variation 0','2013-10-23 00:00:00',NULL,60.00,0.00,NULL,NULL,1,NULL,NULL,188),
    (91,1,110,8,'lfs_ot',NULL,1,NULL,'Bus Route Variation Fee PD2737280/3 Variation 1','2013-10-23 00:00:00',NULL,60.00,0.00,NULL,NULL,1,NULL,NULL,189),
    (92,1,110,9,'lfs_ot',NULL,1,NULL,'Bus Route Variation Fee PD2737280/3 Variation 2','2013-10-23 00:00:00',NULL,60.00,0.00,NULL,NULL,1,NULL,NULL,189),
    (93,1,110,10,'lfs_ot',NULL,1,NULL,'Bus Route Variation Fee PD2737280/3 Variation 3','2013-10-23 00:00:00',NULL,60.00,0.00,NULL,NULL,1,NULL,NULL,189),
    (94,1,110,11,'lfs_ot',NULL,1,NULL,'Bus Route Variation Fee PD2737280/3 Variation 4','2013-10-23 00:00:00',NULL,60.00,0.00,NULL,NULL,1,NULL,NULL,189),
    (97,NULL,NULL,NULL,'lfs_ot',NULL,1,NULL,'Photocopying charge','2015-04-01 12:34:56',NULL,123.45,0.00,NULL,NULL,1,NULL,NULL,20051),
    (98,NULL,NULL,NULL,'lfs_ot',NULL,1,NULL,'Court fee','2015-04-01 12:34:56',NULL,123.45,0.00,NULL,NULL,1,NULL,NULL,20052);

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `enforcement_area_id`, `created_by`, `correspondence_cd_id`, `establishment_cd_id`,
    `transport_consultant_cd_id`, `last_modified_by`,
    `goods_or_psv`, `lic_no`, `status`,
    `ni_flag`, `licence_type`, `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`,
    `safety_ins_vehicles`, `safety_ins_trailers`, `safety_ins_varies`,
    `tachograph_ins`, `tachograph_ins_name`, `created_on`, `last_modified_on`, `version`, `expiry_date`, `tot_community_licences`)
VALUES
    (7,1,'B','V048', 1,102,NULL,104,NULL,'lcat_gv','OB1234567','lsts_valid',0,'ltyp_si','2010-01-12','2010-01-12','2010-01-12',
    '',4,12,NULL,NULL,NULL, NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),

    -- extra licence for application 1
    (201,1,'B',NULL,0,NULL,NULL,NULL,1,NULL,'OB4234560','lsts_not_submitted',NULL,NULL,'2011-03-16','2011-03-16', '2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (202,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB4234561','lsts_consideration',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (203,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234562','lsts_surrendered',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (204,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB4234563','lsts_unlicenced',1,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (205,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234564','lsts_terminated',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (206,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234565','lsts_withdrawn',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (207,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234566','lsts_suspended',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (208,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234567','lsts_curtailed',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',1,
    3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (209,1,'B',NULL,0,NULL,NULL,NULL,1,'lcat_psv','OB4234568','lsts_revoked',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),

    -- extra licence for application 3
    (210,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1,NULL, NULL),

    (30,30,'B',NULL,0,NULL,NULL,NULL,1,'lcat_gv','OB1234568','lsts_not_submitted',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (41,41,'B',NULL,2,NULL,NULL,NULL,2,'lcat_gv','OB1234577','lsts_not_submitted',0,'ltyp_sn','2007-01-12','2007-01-12','2007-01-12','',1,
    21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (54,54,'B',NULL,2,NULL,NULL,NULL,4,'lcat_gv','OB1234578','lsts_not_submitted',0,'ltyp_r','2007-01-12','2007-01-12','2007-01-12','',0,4,NULL,NULL,NULL,NULL,
    NULL,NULL, NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (63,63,'D',NULL,4,NULL,NULL,NULL,0,'lcat_psv','PD1234589','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',1,7,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (75,75,'D',NULL,4,NULL,NULL,NULL,4,'lcat_psv','PD2737289','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (100,100,'D',NULL,4,NULL,NULL,NULL,0,'lcat_psv','PD1001001','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),2, '2016-01-01 10:00:00', NULL),
    (110,75,'D',NULL,4,8,21,25,4,'lcat_psv','PD2737280','lsts_not_submitted',0,'ltyp_r','2010-01-12','2010-01-12',
    '2010-01-12','',0,10,5,5,NULL,NULL,
    NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', 4),
    (114,104,'B',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'OB1534567','lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1, '2016-01-01 10:00:00', NULL),
    (115,105,'S',NULL,NULL,NULL,NULL,NULL,NULL,'lcat_psv','TS1234568','lsts_not_submitted',0,'ltyp_sr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NOW(),NULL,1, '2016-01-01 10:00:00', NULL);

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

INSERT INTO `local_authority` (`id`, `created_by`, `last_modified_by`, `traffic_area_id`, `naptan_code`, `txc_name`, `created_on`, `description`, `email_address`, `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 'N', '639', 'N', NULL, 'Local Auth 1', NULL, NULL, 1),
  (2, 1, 1, 'H', '639', 'H', NULL, 'Local Auth 2', NULL, NULL, 1),
  (3, 1, 1, 'B', '639', 'B', NULL, 'Local Auth 3', NULL, NULL, 1),
  (4, 1, 1, 'F', '639', 'F', NULL, 'Local Auth 4', NULL, NULL, 1),
  (5, 1, 1, 'M', '639', 'M', NULL, 'Local Auth 5', NULL, NULL, 1),
  (6, 1, 1, 'G', '639', 'G', NULL, 'Local Auth 6', NULL, NULL, 1),
  (7, 1, 1, 'B', '639', 'B', NULL, 'Local Auth 7', NULL, NULL, 1),
  (8, 1, 1, 'G', '639', 'G', NULL, 'Local Auth 8', NULL, NULL, 1),
  (9, 1, 1, 'K', '639', 'K', NULL, 'Local Auth 9', NULL, NULL, 1),
  (10, 1, 1, 'C', '639', 'C', NULL, 'Local Auth 10', NULL, NULL, 1),
  (11, 1, 1, 'H', '639', 'H', NULL, 'Local Auth 11', NULL, NULL, 1),
  (12, 1, 1, 'F', '639', 'F', NULL, 'Local Auth 12', NULL, NULL, 1),
  (13, 1, 1, 'C', '639', 'C', NULL, 'Local Auth 13', NULL, NULL, 1),
  (14, 1, 1, 'K', '639', 'K', NULL, 'Local Auth 14', NULL, NULL, 1),
  (15, 1, 1, 'B', '639', 'B', NULL, 'Local Auth 15', NULL, NULL, 1),
  (16, 1, 1, 'D', '639', 'D', NULL, 'Local Auth 16', NULL, NULL, 1),
  (17, 1, 1, 'M', '639', 'M', NULL, 'Local Auth 17', NULL, NULL, 1),
  (18, 1, 1, 'N', '639', 'N', NULL, 'Local Auth 18', NULL, NULL, 1);

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
  `comment`,
  `priority`,
  `created_on`,
  `version`
)
VALUES
(1,  'note_t_app',  NULL, 2, 7,   28, 1,    NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(2,  'note_t_lic',  NULL, 4, 7,   28, NULL, NULL, 'This is a licence note', 1, '2011-10-03 00:00:00', 1),
(3,  'note_t_app',  NULL, 2, 7,   28, 1,    NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(4,  'note_t_app',  NULL, 3, 7,   28, 1,    NULL, 'This is an app note',    0, '2011-10-03 00:00:00', 1),
(5,  'note_t_lic',  NULL, 5, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-03 00:00:00', 1),
(6,  'note_t_case', NULL, 3, 7,   28, NULL, NULL, 'This is a case note',    0, '2011-10-03 00:00:00', 1),
(7,  'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', 1),
(8,  'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2012-10-10 00:00:00', 1),
(9,  'note_t_bus',  1,    3, 110, 75, NULL, NULL, 'This is a bus reg note', 0, '2012-10-10 00:00:00', 1),
(10, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-14 00:00:00', 1),
(11, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-13 00:00:00', 1),
(12, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-15 00:00:00', 1),
(13, 'note_t_lic',  NULL, 3, 7,   28, NULL, NULL, 'This is a licence note', 0, '2011-10-12 00:00:00', 1),
(14, 'note_t_tm',   NULL, 3,NULL,NULL,NULL, 3,    'This is a TM note',      0, '2011-10-12 00:00:00', 1);


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
  (1, 'obj_t_local_auth', 1, 1, 8, '2014-02-20 00:00:00', '2014-02-20 00:00:00', 1),
  (2, 'obj_t_police', 1, 1, 8, '2014-02-21 00:00:00', '2014-02-21 00:00:00', 1);

INSERT INTO `opposition`
(`id`, `opposition_type`, `licence_id`, `application_id`, `case_id`, `opposer_id`, `last_modified_by`, `created_by`, `is_copied`,
 `raised_date`, `is_in_time`, `is_public_inquiry`, `is_withdrawn`, `is_valid`, `valid_notes`, `notes`, `deleted_date`, `created_on`,
 `last_modified_on`, `version`)
VALUES
  (1, 'otf_eob', 7, 1, 29, 1, 1, 1, 1, '2014-02-19', 1, 1, 0, 'validity_no', 'Valid notes', 'Notes', null,
  '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1),
  (2, 'otf_rep', 7, 1, 29, 1, 1, 1, 1, '2014-02-19', 0, 0, 1, 'validity_yes', 'Valid notes', 'Notes', null,
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

INSERT INTO `organisation` (`id`,`lead_tc_area_id`, `created_by`, `last_modified_by`,`contact_details_id`,
`company_or_llp_no`, `name`,`is_mlh`, `type`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,'B',1,3,  21,'12345678','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (30,'C',1,4,  30,'98765432','John Smith Haulage Ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (41,'D',0,4,  41,'241341234','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1),
    (54,'F',3,4,  54,'675675334','Teddie Stobbart Group Ltd',0,'org_t_rc',NOW(),NOW(),1),
    (63,'G',1,2,  63,'353456456','Leeds bus service ltd.',0,'org_t_rc',NOW(),NOW(),1),
    (75,'H',1,0,  75,'12345A1123','Leeds city council',0,'org_t_pa',NOW(),NOW(),1),
    (100,'K',1,3,  100,'100100','Test partnership',0,'org_t_p','2014-01-28 16:25:35','2014-01-28 16:25:35',2),
    (104,'M',NULL,NULL,NULL,'1234567','Company Name',0,'org_t_rc',NULL,NULL,1),
    (105,'N',1,3,NULL,NULL,'SR Orgaisation',0,'org_t_rc',NOW(),NOW(),1);

INSERT INTO `organisation_person` (`id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`,
    `person_id`, `organisation_id`) VALUES
    (1,NULL,NULL,NULL,NULL,1,78,7),
    (2,NULL,NULL,NULL,NULL,1,77,7),
    (3,NULL,NULL,NULL,NULL,1,77,1),
    (4,NULL,NULL,NULL,NULL,1,78,1),
    (5,NULL,NULL,NULL,NULL,1,78,100),
    (6,NULL,NULL,NULL,NULL,1,77,100);

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
    (90,1,1,'Aldershot','title_mr','1960-02-01 00:00:00','ABDOU','BONOMI',NULL,NULL,NULL,1,NULL);

INSERT INTO `disqualification` (
    `id`, `created_by`, `last_modified_by`, `is_disqualified`, `period`,
    `notes`, `created_on`, `last_modified_on`, `version`, `officer_cd_id`
) VALUES
    (10,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (13,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (15,NULL,NULL,1,6,'TBC',NOW(),NULL,1,NULL),
    (32,NULL,NULL,1,2,'TBC',NOW(),NULL,1,NULL),
    (36,NULL,NULL,1,6,'TBC',NOW(),NULL,1,NULL);

INSERT INTO `phone_contact` (`id`,`phone_contact_type`,`phone_number`,`details`,
    `contact_details_id`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'phone_t_tel','0113 123 1234','',101,NULL,NULL,NULL,NULL,1),
    (10,'phone_t_tel','0113 123 1234','',1,NULL,NULL,NULL,NULL,1),
    (2,'phone_t_tel','0113 123 1234','',8,NULL,NULL,NULL,NULL,1);

INSERT INTO `pi` (`id`,`agreed_by_tc_id`,`agreed_by_tc_role`,`assigned_to`,`decided_by_tc_id`,`decided_by_tc_role`,
  `pi_status`,`written_outcome`,`case_id`,`created_by`,`last_modified_by`,`brief_to_tc_date`,`call_up_letter_date`,
  `dec_sent_after_written_dec_date`,`decision_letter_sent_date`,`decision_notes`,`licence_curtailed_at_pi`,
  `licence_revoked_at_pi`,`licence_suspended_at_pi`,`notification_date`,`section_code_text`,`tc_written_decision_date`,
  `tc_written_reason_date`,`written_reason_date`,`written_reason_letter_date`,`agreed_date`,`closed_date`,`comment`,
  `created_on`,`decision_date`,`deleted_date`,`is_cancelled`,`last_modified_on`,`version`,`witnesses`)
VALUES
  (1,2,'tc_r_dtc',NULL,2,'tc_r_dhtru','pi_s_reg',NULL,24,NULL,NULL,NULL,NULL,NULL,NULL,
   'S13 - Consideration of new application under Section 13',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'2014-11-24',NULL,
   'Test Pi','2014-11-24 10:06:49',NULL,NULL,0,'2014-12-11 10:49:57',2,0),
   (2,2,'tc_r_dtc',NULL,2,'tc_r_dhtru','pi_s_reg',NULL,84,NULL,NULL,NULL,NULL,NULL,NULL,
   'S13 - Consideration of new application under Section 13',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'2014-11-24',NULL,
   'Test Pi','2014-11-24 10:06:49',NULL,NULL,0,'2014-12-11 10:49:57',2,0);

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

INSERT INTO `pi_hearing` (`id`,`pi_id`,`presided_by_role`,`created_by`,`last_modified_by`,`pi_venue_id`,`presiding_tc_id`,`adjourned_date`,`adjourned_reason`,`cancelled_date`,`cancelled_reason`,`details`,`is_adjourned`,`presiding_tc_other`,`created_on`,`hearing_date`,`is_cancelled`,`last_modified_on`,`pi_venue_other`,`version`,`witnesses`)
  VALUES
    (1,1,'tc_r_htru',NULL,NULL,1,1,'2014-03-16','adjourned reason',NULL,NULL,'S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',1,NULL,'2014-11-24 10:22:24','2014-03-16 14:30:00',0,NULL,NULL,1,9),
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

INSERT INTO `transport_manager_licence` (`id`, `created_by`, `last_modified_by`, `licence_id`, `tm_type`, `transport_manager_id`, `additional_information`, `created_on`, `deleted_date`, `hours_fri`, `hours_mon`, `hours_sat`, `hours_sun`, `hours_thu`, `hours_tue`, `hours_wed`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, NULL, NULL, 7, '', 1, NULL, NULL, NULL, 2, 2, 2, 2, 2, NULL, NULL, NULL, NULL, 1),
	(2, NULL, NULL, 207, '', 2, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, 1),
	(3, NULL, NULL, 208, '', 3, NULL, NULL, NULL, 2, 2, 2, 2, 2, NULL, NULL, NULL, NULL, 1);

INSERT INTO `transport_manager_application` (`id`, `application_id`, `tm_application_status`, `created_by`, `last_modified_by`, `tm_type`, `transport_manager_id`, `action`, `additional_information`, `created_on`, `deleted_date`, `hours_fri`, `hours_mon`, `hours_sat`, `hours_sun`, `hours_thu`, `hours_tue`, `hours_wed`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, 1, 'tmap_st_incomplete', NULL, NULL, 'tm_t_I', 1, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 1, 1, 1, NULL, NULL, 1),
	(2, 7, 'tmap_st_awaiting_signature', NULL, NULL, 'tm_t_I', 1, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 2, 2, 2, NULL,
	NULL, 1),
  (3, 1, 'tmap_st_tm_signed', NULL, NULL, 'tm_t_I', 3, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 3, 4, 5, NULL,
  NULL, 1),
	(4, 7, 'tmap_st_postal_application', NULL, NULL, 'tm_t_I', 3, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 6, 7, 8, NULL,
	NULL, 1);

INSERT INTO `tm_application_oc` (`transport_manager_application_id`, `operating_centre_id`)
VALUES
	(1, 16),
	(2, 16),
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
    (1,1,NULL,NULL,'GB','tm_qt_CPCSI',NULL,NULL,1,'2014-01-01','1'),
    (2,1,NULL,NULL,'GB','tm_qt_CPCSN',NULL,NULL,1,'2014-02-02','2'),
    (3,3,1,1,'GB','tm_qt_CPCSI','2012-01-01',NULL,1,'2012-01-01','3333'),
    (4,3,1,1,'ZA','tm_qt_CPCSN','2013-02-02',NULL,1,'2013-02-02','4444');

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
    (1,NULL,NULL,'tm_st_A','tm_t_I',115,117,NULL,NULL,NULL,1),
    (2,NULL,NULL,'tm_st_A','tm_t_E',116,118,NULL,NULL,NULL,1),
    (3,NULL,NULL,'tm_st_A','tm_t_I',104,119,NULL,NULL,NULL,1);

INSERT INTO `other_licence` (`id`, `application_id`,`transport_manager_id`,`lic_no`,`created_by`, `last_modified_by`,
`created_on`, `last_modified_on`, `version`, `role`, `operating_centres`, `total_auth_vehicles`, `hours_per_week`,
`transport_manager_application_id`,`transport_manager_licence_id`) VALUES
    (1,3,1,'AB123456',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),
    (2,3,1,'YX654321',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),
    (3,6,2,'AB123456',1,NULL,'2014-11-23 21:58:52',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),
    (4,1,1,'AA111111',1,NULL,'2014-10-01 21:58:52',NULL,1,'ol_role_tm','oc1',2,10,1,NULL),
    (5,1,1,'AB122222',1,NULL,'2014-10-02 21:58:52',NULL,1,'ol_role_tm','oc2',2,10,1,NULL),
    (6,2,1,'AA133333',1,NULL,'2014-10-03 21:58:52',NULL,1,'ol_role_tm','oc3',3,11,2,NULL),
    (7,2,1,'AB144444',1,NULL,'2014-10-04 21:58:52',NULL,1,'ol_role_tm','oc4',3,11,2,NULL),
    (8,1,3,'AA311111',1,NULL,'2014-10-05 21:58:52',NULL,1,'ol_role_tm','oc5',4,12,3,NULL),
    (9,1,3,'AB322222',1,NULL,'2014-10-06 21:58:52',NULL,1,'ol_role_tm','oc6',4,12,3,NULL),
    (10,2,3,'AA333333',1,NULL,'2014-10-07 21:58:52',NULL,1,'ol_role_tm','oc7',5,13,4,NULL),
    (11,2,3,'AB344444',1,NULL,'2014-10-08 21:58:52',NULL,1,'ol_role_tm','oc8',5,13,4,NULL),
    (12,NULL,1,'CC11111',1,NULL,'2014-10-09 21:58:52',NULL,1,'ol_role_tm','oc9',6,14,NULL,1),
    (13,NULL,1,'CD12222',1,NULL,'2014-10-10 21:58:52',NULL,1,'ol_role_tm','oc10',6,14,NULL,1),
    (14,NULL,2,'CC11111',1,NULL,'2014-10-11 21:58:52',NULL,1,'ol_role_tm','oc11',7,15,NULL,2),
    (15,NULL,2,'CD12222',1,NULL,'2014-10-12 21:58:52',NULL,1,'ol_role_tm','oc12',7,15,NULL,2),
    (16,NULL,3,'CC33333',1,NULL,'2014-10-13 21:58:52',NULL,1,'ol_role_tm','oc13',8,16,NULL,3),
    (17,NULL,3,'CD44444',1,NULL,'2014-10-14 21:58:52',NULL,1,'ol_role_tm','oc14',8,16,NULL,3);

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

INSERT INTO `user` (`id`, `team_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`,
`last_successful_login_date`,`version`, `deleted_date`, `login_id`,`contact_details_id`,`email_address`,
`local_authority_id`) VALUES
    (1,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-01-26 09:00:00',1,NULL,'loggedinuser',101,
    'loggedin@test9876.com', 1),
    (2,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-02-25 23:00:00',1,NULL,'johnspellman',105,
    'john.spellman@test9876.com', NULL),
    (3,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-06-23 15:00:00',1,NULL,'stevefox',106,
    'stevefox@test9876.com', NULL),
    (4,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-06-19 14:00:00',1,NULL,'amywrigg',130,
    'amywrigg@test9876.com', NULL),
    (5,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-05-15 17:00:00',1,NULL,'philjowitt',131,
    'philjowitt@test9876.com', NULL),
    (6,3,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-02-22 11:00:00',1,NULL,'kevinrooney',132,
    'kevinrooney@test9876.com', NULL),
    (7,4,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-03-27 00:00:00',1,NULL,'sarahthompson',133,
    'sarahthompson@test9876.com', NULL),
    (8,8,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00','2013-12-27 00:00:00',1,NULL,'anotheruser',114,
    'anotheruser@test9876.com', NULL),
    (12504,32,1,1,'2000-04-02 10:57:00','2000-04-02 10:57:00','2010-03-31 19:00:00',1,NULL,'abdou.bonomi',140,
    NULL, NULL),
    (12505,32,1,1,'2000-04-02 10:57:00','2000-04-02 10:57:00','2010-03-31 19:00:00',1,NULL,'abdou.bonomi2',140,
    NULL, NULL);

INSERT INTO `organisation_user` (`organisation_id`, `user_id`) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7),
    (1, 12504),
    (1, 12505);

INSERT INTO `user_role` (`user_id`, `role_id`, `created_by`,`last_modified_by`,`expiry_date`,
`valid_from`, `created_on`,`version`) VALUES
    (12504, 3, 1, 1, NOW(),NOW(),NOW(),1),
    (12505, 5, 1, 1, NOW(),NOW(),NOW(),1),
    (1, 1, 1, 1, NOW(),NOW(),NOW(),1), -- loggedinuser=internal-limited-read-only
    (2, 4, 1, 1, NOW(),NOW(),NOW(),1), -- johnspellman=internal-admin
    (3, 4, 1, 1, NOW(),NOW(),NOW(),1), -- stevefox=internal-admin
    (4, 3, 1, 1, NOW(),NOW(),NOW(),1), -- amywrigg=internal-case-worker
    (5, 4, 1, 1, NOW(),NOW(),NOW(),1), -- philjowitt=internal-admin
    (6, 4, 1, 1, NOW(),NOW(),NOW(),1), -- kevinrooney=internal-admin
    (7, 4, 1, 1, NOW(),NOW(),NOW(),1), -- sarahthompson=internal-admin
    (8, 3, 1, 1, NOW(),NOW(),NOW(),1); -- anotheruser=internal-case-worker

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
   `last_modified_by`,`created_by`,`ecms_no`,`open_date`,`closed_date`,`description`,`is_impounding`,
   `erru_originating_authority`,`erru_transport_undertaking_name`,`erru_vrm`,`annual_test_history`,`prohibition_note`,
   `conviction_note`,`penalties_note`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (24,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2012-03-21',NULL,'Case for convictions against company
  directors',0,NULL,NULL,NULL,'Annual test history for case 24','prohibition test notes','test comments',NULL,NULL,
  '2013-11-12 12:27:33',NULL,1),
  (28,'case_t_app',NULL,7,1,NULL,NULL,NULL,'E123444','2012-06-13',NULL,'Convictions against operator',0,NULL,NULL,
  NULL,'Annual Test History for case 28',NULL,NULL,NULL,NULL,'2014-01-01 11:11:11',NULL,1),
  (29,'case_t_lic','erru_case_t_msi',7,NULL,NULL,NULL,NULL,'','2014-02-11',NULL,'1213213',0,'Polish Transport Authority','Polish Transport Authority','GH52 ABC',NULL,NULL,NULL,'comment',NULL,'2014-01-11 11:11:11','2014-11-07 12:47:07',3),
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
  (82,'case_t_tm',NULL,NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an internal Transport manager',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (83,'case_t_tm',NULL,NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an external Transport manager',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1),
  (84,'case_t_tm',NULL,NULL,NULL,3,NULL,NULL,'','2014-02-11',NULL,'Case linked to an external Transport manager',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1);

INSERT INTO team(id,version,name,traffic_area_id) VALUES
    (1,1,'Marketing',''),
    (2,1,'Development','B'),
    (3,1,'Infrastructure',''),
    (4,1,'Support',''),
    (5,1,'Assisted Digital FEP',''),
    (6,1,'Bus Reg Team',''),
    (7,1,'Compliance Team',''),
    (8,1,'Environmental Team',''),
    (9,1,'IRFO Team',''),
    (32,1,'Self service Operators','');

INSERT INTO `case_category` (`case_id`, `category_id`)
VALUES
    (29, 'case_cat_7');

/**
 * NOTE: These inserts can't be grouped into one as they insert different columns
 */
/* Application task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (1,1,7,9,8,1,2,'A test task','2014-08-12',1);
    /* Licence task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (2,null,110,1,74,1,2,'Another test task','2013-02-11',1);
/* IRFO task */
INSERT INTO task(id,irfo_organisation_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (3,1,8,70,1,2,'An organisation task','2014-05-01',1);
/* Transport Manager task */
INSERT INTO task(id,transport_manager_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (4,2,5,105,6,3,'A transport task','2010-01-01',1);
/* Case task */
INSERT INTO task(id,case_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (5,24,2,44,null,4,'A case task','2010-02-01',1);
/* Unlinked task */
INSERT INTO task(id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (6,7,67,null,4,'Unassigned task','2010-07-03',1);
/* Application, future, urgent task */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (7,2,7,9,33,1,2,'A test task','2018-09-27',1,1);
/* Licence, single licence holder */
INSERT INTO task(id,application_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,urgent,version) VALUES
    (8,null,63,1,110,1,2,'Single licence','2012-09-27',0,1);
/* Transport Manager task */
INSERT INTO task(id,transport_manager_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (9,3,5,103,1,2,'A test task for TM 3','2014-12-15',1);
/* Bus Registration task */
INSERT INTO task(id,bus_reg_id,licence_id,category_id,sub_category_id,assigned_to_user_id,assigned_to_team_id,description,action_date,version) VALUES
    (10,1,110,3,39,1,2,'A test Bus Reg task','2014-12-15',1);

INSERT INTO `task_allocation_rule` (`id`, `category_id`, `team_id`, `user_id`, `goods_or_psv`, `is_mlh`, `traffic_area_id`) VALUES
    (1,9,5,1,NULL,NULL,NULL),
    (2,3,6,1,NULL,NULL,NULL),
    (3,2,7,1,NULL,NULL,NULL),
    (4,7,8,8,NULL,NULL,NULL),
    (5,8,9,1,NULL,NULL,NULL),
    (6,1,5,1,NULL,NULL,NULL);

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
    (2, 'pi', 'briefToTcDate', 'hearingDate', -14, 0, 0, '1900-01-01', NULL),
    (3, 'pi', 'decisionLetterSentDate', 'hearingDate', 5, 1, 1, '1900-01-01', NULL),
    (4, 'pi', 'tcWrittenDecisionDate', 'hearingDate', 20, 1, 1, '1900-01-01', NULL),
    (5, 'pi', 'tcWrittenReasonDate', 'hearingDate', 5, 1, 1, '1900-01-01', NULL),
    (6, 'pi', 'writtenReasonLetterDate', 'tcWrittenReasonDate', 5, 1, 1, '1900-01-01', NULL),
    (7, 'pi', 'decSentAfterWrittenDecDate', 'hearingDate', 2, 1, 1, '1900-01-01', NULL),
    (8, 'pi_hearing', 'hearingDate', 'agreedDate', 60, 1, 1, '1900-01-01', NULL);


INSERT INTO `serious_infringement`
(`id`, `si_category_type_id`, `erru_response_user_id`, `member_state_code`, `created_by`,`last_modified_by`,
`si_category_id`, `case_id`, `check_date`, `erru_response_sent`,`erru_response_time`, `infringement_date`,
`notification_number`, `workflow_id`, `reason`, `deleted_date`,`created_on`, `last_modified_on`, `version`)
VALUES
  (1, '101', 1, 'PL', 1,1, 'MSI', 29, '2014-04-04', 0,null, '2014-04-05', 12345, 'A3CCBDB1-6C8B-4741-847B-4C6B80AA8608',
   null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1),
  (2, '101', 1, 'PL', 1,1, 'MSI', 24, '2014-04-04', 0,null, '2014-04-05', 678910,
   'FB4F5CE2-4D38-4AB8-8185-03947C939393', null, null,'2014-05-04 17:50:06', '2014-05-04 17:50:06', 1);

INSERT INTO `si_penalty`
(`id`, `si_penalty_type_id`, `last_modified_by`, `created_by`, `serious_infringement_id`, `imposed`,
 `reason_not_imposed`, `start_date`, `end_date`, `deleted_date`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, '101', 1, 1, 1, 1, null, '2014-06-01', '2015-01-31', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (2, '306', 1, 1, 1, 0, 'Reason the penalty was not imposed', null, null, null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (3, '306', 1, 1, 2, 0, 'Reason the penalty was not imposed', null, null, null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (4, '101', 1, 1, 2, 1, '', '2014-05-01', '2015-01-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (5, '102', 1, 1, 2, 1, '', '2014-04-01', '2015-04-30', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (6, '301', 1, 1, 2, 1, '', '2014-03-01', '2015-03-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (7, '302', 1, 1, 2, 1, '', '2014-02-01', '2015-02-28', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (8, '303', 1, 1, 2, 1, '', '2014-01-01', '2015-01-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (9, '304', 1, 1, 2, 1, '', '2013-12-01', '2014-12-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (10, '305', 1, 1, 2, 1, '', '2013-11-01', '2014-11-30', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (11, '306', 1, 1, 2, 1, '', '2013-10-01', '2014-10-31', null,
  '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (12, '307', 1, 1, 2, 1, '', '2013-09-01', '2014-09-30', null,
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
  (1, '204', 1, 1, 1, '2014-10-02', 'pen_erru_imposed_executed_yes', '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (2, '202', 1, 1, 1, '2014-10-02', 'pen_erru_imposed_executed_no', '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1),
  (3, '201', 1, 1, 1, '2014-10-02', 'pen_erru_imposed_executed_un', '2014-11-01', '2015-12-01', null, '2014-05-21 12:22:09', '2014-05-21 12:22:09', 1);

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

INSERT INTO `publication_police_data` (`id`,`publication_link_id`,`created_by`,`last_modified_by`,`olbs_dob`,`birth_date`,`created_on`,`family_name`,`forename`,`last_modified_on`,`version`)
  VALUES
    (1,1,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:00:34','Jones','Tom',NULL,1),
    (2,1,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:00:35','Winnard','Keith',NULL,1),
    (3,2,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:02:18','Jones','Tom',NULL,1),
    (4,2,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:02:19','Winnard','Keith',NULL,1),
    (5,3,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:03:15','Jones','Tom',NULL,1),
    (6,3,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:03:16','Winnard','Keith',NULL,1);

INSERT INTO `organisation_nature_of_business` (`organisation_id`, `ref_data_id`)
VALUES
	(1, '01120'),
	(1, '01150'),
	(30, '01150'),
	(41, '01150'),
	(54, '01150'),
	(63, '01150'),
	(75, '01150'),
	(100, '01150'),
	(104, '01150'),
	(105, '01150');

INSERT INTO `system_parameter` (`id`, `param_value`, `description`)
VALUES
    ('task.default_team', 2, NULL),
    ('task.default_user', 1, NULL);

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
  (3, 'cm_email', 122, 'statement_t_36', 24, 1, 1, 'Authorisers decision 3', '2014-07-03',
  '2014-03-03', 'Requestors body 3', '2014-01-10', '2013-01-03', '2013-01-04', 1, 'VRM 3'),
  (4, 'cm_tel', 123, 'statement_t_38', 24, 1, 1, 'Authorisers decision 4', '2014-08-04',
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

INSERT INTO `event_history_type` (`id`,`event_code`,`description`) VALUES
('1','2ND','Reminder Printed')
,('2','ACN','Application Acknowledged')
,('3','ACV','Variation Acknowledged')
,('4','AOC','Amend OC Authorisation')
,('5','AWD','Application Withdrawn')
,('6','COM','Not Used')
,('7','CUR','Licence Curtailed')
,('8','CVE','Maintenance Checklist')
,('9','DIP','Not Used')
,('10','DUP','Duplicate Document Requested')
,('11','FEE','Not Used')
,('12','GRA','Application Granted')
,('13','INT','Interim Granted')
,('14','IRF','Interim Refused')
,('15','NEW','New Application')
,('16','NTU','Not Taken Up')
,('17','OBJ','Objection Created')
,('18','PIR','Not Used')
,('19','PRM','Checklist Printed')
,('20','PRT','Document Printed')
,('21','PVI','Not Used')
,('22','RAM','Licence Amended')
,('23','REF','Application Refused')
,('24','REG','Register Application')
,('25','REV','Licence Revoked')
,('26','RPT','Not Used')
,('27','RTU','Not Taken Up Revived')
,('28','RVI','VI Inspection Requested')
,('29','SSD','Not Used')
,('30','SUR','Licence Surrendered')
,('31','SUS','Licence Suspended')
,('32','TEX','Time Expired Set')
,('33','TMC','Transport Manager Amended')
,('34','TRA','Not Used')
,('35','UNG','Application Ungranted')
,('36','USR','Licence Unsurrendered')
,('37','VAR','Variation Application')
,('38','VCH','Vehicle Changed')
,('39','VH+','Vehicle Added')
,('40','VH-','Vehicle Removed')
,('41','EML','Documents Emailed')
,('42','CCA','Change Correspondence Address')
,('43','ADI','Add Director')
,('44','RDI','Remove Director')
,('45','ASU','Add Subsidiary')
,('46','RSU','Remove Subsidiary')
,('47','APA','Add Partner')
,('48','RPA','Remove Partner')
,('49','ATM','Add Transport Manager')
,('50','RTM','Remove Transport Manager')
,('51','NOC','Add Operating Centre')
,('52','ROC','Remove Operating Centre')
,('53','IAU','OC Authorisation Increased')
,('54','DAU','OC Authorisation Decreased')
,('55','S4R','Schedule 4 Refused')
,('56','CNS','Continuation Not Sought')
,('57','OCN','Operator Company Name')
,('58','OTN','Operator Trading Name Added')
,('59','OAC','Operator Address Change')
,('60','CRN','Company Reg Number')
,('61','OCI','Certificate seen changed')
,('62','OOA','Org Office Address')
,('63','TA+','Trailer Authorisation Increased')
,('64','TA-','Trailer Authorisation Decreased')
,('65','VIA','Area Office Changed')
,('66','MSI','Maintenance Safety Inspecition Changed')
,('67','MSC','Contract Satisfactory')
,('68','FIN','Finance Details Changed')
,('69','CON','Condition Added')
,('70','UND','Undertaking Added')
,('71','INR','Revoke Interim')
,('72','VA+','Vehicle Authorisation Increased')
,('73','VA-','Vehicle Authorisation Decreased')
,('74','USU','Update Subsidiary')
,('75','RES','Reset to Valid')
,('76','REP','Representation Created')
,('77','OTR','Operator Trading Name Removed')
,('78','UDI','Update Director')
,('79','UPA','Update Partner')
,('80','UOB','Update Objection')
,('81','URE','Update Representation')
,('82','CTA','Change Transport Consultant Address')
,('83','CTM','Create Transport Manager')
,('84','MTM','Modify Transport Manager')
,('85','DTM','Delete Transport Manager')
,('86','CTQ','Create Qualification')
,('87','MTQ','Modify Qualification')
,('88','DTQ','Delete Qualification')
,('89','ATA','Add Transport Manager Application')
,('90','DTA','Delete Transport Manager Application')
,('91','ATL','Add Transport Manager Licence')
,('92','DTL','Delete Transport Manager Licence')
,('93','TMS','Source Of Transport Manager Merge')
,('94','TMD','Destination Of Transport Manager Merge')
,('95','DNM','Delete Non-PI Compliance Meeting')
,('96','CEP','Create Compliance Episode')
,('97','MCE','Modify Compliance Episode')
,('98','DCE','Delete Compliance Episode')
,('99','CLE','Close Compliance Episode')
,('100','RCE','Re-Open Compliance Episode')
,('101','CEN','Create Episode Note')
,('102','MEN','Modify Episode Note')
,('103','DEN','Delete Episode Note')
,('104','CED','Create Episode Document')
,('105','MED','Modify Episode Document')
,('106','DED','Delete Episode Document')
,('107','CER','Create Episode Recommendation')
,('108','MER','Modify Episode Recommendation')
,('109','DER','Delete Episode Recommendation')
,('110','CNP','Create Episode Non PublicInquiry')
,('111','MNP','Modify Episode Non PublicInquiry')
,('112','DNP','Delete Episode Non PublicInquiry')
,('113','CPI','Create Episode Public Inquiry')
,('114','MPI','Modify Episode Public Inquiry')
,('115','DPI','Delete Episode Public Inquiry')
,('116','CDE','Create Episode Decision')
,('117','MDE','Modify Episode Decision')
,('118','DDE','Delete Episode Decision')
,('119','CSE','Create Episode Serious Infringement')
,('120','MSE','Modify Episode Serious Infringement')
,('121','DSE','Delete Episode Serious Infringement')
,('122','CES','Create Episode Stay')
,('123','MES','Modify Episode Stay')
,('124','DES','Delete Episode Stay')
,('125','CEA','Create Episode Appeal')
,('126','MEA','Modify Episode Appeal')
,('127','DEA','Delete Episode Appeal')
,('128','DFT','Declare Fit')
,('129','DUF','Declare Unfit')
,('130','STC','Set To Current')
,('131','MTT','Modify Transport Manager Type')
,('132','MTC','Modify Transport Manager Case Notes')
,('133','MTA','Modify Transport Manager Address')
,('134','MTE','Modify Transport Manager Email')
,('135','CHR','Checklist Received')
,('136','CHN','Checklist Not Received')
,('137','RCN','Remove Casenote')
,('138','ACE','Add Casenote')
,('139','SFI','Sole Trader First Name Changed')
,('140','SFA','Sole Trader Family Name Changed')
,('141','SDB','Sole Trader Date of BirthChanged')
,('142','DFI','Director First Name Changed')
,('143','DFA','Director Family Name Changed')
,('144','DDB','Director Date of Birth Changed')
,('145','PFI','Partner First Name Changed')
,('146','PFA','Partner Family Name Changed')
,('147','PDB','Partner Date of Birth Changed');

INSERT INTO `event_history` (`id`, `event_history_type_id`, `application_id`, `bus_reg_id`, `case_id`, `licence_id`, `organisation_id`, `transport_manager_id`, `user_id`, `entity_pk`, `entity_type`, `entity_version`, `event_data`, `event_datetime`, `event_description`)
VALUES
	(8, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Event Data', '2015-03-24 11:02:49', 'Event Description 1'),
	(9, 1, NULL, NULL, NULL, 30, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 2'),
	(10, 1, NULL, NULL, NULL, 110, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 3'),
	(11, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 4'),
	(12, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 5'),
	(13, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 6'),
	(14, 1, NULL, NULL, NULL, 30, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 7'),
	(15, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 8'),
	(16, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 9'),
	(17, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 10'),
	(18, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 11'),
	(19, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 12'),
	(20, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 13'),
	(21, 1, NULL, NULL, NULL, 110, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 14'),
	(22, 1, NULL, NULL, NULL, 110, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 15'),
	(23, 1, NULL, NULL, NULL, 110, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 16'),
	(24, 1, NULL, NULL, NULL, 110, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 17'),
	(25, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 18'),
	(26, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 19'),
	(27, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 20'),
	(28, 1, NULL, NULL, NULL, 7, NULL, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 21'),
	(29, 1, NULL, NULL, NULL, 7, 1, NULL, 4, NULL, NULL, NULL, 'Licence Event Data', '2015-03-16 10:30:18', 'Event Description 22'),
	(39, 102, NULL, NULL, 29, NULL, 1, NULL, 4, NULL, NULL, NULL, 'Case Event Data', '2015-03-24 11:02:49', 'Not used'),
	(30, 131, NULL, NULL, NULL, NULL, NULL, 1, 4, NULL, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(33, 131, 1, NULL, NULL, NULL, NULL, 1, 4, NULL, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(34, 131, 1, NULL, NULL, NULL, NULL, 1, 4, NULL, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(35, 131, 1, NULL, NULL, NULL, 1, 1, 4, NULL, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used'),
	(36, 131, 1, NULL, NULL, NULL, 1, 1, 4, NULL, NULL, NULL, 'TM Event Data', '2015-03-19 13:37:36', 'Not used');


-- End: Event History Test Data

INSERT INTO `hint_question` (`id`,`created_by`,`last_modified_by`,`category_no`,`hint_question`,`created_on`,
`last_modified_on`,`version`)
  VALUES
    (1,1,1,1,'What is your favourite colour?', '2015-03-27 00:00:00',null,1),
    (2,1,1,1,'What is your Mother\'s maiden name?', '2015-03-27 00:00:00',null,1),
    (3,1,1,1,'What is your memorable date?', '2015-03-27 00:00:00',null,1);


SET foreign_key_checks = 1;

-- Start: Application 7 - new Goods Vehicle Standard National application ready to submit
BEGIN;
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (116,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (117,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (118,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:30:12',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (119,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (124,116,'ct_corr',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,'dvsa@stolenegg.com',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (125,117,'ct_est',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (126,119,'ct_work',NULL,NULL,NULL,NULL,0,'2015-03-27 12:31:05',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `operating_centre` (`id`, `address_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `olbs_key`, `version`, `vi_action`) VALUES (73,118,NULL,NULL,'2015-03-27 12:30:12',NULL,NULL,1,NULL);
INSERT INTO `licence` (`id`, `correspondence_cd_id`, `enforcement_area_id`, `establishment_cd_id`, `organisation_id`, `tachograph_ins`, `transport_consultant_cd_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`, `traffic_area_id`, `fabs_reference`, `fee_date`, `psv_discs_to_be_printed_no`, `review_date`, `safety_ins`, `safety_ins_trailers`, `safety_ins_varies`, `safety_ins_vehicles`, `surrendered_date`, `tachograph_ins_name`, `trailers_in_possession`, `translate_to_welsh`, `created_on`, `deleted_date`, `expiry_date`, `granted_date`, `in_force_date`, `is_maintenance_suitable`, `last_modified_on`, `lic_no`, `ni_flag`, `olbs_key`, `tot_auth_large_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_small_vehicles`, `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`, `version`, `vi_action`) VALUES (211,124,NULL,125,1,'tach_internal',NULL,NULL,NULL,NULL,NULL,'lsts_not_submitted','B',NULL,NULL,NULL,NULL,0,1,0,1,NULL,'Dan',NULL,0,'2015-03-27 12:28:05',NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:31:10','OB1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,7,NULL);
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES (1,211);
INSERT INTO `application` (`id`, `interim_status`, `licence_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`, `withdrawn_reason`, `administration`, `bankrupt`, `convictions_confirmation`, `declaration_confirmation`, `disqualified`, `financial_evidence_uploaded`, `has_entered_reg`, `insolvency_confirmation`, `insolvency_details`, `interim_auth_trailers`, `interim_auth_vehicles`, `interim_end`, `interim_reason`, `interim_start`, `is_variation`, `liquidation`, `override_ooo`, `prev_been_at_pi`, `prev_been_disqualified_tc`, `prev_been_refused`, `prev_been_revoked`, `prev_conviction`, `prev_had_licence`, `prev_has_licence`, `prev_purchased_assets`, `psv_limousines`, `psv_medium_vhl_confirmation`, `psv_medium_vhl_notes`, `psv_no_limousine_confirmation`, `psv_no_small_vhl_confirmation`, `psv_only_limousines_confirmation`, `psv_operate_small_vhl`, `psv_small_vhl_confirmation`, `psv_small_vhl_notes`, `receivership`, `refused_date`, `safety_confirmation`, `target_completion_date`, `created_on`, `deleted_date`, `granted_date`, `is_maintenance_suitable`, `last_modified_on`, `ni_flag`, `received_date`, `tot_auth_large_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_small_vehicles`, `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`, `version`, `withdrawn_date`) VALUES (7,NULL,211,NULL,'lcat_gv',NULL,'ltyp_sn','apsts_not_submitted',NULL,0,0,1,1,0,0,0,1,'',NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,'2015-03-27 12:28:06',NULL,NULL,NULL,'2015-03-27 12:32:04',0,NULL,NULL,NULL,NULL,1,1,NULL,10,NULL);
INSERT INTO `application_completion` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `last_section`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `undertakings_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (4,7,NULL,NULL,2,2,2,NULL,NULL,2,NULL,2,2,NULL,2,2,2,2,NULL,2,2,2,NULL,NULL,2,'2015-03-27 12:28:07','2015-03-27 12:32:04',19);
INSERT INTO `application_operating_centre` (`id`, `application_id`, `created_by`, `last_modified_by`, `operating_centre_id`, `s4_id`, `ad_placed`, `publication_appropriate`, `sufficient_parking`, `action`, `ad_placed_date`, `ad_placed_in`, `created_on`, `deleted_date`, `is_interim`, `last_modified_on`, `no_of_trailers_required`, `no_of_vehicles_required`, `olbs_key`, `permission`, `version`, `vi_action`) VALUES (4,7,NULL,NULL,73,NULL,0,0,1,'A',NULL,'','2015-03-27 12:30:12',NULL,0,NULL,1,1,NULL,1,1,NULL);
INSERT INTO `application_tracking` (`id`, `application_id`, `created_by`, `last_modified_by`, `addresses_status`, `business_details_status`, `business_type_status`, `community_licences_status`, `conditions_undertakings_status`, `convictions_penalties_status`, `discs_status`, `financial_evidence_status`, `financial_history_status`, `licence_history_status`, `operating_centres_status`, `people_status`, `safety_status`, `taxi_phv_status`, `transport_managers_status`, `type_of_licence_status`, `undertakings_status`, `vehicles_declarations_status`, `vehicles_psv_status`, `vehicles_status`, `created_on`, `last_modified_on`, `version`) VALUES (4,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:28:07',NULL,1);
INSERT INTO `fee` (`id`, `fee_status`, `fee_type_id`, `parent_fee_id`, `payment_method`, `waive_approver_user_id`, `waive_recommender_user_id`, `application_id`, `bus_reg_id`, `created_by`, `irfo_gv_permit_id`, `last_modified_by`, `licence_id`, `task_id`, `amount`, `cheque_po_date`, `cheque_po_number`, `invoice_line_no`, `invoiced_date`, `irfo_fee_exempt`, `irfo_file_no`, `payer_name`, `paying_in_slip_number`, `receipt_no`, `received_amount`, `waive_approval_date`, `waive_reason`, `waive_recommendation_date`, `created_on`, `description`, `irfo_fee_id`, `last_modified_on`, `received_date`, `version`) VALUES (95,'lfs_ot',338,NULL,NULL,NULL,NULL,7,NULL,NULL,NULL,NULL,211,NULL,254.40,NULL,NULL,NULL,'2015-03-27 00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:28:08','GV/SN Application Fee for application 7',NULL,NULL,NULL,1);
INSERT INTO `phone_contact` (`id`, `contact_details_id`, `phone_contact_type`, `created_by`, `last_modified_by`, `details`, `phone_number`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (11,124,'phone_t_tel',NULL,NULL,NULL,'01234 567890','2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `workshop` (`id`, `licence_id`, `contact_details_id`, `created_by`, `last_modified_by`, `is_external`, `maintenance`, `safety_inspection`, `created_on`, `last_modified_on`, `olbs_key`, `removed_date`, `version`) VALUES (1,211,126,NULL,NULL,0,0,0,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
COMMIT;
-- End: Application 7

-- Start: Application 8 - new Goods Vehicle Standard National application with tracking status completed (i.e. ready to grant)
BEGIN;
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (120,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (121,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (122,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:30:12',NULL,NULL,NULL,1);
INSERT INTO `address` (`id`, `admin_area`, `country_code`, `created_by`, `last_modified_by`, `saon_desc`, `paon_desc`, `street`, `locality`, `paon_end`, `paon_start`, `postcode`, `saon_end`, `saon_start`, `town`, `uprn`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (123,NULL,'GB',NULL,NULL,'DVSA','','','',NULL,NULL,'LS9 6NF',NULL,NULL,'Leeds',NULL,'2015-03-27 12:31:05',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (127,120,'ct_corr',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,'dvsa@stolenegg.com',NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (128,121,'ct_est',NULL,NULL,NULL,NULL,0,'2015-03-27 12:29:38',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `contact_details` (`id`, `address_id`, `contact_type`, `person_id`, `created_by`, `last_modified_by`, `fao`, `written_permission_to_engage`, `created_on`, `deleted_date`, `description`, `email_address`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (129,123,'ct_work',NULL,NULL,NULL,NULL,0,'2015-03-27 12:31:05',NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `operating_centre` (`id`, `address_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `olbs_key`, `version`, `vi_action`) VALUES (74,122,NULL,NULL,'2015-03-27 12:30:12',NULL,NULL,1,NULL);
INSERT INTO `licence` (`id`, `correspondence_cd_id`, `enforcement_area_id`, `establishment_cd_id`, `organisation_id`, `tachograph_ins`, `transport_consultant_cd_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`, `traffic_area_id`, `fabs_reference`, `fee_date`, `psv_discs_to_be_printed_no`, `review_date`, `safety_ins`, `safety_ins_trailers`, `safety_ins_varies`, `safety_ins_vehicles`, `surrendered_date`, `tachograph_ins_name`, `trailers_in_possession`, `translate_to_welsh`, `created_on`, `deleted_date`, `expiry_date`, `granted_date`, `in_force_date`, `is_maintenance_suitable`, `last_modified_on`, `lic_no`, `ni_flag`, `olbs_key`, `tot_auth_large_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_small_vehicles`, `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`, `version`, `vi_action`) VALUES (212,127,'V048',127,1,'tach_internal',NULL,NULL,NULL,NULL,NULL,'lsts_consideration','B',NULL,NULL,NULL,NULL,0,1,0,1,NULL,'Dan',NULL,0,'2015-03-27 12:28:05',NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:31:10','ON2',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,7,NULL);
INSERT INTO `licence_no_gen` (`id`, `licence_id`) VALUES (2,212);
INSERT INTO `application` (`id`, `interim_status`, `licence_id`, `created_by`, `goods_or_psv`, `last_modified_by`, `licence_type`, `status`, `withdrawn_reason`, `administration`, `bankrupt`, `convictions_confirmation`, `declaration_confirmation`, `disqualified`, `financial_evidence_uploaded`, `has_entered_reg`, `insolvency_confirmation`, `insolvency_details`, `interim_auth_trailers`, `interim_auth_vehicles`, `interim_end`, `interim_reason`, `interim_start`, `is_variation`, `liquidation`, `override_ooo`, `prev_been_at_pi`, `prev_been_disqualified_tc`, `prev_been_refused`, `prev_been_revoked`, `prev_conviction`, `prev_had_licence`, `prev_has_licence`, `prev_purchased_assets`, `psv_limousines`, `psv_medium_vhl_confirmation`, `psv_medium_vhl_notes`, `psv_no_limousine_confirmation`, `psv_no_small_vhl_confirmation`, `psv_only_limousines_confirmation`, `psv_operate_small_vhl`, `psv_small_vhl_confirmation`, `psv_small_vhl_notes`, `receivership`, `refused_date`, `safety_confirmation`, `target_completion_date`, `created_on`, `deleted_date`, `granted_date`, `is_maintenance_suitable`, `last_modified_on`, `ni_flag`, `received_date`, `tot_auth_large_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_small_vehicles`, `tot_auth_trailers`, `tot_auth_vehicles`, `tot_community_licences`, `version`, `withdrawn_date`) VALUES (8,NULL,212,NULL,'lcat_gv',NULL,'ltyp_sn','apsts_consideration',NULL,0,0,1,1,0,0,0,1,'',NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,'2015-03-27 12:28:06',NULL,NULL,NULL,'2015-03-27 12:32:04',1,'2015-03-27 12:34:56',NULL,NULL,NULL,1,1,NULL,10,NULL);
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
    `undertakings_status`,
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
INSERT INTO `fee` (`id`, `fee_status`, `fee_type_id`, `parent_fee_id`, `payment_method`, `waive_approver_user_id`, `waive_recommender_user_id`, `application_id`, `bus_reg_id`, `created_by`, `irfo_gv_permit_id`, `last_modified_by`, `licence_id`, `task_id`, `amount`, `cheque_po_date`, `cheque_po_number`, `invoice_line_no`, `invoiced_date`, `irfo_fee_exempt`, `irfo_file_no`, `payer_name`, `paying_in_slip_number`, `receipt_no`, `received_amount`, `waive_approval_date`, `waive_reason`, `waive_recommendation_date`, `created_on`, `description`, `irfo_fee_id`, `last_modified_on`, `received_date`, `version`) VALUES (96,'lfs_w',338,NULL,NULL,NULL,NULL,8,NULL,NULL,NULL,NULL,212,NULL,254.40,NULL,NULL,NULL,'2015-03-27 00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-03-27 12:28:08','GV/SN Application Fee for application 7',NULL,NULL,NULL,1);
INSERT INTO `phone_contact` (`id`, `contact_details_id`, `phone_contact_type`, `created_by`, `last_modified_by`, `details`, `phone_number`, `created_on`, `last_modified_on`, `olbs_key`, `olbs_type`, `version`) VALUES (12,127,'phone_t_tel',NULL,NULL,NULL,'01234 567890','2015-03-27 12:29:38',NULL,NULL,NULL,1);
INSERT INTO `workshop` (`id`, `licence_id`, `contact_details_id`, `created_by`, `last_modified_by`, `is_external`, `maintenance`, `safety_inspection`, `created_on`, `last_modified_on`, `olbs_key`, `removed_date`, `version`) VALUES (2,212,129,NULL,NULL,0,0,0,'2015-03-27 12:31:05',NULL,NULL,NULL,1);

INSERT INTO `change_of_entity` (`id`, `licence_id`, `old_licence_no`, `old_organisation_name`, `created_on`, `version`) VALUES ('1', '7', '0000000', 'Old Organisation Name', '2015-03-27 12:28:07', '1');

COMMIT;
-- End: Application 8


INSERT INTO `inspection_request` (`id`, `report_type`, `request_type`, `requestor_user_id`, `result_type`, `application_id`,
`case_id`, `created_by`, `last_modified_by`, `licence_id`, `operating_centre_id`, `task_id`, `deferred_date`, `due_date`, `from_date`,
`inspector_name`, `inspector_notes`, `request_date`, `requestor_notes`, `return_date`, `to_date`, `trailors_examined_no`,
`vehicles_examined_no`, `created_on`, `last_modified_on`, `olbs_key`, `version`)
VALUES
	(1, 'insp_rep_t_maint', 'insp_req_t_coe', 2, 'insp_res_t_new', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-01', NULL, NULL,
    NULL, '2015-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 'insp_rep_t_bus', 'insp_req_t_comp', 2, 'insp_res_t_new_sat', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-02', NULL, NULL,
    NULL, '2015-01-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(3, 'insp_rep_t_TE', 'insp_req_t_new_op', 2, 'insp_res_t_new_unsat', 1, NULL, NULL, NULL, 7, 16, NULL, NULL, '2015-02-03', NULL, NULL,
    NULL, '2015-01-03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
