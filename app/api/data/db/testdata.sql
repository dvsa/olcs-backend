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
TRUNCATE TABLE `bus_service_type`;
TRUNCATE TABLE `bus_reg_bus_service_type`;
TRUNCATE TABLE `bus_reg_variation_reason`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `complaint_oc_licence`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `disc_sequence`;
TRUNCATE TABLE `ebsr_submission`;
TRUNCATE TABLE `fee`;
TRUNCATE TABLE `licence`;
TRUNCATE TABLE `licence_vehicle`;
TRUNCATE TABLE `licence_operating_centre`;
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
TRUNCATE TABLE `user`;
TRUNCATE TABLE `organisation_user`;
TRUNCATE TABLE `vehicle`;
TRUNCATE TABLE `cases`;
TRUNCATE TABLE `case_category`;
TRUNCATE TABLE `impounding`;
TRUNCATE TABLE `impounding_legislation_type`;
TRUNCATE TABLE `team`;
TRUNCATE TABLE `task`;
TRUNCATE TABLE `task_allocation_rule`;
TRUNCATE TABLE `licence`;
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
TRUNCATE TABLE `serious_infringement`;
TRUNCATE TABLE `sla`;
TRUNCATE TABLE `statement`;
TRUNCATE TABLE `submission_action`;
TRUNCATE TABLE `system_parameter`;
TRUNCATE TABLE `publication`;
TRUNCATE TABLE `publication_section`;
TRUNCATE TABLE `publication_link`;
TRUNCATE TABLE `publication_police_data`;
TRUNCATE TABLE `public_holiday`;
TRUNCATE TABLE `community_lic`;
TRUNCATE TABLE `community_lic_suspension`;
TRUNCATE TABLE `community_lic_suspension_reason`;
TRUNCATE TABLE `community_lic_suspension_reason_type`;
TRUNCATE TABLE `community_lic_withdrawal`;
TRUNCATE TABLE `community_lic_withdrawal_reason`;
TRUNCATE TABLE `community_lic_withdrawal_reason_type`;
TRUNCATE TABLE `previous_conviction`;
TRUNCATE TABLE `operating_centre_opposition`;
TRUNCATE TABLE `case_outcome`;

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
    (109,NULL,NULL,'A Place','123 Some Street','Some Area','','WM5 2FA','Birmingham','GB',NOW(),NOW(),1);

