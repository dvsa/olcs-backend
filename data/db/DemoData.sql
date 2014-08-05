
-- Truncate the table

SET foreign_key_checks = 0;

TRUNCATE TABLE user;
TRUNCATE TABLE organisation;
TRUNCATE TABLE licence;
TRUNCATE TABLE vosa_case;
TRUNCATE TABLE statement;

SET foreign_key_checks = 1;

-- Create the users

INSERT INTO user (
    version, username, created_on, last_updated_on, display_name, is_deleted
) VALUES (
    1, 'AmyWrigg', NOW(), NOW(), 'Amy Wrigg', 0
);

-- Create the organisations

INSERT INTO organisation (
    version, created_by, last_updated_by, registered_company_number, name,
    organisation_type, created_on, last_updated_on
) VALUES (
    1, 1, 1, '1234567', 'John Smith Haulage Ltd', 'Registered company', NOW(), NOW()
);

-- Create Traffic area

INSERT INTO traffic_area (
    version, created_by, created_on, last_updated_on, last_updated_by, areaname
) VALUES (
    1, NULL, NULL, NULL, NULL, 'North East of England'
);

-- Create a licence

INSERT INTO licence (
    traffic_area_uid, version, created_by, created_on, last_updated_on, last_updated_by,
    f_user_uid_status_changed_by, goods_or_psv, status_changed_on,
    licenceNumber, status, licenceType, startDate, reviewDate, endDate,
    fabsReference, operatorId, has_cases, tradeType
) VALUES (
    1, 1, 1, NOW(), NOW(), 1, 0, 'Goods', NOW(), 'OB1234567', 'Valid',
    'Standard National', '2010-01-12', '2010-01-12', '2010-01-12', '', 1, 0,
    'Utilities'
);


-- Create cases

INSERT INTO vosa_case (
    licence, caseNumber, status, description, ecms, openTime, owner, caseType,
    version
) VALUES (
    1, 12345678, 'Open', 'Case for convictions against company directors',
    'E123456', '2012-03-21', 'TBC', 'Compliance', 1
);

-- Create vosa case category
INSERT INTO vosa_case_case_category (
    vosacase_id,
    casecategory_id
) VALUES (1, 1), (1, 10), (1, 13), (1, 21);

-- Create Statements

INSERT INTO statement (
    created_by, last_updated_by, vosa_case, created_on, last_updated_on,
    version, statement_type, vrm, date_stopped, date_requested,
    authorisers_decision, contact_type, issued_date,
    requestors_address_id, requestors_family_name,
    requestors_forename, requestors_body
) VALUES (
    1, 1, 1, NOW(), NOW(), 1, 1, 'AB10 ABC', '2013-01-01', '2014-01-01', 
    'Some Decisions', 1, '2012-01-01', 1, 'Smith', 'John',
    'Test Body'
);

-- INSERT INTO `application` (`id`,`traffic_area_uid`,`licence_uid`,`application_number`, `version`) VALUES (1,1,7,'100001', 1);
-- INSERT INTO `application_operating_centre` (`id`, `no_of_vehicles_required`, `no_of_trailers_required`, `sufficient_parking`, `permission`, `applicationId`) VALUES ('1', '34', '23', '1', '1', '1');

START TRANSACTION;

-- action status types

INSERT INTO `submission_action_status_type` SET `name` = 'Recommendation';
SET @recommendation_submission_action_status_type_id = LAST_INSERT_ID();

INSERT INTO `submission_action_status_type` SET `name` = 'Decision';
SET @decision_submission_action_status_type_id = LAST_INSERT_ID();

-- submission action status

INSERT INTO `submission_action_status` SET `name` = 'Recommendation Status 1', `submission_action_status_type_id` = @recommendation_submission_action_status_type_id;
SET @recommentation_submission_action_status_id = LAST_INSERT_ID();
INSERT INTO `submission_action_status` SET `name` = 'Decision Status 1', `submission_action_status_type_id` = @decision_submission_action_status_type_id;
SET @decision_submission_action_status_id = LAST_INSERT_ID();


-- submissions

INSERT INTO submission SET text = 'Submission (recom)', vosa_case_id = 1, created_on = NOW(), created_by = 1, last_updated_on = NOW(), last_updated_by = 1;
SET @recommendation_submission_id = LAST_INSERT_ID();
INSERT INTO submission SET text = 'Submission (descision)', vosa_case_id = 1, created_on = NOW(), created_by = 1, last_updated_on = NOW(), last_updated_by = 1;
SET @decision_submission_id = LAST_INSERT_ID();

-- submission actions

INSERT INTO submission_action SET comment = 'Submission ation for recommendation', urgent = 1, submission_id = @recommendation_submission_id, created_on = NOW(), last_updated_on = NOW(), sender_user_id = 1, recipient_user_id = 1, submission_action_status_id = @recommentation_submission_action_status_id;

INSERT INTO submission_action SET comment = 'Submission ation for decision', urgent = 1, submission_id = @decision_submission_id, created_on = NOW(), last_updated_on = NOW(), sender_user_id = 1, recipient_user_id = 1, submission_action_status_id = @decision_submission_action_status_id;

COMMIT;


