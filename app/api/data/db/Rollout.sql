SET foreign_key_checks = 0;

TRUNCATE TABLE category;
TRUNCATE TABLE submission_section;
TRUNCATE TABLE task_sub_category;

SET foreign_key_checks = 1;

INSERT INTO `submission_section` (`id`, `description`, `group`) VALUES
    (1, 'Offences (inc. driver hours)', 'Compliance'),
    (2, 'Prohibitions', 'Compliance'),
    (3, 'Convictions', 'Compliance'),
    (4, 'Penalties', 'Compliance'),
    (5, 'ERRU MSI', 'Compliance'),
    (6, 'Bus compliance', 'Compliance'),
    (7, 'Section 9', 'Compliance'),
    (8, 'Section 43', 'Compliance'),
    (9, 'Impounding', 'Compliance'),
    (10, 'Duplicate TM', 'TM'),
    (11, 'Repute / professional competence of TM', 'TM'),
    (12, 'TM Hours', 'TM'),
    (13, 'Interim with / without submission', 'Licensing application'),
    (14, 'Representation', 'Licensing application'),
    (15, 'Objection', 'Licensing application'),
    (16, 'Non-chargeable variation', 'Licensing application'),
    (17, 'Regulation 31', 'Licensing application'),
    (18, 'Schedule 4', 'Licensing application'),
    (19, 'Chargeable variation', 'Licensing application'),
    (20, 'New application', 'Licensing application'),
    (21, 'Surrender', 'Licence referral'),
    (22, 'Non application related maintenance issue', 'Licence referral'),
    (23, 'Review complaint', 'Licence referral'),
    (24, 'Late fee', 'Licence referral'),
    (25, 'Financial standing issue (continuation)', 'Licence referral'),
    (26, 'Repute fitness of director', 'Licence referral'),
    (27, 'Period of grace', 'Licence referral'),
    (28, 'Proposal to revoke', 'Licence referral');

INSERT INTO `category` (`id`,`description`,`is_doc_category`,`is_task_category`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'Licensing',1,1,NULL,NULL,NULL,NULL,1),
    (2,'Compliance',1,1,NULL,NULL,NULL,NULL,1),
    (3,'Bus Registration',1,1,NULL,NULL,NULL,NULL,1),
    (4,'Permits',1,1,NULL,NULL,NULL,NULL,1),
    (5,'Transport Manager',1,1,NULL,NULL,NULL,NULL,1),
    (7,'Environmental',1,1,NULL,NULL,NULL,NULL,1),
    (8,'IRFO',1,1,NULL,NULL,NULL,NULL,1),
    (9,'Application',0,1,NULL,NULL,NULL,NULL,1),
    (10,'Submission',0,1,NULL,NULL,NULL,NULL,1);

