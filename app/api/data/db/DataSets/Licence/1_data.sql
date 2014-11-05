-- @NOTE This dataset is not complete
-- Organisations    1**
-- Licences         2**
-- OperatingCentres 3**
-- Address          4**

-- @NOTE This dataset relies on Minimal
SOURCE ./../db/DataSets/Minimal/1_data.sql;

SET foreign_key_checks = 0;

DELETE FROM `licence` WHERE id = 200;
DELETE FROM `address` WHERE id = 400;
DELETE FROM `operating_centre` WHERE id = 300;
DELETE FROM `licence_operating_centre` WHERE id = 200300;

INSERT INTO `licence` (
    `id`, `organisation_id`, `traffic_area_id`, `goods_or_psv`, `lic_no`, `status`, `ni_flag`, `licence_type`,
    `in_force_date`, `review_date`, `surrendered_date`, `fabs_reference`, `tot_auth_trailers`, `tot_auth_vehicles`,
    `tot_auth_small_vehicles`, `tot_auth_medium_vehicles`, `safety_ins_vehicles`, `safety_ins_trailers`,
    `safety_ins_varies`, `tachograph_ins`, `tachograph_ins_name`
) VALUES (
    200,100,'B','lcat_gv','OB101','lsts_valid',0,'ltyp_sn','2010-01-12','2010-01-12','2010-01-12','',10,10,NULL,NULL,NULL,
    NULL,NULL,NULL,NULL
);

INSERT INTO `address` (
    `id`, `saon_desc`, `paon_desc`, `street`, `locality`,
    `postcode`, `town`, `country_code`
) VALUES (
    400,'Unit 5','12 Albert Street','Westpoint','','LS9 6NA','Leeds','GB'
);

INSERT INTO `operating_centre` (`id`, `address_id`) VALUES (300, 400);

INSERT INTO `licence_operating_centre` (
    `id`,`licence_id`, `no_of_vehicles_required`, `no_of_trailers_required`, `sufficient_parking`, `ad_placed`,
    `permission`, `operating_centre_id`
) VALUES (
    200300,200,10,10,1,0,1,300
);

SET foreign_key_checks = 1;