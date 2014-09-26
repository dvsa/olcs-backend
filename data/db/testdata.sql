SET foreign_key_checks = 0;

TRUNCATE TABLE `address`;
TRUNCATE TABLE `admin_area_traffic_area`;
TRUNCATE TABLE `application`;
TRUNCATE TABLE `application_completion`;
TRUNCATE TABLE `application_operating_centre`;
TRUNCATE TABLE `bus_reg`;
TRUNCATE TABLE `bus_reg_other_service`;
TRUNCATE TABLE `complaint`;
TRUNCATE TABLE `complaint_case`;
TRUNCATE TABLE `condition_undertaking`;
TRUNCATE TABLE `contact_details`;
TRUNCATE TABLE `conviction`;
TRUNCATE TABLE `driver`;
TRUNCATE TABLE `document`;
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
TRUNCATE TABLE `pi_decision`;
TRUNCATE TABLE `pi_type`;
TRUNCATE TABLE `pi_hearing`;
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
TRUNCATE TABLE `licence`;

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

INSERT INTO `admin_area_traffic_area`(id, traffic_area_id) VALUES
('NEWCASTLE UPON TYNE','B'),
('GATESHEAD','B'),
('NORTH TYNESIDE','B'),
('SOUTH TYNESIDE','B'),
('SUNDERLAND','B'),
('BLACKBURN WITH DARWEN','C'),
('BLACKPOOL','C'),
('BOLTON','C'),
('BURY','C'),
('LANCASHIRE','C'),
('CHESHIRE EAST','C'),
('CHESHIRE WEST AND CHESTER','C'),
('CALDERDALE','B'),
('HALTON','C'),
('KNOWSLEY','C'),
('LIVERPOOL','C'),
('MANCHESTER','C'),
('STAFFORDSHIRE','D'),
('OLDHAM','C'),
('ROCHDALE','C'),
('SALFORD','C'),
('SEFTON COUNCIL','C'),
('ST HELENS COUNCIL','C'),
('STOCKPORT','B'),
('TAMESIDE','C'),
('TRAFFORD','C'),
('WIGAN','C'),
('WARRINGTON','C'),
('WIRRAL','C'),
('NOTTINGHAMSHIRE','B'),
('DERBYSHIRE','C'),
('LINCOLNSHIRE','B'),
('LEICESTERSHIRE','F'),
('NORTHAMPTONSHIRE','F'),
('CITY OF STOKE-ON-TRENT','D'),
('DERBY','C'),
('DUDLEY','D'),
('CAMBRIDGESHIRE','F'),
('LEICESTER CITY','F'),
('NOTTINGHAM CITY','B'),
('WARWICKSHIRE','D'),
('PETERBOROUGH','F'),
('RUTLAND','F'),
('WALSALL','D'),
('WOLVERHAMPTON','D'),
('WORCESTERSHIRE','D'),
('BIRMINGHAM','D'),
('COVENTRY','D'),
('SHROPSHIRE','D'),
('GLOUCESTERSHIRE','H'),
('WEST MIDLANDS','D'),
('SANDWELL','D'),
('SOLIHULL','D'),
('TELFORD AND WREKIN','D'),
('BRACKNELL FOREST','H'),
('BUCKINGHAMSHIRE','F'),
('HAMPSHIRE','H'),
('HERTFORDSHIRE','F'),
('CENTRAL BEDFORDSHIRE','F'),
('LUTON','F'),
('HARROW','K'),
('ESSEX','F'),
('MILTON KEYNES','F'),
('WEST BERKSHIRE','H'),
('BEDFORD','F'),
('READING','H'),
('SLOUGH','H'),
('OXFORDSHIRE','H'),
('WINDSOR AND MAIDENHEAD','H'),
('SURREY','K'),
('WOKINGHAM','H'),
('SUFFOLK','F'),
('NORFOLK','F'),
('SOUTHEND-ON-SEA','F'),
('THURROCK','F'),
('WEST SUSSEX','K'),
('KENT','K'),
('BRIGHTON & HOVE','K'),
('EAST SUSSEX','K'),
('CROYDON','K'),
('LONDON BOROUGH OF HOUNSLOW','K'),
('SUTTON','K'),
('PORTSMOUTH CITY COUNCIL','H'),
('MEDWAY','K'),
('WILTSHIRE','H'),
('SOUTHAMPTON','H'),
('ISLE OF WIGHT','H'),
('CITY OF LONDON','K'),
('CITY OF WESTMINSTER','K'),
('BARKING AND DAGENHAM','F'),
('BEXLEY','K'),
('BARNET','F'),
('BRENT','K'),
('CAMDEN','K'),
('EALING','K'),
('ENFIELD','K'),
('LONDON BOROUGH OF BROMLEY','K'),
('GREENWICH','K'),
('ISLINGTON','K'),
('KENSINGTON AND CHELSEA','K'),
('HILLINGDON','K'),
('HAVERING','K'),
('HAMMERSMITH AND FULHAM','K'),
('LONDON BOROUGH OF HARINGEY','K'),
('KINGSTON UPON THAMES','K'),
('HACKNEY','K'),
('LAMBETH','K'),
('RICHMOND UPON THAMES','K'),
('NEWHAM','K'),
('TOWER HAMLETS','K'),
('LEWISHAM','K'),
('REDBRIDGE','K'),
('CARMARTHENSHIRE','G'),
('FLINTSHIRE','G'),
('CONWY','G'),
('CEREDIGION','G'),
('GWYNEDD','G'),
('DENBIGHSHIRE','G'),
('SOUTHWARK','K'),
('CARDIFF','G'),
('POWYS','G'),
('WALTHAM FOREST','K'),
('BLAENAU GWENT','G'),
('MERTON','K'),
('MONMOUTHSHIRE','G'),
('ISLE OF ANGLESEY','G'),
('NEWPORT','D'),
('GREATER LONDON','K'),
('MERTHYR TYDFIL UA','G'),
('TORFAEN','G'),
('SWANSEA','G'),
('WREXHAM','G'),
('PEMBROKESHIRE','G'),
('VALE OF GLAMORGAN','G'),
('RHONDDA CYNON TAFF','G'),
('CAERPHILLY COUNTY BOROUGH','G'),
('BRIDGEND COUNTY BOROUGH','G'),
('NEATH PORT TALBOT','G'),
('BARNSLEY','B'),
('BRADFORD MDC','B'),
('WEST YORKSHIRE','B'),
('DONCASTER','B'),
('NORTH YORKSHIRE','B'),
('YORK','B'),
('KIRKLEES','B'),
('KINGSTON UPON HULL','B'),
('LEEDS','B'),
('ROTHERHAM','B'),
('SHEFFIELD','B'),
('WAKEFIELD','B'),
('STOCKTON-ON-TEES','B'),
('NORTH EAST LINCOLNSHIRE','B'),
('NORTH LINCOLNSHIRE','B'),
('EAST RIDING OF YORKSHIRE','B'),
('CUMBRIA','C'),
('NORTHUMBERLAND','B'),
('DURHAM','B'),
('DARLINGTON','B'),
('MIDDLESBROUGH','B'),
('HARTLEPOOL','B'),
('BOURNEMOUTH','H'),
('REDCAR AND CLEVELAND','B'),
('DORSET','H'),
('CORNWALL','H'),
('DEVON','H'),
('SOMERSET','H'),
('PLYMOUTH','H'),
('POOLE','H'),
('TORBAY','H'),
('BRISTOL','H'),
('HEREFORDSHIRE','D'),
('SWINDON','H'),
('NORTH SOMERSET','H'),
('BATH AND NORTH EAST SOMERSET','H'),
('SOUTH GLOUCESTERSHIRE','H'),
('ISLES OF SCILLY','H'),
('NORTH LANARKSHIRE','M'),
('GLASGOW CITY','M'),
('South Lanarkshire','M'),
('HIGHLAND','M'),
('FALKIRK','M'),
('DUMFRIES AND GALLOWAY','M'),
('Perth And Kinross','M'),
('West Lothian','M'),
('STIRLING','M'),
('DUNDEE CITY','M'),
('FIFE','M'),
('SOUTH AYRSHIRE','M'),
('MORAY','M'),
('SCOTTISH BORDERS','M'),
('MIDLOTHIAN','M'),
('AYRSHIRE','M'),
('EAST LOTHIAN','M'),
('ISLE OF ARRAN','M'),
('EAST DUNBARTONSHIRE','M'),
('WEST DUNBARTONSHIRE','M'),
('CITY OF EDINBURGH','M'),
('SHETLAND','M'),
('Aberdeenshire','M'),
('ORKNEY ISLANDS','M'),
('NA H-EILEANAN AN IAR','M'),
('ARGYLL AND BUTE','M'),
('ANGUS','M'),
('ABERDEEN CITY','M'),
('EAST AYRSHIRE','M'),
('EAST RENFREWSHIRE','M'),
('CLACKMANNANSHIRE','M'),
('Renfrewshire','M'),
('Inverclyde','M'),
('ISLE OF CUMBRAE','M'),
('ORKNEY','M'),
('HIGHLANDS','M'),
('GLASGOW','M'),
('ABERDEEN CUTY','M'),
('EASTDUNBARTONSHIRE','M'),
('EAS DUNBARTONSHIRE','M'),
('GLASGOW CITY COUCIL','M'),
('HIGHANDS','M'),
('NORTH AYRSHIRE','M'),
('AYR','M'),
('SOUTH AYSHIRE','M'),
('EAST DUNNBARTONSHIRE','M'),
('LONDON BOROUGH OF HAVERING','K'),
('EASE DUNBARTONSHIRE','M'),
('ABERDEEN C ITY','M');

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
  (1, 1, 'subsidised_key1', 1, '', 110, 1, 1, 14686, 'PD2737280/14686', 'Doncaster', 'Doncaster', 'Doncaster', 'Other details', 1,
   1, 1, '', 1, '', 1, '', '', 1, 1, 'Route description', 1, 1, 1, null, 0, 1, 'Stopping arrangements', 1,
  'Trc notes', 'breg_s_registered', 'revert status', '', 1, '', '', '', '', 0, 90839, null, null, null, null, null, 1);