INSERT INTO task_sub_category(id,description,name,category_id,is_freetext_description) VALUES
    (1, 'Address Change ', 'Address Change Assisted Digital', 9, 0),
    (2, 'Address Change ', 'Address Change Digital', 9, 0),
    (3, 'Bank Statement', 'Bank Statement Assisted Digital', 9, 0),
    (4, 'Bank Statement', 'Bank Statement Digital', 9, 0),
    (5, 'Change of Partner(s)', 'Change of Partner Assisted Digital', 9, 0),
    (6, 'Change of Partner(s)', 'Change of Partner Digital', 9, 0),
    (7, 'Change of Director(s)', 'Director Assisted Digital App', 9, 0),
    (8, 'Change of Director(s)', 'Director Digital App', 9, 0),
    (9, 'Application Fee Due', 'Fee Due', 9, 0),
    (10, 'Grant Fee Due', 'Fee Due', 9, 0),
    (11, 'Interim Fee Due', 'Fee Due', 9, 0),
    (12, 'Application Fee Due', 'Fee Due', 9, 0),
    (13, null, 'Financial Document Assisted Digital', 9, 1),
    (14, null, 'Financial Document Digital', 9, 1),
    (15, 'GV79 Application', 'GV79 Assisted Digital', 9, 0),
    (16, 'GV79 Application', 'GV79 Digital', 9, 0),
    (17, 'GV80A', 'GV80A Assisted Digital', 9, 0),
    (18, 'GV80A', 'GV80A Digital', 9, 0),
    (19, 'GV81 Assisted Digital', 'GV81 Assisted Digital', 9, 0),
    (20, 'GV81 Digital', 'GV81 Digital', 9, 0),
    (21, 'Interim licence expiring', 'Interim', 9, 0),
    (22, 'Interim Request', 'Interim App Assisted Digital', 9, 0),
    (23, 'Interim Request', 'Interim App Digital', 9, 0),
    (24, 'Maintenance Contract', 'Maint Contract Assisted Digital', 9, 0),
    (25, 'Maintenance Contract', 'Maint Contract Digital', 9, 0),
    (26, 'PSV421 Application', 'PSV421 Assisted Digital', 9, 0),
    (27, 'PSV421 Application', 'PSV421 Digital', 9, 0),
    (28, 'PSV431 Application', 'PSV431 Assisted Digital', 9, 0),
    (29, 'PSV431 Application', 'PSV431 Digital', 9, 0),
    (30, null, 'Response to 1st Request ', 9, 1),
    (31, null, 'Response to Final Request', 9, 1),
    (32, 'Subsidiary Company change', 'Subsidiary Assisted Digital App', 9, 0),
    (33, 'Subsidiary Company change', 'Subsidiary Digital App', 9, 0),
    (34, 'New application ', 'Time expired', 9, 0),
    (35, 'Variation Application', 'Time expired', 9, 0),
    (36, 'New Application: {bus_reg_no}', 'EBSR', 3, 0),
    (37, 'Data Refresh: {bus_reg_no}', 'EBSR', 3, 0),
    (38, 'Variation: {bus_reg_no}', 'EBSR', 3, 0),
    (39, 'Cancellation: {bus_reg_no}', 'EBSR', 3, 0),
    (40, 'Bus Registration Fee Due', 'Fee Due', 3, 0),
    (41, 'Bus Registration Fee Due', 'Fee Due', 3, 0),
    (42, null, 'General Task', 3, 1),
    (43, 'SFTP Request {user_name}', 'SFTP request', 3, 0),
    (44, 'Bank Statement', 'Bank Statement', 2, 0),
    (45, 'ERRU Case:{case_id}', 'ERRU Auto Case', 2, 0),
    (46, null, 'TM Compliance Doc', 2, 1),
    (47, null, 'General Task', 2, 1),
    (48, 'GV79E', 'GV79E', 7, 0),
    (49, 'Objection', 'Objection', 7, 0),
    (50, 'Plan of OC', 'Plan of OC', 7, 0),
    (51, 'Representation', 'Representation', 7, 0),
    (52, 'Review Complaint', 'Review Complaint', 7, 0),
    (53, 'Review period complaint recorded', 'Review Period ends in 2 weeks', 7, 0),
    (54, null, 'General Task', 7, 1),
    (55, 'Application', 'Application', 8, 0),
    (56, 'Fee Due', 'Fee Due', 8, 0),
    (57, null, 'General Task', 8, 1),
    (58, 'Bank Statement', 'Bank Statement', 1, 0),
    (59, 'Old Licence: {lic_no}', 'Change of entity Assisted Digital', 1, 0),
    (60, 'Old Licence: {lic_no}', 'Change of entity Digital', 1, 0),
    (61, 'Checklist Not Received', 'Checklist Not received', 1, 0),
    (62, null, 'General Task', 1, 1),
    (63, 'Response To Request {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (64, 'Satisfactory Result {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (65, 'Unsatisfactory Result {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (66, 'ID:{organisation_id}/ Merge Lic: {lic_no}', 'Request to merge licences', 1, 0),
    (67, 'Surrender Request', 'Sur 1 Assisted Digital', 1, 0),
    (68, 'Surrender Request', 'Sur 1 Digital', 1, 0),
    (69, 'TM Required', 'TM Period of Grace', 1, 0),
    (70, 'Application', 'Application', 4, 0),
    (71, 'Fee Due', 'Fee Due', 4, 0),
    (72, null, 'General Task', 4, 1),
    (73, 'Case Decision', 'Decision', 10, 0),
    (74, 'Case Recommendation', 'Recommendation', 10, 0),
    (75, null, 'General Task', 5, 1),
    (76, 'Certificate', 'Certificate', 5, 0),
    (77, 'Close TM Case', 'Close TM Case', 5, 0),
    (78, 'TM Removed from this licence', 'TM Declared Unfit', 5, 0),
    (79, 'TM1 Application', 'TM1 Assisted Digital', 5, 0),
    (80, 'TM1 Application', 'TM1 Digital', 5, 0);

DROP TABLE IF EXISTS task_search_view;

CREATE VIEW task_search_view AS
    SELECT t.id, t.assigned_to_team_id, t.assigned_to_user_id, t.action_date, t.urgent,
        t.is_closed, t.category_id, t.task_sub_category_id, t.description,
        cat.description category_name, tsc.description task_sub_category_name,
        coalesce(c.id, br.reg_no, l.lic_no, irfo.id, tm.id, 'Unlinked') id_col,
        coalesce(o.name, irfo.name, tmp.family_name, concat('Case ', c.id), 'Unlinked') name_col,
        l.lic_no, irfo.name irfo_op_name, o.name op_name, tmp.family_name, c.id case_id, br.id bus_reg_id,
        u.name user_name, COUNT(ll.id) licence_count
    FROM `task` t

    INNER JOIN (category cat, task_sub_category tsc) ON (cat.id = t.category_id AND tsc.id = t.task_sub_category_id)

    LEFT JOIN licence l ON t.licence_id = l.id

    LEFT JOIN organisation irfo ON t.irfo_organisation_id = irfo.id

    LEFT JOIN organisation o ON l.organisation_id = o.id

    LEFT JOIN licence ll ON (ll.organisation_id = o.id AND (ll.status = 'Valid'))

    LEFT JOIN (transport_manager tm, person tmp, contact_details tmcd)
        ON (t.transport_manager_id = tm.id AND tmp.id = tmcd.person_id AND tmcd.id = tm.contact_details_id)

    LEFT JOIN cases c ON t.case_id = c.id

    LEFT JOIN bus_reg br ON t.bus_reg_id = br.id

    LEFT JOIN user u ON t.assigned_to_user_id = u.id

    GROUP BY (t.id);