-- MySQL dump 10.13  Distrib 5.6.22-72.0, for Linux (x86_64)
--
-- Host: localhost    Database: etl_schema
-- ------------------------------------------------------
-- Server version	5.6.22-72.0-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `uprn` bigint(20) DEFAULT NULL,
  `paon_start` varchar(5) DEFAULT NULL,
  `paon_end` varchar(5) DEFAULT NULL,
  `paon_desc` varchar(90) DEFAULT NULL COMMENT 'Primary adressable object. Second line of address',
  `saon_start` varchar(5) DEFAULT NULL,
  `saon_end` varchar(5) DEFAULT NULL,
  `saon_desc` varchar(90) DEFAULT NULL COMMENT 'Secondary addressable object. First line od address',
  `street` varchar(100) DEFAULT NULL,
  `locality` varchar(35) DEFAULT NULL,
  `town` varchar(30) DEFAULT NULL,
  `postcode` varchar(8) DEFAULT NULL,
  `admin_area` char(40) DEFAULT NULL COMMENT 'Local council name.Defines traffic area',
  `country_code` varchar(8) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_address_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_address_country_code` (`country_code`),
  KEY `ix_address_created_by` (`created_by`),
  KEY `ix_address_last_modified_by` (`last_modified_by`),
  KEY `ix_address_admin_area` (`admin_area`),
  CONSTRAINT `fk_address_admin_area_admin_area_traffic_area_id` FOREIGN KEY (`admin_area`) REFERENCES `admin_area_traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_country_code_country_id` FOREIGN KEY (`country_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds addreses. Accessed via contact_details for context of address type, e.g. Registered Office address, Transport Consultant etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_area_traffic_area`
--

DROP TABLE IF EXISTS `admin_area_traffic_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_area_traffic_area` (
  `id` char(40) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `traffic_area_id` varchar(1) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_admin_area_traffic_area_traffic_area_id` (`traffic_area_id`),
  KEY `ix_admin_area_traffic_area_created_by` (`created_by`),
  KEY `ix_admin_area_traffic_area_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_admin_area_traffic_area_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_admin_area_traffic_area_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_admin_area_traffic_area_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A traffic area contains several admin areas. The collection of admin areas will be the councils inside a county boundary. Traffic areas split on groups of county boundaries.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_area_traffic_area`
--