INSERT INTO `bus_reg_other_service`
(`id`, `bus_reg_id`, `last_modified_by`, `created_by`, `service_no`, `created_on`, `last_modified_on`, `version`)
VALUES
  (1, 1, 1, 1, 90840, '2013-11-25 00:00:00', '2013-11-27 13:41:00', 1),
  (2, 1, 1, 1, 90841, '2013-11-26 00:00:00', '2013-11-28 15:47:00', 1);


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
    (1,24,NULL,16,NULL,NULL,'Case','cat_oc','cdt_con',NULL,NULL,0,0,'Some notes 1',NOW(),NULL,1),
    (2,24,NULL,16,NULL,NULL,'Case','cat_oc','cdt_con',NULL,NULL,0,0,'Some notes 2',NOW(),NULL,1),
    (3,24,NULL,21,NULL,NULL,'Case','cat_oc','cdt_con',NULL,NULL,0,0,'Some notes 3',NOW(),NULL,1),
    (4,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_und',NULL,NULL,0,1,'Some notes 4',NOW(),NULL,1),
    (5,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_und',NULL,NULL,0,1,'Some notes 5',NOW(),NULL,1),
    (6,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_con',NULL,NULL,0,1,'Some notes 6',NOW(),NULL,1),
    (7,24,NULL,48,NULL,NULL,'Case','cat_oc','cdt_con',NULL,NULL,0,0,'Some notes 7',NOW(),NULL,1),
    (8,24,NULL,37,NULL,NULL,'Case','cat_oc','cdt_und',NULL,NULL,0,1,'Some notes 8',NOW(),NULL,1),
    (9,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_con',NULL,NULL,0,0,'Some notes 9',NOW(),NULL,1),
    (10,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_con',NULL,NULL,0,0,'Some notes 10',NOW(),NULL,1),
    (11,24,7,NULL,NULL,NULL,'Case','cat_lic','cdt_con',NULL,NULL,0,0,'Some notes 11',NOW(),NULL,1);

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
    `offence_date`, `conviction_date`, `court`, `penalty`, `costs`, `msi`, `operator_name`,
    `defendant_type`, `notes`, `taken_into_consideration`, `person_id`, `created_on`, `last_modified_on`, `version`,
    `conviction_category_id`) VALUES
    (25,24,3,4,NULL,NULL,'2012-03-10','2012-06-15','FPN','3 points on licence','60',0,'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1,397),
    (26,24,0,4,NULL,NULL,'2012-04-10','2012-05-15','Leeds Magistrate court','3 points on licence','60',0,'','def_t_owner',NULL,NULL,4,NOW(),NOW(),1,399),
    (27,24,1,3,NULL,NULL,'2012-12-17','2013-03-02','FPN','3 points on licence','60',0,'','def_t_owner',NULL,NULL,4,NOW(),NOW(),1,399),
    (29,24,3,3,NULL,NULL,'2012-03-10','2012-06-15','Leeds Magistrate court','6 monthly investigation','2000',1,'John Smith Haulage Ltd.','def_t_op',NULL,NULL,4,NOW(),NOW(),1,399);

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
    (110,75,1,4,4,'lcat_psv','PD2737280','lsts_new',0,'ltyp_r','2010-01-12','2010-01-12','2010-01-12','',0,4,NULL,NULL,
    NULL,NULL,NULL,NOW(),NOW(),1),
    (114,104,1,NULL,NULL,'lcat_psv','OB1534567','lsts_new',1,'ltyp_sn',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,'2014-04-30 12:07:14','2014-04-30 12:07:17',1),
    (115,105,1,NULL,NULL,'lcat_psv','TS1234568','lsts_new',0,'ltyp_sr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
    NULL,NULL,NULL,NOW(),NULL,1);

INSERT INTO `licence_vehicle` (`id`, `licence_id`, `vehicle_id`, `created_by`, `last_modified_by`,
    `removal`, `removal_reason`, `specified_date`, `created_on`, `last_modified_on`,
    `version`) VALUES
    (1,7,1,NULL,4,1,'removal reason 1','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (2,7,2,NULL,4,1,'removal reason 2','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (3,7,3,NULL,4,1,'removal reason 3','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1),
    (4,7,4,NULL,4,1,'removal reason 4','2014-02-20 00:00:00','2010-01-12 00:00:00',
    '2014-02-20 00:00:00',1);

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
    (10,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,10),
    (13,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,13),
    (15,NULL,NULL,1,'6 months','TBC',NOW(),NULL,1,15),
    (32,NULL,NULL,1,'2 months','TBC',NOW(),NULL,1,32),
    (36,NULL,NULL,1,'6 months','TBC',NOW(),NULL,1,15);

INSERT INTO `pi` (
    `id`, `pi_status`, `created_by`, `last_modified_by`, `case_id`, `witnesses`, `is_cancelled`, `is_adjourned`, `licence_revoked_at_pi`,
    `agreed_date`, `decision_date`,`created_on`, `last_modified_on`,`version`, `deleted_date`) VALUES
    (1,'pi_s_schedule',1,1,24,20,0,0,0,NOW(),NOW(), NULL,NULL,1,NULL);

INSERT INTO `pi_hearing` (`id`, `pi_id`, `created_by`, `last_modified_by`, `presiding_tc_id`, `presided_by_role`,
    `hearing_date`, `venue`, `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,NULL,NULL,1,'tc_r_dtc', NOW(),'Some Venue',NULL,NULL,1),
    (2,1,NULL,NULL,2,'tc_r_htru', NOW(),'Some Alt. Venue',NULL,NULL,1);

INSERT INTO `pi_reason` (`pi_id`, `reason_id`)
VALUES
    (1,2),
    (1,6);

INSERT INTO `pi_decision` (`pi_id`, `decision_id`)
VALUES
    (1,2);

INSERT INTO `pi_venue` (`id`, `traffic_area_id`, `created_by`, `last_modified_by`, `created_on`, `last_modified_on`,
    `version`, `name`, `address_id`) VALUES
    (1,'B',NULL,NULL,NULL,NULL,1,'venue_1',21),
    (2,'B',NULL,NULL,NULL,NULL,1,'venue_2',22),
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

INSERT INTO `cases` (`id`, `licence_id`, `created_by`, `last_modified_by`, `description`, `ecms_no`, `open_date`,
    `case_type`, `close_date`, `annual_test_history`, `created_on`, `last_modified_on`, `version`, `is_impounding`) VALUES
    (24,7,NULL,NULL,'Case for convictions against company directors','E123456','2012-03-21 00:00:00','Compliance',NULL,NULL,'2013-11-12 12:27:33',   NULL,1,0),
    (28,7,NULL,NULL,'Convictions against operator','E123444','2012-06-13 00:00:00','Compliance',NULL,NULL,'2014-01-01 11:11:11',NULL,1,0),
    (29,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (30,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (31,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (32,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (33,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence','2014-03-29 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (34,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (35,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence','2014-04-15 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (36,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (37,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence','2014-04-23 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (38,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (39,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence','2014-05-25 12:27:33',NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (40,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2,0),
    (41,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (42,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-06-01 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (43,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-06-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14,0),
    (44,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (45,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (46,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (47,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (48,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (49,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (50,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (51,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (52,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (53,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (54,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (55,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2,0),
    (56,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (57,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-11-01 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (58,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-11-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14,0),
    (59,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (60,7,NULL,NULL,'werwrew','','2014-02-11 12:27:47','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (61,7,NULL,NULL,'345345345','','2014-02-11 12:28:07','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (62,7,NULL,NULL,'weewrerwerw','','2014-02-11 12:28:25','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (63,7,NULL,NULL,'345345345','','2014-02-11 12:28:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (64,7,NULL,NULL,'7656567567','','2014-02-11 12:29:01','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (65,7,NULL,NULL,'45645645645','','2014-02-11 12:29:17','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (66,7,NULL,NULL,'56756757','','2014-02-11 12:29:40','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (67,7,NULL,NULL,'3453g345','','2014-02-11 12:29:59','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (68,7,NULL,NULL,'MWC test case 1','2345678','2014-02-13 23:43:58','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (69,7,NULL,NULL,'new test case 2','coops12345','2014-02-14 02:37:39','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (70,7,NULL,NULL,'MWC test case 3','coops4321','2014-02-14 02:39:38','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',2,0),
    (71,7,NULL,NULL,'MWC test case 4','E647654','2014-02-14 16:29:03','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (72,7,NULL,NULL,'Case for convictions against company directors','E123456','2013-11-02 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (73,7,NULL,NULL,'Convictions against operator Fred','E123444','2013-11-03 00:00:00','Compliance',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',14,0),
    (74,7,NULL,NULL,'1213213','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0),
    (75,110,NULL,NULL,'PSV licence case','','2014-02-11 12:27:33','licence',NULL,NULL,'2014-01-11 11:11:11','2014-02-22 12:22:22',1,0);

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

/* Document dummy data */
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (1,7,'Test document not digital','testdocument1.doc',0,1,1,'doc_doc','2014-08-23 18:00:05');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (2,7,'Test document digital','testdocument2.doc',1,1,1,'doc_doc','2014-08-25 12:04:35');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (3,7,'Test document 3','testdocument3.doc',0,1,2,'doc_doc','2014-08-22 11:01:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (4,7,'Test document 4','testdocument4.doc',0,2,3,'doc_doc','2014-08-24 16:23:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (5,7,'Test document 5','testdocument5.xls',0,2,3,'doc_xls','2014-07-01 15:01:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (6,7,'Test document 6','testdocument6.docx',0,2,3,'doc_docx','2014-07-05 09:00:05');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (7,7,'Test document 7','testdocument7.xls',0,2,4,'doc_xls','2014-07-05 10:23:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (8,7,'Test document 8','testdocument8.doc',1,2,4,'doc_doc','2014-07-05 10:45:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (9,7,'Test document 9','testdocument9.ppt',1,2,4,'doc_ppt','2014-08-05 08:59:40');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (10,7,'Test document 10','testdocument10.jpg',0,1,2,'doc_jpg','2014-08-08 12:47:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (11,7,'Test document 11','testdocument11.txt',0,1,1,'doc_txt','2014-08-14 14:00:00');
INSERT INTO document(id,licence_id,description,filename,is_digital,category_id,document_sub_category_id,file_extension,issued_date) VALUES
    (12,7,'Test document 12','testdocument12.xls',1,1,2,'doc_xls','2014-08-28 14:03:00');

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

SET foreign_key_checks = 1;
