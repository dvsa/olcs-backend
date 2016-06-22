#!/usr/bin/env bash

# This is scripts tests that the data validators are working correctly on the backend
# It does this by setting up the data (mysql), calling a backend URL (curl) and asserting the response for a 200 or 400
# For issues see mat.evans@valtech.co.uk
#
# NB Currently run this against the dev test database (run inn Vagrant), It will need ID's changing if running on
# another database

curlOptions='-s --head --header "X-Pid: f1e9435a0975c1007d77bf466ac7507e66cc599ae3cafa3b2b0909cac2cc9da2"'
backendUrl="http://olcs-backend/api"
mysqlOptions="olcs_be"
userId=611
userOrganisationId=1
userLicenceId=7
userApplicationId=7
notUserOrganisationId=30


function assertValid {

    result=$(eval curl $curlOptions -XGET $backendUrl/$1)
    match=$(grep -o "HTTP/1.1 200 OK" <<< $result)

    if [ -z "$match" ]; then
        echo FAILED assertValid $backendUrl/$1
        exit;
    else
        echo PASSED assertValid $backendUrl/$1
    fi
}

function assertNotValid {

    result=$(eval curl $curlOptions -XGET $backendUrl/$1)
    match=$(grep -o "HTTP/1.1 400 Bad Request" <<< $result)

    if [ -z "$match" ]; then
        echo FAILED assertNotValid $backendUrl/$1
        exit;
    else
        echo PASSED assertNotValid $backendUrl/$1
    fi
}

function executeSql {
    mysql $mysqlOptions -e "$1"
}

echo
echo "=Testing document download="
documentId=10009

echo "==Licence=="
executeSql "UPDATE document SET \
    licence_id = $userLicenceId, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Application=="
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = $userApplicationId, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Case=="
caseId=24
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = $caseId, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

echo "===Application==="
executeSql "UPDATE cases SET \
    application_id = $userApplicationId, \
    transport_manager_id = NULL, \
    licence_id = NULL \
    WHERE id = $caseId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "===Transport Manager==="
transportManagerId=1
executeSql "UPDATE cases SET \
    application_id = NULL, \
    transport_manager_id = $transportManagerId, \
    licence_id = NULL \
    WHERE id = $caseId"

echo "====Transport Manager Application===="
executeSql "UPDATE transport_manager_application SET application_id = $userApplicationId"
executeSql "UPDATE transport_manager_licence SET licence_id = 114"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "====Transport Manager Licence===="
executeSql "UPDATE transport_manager_application SET application_id = 6"
executeSql "UPDATE transport_manager_licence SET licence_id = $userLicenceId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "===Licence==="
executeSql "UPDATE cases SET \
    application_id = NULL, \
    transport_manager_id = NULL, \
    licence_id = $userLicenceId \
    WHERE id = $caseId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Transport Manager=="
transportManagerId=1
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = $transportManagerId, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE transport_manager_application SET application_id = $userApplicationId"
executeSql "UPDATE transport_manager_licence SET licence_id = 114"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

executeSql "UPDATE transport_manager_application SET application_id = 6"
executeSql "UPDATE transport_manager_licence SET licence_id = $userLicenceId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Operating Centre=="
operatingCentreId=16
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = $operatingCentreId, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE application_operating_centre SET application_id = $userApplicationId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Bus Reg=="
busRegId=1
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = $busRegId, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE bus_reg SET licence_id = $userLicenceId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==IRFO Organisation=="
executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = $userOrganisationId, \
    submission_id = NULL, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Submission=="
submissionId=1
# Use caseId from previos test

executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = $submissionId, \
    statement_id = NULL \
    WHERE id = $documentId"

executeSql "UPDATE submission SET case_id = $caseId WHERE id = $submissionId"

executeSql "UPDATE cases SET \
    application_id = $userApplicationId, \
    transport_manager_id = NULL, \
    licence_id = NULL \
    WHERE id = $caseId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"

echo "==Statement=="
statementId=1
# Use caseId from previous test

executeSql "UPDATE document SET \
    licence_id = NULL, \
    application_id = NULL, \
    case_id = NULL, \
    transport_manager_id = NULL, \
    operating_centre_id = NULL, \
    bus_reg_id = NULL, \
    irfo_organisation_id = NULL, \
    submission_id = NULL, \
    statement_id = $statementId \
    WHERE id = $documentId"

executeSql "UPDATE statement SET case_id = $caseId WHERE id = $statementId"

executeSql "UPDATE cases SET \
    application_id = $userApplicationId, \
    transport_manager_id = NULL, \
    licence_id = NULL \
    WHERE id = $caseId"

executeSql "UPDATE organisation_user SET organisation_id = $userOrganisationId WHERE user_id = $userId"
assertValid "document/download?identifier=$documentId"

executeSql  "UPDATE organisation_user SET organisation_id = $notUserOrganisationId WHERE user_id = $userId"
assertNotValid "document/download?identifier=$documentId"






echo
echo Woohoo ALL PASSED !!!
echo