INSERT INTO `application` (
    `id`, `licence_id`, `created_by`, `last_modified_by`, `status`,
    `tot_auth_vehicles`, `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`, `tot_auth_large_vehicles`, `tot_community_licences`,
    `tot_auth_trailers`, `bankrupt`, `liquidation`, `receivership`, `administration`,
    `disqualified`, `insolvency_details`, `received_date`,
    `target_completion_date`, `prev_conviction`, `created_on`, `last_modified_on`,
    `version`, `is_variation`, `goods_or_psv`, `ni_flag`, `licence_type`
) VALUES
    (
        1,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2010-12-15 10:48:00',
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, 'ltyp_r'
    ),
    (
        2,7,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,NULL,NULL,
        1,1,'lcat_gv',0, NULL
    ),
    (
        3,210,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,
        NULL,NULL,NOW(),NULL,
        1,0,'lcat_gv',0, NULL
    ),
    (
        6,114,NULL,NULL,'apsts_not_submitted',
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,NULL,NULL,NULL,
        NULL,NULL,'2014-12-15 10:48:00',
        '2015-02-16 10:48:00',NULL,'2014-04-30 12:09:37','2014-04-30 12:09:39',
        1,0,'lcat_psv',1,NULL
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
    `created_on`, `last_modified_on`, `version`, `application_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,34,23,1,0,NULL,'2014-03-13',1,NULL,NULL,1,1,16),
(2,NULL,NULL,34,23,1,0,NULL,'2014-03-21',1,NULL,NULL,1,1,16),
(3,NULL,NULL,34,23,1,0,NULL,'2014-04-01',1,NULL,NULL,1,1,16);

INSERT INTO `licence_operating_centre` (`id`, `created_by`, `last_modified_by`, `no_of_vehicles_required`,
    `no_of_trailers_required`, `sufficient_parking`, `ad_placed`, `ad_placed_in`, `ad_placed_date`, `permission`,
    `created_on`, `last_modified_on`, `version`, `licence_id`, `operating_centre_id`) VALUES
(1,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,7,16),
(2,NULL,NULL,10,0,1,0,NULL,NULL,1,NULL,NULL,1,110,16),
(3,NULL,NULL,14,4,1,0,NULL,NULL,1,NULL,NULL,1,41,17),
(4,NULL,NULL,32,46,1,0,NULL,NULL,1,NULL,NULL,1,7,72);

INSERT INTO `bus_reg` (`id`, `bus_notice_period_id`, `parent_id`, `revert_status`, `subsidised`, `created_by`, `last_modified_by`, `licence_id`, `operating_centre_id`, `status`, `withdrawn_reason`, `application_signed`, `copied_to_la_pte`, `ebsr_refresh`, `finish_point`, `has_manoeuvre`, `has_not_fixed_stop`, `is_quality_contract`, `is_quality_partnership`, `is_short_notice`, `is_txc_app`, `la_short_note`, `manoeuvre_detail`, `map_supplied`, `need_new_stop`, `new_stop_detail`, `not_fixed_stop_detail`, `op_notified_la_pte`, `organisation_email`, `other_details`, `quality_contract_details`, `quality_partnership_details`, `quality_partnership_facilities_used`, `reason_cancelled`, `reason_refused`, `reason_sn_refused`, `received_date`, `reg_no`, `route_description`, `route_no`, `short_notice_refused`, `start_point`, `stopping_arrangements`, `subsidy_detail`, `timetable_acceptable`, `trc_condition_checked`, `trc_notes`, `txc_app_type`, `use_all_stops`, `variation_no`, `via`, `created_on`, `effective_date`, `end_date`, `last_modified_on`, `service_no`, `version`)
VALUES
  (1, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 1, 'breg_s_new', '', 0, 0, 1, 'Sheffield', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-13', 'PD2737280/14686', 'Route description', 14686, 0, 'Doncaster', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'York', NULL, '2014-03-15', NULL, NULL, '90839', 1),
  (2, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 0, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description', 15711, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 0, 'York', NULL, '2014-03-05', '2015-03-05', NULL, '46474', 1),
  (3, 1, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 1, 'breg_s_new', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-11', 'PD2737280/43542', 'Scotish Route description', 43542, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (4, 2, NULL, 'breg_s_new', 'bs_no', 1, 1, 110, 1, 'breg_s_new', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-05-24', 'PD2737280/13245', 'Non-scottish Route description cancelled', 13245, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 0, 'York', NULL, '2014-05-31', NULL, NULL, '26453', 1),
  (5, 2, 2, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 1', 15711, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 1, 'York', NULL, '2014-03-05', '2015-03-05', NULL, '46474', 1),
  (6, 2, 5, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 2', 15711, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '1', 0, 2, 'York', NULL, '2014-03-08', '2015-03-05', NULL, '46474', 1),
  (7, 2, 6, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 3', 15711, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 3, 'York', NULL, '2014-03-10', '2015-03-05', NULL, '46474', 1),
  (8, 1, 3, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-15', 'PD2737280/43542', 'Scotish Route description', 43542, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 1, 'Dundee', NULL, '2014-03-15', NULL, NULL, '34254', 1),
  (9, 1, 8, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-11', 'PD2737280/43542', 'Scotish Route description', 43542, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 2, 'Dundee', NULL, '2014-03-11', NULL, NULL, '34254', 1),
  (10, 1, 9, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-12', 'PD2737280/43542', 'Scotish Route description', 43542, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 3, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (11, 1, 10, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 0, 'Edinburgh', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-03-13', 'PD2737280/43542', 'Scotish Route description', 43542, 0, 'Aberdeen', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 4, 'Dundee', NULL, '2014-03-14', NULL, NULL, '34254', 1),
  (12, 2, 4, 'breg_s_new', 'bs_no', 1, 1, 110, 1, 'breg_s_var', '', 0, 0, 0, 'Doncaster', 0, 0, 0, 0, 0, 1, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-05-27', 'PD2737280/13245', 'Non-scottish Route description cancelled', 13245, 0, 'Leeds', 'Stopping arrangements', '', 0, 0, 'Trc notes', '0', 0, 1, 'York', NULL, '2014-05-27', NULL, NULL, '26453', 1),
  (13, 2, 7, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 4', 15711, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 4, 'York', NULL, '2014-03-10', '2015-03-05', NULL, '46474', 1),
  (14, 2, 13, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_registered', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 5', 15711, 0, 'Leeds', 'Stopping arrangements change 4', '', 0, 0, 'Trc notes', '1', 0, 5, 'York', NULL, '2014-03-10', '2015-03-05', NULL, '46474', 1),
  (15, 2, 14, 'breg_s_var', 'bs_no', 1, 1, 110, 1, 'breg_s_cancellation', '', 0, 0, 1, 'Doncaster', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, '', 'Other details', NULL, NULL, 0, '', '', '', '2014-02-27', 'PD2737280/15711', 'Route description change 6', 15711, 0, 'Leeds', 'Stopping arrangements change 3', '', 0, 0, 'Trc notes', '1', 0, 6, 'York', NULL, '2014-03-10', '2015-03-05', NULL, '46474', 1);

INSERT INTO `bus_reg_traffic_area` (`bus_reg_id`, `traffic_area_id`)
VALUES
  (1, 'B'),
  (1, 'G'),
  (2, 'B'),
  (2, 'G'),
  (12, 'D'),
  (12, 'F'),
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
  (12, 1),
  (12, 3),
  (12, 4);

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

INSERT INTO `complaint` (`complainant_contact_details_id`, `status`, `complaint_type`, `is_compliance`, `created_by`,
    `last_modified_by`, `case_id`, `complaint_date`, `driver_forename`, `driver_family_name`, `description`, `vrm`,
    `created_on`, `last_modified_on`, `version`, `close_date`)
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
   `last_modified_by`,`created_by`,`fao`,`forename`,`family_name`,`written_permission_to_engage`,`email_address`,
   `description`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
    (1,'ct_ta',26,NULL,2,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (3,'ct_corr',109,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (8,'ct_corr',8,10,2,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (21,'ct_reg',21,NULL,0,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (25,'ct_def',25,NULL,4,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (26,'ct_def',26,NULL,0,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (27,'ct_def',27,NULL,2,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (29,'ct_def',29,NULL,3,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (30,'ct_reg',30,NULL,2,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (31,'ct_corr',31,NULL,0,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (37,'ct_oc',37,NULL,2,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (39,'ct_oc',39,NULL,4,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (41,'ct_reg',41,NULL,2,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (42,'ct_corr',42,NULL,1,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (54,'ct_reg',54,NULL,4,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (55,'ct_corr',55,NULL,3,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (63,'ct_reg',63,NULL,3,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (64,'ct_corr',64,NULL,0,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (67,'ct_oc',67,NULL,4,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (72,'ct_oc',72,NULL,2,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (75,'',75,NULL,4,3,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (76,'ct_corr',76,46,4,1,'Important Person',NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (77,'ct_corr',72,46,4,1,'Important Person',NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (100,'ct_reg',100,44,4,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (101,'ct_team_user',26,NULL,4,1,NULL,'Logged in','User',0,'loggedin@user.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (102,'ct_corr',41,NULL,1,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (103,'ct_complainant',72,46,4,1,NULL,'John','Smith',0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (104,'ct_tm',104,NULL,1,1,NULL,NULL,NULL,0,'some@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (105,'ct_team_user',26,NULL,4,1,NULL,'John','Spellman',0,'john@example.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (106,'ct_team_user',26,NULL,4,1,NULL,'Steve','Fox',0,'steve@example.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (107,'ct_complainant',72,33,4,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (108,'ct_complainant',72,34,4,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (109,'ct_complainant',72,35,4,1,NULL,NULL,NULL,0,NULL,NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),

    (110,'ct_complainant',26,60,4,1,NULL,NULL,NULL,0,'l.hamilton@mercedes.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (111,'ct_complainant',26,65,4,1,NULL,NULL,NULL,0,'j.smith@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (112,'ct_complainant',26,66,4,1,NULL,NULL,NULL,0,'t.cooper@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (113,'ct_complainant',26,77,4,1,NULL,NULL,NULL,0,'t.jones@example.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (114,'ct_team_user',26,NULL,4,1,NULL,'Another','User',0,'another@user.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (115,'ct_tm',104,NULL,1,1,NULL,NULL,NULL,0,'some@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (116,'ct_tm',104,NULL,1,1,NULL,NULL,NULL,0,'some@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (117,'ct_tm',55,80,1,1,NULL,NULL,NULL,0,'anotherone@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (118,'ct_tm',63,80,1,1,NULL,NULL,NULL,0,'anotherone@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (119,'ct_tm',72,80,1,1,NULL,NULL,NULL,0,'anotherone@email.com',NULL,NULL,'2014-11-24 10:30:04','2014-11-24 10:30:04',1),
    (120,'ct_corr',105,4,1,1,NULL,NULL,NULL,0,'some1@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (121,'ct_corr',106,9,1,1,NULL,NULL,NULL,0,'some2@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (122,'ct_corr',107,10,1,1,NULL,NULL,NULL,0,'some3@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1),
    (123,'ct_corr',108,11,1,1,NULL,NULL,NULL,0,'some4@email.com',NULL,NULL,'2014-11-24 10:30:04',
    '2014-11-24 10:30:04',1);

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
  (1, null, 1, 1, 1, null, 110, null, null, null, null, null, null, null, null, null, null, null, null, null,
   null, 0, null);

INSERT INTO `fee` (`id`, `application_id`, `licence_id`, `fee_status`, `receipt_no`, `created_by`, `last_modified_by`, `description`,
    `invoiced_date`, `received_date`, `amount`, `received_amount`, `created_on`, `last_modified_on`, `version`, `payment_method`, `waive_reason`, `fee_type_id`) VALUES
    (7,NULL,7,'lfs_ot',NULL,1,NULL,'Application fee','2013-11-25 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (30,NULL,110,'lfs_pd','654321',1,2,'Application fee','2013-11-22 00:00:00','2014-01-13 00:00:00',251.00,251.00,NULL,NULL,1,'fpm_card_online',NULL,1),
    (41,NULL,110,'lfs_wr','345253',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (54,NULL,110,'lfs_ot','829485',1,NULL,'Application fee','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (63,NULL,110,'lfs_ot','481024',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (75,NULL,110,'lfs_ot','964732',1,NULL,'Application fee','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (76,1,110,'lfs_wr','234343',1,NULL,'Application fee 1','2013-11-25 00:00:00',NULL,250.50,0.50,NULL,NULL,2,NULL,NULL,1),
    (77,1,110,'lfs_wr','836724',1,NULL,'Application fee 2','2013-11-22 00:00:00',NULL,251.75,0.00,NULL,NULL,2,NULL,NULL,1),
    (78,1,110,'lfs_wr','561023',1,NULL,'Grant fee','2013-11-21 00:00:00',NULL,150.00,0.00,NULL,NULL,3,NULL,NULL,1),
    (79,1,110,'lfs_wr','634820',1,NULL,'Application fee 3','2013-11-12 00:00:00',NULL,250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (80,1,110,'lfs_pd','458750',1,2,'Application fee 4','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash',NULL,1),
    (81,1,110,'lfs_ot','837495',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (82,1,30,'lfs_ot','354784',1,NULL,'Bus route 1','2013-10-23 00:00:00',NULL,500.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (83,1,110,'lfs_wr','435235',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (84,1,110,'lfs_ot','435563',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (85,1,110,'lfs_wr','534633',1,NULL,'Application fee 4','2013-11-10 00:00:00',NULL,250.00,0.00,NULL,NULL,1,NULL,NULL,1),
    (86,1,110,'lfs_ot','426786',1,NULL,'Application fee 5','2013-11-10 00:00:00',NULL,1250.00,0.00,NULL,NULL,2,NULL,NULL,1),
    (87,1,110,'lfs_w','68750',1,2,'Application fee 6','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_cash','some waive reason',1),
    (88,1,110,'lfs_cn','78750',1,2,'Application fee 7','2013-11-10 00:00:00','2014-01-04 00:00:00',250.00,250.00,NULL,NULL,1,'fpm_card_online',NULL,1),
    (89,3,210,'lfs_w', '87654',1,2,'Application fee 8','2013-11-10 00:00:00','2015-01-09 00:00:00',254.40,254.40,NULL,NULL,1,'fpm_waive','waived for demo purposes',1);

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `created_by`, `correspondence_cd_id`, `establishment_cd_id`,
    `transport_consultant_cd_id`, `last_modified_by`,
    `goods_or_psv`, `lic_no`, `status`,
    `ni_flag`, `licence_type`, `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`,
    `tot_auth_trailers`, `tot_auth_vehicles`, `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`,
    `safety_ins_vehicles`, `safety_ins_trailers`, `safety_ins_varies`,
    `tachograph_ins`, `tachograph_ins_name`, `created_on`, `last_modified_on`, `version`, `expiry_date`, `tot_community_licences`) VALUES
    (7,1,'B',1,102,NULL,104,NULL,'lcat_gv','OB1234567','lsts_valid',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12',
    '',4,12,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),

    -- extra licence for application 1
    (201,1,'B',0,NULL,NULL,NULL,1,NULL,'OB4234560','lsts_not_submitted',NULL,NULL,'2011-03-16','2011-03-16',
    '2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (202,1,'B',0,NULL,NULL,NULL,1,'lcat_gv','OB4234561','lsts_consideration',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (203,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234562','lsts_surrendered',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (204,1,'B',0,NULL,NULL,NULL,1,'lcat_gv','OB4234563','lsts_unlicenced',1,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (205,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234564','lsts_terminated',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (206,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234565','lsts_withdrawn',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (207,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234566','lsts_suspended',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (208,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234567','lsts_curtailed',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',1,
    3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (209,1,'B',0,NULL,NULL,NULL,1,'lcat_psv','OB4234568','lsts_revoked',0,'ltyp_sn','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),

    -- extra licence for application 3
    (210,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1,NULL, NULL),


    (30,30,'B',0,NULL,NULL,NULL,1,'lcat_gv','OB1234568','lsts_not_submitted',0,'ltyp_si','2011-03-16','2011-03-16','2011-03-16','',3,
    9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (41,41,'B',2,NULL,NULL,NULL,2,'lcat_gv','OB1234577','lsts_not_submitted',0,'ltyp_sn','2007-01-12','2007-01-12','2007-01-12','',1,
    21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (54,54,'B',2,NULL,NULL,NULL,4,'lcat_gv','OB1234578','lsts_not_submitted',0,'ltyp_r','2007-01-12','2007-01-12','2007-01-12','',0,4,NULL,NULL,NULL,NULL,
    NULL,NULL, NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (63,63,'D',4,NULL,NULL,NULL,0,'lcat_psv','PD1234589','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',1,7,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (75,75,'D',4,NULL,NULL,NULL,4,'lcat_psv','PD2737289','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', NULL),
    (100,100,'D',4,NULL,NULL,NULL,0,'lcat_psv','PD1001001','lsts_not_submitted',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,
    NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),2, '2016-01-01 10:00:00', NULL),
    (110,75,'D',4,8,21,25,4,'lcat_psv','PD2737280','lsts_not_submitted',0,'ltyp_r','2010-01-12','2010-01-12',
    '2010-01-12','',0,10,5,5,NULL,NULL,
    NULL,NULL,NULL,NOW(),NOW(),1, '2016-01-01 10:00:00', 4),
    (114,104,'B',NULL,NULL,NULL,NULL,NULL,NULL,'OB1534567','lsts_not_submitted',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1, '2016-01-01 10:00:00', NULL),
    (115,105,'S',NULL,NULL,NULL,NULL,NULL,'lcat_psv','TS1234568','lsts_not_submitted',0,'ltyp_sr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NOW(),NULL,1, '2016-01-01 10:00:00', NULL);

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
  (1, 'otf_eob', 7, 1, 29, 1, 1, 1, 1, '2014-02-19', 1, 1, 0, 1, 'Valid notes', 'Notes', null, '2014-02-20 00:00:00',
   '2014-02-20 00:00:00', 1),
  (2, 'otf_rep', 7, 1, 29, 1, 1, 1, 1, '2014-02-19', 0, 0, 1, 1, 'Valid notes', 'Notes', null, '2014-02-20 00:00:00',
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
    (4,NULL,NULL,'Aldershot','Mr','1960-02-01 00:00:00','Jack','Da Ripper',NULL,NULL,NULL,1,NULL),
    (8,NULL,NULL,'Birmingham','Mr','1960-02-01 00:00:00','Simon','Fish',NULL,NULL,NULL,1,NULL),
    (9,NULL,NULL,'Cheltenham','Mr','1960-02-15 00:00:00','John','Smith',NULL,NULL,NULL,1,NULL),
    (10,NULL,NULL,'Darlington','Mr','1965-07-12 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (11,NULL,NULL,'Edinburgh','Mr','1970-04-14 00:00:00','Joe','Lambert',NULL,NULL,NULL,1,NULL),
    (12,NULL,NULL,'Farnham','Mr','1975-04-15 00:00:00','Tom','Cooper',NULL,NULL,NULL,1,NULL),
    (13,NULL,NULL,'Godmanchester','Mr','1973-03-03 00:00:00','Mark','Anthony',NULL,NULL,NULL,1,NULL),
    (14,NULL,NULL,'Hereford','Mr','1975-02-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (15,NULL,NULL,'Isle of Wight','Mr','1973-12-09 00:00:00','Tom','Anthony',NULL,NULL,NULL,1,NULL),
    (32,NULL,NULL,'Jamaica','Mr','1960-04-15 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (33,NULL,NULL,'Kiddiminster','Mr','1965-04-12 00:00:00','Mark','Jones',NULL,NULL,NULL,1,NULL),
    (34,NULL,NULL,'London','Mr','1970-06-14 00:00:00','Tim','Lambert',NULL,NULL,NULL,1,NULL),
    (35,NULL,NULL,'Manchester','Mr','1975-04-18 00:00:00','Joe','Cooper',NULL,NULL,NULL,1,NULL),
    (43,NULL,NULL,'Newcastle','Mr','1960-02-15 00:00:00','Ted','Smith',NULL,NULL,NULL,1,NULL),
    (44,NULL,NULL,'Otley','Mr','1970-04-14 00:00:00','Peter','Lambert',NULL,NULL,NULL,1,NULL),
    (45,NULL,NULL,'Peterborough','Mr','1975-04-15 00:00:00','Mark','Cooper',NULL,NULL,NULL,1,NULL),
    (46,NULL,NULL,'Quatar','Mr','1973-03-03 00:00:00','David','Anthony',NULL,NULL,NULL,1,NULL),
    (47,NULL,NULL,'Rotherham','Mr','1975-02-15 00:00:00','Lewis','Howarth',NULL,NULL,NULL,1,NULL),
    (59,NULL,NULL,'Swansea','Mr','1973-03-03 00:00:00','Peter','Smith',NULL,NULL,NULL,1,NULL),
    (60,NULL,NULL,'Tadcaster','Mr','1975-02-15 00:00:00','Lewis','Hamilton',NULL,NULL,NULL,1,NULL),
    (65,NULL,NULL,'Upminster','Mr','1972-02-15 00:00:00','Jonathan','Smith',NULL,NULL,NULL,1,NULL),
    (66,NULL,NULL,'Victoria','Mr','1975-03-15 00:00:00','Tim','Cooper',NULL,NULL,NULL,1,NULL),
    (77,NULL,NULL,'Leeds','Mr','1972-02-15 00:00:00','Tom','Jones',NULL,NULL,NULL,1,NULL),
    (78,NULL,NULL,'Xanten','Mr','1975-03-15 00:00:00','Keith','Winnard',NULL,NULL,NULL,1,NULL),
    (79,NULL,NULL,'York','Mr','1975-04-15 00:00:00','James','Bond',NULL,NULL,NULL,1,NULL),
    (80,NULL,NULL,'Zurich','Mr','1975-04-15 00:00:00','Dave','Smith',NULL,NULL,NULL,1,NULL);

INSERT INTO `disqualification` (
    `id`, `created_by`, `last_modified_by`, `is_disqualified`, `period`,
    `notes`, `created_on`, `last_modified_on`, `version`, `person_id`
) VALUES
    (10,NULL,NULL,1,'2 months',
        'TBC',NOW(),NULL,1,10),
    (13,NULL,NULL,1,'2 months',
        'TBC',NOW(),NULL,1,13),
    (15,NULL,NULL,1,'6 months',
        'TBC',NOW(),NULL,1,15),
    (32,NULL,NULL,1,'2 months',
        'TBC',NOW(),NULL,1,32),
    (36,NULL,NULL,1,'6 months',
        'TBC',NOW(),NULL,1,15);

INSERT INTO `phone_contact` (`id`,`phone_contact_type`,`phone_number`,`details`,
    `contact_details_id`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'phone_t_tel','0113 123 1234','',101,NULL,NULL,NULL,NULL,1),
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
	(1, 1, 'apsts_not_submitted', NULL, NULL, 'tm_t_I', 1, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 1, 1, 1, NULL, NULL, 1),
	(2, 2, 'apsts_not_submitted', NULL, NULL, 'tm_t_I', 1, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 2, 2, 2, NULL,
	NULL, 1),
  (3, 1, 'apsts_not_submitted', NULL, NULL, 'tm_t_I', 3, 'A', NULL, NULL, NULL, 1, 1, NULL, NULL, 3, 4, 5, NULL,
  NULL, 1),
	(4, 2, 'apsts_not_submitted', NULL, NULL, 'tm_t_I', 3, 'A', NULL, NULL, NULL, 2, 2, NULL, NULL, 6, 7, 8, NULL,
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

INSERT INTO `tm_case_decision_rehab` (`tm_case_decision_rehab_id`,`rehab_measure_id`) VALUES
  (1,'tm_rehab_adc');

INSERT INTO `tm_case_decision_unfitness` (`tm_case_decision_unfitness_id`,`unfitness_reason_id`) VALUES
  (1,'tm_unfit_inn');

INSERT INTO `user` (`id`, `team_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`, `version`, `deleted_date`,
    `login_id`,`contact_details_id`,`job_title`,`division_group`,`department_name`) VALUES
    (1,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'loggedinuser',101,'Accountant','Division 1','Department X'),
    (2,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'johnspellman',105,'','',''),
    (3,2,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'stevefox',106,'','',''),
    (4,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'amywrigg',NULL,'','',''),
    (5,1,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'philjowitt',NULL,'','',''),
    (6,3,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'kevinrooney',NULL,'','',''),
    (7,4,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'sarahthompson',NULL,'','',''),
    (8,8,NULL,NULL,'2013-11-27 00:00:00','2013-11-27 00:00:00',1,NULL,'anotheruser',114,'','','');

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
   `last_modified_by`,`created_by`,`ecms_no`,`open_date`,`closed_date`,`description`,`is_impounding`,
   `erru_originating_authority`,`erru_transport_undertaking_name`,`erru_vrm`,`annual_test_history`,`prohibition_note`,
   `conviction_note`,`penalties_note`,`deleted_date`,`created_on`,`last_modified_on`,`version`)
VALUES
  (24,'case_t_lic',NULL,7,NULL,NULL,NULL,NULL,'E123456','2012-03-21',NULL,'Case for convictions against company
  directors',0,NULL,NULL,NULL,'Annual test history for case 24','prohibition test notes','test comments',NULL,NULL,
  '2013-11-12 12:27:33',NULL,1),
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
    (9,1,'IRFO Team','');

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
    (1,9,5,NULL,NULL,NULL,NULL),
    (2,3,6,NULL,NULL,NULL,NULL),
    (3,2,7,NULL,NULL,NULL,NULL),
    (4,7,8,8,   NULL,NULL,NULL),
    (5,8,9,NULL,NULL,NULL,NULL),
    (6,1,5,NULL,NULL,NULL,NULL);

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
  (13, 1, 1, 'PI Hearing', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (14, 1, 1, 'PI Decision', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (21, 1, 1, 'Bus Registration New Granted', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (22, 1, 1, 'Bus Registration New Granted (SN)', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (23, 1, 1, 'Bus Registration Variation Granted', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (24, 1, 1, 'Bus Registration Variation Granted (SN)', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (25, 1, 1, 'Bus Registration Cancellation Granted', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (26, 1, 1, 'Bus Registration Cancellation Granted (SN)', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (27, 1, 1, 'TM PI Hearing', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1),
  (28, 1, 1, 'TM PI Decision', '2014-10-30 00:00:00', '2014-10-30 00:00:00', 1);

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
  (17,'pub_s_printed',1,1,'M','2014-10-30',NULL,1891,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (18,'pub_s_printed',1,1,'M','2014-10-30',NULL,2014,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (19,'pub_s_printed',1,1,'N','2014-10-30',NULL,30,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (20,'pub_s_printed',1,1,'N','2014-10-30',NULL,2,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),

  (21,'pub_s_new',1,1,'M','2014-10-30',NULL,6666,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (22,'pub_s_new',1,1,'M','2014-10-20',NULL,7777,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (23,'pub_s_new',1,1,'N','2014-10-31',NULL,8888,'A&D','2014-10-30 00:00:00','2014-10-30 00:00:00',1),
  (24,'pub_s_new',1,1,'N','2014-10-21',NULL,9999,'N&P','2014-10-30 00:00:00','2014-10-30 00:00:00',1);

INSERT INTO `publication_link` (`id`,`pi_id`,`publication_id`,`publication_section_id`,`application_id`,`bus_reg_id`,`created_by`,`last_modified_by`,`licence_id`,`traffic_area_id`,`text1`,`text2`,`text3`,`created_on`,`deleted_date`,`last_modified_on`,`version`)
  VALUES
    (1,1,1,13,NULL,NULL,NULL,NULL,7,'B','Public Inquiry (1) to be held at venue_1, Unit 9, Shapely Industrial Estate, Harehills, Leeds, LS9 2FA, on 16 March 2014 commencing at 14:30 \nOB1234567 SN \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',NULL,'2014-11-25 15:47:03',NULL,NULL,1),
    (2,1,3,13,NULL,NULL,NULL,NULL,7,'B','Public Inquiry (1) to be held at venue_1, Unit 9, Shapely Industrial Estate, Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) Previous hearing on 16 March 2014 was adjourned. \nOB1234567 SN \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S23 - Consider attaching conditions under Section 23\r\nS23 - Consider attaching conditions under Section 23\r\nS24 - Consideration of interim licence under Section 24\r\nS25 - Consideration of interim variation under Section 25\r\nS26 - Consideration of disciplinary action under Section 26',NULL,'2014-11-25 15:47:03',NULL,NULL,1),
    (3,1,3,14,NULL,NULL,NULL,NULL,7,'B','Public Inquiry (1) held at venue_1, Unit 9, Shapely Industrial Estate,
    Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) \nOB1234567 SN \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S13 - Consideration of new application under Section 13',NULL,'2014-12-11 10:03:15',NULL,NULL,1),

    (4,1,21,3,1,NULL,NULL,NULL,7,'B','Public Inquiry (1) held at venue_1, Unit 9, Shapely Industrial Estate,
    Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) \nOB1234567 SN
    \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S13 - Consideration of new application under Section 13',NULL,'2014-12-11 10:03:15',NULL,NULL,1),
    (5,1,22,14,1,NULL,NULL,NULL,7,'B','Public Inquiry (1) held at venue_1, Unit 9, Shapely Industrial Estate,
    Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) \nOB1234567 SN
    \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S13 - Consideration of new application under Section 13',NULL,'2014-12-11 10:03:15',NULL,NULL,1),
    (6,1,23,1,1,NULL,NULL,NULL,7,'B','Public Inquiry (1) held at venue_1, Unit 9, Shapely Industrial Estate,
    Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) \nOB1234567 SN
    \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S13 - Consideration of new application under Section 13',NULL,'2014-12-11 10:03:15',NULL,NULL,1),
    (7,1,24,1,2,NULL,NULL,NULL,7,'B','Public Inquiry (1) held at venue_1, Unit 9, Shapely Industrial Estate,
    Harehills, Leeds, LS9 2FA, on 5 April 2014 commencing at 14:30 (Previous Publication:(6128)) \nOB1234567 SN \nJOHN SMITH HAULAGE LTD.\nT/A JSH LOGISTICS \nDirector(s): TOM JONES, KEITH WINNARD \nSOLWAY BUSINESS CENTRE, KINGSTOWN, CARLISLE, CA6 4BY','S13 - Consideration of new application under Section 13',NULL,'2014-12-11 10:03:15',NULL,NULL,1);

INSERT INTO `publication_police_data` (`id`,`publication_link_id`,`created_by`,`last_modified_by`,`olbs_dob`,`olbs_id`,`birth_date`,`created_on`,`family_name`,`forename`,`last_modified_on`,`version`)
  VALUES
    (1,1,NULL,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:00:34','Jones','Tom',NULL,1),
    (2,1,NULL,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:00:35','Winnard','Keith',NULL,1),
    (3,2,NULL,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:02:18','Jones','Tom',NULL,1),
    (4,2,NULL,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:02:19','Winnard','Keith',NULL,1),
    (5,3,NULL,NULL,NULL,NULL,'1972-02-15','2014-12-11 10:03:15','Jones','Tom',NULL,1),
    (6,3,NULL,NULL,NULL,NULL,'1975-03-15','2014-12-11 10:03:16','Winnard','Keith',NULL,1);

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
    (8, 'cl_sts_pending', 110, NULL, 7, NULL, 'UKNI', NULL, NULL);

INSERT INTO `community_lic_suspension` (`id`, `community_lic_id`, `created_by`,
    `last_modified_by`, `is_actioned`, `created_on`, `end_date`, `last_modified_on`, `start_date`, `version`)
VALUES
	(1, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `community_lic_suspension_reason` (`id`, `community_lic_suspension_id`, `type_id`, `created_by`,
    `last_modified_by`, `created_on`, `deleted_date`, `last_modified_on`, `version`)
VALUES
	(1, 1, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1),
	(2, 2, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `community_lic_withdrawal` (`id`, `community_lic_id`, `created_by`, `last_modified_by`,
    `created_on`, `end_date`, `last_modified_on`, `start_date`, `version`)
VALUES
	(1, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `community_lic_withdrawal_reason` (`id`, `community_lic_withdrawal_id`, `type_id`,
    `created_by`, `last_modified_by`, `created_on`, `deleted_date`, `last_modified_on`, `version`)
VALUES
	(1, 1, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1),
	(2, 2, 'cl_sw_reason_other', NULL, NULL, NULL, NULL, NULL, 1);

INSERT INTO `statement`
(`id`, `contact_type`, `requestors_contact_details_id`, `statement_type`, `case_id`, `created_by`,`last_modified_by`,
`authorisers_decision`, `authorisers_title`, `stopped_date`, `requested_date`, `requestors_body`, `issued_date`,
`created_on`, `last_modified_on`, `version`, `vrm`)
VALUES
  (1, 'cm_letter', 120, 'statement_t_43', 24, 1, 1, 'Authorisers decision 1', 'Authorisers title 1', '2014-05-01',
  '2014-01-01', 'Requestors body 1', '2014-01-08', '2013-01-01', '2013-01-02', 1, 'VRM 1'),
  (2, 'cm_fax', 121, 'statement_t_9', 24, 1, 1, 'Authorisers decision 2', 'Authorisers title 2', '2014-06-02',
  '2014-02-02', 'Requestors body 2', '2014-01-09', '2013-01-02', '2013-01-03', 1, 'VRM 2'),
  (3, 'cm_email', 122, 'statement_t_36', 24, 1, 1, 'Authorisers decision 3', 'Authorisers title 3', '2014-07-03',
  '2014-03-03', 'Requestors body 3', '2014-01-10', '2013-01-03', '2013-01-04', 1, 'VRM 3'),
  (4, 'cm_tel', 123, 'statement_t_38', 24, 1, 1, 'Authorisers decision 4', 'Authorisers title 4', '2014-08-04',
  '2014-04-04', 'Requestors body 4', '2014-01-11', '2013-01-04', '2013-01-05', 1, 'VRM 4');

INSERT INTO `previous_conviction` (`id`, `conviction_date`, `transport_manager_id`, `category_text`, `notes`,
   `court_fpn`, `penalty`, `version`)
VALUES
  (1, '2014-10-30 10:00:00', 1, 'Offence 1', 'Offence 1 details', 'Court 1', 'Penalty 1', 1),
  (2, '2014-11-30 11:00:00', 1, 'Offence 2', 'Offence 2 details', 'Court 2', 'Penalty 2', 1),
  (3, '2012-10-30 10:00:00', 3, 'Offence 3', 'Offence 3 details', 'Court 3', 'Penalty 3', 1),
  (4, '2011-11-30 11:00:00', 3, 'Offence 4', 'Offence 4 details', 'Court 4', 'Penalty 4', 1);

SET foreign_key_checks = 1;