LOCK TABLES `admin_area_traffic_area` WRITE;
/*!40000 ALTER TABLE `admin_area_traffic_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_area_traffic_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appeal`
--

DROP TABLE IF EXISTS `appeal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appeal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `appeal_no` varchar(20) NOT NULL COMMENT 'Non system generated number entered by user.',
  `case_id` int(10) unsigned DEFAULT NULL,
  `deadline_date` datetime DEFAULT NULL,
  `appeal_date` datetime DEFAULT NULL,
  `outline_ground` varchar(1024) DEFAULT NULL COMMENT 'Grounds for the appeal.',
  `hearing_date` datetime DEFAULT NULL,
  `papers_due_date` datetime DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `papers_sent_date` date DEFAULT NULL,
  `decision_date` date DEFAULT NULL,
  `reason` varchar(32) DEFAULT NULL,
  `outcome` varchar(32) DEFAULT NULL,
  `withdrawn_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_appeal_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_appeal_case_id` (`case_id`),
  KEY `ix_appeal_created_by` (`created_by`),
  KEY `ix_appeal_last_modified_by` (`last_modified_by`),
  KEY `ix_appeal_reason` (`reason`),
  KEY `ix_appeal_outcome` (`outcome`),
  CONSTRAINT `fk_appeal_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_outcome_ref_data_id` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_reason_ref_data_id` FOREIGN KEY (`reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='After a case has a decision there can be One appeal made against the decision.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appeal`
--

LOCK TABLES `appeal` WRITE;
/*!40000 ALTER TABLE `appeal` DISABLE KEYS */;
/*!40000 ALTER TABLE `appeal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL COMMENT 'Licence can have many applications, even several being processed at the same time',
  `status` varchar(32) NOT NULL COMMENT 'Applications, once submitted, are new. Can then be granted or not. Normally become valid.',
  `is_variation` tinyint(1) NOT NULL COMMENT 'New or variation application. 0 for new, 1 for variation',
  `has_entered_reg` tinyint(1) DEFAULT NULL COMMENT 'Stores user has elected to enter vehicles. Affects application screenflow. Show screen to enter vehicles or not.',
  `tot_auth_trailers` smallint(5) unsigned DEFAULT NULL COMMENT 'Applicant wants to be authorised for this number of trailers.',
  `tot_auth_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'Applicant wants to be authorised for this number of vehicles for goods. Will be sum of the psv columns for psv.',
  `tot_auth_small_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'psv small vehicles',
  `tot_auth_medium_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'psv medium vehicles',
  `tot_auth_large_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'psv large vehicles',
  `tot_community_licences` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of EU community licences required',
  `licence_type` varchar(32) DEFAULT NULL COMMENT 'Restricted, Standard International etc.',
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `bankrupt` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been declared bankrupt',
  `administration` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been involved in a company that went into administration',
  `disqualified` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been disqualified as a director or manager of a company',
  `liquidation` tinyint(1) DEFAULT NULL COMMENT 'Operator has been liquidated',
  `receivership` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been involved in a company that went into receivership',
  `insolvency_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User has confirmed that any futire insolvency will be communicated to the TC',
  `insolvency_details` varchar(4000) DEFAULT NULL COMMENT 'Details of previous bankrupcy, insolvency, administration, receivership of people linked to application',
  `safety_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User confirms they have read safety information in application and will comply',
  `declaration_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User confirms they have read undertakings and declarations and will comply',
  `financial_evidence_uploaded` tinyint(1) DEFAULT NULL COMMENT 'User specifies whether they have uploaded financial evidence or will send by post',
  `received_date` datetime DEFAULT NULL COMMENT 'Submitted date.  Was date_entered in OLBS',
  `target_completion_date` datetime DEFAULT NULL COMMENT 'SLA for application to be processed.',
  `granted_date` datetime DEFAULT NULL COMMENT 'Date application granted.',
  `refused_date` datetime DEFAULT NULL COMMENT 'Date application refused.',
  `withdrawn_date` datetime DEFAULT NULL COMMENT 'Date application withdrawn.',
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `prev_has_licence` tinyint(1) DEFAULT NULL COMMENT 'History section. Ay person linked to application currently holds a licence',
  `prev_had_licence` tinyint(1) DEFAULT NULL COMMENT 'History section. Any person linked to application has previously held a licence',
  `prev_been_refused` tinyint(1) DEFAULT NULL COMMENT 'History section. Any person linked to application has previously been refused a licence in the EU',
  `prev_been_revoked` tinyint(1) DEFAULT NULL COMMENT 'History section. Any person linked to application has previously been on a licence revoked in the EU',
  `prev_been_at_pi` tinyint(1) DEFAULT NULL COMMENT 'History section. Any person linked to application has previously been to a public enquiry before a TC',
  `prev_been_disqualified_tc` tinyint(1) DEFAULT NULL COMMENT 'History section. Any person linked to application has previously been disqualified by any TC',
  `prev_purchased_assets` tinyint(1) DEFAULT NULL COMMENT 'History section. In last 12 months any person or operator linked to the application has purchased a company or shares of  that has or had a licence',
  `override_ooo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Override out of objection date',
  `prev_conviction` tinyint(1) DEFAULT NULL COMMENT 'Anyone linked to app has been convicted or linked to a company that was convicted',
  `convictions_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User confirmation that any convictions that occur during application process will be communicated to TC.',
  `psv_operate_small_vhl` tinyint(1) DEFAULT NULL COMMENT 'The psv operator intends to operate small vehicles English and Welsh operators only.Section 15B PSV421',
  `psv_small_vhl_notes` varchar(4000) DEFAULT NULL COMMENT 'Small vehicle notes. Section 15B PSV421',
  `psv_small_vhl_confirmation` tinyint(1) DEFAULT NULL COMMENT 'User confirmation That if they operate small vehicles they agree to the conditions in the application ui form. Section 15D PSV421',
  `psv_no_small_vhl_confirmation` tinyint(1) DEFAULT NULL COMMENT 'Confirm vehicles with 8 passenger seats or less will not be operated on the licence. Section 15E PSV421',
  `psv_medium_vhl_confirmation` tinyint(1) DEFAULT NULL COMMENT 'User confirms compliance for restricted psv medium vehicle legislation. Section 8 of PSV 421 form',
  `psv_medium_vhl_notes` varchar(1000) DEFAULT NULL COMMENT 'Notes by applicant on psv restriced medium vehicles.',  
  `psv_limousines` tinyint(1) DEFAULT NULL COMMENT 'Are any vehicles on licence limos or novelty.Section 15F PSV421',
  `psv_no_limousine_confirmation` tinyint(1) DEFAULT NULL COMMENT 'If no limos on licence user confirms they will not put any on licence. Section 15F PSV421',
  `psv_only_limousines_confirmation` tinyint(1) DEFAULT NULL COMMENT 'Licence is only for limos and no other vehicle types. Section 15G PSV 421',
  `interim_start` date DEFAULT NULL COMMENT 'Date interim licence is to start.',
  `interim_end` date DEFAULT NULL COMMENT 'Date interim licence is to end.',
  `interim_auth_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of vehicles authorised on interim licence.',
  `interim_auth_trailers` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of trailers authorised on interim licence.',
  `interim_status` varchar(32) DEFAULT NULL COMMENT 'Interim licence status.',
  `interim_reason` varchar(1000) DEFAULT NULL,
  `is_maintenance_suitable` tinyint(1) DEFAULT NULL COMMENT 'User confirmation that maintenance agreements are suitable and guidence notes read.',
  `ni_flag` tinyint(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_application_licence_id` (`licence_id`),
  KEY `ix_application_created_by` (`created_by`),
  KEY `ix_application_last_modified_by` (`last_modified_by`),
  KEY `ix_application_licence_type` (`licence_type`),
  KEY `ix_application_status` (`status`),
  KEY `ix_application_interim_status` (`interim_status`),
  KEY `ix_application_withdrawn_reason` (`withdrawn_reason`),
  KEY `ix_application_goods_or_psv` (`goods_or_psv`),
  CONSTRAINT `fk_application_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_interim_status_ref_data_id` FOREIGN KEY (`interim_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_licence_type_ref_data_id` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_withdrawn_reason_ref_data_id` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COMMENT='Application to vary a licence or to apply for a new licence. If successful values from app will be copied into licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application`
--

LOCK TABLES `application` WRITE;
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
/*!40000 ALTER TABLE `application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_completion`
--

DROP TABLE IF EXISTS `application_completion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_completion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned NOT NULL,
  `type_of_licence_status` smallint(5) unsigned DEFAULT NULL,
  `business_type_status` smallint(5) unsigned DEFAULT NULL,
  `business_details_status` smallint(5) unsigned DEFAULT NULL,
  `addresses_status` smallint(5) unsigned DEFAULT NULL,
  `people_status` smallint(5) unsigned DEFAULT NULL,
  `taxi_phv_status` smallint(5) unsigned DEFAULT NULL,
  `operating_centres_status` smallint(5) unsigned DEFAULT NULL,
  `financial_evidence_status` smallint(5) unsigned DEFAULT NULL,
  `transport_managers_status` smallint(5) unsigned DEFAULT NULL,
  `vehicles_status` smallint(5) unsigned DEFAULT NULL,
  `vehicles_psv_status` smallint(5) unsigned DEFAULT NULL,
  `vehicles_declarations_status` smallint(5) unsigned DEFAULT NULL,
  `discs_status` smallint(5) unsigned DEFAULT NULL,
  `community_licences_status` smallint(5) unsigned DEFAULT NULL,
  `safety_status` smallint(5) unsigned DEFAULT NULL,
  `conditions_undertakings_status` smallint(5) unsigned DEFAULT NULL,
  `financial_history_status` smallint(5) unsigned DEFAULT NULL,
  `licence_history_status` smallint(5) unsigned DEFAULT NULL,
  `convictions_penalties_status` smallint(5) unsigned DEFAULT NULL,
  `undertakings_status` smallint(5) unsigned DEFAULT NULL,
  `last_section` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_application_completion_application_id` (`application_id`),
  KEY `ix_application_completion_created_by` (`created_by`),
  KEY `ix_application_completion_last_modified_by` (`last_modified_by`),
  KEY `ix_application_completion_application_id` (`application_id`),
  CONSTRAINT `fk_application_completion_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_completion_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_completion_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores progress of an online (self service) application. Used to decide if app has enough info to be submitted and to display feedback to user of completion status of app sections.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_completion`
--

LOCK TABLES `application_completion` WRITE;
/*!40000 ALTER TABLE `application_completion` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_completion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_operating_centre`
--

DROP TABLE IF EXISTS `application_operating_centre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_operating_centre` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  `action` varchar(1) DEFAULT NULL COMMENT 'Flag for add, delete, update. Values A,U or D',
  `ad_placed` tinyint(1) NOT NULL COMMENT 'An advert has been placed in a suitable publication to notify public of op centre changes.',
  `ad_placed_in` varchar(70) DEFAULT NULL COMMENT 'Publication advert placed in.',
  `ad_placed_date` date DEFAULT NULL COMMENT 'Date advert published.',
  `publication_appropriate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publication deemed appropriate by caseworker.',
  `permission` tinyint(1) NOT NULL COMMENT 'Applicant has permission to use site or owns it.',
  `sufficient_parking` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Site has sufficient parking for vehicles and trailers applied for.',
  `no_of_trailers_required` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of trailers required to be kept at op centre',
  `no_of_vehicles_required` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of vehicles required to be kept at op centre',
  `vi_action` varchar(1) DEFAULT NULL COMMENT 'Flag used in populated the vehicle inspectorate extract sent to mobile compliance system as part of batch job',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `is_interim` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is operating centre required to be on interim licence.',
  `s4_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_application_operating_centre_olbs_key` (`olbs_key`),
  KEY `ix_application_operating_centre_application_id` (`application_id`),
  KEY `ix_application_operating_centre_operating_centre_id` (`operating_centre_id`),
  KEY `ix_application_operating_centre_created_by` (`created_by`),
  KEY `ix_application_operating_centre_last_modified_by` (`last_modified_by`),
  KEY `ix_application_operating_centre_s4_id` (`s4_id`),
  CONSTRAINT `fk_application_oc_oc_id_oc_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_s4_id_s4_id` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Operating centre changes included in the application. Can be add, update or deletes. Adds will create a licence OC if app is successful. Update change values on relevent lic OC and deletes set removed date.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_operating_centre`
--

LOCK TABLES `application_operating_centre` WRITE;
/*!40000 ALTER TABLE `application_operating_centre` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_operating_centre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_organisation_person`
--

DROP TABLE IF EXISTS `application_organisation_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_organisation_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `person_id` int(10) unsigned NOT NULL,
  `original_person_id` int(10) unsigned DEFAULT NULL COMMENT 'Populated if change is an edit of a person record on a licence.',
  `organisation_id` int(10) unsigned NOT NULL,
  `application_id` int(10) unsigned NOT NULL,
  `action` varchar(1) NOT NULL,
  `position` varchar(45) DEFAULT NULL COMMENT 'Populated if org type is other.  For Ltd companies derived from company type.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_application_organisation_person_person_id` (`person_id`),
  KEY `ix_application_organisation_person_organisation_id` (`organisation_id`),
  KEY `ix_application_organisation_person_application_id` (`application_id`),
  KEY `ix_application_organisation_person_last_modified_by` (`last_modified_by`),
  KEY `ix_application_organisation_person_created_by` (`created_by`),
  KEY `fk_application_organisation_person_person1_idx` (`original_person_id`),
  CONSTRAINT `fk_application_org_person_org_id_org_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_organisation_person_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_organisation_person_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_organisation_person_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_organisation_person_person1` FOREIGN KEY (`original_person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_organisation_person_person_id_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores organisation people on an application. When application is granted they get copied into main organisation_person table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_organisation_person`
--

LOCK TABLES `application_organisation_person` WRITE;
/*!40000 ALTER TABLE `application_organisation_person` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_organisation_person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_tracking`
--

DROP TABLE IF EXISTS `application_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_tracking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `type_of_licence_status` int(11) DEFAULT NULL,
  `business_type_status` int(11) DEFAULT NULL,
  `business_details_status` int(11) DEFAULT NULL,
  `addresses_status` int(11) DEFAULT NULL,
  `people_status` int(11) DEFAULT NULL,
  `taxi_phv_status` int(11) DEFAULT NULL,
  `operating_centres_status` int(11) DEFAULT NULL,
  `financial_evidence_status` int(11) DEFAULT NULL,
  `transport_managers_status` int(11) DEFAULT NULL,
  `vehicles_status` int(11) DEFAULT NULL,
  `vehicles_psv_status` int(11) DEFAULT NULL,
  `vehicles_declarations_status` int(11) DEFAULT NULL,
  `discs_status` int(11) DEFAULT NULL,
  `community_licences_status` int(11) DEFAULT NULL,
  `safety_status` int(11) DEFAULT NULL,
  `conditions_undertakings_status` int(11) DEFAULT NULL,
  `financial_history_status` int(11) DEFAULT NULL,
  `licence_history_status` int(11) DEFAULT NULL,
  `convictions_penalties_status` int(11) DEFAULT NULL,
  `undertakings_status` int(11) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `last_modified_by` int(10) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_id_UNIQUE` (`application_id`),
  KEY `fk_application_tracking_application1_idx` (`application_id`),
  KEY `fk_application_tracking_user1_idx` (`created_by`),
  KEY `fk_application_tracking_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_application_tracking_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_tracking_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_tracking_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used to track status of an application for display to internal users.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_tracking`
--

LOCK TABLES `application_tracking` WRITE;
/*!40000 ALTER TABLE `application_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_notice_period`
--

DROP TABLE IF EXISTS `bus_notice_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_notice_period` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `notice_area` varchar(70) NOT NULL COMMENT 'The area relevant for the period. Initially Scotland or Other.',
  `standard_period` smallint(5) unsigned NOT NULL,
  `cancellation_period` smallint(5) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_bus_notice_period_created_by` (`created_by`),
  KEY `ix_bus_notice_period_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_bus_notice_period_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_notice_period_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Notice periods for bus route changes to decide if route variation is short notice';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_notice_period`
--

LOCK TABLES `bus_notice_period` WRITE;
/*!40000 ALTER TABLE `bus_notice_period` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_notice_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg`
--

DROP TABLE IF EXISTS `bus_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `parent_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(32) NOT NULL,
  `status_change_date` datetime DEFAULT NULL COMMENT 'Used for reporting on SLAs. Updated whenever state changes.',
  `revert_status` varchar(32) NOT NULL,
  `licence_id` int(10) unsigned NOT NULL,
  `bus_notice_period_id` int(10) unsigned NOT NULL COMMENT 'Scottish or other',
  `route_no` smallint(5) unsigned NOT NULL COMMENT 'Increases by one for each registration added to licence',
  `reg_no` varchar(70) NOT NULL COMMENT 'lic_no plus slash plus route_no',
  `service_no` varchar(70) DEFAULT NULL COMMENT 'Number on front of bus',
  `start_point` varchar(100) DEFAULT NULL,
  `finish_point` varchar(100) DEFAULT NULL,
  `via` varchar(255) DEFAULT NULL,
  `other_details` varchar(800) DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_short_notice` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Application late.  Enables short notice detail entry',
  `use_all_stops` tinyint(1) NOT NULL DEFAULT '0',
  `has_manoeuvre` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Service reverses, turns around etc.',
  `manoeuvre_detail` varchar(255) DEFAULT NULL,
  `need_new_stop` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Needs a new bus stop',
  `new_stop_detail` varchar(255) DEFAULT NULL,
  `has_not_fixed_stop` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Stops at not predefined stop.  i.e. waved down by user',
  `not_fixed_stop_detail` varchar(255) DEFAULT NULL,
  `subsidised` varchar(32) NOT NULL COMMENT 'Yes, No, In-Part',
  `subsidy_detail` varchar(255) DEFAULT NULL,
  `timetable_acceptable` tinyint(1) NOT NULL DEFAULT '0',
  `map_supplied` tinyint(1) NOT NULL DEFAULT '0',
  `route_description` varchar(1000) DEFAULT NULL,
  `copied_to_la_pte` tinyint(1) NOT NULL DEFAULT '0',
  `la_short_note` tinyint(1) NOT NULL DEFAULT '0',
  `application_signed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_date` date DEFAULT NULL,
  `operating_centre_id` int(10) unsigned DEFAULT NULL COMMENT 'Populated if the oc address is to be used',
  `variation_no` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Increments for each variation',
  `op_notified_la_pte` tinyint(1) NOT NULL DEFAULT '0',
  `stopping_arrangements` varchar(800) DEFAULT NULL,
  `trc_condition_checked` tinyint(1) NOT NULL DEFAULT '0',
  `trc_notes` varchar(255) DEFAULT NULL,
  `organisation_email` varchar(255) DEFAULT NULL,
  `is_txc_app` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Was created through transxchange',
  `ebsr_refresh` tinyint(1) NOT NULL DEFAULT '0',
  `txc_app_type` varchar(20) DEFAULT NULL,
  `reason_cancelled` varchar(255) DEFAULT NULL,
  `reason_refused` varchar(255) DEFAULT NULL,
  `reason_sn_refused` varchar(255) DEFAULT NULL,
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `short_notice_refused` tinyint(1) NOT NULL DEFAULT '0',
  `is_quality_partnership` tinyint(1) NOT NULL DEFAULT '0',
  `quality_partnership_details` varchar(4000) DEFAULT NULL,
  `quality_partnership_facilities_used` tinyint(1) NOT NULL DEFAULT '0',
  `is_quality_contract` tinyint(1) NOT NULL DEFAULT '0',
  `quality_contract_details` varchar(4000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bus_reg_olbs_key` (`olbs_key`),
  KEY `ix_bus_reg_licence_id` (`licence_id`),
  KEY `ix_bus_reg_bus_notice_period_id` (`bus_notice_period_id`),
  KEY `ix_bus_reg_subsidised` (`subsidised`),
  KEY `ix_bus_reg_created_by` (`created_by`),
  KEY `ix_bus_reg_last_modified_by` (`last_modified_by`),
  KEY `ix_bus_reg_withdrawn_reason` (`withdrawn_reason`),
  KEY `ix_bus_reg_status` (`status`),
  KEY `ix_bus_reg_revert_status` (`revert_status`),
  KEY `ix_bus_reg_reg_no` (`reg_no`),
  KEY `fk_bus_reg_parent_id_bus_reg_id` (`parent_id`),
  CONSTRAINT `fk_bus_reg_bus_notice_period_id_bus_notice_period_id` FOREIGN KEY (`bus_notice_period_id`) REFERENCES `bus_notice_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_parent_id_bus_reg_id` FOREIGN KEY (`parent_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_revert_status_ref_data_id` FOREIGN KEY (`revert_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_subsidised_ref_data_id` FOREIGN KEY (`subsidised`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_withdrawn_reason_ref_data_id` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bus registration.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg`
--

LOCK TABLES `bus_reg` WRITE;
/*!40000 ALTER TABLE `bus_reg` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg_bus_service_type`
--

DROP TABLE IF EXISTS `bus_reg_bus_service_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg_bus_service_type` (
  `bus_reg_id` int(10) unsigned NOT NULL,
  `bus_service_type_id` int(10) unsigned NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`bus_reg_id`,`bus_service_type_id`),
  UNIQUE KEY `uk_bus_reg_bus_service_type_olbs_key_bus_service_type_id` (`olbs_key`,`bus_service_type_id`),
  KEY `ix_bus_reg_bus_service_type_bus_reg_id` (`bus_reg_id`),
  KEY `fk_bus_reg_bus_serv_type_bus_service_type_id_bus_service_type_id` (`bus_service_type_id`),
  CONSTRAINT `fk_bus_reg_bus_serv_type_bus_service_type_id_bus_service_type_id` FOREIGN KEY (`bus_service_type_id`) REFERENCES `bus_service_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_bus_service_type_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg_bus_service_type`
--

LOCK TABLES `bus_reg_bus_service_type` WRITE;
/*!40000 ALTER TABLE `bus_reg_bus_service_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg_bus_service_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg_local_auth`
--

DROP TABLE IF EXISTS `bus_reg_local_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg_local_auth` (
  `bus_reg_id` int(10) unsigned NOT NULL,
  `local_authority_id` int(10) unsigned NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`bus_reg_id`,`local_authority_id`),
  UNIQUE KEY `uk_bus_reg_local_auth_local_authority_id_bus_reg_id` (`local_authority_id`,`bus_reg_id`),
  KEY `ix_bus_reg_local_auth_local_authority_id` (`local_authority_id`),
  KEY `ix_bus_reg_local_auth_olbs_key_local_authority_id` (`olbs_key`,`local_authority_id`),
  CONSTRAINT `fk_bus_reg_local_auth_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_local_auth_local_authority_id_local_authority_id` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg_local_auth`
--

LOCK TABLES `bus_reg_local_auth` WRITE;
/*!40000 ALTER TABLE `bus_reg_local_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg_local_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg_other_service`
--

DROP TABLE IF EXISTS `bus_reg_other_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg_other_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `bus_reg_id` int(10) unsigned NOT NULL,
  `service_no` varchar(70) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bus_reg_other_service_olbs_key` (`olbs_key`),
  KEY `ix_bus_reg_other_service_bus_reg_id` (`bus_reg_id`),
  KEY `ix_bus_reg_other_service_created_by` (`created_by`),
  KEY `ix_bus_reg_other_service_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_bus_reg_other_service_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_other_service_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_other_service_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg_other_service`
--

LOCK TABLES `bus_reg_other_service` WRITE;
/*!40000 ALTER TABLE `bus_reg_other_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg_other_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg_traffic_area`
--

DROP TABLE IF EXISTS `bus_reg_traffic_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg_traffic_area` (
  `traffic_area_id` varchar(1) NOT NULL,
  `bus_reg_id` int(10) unsigned NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`bus_reg_id`,`traffic_area_id`),
  UNIQUE KEY `uk_bus_reg_traffic_area_traffic_area_id_bus_reg_id` (`traffic_area_id`,`bus_reg_id`),
  KEY `ix_bus_reg_traffic_area_bus_reg_id` (`bus_reg_id`),
  KEY `ix_bus_reg_traffic_area_olbs_key_traffic_area_id` (`olbs_key`,`traffic_area_id`),
  CONSTRAINT `fk_bus_reg_traffic_area_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_traffic_area_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg_traffic_area`
--

LOCK TABLES `bus_reg_traffic_area` WRITE;
/*!40000 ALTER TABLE `bus_reg_traffic_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg_traffic_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_reg_variation_reason`
--

DROP TABLE IF EXISTS `bus_reg_variation_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_reg_variation_reason` (
  `bus_reg_id` int(10) unsigned NOT NULL,
  `variation_reason_id` varchar(32) NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` int(10) unsigned DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`bus_reg_id`,`variation_reason_id`),
  KEY `ix_bus_reg_variation_reason_bus_reg_id` (`bus_reg_id`),
  KEY `ix_bus_reg_variation_reason_variation_reason_id` (`variation_reason_id`),
  KEY `ix_bus_reg_variation_reason_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  CONSTRAINT `fk_bus_reg_variation_reason_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_variation_reason_variation_reason_id_ref_data_id` FOREIGN KEY (`variation_reason_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reasons why a bus reg variation was applied for';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_reg_variation_reason`
--

LOCK TABLES `bus_reg_variation_reason` WRITE;
/*!40000 ALTER TABLE `bus_reg_variation_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_reg_variation_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_service_type`
--

DROP TABLE IF EXISTS `bus_service_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_service_type` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(70) DEFAULT NULL,
  `txc_name` varchar(70) DEFAULT NULL COMMENT 'TransXChange name.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bus service types such as hail and ride, normal stopping, circular etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_service_type`
--

LOCK TABLES `bus_service_type` WRITE;
/*!40000 ALTER TABLE `bus_service_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_service_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_short_notice`
--

DROP TABLE IF EXISTS `bus_short_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_short_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `bus_reg_id` int(10) unsigned NOT NULL,
  `bank_holiday_change` tinyint(1) NOT NULL DEFAULT '0',
  `unforseen_change` tinyint(1) NOT NULL DEFAULT '0',
  `unforseen_detail` varchar(255) DEFAULT NULL,
  `timetable_change` tinyint(1) NOT NULL DEFAULT '0',
  `timetable_detail` varchar(255) DEFAULT NULL,
  `replacement_change` tinyint(1) NOT NULL DEFAULT '0',
  `replacement_detail` varchar(255) DEFAULT NULL,
  `holiday_change` tinyint(1) NOT NULL DEFAULT '0',
  `holiday_detail` varchar(255) DEFAULT NULL,
  `trc_change` tinyint(1) NOT NULL DEFAULT '0',
  `trc_detail` varchar(255) DEFAULT NULL,
  `police_change` tinyint(1) NOT NULL DEFAULT '0',
  `police_detail` varchar(255) DEFAULT NULL,
  `special_occasion_change` tinyint(1) NOT NULL DEFAULT '0',
  `special_occasion_detail` varchar(255) DEFAULT NULL,
  `connection_change` tinyint(1) NOT NULL DEFAULT '0',
  `connection_detail` varchar(255) DEFAULT NULL,
  `not_available_change` tinyint(1) NOT NULL DEFAULT '0',
  `not_available_detail` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bus_short_notice_bus_reg_id` (`bus_reg_id`),
  UNIQUE KEY `uk_bus_short_notice_olbs_key` (`olbs_key`),
  KEY `ix_bus_short_notice_created_by` (`created_by`),
  KEY `ix_bus_short_notice_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_bus_short_notice_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_short_notice_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_short_notice_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_short_notice`
--

LOCK TABLES `bus_short_notice` WRITE;
/*!40000 ALTER TABLE `bus_short_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `bus_short_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_category`
--

DROP TABLE IF EXISTS `case_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_category` (
  `case_id` int(10) unsigned NOT NULL,
  `category_id` varchar(32) NOT NULL,
  PRIMARY KEY (`case_id`,`category_id`),
  KEY `ix_case_category_case_id` (`case_id`),
  KEY `ix_case_category_category_id` (`category_id`),
  CONSTRAINT `fk_case_category_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_category_category_id_ref_data_id` FOREIGN KEY (`category_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorises a case for reporting and searching.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_category`
--

LOCK TABLES `case_category` WRITE;
/*!40000 ALTER TABLE `case_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_outcome`
--

DROP TABLE IF EXISTS `case_outcome`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_outcome` (
  `cases_id` int(10) unsigned NOT NULL,
  `outcome_id` varchar(32) NOT NULL,
  PRIMARY KEY (`cases_id`,`outcome_id`),
  KEY `fk_case_outcome_cases1_idx` (`cases_id`),
  KEY `fk_case_outcome_ref_data1_idx` (`outcome_id`),
  CONSTRAINT `fk_case_outcome_cases1` FOREIGN KEY (`cases_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_outcome_ref_data1` FOREIGN KEY (`outcome_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_outcome`
--

LOCK TABLES `case_outcome` WRITE;
/*!40000 ALTER TABLE `case_outcome` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_outcome` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_type` varchar(32) NOT NULL COMMENT 'Created from App.lic or TM',
  `application_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `ecms_no` varchar(45) DEFAULT NULL,
  `open_date` datetime NOT NULL,
  `closed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `description` varchar(1024) DEFAULT NULL COMMENT 'Short summary note in old system',
  `is_impounding` tinyint(1) NOT NULL DEFAULT '0',
  `erru_originating_authority` varchar(50) DEFAULT NULL,
  `erru_transport_undertaking_name` varchar(100) DEFAULT NULL,
  `erru_vrm` varchar(15) DEFAULT NULL,
  `erru_case_type` varchar(32) DEFAULT NULL COMMENT 'MSI type.',
  `annual_test_history` varchar(4000) DEFAULT NULL,
  `prohibition_note` varchar(4000) DEFAULT NULL,
  `penalties_note` varchar(4000) DEFAULT NULL,
  `conviction_note` varchar(4000) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  KEY `ix_cases_application_id` (`application_id`),
  KEY `ix_cases_created_by` (`created_by`),
  KEY `ix_cases_last_modified_by` (`last_modified_by`),
  KEY `ix_cases_transport_manager_id` (`transport_manager_id`),
  KEY `ix_cases_case_type` (`case_type`),
  KEY `ix_cases_erru_case_type` (`erru_case_type`),
  KEY `ix_cases_licence_id` (`licence_id`),
  KEY `ix_cases_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  CONSTRAINT `fk_cases_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_case_type_ref_data_id` FOREIGN KEY (`case_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_erru_case_type_ref_data_id` FOREIGN KEY (`erru_case_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Compliance case.  Can be for TMs or a licence. If licence can link to application and operating centres. Various types, such as public inquiry, impounding etc. Has several SLAs and a decision, stay, appeal process.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cases`
--

LOCK TABLES `cases` WRITE;
/*!40000 ALTER TABLE `cases` DISABLE KEYS */;
/*!40000 ALTER TABLE `cases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) NOT NULL,
  `is_doc_category` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Documents can have this category',
  `is_task_category` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Tasks can have this category',
  `is_scan_category` tinyint(1) NOT NULL DEFAULT '1',
  `task_allocation_type` varchar(32) DEFAULT NULL COMMENT 'Tasks of this category are allocated based upon TA, a single team or complex rules for icence type, TA, MLH.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_category_created_by` (`created_by`),
  KEY `ix_category_last_modified_by` (`last_modified_by`),
  KEY `ix_category_task_allocation_type` (`task_allocation_type`),
  CONSTRAINT `fk_category_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_task_allocation_type_ref_data_id` FOREIGN KEY (`task_allocation_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Business Category, such as licencing, compliance, environmental. Used to categorise documentation and tasks. Has different sub categories for tasks ao documents.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_of_entity`
--

DROP TABLE IF EXISTS `change_of_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_of_entity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL COMMENT 'The new licence',
  `old_licence_no` varchar(18) NOT NULL COMMENT 'The old licence number for display purposes',
  `old_organisation_name` varchar(160) NOT NULL COMMENT 'The old organisation for display purposes',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_change_of_entity_licence_id` (`licence_id`),
  UNIQUE KEY `uk_change_of_entity_olbs_key` (`olbs_key`),
  KEY `ix_change_of_entity_licence_id` (`licence_id`),
  KEY `ix_change_of_entity_created_by` (`created_by`),
  KEY `ix_change_of_entity_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_change_of_entity_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_change_of_entity_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_change_of_entity_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used when an organisation changes name via companies house and applies for a new licence. Results in old licence being withdrawn and link to old org name and licence being stored.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_of_entity`
--

LOCK TABLES `change_of_entity` WRITE;
/*!40000 ALTER TABLE `change_of_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `change_of_entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic`
--

DROP TABLE IF EXISTS `community_lic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `status` varchar(32) NOT NULL COMMENT 'annulled, cns, expired, pending, returned, revoked, surrender, suspended, valid, void, withdrawn',
  `expired_date` datetime DEFAULT NULL COMMENT 'The date the licence expired.',
  `specified_date` datetime DEFAULT NULL COMMENT 'Activation date of com licence.',
  `licence_expired_date` date DEFAULT NULL COMMENT 'The date the community licence will expire. Typically 5 years after specified date.  Generally less for an interim licence.',
  `issue_no` smallint(5) unsigned DEFAULT NULL COMMENT 'Issue 0 is the office copy. 0 is the licence, all others are refered to as certified copies.',
  `serial_no` int(10) unsigned DEFAULT NULL COMMENT 'Business ID',
  `serial_no_prefix` varchar(4) DEFAULT NULL COMMENT 'UKGB or UKNI',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_lic_olbs_key` (`olbs_key`),
  KEY `ix_community_lic_licence_id` (`licence_id`),
  KEY `ix_community_lic_created_by` (`created_by`),
  KEY `ix_community_lic_last_modified_by` (`last_modified_by`),
  KEY `ix_community_lic_com_lic_status` (`status`),
  CONSTRAINT `fk_community_lic_com_lic_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Community licence. A licence for travel within the EU for both goods and PSV (but not PSV SR).';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic`
--

LOCK TABLES `community_lic` WRITE;
/*!40000 ALTER TABLE `community_lic` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_suspension`
--

DROP TABLE IF EXISTS `community_lic_suspension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_suspension` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `community_lic_id` int(10) unsigned NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_actioned` tinyint(1) DEFAULT '0' COMMENT 'Possibly not required. In legacy as part of batch job.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_lic_suspension_olbs_key` (`olbs_key`),
  KEY `ix_community_lic_suspension_community_lic_id` (`community_lic_id`),
  KEY `ix_community_lic_suspension_created_by` (`created_by`),
  KEY `ix_community_lic_suspension_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_suspension_community_lic_id_community_lic_id` FOREIGN KEY (`community_lic_id`) REFERENCES `community_lic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A suspension for a community lic.  Possibly future dated. Processed by overnight batch job to change com lic state.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_suspension`
--

LOCK TABLES `community_lic_suspension` WRITE;
/*!40000 ALTER TABLE `community_lic_suspension` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_suspension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_suspension_reason`
--

DROP TABLE IF EXISTS `community_lic_suspension_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_suspension_reason` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `community_lic_suspension_id` int(10) unsigned NOT NULL,
  `type_id` varchar(32) NOT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_lic_suspension_reason_olbs_key` (`olbs_key`),
  KEY `ix_community_lic_suspension_reason_community_lic_suspension_id` (`community_lic_suspension_id`),
  KEY `ix_community_lic_suspension_reason_created_by` (`created_by`),
  KEY `ix_community_lic_suspension_reason_last_modified_by` (`last_modified_by`),
  KEY `fk_community_lic_suspension_reason_ref_data1_idx` (`type_id`),
  CONSTRAINT `fk_com_lic_susp_reason_com_lic_susp_id_com_lic_susp_id` FOREIGN KEY (`community_lic_suspension_id`) REFERENCES `community_lic_suspension` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_ref_data1` FOREIGN KEY (`type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reasons for a suspension.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_suspension_reason`
--

LOCK TABLES `community_lic_suspension_reason` WRITE;
/*!40000 ALTER TABLE `community_lic_suspension_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_suspension_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_withdrawal`
--

DROP TABLE IF EXISTS `community_lic_withdrawal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_withdrawal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `community_lic_id` int(10) unsigned NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_lic_withdrawal_olbs_key` (`olbs_key`),
  KEY `ix_community_lic_withdrawal_community_lic_id` (`community_lic_id`),
  KEY `ix_community_lic_withdrawal_created_by` (`created_by`),
  KEY `ix_community_lic_withdrawal_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_withdrawal_community_lic_id_community_lic_id` FOREIGN KEY (`community_lic_id`) REFERENCES `community_lic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Time period a community licence is withdrawn over.  Possibly future dated. Batch job uses this to change com lic states overnight.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_withdrawal`
--

LOCK TABLES `community_lic_withdrawal` WRITE;
/*!40000 ALTER TABLE `community_lic_withdrawal` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_withdrawal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_withdrawal_reason`
--

DROP TABLE IF EXISTS `community_lic_withdrawal_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_withdrawal_reason` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `community_lic_withdrawal_id` int(10) unsigned NOT NULL,
  `type_id` varchar(32) NOT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_lic_withdrawal_reason_olbs_key` (`olbs_key`),
  KEY `ix_community_lic_withdrawal_reason_community_lic_withdrawal_id` (`community_lic_withdrawal_id`),
  KEY `ix_community_lic_withdrawal_reason_created_by` (`created_by`),
  KEY `ix_community_lic_withdrawal_reason_last_modified_by` (`last_modified_by`),
  KEY `fk_community_lic_withdrawal_reason_ref_data1_idx` (`type_id`),
  CONSTRAINT `fk_com_lic_withdrw_reason_com_lic_withdrw_id_com_lic_withdrw_id` FOREIGN KEY (`community_lic_withdrawal_id`) REFERENCES `community_lic_withdrawal` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_ref_data1` FOREIGN KEY (`type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reasons for com lic withdrawal.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_withdrawal_reason`
--

LOCK TABLES `community_lic_withdrawal_reason` WRITE;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies_house_request`
--

DROP TABLE IF EXISTS `companies_house_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies_house_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `requested_on` datetime DEFAULT NULL,
  `request_type` varchar(255) DEFAULT NULL,
  `request_error` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Logging of companies house interface requests.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies_house_request`
--

LOCK TABLES `companies_house_request` WRITE;
/*!40000 ALTER TABLE `companies_house_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `companies_house_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_subsidiary`
--

DROP TABLE IF EXISTS `company_subsidiary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_subsidiary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `name` varchar(70) DEFAULT NULL,
  `company_no` varchar(12) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_company_subsidiary_olbs_key` (`olbs_key`,`licence_id`),
  KEY `ix_company_subsidiary_created_by` (`created_by`),
  KEY `ix_company_subsidiary_last_modified_by` (`last_modified_by`),
  KEY `fk_company_subsidiary_licence1_idx` (`licence_id`),
  CONSTRAINT `fk_company_subsidiary_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subsidiaries of a company.  Business requirement is only to store name number, hence not stored in organisation table.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_subsidiary`
--

LOCK TABLES `company_subsidiary` WRITE;
/*!40000 ALTER TABLE `company_subsidiary` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_subsidiary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint`
--

DROP TABLE IF EXISTS `complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `is_compliance` tinyint(1) NOT NULL COMMENT 'Compliance complaints are against people, environmental against sites (OCs)',
  `case_id` int(10) unsigned NOT NULL,
  `complainant_contact_details_id` int(10) unsigned DEFAULT NULL COMMENT 'The person making the complaint',
  `complaint_date` datetime DEFAULT NULL COMMENT 'Date received',
  `status` varchar(32) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `complaint_type` varchar(32) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `driver_forename` varchar(40) DEFAULT NULL,
  `driver_family_name` varchar(40) DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_complaint_complainant_contact_details_id` (`complainant_contact_details_id`),
  KEY `ix_complaint_created_by` (`created_by`),
  KEY `ix_complaint_last_modified_by` (`last_modified_by`),
  KEY `ix_complaint_status` (`status`),
  KEY `ix_complaint_complaint_type` (`complaint_type`),
  KEY `ix_complaint_case_id` (`case_id`),
  KEY `ix_complaint_olbs_key` (`olbs_key`),
  CONSTRAINT `fk_complaint_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_complainant_contact_details_id_contact_details_id` FOREIGN KEY (`complainant_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_complaint_type_ref_data_id` FOREIGN KEY (`complaint_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Complaints against entities (e.g. director, company) or sites, known as environmental complaints.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint`
--

LOCK TABLES `complaint` WRITE;
/*!40000 ALTER TABLE `complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condition_undertaking`
--

DROP TABLE IF EXISTS `condition_undertaking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condition_undertaking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `operating_centre_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `lic_condition_variation_id` int(10) unsigned DEFAULT NULL COMMENT 'The condition on linked to the licence that is being changed by the application condition. Changes applied when application is granted.',
  `condition_type` varchar(32) NOT NULL COMMENT 'Condition or Undertaking',
  `added_via` varchar(32) DEFAULT NULL COMMENT 'Episode, Application or Licence',
  `action` varchar(1) DEFAULT NULL COMMENT 'For application conditions A for add and U for update, if updating a licence condition via an app.',
  `attached_to` varchar(32) DEFAULT NULL COMMENT 'Licence or Operating Centre',
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `is_fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  `approval_user_id` int(10) unsigned DEFAULT NULL,
  `notes` varchar(8000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_condition_undertaking_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_condition_undertaking_added_via` (`added_via`),
  KEY `ix_condition_undertaking_attached_to` (`attached_to`),
  KEY `ix_condition_undertaking_condition_type` (`condition_type`),
  KEY `ix_condition_undertaking_case_id` (`case_id`),
  KEY `ix_condition_undertaking_licence_id` (`licence_id`),
  KEY `ix_condition_undertaking_operating_centre_id` (`operating_centre_id`),
  KEY `ix_condition_undertaking_application_id` (`application_id`),
  KEY `ix_condition_undertaking_created_by` (`created_by`),
  KEY `ix_condition_undertaking_last_modified_by` (`last_modified_by`),
  KEY `ix_condition_undertaking_lic_condition_variation_id` (`lic_condition_variation_id`),
  KEY `ix_condition_undertaking_approval_user_id` (`approval_user_id`),
  CONSTRAINT `fk_cond_undertaking_lic_cond_variation_id_cond_undertaking_id` FOREIGN KEY (`lic_condition_variation_id`) REFERENCES `condition_undertaking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_added_via_ref_data_id` FOREIGN KEY (`added_via`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_approval_user_id_user_id` FOREIGN KEY (`approval_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_attached_to_ref_data_id` FOREIGN KEY (`attached_to`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_condition_type_ref_data_id` FOREIGN KEY (`condition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Conditions or undertakings applied to a licence or application. e.g. Remove hedge at site X.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condition_undertaking`
--

LOCK TABLES `condition_undertaking` WRITE;
/*!40000 ALTER TABLE `condition_undertaking` DISABLE KEYS */;
/*!40000 ALTER TABLE `condition_undertaking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `contact_type` varchar(32) NOT NULL,
  `email_address` varchar(60) DEFAULT NULL,
  `fao` varchar(90) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `address_id` int(10) unsigned DEFAULT NULL,
  `person_id` int(10) unsigned DEFAULT NULL,
  `forename` varchar(40) DEFAULT NULL,
  `family_name` varchar(40) DEFAULT NULL,
  `written_permission_to_engage` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_contact_details_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_contact_details_person_id` (`person_id`),
  KEY `ix_contact_details_address_id` (`address_id`),
  KEY `ix_contact_details_created_by` (`created_by`),
  KEY `ix_contact_details_last_modified_by` (`last_modified_by`),
  KEY `ix_contact_details_contact_type` (`contact_type`),
  CONSTRAINT `fk_contact_details_address_id_address_id` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_contact_type_ref_data_id` FOREIGN KEY (`contact_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_person_id_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contact details of an entity, normally a person or site.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_details`
--

LOCK TABLES `contact_details` WRITE;
/*!40000 ALTER TABLE `contact_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conviction`
--

DROP TABLE IF EXISTS `conviction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conviction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `defendant_type` varchar(32) NOT NULL,
  `offence_date` datetime DEFAULT NULL,
  `conviction_date` datetime DEFAULT NULL,
  `court` varchar(70) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `costs` varchar(255) DEFAULT NULL COMMENT 'New olcs field?',
  `msi` tinyint(1) DEFAULT NULL,
  `is_dealt_with` tinyint(1) NOT NULL DEFAULT '0',
  `is_declared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Declared to TC',
  `birth_date` date DEFAULT NULL,
  `person_firstname` varchar(70) DEFAULT NULL,
  `person_lastname` varchar(70) DEFAULT NULL COMMENT 'Length 70 because of ETL. Will hold some org names from legacy data.',
  `notes` varchar(4000) DEFAULT NULL,
  `taken_into_consideration` varchar(4000) DEFAULT NULL,
  `category_text` varchar(1024) DEFAULT NULL COMMENT 'user entered category for non act',
  `conviction_category` varchar(32) DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `operator_name` varchar(70) DEFAULT NULL COMMENT 'Set if defendant type is operator. Copy of op name at time of conviction.',
  `case_id` int(10) unsigned NOT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_conviction_olbs_key` (`olbs_key`),
  KEY `ix_conviction_transport_manager_id` (`transport_manager_id`),
  KEY `ix_conviction_created_by` (`created_by`),
  KEY `ix_conviction_last_modified_by` (`last_modified_by`),
  KEY `ix_conviction_case_id` (`case_id`),
  KEY `ix_conviction_defendant_type` (`defendant_type`),
  KEY `ix_conviction_conviction_category` (`conviction_category`),
  CONSTRAINT `fk_conviction_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_conviction_category_ref_data_id` FOREIGN KEY (`conviction_category`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_defendant_type_ref_data_id` FOREIGN KEY (`defendant_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Traffic conviction notified to the traffic commissioner by an operator.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conviction`
--

LOCK TABLES `conviction` WRITE;
/*!40000 ALTER TABLE `conviction` DISABLE KEYS */;
/*!40000 ALTER TABLE `conviction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `correspondence_inbox`
--

DROP TABLE IF EXISTS `correspondence_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `correspondence_inbox` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `archived` tinyint(1) DEFAULT NULL,
  `accessed` tinyint(1) DEFAULT NULL,
  `email_reminder_sent` tinyint(1) DEFAULT NULL,
  `printed` tinyint(1) DEFAULT NULL,
  `document_id` int(10) unsigned NOT NULL,
  `licence_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_correspondence_inbox_olbs_key` (`olbs_key`),
  KEY `ix_correspondence_inbox_document_id` (`document_id`),
  KEY `ix_correspondence_inbox_licence_id` (`licence_id`),
  KEY `ix_correspondence_inbox_created_by` (`created_by`),
  KEY `ix_correspondence_inbox_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_correspondence_inbox_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_document_id_document_id` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `correspondence_inbox`
--

LOCK TABLES `correspondence_inbox` WRITE;
/*!40000 ALTER TABLE `correspondence_inbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `correspondence_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` varchar(2) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `country_desc` varchar(50) DEFAULT NULL,
  `is_member_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is EU member. Affects transit rules and EU permits',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_country_created_by` (`created_by`),
  KEY `ix_country_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_country_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_country_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ISO3166 Country list';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `decision`
--

DROP TABLE IF EXISTS `decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `decision` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_decision_created_by` (`created_by`),
  KEY `ix_decision_last_modified_by` (`last_modified_by`),
  KEY `ix_decision_goods_or_psv` (`goods_or_psv`),
  CONSTRAINT `fk_decision_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_decision_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_decision_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decision`
--

LOCK TABLES `decision` WRITE;
/*!40000 ALTER TABLE `decision` DISABLE KEYS */;
/*!40000 ALTER TABLE `decision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disc_sequence`
--

DROP TABLE IF EXISTS `disc_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disc_sequence` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `goods_or_psv` varchar(32) NOT NULL,
  `restricted` int(10) unsigned DEFAULT NULL,
  `special_restricted` int(10) unsigned DEFAULT NULL,
  `standard_national` int(10) unsigned DEFAULT NULL,
  `standard_international` int(10) unsigned DEFAULT NULL,
  `r_prefix` varchar(3) DEFAULT NULL,
  `sr_prefix` varchar(3) DEFAULT NULL,
  `sn_prefix` varchar(3) DEFAULT NULL,
  `si_prefix` varchar(3) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  `is_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_disc_sequence_goods_or_psv` (`goods_or_psv`),
  KEY `ix_disc_sequence_traffic_area_id` (`traffic_area_id`),
  KEY `ix_disc_sequence_created_by` (`created_by`),
  KEY `ix_disc_sequence_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_disc_sequence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disc_sequence`
--

LOCK TABLES `disc_sequence` WRITE;
/*!40000 ALTER TABLE `disc_sequence` DISABLE KEYS */;
/*!40000 ALTER TABLE `disc_sequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disqualification`
--

DROP TABLE IF EXISTS `disqualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disqualification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `is_disqualified` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `period` smallint(5) NOT NULL,
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `officer_cd_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_disqualification_olbs_key` (`olbs_key`),
  KEY `ix_disqualification_organisation_id` (`organisation_id`),
  KEY `ix_disqualification_created_by` (`created_by`),
  KEY `ix_disqualification_last_modified_by` (`last_modified_by`),
  KEY `ix_disqualification_officer_cd_id` (`officer_cd_id`),
  CONSTRAINT `fk_disqualification_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_officer_cd_id_contact_details_id` FOREIGN KEY (`officer_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disqualification`
--

LOCK TABLES `disqualification` WRITE;
/*!40000 ALTER TABLE `disqualification` DISABLE KEYS */;
/*!40000 ALTER TABLE `disqualification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_bookmark`
--

DROP TABLE IF EXISTS `doc_bookmark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_bookmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_doc_bookmark_created_by` (`created_by`),
  KEY `ix_doc_bookmark_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_doc_bookmark_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_bookmark_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Name of a bookmark in a document to be replaced during document generation with standard text';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_bookmark`
--

LOCK TABLES `doc_bookmark` WRITE;
/*!40000 ALTER TABLE `doc_bookmark` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_bookmark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_paragraph`
--

DROP TABLE IF EXISTS `doc_paragraph`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_paragraph` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `para_title` varchar(255) NOT NULL COMMENT 'Displayed on UI for user to select paragraph',
  `para_text` varchar(1000) DEFAULT NULL COMMENT 'Text to replace bookmark with.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_doc_paragraph_created_by` (`created_by`),
  KEY `ix_doc_paragraph_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_doc_paragraph_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Paragraph of text that can be used to replace bookmarks in generated documents';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_paragraph`
--

LOCK TABLES `doc_paragraph` WRITE;
/*!40000 ALTER TABLE `doc_paragraph` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_paragraph` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_paragraph_bookmark`
--

DROP TABLE IF EXISTS `doc_paragraph_bookmark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_paragraph_bookmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `doc_bookmark_id` int(10) unsigned NOT NULL,
  `doc_paragraph_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_doc_paragraph_bookmark_doc_bookmark_id_doc_paragraph_id` (`doc_bookmark_id`,`doc_paragraph_id`),
  KEY `ix_doc_paragraph_bookmark_doc_paragraph_id` (`doc_paragraph_id`),
  KEY `ix_doc_paragraph_bookmark_created_by` (`created_by`),
  KEY `ix_doc_paragraph_bookmark_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_doc_paragraph_bookmark_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_doc_bookmark_id_doc_bookmark_id` FOREIGN KEY (`doc_bookmark_id`) REFERENCES `doc_bookmark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_doc_paragraph_id_doc_paragraph_id` FOREIGN KEY (`doc_paragraph_id`) REFERENCES `doc_paragraph` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Possible paragraphs that can replace a bookmark.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_paragraph_bookmark`
--

LOCK TABLES `doc_paragraph_bookmark` WRITE;
/*!40000 ALTER TABLE `doc_paragraph_bookmark` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_paragraph_bookmark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_template`
--

DROP TABLE IF EXISTS `doc_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `document_id` int(10) unsigned NOT NULL COMMENT 'Link to the rtf template',
  `is_ni` tinyint(1) NOT NULL DEFAULT '0',
  `suppress_from_op` tinyint(1) NOT NULL COMMENT 'Do not send to organisation even if they are signed up to receive documents by email.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_doc_template_sub_category_id` (`sub_category_id`),
  KEY `ix_doc_template_document_id` (`document_id`),
  KEY `ix_doc_template_created_by` (`created_by`),
  KEY `ix_doc_template_last_modified_by` (`last_modified_by`),
  KEY `ix_doc_template_category_id` (`category_id`),
  CONSTRAINT `fk_doc_template_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_document_id_document_id` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_sub_category_id_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Document templates used to generate standard documents allowing the user to replace bookmarks with one of several paragraphs related to the bookmark';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_template`
--

LOCK TABLES `doc_template` WRITE;
/*!40000 ALTER TABLE `doc_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_template_bookmark`
--

DROP TABLE IF EXISTS `doc_template_bookmark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_template_bookmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `doc_template_id` int(10) unsigned NOT NULL,
  `doc_bookmark_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_doc_template_bookmark_doc_template_id_doc_bookmark_id` (`doc_template_id`,`doc_bookmark_id`),
  KEY `ix_doc_template_bookmark_doc_bookmark_id` (`doc_bookmark_id`),
  KEY `ix_doc_template_bookmark_created_by` (`created_by`),
  KEY `ix_doc_template_bookmark_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_doc_template_bookmark_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_doc_bookmark_id_doc_bookmark_id` FOREIGN KEY (`doc_bookmark_id`) REFERENCES `doc_bookmark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_doc_template_id_doc_template_id` FOREIGN KEY (`doc_template_id`) REFERENCES `doc_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bookmarks within a template. Used to display list of bookmarks in UI for replacement.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_template_bookmark`
--

LOCK TABLES `doc_template_bookmark` WRITE;
/*!40000 ALTER TABLE `doc_template_bookmark` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_template_bookmark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `document_store_id` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_id` int(10) unsigned DEFAULT NULL,
  `is_read_only` tinyint(1) DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `operating_centre_id` int(10) unsigned DEFAULT NULL,
  `opposition_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `irfo_organisation_id` int(10) unsigned DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `is_digital` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag true if doc was received or sent via email',
  `is_scan` tinyint(1) NOT NULL DEFAULT '0',
  `file_extension` varchar(32) NOT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_document_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_document_file_extension` (`file_extension`),
  KEY `ix_document_traffic_area_id` (`traffic_area_id`),
  KEY `ix_document_category_id` (`category_id`),
  KEY `ix_document_sub_category_id` (`sub_category_id`),
  KEY `ix_document_licence_id` (`licence_id`),
  KEY `ix_document_application_id` (`application_id`),
  KEY `ix_document_case_id` (`case_id`),
  KEY `ix_document_transport_manager_id` (`transport_manager_id`),
  KEY `ix_document_operating_centre_id` (`operating_centre_id`),
  KEY `ix_document_created_by` (`created_by`),
  KEY `ix_document_last_modified_by` (`last_modified_by`),
  KEY `ix_document_opposition_id` (`opposition_id`),
  KEY `ix_document_bus_reg_id` (`bus_reg_id`),
  KEY `ix_document_irfo_organisation_id` (`irfo_organisation_id`),
  CONSTRAINT `fk_document_file_extension` FOREIGN KEY (`file_extension`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_irfo_organisation_id_organisation_id` FOREIGN KEY (`irfo_organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_opposition_id_opposition_id` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_sub_category_id_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reference to a single document. Containg categorisation of document and links to key entities for ownership.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document`
--

LOCK TABLES `document` WRITE;
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
/*!40000 ALTER TABLE `document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebsr_route_reprint`
--

DROP TABLE IF EXISTS `ebsr_route_reprint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebsr_route_reprint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `exception_name` varchar(45) DEFAULT NULL,
  `scale` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `published_timestamp` datetime DEFAULT NULL,
  `requested_timestamp` datetime NOT NULL,
  `bus_reg_id` int(10) unsigned NOT NULL,
  `requested_user_id` int(10) unsigned NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_ebsr_route_reprint_bus_reg_id` (`bus_reg_id`),
  KEY `ix_ebsr_route_reprint_requested_user_id` (`requested_user_id`),
  KEY `ix_ebsr_route_reprint_olbs_key` (`olbs_key`),
  CONSTRAINT `fk_ebsr_route_reprint_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_route_reprint_requested_user_id_user_id` FOREIGN KEY (`requested_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebsr_route_reprint`
--

LOCK TABLES `ebsr_route_reprint` WRITE;
/*!40000 ALTER TABLE `ebsr_route_reprint` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebsr_route_reprint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebsr_submission`
--

DROP TABLE IF EXISTS `ebsr_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebsr_submission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `ebsr_submission_status_id` varchar(32) NOT NULL,
  `ebsr_submission_type_id` varchar(32) NOT NULL,
  `document_id` int(10) unsigned DEFAULT NULL,
  `submitted_date` datetime DEFAULT NULL,
  `licence_no` varchar(7) DEFAULT NULL,
  `organisation_email_address` varchar(100) DEFAULT NULL,
  `application_classification` varchar(32) DEFAULT NULL,
  `variation_no` smallint(5) unsigned DEFAULT NULL,
  `tan_code` varchar(2) DEFAULT NULL,
  `registration_no` varchar(4) DEFAULT NULL,
  `validation_start` datetime DEFAULT NULL,
  `validation_end` datetime DEFAULT NULL,
  `publish_start` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `process_start` datetime DEFAULT NULL,
  `process_end` datetime DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `ebsr_submission_result` varchar(64) DEFAULT NULL,
  `distribute_start` datetime DEFAULT NULL,
  `distribute_end` datetime DEFAULT NULL,
  `distribute_expire` datetime DEFAULT NULL,
  `is_from_ftp` tinyint(1) NOT NULL DEFAULT '0',
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ebsr_submission_olbs_key` (`olbs_key`),
  KEY `ix_ebsr_submission_document_id` (`document_id`),
  KEY `ix_ebsr_submission_bus_reg_id` (`bus_reg_id`),
  KEY `ix_ebsr_submission_ebsr_submission_status_id` (`ebsr_submission_status_id`),
  KEY `ix_ebsr_submission_ebsr_submission_type_id` (`ebsr_submission_type_id`),
  CONSTRAINT `fk_ebsr_submission_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_document_id_document_id` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_ebsr_submission_status_id_ref_data_id` FOREIGN KEY (`ebsr_submission_status_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_ebsr_submission_type_id_ref_data_id` FOREIGN KEY (`ebsr_submission_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebsr_submission`
--

LOCK TABLES `ebsr_submission` WRITE;
/*!40000 ALTER TABLE `ebsr_submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebsr_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elastic_updates`
--

DROP TABLE IF EXISTS `elastic_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elastic_updates` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `index_name` varchar(32) NOT NULL COMMENT 'Elastic search index name.',
  `previous_runtime` int(10) unsigned DEFAULT NULL COMMENT 'unix time',
  `runtime` int(10) unsigned DEFAULT NULL COMMENT 'unix time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Enables delta builds of elastic indexes by storing runtime to be compared to last_modified_on columns in index source data.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elastic_updates`
--

LOCK TABLES `elastic_updates` WRITE;
/*!40000 ALTER TABLE `elastic_updates` DISABLE KEYS */;
/*!40000 ALTER TABLE `elastic_updates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enforcement_area`
--

DROP TABLE IF EXISTS `enforcement_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enforcement_area` (
  `id` varchar(4) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(70) NOT NULL,
  `email_address` varchar(60) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_enforcement_area_created_by` (`created_by`),
  KEY `ix_enforcement_area_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_enforcement_area_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_enforcement_area_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Geographic area for enforcement';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enforcement_area`
--

LOCK TABLES `enforcement_area` WRITE;
/*!40000 ALTER TABLE `enforcement_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `enforcement_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_history`
--

DROP TABLE IF EXISTS `event_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `event_history_type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `event_datetime` datetime NOT NULL,
  `event_description` varchar(255) DEFAULT NULL,
  `entity_type` varchar(45) DEFAULT NULL,
  `entity_pk` int(10) unsigned DEFAULT NULL,
  `entity_version` int(10) unsigned DEFAULT NULL,
  `event_data` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_event_history_user_id` (`user_id`),
  KEY `ix_event_history_licence_id` (`licence_id`),
  KEY `ix_event_history_application_id` (`application_id`),
  KEY `ix_event_history_transport_manager_id` (`transport_manager_id`),
  KEY `fk_event_history_event_history_type1_idx` (`event_history_type_id`),
  KEY `fk_event_history_organisation1_idx` (`organisation_id`),
  KEY `fk_event_history_cases1_idx` (`case_id`),
  KEY `fk_event_history_bus_reg1_idx` (`bus_reg_id`),
  CONSTRAINT `fk_event_history_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_event_history_type1` FOREIGN KEY (`event_history_type_id`) REFERENCES `event_history_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Key events related to a key entity. e.g. vehicle added to licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_history`
--

LOCK TABLES `event_history` WRITE;
/*!40000 ALTER TABLE `event_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_history_type`
--

DROP TABLE IF EXISTS `event_history_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_history_type` (
  `id` int(10) unsigned NOT NULL,
  `event_code` varchar(3) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Description of events. Such as vehicle added. Licence revoked.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_history_type`
--

LOCK TABLES `event_history_type` WRITE;
/*!40000 ALTER TABLE `event_history_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_history_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee`
--

DROP TABLE IF EXISTS `fee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `fee_type_id` int(10) unsigned NOT NULL,
  `fee_status` varchar(32) NOT NULL,
  `parent_fee_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `received_amount` decimal(10,2) DEFAULT NULL,
  `invoice_line_no` smallint(5) unsigned DEFAULT NULL,
  `invoiced_date` datetime DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `receipt_no` varchar(45) DEFAULT NULL,
  `waive_approval_date` datetime DEFAULT NULL,
  `waive_reason` varchar(255) DEFAULT NULL,
  `waive_recommendation_date` datetime DEFAULT NULL,
  `waive_recommender_user_id` int(10) unsigned DEFAULT NULL,
  `waive_approver_user_id` int(10) unsigned DEFAULT NULL,
  `irfo_fee_id` varchar(10) DEFAULT NULL,
  `irfo_fee_exempt` tinyint(1) DEFAULT NULL,
  `irfo_file_no` varchar(10) DEFAULT NULL,
  `irfo_gv_permit_id` int(10) unsigned DEFAULT NULL,
  `payment_method` varchar(32) DEFAULT NULL COMMENT 'The method of the successful payment. There could have been several attempts to pay with differing methods, but only one successful.',
  `payer_name` varchar(100) DEFAULT NULL COMMENT 'Name on cheque or POs',
  `cheque_po_number` varchar(100) DEFAULT NULL,
  `cheque_po_date` datetime DEFAULT NULL,
  `paying_in_slip_number` varchar(100) DEFAULT NULL COMMENT 'Paying in slip from DVSA employee paying cheque or PO into bank.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_fee_application_id` (`application_id`),
  KEY `ix_fee_bus_reg_id` (`bus_reg_id`),
  KEY `ix_fee_licence_id` (`licence_id`),
  KEY `ix_fee_task_id` (`task_id`),
  KEY `ix_fee_fee_type_id` (`fee_type_id`),
  KEY `ix_fee_parent_fee_id` (`parent_fee_id`),
  KEY `ix_fee_waive_recommender_user_id` (`waive_recommender_user_id`),
  KEY `ix_fee_waive_approver_user_id` (`waive_approver_user_id`),
  KEY `ix_fee_created_by` (`created_by`),
  KEY `ix_fee_last_modified_by` (`last_modified_by`),
  KEY `ix_fee_irfo_gv_permit_id` (`irfo_gv_permit_id`),
  KEY `ix_fee_fee_status` (`fee_status`),
  KEY `ix_fee_payment_method` (`payment_method`),
  CONSTRAINT `fk_fee_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_fee_status_ref_data_id` FOREIGN KEY (`fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_fee_type_id_fee_type_id` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_irfo_gv_permit_id_irfo_gv_permit_id` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_parent_fee_id_fee_id` FOREIGN KEY (`parent_fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_method_ref_data_id` FOREIGN KEY (`payment_method`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_task_id_task_id` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_waive_approver_user_id_user_id` FOREIGN KEY (`waive_approver_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_waive_recommender_user_id_user_id` FOREIGN KEY (`waive_recommender_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee`
--

LOCK TABLES `fee` WRITE;
/*!40000 ALTER TABLE `fee` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_manual_alteration`
--

DROP TABLE IF EXISTS `fee_manual_alteration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_manual_alteration` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `fee_id` int(10) unsigned NOT NULL,
  `alteration_type` varchar(32) NOT NULL,
  `actioned_date` datetime DEFAULT NULL,
  `post_received_date` datetime DEFAULT NULL,
  `post_receipt_no` varchar(45) DEFAULT NULL,
  `post_value` decimal(10,2) DEFAULT NULL,
  `post_fee_status` varchar(32) NOT NULL,
  `pre_received_date` datetime DEFAULT NULL,
  `pre_receipt_no` varchar(45) DEFAULT NULL,
  `pre_value` decimal(10,2) DEFAULT NULL,
  `pre_fee_status` varchar(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_fee_manual_alteration_fee_id` (`fee_id`),
  KEY `ix_fee_manual_alteration_alteration_type` (`alteration_type`),
  KEY `ix_fee_manual_alteration_post_fee_status` (`post_fee_status`),
  KEY `ix_fee_manual_alteration_pre_fee_status` (`pre_fee_status`),
  KEY `ix_fee_manual_alteration_user_id` (`user_id`),
  CONSTRAINT `fk_fee_manual_alteration_alteration_type_ref_data_id` FOREIGN KEY (`alteration_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_fee_id_fee_id` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_post_fee_status_ref_data_id` FOREIGN KEY (`post_fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_pre_fee_status_ref_data_id` FOREIGN KEY (`pre_fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_manual_alteration`
--

LOCK TABLES `fee_manual_alteration` WRITE;
/*!40000 ALTER TABLE `fee_manual_alteration` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_manual_alteration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_payment`
--

DROP TABLE IF EXISTS `fee_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `fee_id` int(10) unsigned NOT NULL,
  `payment_id` int(10) unsigned NOT NULL,
  `fee_value` decimal(10,2) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_fee_payment_fee_id_payment_id` (`fee_id`,`payment_id`),
  KEY `ix_fee_payment_payment_id` (`payment_id`),
  KEY `ix_fee_payment_fee_id` (`fee_id`),
  KEY `ix_fee_payment_created_by` (`created_by`),
  KEY `ix_fee_payment_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_fee_payment_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_fee_id_fee_id` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_payment_id_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_payment`
--

LOCK TABLES `fee_payment` WRITE;
/*!40000 ALTER TABLE `fee_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_type`
--

DROP TABLE IF EXISTS `fee_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `fee_type` varchar(20) NOT NULL,
  `effective_from` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `accrual_rule` varchar(32) NOT NULL,
  `fixed_value` decimal(10,2) DEFAULT NULL,
  `annual_value` decimal(10,2) DEFAULT NULL,
  `five_year_value` decimal(10,2) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL,
  `goods_or_psv` varchar(32) NOT NULL,
  `expire_fee_with_licence` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Dont allow payment after licence expires',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_fee_type_traffic_area_id` (`traffic_area_id`),
  KEY `ix_fee_type_licence_type` (`licence_type`),
  KEY `ix_fee_type_goods_or_psv` (`goods_or_psv`),
  KEY `ix_fee_type_created_by` (`created_by`),
  KEY `ix_fee_type_last_modified_by` (`last_modified_by`),
  KEY `fk_fee_type_ref_data1_idx` (`accrual_rule`),
  CONSTRAINT `fk_fee_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_licence_type_ref_data_id` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_ref_data1` FOREIGN KEY (`accrual_rule`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_type`
--

LOCK TABLES `fee_type` WRITE;
/*!40000 ALTER TABLE `fee_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_standing_rate`
--

DROP TABLE IF EXISTS `financial_standing_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_standing_rate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_type` varchar(32) NOT NULL COMMENT 'e.g. Special Restricted',
  `goods_or_psv` varchar(32) NOT NULL COMMENT 'Goods or PSV',
  `additional_vehicle_rate` int(11) DEFAULT NULL,
  `first_vehicle_rate` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `effective_from` date NOT NULL COMMENT 'Effective from',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_financial_standing_rate_licence_type` (`licence_type`),
  KEY `ix_financial_standing_rate_goods_or_psv` (`goods_or_psv`),
  KEY `ix_financial_standing_rate_created_by` (`created_by`),
  KEY `ix_financial_standing_rate_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_financial_standing_rate_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_financial_standing_rate_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_financial_standing_rate_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_financial_standing_rate_licence_type_ref_data_id` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used to calculate financial standing requirements for an operator.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_standing_rate`
--

LOCK TABLES `financial_standing_rate` WRITE;
/*!40000 ALTER TABLE `financial_standing_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_standing_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_disc`
--

DROP TABLE IF EXISTS `goods_disc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_disc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_vehicle_id` int(10) unsigned NOT NULL,
  `disc_no` varchar(50) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `ceased_date` datetime DEFAULT NULL,
  `is_copy` tinyint(1) NOT NULL DEFAULT '0',
  `is_interim` tinyint(1) NOT NULL DEFAULT '0',
  `reprint_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_printing` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_goods_disc_olbs_key` (`olbs_key`),
  KEY `ix_goods_disc_licence_vehicle_id` (`licence_vehicle_id`),
  KEY `ix_goods_disc_created_by` (`created_by`),
  KEY `ix_goods_disc_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_goods_disc_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_goods_disc_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_goods_disc_licence_vehicle_id_licence_vehicle_id` FOREIGN KEY (`licence_vehicle_id`) REFERENCES `licence_vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Goods vehicle disc details';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_disc`
--

LOCK TABLES `goods_disc` WRITE;
/*!40000 ALTER TABLE `goods_disc` DISABLE KEYS */;
/*!40000 ALTER TABLE `goods_disc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grace_period`
--

DROP TABLE IF EXISTS `grace_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grace_period` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `assigned_to_user_id` int(10) unsigned NOT NULL,
  `period_type` varchar(32) NOT NULL COMMENT 'Either TM, financial standing or both.',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL COMMENT 'Period can start on a future date.',
  `end_date` date NOT NULL,
  `grace_period_no` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Grace period number for the licence. Starts at 1.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_grace_period_olbs_key` (`olbs_key`),
  KEY `ix_grace_period_licence_id` (`licence_id`),
  KEY `ix_grace_period_assigned_to_user_id` (`assigned_to_user_id`),
  KEY `ix_grace_period_created_by` (`created_by`),
  KEY `ix_grace_period_last_modified_by` (`last_modified_by`),
  KEY `ix_grace_period_period_type` (`period_type`),
  CONSTRAINT `fk_grace_period_assigned_to_user_id_user_id` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_grace_period_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_grace_period_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_grace_period_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_grace_period_period_type_ref_data_id` FOREIGN KEY (`period_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A period when a licence has no TM or financial standing info.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grace_period`
--

LOCK TABLES `grace_period` WRITE;
/*!40000 ALTER TABLE `grace_period` DISABLE KEYS */;
/*!40000 ALTER TABLE `grace_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hearing`
--

DROP TABLE IF EXISTS `hearing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hearing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `venue_id` int(10) unsigned NOT NULL,
  `venue_other` varchar(255) DEFAULT NULL COMMENT 'Freetext if venue is not in list of common venues',
  `presiding_tc_id` int(10) unsigned DEFAULT NULL,
  `hearing_type` varchar(32) NOT NULL COMMENT 'In chambers or office procedure.',
  `hearing_date` datetime DEFAULT NULL,
  `agreed_by_tc_date` date DEFAULT NULL,
  `witness_count` tinyint(3) unsigned DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_hearing_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_hearing_case_id` (`case_id`),
  KEY `ix_hearing_venue_id` (`venue_id`),
  KEY `ix_hearing_created_by` (`created_by`),
  KEY `ix_hearing_last_modified_by` (`last_modified_by`),
  KEY `ix_hearing_presiding_tc_id` (`presiding_tc_id`),
  KEY `ix_hearing_hearing_type` (`hearing_type`),
  CONSTRAINT `fk_hearing_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_hearing_type_ref_data_id` FOREIGN KEY (`hearing_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_presiding_tc_id_presiding_tc_id` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_venue_id_pi_venue_id` FOREIGN KEY (`venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hearing`
--

LOCK TABLES `hearing` WRITE;
/*!40000 ALTER TABLE `hearing` DISABLE KEYS */;
/*!40000 ALTER TABLE `hearing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hint_question`
--

DROP TABLE IF EXISTS `hint_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hint_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `category_no` tinyint(3) unsigned NOT NULL COMMENT 'Split questions into groups to force picking one from each.',
  `hint_question` varchar(100) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_hint_question_created_by` (`created_by`),
  KEY `ix_hint_question_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_hint_question_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hint_question_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Hint questions for user password reset.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hint_question`
--

LOCK TABLES `hint_question` WRITE;
/*!40000 ALTER TABLE `hint_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `hint_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impounding`
--

DROP TABLE IF EXISTS `impounding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impounding` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `impounding_type` varchar(32) NOT NULL,
  `case_id` int(10) unsigned NOT NULL,
  `hearing_date` datetime DEFAULT NULL,
  `application_receipt_date` datetime DEFAULT NULL,
  `outcome_sent_date` datetime DEFAULT NULL,
  `presiding_tc_id` int(10) unsigned DEFAULT NULL,
  `outcome` varchar(32) DEFAULT NULL COMMENT 'Vehicle(s) returned or not returned',
  `notes` varchar(4000) DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `pi_venue_id` int(10) unsigned DEFAULT NULL,
  `pi_venue_other` varchar(255) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_impounding_created_by` (`created_by`),
  KEY `ix_impounding_last_modified_by` (`last_modified_by`),
  KEY `ix_impounding_presiding_tc_id` (`presiding_tc_id`),
  KEY `ix_impounding_outcome` (`outcome`),
  KEY `ix_impounding_impounding_type` (`impounding_type`),
  KEY `ix_impounding_case_id` (`case_id`),
  KEY `ix_impounding_pi_venue_id` (`pi_venue_id`),
  CONSTRAINT `fk_impounding_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_impounding_type_ref_data_id` FOREIGN KEY (`impounding_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_outcome_ref_data_id` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_pi_venue_id_pi_venue_id` FOREIGN KEY (`pi_venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_presiding_tc_id_presiding_tc_id` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Details of vehicle impoundings.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impounding`
--

LOCK TABLES `impounding` WRITE;
/*!40000 ALTER TABLE `impounding` DISABLE KEYS */;
/*!40000 ALTER TABLE `impounding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impounding_legislation_type`
--

DROP TABLE IF EXISTS `impounding_legislation_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impounding_legislation_type` (
  `impounding_id` int(10) unsigned NOT NULL,
  `impounding_legislation_type_id` varchar(32) NOT NULL,
  PRIMARY KEY (`impounding_id`,`impounding_legislation_type_id`),
  KEY `ix_impounding_legislation_type_impounding_legislation_type_id` (`impounding_legislation_type_id`),
  KEY `ix_impounding_legislation_type_impounding_id` (`impounding_id`),
  CONSTRAINT `fk_impndng_legislatn_type_impndng_legislatn_type_id_ref_data_id` FOREIGN KEY (`impounding_legislation_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_legislation_type_impounding_id_impounding_id` FOREIGN KEY (`impounding_id`) REFERENCES `impounding` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impounding_legislation_type`
--

LOCK TABLES `impounding_legislation_type` WRITE;
/*!40000 ALTER TABLE `impounding_legislation_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `impounding_legislation_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inspection_email`
--

DROP TABLE IF EXISTS `inspection_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspection_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `subject` varchar(1024) NOT NULL,
  `message_body` mediumtext,
  `email_status` varchar(1) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `inspection_request_id` int(10) unsigned NOT NULL,
  `sender_email_address` varchar(200) DEFAULT NULL,
  `received_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_inspection_email_inspection_request_id` (`inspection_request_id`),
  CONSTRAINT `fk_inspection_email_inspection_request_id_inspection_request_id` FOREIGN KEY (`inspection_request_id`) REFERENCES `inspection_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Details of emails sent to request inspections of operating centres and the response from the inspector.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inspection_email`
--

LOCK TABLES `inspection_email` WRITE;
/*!40000 ALTER TABLE `inspection_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `inspection_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inspection_request`
--

DROP TABLE IF EXISTS `inspection_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspection_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  `requestor_user_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `request_type` varchar(32) NOT NULL,
  `result_type` varchar(32) NOT NULL,
  `requestor_notes` text,
  `inspector_notes` text,
  `due_date` date DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  `deferred_date` date DEFAULT NULL,
  `inspector_name` varchar(70) DEFAULT NULL,
  `report_type` varchar(32) DEFAULT NULL,
  `trailors_examined_no` smallint(5) unsigned DEFAULT NULL,
  `vehicles_examined_no` smallint(5) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_inspection_request_olbs_key` (`olbs_key`),
  KEY `ix_inspection_request_licence_id` (`licence_id`),
  KEY `ix_inspection_request_application_id` (`application_id`),
  KEY `ix_inspection_request_operating_centre_id` (`operating_centre_id`),
  KEY `ix_inspection_request_task_id` (`task_id`),
  KEY `ix_inspection_request_case_id` (`case_id`),
  KEY `ix_inspection_request_report_type` (`report_type`),
  KEY `ix_inspection_request_request_type` (`request_type`),
  KEY `ix_inspection_request_result_type` (`result_type`),
  KEY `ix_inspection_request_requestor_user_id` (`requestor_user_id`),
  KEY `ix_inspection_request_created_by` (`created_by`),
  KEY `ix_inspection_request_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_inspection_request_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_report_type_ref_data_id` FOREIGN KEY (`report_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_request_type_ref_data_id` FOREIGN KEY (`request_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_requestor_user_id_user_id` FOREIGN KEY (`requestor_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_result_type_ref_data_id` FOREIGN KEY (`result_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_task_id_task_id` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Begins and tracks a request to inspect an operating centre.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inspection_request`
--

LOCK TABLES `inspection_request` WRITE;
/*!40000 ALTER TABLE `inspection_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `inspection_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_country`
--

DROP TABLE IF EXISTS `irfo_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(100) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_irfo_country_created_by` (`created_by`),
  KEY `ix_irfo_country_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_country_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_country_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IRFO country list. Not necessarily real country names. e.g. Turkey 3rd Country Transit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_country`
--

LOCK TABLES `irfo_country` WRITE;
/*!40000 ALTER TABLE `irfo_country` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_gv_permit`
--

DROP TABLE IF EXISTS `irfo_gv_permit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_gv_permit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `organisation_id` int(10) unsigned NOT NULL,
  `irfo_gv_permit_type_id` int(10) unsigned NOT NULL,
  `irfo_fee_id` varchar(10) DEFAULT NULL,
  `irfo_permit_status` varchar(32) NOT NULL,
  `exemption_details` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_fee_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `in_force_date` date DEFAULT NULL,
  `no_of_copies` smallint(5) unsigned NOT NULL DEFAULT '0',
  `note` varchar(2000) DEFAULT NULL,
  `permit_printed` tinyint(1) NOT NULL DEFAULT '0',
  `year_required` smallint(5) unsigned DEFAULT NULL,
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_gv_permit_olbs_key` (`olbs_key`),
  KEY `ix_irfo_gv_permit_created_by` (`created_by`),
  KEY `ix_irfo_gv_permit_last_modified_by` (`last_modified_by`),
  KEY `ix_irfo_gv_permit_organisation_id` (`organisation_id`),
  KEY `ix_irfo_gv_permit_irfo_gv_permit_type_id` (`irfo_gv_permit_type_id`),
  KEY `ix_irfo_gv_permit_irfo_permit_status` (`irfo_permit_status`),
  KEY `ix_irfo_gv_permit_withdrawn_reason` (`withdrawn_reason`),
  CONSTRAINT `fk_irfo_gv_permit_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_irfo_gv_permit_type_id_irfo_gv_permit_type_id` FOREIGN KEY (`irfo_gv_permit_type_id`) REFERENCES `irfo_gv_permit_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_irfo_permit_status_ref_data_id` FOREIGN KEY (`irfo_permit_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_withdrawn_reason_ref_data_id` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Goods vehicle permit for International Road Freight Operator';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_gv_permit`
--

LOCK TABLES `irfo_gv_permit` WRITE;
/*!40000 ALTER TABLE `irfo_gv_permit` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_gv_permit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_gv_permit_type`
--

DROP TABLE IF EXISTS `irfo_gv_permit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_gv_permit_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(100) NOT NULL,
  `irfo_country_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_irfo_gv_permit_type_irfo_country_id` (`irfo_country_id`),
  KEY `ix_irfo_gv_permit_type_created_by` (`created_by`),
  KEY `ix_irfo_gv_permit_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_gv_permit_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_type_irfo_country_id_irfo_country_id` FOREIGN KEY (`irfo_country_id`) REFERENCES `irfo_country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_gv_permit_type`
--

LOCK TABLES `irfo_gv_permit_type` WRITE;
/*!40000 ALTER TABLE `irfo_gv_permit_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_gv_permit_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_partner`
--

DROP TABLE IF EXISTS `irfo_partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_partner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `irfo_psv_auth_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(70) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_partner_olbs_key` (`olbs_key`),
  KEY `ix_irfo_partner_organisation_id` (`organisation_id`),
  KEY `ix_irfo_partner_irfo_psv_auth_id` (`irfo_psv_auth_id`),
  KEY `ix_irfo_partner_created_by` (`created_by`),
  KEY `ix_irfo_partner_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_partner_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_irfo_psv_auth_id_irfo_psv_auth_id` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_partner`
--

LOCK TABLES `irfo_partner` WRITE;
/*!40000 ALTER TABLE `irfo_partner` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_partner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_permit_stock`
--

DROP TABLE IF EXISTS `irfo_permit_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_permit_stock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `irfo_gv_permit_id` int(10) unsigned DEFAULT NULL,
  `serial_no` int(10) unsigned NOT NULL,
  `irfo_country_id` int(10) unsigned NOT NULL,
  `valid_for_year` smallint(5) unsigned NOT NULL,
  `status` varchar(32) NOT NULL,
  `void_return_date` date DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_permit_stock_olbs_key` (`olbs_key`),
  KEY `ix_irfo_permit_stock_irfo_gv_permit_id` (`irfo_gv_permit_id`),
  KEY `ix_irfo_permit_stock_irfo_country_id` (`irfo_country_id`),
  KEY `ix_irfo_permit_stock_status` (`status`),
  KEY `ix_irfo_permit_stock_created_by` (`created_by`),
  KEY `ix_irfo_permit_stock_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_permit_stock_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_irfo_country_id_irfo_country_id` FOREIGN KEY (`irfo_country_id`) REFERENCES `irfo_country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_irfo_gv_permit_id_irfo_gv_permit_id` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_permit_stock`
--

LOCK TABLES `irfo_permit_stock` WRITE;
/*!40000 ALTER TABLE `irfo_permit_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_permit_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_psv_auth`
--

DROP TABLE IF EXISTS `irfo_psv_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_psv_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `organisation_id` int(10) unsigned NOT NULL,
  `irfo_psv_auth_type_id` int(10) unsigned NOT NULL,
  `status` varchar(32) NOT NULL,
  `exemption_details` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_fee_exempt_application` tinyint(1) NOT NULL DEFAULT '0',
  `is_fee_exempt_annual` tinyint(1) NOT NULL DEFAULT '0',
  `in_force_date` date DEFAULT NULL,
  `irfo_fee_id` varchar(10) NOT NULL,
  `irfo_file_no` varchar(10) NOT NULL,
  `copies_issued` smallint(5) unsigned NOT NULL DEFAULT '0',
  `copies_required` smallint(5) unsigned NOT NULL DEFAULT '0',
  `copies_required_total` smallint(5) unsigned NOT NULL DEFAULT '0',
  `copies_issued_total` smallint(5) unsigned NOT NULL DEFAULT '0',
  `journey_frequency` varchar(32) DEFAULT NULL,
  `last_date_copies_req` datetime DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `service_route_from` varchar(30) NOT NULL,
  `service_route_to` varchar(30) NOT NULL,
  `validity_period` smallint(6) NOT NULL COMMENT 'Years valid for.  Some negative numbers in legacy hence signed.',
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_psv_auth_olbs_key` (`olbs_key`),
  KEY `ix_irfo_psv_auth_created_by` (`created_by`),
  KEY `ix_irfo_psv_auth_last_modified_by` (`last_modified_by`),
  KEY `ix_irfo_psv_auth_organisation_id` (`organisation_id`),
  KEY `ix_irfo_psv_auth_journey_frequency` (`journey_frequency`),
  KEY `ix_irfo_psv_auth_irfo_psv_auth_type_id` (`irfo_psv_auth_type_id`),
  KEY `ix_irfo_psv_auth_status` (`status`),
  KEY `ix_irfo_psv_auth_withdrawn_reason` (`withdrawn_reason`),
  CONSTRAINT `fk_irfo_psv_auth_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_irfo_psv_auth_type_id_irfo_psv_auth_type_id` FOREIGN KEY (`irfo_psv_auth_type_id`) REFERENCES `irfo_psv_auth_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_journey_frequency_ref_data_id` FOREIGN KEY (`journey_frequency`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_withdrawn_reason_ref_data_id` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PRV Authorisation to use a vehicle to travel through another country.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_psv_auth`
--

LOCK TABLES `irfo_psv_auth` WRITE;
/*!40000 ALTER TABLE `irfo_psv_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_psv_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_psv_auth_type`
--

DROP TABLE IF EXISTS `irfo_psv_auth_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_psv_auth_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(100) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_irfo_psv_auth_type_created_by` (`created_by`),
  KEY `ix_irfo_psv_auth_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_psv_auth_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_psv_auth_type`
--

LOCK TABLES `irfo_psv_auth_type` WRITE;
/*!40000 ALTER TABLE `irfo_psv_auth_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_psv_auth_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_transit_country`
--

DROP TABLE IF EXISTS `irfo_transit_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_transit_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(45) NOT NULL,
  `irfo_psv_auth_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` int(10) unsigned DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_transit_country_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_irfo_transit_country_irfo_psv_auth_id` (`irfo_psv_auth_id`),
  KEY `ix_irfo_transit_country_created_by` (`created_by`),
  KEY `ix_irfo_transit_country_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_irfo_transit_country_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_transit_country_irfo_psv_auth_id_irfo_psv_auth_id` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_transit_country_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_transit_country`
--

LOCK TABLES `irfo_transit_country` WRITE;
/*!40000 ALTER TABLE `irfo_transit_country` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_transit_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irfo_vehicle`
--

DROP TABLE IF EXISTS `irfo_vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irfo_vehicle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `coc_a` tinyint(1) NOT NULL DEFAULT '0',
  `coc_b` tinyint(1) NOT NULL DEFAULT '0',
  `coc_c` tinyint(1) NOT NULL DEFAULT '0',
  `coc_d` tinyint(1) NOT NULL DEFAULT '0',
  `coc_t` tinyint(1) NOT NULL DEFAULT '0',
  `vrm` varchar(20) NOT NULL,
  `irfo_psv_auth_id` int(10) unsigned DEFAULT NULL,
  `irfo_gv_permit_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_irfo_vehicle_olbs_key` (`olbs_key`),
  KEY `ix_irfo_vehicle_irfo_psv_auth_id` (`irfo_psv_auth_id`),
  KEY `ix_irfo_vehicle_created_by` (`created_by`),
  KEY `ix_irfo_vehicle_last_modified_by` (`last_modified_by`),
  KEY `ix_irfo_vehicle_irfo_gv_permit_id` (`irfo_gv_permit_id`),
  CONSTRAINT `fk_irfo_vehicle_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_irfo_gv_permit_id_irfo_gv_permit_id` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_irfo_psv_auth_id_irfo_psv_auth_id` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irfo_vehicle`
--

LOCK TABLES `irfo_vehicle` WRITE;
/*!40000 ALTER TABLE `irfo_vehicle` DISABLE KEYS */;
/*!40000 ALTER TABLE `irfo_vehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_case_action`
--

DROP TABLE IF EXISTS `legacy_case_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_case_action` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(45) DEFAULT NULL COMMENT 'Examples, No Action, Warning, Interview',
  `is_driver` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Possibly case outcomes in legacy, olbs system.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_case_action`
--

LOCK TABLES `legacy_case_action` WRITE;
/*!40000 ALTER TABLE `legacy_case_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_case_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_offence`
--

DROP TABLE IF EXISTS `legacy_offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_offence` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `definition` varchar(1000) DEFAULT NULL,
  `is_trailer` tinyint(1) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `num_of_offences` smallint(5) unsigned DEFAULT NULL,
  `offence_authority` varchar(100) DEFAULT NULL,
  `offence_date` date DEFAULT NULL,
  `offence_to_date` date DEFAULT NULL,
  `offender_name` varchar(100) DEFAULT NULL,
  `points` smallint(5) unsigned DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `offence_type` varchar(100) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_legacy_offence_created_by` (`created_by`),
  KEY `ix_legacy_offence_last_modified_by` (`last_modified_by`),
  KEY `fk_legacy_offence_cases1_idx` (`case_id`),
  CONSTRAINT `fk_legacy_offence_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_offence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_offence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy table holding offence information. Read only until can be dropped';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_offence`
--

LOCK TABLES `legacy_offence` WRITE;
/*!40000 ALTER TABLE `legacy_offence` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_offence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_pi_reason`
--

DROP TABLE IF EXISTS `legacy_pi_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_pi_reason` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `goods_or_psv` varchar(3) NOT NULL COMMENT 'GV or PSV',
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL COMMENT 'Northern Ireland or not',
  `is_decision` tinyint(1) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_legacy_pi_reason_created_by` (`created_by`),
  KEY `ix_legacy_pi_reason_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_legacy_pi_reason_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_pi_reason_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy system reasons and decisions related to public inquiries. Kept for read only screens';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_pi_reason`
--

LOCK TABLES `legacy_pi_reason` WRITE;
/*!40000 ALTER TABLE `legacy_pi_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_pi_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_recommendation`
--

DROP TABLE IF EXISTS `legacy_recommendation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_recommendation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `action_id` int(10) unsigned NOT NULL,
  `from_user_id` int(10) unsigned DEFAULT NULL,
  `to_user_id` int(10) unsigned DEFAULT NULL,
  `rec_date` datetime NOT NULL,
  `pi_reason` varchar(255) DEFAULT NULL,
  `comment` varchar(4000) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `notes` text,
  `pi_decision` varchar(255) DEFAULT NULL,
  `request` varchar(20) DEFAULT NULL,
  `revoke_lic` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `total_points` smallint(5) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_legacy_recommendation_case_id` (`case_id`),
  KEY `ix_legacy_recommendation_from_user_id` (`from_user_id`),
  KEY `ix_legacy_recommendation_to_user_id` (`to_user_id`),
  KEY `ix_legacy_recommendation_action_id` (`action_id`),
  KEY `ix_legacy_recommendation_created_by` (`created_by`),
  KEY `ix_legacy_recommendation_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_legacy_recommendation_action_id_legacy_case_action_id` FOREIGN KEY (`action_id`) REFERENCES `legacy_case_action` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_from_user_id_user_id` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_to_user_id_user_id` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recommendations table in OLBS.  Kept as read only reference only.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_recommendation`
--

LOCK TABLES `legacy_recommendation` WRITE;
/*!40000 ALTER TABLE `legacy_recommendation` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_recommendation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_recommendation_pi_reason`
--

DROP TABLE IF EXISTS `legacy_recommendation_pi_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_recommendation_pi_reason` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `comment` varchar(30) DEFAULT NULL,
  `legacy_recommendation_id` int(10) unsigned NOT NULL,
  `legacy_pi_reason_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_legacy_recommendation_pi_reason_legacy_recommendation_id` (`legacy_recommendation_id`),
  KEY `ix_legacy_recommendation_pi_reason_created_by` (`created_by`),
  KEY `ix_legacy_recommendation_pi_reason_last_modified_by` (`last_modified_by`),
  KEY `ix_legacy_recommendation_pi_reason_legacy_pi_reason_id` (`legacy_pi_reason_id`),
  CONSTRAINT `fk_legacy_rec_pi_reason_legacy_pi_reason_id_legacy_pi_reason_id` FOREIGN KEY (`legacy_pi_reason_id`) REFERENCES `legacy_pi_reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_rec_pi_reason_legacy_rec_id_legacy_rec_id` FOREIGN KEY (`legacy_recommendation_id`) REFERENCES `legacy_recommendation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_pi_reason_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_pi_reason_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_recommendation_pi_reason`
--

LOCK TABLES `legacy_recommendation_pi_reason` WRITE;
/*!40000 ALTER TABLE `legacy_recommendation_pi_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_recommendation_pi_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence`
--

DROP TABLE IF EXISTS `licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `enforcement_area_id` varchar(4) DEFAULT NULL COMMENT 'FK to vehicle_inspectorate.',
  `organisation_id` int(10) unsigned NOT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL COMMENT 'FK to traffic area.  An Operator can have One licence per area.',
  `correspondence_cd_id` int(10) unsigned DEFAULT NULL COMMENT 'Correspondence contact details',
  `establishment_cd_id` int(10) unsigned DEFAULT NULL COMMENT 'Establishment contact details',
  `transport_consultant_cd_id` int(10) unsigned DEFAULT NULL COMMENT 'Transport consultant contact details',
  `lic_no` varchar(18) DEFAULT NULL COMMENT 'Licence number.  Normally 9 Chars.  First denotes goods/psv, second TA, rest ID.',
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL,
  `status` varchar(32) NOT NULL,
  `vi_action` varchar(1) DEFAULT NULL COMMENT 'C, U or D.  Triggers VI export.',
  `tot_auth_trailers` smallint(5) unsigned DEFAULT NULL,
  `tot_auth_vehicles` smallint(5) unsigned DEFAULT NULL,
  `tot_auth_small_vehicles` smallint(5) unsigned DEFAULT NULL,
  `tot_auth_medium_vehicles` smallint(5) unsigned DEFAULT NULL,
  `tot_auth_large_vehicles` smallint(5) unsigned DEFAULT NULL,
  `tot_community_licences` smallint(5) unsigned DEFAULT NULL,
  `trailers_in_possession` smallint(5) unsigned DEFAULT NULL,
  `fabs_reference` varchar(10) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL COMMENT 'expiry date',
  `granted_date` datetime DEFAULT NULL COMMENT 'granted date',
  `review_date` date DEFAULT NULL,
  `fee_date` date DEFAULT NULL,
  `in_force_date` date DEFAULT NULL,
  `surrendered_date` datetime DEFAULT NULL,
  `safety_ins_trailers` smallint(5) unsigned DEFAULT NULL COMMENT 'Max period in weeks between safety inspections.',
  `safety_ins_vehicles` smallint(5) unsigned DEFAULT NULL COMMENT 'Max period in weeks between safety inspections.',
  `safety_ins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Does own safety inspections.',
  `safety_ins_varies` tinyint(1) DEFAULT NULL COMMENT 'New olcs column for when some vehicles inspected more often',
  `ni_flag` tinyint(1) DEFAULT NULL,
  `tachograph_ins` varchar(32) DEFAULT NULL COMMENT 'New olcs column values not applicable, external, internal',
  `tachograph_ins_name` varchar(90) DEFAULT NULL COMMENT 'New olcs column for tachograph inspector',
  `psv_discs_to_be_printed_no` smallint(5) unsigned DEFAULT NULL,
  `translate_to_welsh` tinyint(1) NOT NULL DEFAULT '0',
  `is_maintenance_suitable` tinyint(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_licence_lic_no` (`lic_no`),
  UNIQUE KEY `uk_licence_olbs_key` (`olbs_key`),
  KEY `ix_licence_enforcement_area_id` (`enforcement_area_id`),
  KEY `ix_licence_traffic_area_id` (`traffic_area_id`),
  KEY `ix_licence_organisation_id` (`organisation_id`),
  KEY `ix_licence_created_by` (`created_by`),
  KEY `ix_licence_last_modified_by` (`last_modified_by`),
  KEY `ix_licence_goods_or_psv` (`goods_or_psv`),
  KEY `ix_licence_licence_type` (`licence_type`),
  KEY `ix_licence_status` (`status`),
  KEY `ix_licence_tachograph_ins` (`tachograph_ins`),
  KEY `ix_licence_correspondence_cd_id` (`correspondence_cd_id`),
  KEY `ix_licence_establishment_cd_id` (`establishment_cd_id`),
  KEY `ix_licence_transport_consultant_cd_id` (`transport_consultant_cd_id`),
  CONSTRAINT `fk_licence_correspondence_cd_id_contact_details_id` FOREIGN KEY (`correspondence_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_enforcement_area_id_enforcement_area_id` FOREIGN KEY (`enforcement_area_id`) REFERENCES `enforcement_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_establishment_cd_id_contact_details_id` FOREIGN KEY (`establishment_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_licence_type_ref_data_id` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_tachograph_ins_ref_data_id` FOREIGN KEY (`tachograph_ins`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_transport_consultant_cd_id_contact_details_id` FOREIGN KEY (`transport_consultant_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licence to operate goods or psv vehicles';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence`
--

LOCK TABLES `licence` WRITE;
/*!40000 ALTER TABLE `licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence_no_gen`
--

DROP TABLE IF EXISTS `licence_no_gen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence_no_gen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_licence_no_gen_licence_id` (`licence_id`),
  CONSTRAINT `fk_licence_no_gen_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2000000 DEFAULT CHARSET=utf8 COMMENT='Used as a sequence when generating licence numbers.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence_no_gen`
--

LOCK TABLES `licence_no_gen` WRITE;
/*!40000 ALTER TABLE `licence_no_gen` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence_no_gen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence_operating_centre`
--

DROP TABLE IF EXISTS `licence_operating_centre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence_operating_centre` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  `ad_placed` tinyint(1) NOT NULL,
  `ad_placed_in` varchar(70) DEFAULT NULL,
  `ad_placed_date` date DEFAULT NULL,
  `sufficient_parking` tinyint(1) NOT NULL,
  `permission` tinyint(1) NOT NULL,
  `no_of_trailers_required` smallint(5) unsigned DEFAULT NULL,
  `no_of_vehicles_required` smallint(5) unsigned DEFAULT NULL,
  `no_of_vehicles_possessed` smallint(5) unsigned DEFAULT NULL,
  `no_of_trailers_possessed` smallint(5) unsigned DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `is_interim` tinyint(1) DEFAULT NULL,
  `publication_appropriate` tinyint(1) DEFAULT NULL,
  `s4_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_licence_operating_centre_olbs_key` (`olbs_key`),
  KEY `ix_licence_operating_centre_licence_id` (`licence_id`),
  KEY `ix_licence_operating_centre_operating_centre_id` (`operating_centre_id`),
  KEY `ix_licence_operating_centre_created_by` (`created_by`),
  KEY `ix_licence_operating_centre_last_modified_by` (`last_modified_by`),
  KEY `ix_licence_operating_centre_s4_id` (`s4_id`),
  CONSTRAINT `fk_licence_oc_oc_id_oc_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_s4_id_s4_id` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Operating centres on a licence';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence_operating_centre`
--

LOCK TABLES `licence_operating_centre` WRITE;
/*!40000 ALTER TABLE `licence_operating_centre` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence_operating_centre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence_status_rule`
--

DROP TABLE IF EXISTS `licence_status_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence_status_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `licence_status` varchar(32) NOT NULL COMMENT 'The status the licence will inherit on the start date',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `start_processed_date` datetime DEFAULT NULL COMMENT 'Date processed by batch job',
  `end_processed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_licence_status_rule_licence_id` (`licence_id`),
  KEY `ix_licence_status_rule_licence_status` (`licence_status`),
  KEY `ix_licence_status_rule_created_by` (`created_by`),
  KEY `ix_licence_status_rule_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_licence_status_rule_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_licence_status_ref_data_id` FOREIGN KEY (`licence_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence_status_rule`
--

LOCK TABLES `licence_status_rule` WRITE;
/*!40000 ALTER TABLE `licence_status_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence_status_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence_vehicle`
--

DROP TABLE IF EXISTS `licence_vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence_vehicle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `vehicle_id` int(10) unsigned NOT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `interim_application_id` int(10) unsigned DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `removal_date` datetime DEFAULT NULL COMMENT 'Date vehicle removed from licence',
  `removal_letter_seed_date` datetime DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `warning_letter_seed_date` datetime DEFAULT NULL,
  `warning_letter_sent_date` datetime DEFAULT NULL,
  `specified_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_licence_vehicle_olbs_key` (`olbs_key`),
  KEY `ix_licence_vehicle_vehicle_id` (`vehicle_id`),
  KEY `ix_licence_vehicle_created_by` (`created_by`),
  KEY `ix_licence_vehicle_last_modified_by` (`last_modified_by`),
  KEY `ix_licence_vehicle_application_id` (`application_id`),
  KEY `ix_licence_vehicle_interim_application_id` (`interim_application_id`),
  KEY `fk_licence_vehicle_licence_id_licence_id` (`licence_id`),
  CONSTRAINT `fk_licence_vehicle_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_interim_application_id_application_id` FOREIGN KEY (`interim_application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_vehicle_id_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Vehicles on a licence and status, if, for example the vehicle is suspended';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence_vehicle`
--

LOCK TABLES `licence_vehicle` WRITE;
/*!40000 ALTER TABLE `licence_vehicle` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence_vehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licence_vehicle_fee`
--

DROP TABLE IF EXISTS `licence_vehicle_fee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licence_vehicle_fee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_vehicle_id` int(10) unsigned NOT NULL,
  `fee_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_licence_vehicle_fee_olbs_key` (`olbs_key`),
  KEY `ix_licence_vehicle_fee_fee_id` (`fee_id`),
  KEY `ix_licence_vehicle_fee_created_by` (`created_by`),
  KEY `ix_licence_vehicle_fee_last_modified_by` (`last_modified_by`),
  KEY `fk_licence_vehicle_fee_licence_vehicle_id_licence_vehicle_id` (`licence_vehicle_id`),
  CONSTRAINT `fk_licence_vehicle_fee_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_fee_id_fee_id` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_licence_vehicle_id_licence_vehicle_id` FOREIGN KEY (`licence_vehicle_id`) REFERENCES `licence_vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licence_vehicle_fee`
--

LOCK TABLES `licence_vehicle_fee` WRITE;
/*!40000 ALTER TABLE `licence_vehicle_fee` DISABLE KEYS */;
/*!40000 ALTER TABLE `licence_vehicle_fee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `local_authority`
--

DROP TABLE IF EXISTS `local_authority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_authority` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) NOT NULL,
  `email_address` varchar(45) DEFAULT NULL,
  `txc_name` varchar(255) DEFAULT NULL COMMENT 'Authorities name in TransXChange.',
  `naptan_code` char(3) DEFAULT NULL COMMENT 'GB National Public Transport Access Nodes code for authority',
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_local_authority_created_by` (`created_by`),
  KEY `ix_local_authority_last_modified_by` (`last_modified_by`),
  KEY `ix_local_authority_traffic_area_id` (`traffic_area_id`),
  CONSTRAINT `fk_local_authority_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_local_authority_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_local_authority_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='UK local authorities.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `local_authority`
--

LOCK TABLES `local_authority` WRITE;
/*!40000 ALTER TABLE `local_authority` DISABLE KEYS */;
/*!40000 ALTER TABLE `local_authority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `comment` varchar(4000) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '0',
  `note_type` varchar(32) NOT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `irfo_gv_permit_id` int(10) unsigned DEFAULT NULL,
  `irfo_psv_auth_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_note_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_note_application_id` (`application_id`),
  KEY `ix_note_licence_id` (`licence_id`),
  KEY `ix_note_case_id` (`case_id`),
  KEY `ix_note_irfo_gv_permit_id` (`irfo_gv_permit_id`),
  KEY `ix_note_irfo_psv_auth_id` (`irfo_psv_auth_id`),
  KEY `ix_note_created_by` (`created_by`),
  KEY `ix_note_last_modified_by` (`last_modified_by`),
  KEY `ix_note_note_type` (`note_type`),
  KEY `ix_note_bus_reg_id` (`bus_reg_id`),
  KEY `fk_note_transport_manager1_idx` (`transport_manager_id`),
  CONSTRAINT `fk_note_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_irfo_gv_permit_id_irfo_gv_permit_id` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_irfo_psv_auth_id_irfo_psv_auth_id` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_note_type_ref_data_id` FOREIGN KEY (`note_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Notes and their context.  Usage is to for example list all notes linked to a licence or an application.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note`
--

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;
/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oc_complaint`
--

DROP TABLE IF EXISTS `oc_complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oc_complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `complaint_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_oc_complaint_complaint_id` (`complaint_id`),
  KEY `ix_oc_complaint_operating_centre_id` (`operating_centre_id`),
  CONSTRAINT `fk_oc_complaint_complaint_id_complaint_id` FOREIGN KEY (`complaint_id`) REFERENCES `complaint` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_oc_complaint_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A coplaint can be linked to multiple operating centres on a licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oc_complaint`
--

LOCK TABLES `oc_complaint` WRITE;
/*!40000 ALTER TABLE `oc_complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `oc_complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_centre`
--

DROP TABLE IF EXISTS `operating_centre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operating_centre` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `address_id` int(10) unsigned DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_operating_centre_olbs_key` (`olbs_key`),
  KEY `ix_operating_centre_address_id` (`address_id`),
  KEY `ix_operating_centre_created_by` (`created_by`),
  KEY `ix_operating_centre_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_operating_centre_address_id_address_id` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Site from which vehicles operate.  Can only be active on one licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_centre`
--

LOCK TABLES `operating_centre` WRITE;
/*!40000 ALTER TABLE `operating_centre` DISABLE KEYS */;
/*!40000 ALTER TABLE `operating_centre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_centre_opposition`
--

DROP TABLE IF EXISTS `operating_centre_opposition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operating_centre_opposition` (
  `opposition_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  `olbs_oc_id` int(10) unsigned DEFAULT NULL,
  `olbs_opp_id` int(10) unsigned DEFAULT NULL,
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`opposition_id`,`operating_centre_id`),
  UNIQUE KEY `uk_operating_centre_opposition_olbs_oc_id_olbs_opp_id_olbs_type` (`olbs_oc_id`,`olbs_opp_id`,`olbs_type`),
  KEY `ix_operating_centre_opposition_opposition_id` (`opposition_id`),
  KEY `ix_operating_centre_opposition_operating_centre_id` (`operating_centre_id`),
  CONSTRAINT `fk_oc_opposition_oc_id_oc_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_opposition_opposition_id_opposition_id` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_centre_opposition`
--

LOCK TABLES `operating_centre_opposition` WRITE;
/*!40000 ALTER TABLE `operating_centre_opposition` DISABLE KEYS */;
/*!40000 ALTER TABLE `operating_centre_opposition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opposer`
--

DROP TABLE IF EXISTS `opposer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opposer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `contact_details_id` int(10) unsigned NOT NULL,
  `opposer_type` varchar(32) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_opposer_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_opposer_contact_details_id` (`contact_details_id`),
  KEY `ix_opposer_opposer_type` (`opposer_type`),
  KEY `ix_opposer_created_by` (`created_by`),
  KEY `ix_opposer_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_opposer_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_opposer_type_ref_data_id` FOREIGN KEY (`opposer_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opposer`
--

LOCK TABLES `opposer` WRITE;
/*!40000 ALTER TABLE `opposer` DISABLE KEYS */;
/*!40000 ALTER TABLE `opposer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opposition`
--

DROP TABLE IF EXISTS `opposition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opposition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `application_id` int(10) unsigned NOT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `opposer_id` int(10) unsigned NOT NULL,
  `opposition_type` varchar(32) NOT NULL,
  `status` varchar(32) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `is_copied` tinyint(1) NOT NULL DEFAULT '0',
  `raised_date` date DEFAULT NULL,
  `is_in_time` tinyint(1) NOT NULL DEFAULT '0',
  `is_public_inquiry` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `is_withdrawn` tinyint(1) NOT NULL DEFAULT '0',
  `is_valid` varchar(32) NOT NULL COMMENT 'yes, no, undecided',
  `valid_notes` varchar(4000) DEFAULT NULL,
  `is_willing_to_attend_pi` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_olbs_key` (`olbs_key`,`olbs_type`),
  KEY `ix_opposition_application_id` (`application_id`),
  KEY `ix_opposition_opposer_id` (`opposer_id`),
  KEY `ix_opposition_created_by` (`created_by`),
  KEY `ix_opposition_last_modified_by` (`last_modified_by`),
  KEY `ix_opposition_case_id` (`case_id`),
  KEY `ix_opposition_licence_id` (`licence_id`),
  KEY `ix_opposition_opposition_type` (`opposition_type`),
  KEY `ix_opposition_is_valid` (`is_valid`),
  KEY `ix_opposition_status` (`status`),
  CONSTRAINT `fk_opposition_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_is_valid_ref_data_id` FOREIGN KEY (`is_valid`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_opposer_id_opposer_id` FOREIGN KEY (`opposer_id`) REFERENCES `opposer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_opposition_type_ref_data_id` FOREIGN KEY (`opposition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opposition`
--

LOCK TABLES `opposition` WRITE;
/*!40000 ALTER TABLE `opposition` DISABLE KEYS */;
/*!40000 ALTER TABLE `opposition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opposition_grounds`
--

DROP TABLE IF EXISTS `opposition_grounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opposition_grounds` (
  `opposition_id` int(10) unsigned NOT NULL,
  `ground_id` varchar(32) NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`opposition_id`,`ground_id`),
  UNIQUE KEY `uk_opposition_grounds_olbs_key` (`olbs_key`),
  KEY `ix_opposition_grounds_opposition_id` (`opposition_id`),
  KEY `ix_opposition_grounds_grounds` (`ground_id`),
  CONSTRAINT `fk_opposition_grounds_grounds_ref_data_id` FOREIGN KEY (`ground_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_grounds_opposition_id_opposition_id` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opposition_grounds`
--

LOCK TABLES `opposition_grounds` WRITE;
/*!40000 ALTER TABLE `opposition_grounds` DISABLE KEYS */;
/*!40000 ALTER TABLE `opposition_grounds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation`
--

DROP TABLE IF EXISTS `organisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `company_or_llp_no` varchar(20) DEFAULT NULL,
  `name` varchar(160) DEFAULT NULL,
  `irfo_name` varchar(160) DEFAULT NULL COMMENT 'Hold irfo company name separate from normal name.  Dont want changes to one affecting other on licences.',
  `contact_details_id` int(10) unsigned DEFAULT NULL COMMENT 'Registered office details',
  `irfo_contact_details_id` int(10) unsigned DEFAULT NULL COMMENT 'Separate contact details for IRFO info.',
  `type` varchar(32) NOT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `is_mlh` tinyint(1) NOT NULL DEFAULT '0',
  `company_cert_seen` tinyint(1) NOT NULL DEFAULT '0',
  `irfo_nationality` varchar(45) DEFAULT NULL,
  `is_irfo` tinyint(1) NOT NULL DEFAULT '0',
  `allow_email` tinyint(1) NOT NULL DEFAULT '0',
  `lead_tc_area_id` char(1) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_organisation_created_by` (`created_by`),
  KEY `ix_organisation_last_modified_by` (`last_modified_by`),
  KEY `ix_organisation_type` (`type`),
  KEY `ix_organisation_lead_tc_area_id` (`lead_tc_area_id`),
  KEY `ix_organisation_name` (`name`),
  KEY `ix_organisation_contact_details_id` (`contact_details_id`),
  KEY `ix_organisation_irfo_contact_details_id` (`irfo_contact_details_id`),
  CONSTRAINT `fk_organisation_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_irfo_contact_details_id_contact_details_id` FOREIGN KEY (`irfo_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_lead_tc_area_id_traffic_area_id` FOREIGN KEY (`lead_tc_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_type_ref_data_id` FOREIGN KEY (`type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COMMENT='An organisation that has applied for or holds a licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation`
--

LOCK TABLES `organisation` WRITE;
/*!40000 ALTER TABLE `organisation` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation_nature_of_business`
--

DROP TABLE IF EXISTS `organisation_nature_of_business`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation_nature_of_business` (
  `organisation_id` int(10) unsigned NOT NULL,
  `ref_data_id` varchar(32) NOT NULL COMMENT 'Companies House SIC code in ref data',
  PRIMARY KEY (`organisation_id`,`ref_data_id`),
  KEY `ix_organisation_nature_of_business_ref_data_id` (`ref_data_id`),
  KEY `ix_organisation_nature_of_business_organisation_id` (`organisation_id`),
  CONSTRAINT `fk_org_nature_of_business_org_id_org_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_nature_of_business_ref_data_id_ref_data_id` FOREIGN KEY (`ref_data_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores companies house sic codes for an organisation';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation_nature_of_business`
--

LOCK TABLES `organisation_nature_of_business` WRITE;
/*!40000 ALTER TABLE `organisation_nature_of_business` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_nature_of_business` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation_person`
--

DROP TABLE IF EXISTS `organisation_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation_person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `person_id` int(10) unsigned NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `added_date` varchar(45) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_organisation_person_olbs_key` (`olbs_key`),
  KEY `ix_organisation_person_person_id` (`person_id`),
  KEY `ix_organisation_person_organisation_id` (`organisation_id`),
  KEY `ix_organisation_person_created_by` (`created_by`),
  KEY `ix_organisation_person_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_organisation_person_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_person_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_person_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_person_person_id_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Key people in an organisation, eg directors or partners.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation_person`
--

LOCK TABLES `organisation_person` WRITE;
/*!40000 ALTER TABLE `organisation_person` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation_type`
--

DROP TABLE IF EXISTS `organisation_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `org_type_id` varchar(32) NOT NULL COMMENT 'LTD, Partnership etc.',
  `org_person_type_id` varchar(32) NOT NULL COMMENT 'Type if officers in org. Partners, directors etc.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_org_person` (`org_type_id`,`org_person_type_id`),
  KEY `ix_organisation_type_org_type_id` (`org_type_id`),
  KEY `ix_organisation_type_org_person_type_id` (`org_person_type_id`),
  CONSTRAINT `fk_organisation_type_org_person_type_id_ref_data_id` FOREIGN KEY (`org_person_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_type_org_type_id_ref_data_id` FOREIGN KEY (`org_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Organisation meta info.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation_type`
--

LOCK TABLES `organisation_type` WRITE;
/*!40000 ALTER TABLE `organisation_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation_user`
--

DROP TABLE IF EXISTS `organisation_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `organisation_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `is_administrator` tinyint(1) NOT NULL DEFAULT '0',
  `added_date` datetime DEFAULT NULL,
  `removed_date` datetime DEFAULT NULL,
  `sftp_access` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_organisation_user_organisation_id_user_id` (`organisation_id`,`user_id`),
  KEY `ix_organisation_user_user_id` (`user_id`),
  KEY `ix_organisation_user_created_by` (`created_by`),
  KEY `ix_organisation_user_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_organisation_user_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maps organisations a user can access. Not used for DVSA users as they can see all. Transport consultant is an example.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation_user`
--

LOCK TABLES `organisation_user` WRITE;
/*!40000 ALTER TABLE `organisation_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `other_licence`
--

DROP TABLE IF EXISTS `other_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `other_licence` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_licence_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_application_id` int(10) unsigned DEFAULT NULL,
  `role` varchar(32) DEFAULT NULL,
  `lic_no` varchar(18) DEFAULT NULL,
  `holder_name` varchar(90) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `will_surrender` tinyint(1) DEFAULT NULL,
  `disqualification_date` date DEFAULT NULL,
  `disqualification_length` varchar(255) DEFAULT NULL,
  `previous_licence_type` varchar(32) DEFAULT NULL,
  `additional_information` varchar(4000) DEFAULT NULL,
  `operating_centres` varchar(255) DEFAULT NULL,
  `total_auth_vehicles` smallint(5) unsigned DEFAULT NULL,
  `hours_per_week` smallint(5) unsigned DEFAULT NULL COMMENT 'If on transport manager',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_other_licence_application_id` (`application_id`),
  KEY `ix_other_licence_previous_licence_type` (`previous_licence_type`),
  KEY `ix_other_licence_created_by` (`created_by`),
  KEY `ix_other_licence_last_modified_by` (`last_modified_by`),
  KEY `ix_other_licence_transport_manager_id` (`transport_manager_id`),
  KEY `ix_other_licence_transport_manager_application_id` (`transport_manager_application_id`),
  KEY `fk_other_licence_transport_manager_licence1_idx` (`transport_manager_licence_id`),
  KEY `fk_other_licence_ref_data1_idx` (`role`),
  CONSTRAINT `fk_other_licence_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_previous_licence_type_ref_data_id` FOREIGN KEY (`previous_licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_ref_data1` FOREIGN KEY (`role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_tm_application_id_tm_application_id` FOREIGN KEY (`transport_manager_application_id`) REFERENCES `transport_manager_application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_other_licence_transport_manager_licence1` FOREIGN KEY (`transport_manager_licence_id`) REFERENCES `transport_manager_licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Added to store input of other licences section of application.  Currently unused business wise but need to be recorded.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `other_licence`
--

LOCK TABLES `other_licence` WRITE;
/*!40000 ALTER TABLE `other_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `other_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `legacy_status` smallint(5) unsigned DEFAULT NULL COMMENT 'OLBS payment status',
  `legacy_method` smallint(5) unsigned DEFAULT NULL COMMENT 'OLBS payment method',
  `legacy_choice` smallint(5) unsigned DEFAULT NULL,
  `legacy_guid` varchar(255) DEFAULT NULL COMMENT 'OLBS payment reference',
  `status` varchar(32) NOT NULL COMMENT 'Failed, Cancelled, Paid or Legacy. Legacy to allow not null.',
  `completed_date` datetime DEFAULT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_payment_created_by` (`created_by`),
  KEY `ix_payment_last_modified_by` (`last_modified_by`),
  KEY `ix_payment_payment_status` (`status`),
  CONSTRAINT `fk_payment_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_payment_status_ref_data_id` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COMMENT='A payment attempt';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `code` varchar(5) NOT NULL,
  `name` varchar(45) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_permission_created_by` (`created_by`),
  KEY `ix_permission_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_permission_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permission_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `forename` varchar(35) DEFAULT NULL,
  `family_name` varchar(35) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(35) DEFAULT NULL,
  `other_name` varchar(35) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `title_other` varchar(20) DEFAULT NULL COMMENT 'Populated it title is other in dropdown',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_person_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_person_created_by` (`created_by`),
  KEY `ix_person_last_modified_by` (`last_modified_by`),
  KEY `ix_person_family_name` (`family_name`),
  KEY `ix_person_forename` (`forename`),
  CONSTRAINT `fk_person_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Any business significant person in the system.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_contact`
--

DROP TABLE IF EXISTS `phone_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `phone_contact_type` varchar(32) NOT NULL,
  `phone_number` varchar(45) DEFAULT NULL,
  `details` varchar(45) DEFAULT NULL,
  `contact_details_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_phone_contact_olbs_key_olbs_type_phone_contact_type` (`olbs_key`,`olbs_type`,`phone_contact_type`),
  KEY `ix_phone_contact_contact_details_id` (`contact_details_id`),
  KEY `ix_phone_contact_phone_contact_type` (`phone_contact_type`),
  KEY `ix_phone_contact_created_by` (`created_by`),
  KEY `ix_phone_contact_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_phone_contact_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_phone_contact_type_ref_data_id` FOREIGN KEY (`phone_contact_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Phone contact details.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_contact`
--

LOCK TABLES `phone_contact` WRITE;
/*!40000 ALTER TABLE `phone_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi`
--

DROP TABLE IF EXISTS `pi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL COMMENT 'User PI is assigned to.',
  `agreed_by_tc_id` int(10) unsigned DEFAULT NULL COMMENT 'TC who agreed the PI',
  `decided_by_tc_id` int(10) unsigned DEFAULT NULL COMMENT 'TC who presided over PI decision',
  `agreed_by_tc_role` varchar(32) DEFAULT NULL COMMENT 'e.g. Traffic Commissioner or Deputy Traffic Commissioner',
  `decided_by_tc_role` varchar(32) DEFAULT NULL COMMENT 'e.g. Traffic Commissioner or Deputy Traffic Commissioner',
  `agreed_date` date DEFAULT NULL,
  `witnesses` smallint(5) unsigned DEFAULT NULL COMMENT 'Witnesses for the PI decision',
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'PI is cancelled',
  `pi_status` varchar(32) NOT NULL,
  `section_code_text` varchar(1024) DEFAULT NULL COMMENT 'Populated from definitions. Can be edited by user.',
  `decision_date` date DEFAULT NULL,
  `licence_revoked_at_pi` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The licence was revoked',
  `licence_curtailed_at_pi` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The licence was curtailed. e.g. No of vehicles decreased.',
  `licence_suspended_at_pi` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Licence suspended',
  `notification_date` date DEFAULT NULL,
  `decision_notes` text,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `comment` varchar(4000) DEFAULT NULL,
  `call_up_letter_date` date DEFAULT NULL,
  `brief_to_tc_date` date DEFAULT NULL,
  `written_outcome` varchar(32) DEFAULT NULL,
  `written_reason_date` date DEFAULT NULL,
  `decision_letter_sent_date` date DEFAULT NULL,
  `tc_written_reason_date` date DEFAULT NULL,
  `tc_written_decision_date` date DEFAULT NULL,
  `written_reason_letter_date` date DEFAULT NULL,
  `dec_sent_after_written_dec_date` date DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL COMMENT 'Date pi closed.For showing important, open records to user.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pi_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_pi_case_id` (`case_id`),
  KEY `ix_pi_pi_status` (`pi_status`),
  KEY `ix_pi_created_by` (`created_by`),
  KEY `ix_pi_last_modified_by` (`last_modified_by`),
  KEY `ix_pi_assigned_to` (`assigned_to`),
  KEY `ix_pi_agreed_by_tc_id` (`agreed_by_tc_id`),
  KEY `ix_pi_decided_by_tc_id` (`decided_by_tc_id`),
  KEY `ix_pi_agreed_by_tc_role` (`agreed_by_tc_role`),
  KEY `ix_pi_decided_by_tc_role` (`decided_by_tc_role`),
  KEY `ix_pi_written_outcome` (`written_outcome`),
  CONSTRAINT `fk_pi_agreed_by_tc_id_presiding_tc_id` FOREIGN KEY (`agreed_by_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_agreed_by_tc_role_ref_data_id` FOREIGN KEY (`agreed_by_tc_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_assigned_to_user_id` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_decided_by_tc_id_presiding_tc_id` FOREIGN KEY (`decided_by_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_decided_by_tc_role_ref_data_id` FOREIGN KEY (`decided_by_tc_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_pi_status_ref_data_id` FOREIGN KEY (`pi_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_written_outcome_ref_data_id` FOREIGN KEY (`written_outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Public Inquiry.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi`
--

LOCK TABLES `pi` WRITE;
/*!40000 ALTER TABLE `pi` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_decision`
--

DROP TABLE IF EXISTS `pi_decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_decision` (
  `pi_id` int(10) unsigned NOT NULL,
  `decision_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pi_id`,`decision_id`),
  KEY `ix_pi_decision_decision_id` (`decision_id`),
  KEY `ix_pi_decision_pi_id` (`pi_id`),
  CONSTRAINT `fk_pi_decision_decision_id_decision_id` FOREIGN KEY (`decision_id`) REFERENCES `decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_decision_pi_id_pi_id` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_decision`
--

LOCK TABLES `pi_decision` WRITE;
/*!40000 ALTER TABLE `pi_decision` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_decision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_definition`
--

DROP TABLE IF EXISTS `pi_definition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_definition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `pi_definition_category` varchar(32) NOT NULL COMMENT 'Eamples, Good Repute, Withdrawn, Formal Warning',
  `section_code` varchar(20) NOT NULL COMMENT 'Section of related legislation',
  `description` varchar(255) NOT NULL,
  `is_ni` tinyint(1) NOT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_pi_definition_goods_or_psv` (`goods_or_psv`),
  KEY `ix_pi_definition_created_by` (`created_by`),
  KEY `ix_pi_definition_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_pi_definition_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_definition_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_definition_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Definition of a public inquiry. An agenda for the inquiry';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_definition`
--

LOCK TABLES `pi_definition` WRITE;
/*!40000 ALTER TABLE `pi_definition` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_definition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_hearing`
--

DROP TABLE IF EXISTS `pi_hearing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_hearing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `pi_id` int(10) unsigned NOT NULL,
  `hearing_date` datetime DEFAULT NULL,
  `presiding_tc_id` int(10) unsigned DEFAULT NULL,
  `presiding_tc_other` varchar(45) DEFAULT NULL,
  `presided_by_role` varchar(32) DEFAULT NULL,
  `pi_venue_other` varchar(255) DEFAULT NULL,
  `pi_venue_id` int(10) unsigned DEFAULT NULL COMMENT 'The venue at the time of selection is stored in pi_venue_other. If venue data changes, other still stores data at time of selection.',
  `witnesses` smallint(5) unsigned DEFAULT NULL,
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `cancelled_reason` varchar(4000) DEFAULT NULL,
  `cancelled_date` date DEFAULT NULL,
  `is_adjourned` tinyint(1) NOT NULL DEFAULT '0',
  `adjourned_date` date DEFAULT NULL,
  `adjourned_reason` varchar(4000) DEFAULT NULL,
  `details` varchar(4000) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pi_hearing_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_pi_hearing_pi_id` (`pi_id`),
  KEY `ix_pi_hearing_presiding_tc_id` (`presiding_tc_id`),
  KEY `ix_pi_hearing_created_by` (`created_by`),
  KEY `ix_pi_hearing_last_modified_by` (`last_modified_by`),
  KEY `ix_pi_hearing_presided_by_role` (`presided_by_role`),
  KEY `ix_pi_hearing_pi_venue_id` (`pi_venue_id`),
  CONSTRAINT `fk_pi_hearing_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_pi_id_pi_id` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_pi_venue_id_pi_venue_id` FOREIGN KEY (`pi_venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_presided_by_role_ref_data_id` FOREIGN KEY (`presided_by_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_presiding_tc_id_presiding_tc_id` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Details of a public inquiry hearing.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_hearing`
--

LOCK TABLES `pi_hearing` WRITE;
/*!40000 ALTER TABLE `pi_hearing` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_hearing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_reason`
--

DROP TABLE IF EXISTS `pi_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_reason` (
  `pi_id` int(10) unsigned NOT NULL,
  `reason_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pi_id`,`reason_id`),
  KEY `ix_pi_reason_reason_id` (`reason_id`),
  KEY `ix_pi_reason_pi_id` (`pi_id`),
  CONSTRAINT `fk_pi_reason_pi_id_pi_id` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reason_reason_id_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_reason`
--

LOCK TABLES `pi_reason` WRITE;
/*!40000 ALTER TABLE `pi_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_type`
--

DROP TABLE IF EXISTS `pi_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_type` (
  `pi_id` int(10) unsigned NOT NULL,
  `pi_type_id` varchar(32) NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pi_id`,`pi_type_id`),
  UNIQUE KEY `uk_pi_type_pi_id_pi_type_id` (`pi_id`,`pi_type_id`),
  KEY `ix_pi_type_pi_id` (`pi_id`),
  KEY `ix_pi_type_pi_type_id` (`pi_type_id`),
  KEY `ix_pi_type_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  CONSTRAINT `fk_pi_type_pi_id_pi_id` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_type_pi_type_id_ref_data_id` FOREIGN KEY (`pi_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_type`
--

LOCK TABLES `pi_type` WRITE;
/*!40000 ALTER TABLE `pi_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pi_venue`
--

DROP TABLE IF EXISTS `pi_venue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pi_venue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(70) NOT NULL,
  `address_id` int(10) unsigned DEFAULT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pi_venue_olbs_key` (`olbs_key`),
  KEY `ix_pi_venue_address_id` (`address_id`),
  KEY `ix_pi_venue_created_by` (`created_by`),
  KEY `ix_pi_venue_last_modified_by` (`last_modified_by`),
  KEY `ix_pi_venue_traffic_area_id` (`traffic_area_id`),
  CONSTRAINT `fk_pi_venue_address_id_address_id` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Possible venues for a Public Enquiry.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pi_venue`
--

LOCK TABLES `pi_venue` WRITE;
/*!40000 ALTER TABLE `pi_venue` DISABLE KEYS */;
/*!40000 ALTER TABLE `pi_venue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postcode_enforcement_area`
--

DROP TABLE IF EXISTS `postcode_enforcement_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postcode_enforcement_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `enforcement_area_id` varchar(4) NOT NULL,
  `postcode_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_postcode_enforcement_area_enforcement_area_id_postcode_id` (`enforcement_area_id`,`postcode_id`),
  KEY `ix_postcode_enforcement_area_enforcement_area_id` (`enforcement_area_id`),
  KEY `ix_postcode_enforcement_area_created_by` (`created_by`),
  KEY `ix_postcode_enforcement_area_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_postcode_enf_area_enf_area_id_enf_area_id` FOREIGN KEY (`enforcement_area_id`) REFERENCES `enforcement_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_postcode_enforcement_area_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_postcode_enforcement_area_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Postcodes covered by an enforcement area';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postcode_enforcement_area`
--

LOCK TABLES `postcode_enforcement_area` WRITE;
/*!40000 ALTER TABLE `postcode_enforcement_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `postcode_enforcement_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presiding_tc`
--

DROP TABLE IF EXISTS `presiding_tc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presiding_tc` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(70) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Traffic Commissioners who preside over hearings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presiding_tc`
--

LOCK TABLES `presiding_tc` WRITE;
/*!40000 ALTER TABLE `presiding_tc` DISABLE KEYS */;
/*!40000 ALTER TABLE `presiding_tc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `previous_conviction`
--

DROP TABLE IF EXISTS `previous_conviction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `previous_conviction` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `conviction_date` date DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `forename` varchar(35) DEFAULT NULL,
  `family_name` varchar(35) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `category_text` varchar(1024) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `court_fpn` varchar(70) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_previous_conviction_application_id` (`application_id`),
  KEY `ix_previous_conviction_transport_manager_id` (`transport_manager_id`),
  CONSTRAINT `fk_previous_conviction_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_previous_conviction_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores data entered in application of previous convictions.  When the app is granted this may be used to populate the conviction table.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_conviction`
--

LOCK TABLES `previous_conviction` WRITE;
/*!40000 ALTER TABLE `previous_conviction` DISABLE KEYS */;
/*!40000 ALTER TABLE `previous_conviction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_queue`
--

DROP TABLE IF EXISTS `print_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `team_printer_id` int(10) unsigned NOT NULL,
  `document_id` int(10) unsigned NOT NULL,
  `added_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_print_queue_team_printer_id` (`team_printer_id`),
  KEY `ix_print_queue_document_id` (`document_id`),
  CONSTRAINT `fk_print_queue_document_id_document_id` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_print_queue_team_printer_id_team_printer_id` FOREIGN KEY (`team_printer_id`) REFERENCES `team_printer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Queues print jobs.  Not confirmed this will be used yet.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_queue`
--

LOCK TABLES `print_queue` WRITE;
/*!40000 ALTER TABLE `print_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printer`
--

DROP TABLE IF EXISTS `printer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `printer_tray` varchar(45) DEFAULT NULL,
  `printer_name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Details of printers with specific OLCS roles, such as the printer for vehicle discs.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printer`
--

LOCK TABLES `printer` WRITE;
/*!40000 ALTER TABLE `printer` DISABLE KEYS */;
/*!40000 ALTER TABLE `printer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_hire_licence`
--

DROP TABLE IF EXISTS `private_hire_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_hire_licence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `private_hire_licence_no` varchar(10) NOT NULL,
  `contact_details_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_private_hire_licence_olbs_key` (`olbs_key`),
  KEY `ix_private_hire_licence_licence_id` (`licence_id`),
  KEY `ix_private_hire_licence_contact_details_id` (`contact_details_id`),
  KEY `ix_private_hire_licence_created_by` (`created_by`),
  KEY `ix_private_hire_licence_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_private_hire_licence_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_private_hire_licence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_private_hire_licence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_private_hire_licence_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Details of private hire vehicles linked to a licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_hire_licence`
--

LOCK TABLES `private_hire_licence` WRITE;
/*!40000 ALTER TABLE `private_hire_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_hire_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prohibition`
--

DROP TABLE IF EXISTS `prohibition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prohibition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `prohibition_date` date NOT NULL,
  `cleared_date` date DEFAULT NULL,
  `is_trailer` tinyint(1) NOT NULL DEFAULT '0',
  `prohibition_type` varchar(32) NOT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `imposed_at` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_prohibition_olbs_key` (`olbs_key`),
  KEY `ix_prohibition_case_id` (`case_id`),
  KEY `ix_prohibition_created_by` (`created_by`),
  KEY `ix_prohibition_last_modified_by` (`last_modified_by`),
  KEY `ix_prohibition_prohibition_type` (`prohibition_type`),
  CONSTRAINT `fk_prohibition_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_prohibition_type_ref_data_id` FOREIGN KEY (`prohibition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Prohibition applied to a licence as a result of a case.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prohibition`
--

LOCK TABLES `prohibition` WRITE;
/*!40000 ALTER TABLE `prohibition` DISABLE KEYS */;
/*!40000 ALTER TABLE `prohibition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prohibition_defect`
--

DROP TABLE IF EXISTS `prohibition_defect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prohibition_defect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `prohibition_id` int(10) unsigned NOT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `defect_type` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_prohibition_defect_olbs_key` (`olbs_key`),
  KEY `ix_prohibition_defect_prohibition_id` (`prohibition_id`),
  KEY `ix_prohibition_defect_created_by` (`created_by`),
  KEY `ix_prohibition_defect_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_prohibition_defect_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_defect_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_defect_prohibition_id_prohibition_id` FOREIGN KEY (`prohibition_id`) REFERENCES `prohibition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prohibition_defect`
--

LOCK TABLES `prohibition_defect` WRITE;
/*!40000 ALTER TABLE `prohibition_defect` DISABLE KEYS */;
/*!40000 ALTER TABLE `prohibition_defect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propose_to_revoke`
--

DROP TABLE IF EXISTS `propose_to_revoke`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propose_to_revoke` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `presiding_tc_id` int(10) unsigned NOT NULL,
  `ptr_agreed_date` datetime DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `comment` varchar(4000) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_propose_to_revoke_case_id` (`case_id`),
  KEY `ix_propose_to_revoke_presiding_tc_id` (`presiding_tc_id`),
  KEY `ix_propose_to_revoke_created_by` (`created_by`),
  KEY `ix_propose_to_revoke_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_propose_to_revoke_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_presiding_tc_id_presiding_tc_id` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Controls the process to revoke a licence and keeps track of SLAs.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propose_to_revoke`
--

LOCK TABLES `propose_to_revoke` WRITE;
/*!40000 ALTER TABLE `propose_to_revoke` DISABLE KEYS */;
/*!40000 ALTER TABLE `propose_to_revoke` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `psv_disc`
--

DROP TABLE IF EXISTS `psv_disc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `psv_disc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `disc_no` varchar(50) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `ceased_date` datetime DEFAULT NULL,
  `is_copy` tinyint(1) NOT NULL DEFAULT '0',
  `reprint_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_printing` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_psv_disc_olbs_key` (`olbs_key`),
  KEY `ix_psv_disc_licence_id` (`licence_id`),
  KEY `ix_psv_disc_created_by` (`created_by`),
  KEY `ix_psv_disc_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_psv_disc_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_psv_disc_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_psv_disc_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PSV disc details. Unlike goods, not linked to a specific vehicle.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `psv_disc`
--

LOCK TABLES `psv_disc` WRITE;
/*!40000 ALTER TABLE `psv_disc` DISABLE KEYS */;
/*!40000 ALTER TABLE `psv_disc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ptr_reason`
--

DROP TABLE IF EXISTS `ptr_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ptr_reason` (
  `propose_to_revoke_id` int(10) unsigned NOT NULL,
  `reason_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`propose_to_revoke_id`,`reason_id`),
  KEY `ix_ptr_reason_reason_id` (`reason_id`),
  KEY `ix_ptr_reason_propose_to_revoke_id` (`propose_to_revoke_id`),
  CONSTRAINT `fk_ptr_reason_propose_to_revoke_id_propose_to_revoke_id` FOREIGN KEY (`propose_to_revoke_id`) REFERENCES `propose_to_revoke` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ptr_reason_reason_id_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reasons for a Propose To Revoke process being started.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ptr_reason`
--

LOCK TABLES `ptr_reason` WRITE;
/*!40000 ALTER TABLE `ptr_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `ptr_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `public_holiday`
--

DROP TABLE IF EXISTS `public_holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_holiday` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `public_holiday_date` date NOT NULL,
  `is_england` tinyint(1) DEFAULT NULL,
  `is_wales` tinyint(1) DEFAULT NULL,
  `is_scotland` tinyint(1) DEFAULT NULL,
  `is_ni` tinyint(1) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_public_holiday_created_by` (`created_by`),
  KEY `ix_public_holiday_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_public_holiday_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_public_holiday_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `public_holiday`
--

LOCK TABLES `public_holiday` WRITE;
/*!40000 ALTER TABLE `public_holiday` DISABLE KEYS */;
/*!40000 ALTER TABLE `public_holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication`
--

DROP TABLE IF EXISTS `publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `publication_no` smallint(5) unsigned NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  `document_id` int(10) unsigned DEFAULT NULL,
  `doc_template_id` int(10) unsigned DEFAULT NULL,
  `pub_status` varchar(32) NOT NULL,
  `pub_type` varchar(3) NOT NULL COMMENT 'Either A&D or N&P',
  `pub_date` date DEFAULT NULL,
  `doc_name` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_publication_traffic_area_id` (`traffic_area_id`),
  KEY `ix_publication_pub_status` (`pub_status`),
  KEY `ix_publication_created_by` (`created_by`),
  KEY `ix_publication_last_modified_by` (`last_modified_by`),
  KEY `fk_publication_document1_idx` (`document_id`),
  KEY `fk_publication_doc_template1_idx` (`doc_template_id`),
  CONSTRAINT `fk_publication_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_doc_template1` FOREIGN KEY (`doc_template_id`) REFERENCES `doc_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_pub_status_ref_data_id` FOREIGN KEY (`pub_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Official Publication to be published on national register containing operator/tm changes etc..';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication`
--

LOCK TABLES `publication` WRITE;
/*!40000 ALTER TABLE `publication` DISABLE KEYS */;
/*!40000 ALTER TABLE `publication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication_link`
--

DROP TABLE IF EXISTS `publication_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `publication_id` int(10) unsigned NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `pi_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `text1` text,
  `text2` text,
  `text3` text,
  `publication_section_id` int(10) unsigned NOT NULL,
  `orig_pub_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(32) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_publication_link_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_publication_link_licence_id` (`licence_id`),
  KEY `ix_publication_link_publication_id` (`publication_id`),
  KEY `ix_publication_link_pi_id` (`pi_id`),
  KEY `ix_publication_link_traffic_area_id` (`traffic_area_id`),
  KEY `ix_publication_link_application_id` (`application_id`),
  KEY `ix_publication_link_bus_reg_id` (`bus_reg_id`),
  KEY `ix_publication_link_publication_section_id` (`publication_section_id`),
  KEY `ix_publication_link_created_by` (`created_by`),
  KEY `ix_publication_link_last_modified_by` (`last_modified_by`),
  KEY `fk_publication_link_transport_manager1_idx` (`transport_manager_id`),
  CONSTRAINT `fk_publication_link_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_pi_id_pi_id` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_publication_id_publication_id` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_link_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_lnk_publication_section_id_publication_section_id` FOREIGN KEY (`publication_section_id`) REFERENCES `publication_section` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link between publication and its sections and licences it refers to etc..';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication_link`
--

LOCK TABLES `publication_link` WRITE;
/*!40000 ALTER TABLE `publication_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `publication_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication_police_data`
--

DROP TABLE IF EXISTS `publication_police_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication_police_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `publication_link_id` int(10) unsigned NOT NULL,
  `forename` varchar(35) DEFAULT NULL,
  `family_name` varchar(35) DEFAULT NULL,
  `birth_date` date DEFAULT NULL COMMENT 'If null, police report will replace with not given.',
  `olbs_dob` varchar(20) DEFAULT NULL COMMENT 'Legacy DOB. Was stred as varchar and format was not consistand',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_publication_police_data_olbs_key` (`olbs_key`) COMMENT 'Only required when etl active',
  KEY `ix_publication_police_data_publication_link_id` (`publication_link_id`),
  KEY `ix_publication_police_data_created_by` (`created_by`),
  KEY `ix_publication_police_data_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_publication_police_data_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_police_data_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_police_data_publication_lnk_id_publication_lnk_id` FOREIGN KEY (`publication_link_id`) REFERENCES `publication_link` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Police recipients of a publication get extra data. Currently this is the date of birth of people in the publication.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication_police_data`
--

LOCK TABLES `publication_police_data` WRITE;
/*!40000 ALTER TABLE `publication_police_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `publication_police_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication_section`
--

DROP TABLE IF EXISTS `publication_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(70) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_publication_section_created_by` (`created_by`),
  KEY `ix_publication_section_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_publication_section_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_section_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Publication sections, for example New Applications, Surrendered Licences';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication_section`
--

LOCK TABLES `publication_section` WRITE;
/*!40000 ALTER TABLE `publication_section` DISABLE KEYS */;
/*!40000 ALTER TABLE `publication_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reason`
--

DROP TABLE IF EXISTS `reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reason` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL COMMENT 'Northern Ireland or not',
  `is_propose_to_revoke` tinyint(1) NOT NULL COMMENT 'Used in Propose to Revoke',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_reason_created_by` (`created_by`),
  KEY `ix_reason_last_modified_by` (`last_modified_by`),
  KEY `ix_reason_goods_or_psv` (`goods_or_psv`),
  CONSTRAINT `fk_reason_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reason_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reason_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reasons for a PI or other compliance related process.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reason`
--

LOCK TABLES `reason` WRITE;
/*!40000 ALTER TABLE `reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipient`
--

DROP TABLE IF EXISTS `recipient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `send_app_decision` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Recipient registered for AD, Applications and Decisions, publications',
  `send_notices_procs` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Recipient registered for NP, Notices and Procedures, publications',
  `is_police` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Recipient receives extra sensitive info. DOBs for people in publication.',
  `is_objector` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is objector or representor',
  `contact_name` varchar(100) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_recipient_olbs_key` (`olbs_key`),
  KEY `ix_recipient_created_by` (`created_by`),
  KEY `ix_recipient_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_recipient_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_recipient_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recipient of a publication. Whenever a publication is published will be emailed to recipients';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipient`
--

LOCK TABLES `recipient` WRITE;
/*!40000 ALTER TABLE `recipient` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipient_traffic_area`
--

DROP TABLE IF EXISTS `recipient_traffic_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipient_traffic_area` (
  `recipient_id` int(10) unsigned NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  PRIMARY KEY (`recipient_id`,`traffic_area_id`),
  KEY `ix_recipient_traffic_area_traffic_area_id` (`traffic_area_id`),
  KEY `ix_recipient_traffic_area_recipient_id` (`recipient_id`),
  CONSTRAINT `fk_recipient_traffic_area_recipient_id_recipient_id` FOREIGN KEY (`recipient_id`) REFERENCES `recipient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_recipient_traffic_area_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipient_traffic_area`
--

LOCK TABLES `recipient_traffic_area` WRITE;
/*!40000 ALTER TABLE `recipient_traffic_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipient_traffic_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_data`
--

DROP TABLE IF EXISTS `ref_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_data` (
  `id` varchar(32) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(512) DEFAULT NULL,
  `ref_data_category_id` varchar(32) NOT NULL,
  `olbs_key` varchar(20) DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `parent_id` varchar(32) DEFAULT NULL,
  `display_order` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_ref_data_parent_id` (`parent_id`),
  KEY `ix_ref_data_ref_data_category_id` (`ref_data_category_id`),
  CONSTRAINT `fk_ref_data_parent_id_ref_data_id` FOREIGN KEY (`parent_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds ref data if key value pairs is adequate. If other columns needed a separate table is used.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_data`
--

LOCK TABLES `ref_data` WRITE;
/*!40000 ALTER TABLE `ref_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `code` varchar(5) NOT NULL,
  `role` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_role_created_by` (`created_by`),
  KEY `ix_role_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_role_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_role_permission_permission_id` (`permission_id`),
  KEY `ix_role_permission_role_id` (`role_id`),
  KEY `ix_role_permission_created_by` (`created_by`),
  KEY `ix_role_permission_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_role_permission_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_permission_id_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_role_id_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s4`
--

DROP TABLE IF EXISTS `s4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s4` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned NOT NULL,
  `agreed_date` datetime DEFAULT NULL,
  `received_date` datetime NOT NULL,
  `outcome` varchar(20) DEFAULT NULL,
  `surrender_licence` tinyint(1) NOT NULL DEFAULT '0',
  `is_true_s4` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_s4_application_id` (`application_id`),
  KEY `ix_s4_licence_id` (`licence_id`),
  KEY `ix_s4_created_by` (`created_by`),
  KEY `ix_s4_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_s4_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Process of moving an operating centre from one licence to another.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s4`
--

LOCK TABLES `s4` WRITE;
/*!40000 ALTER TABLE `s4` DISABLE KEYS */;
/*!40000 ALTER TABLE `s4` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s4_condition`
--

DROP TABLE IF EXISTS `s4_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s4_condition` (
  `condition_id` int(10) unsigned NOT NULL,
  `s4_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`condition_id`,`s4_id`),
  KEY `ix_s4_condition_condition_id` (`condition_id`),
  KEY `ix_s4_condition_s4_id` (`s4_id`),
  CONSTRAINT `fk_s4_condition_condition_id_condition_undertaking_id` FOREIGN KEY (`condition_id`) REFERENCES `condition_undertaking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_condition_s4_id_s4_id` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A condition to be moved from one licence to another as part of an S4.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s4_condition`
--

LOCK TABLES `s4_condition` WRITE;
/*!40000 ALTER TABLE `s4_condition` DISABLE KEYS */;
/*!40000 ALTER TABLE `s4_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scan`
--

DROP TABLE IF EXISTS `scan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `application_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `case_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `irfo_organisation_id` int(10) unsigned DEFAULT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_scan_application_id` (`application_id`),
  KEY `ix_scan_bus_reg_id` (`bus_reg_id`),
  KEY `ix_scan_licence_id` (`licence_id`),
  KEY `ix_scan_case_id` (`case_id`),
  KEY `ix_scan_transport_manager_id` (`transport_manager_id`),
  KEY `ix_scan_sub_category_id` (`sub_category_id`),
  KEY `ix_scan_created_by` (`created_by`),
  KEY `ix_scan_last_modified_by` (`last_modified_by`),
  KEY `ix_scan_category_id` (`category_id`),
  KEY `ix_scan_irfo_organisation_id` (`irfo_organisation_id`),
  CONSTRAINT `fk_scan_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_irfo_organisation_id_organisation_id` FOREIGN KEY (`irfo_organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_sub_category_id_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User entered scan details. Used to create document and task after scan is uploaded via cofax application.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scan`
--

LOCK TABLES `scan` WRITE;
/*!40000 ALTER TABLE `scan` DISABLE KEYS */;
/*!40000 ALTER TABLE `scan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serious_infringement`
--

DROP TABLE IF EXISTS `serious_infringement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serious_infringement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `check_date` date DEFAULT NULL,
  `erru_response_sent` tinyint(1) NOT NULL DEFAULT '0',
  `erru_response_user_id` int(10) unsigned DEFAULT NULL,
  `erru_response_time` datetime DEFAULT NULL,
  `infringement_date` date DEFAULT NULL,
  `member_state_code` varchar(2) DEFAULT NULL,
  `notification_number` varchar(36) DEFAULT NULL COMMENT 'ERRU guid',
  `reason` varchar(500) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `si_category_id` varchar(8) NOT NULL,
  `si_category_type_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  `olbs_type` varchar(50) DEFAULT NULL COMMENT 'used to differntiate source of data during ETL when one OLCS table relates to many OLBS. Can be dropped when fully live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_serious_infringement_olbs_key_olbs_type` (`olbs_key`,`olbs_type`),
  KEY `ix_serious_infringement_case_id` (`case_id`),
  KEY `ix_serious_infringement_erru_response_user_id` (`erru_response_user_id`),
  KEY `ix_serious_infringement_member_state_code` (`member_state_code`),
  KEY `ix_serious_infringement_si_category_id` (`si_category_id`),
  KEY `ix_serious_infringement_si_category_type_id` (`si_category_type_id`),
  KEY `ix_serious_infringement_created_by` (`created_by`),
  KEY `ix_serious_infringement_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_serious_infringement_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_erru_response_user_id_user_id` FOREIGN KEY (`erru_response_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_member_state_code_country_id` FOREIGN KEY (`member_state_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_si_category_id_si_category_id` FOREIGN KEY (`si_category_id`) REFERENCES `si_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_si_category_type_id_si_category_type_id` FOREIGN KEY (`si_category_type_id`) REFERENCES `si_category_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `serious_infringement`
--

LOCK TABLES `serious_infringement` WRITE;
/*!40000 ALTER TABLE `serious_infringement` DISABLE KEYS */;
/*!40000 ALTER TABLE `serious_infringement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_category`
--

DROP TABLE IF EXISTS `si_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_category` (
  `id` varchar(8) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_si_category_created_by` (`created_by`),
  KEY `ix_si_category_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_category_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category of Serious Infringement.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_category`
--

LOCK TABLES `si_category` WRITE;
/*!40000 ALTER TABLE `si_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_category_type`
--

DROP TABLE IF EXISTS `si_category_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_category_type` (
  `id` varchar(8) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `si_category_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_si_category_type_si_category_id` (`si_category_id`),
  KEY `ix_si_category_type_created_by` (`created_by`),
  KEY `ix_si_category_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_category_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_type_si_category_id_si_category_id` FOREIGN KEY (`si_category_id`) REFERENCES `si_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_category_type`
--

LOCK TABLES `si_category_type` WRITE;
/*!40000 ALTER TABLE `si_category_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_category_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty`
--

DROP TABLE IF EXISTS `si_penalty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `serious_infringement_id` int(10) unsigned NOT NULL,
  `imposed` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason_not_imposed` varchar(500) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `si_penalty_type_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_serious_infringement_id` (`serious_infringement_id`),
  KEY `ix_si_penalty_si_penalty_type_id` (`si_penalty_type_id`),
  KEY `ix_si_penalty_created_by` (`created_by`),
  KEY `ix_si_penalty_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_serious_infringement_id_serious_infringement_id` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_si_penalty_type_id_si_penalty_type_id` FOREIGN KEY (`si_penalty_type_id`) REFERENCES `si_penalty_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty`
--

LOCK TABLES `si_penalty` WRITE;
/*!40000 ALTER TABLE `si_penalty` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty_erru_imposed`
--

DROP TABLE IF EXISTS `si_penalty_erru_imposed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty_erru_imposed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `final_decision_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `executed` tinyint(1) DEFAULT NULL,
  `serious_infringement_id` int(10) unsigned NOT NULL,
  `si_penalty_imposed_type_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_erru_imposed_created_by` (`created_by`),
  KEY `ix_si_penalty_erru_imposed_last_modified_by` (`last_modified_by`),
  KEY `ix_si_penalty_erru_imposed_serious_infringement_id` (`serious_infringement_id`),
  KEY `ix_si_penalty_erru_imposed_si_penalty_imposed_type_id` (`si_penalty_imposed_type_id`),
  CONSTRAINT `fk_si_pen_erru_impsd_si_pen_impsd_type_id_si_pen_impsd_type_id` FOREIGN KEY (`si_penalty_imposed_type_id`) REFERENCES `si_penalty_imposed_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_imposed_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_imposed_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_imposed_si_id_si_id` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty_erru_imposed`
--

LOCK TABLES `si_penalty_erru_imposed` WRITE;
/*!40000 ALTER TABLE `si_penalty_erru_imposed` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty_erru_imposed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty_erru_requested`
--

DROP TABLE IF EXISTS `si_penalty_erru_requested`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty_erru_requested` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `duration` smallint(5) unsigned DEFAULT NULL COMMENT 'Number of months.',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `serious_infringement_id` int(10) unsigned NOT NULL,
  `si_penalty_requested_type_id` varchar(8) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_erru_requested_serious_infringement_id` (`serious_infringement_id`),
  KEY `ix_si_penalty_erru_requested_si_penalty_requested_type_id` (`si_penalty_requested_type_id`),
  KEY `ix_si_penalty_erru_requested_created_by` (`created_by`),
  KEY `ix_si_penalty_erru_requested_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_pen_erru_req_si_pen_req_type_id_si_pen_req_type_id` FOREIGN KEY (`si_penalty_requested_type_id`) REFERENCES `si_penalty_requested_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_si_id_si_id` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty_erru_requested`
--

LOCK TABLES `si_penalty_erru_requested` WRITE;
/*!40000 ALTER TABLE `si_penalty_erru_requested` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty_erru_requested` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty_imposed_type`
--

DROP TABLE IF EXISTS `si_penalty_imposed_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty_imposed_type` (
  `id` varchar(8) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` date DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_imposed_type_created_by` (`created_by`),
  KEY `ix_si_penalty_imposed_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_imposed_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_imposed_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty_imposed_type`
--

LOCK TABLES `si_penalty_imposed_type` WRITE;
/*!40000 ALTER TABLE `si_penalty_imposed_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty_imposed_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty_requested_type`
--

DROP TABLE IF EXISTS `si_penalty_requested_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty_requested_type` (
  `id` varchar(8) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_requested_type_created_by` (`created_by`),
  KEY `ix_si_penalty_requested_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_requested_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_requested_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty_requested_type`
--

LOCK TABLES `si_penalty_requested_type` WRITE;
/*!40000 ALTER TABLE `si_penalty_requested_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty_requested_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_penalty_type`
--

DROP TABLE IF EXISTS `si_penalty_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_penalty_type` (
  `id` varchar(8) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_si_penalty_type_created_by` (`created_by`),
  KEY `ix_si_penalty_type_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_type_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_type_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_penalty_type`
--

LOCK TABLES `si_penalty_type` WRITE;
/*!40000 ALTER TABLE `si_penalty_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_penalty_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla`
--

DROP TABLE IF EXISTS `sla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `category` varchar(32) DEFAULT NULL COMMENT 'e.g. PI',
  `field` varchar(32) DEFAULT NULL COMMENT 'Field holding source of sla',
  `compare_to` varchar(32) DEFAULT NULL COMMENT 'Field holding result',
  `days` smallint(6) DEFAULT NULL COMMENT 'Number of days between source and result for succes. Can be negative',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `public_holiday` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Include public holidays in SLA calculation',
  `weekend` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Include weekends in SLA calculation',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Service level agreements. Number of days from one value to another defines if sla met.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla`
--

LOCK TABLES `sla` WRITE;
/*!40000 ALTER TABLE `sla` DISABLE KEYS */;
/*!40000 ALTER TABLE `sla` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statement`
--

DROP TABLE IF EXISTS `statement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `statement_type` varchar(32) NOT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `stopped_date` datetime DEFAULT NULL,
  `requested_date` datetime DEFAULT NULL,
  `authorisers_decision` varchar(4000) DEFAULT NULL,
  `contact_type` varchar(32) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `licence_no` varchar(20) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL,
  `requestors_body` varchar(40) DEFAULT NULL,
  `requestors_contact_details_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_statement_olbs_key` (`olbs_key`),
  KEY `ix_statement_case_id` (`case_id`),
  KEY `ix_statement_created_by` (`created_by`),
  KEY `ix_statement_last_modified_by` (`last_modified_by`),
  KEY `ix_statement_contact_type` (`contact_type`),
  KEY `ix_statement_statement_type` (`statement_type`),
  KEY `fk_statement_contact_details1_idx` (`requestors_contact_details_id`),
  CONSTRAINT `fk_statement_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_contact_details1` FOREIGN KEY (`requestors_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_contact_type_ref_data_id` FOREIGN KEY (`contact_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_statement_type_ref_data_id` FOREIGN KEY (`statement_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Statement 9 or 43 details.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statement`
--

LOCK TABLES `statement` WRITE;
/*!40000 ALTER TABLE `statement` DISABLE KEYS */;
/*!40000 ALTER TABLE `statement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stay`
--

DROP TABLE IF EXISTS `stay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `request_date` datetime DEFAULT NULL,
  `withdrawn_date` datetime DEFAULT NULL,
  `decision_date` datetime DEFAULT NULL,
  `outcome` varchar(32) DEFAULT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  `stay_type` varchar(32) NOT NULL COMMENT 'TC or UT',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_stay_case_id` (`case_id`),
  KEY `ix_stay_created_by` (`created_by`),
  KEY `ix_stay_last_modified_by` (`last_modified_by`),
  KEY `ix_stay_outcome` (`outcome`),
  KEY `ix_stay_stay_type` (`stay_type`),
  CONSTRAINT `fk_stay_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_outcome_ref_data_id` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_stay_type_ref_data_id` FOREIGN KEY (`stay_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A stay of decision on a Case. One per case.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stay`
--

LOCK TABLES `stay` WRITE;
/*!40000 ALTER TABLE `stay` DISABLE KEYS */;
/*!40000 ALTER TABLE `stay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_category`
--

DROP TABLE IF EXISTS `sub_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_category` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_name` varchar(64) NOT NULL,
  `is_scan` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Category used for scanning documents',
  `is_doc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is a valid document category',
  `is_task` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is a valid task category',
  `is_free_text` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User can enter freetext description - applied to task etc when creating.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_sub_category_category_id` (`category_id`),
  KEY `ix_sub_category_created_by` (`created_by`),
  KEY `ix_sub_category_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_sub_category_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_category_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_category_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used to categorise documents, tasks and scans.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_category`
--

LOCK TABLES `sub_category` WRITE;
/*!40000 ALTER TABLE `sub_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_category_description`
--

DROP TABLE IF EXISTS `sub_category_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_category_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `sub_category_id` int(10) unsigned NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sub_category_description` (`sub_category_id`,`description`),
  KEY `ix_sub_category_description_sub_category_id` (`sub_category_id`),
  CONSTRAINT `fk_sub_category_description_sub_category_id_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Possible values to be used in task or document description field for the sub category.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_category_description`
--

LOCK TABLES `sub_category_description` WRITE;
/*!40000 ALTER TABLE `sub_category_description` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_category_description` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission`
--

DROP TABLE IF EXISTS `submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `submission_type` varchar(32) NOT NULL,
  `data_snapshot` text COMMENT 'Contains data for each submission section concatenated togather as a JSon string.',
  `closed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_submission_case_id` (`case_id`),
  KEY `ix_submission_created_by` (`created_by`),
  KEY `ix_submission_last_modified_by` (`last_modified_by`),
  KEY `ix_submission_submission_type` (`submission_type`),
  CONSTRAINT `fk_submission_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_submission_type_ref_data_id` FOREIGN KEY (`submission_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Submission of a case or application.  Contains a summary snapshot of all related info.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission`
--

LOCK TABLES `submission` WRITE;
/*!40000 ALTER TABLE `submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_action`
--

DROP TABLE IF EXISTS `submission_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `is_decision` tinyint(1) NOT NULL,
  `comment` text,
  `urgent` tinyint(1) DEFAULT NULL,
  `submission_id` int(10) unsigned NOT NULL,
  `sender_user_id` int(10) unsigned NOT NULL,
  `recipient_user_id` int(10) unsigned NOT NULL,
  `submission_action_status` varchar(32) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_submission_action_sender_user_id` (`sender_user_id`),
  KEY `ix_submission_action_recipient_user_id` (`recipient_user_id`),
  KEY `ix_submission_action_created_by` (`created_by`),
  KEY `ix_submission_action_last_modified_by` (`last_modified_by`),
  KEY `ix_submission_action_submission_id` (`submission_id`),
  KEY `ix_submission_action_submission_action_status` (`submission_action_status`),
  CONSTRAINT `fk_submission_action_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_recipient_user_id_user_id` FOREIGN KEY (`recipient_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_sender_user_id_user_id` FOREIGN KEY (`sender_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_submission_action_status_ref_data_id` FOREIGN KEY (`submission_action_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_submission_id_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `submission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Action to be taken regarding a submission.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_action`
--

LOCK TABLES `submission_action` WRITE;
/*!40000 ALTER TABLE `submission_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `submission_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_action_reason`
--

DROP TABLE IF EXISTS `submission_action_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission_action_reason` (
  `submission_action_id` int(10) unsigned NOT NULL,
  `reason_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`submission_action_id`,`reason_id`),
  KEY `ix_submission_action_reason_reason_id` (`reason_id`),
  KEY `ix_submission_action_reason_submission_action_id` (`submission_action_id`),
  CONSTRAINT `fk_submission_action_reason_reason_id_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submissn_action_reasn_submissn_action_id_submissn_action_id` FOREIGN KEY (`submission_action_id`) REFERENCES `submission_action` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_action_reason`
--

LOCK TABLES `submission_action_reason` WRITE;
/*!40000 ALTER TABLE `submission_action_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `submission_action_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_section_comment`
--

DROP TABLE IF EXISTS `submission_section_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission_section_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `comment` text,
  `submission_id` int(10) unsigned NOT NULL,
  `submission_section` varchar(32) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_submission_section_comment_submission_id` (`submission_id`),
  KEY `ix_submission_section_comment_submission_section` (`submission_section`),
  KEY `ix_submission_section_comment_created_by` (`created_by`),
  KEY `ix_submission_section_comment_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_submission_section_comment_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comment_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comment_submission_id_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `submission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comment_submission_section_ref_data_id` FOREIGN KEY (`submission_section`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comments added to a section of a submission.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_section_comment`
--

LOCK TABLES `submission_section_comment` WRITE;
/*!40000 ALTER TABLE `submission_section_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `submission_section_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_info_message`
--

DROP TABLE IF EXISTS `system_info_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_info_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `is_internal` tinyint(1) NOT NULL,
  `activate_date` date NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(1024) NOT NULL,
  `importance` tinyint(3) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_system_info_message_created_by` (`created_by`),
  KEY `ix_system_info_message_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_system_info_message_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_info_message_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_info_message`
--

LOCK TABLES `system_info_message` WRITE;
/*!40000 ALTER TABLE `system_info_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_info_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_parameter`
--

DROP TABLE IF EXISTS `system_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_parameter` (
  `id` varchar(32) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `param_value` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Key value system parameters.  To allow changes without app redeploy.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_parameter`
--

LOCK TABLES `system_parameter` WRITE;
/*!40000 ALTER TABLE `system_parameter` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `assigned_to_user_id` int(10) unsigned DEFAULT NULL,
  `assigned_to_team_id` int(10) unsigned DEFAULT NULL,
  `assigned_by_user_id` int(10) unsigned DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `case_id` int(10) unsigned DEFAULT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `bus_reg_id` int(10) unsigned DEFAULT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `irfo_organisation_id` int(10) unsigned DEFAULT NULL,
  `urgent` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_task_olbs_key` (`olbs_key`),
  KEY `ix_task_assigned_to_user_id` (`assigned_to_user_id`),
  KEY `ix_task_assigned_to_team_id` (`assigned_to_team_id`),
  KEY `ix_task_assigned_by_user_id` (`assigned_by_user_id`),
  KEY `ix_task_licence_id` (`licence_id`),
  KEY `ix_task_application_id` (`application_id`),
  KEY `ix_task_bus_reg_id` (`bus_reg_id`),
  KEY `ix_task_transport_manager_id` (`transport_manager_id`),
  KEY `ix_task_irfo_organisation_id` (`irfo_organisation_id`),
  KEY `ix_task_created_by` (`created_by`),
  KEY `ix_task_last_modified_by` (`last_modified_by`),
  KEY `ix_task_category_id` (`category_id`),
  KEY `ix_task_case_id` (`case_id`),
  KEY `ix_task_sub_category_id` (`sub_category_id`),
  KEY `ix_task_etl` (`description`,`category_id`,`sub_category_id`),
  CONSTRAINT `fk_task_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_assigned_by_user_id_user_id` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_assigned_to_team_id_team_id` FOREIGN KEY (`assigned_to_team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_assigned_to_user_id_user_id` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_irfo_organisation_id_organisation_id` FOREIGN KEY (`irfo_organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_sub_category_id_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Task.  Generally raised by the system on key events such as an application being submitted.  Does not result in complicated workflow.  A task is completed and then ends.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_allocation_rule`
--

DROP TABLE IF EXISTS `task_allocation_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_allocation_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `category_id` int(10) unsigned DEFAULT NULL,
  `team_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `is_mlh` tinyint(1) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_task_allocation_rule_category_id` (`category_id`),
  KEY `ix_task_allocation_rule_team_id` (`team_id`),
  KEY `ix_task_allocation_rule_user_id` (`user_id`),
  KEY `ix_task_allocation_rule_goods_or_psv` (`goods_or_psv`),
  KEY `ix_task_allocation_rule_traffic_area_id` (`traffic_area_id`),
  CONSTRAINT `fk_task_allocation_rule_category_id_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_team_id_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_allocation_rule`
--

LOCK TABLES `task_allocation_rule` WRITE;
/*!40000 ALTER TABLE `task_allocation_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_allocation_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_alpha_split`
--

DROP TABLE IF EXISTS `task_alpha_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_alpha_split` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `task_allocation_rules_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `split_from_inclusive` varchar(2) NOT NULL,
  `split_to_inclusive` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_task_alpha_split_task_allocation_rules_id` (`task_allocation_rules_id`),
  KEY `ix_task_alpha_split_user_id` (`user_id`),
  CONSTRAINT `fk_task_alpha_split_task_alloc_rules_id_task_alloc_rule_id` FOREIGN KEY (`task_allocation_rules_id`) REFERENCES `task_allocation_rule` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_alpha_split_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_alpha_split`
--

LOCK TABLES `task_alpha_split` WRITE;
/*!40000 ALTER TABLE `task_alpha_split` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_alpha_split` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `traffic_area_id` varchar(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(70) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_team_olbs_key` (`olbs_key`),
  KEY `ix_team_traffic_area_id` (`traffic_area_id`),
  KEY `ix_team_last_modified_by` (`last_modified_by`),
  KEY `ix_team_created_by` (`created_by`),
  CONSTRAINT `fk_team_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Team.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_printer`
--

DROP TABLE IF EXISTS `team_printer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `team_id` int(10) unsigned NOT NULL,
  `printer_id` int(10) unsigned NOT NULL,
  `document_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_team_printer_printer_id` (`printer_id`),
  KEY `ix_team_printer_team_id` (`team_id`),
  CONSTRAINT `fk_team_printer_printer_id_printer_id` FOREIGN KEY (`printer_id`) REFERENCES `printer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_printer_team_id_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Printers for a team.  Type denotes for example which printer a teams disc print requests go to.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_printer`
--

LOCK TABLES `team_printer` WRITE;
/*!40000 ALTER TABLE `team_printer` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_printer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_application_oc`
--

DROP TABLE IF EXISTS `tm_application_oc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_application_oc` (
  `transport_manager_application_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`transport_manager_application_id`,`operating_centre_id`),
  KEY `ix_tm_application_oc_transport_manager_application_id` (`transport_manager_application_id`),
  KEY `ix_tm_application_oc_operating_centre_id` (`operating_centre_id`),
  CONSTRAINT `fk_tm_application_oc_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_application_oc_tm_application_id_tm_application_id` FOREIGN KEY (`transport_manager_application_id`) REFERENCES `transport_manager_application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_application_oc`
--

LOCK TABLES `tm_application_oc` WRITE;
/*!40000 ALTER TABLE `tm_application_oc` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_application_oc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_case_decision`
--

DROP TABLE IF EXISTS `tm_case_decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_case_decision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `case_id` int(10) unsigned NOT NULL,
  `decision_date` date DEFAULT NULL,
  `notified_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `decision` varchar(32) NOT NULL,
  `is_msi` tinyint(1) NOT NULL DEFAULT '0',
  `repute_not_lost_reason` varchar(500) DEFAULT NULL,
  `unfitness_start_date` date DEFAULT NULL,
  `unfitness_end_date` date DEFAULT NULL,
  `no_further_action_reason` varchar(4000) DEFAULT NULL,  
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tm_case_decision_olbs_key` (`olbs_key`),
  KEY `ix_tm_case_decision_decision` (`decision`),
  KEY `ix_tm_case_decision_created_by` (`created_by`),
  KEY `ix_tm_case_decision_last_modified_by` (`last_modified_by`),
  KEY `ix_tm_case_decision_case_id` (`case_id`),
  CONSTRAINT `fk_tm_case_decision_case_id_cases_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_decision_ref_data_id` FOREIGN KEY (`decision`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_case_decision`
--

LOCK TABLES `tm_case_decision` WRITE;
/*!40000 ALTER TABLE `tm_case_decision` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_case_decision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_case_decision_rehab`
--

DROP TABLE IF EXISTS `tm_case_decision_rehab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_case_decision_rehab` (
  `tm_case_decision_id` int(10) unsigned NOT NULL,
  `rehab_measure_id` varchar(32) NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`tm_case_decision_id`,`rehab_measure_id`),
  UNIQUE KEY `uk_tm_case_decision_rehab_olbs_key` (`olbs_key`),
  KEY `ix_tm_case_decision_rehab_tm_case_decision_id` (`tm_case_decision_id`),
  KEY `ix_tm_case_decision_rehab_rehab_measure_id` (`rehab_measure_id`),
  CONSTRAINT `fk_tm_case_decision_rehab_rehab_measure_id_ref_data_id` FOREIGN KEY (`rehab_measure_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decisn_rehab_tm_case_decisn_id_tm_case_decisn_id` FOREIGN KEY (`tm_case_decision_id`) REFERENCES `tm_case_decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_case_decision_rehab`
--

LOCK TABLES `tm_case_decision_rehab` WRITE;
/*!40000 ALTER TABLE `tm_case_decision_rehab` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_case_decision_rehab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_case_decision_unfitness`
--

DROP TABLE IF EXISTS `tm_case_decision_unfitness`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_case_decision_unfitness` (
  `tm_case_decision_id` int(10) unsigned NOT NULL,
  `unfitness_reason_id` varchar(32) NOT NULL,
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`tm_case_decision_id`,`unfitness_reason_id`),
  UNIQUE KEY `uk_tm_case_decision_unfitness_olbs_key` (`olbs_key`),
  KEY `ix_tm_case_decision_unfitness_tm_case_decision_id` (`tm_case_decision_id`),
  KEY `ix_tm_case_decision_unfitness_unfitness_reason_id` (`unfitness_reason_id`),
  CONSTRAINT `fk_tm_case_decision_unfitness_unfitness_reason_id_ref_data_id` FOREIGN KEY (`unfitness_reason_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decisn_unfitness_tm_case_decisn_id_tm_case_decisn_id` FOREIGN KEY (`tm_case_decision_id`) REFERENCES `tm_case_decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_case_decision_unfitness`
--

LOCK TABLES `tm_case_decision_unfitness` WRITE;
/*!40000 ALTER TABLE `tm_case_decision_unfitness` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_case_decision_unfitness` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_employment`
--

DROP TABLE IF EXISTS `tm_employment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_employment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `transport_manager_id` int(10) unsigned NOT NULL,
  `contact_details_id` int(10) unsigned NOT NULL,
  `position` varchar(45) DEFAULT NULL,
  `employer_name` varchar(90) DEFAULT NULL,
  `hours_per_week` varchar(100) DEFAULT NULL COMMENT 'e.g. 8 hours 5 days a week. Hence varchar',
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `last_modified_by` int(10) unsigned DEFAULT NULL,
  `created_on` datetime(6) DEFAULT NULL,
  `last_modified_on` datetime(6) DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `ix_tm_employment_transport_manager_id` (`transport_manager_id`),
  KEY `ix_tm_employment_contact_details_id` (`contact_details_id`),
  KEY `fk_tm_employment_user1_idx` (`created_by`),
  KEY `fk_tm_employment_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_tm_employment_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_employment_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_employment_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_employment_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_employment`
--

LOCK TABLES `tm_employment` WRITE;
/*!40000 ALTER TABLE `tm_employment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_employment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_licence_oc`
--

DROP TABLE IF EXISTS `tm_licence_oc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_licence_oc` (
  `transport_manager_licence_id` int(10) unsigned NOT NULL,
  `operating_centre_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`transport_manager_licence_id`,`operating_centre_id`),
  KEY `ix_tm_licence_oc_transport_manager_licence_id` (`transport_manager_licence_id`),
  KEY `ix_tm_licence_oc_operating_centre_id` (`operating_centre_id`),
  CONSTRAINT `fk_tm_licence_oc_operating_centre_id_operating_centre_id` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_licence_oc_tm_licence_id_tm_licence_id` FOREIGN KEY (`transport_manager_licence_id`) REFERENCES `transport_manager_licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_licence_oc`
--

LOCK TABLES `tm_licence_oc` WRITE;
/*!40000 ALTER TABLE `tm_licence_oc` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_licence_oc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_merge`
--

DROP TABLE IF EXISTS `tm_merge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_merge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `tm_from_id` int(10) unsigned NOT NULL COMMENT 'The TM that is being merged from',
  `tm_to_id` int(10) unsigned NOT NULL COMMENT 'The TM merging into',
  `tm_application_id` int(10) unsigned DEFAULT NULL COMMENT 'Application being moved from TM to TM',
  `tm_licence_id` int(10) unsigned DEFAULT NULL COMMENT 'Licence being moved from TM to TM',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tm_merge_olbs_key` (`olbs_key`),
  KEY `ix_tm_merge_tm_from_id` (`tm_from_id`),
  KEY `ix_tm_merge_tm_to_id` (`tm_to_id`),
  KEY `ix_tm_merge_tm_application_id` (`tm_application_id`),
  KEY `ix_tm_merge_tm_licence_id` (`tm_licence_id`),
  KEY `ix_tm_merge_created_by` (`created_by`),
  KEY `ix_tm_merge_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_tm_merge_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_tm_application_id_transport_manager_application_id` FOREIGN KEY (`tm_application_id`) REFERENCES `transport_manager_application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_tm_from_id_transport_manager_id` FOREIGN KEY (`tm_from_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_tm_licence_id_transport_manager_licence_id` FOREIGN KEY (`tm_licence_id`) REFERENCES `transport_manager_licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_tm_to_id_transport_manager_id` FOREIGN KEY (`tm_to_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_merge`
--

LOCK TABLES `tm_merge` WRITE;
/*!40000 ALTER TABLE `tm_merge` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_merge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_qualification`
--

DROP TABLE IF EXISTS `tm_qualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_qualification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `transport_manager_id` int(10) unsigned NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `qualification_type` varchar(32) NOT NULL,
  `issued_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `serial_no` varchar(20) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tm_qualification_olbs_key` (`olbs_key`),
  KEY `ix_tm_qualification_transport_manager_id` (`transport_manager_id`),
  KEY `ix_tm_qualification_country_code` (`country_code`),
  KEY `ix_tm_qualification_qualification_type` (`qualification_type`),
  KEY `ix_tm_qualification_created_by` (`created_by`),
  KEY `ix_tm_qualification_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_tm_qualification_country_code_country_id` FOREIGN KEY (`country_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_qualification_type_ref_data_id` FOREIGN KEY (`qualification_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Qualifications held by a transport manager.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_qualification`
--

LOCK TABLES `tm_qualification` WRITE;
/*!40000 ALTER TABLE `tm_qualification` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_qualification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trading_name`
--

DROP TABLE IF EXISTS `trading_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trading_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(160) NOT NULL,
  `licence_id` int(10) unsigned DEFAULT NULL COMMENT 'populated for non irfo records',
  `organisation_id` int(10) unsigned DEFAULT NULL COMMENT 'Used by IRFO',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `vi_action` varchar(1) DEFAULT NULL COMMENT 'Triggers entry in batch export to mobile compliance system',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trading_name_olbs_key` (`olbs_key`),
  KEY `ix_trading_name_licence_id` (`licence_id`),
  KEY `ix_trading_name_organisation_id` (`organisation_id`),
  KEY `ix_trading_name_created_by` (`created_by`),
  KEY `ix_trading_name_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_trading_name_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Trading name of a company if different from company name. Linked to licence as often different geographically. Linked to org for IRFO.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trading_name`
--

LOCK TABLES `trading_name` WRITE;
/*!40000 ALTER TABLE `trading_name` DISABLE KEYS */;
/*!40000 ALTER TABLE `trading_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_area`
--

DROP TABLE IF EXISTS `traffic_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_area` (
  `id` char(1) NOT NULL COMMENT 'Primary key.  Auto incremented if numeric.',
  `name` varchar(70) NOT NULL,
  `txc_name` varchar(70) DEFAULT NULL COMMENT 'TransXChange name',
  `contact_details_id` int(10) unsigned NOT NULL,
  `is_scotland` tinyint(1) NOT NULL DEFAULT '0',
  `is_wales` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni` tinyint(1) NOT NULL DEFAULT '0',
  `is_england` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_traffic_area_created_by` (`created_by`),
  KEY `ix_traffic_area_last_modified_by` (`last_modified_by`),
  KEY `ix_traffic_area_contact_details_id` (`contact_details_id`),
  CONSTRAINT `fk_traffic_area_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='County councils are grouped into traffic areas. DVSA business responsibility is split into traffic areas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_area`
--

LOCK TABLES `traffic_area` WRITE;
/*!40000 ALTER TABLE `traffic_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_area_enforcement_area`
--

DROP TABLE IF EXISTS `traffic_area_enforcement_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_area_enforcement_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `traffic_area_id` varchar(1) NOT NULL,
  `enforcement_area_id` varchar(4) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ta_enforcement_area_traffic_area_id_enforcement_area_id` (`traffic_area_id`,`enforcement_area_id`),
  KEY `ix_traffic_area_enforcement_area_traffic_area_id` (`traffic_area_id`),
  KEY `ix_traffic_area_enforcement_area_enforcement_area_id` (`enforcement_area_id`),
  KEY `ix_traffic_area_enforcement_area_created_by` (`created_by`),
  KEY `ix_traffic_area_enforcement_area_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_traffic_area_enf_area_enf_area_id_enf_area_id` FOREIGN KEY (`enforcement_area_id`) REFERENCES `enforcement_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_enforcement_area_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_enforcement_area_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_enforcement_area_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Enforcement areas within a traffic area';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_area_enforcement_area`
--

LOCK TABLES `traffic_area_enforcement_area` WRITE;
/*!40000 ALTER TABLE `traffic_area_enforcement_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_area_enforcement_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trailer`
--

DROP TABLE IF EXISTS `trailer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trailer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `trailer_no` varchar(20) NOT NULL,
  `specified_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trailer_olbs_key` (`olbs_key`),
  KEY `ix_trailer_licence_id` (`licence_id`),
  KEY `ix_trailer_created_by` (`created_by`),
  KEY `ix_trailer_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_trailer_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trailer_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trailer_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Trailer details.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trailer`
--

LOCK TABLES `trailer` WRITE;
/*!40000 ALTER TABLE `trailer` DISABLE KEYS */;
/*!40000 ALTER TABLE `trailer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport_manager`
--

DROP TABLE IF EXISTS `transport_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transport_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `tm_status` varchar(32) NOT NULL,
  `tm_type` varchar(32) NOT NULL,
  `home_cd_id` int(10) unsigned NOT NULL,
  `work_cd_id` int(10) unsigned DEFAULT NULL,
  `disqualification_tm_case_id` int(10) unsigned DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `nysiis_family_name` varchar(100) DEFAULT NULL,
  `nysiis_forename` varchar(100) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_transport_manager_olbs_key` (`olbs_key`),
  KEY `ix_transport_manager_tm_status` (`tm_status`),
  KEY `ix_transport_manager_tm_type` (`tm_type`),
  KEY `ix_transport_manager_home_cd_id` (`home_cd_id`),
  KEY `ix_transport_manager_created_by` (`created_by`),
  KEY `ix_transport_manager_last_modified_by` (`last_modified_by`),
  KEY `ix_transport_manager_work_cd_id` (`work_cd_id`),
  CONSTRAINT `fk_transport_manager_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_home_cd_id_contact_details_id` FOREIGN KEY (`home_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_tm_status_ref_data_id` FOREIGN KEY (`tm_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_tm_type_ref_data_id` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_work_cd_id_contact_details_id` FOREIGN KEY (`work_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Person responsible for compliance on numerous licences and applications.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_manager`
--

LOCK TABLES `transport_manager` WRITE;
/*!40000 ALTER TABLE `transport_manager` DISABLE KEYS */;
/*!40000 ALTER TABLE `transport_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport_manager_application`
--

DROP TABLE IF EXISTS `transport_manager_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transport_manager_application` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `transport_manager_id` int(10) unsigned NOT NULL,
  `application_id` int(10) unsigned NOT NULL,
  `tm_type` varchar(32) DEFAULT NULL,
  `tm_application_status` varchar(32) DEFAULT NULL,
  `action` varchar(1) DEFAULT NULL COMMENT 'A or D for Add or Delete',
  `hours_mon` smallint(5) unsigned DEFAULT NULL,
  `hours_tue` smallint(5) unsigned DEFAULT NULL,
  `hours_wed` smallint(5) unsigned DEFAULT NULL,
  `hours_thu` smallint(5) unsigned DEFAULT NULL,
  `hours_fri` smallint(5) unsigned DEFAULT NULL,
  `hours_sat` smallint(5) unsigned DEFAULT NULL,
  `hours_sun` smallint(5) unsigned DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `additional_information` varchar(4000) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_transport_manager_application_olbs_key` (`olbs_key`),
  KEY `ix_transport_manager_application_transport_manager_id` (`transport_manager_id`),
  KEY `ix_transport_manager_application_application_id` (`application_id`),
  KEY `ix_transport_manager_application_created_by` (`created_by`),
  KEY `ix_transport_manager_application_last_modified_by` (`last_modified_by`),
  KEY `ix_transport_manager_application_tm_type` (`tm_type`),
  KEY `ix_transport_manager_application_tm_application_status` (`tm_application_status`),
  CONSTRAINT `fk_tm_application_tm_application_status_ref_data_id` FOREIGN KEY (`tm_application_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_application_tm_id_tm_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_application_id_application_id` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_tm_type_ref_data_id` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Applications a transport manager is responsible for.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_manager_application`
--

LOCK TABLES `transport_manager_application` WRITE;
/*!40000 ALTER TABLE `transport_manager_application` DISABLE KEYS */;
/*!40000 ALTER TABLE `transport_manager_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport_manager_licence`
--

DROP TABLE IF EXISTS `transport_manager_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transport_manager_licence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `transport_manager_id` int(10) unsigned NOT NULL,
  `licence_id` int(10) unsigned NOT NULL,
  `tm_type` varchar(32) DEFAULT NULL,
  `hours_mon` smallint(5) unsigned DEFAULT NULL,
  `hours_tue` smallint(5) unsigned DEFAULT NULL,
  `hours_wed` smallint(5) unsigned DEFAULT NULL,
  `hours_thu` smallint(5) unsigned DEFAULT NULL,
  `hours_fri` smallint(5) unsigned DEFAULT NULL,
  `hours_sat` smallint(5) unsigned DEFAULT NULL,
  `hours_sun` smallint(5) unsigned DEFAULT NULL,
  `additional_information` varchar(4000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_transport_manager_licence_olbs_key` (`olbs_key`),
  KEY `ix_transport_manager_licence_transport_manager_id` (`transport_manager_id`),
  KEY `ix_transport_manager_licence_licence_id` (`licence_id`),
  KEY `ix_transport_manager_licence_created_by` (`created_by`),
  KEY `ix_transport_manager_licence_last_modified_by` (`last_modified_by`),
  KEY `ix_transport_manager_licence_tm_type` (`tm_type`),
  CONSTRAINT `fk_tm_licence_tm_id_tm_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_tm_type_ref_data_id` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licences a transport manager is responsible for.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_manager_licence`
--

LOCK TABLES `transport_manager_licence` WRITE;
/*!40000 ALTER TABLE `transport_manager_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `transport_manager_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `txc_inbox`
--

DROP TABLE IF EXISTS `txc_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `txc_inbox` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `bus_reg_id` int(10) unsigned NOT NULL,
  `local_authority_id` int(10) unsigned DEFAULT NULL,
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `zip_document_id` int(10) unsigned NOT NULL,
  `route_document_id` int(10) unsigned NOT NULL,
  `pdf_document_id` int(10) unsigned NOT NULL,
  `file_read` tinyint(1) DEFAULT NULL,
  `variation_no` smallint(5) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_txc_inbox_bus_reg_id` (`bus_reg_id`),
  KEY `ix_txc_inbox_local_authority_id` (`local_authority_id`),
  KEY `ix_txc_inbox_organisation_id` (`organisation_id`),
  KEY `ix_txc_inbox_zip_document_id` (`zip_document_id`),
  KEY `ix_txc_inbox_route_document_id` (`route_document_id`),
  KEY `ix_txc_inbox_pdf_document_id` (`pdf_document_id`),
  KEY `ix_txc_inbox_created_by` (`created_by`),
  KEY `ix_txc_inbox_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_txc_inbox_bus_reg_id_bus_reg_id` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_local_authority_id_local_authority_id` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_organisation_id_organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_pdf_document_id_document_id` FOREIGN KEY (`pdf_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_route_document_id_document_id` FOREIGN KEY (`route_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_zip_document_id_document_id` FOREIGN KEY (`zip_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `txc_inbox`
--

LOCK TABLES `txc_inbox` WRITE;
/*!40000 ALTER TABLE `txc_inbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `txc_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `team_id` int(10) unsigned NOT NULL,
  `transport_manager_id` int(10) unsigned DEFAULT NULL COMMENT 'If user is also a transport manager.',
  `local_authority_id` int(10) unsigned DEFAULT NULL COMMENT 'If user is a member of a local authority a link to the LA details.',
  `contact_details_id` int(10) unsigned DEFAULT NULL,
  `partner_contact_details_id` int(10) unsigned DEFAULT NULL COMMENT 'If user is part of a partner, such as HMRC a link to the partners details.',
  `email_address` varchar(45) DEFAULT NULL,
  `pid` varchar(255) DEFAULT NULL,
  `login_id` varchar(40) DEFAULT NULL,
  `account_disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Account locked by DVSA. Cannot be unlocked by non DVSA user.',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `locked_date` datetime DEFAULT NULL COMMENT 'To stop brute force password attack.  When number of attemps greate than X date set. User cant attempt again until date plus X number of minutes.',
  `attempts` smallint(5) unsigned DEFAULT NULL COMMENT 'Count of unsuccessful login attempts. Resets on successful login.',
  `last_successful_login_date` datetime DEFAULT NULL,
  `hint_question_id1` int(10) unsigned DEFAULT NULL COMMENT 'Question for user self password reset.',
  `hint_question_id2` int(10) unsigned DEFAULT NULL COMMENT 'Question for user self password reset.',
  `hint_answer_1` varchar(50) DEFAULT NULL COMMENT 'Password reset answer.',
  `hint_answer_2` varchar(50) DEFAULT NULL COMMENT 'Password reset answer.',
  `memorable_word` varchar(10) DEFAULT NULL COMMENT 'Part of non internal user login. User challenged to enter 2 letters of word.',
  `memorable_word_digit1` smallint(5) unsigned DEFAULT NULL COMMENT 'Letter used in last user login challenge. Used to ensure different challenge on next login.',
  `memorable_word_digit2` smallint(5) unsigned DEFAULT NULL COMMENT 'Letter used in last user login challenge. Used to ensure different challenge on next login.',
  `must_reset_password` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'On next login user must reset password.',
  `password_expiry_date` datetime DEFAULT NULL COMMENT 'Typically a year after account created.',
  `reset_password_expiry_date` datetime DEFAULT NULL COMMENT 'After password reset by admin user has x number of days to login and change password or account is locked.',
  `password_reminder_sent` tinyint(1) DEFAULT NULL COMMENT 'At X number of days before password expiry a warning email is sent to user.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_user_team_id` (`team_id`),
  KEY `ix_user_local_authority_id` (`local_authority_id`),
  KEY `ix_user_created_by` (`created_by`),
  KEY `ix_user_last_modified_by` (`last_modified_by`),
  KEY `ix_user_contact_details_id` (`contact_details_id`),
  KEY `ix_user_partner_contact_details_id` (`partner_contact_details_id`),
  KEY `ix_user_hint_question_id1` (`hint_question_id1`),
  KEY `ix_user_hint_question_id2` (`hint_question_id2`),
  KEY `ix_user_transport_manager_id` (`transport_manager_id`),
  CONSTRAINT `fk_user_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_hint_question_id1_hint_question_id` FOREIGN KEY (`hint_question_id1`) REFERENCES `hint_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_hint_question_id2_hint_question_id` FOREIGN KEY (`hint_question_id2`) REFERENCES `hint_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,  
  CONSTRAINT `fk_user_local_authority_id_local_authority_id` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_partner_contact_details_id_contact_details_id` FOREIGN KEY (`partner_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,  
  CONSTRAINT `fk_user_team_id_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_transport_manager_id_transport_manager_id` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COMMENT='System user.  Could be operators, DVSA staff, local authorities, government departments etc..';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  PRIMARY KEY (`id`),
  KEY `ix_user_role_role_id` (`role_id`),
  KEY `ix_user_role_user_id` (`user_id`),
  KEY `ix_user_role_created_by` (`created_by`),
  KEY `ix_user_role_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_user_role_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role_role_id_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role_user_id_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `is_novelty` tinyint(1) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL COMMENT 'Nullable for PSVs',
  `plated_weight` int(10) unsigned DEFAULT NULL,
  `certificate_no` varchar(50) DEFAULT NULL COMMENT 'psv only',
  `vi_action` varchar(1) DEFAULT NULL,
  `section_26` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_curtail` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_revoked` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_suspend` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete',
  `psv_type` varchar(32) DEFAULT NULL COMMENT 'small, medium or large',
  `make_model` varchar(100) DEFAULT NULL COMMENT 'For small PSV vehicles the make and model are recorded.',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vehicle_olbs_key` (`olbs_key`),
  KEY `ix_vehicle_created_by` (`created_by`),
  KEY `ix_vehicle_last_modified_by` (`last_modified_by`),
  KEY `ix_vehicle_psv_type` (`psv_type`),
  CONSTRAINT `fk_vehicle_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vehicle_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vehicle_psv_type_ref_data_id` FOREIGN KEY (`psv_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PSV or goods vehicle.  Not always populated for PSV vehicles on a licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle`
--

LOCK TABLES `vehicle` WRITE;
/*!40000 ALTER TABLE `vehicle` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `void_disc`
--

DROP TABLE IF EXISTS `void_disc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `void_disc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `goods_or_psv` varchar(32) NOT NULL,
  `licence_type` varchar(32) NOT NULL,
  `serial_start` int(10) unsigned DEFAULT NULL,
  `serial_end` int(11) DEFAULT NULL COMMENT 'End of series that are void.  Some -1s in data so signed.',
  `traffic_area_id` char(1) DEFAULT NULL,
  `is_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_void_disc_olbs_key` (`olbs_key`),
  KEY `ix_void_disc_goods_or_psv` (`goods_or_psv`),
  KEY `ix_void_disc_licence_type` (`licence_type`),
  KEY `ix_void_disc_traffic_area_id` (`traffic_area_id`),
  KEY `ix_void_disc_created_by` (`created_by`),
  KEY `ix_void_disc_last_modified_by` (`last_modified_by`),
  CONSTRAINT `fk_void_disc_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_goods_or_psv_ref_data_id` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_licence_type_ref_data_id` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_traffic_area_id_traffic_area_id` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Void disc to not be used in a print run by system. Discs have ids preprinted so need method to skip void sequences';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `void_disc`
--

LOCK TABLES `void_disc` WRITE;
/*!40000 ALTER TABLE `void_disc` DISABLE KEYS */;
/*!40000 ALTER TABLE `void_disc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workshop`
--

DROP TABLE IF EXISTS `workshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workshop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.  Auto incremented if numeric.',
  `licence_id` int(10) unsigned NOT NULL,
  `contact_details_id` int(10) unsigned NOT NULL,
  `is_external` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is garage or workshop.',
  `maintenance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Carries out maintenance.',
  `safety_inspection` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Carries out own safety inspections.',
  `removed_date` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who created record.',
  `last_modified_by` int(10) unsigned DEFAULT NULL COMMENT 'User id of user who last modified the record.',
  `created_on` datetime(6) DEFAULT NULL COMMENT 'Date record created.',
  `last_modified_on` datetime(6) DEFAULT NULL COMMENT 'Date record last modified.',
  `version` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Optimistic Locking',
  `olbs_key` int(10) unsigned DEFAULT NULL COMMENT 'Used to map FKs during ETL. Can be dropped safely when OLBS decommissioned',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_workshop_olbs_key` (`olbs_key`),
  KEY `ix_workshop_licence_id` (`licence_id`),
  KEY `ix_workshop_created_by` (`created_by`),
  KEY `ix_workshop_last_modified_by` (`last_modified_by`),
  KEY `ix_workshop_contact_details_id` (`contact_details_id`),
  CONSTRAINT `fk_workshop_contact_details_id_contact_details_id` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_created_by_user_id` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_last_modified_by_user_id` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_licence_id_licence_id` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Workshop that carries out vehicle maintenance for a licence.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workshop`
--

LOCK TABLES `workshop` WRITE;
/*!40000 ALTER TABLE `workshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `workshop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `bus_reg_search_view`
--

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-19 12:53:18
