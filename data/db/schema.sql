-- MySQL dump 10.13  Distrib 5.6.16, for osx10.7 (x86_64)
--
-- Host: localhost    Database: olcs_tmp
-- ------------------------------------------------------
-- Server version	5.6.16

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_address_country1_idx` (`country_code`),
  KEY `fk_address_user1_idx` (`created_by`),
  KEY `fk_address_user2_idx` (`last_modified_by`),
  KEY `fk_address_admin_area_traffic_area1_idx` (`admin_area`),
  CONSTRAINT `fk_address_country1` FOREIGN KEY (`country_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_admin_area_traffic_area1` FOREIGN KEY (`admin_area`) REFERENCES `admin_area_traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds addreses. Accessed via contact_details for context of address type, e.g. Registered Office address, Transport Consultant etc.';
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
  `id` char(40) NOT NULL COMMENT 'Admin area used to link local auth from address service to traffic area',
  `traffic_area_id` varchar(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_admin_area_traffic_area_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_admin_area_traffic_area_user1_idx` (`created_by`),
  KEY `fk_admin_area_traffic_area_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_admin_area_traffic_area_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_admin_area_traffic_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_admin_area_traffic_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A traffic area contains several admin areas. The collection of admin areas will be the councils inside a county boundary. Traffic areas split on groups of county boundaries.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'A case can have a single appeal.',
  `appeal_no` varchar(20) NOT NULL COMMENT 'Non system generated number entered by user.',
  `case_id` int(11) DEFAULT NULL,
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
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_appeal_case1_idx` (`case_id`),
  KEY `fk_appeal_user1_idx` (`created_by`),
  KEY `fk_appeal_user2_idx` (`last_modified_by`),
  KEY `fk_appeal_ref_data1_idx` (`reason`),
  KEY `fk_appeal_ref_data2_idx` (`outcome`),
  CONSTRAINT `fk_appeal_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_ref_data1` FOREIGN KEY (`reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appeal_ref_data2` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='After a case has a decision there can be One appeal made against the decision.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Application for a new licence or to vary an existing licence.',
  `licence_id` int(11) NOT NULL COMMENT 'Licence can have many applications, even several being processed at the same time',
  `status` varchar(32) NOT NULL COMMENT 'Applications, once submitted, are new. Can then be granted or not. Normally become valid.',
  `is_variation` tinyint(1) NOT NULL COMMENT 'New or variation application. 0 for new, 1 for variation',
  `has_entered_reg` tinyint(1) DEFAULT NULL COMMENT 'Stores user has elected to enter psv vehicles. Affects application screenflow. Show screen to enter vehicles or not.',
  `tot_auth_trailers` int(11) DEFAULT NULL COMMENT 'Applicant wants to be authorised for this number of trailers.',
  `tot_auth_vehicles` int(11) DEFAULT NULL COMMENT 'Applicant wants to be authorised for this number of vehicles for goods. Will be sum of the psv columns for psv.',
  `tot_auth_small_vehicles` int(11) DEFAULT NULL COMMENT 'psv small vehicles',
  `tot_auth_medium_vehicles` int(11) DEFAULT NULL COMMENT 'psv medium vehicles',
  `tot_auth_large_vehicles` int(11) DEFAULT NULL COMMENT 'psv large vehicles',
  `tot_community_licences` int(11) DEFAULT NULL COMMENT 'Number of EU community licences required',
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL COMMENT 'Restricted, Standard International etc.',
  `ni_flag` tinyint(1) DEFAULT NULL,
  `bankrupt` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been declared bankrupt',
  `administration` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been involved in a company that went into administration',
  `disqualified` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been disqualified as a director or manager of a company',
  `liquidation` tinyint(1) DEFAULT NULL COMMENT 'Operator has been liquidated',
  `receivership` tinyint(1) DEFAULT NULL COMMENT 'Any person in application has ever been involved in a company that went into receivership',
  `insolvency_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User has confirmed that any futire insolvency will be communicated to the TC',
  `insolvency_details` varchar(4000) DEFAULT NULL COMMENT 'Details of previous bankrupcy, insolvency, administration, receivership of people linked to application',
  `safety_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User confirms they have read safety information in application and will comply',
  `declaration_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User confirms they have read undertakings and declarations and will comply',
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
  `psv_limousines` tinyint(1) DEFAULT NULL COMMENT 'Are any vehicles on licence limos or novelty.Section 15F PSV421',
  `psv_no_limousine_confirmation` tinyint(1) DEFAULT NULL COMMENT 'If no limos on licence user confirms they will not put any on licence. Section 15F PSV421',
  `psv_only_limousines_confirmation` tinyint(1) DEFAULT NULL COMMENT 'Licence is only for limos and no other vehicle types. Section 15G PSV 421',
  `interim_start` date DEFAULT NULL COMMENT 'Date interim licence is to start.',
  `interim_end` date DEFAULT NULL COMMENT 'Date interim licence is to end.',
  `interim_auth_vehicles` int(11) DEFAULT NULL COMMENT 'Number of vehicles authorised on interim licence.',
  `interim_auth_trailers` int(11) DEFAULT NULL COMMENT 'Number of trailers authorised on interim licence.',
  `interim_status` varchar(32) DEFAULT NULL COMMENT 'Interim licence status.',
  `is_maintenance_suitable` tinyint(1) DEFAULT NULL COMMENT 'User confirmation that maintenance agreements are suitable and guidence notes read.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_application_licence1_idx` (`licence_id`),
  KEY `fk_application_user1_idx` (`created_by`),
  KEY `fk_application_user2_idx` (`last_modified_by`),
  KEY `fk_application_ref_data1_idx` (`licence_type`),
  KEY `fk_application_ref_data2_idx` (`status`),
  KEY `fk_application_ref_data3_idx` (`interim_status`),
  KEY `fk_application_ref_data4_idx` (`withdrawn_reason`),
  KEY `fk_application_ref_data5_idx` (`goods_or_psv`),
  CONSTRAINT `fk_application_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_ref_data1` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_ref_data2` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_ref_data3` FOREIGN KEY (`interim_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_ref_data4` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_ref_data5` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Application to vary a licence or to apply for a new licence. If successful values from app will be copied into licence.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Completion status of each section of an online application.',
  `application_id` int(11) NOT NULL,
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
  `last_section` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_application_completion_user1_idx` (`created_by`),
  KEY `fk_application_completion_user2_idx` (`last_modified_by`),
  UNIQUE KEY `fk_application_completion_application_id_udx` (`application_id`),
  CONSTRAINT `fk_application_completion_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_completion_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_completion_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores progress of an online (self service) application. Used to decide if app has enough info to be submitted and to display feedback to user of completion status of app sections.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Operating centres to be added, deleted, or changed by the application.',
  `application_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `action` varchar(1) DEFAULT NULL COMMENT 'Flag for add, delete, update. Values A,U or D',
  `ad_placed` tinyint(1) NOT NULL COMMENT 'An advert has been placed in a suitable publication to notify public of op centre changes.',
  `ad_placed_in` varchar(70) DEFAULT NULL COMMENT 'Publication advert placed in.',
  `ad_placed_date` date DEFAULT NULL COMMENT 'Date advert published.',
  `publication_appropriate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publication deemed appropriate by caseworker.',
  `permission` tinyint(1) NOT NULL COMMENT 'Applicant has permission to use site or owns it.',
  `sufficient_parking` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Site has sufficient parking for vehicles and trailers applied for.',
  `no_of_trailers_required` int(11) DEFAULT NULL COMMENT 'Number of trailers required to be kept at op centre',
  `no_of_vehicles_required` int(11) DEFAULT NULL COMMENT 'Number of vehicles required to be kept at op centre',
  `vi_action` varchar(1) DEFAULT NULL COMMENT 'Flag used in populated the vehicle inspectorate extract sent to mobile compliance system as part of batch job',
  `deleted_date` datetime DEFAULT NULL,
  `is_interim` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is operating centre required to be on interim licence.',
  `s4_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_ApplicationOperatingCentre_Application1_idx` (`application_id`),
  KEY `fk_ApplicationOperatingCentre_OperatingCentre1_idx` (`operating_centre_id`),
  KEY `fk_application_operating_centre_user1_idx` (`created_by`),
  KEY `fk_application_operating_centre_user2_idx` (`last_modified_by`),
  KEY `fk_application_operating_centre_s41_idx` (`s4_id`),
  CONSTRAINT `fk_ApplicationOperatingCentre_Application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ApplicationOperatingCentre_OperatingCentre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_operating_centre_s41` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Operating centre changes included in the application. Can be add, update or deletes. Adds will create a licence OC if app is successful. Update change values on relevent lic OC and deletes set removed date.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_operating_centre`
--

LOCK TABLES `application_operating_centre` WRITE;
/*!40000 ALTER TABLE `application_operating_centre` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_operating_centre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_notice_period`
--

DROP TABLE IF EXISTS `bus_notice_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_notice_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_area` varchar(70) NOT NULL COMMENT 'The area relevant for the period. Initially Scotland or Other.',
  `standard_period` int(11) NOT NULL,
  `cancellation_period` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_bus_notice_period_user1_idx` (`created_by`),
  KEY `fk_bus_notice_period_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_bus_notice_period_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_notice_period_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds reference data for notice periods for bus registration changes. Currently scotland has different values.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  `revert_status` varchar(32) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `bus_notice_period_id` int(11) NOT NULL COMMENT 'Scottish or other',
  `route_no` int(11) NOT NULL COMMENT 'Increases by one for each registration added to licence',
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
  `operating_centre_id` int(11) DEFAULT NULL COMMENT 'Populated if the oc address is to be used',
  `variation_no` int(11) NOT NULL DEFAULT '0' COMMENT 'Increments for each variation',
  `parent_id` int(11) NULL DEFAULT NULL,
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
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_bus_reg_licence1_idx` (`licence_id`),
  KEY `fk_bus_reg_bus_notice_period1_idx` (`bus_notice_period_id`),
  KEY `fk_bus_reg_ref_data1_idx` (`subsidised`),
  KEY `fk_bus_reg_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_bus_reg_user1_idx` (`created_by`),
  KEY `fk_bus_reg_user2_idx` (`last_modified_by`),
  KEY `fk_bus_reg_ref_data2_idx` (`withdrawn_reason`),
  KEY `fk_bus_reg_ref_data3_idx` (`status`),
  KEY `fk_bus_reg_ref_data4_idx` (`revert_status`),
  KEY `fk_bus_reg_bus_reg_idx` (`parent_id`),
  CONSTRAINT `fk_bus_reg_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_bus_notice_period1` FOREIGN KEY (`bus_notice_period_id`) REFERENCES `bus_notice_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_ref_data1` FOREIGN KEY (`subsidised`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_ref_data2` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_ref_data3` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_ref_data4` FOREIGN KEY (`revert_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_bus_reg` FOREIGN KEY (`parent_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `bus_reg_id` int(11) NOT NULL,
  `bus_service_type_id` int(11) NOT NULL,
  PRIMARY KEY (`bus_reg_id`,`bus_service_type_id`),
  KEY `fk_bus_reg_bus_service_type_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_bus_reg_bus_service_type_bus_service_type1` (`bus_service_type_id`),
  CONSTRAINT `fk_bus_reg_bus_service_type_bus_service_type1` FOREIGN KEY (`bus_service_type_id`) REFERENCES `bus_service_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_bus_service_type_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `bus_reg_id` int(11) NOT NULL,
  `local_authority_id` int(11) NOT NULL,
  PRIMARY KEY (`bus_reg_id`,`local_authority_id`),
  UNIQUE KEY `bus_reg_la_unique` (`local_authority_id`,`bus_reg_id`),
  KEY `fk_bus_reg_local_auth_local_authority1_idx` (`local_authority_id`),
  CONSTRAINT `fk_bus_reg_local_auth_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_local_auth_local_authority1` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_reg_id` int(11) NOT NULL,
  `service_no` varchar(70) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_bus_reg_other_service_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_bus_reg_other_service_user1_idx` (`created_by`),
  KEY `fk_bus_reg_other_service_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_bus_reg_other_service_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_other_service_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_other_service_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traffic_area_id` varchar(1) NOT NULL,
  `bus_reg_id` int(11) NOT NULL,
  `txc_missing` tinyint(1) DEFAULT NULL,
  `txc_not_required` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bus_reg_ta_unique` (`traffic_area_id`,`bus_reg_id`),
  KEY `fk_bus_reg_traffic_area_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_bus_reg_traffic_area_user1_idx` (`created_by`),
  KEY `fk_bus_reg_traffic_area_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_bus_reg_traffic_area_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_traffic_area_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_traffic_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_traffic_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `bus_reg_id` int(11) NOT NULL,
  `variation_reason_id` int(11) NOT NULL,
  PRIMARY KEY (`bus_reg_id`,`variation_reason_id`),
  KEY `fk_bus_reg_has_variation_reason_variation_reason1_idx` (`variation_reason_id`),
  KEY `fk_bus_reg_has_variation_reason_bus_reg1_idx` (`bus_reg_id`),
  CONSTRAINT `fk_bus_reg_has_variation_reason_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_reg_has_variation_reason_variation_reason1` FOREIGN KEY (`variation_reason_id`) REFERENCES `variation_reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `description` varchar(70) DEFAULT NULL,
  `txc_service_type_name` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_reg_id` int(11) NOT NULL,
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
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bus_reg_id_UNIQUE` (`bus_reg_id`),
  KEY `fk_bus_short_notice_user1_idx` (`created_by`),
  KEY `fk_bus_short_notice_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_bus_short_notice_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_short_notice_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bus_short_notice_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `case_id` int(11) NOT NULL,
  `category_id` varchar(32) NOT NULL,
  PRIMARY KEY (`case_id`,`category_id`),
  KEY `fk_case_category_cases1_idx` (`case_id`),
  KEY `fk_case_category_ref_data1_idx` (`category_id`),
  CONSTRAINT `fk_case_category_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_category_ref_data1` FOREIGN KEY (`category_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categorises a case for reporting and searching.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_category`
--

LOCK TABLES `case_category` WRITE;
/*!40000 ALTER TABLE `case_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_type` varchar(32) NOT NULL COMMENT 'Created from App.lic or TM',
  `application_id` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `ecms_no` varchar(45) DEFAULT NULL,
  `open_date` datetime NOT NULL,
  `close_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
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
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_case_application1_idx` (`application_id`),
  KEY `fk_case_licence1_idx` (`licence_id`),
  KEY `fk_case_user1_idx` (`created_by`),
  KEY `fk_case_user2_idx` (`last_modified_by`),
  KEY `fk_cases_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_cases_ref_data1_idx` (`case_type`),
  KEY `fk_cases_ref_data2_idx` (`erru_case_type`),
  CONSTRAINT `fk_case_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_ref_data1` FOREIGN KEY (`case_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cases_ref_data2` FOREIGN KEY (`erru_case_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Compliance case.  Can be for TMs or a licence. If licence can link to application and operating centres. Various types, such as public inquiry, impounding etc. Has several SLAs and a decision, stay, appeal process.';
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
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_doc_category` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Documents can have this category',
  `is_task_category` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Tasks can have this category',
  `is_scan_category` tinyint(1) NOT NULL DEFAULT '1',
  `task_allocation_type` varchar(32) DEFAULT NULL COMMENT 'Tasks of this category are allocated based upon TA, a single team or complex rules for icence type, TA, MLH.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_document_category_user1_idx` (`created_by`),
  KEY `fk_document_category_user2_idx` (`last_modified_by`),
  KEY `fk_category_ref_data2_idx` (`task_allocation_type`),
  CONSTRAINT `fk_document_category_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_category_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_ref_data2` FOREIGN KEY (`task_allocation_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Business Category, such as licencing, compliance, environmental. Used to categorise documentation and tasks. Has different sub categories for tasks ao documents.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL COMMENT 'The new licence',
  `old_licence_no` varchar(18) NOT NULL COMMENT 'The old licence number for display purposes',
  `old_organisation_name` varchar(160) NOT NULL COMMENT 'The old organisation for display purposes',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `licence_id_UNIQUE` (`licence_id`),
  KEY `fk_change_of_entity_licence1_idx` (`licence_id`),
  KEY `fk_change_of_entity_user1_idx` (`created_by`),
  KEY `fk_change_of_entity_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_change_of_entity_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_change_of_entity_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_change_of_entity_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Used when an organisation changes name via companies house and applies for a new licence. Results in old licence being withdrawn and link to old org name and licence being stored.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `status` varchar(32) NOT NULL COMMENT 'annulled, cns, expired, pending, returned, revoked, surrender, suspended, valid, void, withdrawn',
  `expired_date` datetime DEFAULT NULL COMMENT 'The date the licence expired.',
  `specified_date` datetime DEFAULT NULL COMMENT 'Activation date of com licence.',
  `licence_expired_date` date DEFAULT NULL COMMENT 'The date the community licence will expire. Typically 5 years after specified date.  Generally less for an interim licence.',
  `issue_no` int(11) DEFAULT NULL COMMENT 'Issue 0 is the office copy. 0 is the licence, all others are refered to as certified copies.',
  `serial_no` int(11) DEFAULT NULL COMMENT 'Business ID',
  `serial_no_prefix` varchar(4) DEFAULT NULL COMMENT 'UKGB or UKNI',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_licence1_idx` (`licence_id`),
  KEY `fk_community_lic_user1_idx` (`created_by`),
  KEY `fk_community_lic_user2_idx` (`last_modified_by`),
  KEY `fk_community_lic_ref_data1_idx` (`status`),
  CONSTRAINT `fk_community_lic_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_ref_data1` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Community licence. A licence for travel within the EU for both goods and PSV (but not PSV SR).';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_lic_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_actioned` tinyint(1) DEFAULT '0' COMMENT 'Possibly not required. In legacy as part of batch job.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_suspension_community_lic1_idx` (`community_lic_id`),
  KEY `fk_community_lic_suspension_user1_idx` (`created_by`),
  KEY `fk_community_lic_suspension_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_suspension_community_lic1` FOREIGN KEY (`community_lic_id`) REFERENCES `community_lic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A suspension for a community lic.  Possibly future dated. Processed by overnight batch job to change com lic state.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_lic_suspension_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_suspension_reason_community_lic_suspension_idx` (`community_lic_suspension_id`),
  KEY `fk_community_lic_suspension_reason_community_lic_suspension_idx1` (`reason_id`),
  KEY `fk_community_lic_suspension_reason_user1_idx` (`created_by`),
  KEY `fk_community_lic_suspension_reason_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_suspension_reason_community_lic_suspension1` FOREIGN KEY (`community_lic_suspension_id`) REFERENCES `community_lic_suspension` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_community_lic_suspension_r1` FOREIGN KEY (`reason_id`) REFERENCES `community_lic_suspension_reason_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Reasons for a suspension.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_suspension_reason`
--

LOCK TABLES `community_lic_suspension_reason` WRITE;
/*!40000 ALTER TABLE `community_lic_suspension_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_suspension_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_suspension_reason_type`
--

DROP TABLE IF EXISTS `community_lic_suspension_reason_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_suspension_reason_type` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_suspension_reason_type_user1_idx` (`created_by`),
  KEY `fk_community_lic_suspension_reason_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_suspension_reason_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_suspension_reason_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Possible suspension reasons.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_suspension_reason_type`
--

LOCK TABLES `community_lic_suspension_reason_type` WRITE;
/*!40000 ALTER TABLE `community_lic_suspension_reason_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_suspension_reason_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_withdrawal`
--

DROP TABLE IF EXISTS `community_lic_withdrawal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_withdrawal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_lic_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_withdrawal_community_lic1_idx` (`community_lic_id`),
  KEY `fk_community_lic_withdrawal_user1_idx` (`created_by`),
  KEY `fk_community_lic_withdrawal_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_withdrawal_community_lic1` FOREIGN KEY (`community_lic_id`) REFERENCES `community_lic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Time period a community licence is withdrawn over.  Possibly future dated. Batch job uses this to change com lic states overnight.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `community_lic_withdrawal_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_withdrawal_reason_community_lic_withdrawal_idx` (`community_lic_withdrawal_id`),
  KEY `fk_community_lic_withdrawal_reason_community_lic_withdrawal_idx1` (`reason_id`),
  KEY `fk_community_lic_withdrawal_reason_user1_idx` (`created_by`),
  KEY `fk_community_lic_withdrawal_reason_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_withdrawal_reason_community_lic_withdrawal1` FOREIGN KEY (`community_lic_withdrawal_id`) REFERENCES `community_lic_withdrawal` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_community_lic_withdrawal_r1` FOREIGN KEY (`reason_id`) REFERENCES `community_lic_withdrawal_reason_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Reasons for com lic withdrawal.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_withdrawal_reason`
--

LOCK TABLES `community_lic_withdrawal_reason` WRITE;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_lic_withdrawal_reason_type`
--

DROP TABLE IF EXISTS `community_lic_withdrawal_reason_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_lic_withdrawal_reason_type` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_community_lic_withdrawal_reason_type_user1_idx` (`created_by`),
  KEY `fk_community_lic_withdrawal_reason_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_community_lic_withdrawal_reason_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_lic_withdrawal_reason_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Possibly community licence withdrawal reasons.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_lic_withdrawal_reason_type`
--

LOCK TABLES `community_lic_withdrawal_reason_type` WRITE;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_lic_withdrawal_reason_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies_house_request`
--

DROP TABLE IF EXISTS `companies_house_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies_house_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requested_on` datetime DEFAULT NULL,
  `request_type` varchar(255) DEFAULT NULL,
  `request_error` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Logging of companies house interface requests.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) DEFAULT NULL,
  `company_no` varchar(12) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_company_subsidiary_user1_idx` (`created_by`),
  KEY `fk_company_subsidiary_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_company_subsidiary_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_subsidiary`
--

LOCK TABLES `company_subsidiary` WRITE;
/*!40000 ALTER TABLE `company_subsidiary` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_subsidiary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_subsidiary_licence`
--

DROP TABLE IF EXISTS `company_subsidiary_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_subsidiary_licence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_subsidiary_id` int(11) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_subsid_licence_unique` (`company_subsidiary_id`,`licence_id`),
  KEY `fk_company_subsidiary_has_licence_licence1_idx` (`licence_id`),
  KEY `fk_company_subsidiary_has_licence_company_subsidiary1_idx` (`company_subsidiary_id`),
  KEY `fk_company_subsidiary_licence_user1_idx` (`created_by`),
  KEY `fk_company_subsidiary_licence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_company_subsidiary_has_licence_company_subsidiary1` FOREIGN KEY (`company_subsidiary_id`) REFERENCES `company_subsidiary` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_has_licence_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_licence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_subsidiary_licence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_subsidiary_licence`
--

LOCK TABLES `company_subsidiary_licence` WRITE;
/*!40000 ALTER TABLE `company_subsidiary_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_subsidiary_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint`
--

DROP TABLE IF EXISTS `complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `is_compliance` tinyint(1) NOT NULL DEFAULT '1',
  `complainant_contact_details_id` int(11) DEFAULT NULL,
  `complaint_date` datetime DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `complaint_type` varchar(32) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `driver_forename` varchar(40) DEFAULT NULL,
  `driver_family_name` varchar(40) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete.',
  `close_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_complaint_contact_details1_idx` (`complainant_contact_details_id`),
  KEY `fk_complaint_user1_idx` (`created_by`),
  KEY `fk_complaint_user2_idx` (`last_modified_by`),
  KEY `fk_complaint_ref_data1_idx` (`status`),
  KEY `fk_complaint_ref_data2_idx` (`complaint_type`),
  KEY `fk_complaint_cases1_idx` (`case_id`),
  CONSTRAINT `fk_complaint_contact_details1` FOREIGN KEY (`complainant_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_ref_data1` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_ref_data2` FOREIGN KEY (`complaint_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint`
--

LOCK TABLES `complaint` WRITE;
/*!40000 ALTER TABLE `complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint_oc_licence`
--

DROP TABLE IF EXISTS `complaint_oc_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaint_oc_licence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_complaint_oc_licence_licence1_idx` (`licence_id`),
  KEY `fk_complaint_oc_licence_complaint1_idx` (`complaint_id`),
  KEY `fk_complaint_oc_licence_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_complaint_oc_licence_user1_idx` (`created_by`),
  KEY `fk_complaint_oc_licence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_complaint_oc_licence_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_oc_licence_complaint1` FOREIGN KEY (`complaint_id`) REFERENCES `complaint` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_oc_licence_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_oc_licence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_complaint_oc_licence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint_oc_licence`
--

LOCK TABLES `complaint_oc_licence` WRITE;
/*!40000 ALTER TABLE `complaint_oc_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaint_oc_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condition_undertaking`
--

DROP TABLE IF EXISTS `condition_undertaking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condition_undertaking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `operating_centre_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `lic_condition_variation_id` int(11) DEFAULT NULL COMMENT 'The condition on linked to the licence that is being changed by the application condition. Changes applied when application is granted.',
  `condition_type` varchar(32) NOT NULL COMMENT 'Condition or Undertaking',
  `added_via` varchar(32) DEFAULT NULL COMMENT 'Episode, Application or Licence',
  `action` varchar(1) DEFAULT NULL COMMENT 'For application conditions A for add and U for update, if updating a licence condition via an app.',
  `attached_to` varchar(32) DEFAULT NULL COMMENT 'Licence or Operating Centre',
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `is_fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `approval_user_id` int(11) DEFAULT NULL,
  `notes` varchar(8000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_Condition_ref_data1_idx` (`added_via`),
  KEY `fk_Condition_ref_data2_idx` (`attached_to`),
  KEY `fk_Condition_ref_data3_idx` (`condition_type`),
  KEY `fk_Condition_cases1_idx` (`case_id`),
  KEY `fk_Condition_licence1_idx` (`licence_id`),
  KEY `fk_Condition_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_condition_undertaking_application1_idx` (`application_id`),
  KEY `fk_condition_undertaking_user1_idx` (`created_by`),
  KEY `fk_condition_undertaking_user2_idx` (`last_modified_by`),
  KEY `fk_condition_undertaking_condition_undertaking1_idx` (`lic_condition_variation_id`),
  KEY `fk_condition_undertaking_user3_idx` (`approval_user_id`),
  CONSTRAINT `fk_Condition_ref_data1` FOREIGN KEY (`added_via`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Condition_ref_data2` FOREIGN KEY (`attached_to`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Condition_ref_data3` FOREIGN KEY (`condition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Condition_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Condition_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Condition_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_condition_undertaking1` FOREIGN KEY (`lic_condition_variation_id`) REFERENCES `condition_undertaking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_condition_undertaking_user3` FOREIGN KEY (`approval_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` INT NOT NULL AUTO_INCREMENT,
  `contact_type` VARCHAR(32) NOT NULL,
  `email_address` VARCHAR(60) NULL,
  `fao` VARCHAR(90) NULL,
  `description` VARCHAR(255) NULL,
  `address_id` INT NULL,
  `person_id` INT NULL,
  `forename` VARCHAR(40) NULL,
  `family_name` VARCHAR(40) NULL,
  `written_permission_to_engage` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_date` DATETIME NULL,
  `created_by` INT NULL,
  `last_modified_by` INT NULL,
  `created_on` DATETIME NULL,
  `last_modified_on` DATETIME NULL,
  `version` INT NOT NULL DEFAULT 1,
  `olbs_key` INT NULL,
  `olbs_type` VARCHAR(32) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_contact_details_person1_idx` (`person_id` ASC),
  INDEX `fk_contact_details_address1_idx` (`address_id` ASC),
  INDEX `fk_contact_details_user1_idx` (`created_by` ASC),
  INDEX `fk_contact_details_user2_idx` (`last_modified_by` ASC),
  INDEX `fk_contact_details_ref_data1_idx` (`contact_type` ASC),
  UNIQUE INDEX `uk_olbs_key_etl` (`olbs_key` ASC, `olbs_type` ASC),
  CONSTRAINT `fk_contact_details_person1`
    FOREIGN KEY (`person_id`)
    REFERENCES `person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_address1`
    FOREIGN KEY (`address_id`)
    REFERENCES `address` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_user1`
    FOREIGN KEY (`created_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_user2`
    FOREIGN KEY (`last_modified_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_details_ref_data1`
    FOREIGN KEY (`contact_type`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defendant_type` varchar(32) NOT NULL,
  `offence_date` datetime DEFAULT NULL,
  `conviction_date` datetime DEFAULT NULL,
  `court` varchar(70) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `costs` varchar(255) DEFAULT NULL COMMENT 'New olcs field?',
  `msi` tinyint(1) DEFAULT NULL,
  `is_dealt_with` tinyint(1) NOT NULL DEFAULT '0',
  `is_declared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Declared to TC',
  `operator_name` varchar(70) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `person_firstname` varchar(70) DEFAULT NULL,
  `person_lastname` varchar(70) DEFAULT NULL COMMENT 'Length 70 because of ETL. Will hold some org names from legacy data.',
  `notes` varchar(4000) DEFAULT NULL,
  `taken_into_consideration` varchar(4000) DEFAULT NULL,
  `category_text` varchar(1024) DEFAULT NULL COMMENT 'user entered category for non act',
  `conviction_category` varchar(32) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `case_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_conviction_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_conviction_user1_idx` (`created_by`),
  KEY `fk_conviction_user2_idx` (`last_modified_by`),
  KEY `fk_conviction_operator_case1_idx` (`case_id`),
  KEY `fk_conviction_ref_data1_idx` (`defendant_type`),
  KEY `fk_conviction_ref_data2_idx` (`conviction_category`),
  CONSTRAINT `fk_conviction_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_ref_data1` FOREIGN KEY (`defendant_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conviction_ref_data2` FOREIGN KEY (`conviction_category`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `archived` tinyint(1) DEFAULT NULL,
  `accessed` tinyint(1) DEFAULT NULL,
  `email_reminder_sent` tinyint(1) DEFAULT NULL,
  `printed` tinyint(1) DEFAULT NULL,
  `document_id` int(11) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_correspondence_inbox_document1_idx` (`document_id`),
  KEY `fk_correspondence_inbox_licence1_idx` (`licence_id`),
  KEY `fk_correspondence_inbox_user1_idx` (`created_by`),
  KEY `fk_correspondence_inbox_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_correspondence_inbox_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_correspondence_inbox_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `country_desc` varchar(200) DEFAULT NULL,
  `is_member_state` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_country_user1_idx` (`created_by`),
  KEY `fk_country_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_country_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_country_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_decision_user1_idx` (`created_by`),
  KEY `fk_decision_user2_idx` (`last_modified_by`),
  KEY `fk_decision_ref_data1_idx` (`goods_or_psv`),
  CONSTRAINT `fk_decision_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_decision_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_decision_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `goods_or_psv` varchar(32) NOT NULL,
  `restricted` int(11) DEFAULT NULL,
  `special_restricted` int(11) DEFAULT NULL,
  `standard_national` int(11) DEFAULT NULL,
  `standard_international` int(11) DEFAULT NULL,
  `r_prefix` varchar(3) DEFAULT NULL,
  `sr_prefix` varchar(3) DEFAULT NULL,
  `sn_prefix` varchar(3) DEFAULT NULL,
  `si_prefix` varchar(3) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  `is_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_disc_sequence_ref_data1_idx` (`goods_or_psv`),
  KEY `fk_disc_sequence_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_disc_sequence_user1_idx` (`created_by`),
  KEY `fk_disc_sequence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_disc_sequence_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disc_sequence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_disqualified` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `period` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_disqualification_person1_idx` (`person_id`),
  KEY `fk_disqualification_organisation1_idx` (`organisation_id`),
  KEY `fk_disqualification_user1_idx` (`created_by`),
  KEY `fk_disqualification_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_disqualification_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disqualification_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_doc_bookmark_user1_idx` (`created_by`),
  KEY `fk_doc_bookmark_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_doc_bookmark_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_bookmark_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `para_title` varchar(255) NOT NULL,
  `para_text` varchar(1000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_doc_paragraph_user1_idx` (`created_by`),
  KEY `fk_doc_paragraph_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_doc_paragraph_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_bookmark_id` int(11) NOT NULL,
  `doc_paragraph_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_paragraph_bookmark_unique` (`doc_bookmark_id`,`doc_paragraph_id`),
  KEY `fk_doc_paragraph_bookmark_doc_paragraph1_idx` (`doc_paragraph_id`),
  KEY `fk_doc_paragraph_bookmark_user1_idx` (`created_by`),
  KEY `fk_doc_paragraph_bookmark_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_doc_paragraph_bookmark_doc_bookmark1` FOREIGN KEY (`doc_bookmark_id`) REFERENCES `doc_bookmark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_doc_paragraph1` FOREIGN KEY (`doc_paragraph_id`) REFERENCES `doc_paragraph` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_paragraph_bookmark_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_category_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `document_id` int(11) NOT NULL,
  `is_ni` tinyint(1) NOT NULL DEFAULT '0',
  `suppress_from_op` tinyint(1) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_doc_template_document_sub_category1_idx` (`sub_category_id`),
  KEY `fk_doc_template_document1_idx` (`document_id`),
  KEY `fk_doc_template_user1_idx` (`created_by`),
  KEY `fk_doc_template_user2_idx` (`last_modified_by`),
  KEY `fk_doc_template_document_category1_idx` (`category_id`),
  CONSTRAINT `fk_doc_template_document_sub_category1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_document_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_template_id` int(11) NOT NULL,
  `doc_bookmark_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_template_bookmark_unique` (`doc_template_id`,`doc_bookmark_id`),
  KEY `fk_doc_template_bookmark_doc_bookmark1_idx` (`doc_bookmark_id`),
  KEY `fk_doc_template_bookmark_user1_idx` (`created_by`),
  KEY `fk_doc_template_bookmark_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_doc_template_bookmark_doc_template1` FOREIGN KEY (`doc_template_id`) REFERENCES `doc_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_doc_bookmark1` FOREIGN KEY (`doc_bookmark_id`) REFERENCES `doc_bookmark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_doc_template_bookmark_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_store_id` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `is_read_only` tinyint(1) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `irfo_organisation_id` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `operating_centre_id` int(11) DEFAULT NULL,
  `opposition_id` int(11) DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `is_digital` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag true if doc was received or sent via email',
  `is_scan` tinyint(1) NOT NULL DEFAULT '0',
  `file_extension` varchar(32) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_document_ref_data1_idx` (`file_extension`),
  KEY `fk_document_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_document_document_category1_idx` (`category_id`),
  KEY `fk_document_document_sub_category1_idx` (`sub_category_id`),
  KEY `fk_document_licence1_idx` (`licence_id`),
  KEY `fk_document_application1_idx` (`application_id`),
  KEY `fk_document_cases1_idx` (`case_id`),
  KEY `fk_document_irfo_organisation1_idx` (`irfo_organisation_id`),
  KEY `fk_document_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_document_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_document_user1_idx` (`created_by`),
  KEY `fk_document_user2_idx` (`last_modified_by`),
  KEY `fk_document_opposition1_idx` (`opposition_id`),
  KEY `fk_document_bus_reg1_idx` (`bus_reg_id`),
  CONSTRAINT `fk_document_ref_data1_idx` FOREIGN KEY (`file_extension`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_document_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_document_sub_category1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_irfo_organisation1` FOREIGN KEY (`irfo_organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_opposition1` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exception_name` varchar(45) DEFAULT NULL,
  `scale` int(11) NOT NULL DEFAULT '0',
  `published_timestamp` datetime DEFAULT NULL,
  `requested_timestamp` datetime NOT NULL,
  `bus_reg_id` int(11) NOT NULL,
  `requested_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebsr_route_reprint_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_ebsr_route_reprint_user1_idx` (`requested_user_id`),
  CONSTRAINT `fk_ebsr_route_reprint_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_route_reprint_user1` FOREIGN KEY (`requested_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebsr_submission_status_id` varchar(32) NOT NULL,
  `ebsr_submission_type_id` varchar(32) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `submitted_date` datetime DEFAULT NULL,
  `licence_no` varchar(7) DEFAULT NULL,
  `organisation_email_address` varchar(100) DEFAULT NULL,
  `application_classification` varchar(32) DEFAULT NULL,
  `variation_no` int(11) DEFAULT NULL,
  `tan_code` varchar(2) DEFAULT NULL,
  `registration_no` varchar(4) DEFAULT NULL,
  `validation_start` datetime DEFAULT NULL,
  `validation_end` datetime DEFAULT NULL,
  `publish_start` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `process_start` datetime DEFAULT NULL,
  `process_end` datetime DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `ebsr_submission_result` varchar(64) DEFAULT NULL,
  `distribute_start` datetime DEFAULT NULL,
  `distribute_end` datetime DEFAULT NULL,
  `distribute_expire` datetime DEFAULT NULL,
  `is_from_ftp` tinyint(1) NOT NULL DEFAULT '0',
  `organisation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebsr_submission_document1_idx` (`document_id`),
  KEY `fk_ebsr_submission_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_ebsr_submission_ref_data1_idx` (`ebsr_submission_status_id`),
  KEY `fk_ebsr_submission_ref_data2_idx` (`ebsr_submission_type_id`),
  CONSTRAINT `fk_ebsr_submission_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_ref_data1` FOREIGN KEY (`ebsr_submission_status_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebsr_submission_ref_data2` FOREIGN KEY (`ebsr_submission_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebsr_submission`
--

LOCK TABLES `ebsr_submission` WRITE;
/*!40000 ALTER TABLE `ebsr_submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebsr_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `added_date` datetime DEFAULT NULL,
  `deferred_date` datetime DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `importance` smallint(6) DEFAULT NULL,
  `is_sensitive` tinyint(1) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_email_user1_idx` (`created_by`),
  KEY `fk_email_user2_idx` (`last_updated_by`),
  CONSTRAINT `fk_email_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_email_user2` FOREIGN KEY (`last_updated_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email`
--

LOCK TABLES `email` WRITE;
/*!40000 ALTER TABLE `email` DISABLE KEYS */;
/*!40000 ALTER TABLE `email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_attachment`
--

DROP TABLE IF EXISTS `email_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_attachment` (
  `email_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  PRIMARY KEY (`email_id`,`document_id`),
  KEY `fk_email_attachment_email1_idx` (`email_id`),
  KEY `fk_email_attachment_document1_idx` (`document_id`),
  CONSTRAINT `fk_email_attachment_email1` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_email_attachment_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_attachment`
--

LOCK TABLES `email_attachment` WRITE;
/*!40000 ALTER TABLE `email_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_body`
--

DROP TABLE IF EXISTS `email_body`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_body` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `text` varchar(8000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_email_body_email1_idx` (`email_id`),
  CONSTRAINT `fk_email_body_email1` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_body`
--

LOCK TABLES `email_body` WRITE;
/*!40000 ALTER TABLE `email_body` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_body` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_recipient`
--

DROP TABLE IF EXISTS `email_recipient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_recipient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_email_recipient_email1_idx` (`email_id`),
  CONSTRAINT `fk_email_recipient_email1` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_recipient`
--

LOCK TABLES `email_recipient` WRITE;
/*!40000 ALTER TABLE `email_recipient` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_recipient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enforcement_area`
--

DROP TABLE IF EXISTS `enforcement_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enforcement_area` (
  `id` varchar(4) NOT NULL,
  `name` varchar(70) NOT NULL,
  `email_address` varchar(60) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_enforcement_area_user1_idx` (`created_by`),
  KEY `fk_enforcement_area_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_enforcement_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_enforcement_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `event_history_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `licence_vehicle_id` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `event_datetime` datetime NOT NULL,
  `event_description` varchar(255) DEFAULT NULL,
  `entity_type` varchar(45) DEFAULT NULL,
  `entity_pk` int(11) DEFAULT NULL,
  `entity_version` int(11) DEFAULT NULL,
  `entity_data` text,
  `operation` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_history_event_history_type1_idx` (`event_history_type_id`),
  KEY `fk_event_history_user1_idx` (`user_id`),
  KEY `fk_event_history_licence1_idx` (`licence_id`),
  KEY `fk_event_history_application1_idx` (`application_id`),
  KEY `fk_event_history_licence_vehicle1_idx` (`licence_vehicle_id`),
  KEY `fk_event_history_team1_idx` (`team_id`),
  KEY `fk_event_history_transport_manager1_idx` (`transport_manager_id`),
  CONSTRAINT `fk_event_history_event_history_type1` FOREIGN KEY (`event_history_type_id`) REFERENCES `event_history_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_licence_vehicle1` FOREIGN KEY (`licence_vehicle_id`) REFERENCES `licence_vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_history_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `event_type` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Also used as the invoice number.',
  `fee_type_id` int(11) NOT NULL,
  `fee_status` varchar(32) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `received_amount` decimal(10,2) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `invoice_line_no` int(11) DEFAULT NULL,
  `invoiced_date` datetime DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `receipt_no` varchar(45) DEFAULT NULL,
  `parent_fee_id` int(11) DEFAULT NULL,
  `waive_approval_date` datetime DEFAULT NULL,
  `waive_reason` varchar(255) DEFAULT NULL,
  `waive_recommendation_date` datetime DEFAULT NULL,
  `waive_recommender_user_id` int(11) DEFAULT NULL,
  `waive_approver_user_id` int(11) DEFAULT NULL,
  `irfo_fee_id` varchar(10) DEFAULT NULL,
  `irfo_fee_exempt` tinyint(1) DEFAULT NULL,
  `irfo_file_no` varchar(10) DEFAULT NULL,
  `irfo_gv_permit_id` int(11) DEFAULT NULL,
  `payment_method` varchar(32) DEFAULT NULL COMMENT 'The method of the successful payment. There could have been several attempts to pay with differing methods, but only one successful.',
  `payer_name` VARCHAR(100) NULL COMMENT 'Name on cheque or POs',
  `cheque_po_number` VARCHAR(100) NULL,
  `paying_in_slip_number` VARCHAR(100) NULL COMMENT 'Paying in slip from DVSA employee paying cheque or PO into bank.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_fee_application1_idx` (`application_id`),
  KEY `fk_fee_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_fee_licence1_idx` (`licence_id`),
  KEY `fk_fee_task1_idx` (`task_id`),
  KEY `fk_fee_fee_type1_idx` (`fee_type_id`),
  KEY `fk_fee_fee1_idx` (`parent_fee_id`),
  KEY `fk_fee_user1_idx` (`waive_recommender_user_id`),
  KEY `fk_fee_user2_idx` (`waive_approver_user_id`),
  KEY `fk_fee_user3_idx` (`created_by`),
  KEY `fk_fee_user4_idx` (`last_modified_by`),
  KEY `fk_fee_irfo_gv_permit1_idx` (`irfo_gv_permit_id`),
  KEY `fk_fee_ref_data1_idx` (`fee_status`),
  KEY `fk_fee_ref_data2_idx` (`payment_method`),
  CONSTRAINT `fk_fee_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_fee_type1` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_fee1` FOREIGN KEY (`parent_fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_user1` FOREIGN KEY (`waive_recommender_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_user2` FOREIGN KEY (`waive_approver_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_user3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_user4` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_irfo_gv_permit1` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_ref_data1` FOREIGN KEY (`fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_ref_data2` FOREIGN KEY (`payment_method`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
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
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_fee_manual_alteration_fee1_idx` (`fee_id`),
  KEY `fk_fee_manual_alteration_ref_data1_idx` (`alteration_type`),
  KEY `fk_fee_manual_alteration_ref_data2_idx` (`post_fee_status`),
  KEY `fk_fee_manual_alteration_ref_data3_idx` (`pre_fee_status`),
  KEY `fk_fee_manual_alteration_user1_idx` (`user_id`),
  CONSTRAINT `fk_fee_manual_alteration_fee1` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_ref_data1` FOREIGN KEY (`alteration_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_ref_data2` FOREIGN KEY (`post_fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_ref_data3` FOREIGN KEY (`pre_fee_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_manual_alteration_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `fee_value` decimal(10,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_payment_unique` (`fee_id`,`payment_id`),
  KEY `fk_fee_has_payment_payment1_idx` (`payment_id`),
  KEY `fk_fee_has_payment_fee1_idx` (`fee_id`),
  KEY `fk_fee_payment_user1_idx` (`created_by`),
  KEY `fk_fee_payment_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_fee_has_payment_fee1` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_has_payment_payment1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_payment_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_type` varchar(20) NOT NULL,
  `effective_from` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `fixed_value` decimal(10,2) DEFAULT NULL,
  `annual_value` decimal(10,2) DEFAULT NULL,
  `five_year_value` decimal(10,2) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL,
  `goods_or_psv` varchar(32) NOT NULL,
  `expire_fee_with_licence` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Dont allow payment after licence expires',
  `accrual_rule` VARCHAR(32) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_fee_type_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_fee_type_ref_data1_idx` (`licence_type`),
  KEY `fk_fee_type_ref_data2_idx` (`goods_or_psv`),
  KEY `fk_fee_type_ref_data3_idx` (`accrual_rule`),
  KEY `fk_fee_type_user1_idx` (`created_by`),
  KEY `fk_fee_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_fee_type_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_ref_data1` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_ref_data2` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_ref_data3` FOREIGN KEY (`accrual_rule`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fee_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_type`
--

LOCK TABLES `fee_type` WRITE;
/*!40000 ALTER TABLE `fee_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_disc`
--

DROP TABLE IF EXISTS `goods_disc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_disc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_vehicle_id` int(11) NOT NULL,
  `disc_no` varchar(50) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `ceased_date` datetime DEFAULT NULL,
  `is_copy` tinyint(1) NOT NULL DEFAULT '0',
  `is_interim` tinyint(1) NOT NULL DEFAULT '0',
  `reprint_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_printing` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_goods_disc_licence_vehicle1_idx` (`licence_vehicle_id`),
  KEY `fk_goods_disc_user1_idx` (`created_by`),
  KEY `fk_goods_disc_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_goods_disc_licence_vehicle1` FOREIGN KEY (`licence_vehicle_id`) REFERENCES `licence_vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_goods_disc_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_goods_disc_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL COMMENT 'Period can start on a future date.',
  `end_date` date NOT NULL,
  `grace_period_no` int(11) NOT NULL DEFAULT '1' COMMENT 'Grace period number for the licence. Starts at 1.',
  `assigned_to_user_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `period_type` varchar(32) NOT NULL COMMENT 'Either TM, financial standing or both.',
  PRIMARY KEY (`id`),
  KEY `fk_transport_manager_grace_period_licence1_idx` (`licence_id`),
  KEY `fk_transport_manager_grace_period_user1_idx` (`assigned_to_user_id`),
  KEY `fk_transport_manager_grace_period_user2_idx` (`created_by`),
  KEY `fk_transport_manager_grace_period_user3_idx` (`last_modified_by`),
  KEY `fk_grace_period_ref_data1_idx` (`period_type`),
  CONSTRAINT `fk_transport_manager_grace_period_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_grace_period_user1` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_grace_period_user2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_grace_period_user3` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_grace_period_ref_data1` FOREIGN KEY (`period_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A period when a licence has no TM or financial standing info.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hearing_type` varchar(32) NOT NULL,
  `case_id` int(11) NOT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `venue_other` varchar(255) DEFAULT NULL,
  `presiding_tc_id` int(11) DEFAULT NULL,
  `hearing_date` datetime DEFAULT NULL,
  `agreed_by_tc_date` date DEFAULT NULL,
  `witness_count` int(11) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_hearing_hearing_type_idx` (`hearing_type`),
  KEY `fk_hearing_cases1_idx` (`case_id`),
  KEY `fk_hearing_pi_venue1_idx` (`venue_id`),
  KEY `fk_hearing_user1_idx` (`created_by`),
  KEY `fk_hearing_user2_idx` (`last_modified_by`),
  KEY `fk_hearing_presiding_tc1_idx` (`presiding_tc_id`),
  CONSTRAINT `fk_hearing_hearing_type` FOREIGN KEY (`hearing_type`) REFERENCES `ref_data` (`id`),
  CONSTRAINT `fk_hearing_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_pi_venue1` FOREIGN KEY (`venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hearing_presiding_tc1` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_no` int(11) NOT NULL COMMENT 'Split questions into groups to force picking one from each.',
  `hint_question` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_hint_questions_user1_idx` (`created_by`),
  KEY `fk_hint_questions_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_hint_questions_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hint_questions_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `impounding_type` varchar(32) NOT NULL,
  `case_id` int(11) NOT NULL,
  `hearing_date` datetime DEFAULT NULL,
  `application_receipt_date` datetime DEFAULT NULL,
  `outcome_sent_date` datetime DEFAULT NULL,
  `presiding_tc_id` int(11) DEFAULT NULL,
  `outcome` varchar(32) DEFAULT NULL COMMENT 'Vehicle(s) returned or not returned',
  `notes` varchar(4000) DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `pi_venue_id` int(11) DEFAULT NULL,
  `pi_venue_other` varchar(255) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_impounding_user1_idx` (`created_by`),
  KEY `fk_impounding_user2_idx` (`last_modified_by`),
  KEY `fk_impounding_transport_commissioner1_idx` (`presiding_tc_id`),
  KEY `fk_impounding_ref_data1_idx` (`outcome`),
  KEY `fk_impounding_ref_data2_idx` (`impounding_type`),
  KEY `fk_impounding_cases1_idx` (`case_id`),
  KEY `fk_impounding_pi_venue1_idx` (`pi_venue_id`),
  CONSTRAINT `fk_impounding_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_transport_commissioner1` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_ref_data1` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_ref_data2` FOREIGN KEY (`impounding_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_pi_venue1` FOREIGN KEY (`pi_venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `impounding_id` int(11) NOT NULL,
  `impounding_legislation_type_id` varchar(32) NOT NULL,
  PRIMARY KEY (`impounding_id`,`impounding_legislation_type_id`),
  KEY `fk_impounding_has_ref_data_ref_data1_idx` (`impounding_legislation_type_id`),
  KEY `fk_impounding_has_ref_data_impounding1_idx` (`impounding_id`),
  CONSTRAINT `fk_impounding_has_ref_data_impounding1` FOREIGN KEY (`impounding_id`) REFERENCES `impounding` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_impounding_has_ref_data_ref_data1` FOREIGN KEY (`impounding_legislation_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(1024) NOT NULL,
  `message_body` mediumtext,
  `email_status` varchar(1) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `inspection_request_id` int(11) NOT NULL,
  `sender_email_address` varchar(200) DEFAULT NULL,
  `received_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ea_inspection_email_inspection_request1_idx` (`inspection_request_id`),
  CONSTRAINT `fk_ea_inspection_email_inspection_request1` FOREIGN KEY (`inspection_request_id`) REFERENCES `inspection_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
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
  `request_type` varchar(32) NOT NULL,
  `result_type` varchar(32) NOT NULL,
  `local_services_no` int(11) DEFAULT NULL,
  `trailors_examined_no` int(11) DEFAULT NULL,
  `vehicles_examined_no` int(11) DEFAULT NULL,
  `requestor_user_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_inspection_request_licence1_idx` (`licence_id`),
  KEY `fk_inspection_request_application1_idx` (`application_id`),
  KEY `fk_inspection_request_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_inspection_request_task1_idx` (`task_id`),
  KEY `fk_inspection_request_cases1_idx` (`case_id`),
  KEY `fk_inspection_request_ref_data1_idx` (`report_type`),
  KEY `fk_inspection_request_ref_data2_idx` (`request_type`),
  KEY `fk_inspection_request_ref_data3_idx` (`result_type`),
  KEY `fk_inspection_request_user1_idx` (`requestor_user_id`),
  KEY `fk_inspection_request_user2_idx` (`created_by`),
  KEY `fk_inspection_request_user3_idx` (`last_modified_by`),
  CONSTRAINT `fk_inspection_request_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_ref_data1` FOREIGN KEY (`report_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_ref_data2` FOREIGN KEY (`request_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_ref_data3` FOREIGN KEY (`result_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_user1` FOREIGN KEY (`requestor_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_user2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_inspection_request_user3` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_country_user1_idx` (`created_by`),
  KEY `fk_irfo_country_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_country_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_country_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) NOT NULL,
  `exemption_details` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_fee_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `in_force_date` date DEFAULT NULL,
  `irfo_fee_id` varchar(10) DEFAULT NULL,
  `no_of_copies` int(11) NOT NULL DEFAULT '0',
  `note` varchar(2000) DEFAULT NULL,
  `permit_printed` tinyint(1) NOT NULL DEFAULT '0',
  `irfo_gv_permit_type_id` int(11) NOT NULL,
  `year_required` int(11) DEFAULT NULL,
  `irfo_permit_status` varchar(32) NOT NULL,
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_gv_permit_user1_idx` (`created_by`),
  KEY `fk_irfo_gv_permit_user2_idx` (`last_modified_by`),
  KEY `fk_irfo_gv_permit_organisation1_idx` (`organisation_id`),
  KEY `fk_irfo_gv_permit_irfo_gv_permit_type1_idx` (`irfo_gv_permit_type_id`),
  KEY `fk_irfo_gv_permit_ref_data1_idx` (`irfo_permit_status`),
  KEY `fk_irfo_gv_permit_ref_data2_idx` (`withdrawn_reason`),
  CONSTRAINT `fk_irfo_gv_permit_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_irfo_gv_permit_type1` FOREIGN KEY (`irfo_gv_permit_type_id`) REFERENCES `irfo_gv_permit_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_ref_data1` FOREIGN KEY (`irfo_permit_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_ref_data2` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `irfo_country_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_gv_permit_type_irfo_country1_idx` (`irfo_country_id`),
  KEY `fk_irfo_gv_permit_type_user1_idx` (`created_by`),
  KEY `fk_irfo_gv_permit_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_gv_permit_type_irfo_country1` FOREIGN KEY (`irfo_country_id`) REFERENCES `irfo_country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_gv_permit_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) DEFAULT NULL,
  `irfo_psv_auth_id` int(11) DEFAULT NULL,
  `name` varchar(70) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_partner_organisation1_idx` (`organisation_id`),
  KEY `fk_irfo_partner_irfo_psv_auth1_idx` (`irfo_psv_auth_id`),
  KEY `fk_irfo_partner_user1_idx` (`created_by`),
  KEY `fk_irfo_partner_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_partner_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_irfo_psv_auth1` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_partner_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `irfo_gv_permit_id` int(11) DEFAULT NULL,
  `serial_no` int(11) NOT NULL,
  `irfo_country_id` int(11) NOT NULL,
  `valid_for_year` int(11) NOT NULL,
  `status` varchar(32) NOT NULL,
  `void_return_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_permit_stock_irfo_gv_permit1_idx` (`irfo_gv_permit_id`),
  KEY `fk_irfo_permit_stock_irfo_country1_idx` (`irfo_country_id`),
  KEY `fk_irfo_permit_stock_ref_data1_idx` (`status`),
  KEY `fk_irfo_permit_stock_user1_idx` (`created_by`),
  KEY `fk_irfo_permit_stock_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_permit_stock_irfo_gv_permit1` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_irfo_country1` FOREIGN KEY (`irfo_country_id`) REFERENCES `irfo_country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_ref_data1` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_permit_stock_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) NOT NULL,
  `exemption_details` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_fee_exempt_application` tinyint(1) NOT NULL DEFAULT '0',
  `is_fee_exempt_annual` tinyint(1) NOT NULL DEFAULT '0',
  `in_force_date` date DEFAULT NULL,
  `irfo_fee_id` varchar(10) NOT NULL,
  `irfo_file_no` varchar(10) NOT NULL,
  `copies_issued` int(11) NOT NULL DEFAULT '0',
  `copies_required` int(11) NOT NULL DEFAULT '0',
  `copies_required_total` int(11) NOT NULL DEFAULT '0',
  `copies_issued_total` int(11) NOT NULL DEFAULT '0',
  `journey_frequency` varchar(32) DEFAULT NULL,
  `last_date_copies_req` datetime DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `service_route_from` varchar(30) NOT NULL,
  `service_route_to` varchar(30) NOT NULL,
  `irfo_psv_auth_type_id` int(11) NOT NULL,
  `status` varchar(32) NOT NULL,
  `validity_period` int(11) NOT NULL,
  `withdrawn_reason` varchar(32) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_psv_auth_user1_idx` (`created_by`),
  KEY `fk_irfo_psv_auth_user2_idx` (`last_modified_by`),
  KEY `fk_irfo_psv_auth_organisation1_idx` (`organisation_id`),
  KEY `fk_irfo_psv_auth_ref_data1_idx` (`journey_frequency`),
  KEY `fk_irfo_psv_auth_irfo_psv_auth_type1_idx` (`irfo_psv_auth_type_id`),
  KEY `fk_irfo_psv_auth_ref_data2_idx` (`status`),
  KEY `fk_irfo_psv_auth_ref_data3_idx` (`withdrawn_reason`),
  CONSTRAINT `fk_irfo_psv_auth_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_ref_data1` FOREIGN KEY (`journey_frequency`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_irfo_psv_auth_type1` FOREIGN KEY (`irfo_psv_auth_type_id`) REFERENCES `irfo_psv_auth_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_ref_data2` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_ref_data3` FOREIGN KEY (`withdrawn_reason`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_psv_auth_type_user1_idx` (`created_by`),
  KEY `fk_irfo_psv_auth_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_psv_auth_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_psv_auth_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(45) NOT NULL,
  `irfo_psv_auth_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_irfo_transit_country_irfo_psv_auth1_idx` (`irfo_psv_auth_id`),
  KEY `fk_irfo_transit_country_user1_idx` (`created_by`),
  KEY `fk_irfo_transit_country_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_irfo_transit_country_irfo_psv_auth1` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_transit_country_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_transit_country_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coc_a` tinyint(1) NOT NULL DEFAULT '0',
  `coc_b` tinyint(1) NOT NULL DEFAULT '0',
  `coc_c` tinyint(1) NOT NULL DEFAULT '0',
  `coc_d` tinyint(1) NOT NULL DEFAULT '0',
  `coc_t` tinyint(1) NOT NULL DEFAULT '0',
  `vrm` varchar(20) NOT NULL,
  `irfo_psv_auth_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) DEFAULT '1',
  `irfo_gv_permit_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_irfo_vehicle_irfo_psv_auth1_idx` (`irfo_psv_auth_id`),
  KEY `fk_irfo_vehicle_user1_idx` (`created_by`),
  KEY `fk_irfo_vehicle_user2_idx` (`last_modified_by`),
  KEY `fk_irfo_vehicle_irfo_gv_permit1_idx` (`irfo_gv_permit_id`),
  CONSTRAINT `fk_irfo_vehicle_irfo_psv_auth1` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_irfo_vehicle_irfo_gv_permit1` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  `is_driver` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_case_action`
--

LOCK TABLES `legacy_case_action` WRITE;
/*!40000 ALTER TABLE `legacy_case_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_case_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_case_offence`
--

DROP TABLE IF EXISTS `legacy_case_offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_case_offence` (
  `case_id` int(11) NOT NULL,
  `legacy_offence_id` int(11) NOT NULL,
  PRIMARY KEY (`case_id`,`legacy_offence_id`),
  KEY `fk_legacy_case_offence_cases1_idx` (`case_id`),
  KEY `fk_legacy_case_offence_legacy_offence1_idx` (`legacy_offence_id`),
  CONSTRAINT `fk_legacy_case_offence_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_case_offence_legacy_offence1` FOREIGN KEY (`legacy_offence_id`) REFERENCES `legacy_offence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_case_offence`
--

LOCK TABLES `legacy_case_offence` WRITE;
/*!40000 ALTER TABLE `legacy_case_offence` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_case_offence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_offence`
--

DROP TABLE IF EXISTS `legacy_offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_offence` (
  `id` int(11) NOT NULL,
  `definition` varchar(1000) DEFAULT NULL,
  `is_trailer` tinyint(1) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `num_of_offences` int(11) DEFAULT NULL,
  `offence_authority` varchar(100) DEFAULT NULL,
  `offence_date` date DEFAULT NULL,
  `offence_to_date` date DEFAULT NULL,
  `offender_name` varchar(100) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `offence_type` varchar(100) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_legacy_offence_user1_idx` (`created_by`),
  KEY `fk_legacy_offence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_legacy_offence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_offence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `goods_or_psv` varchar(3) NOT NULL COMMENT 'GV or PSV',
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL COMMENT 'Northern Ireland or not',
  `is_decision` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_reason_user1_idx` (`created_by`),
  KEY `fk_pi_reason_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_pi_reason_user10` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reason_user20` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rec_date` datetime NOT NULL,
  `case_id` int(11) NOT NULL,
  `pi_reason` varchar(255) DEFAULT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `comment` varchar(4000) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `notes` text,
  `pi_decision` varchar(255) DEFAULT NULL,
  `request` varchar(20) DEFAULT NULL,
  `revoke_lic` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `total_points` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_case_recommendation_cases1_idx` (`case_id`),
  KEY `fk_case_recommendation_user1_idx` (`from_user_id`),
  KEY `fk_case_recommendation_user2_idx` (`to_user_id`),
  KEY `fk_legacy_recommendation_legacy_case_action1_idx` (`action_id`),
  KEY `fk_legacy_recommendation_user1_idx` (`created_by`),
  KEY `fk_legacy_recommendation_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_case_recommendation_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_recommendation_user1` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_recommendation_user2` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_legacy_case_action1` FOREIGN KEY (`action_id`) REFERENCES `legacy_case_action` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `comment` varchar(30) DEFAULT NULL,
  `legacy_recommendation_id` int(11) NOT NULL,
  `legacy_pi_reason_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_legacy_recommendation_pi_reason_legacy_recommendation1_idx` (`legacy_recommendation_id`),
  KEY `fk_legacy_recommendation_pi_reason_user1_idx` (`created_by`),
  KEY `fk_legacy_recommendation_pi_reason_user2_idx` (`last_modified_by`),
  KEY `fk_legacy_recommendation_pi_reason_legacy_pi_reason1_idx` (`legacy_pi_reason_id`),
  CONSTRAINT `fk_legacy_recommendation_pi_reason_legacy_recommendation1` FOREIGN KEY (`legacy_recommendation_id`) REFERENCES `legacy_recommendation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_pi_reason_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_pi_reason_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_legacy_recommendation_pi_reason_legacy_pi_reason1` FOREIGN KEY (`legacy_pi_reason_id`) REFERENCES `legacy_pi_reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` INT NOT NULL AUTO_INCREMENT,
  `enforcement_area_id` VARCHAR(4) NULL COMMENT 'FK to vehicle_inspectorate.',
  `organisation_id` INT NOT NULL,
  `traffic_area_id` VARCHAR(1) NULL COMMENT 'FK to traffic area.  An Operator can have One licence per area.',
  `correspondence_cd_id` INT NULL COMMENT 'Correspondence contact details',
  `establishment_cd_id` INT NULL COMMENT 'Establishment contact details',
  `transport_consultant_cd_id` INT NULL COMMENT 'Transport consultant contact details',
  `lic_no` VARCHAR(18) NULL COMMENT 'Licence number.  Normally 9 Chars.  First denotes goods/psv, second TA, rest ID.',
  `goods_or_psv` VARCHAR(32) NULL,
  `licence_type` VARCHAR(32) NULL,
  `status` VARCHAR(32) NOT NULL,
  `vi_action` VARCHAR(1) NULL COMMENT 'C, U or D.  Triggers VI export.',
  `tot_auth_trailers` INT NULL,
  `tot_auth_vehicles` INT NULL,
  `tot_auth_small_vehicles` INT NULL,
  `tot_auth_medium_vehicles` INT NULL,
  `tot_auth_large_vehicles` INT NULL,
  `tot_community_licences` INT NULL,
  `trailers_in_possession` INT NULL,
  `fabs_reference` VARCHAR(10) NULL,
  `expiry_date` DATE NULL COMMENT 'expiry date',
  `granted_date` DATETIME NULL COMMENT 'granted date',
  `review_date` DATE NULL,
  `fee_date` DATE NULL,
  `in_force_date` DATE NULL,
  `surrendered_date` DATETIME NULL,
  `safety_ins_trailers` INT NULL COMMENT 'Max period in weeks between safety inspections.',
  `safety_ins_vehicles` INT NULL COMMENT 'Max period in weeks between safety inspections.',
  `safety_ins` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Does own safety inspections.',
  `safety_ins_varies` TINYINT(1) NULL COMMENT 'New olcs column for when some vehicles inspected more often',
  `ni_flag` TINYINT(1) NULL,
  `tachograph_ins` VARCHAR(32) NULL COMMENT 'New olcs column values not applicable, external, internal',
  `tachograph_ins_name` VARCHAR(90) NULL COMMENT 'New olcs column for tachograph inspector',
  `psv_discs_to_be_printed_no` INT NULL,
  `translate_to_welsh` TINYINT(1) NOT NULL DEFAULT 0,
  `is_maintenance_suitable` TINYINT(1) NULL,
  `created_by` INT NULL,
  `last_modified_by` INT NULL,
  `created_on` DATETIME NULL,
  `last_modified_on` DATETIME NULL,
  `deleted_date` datetime DEFAULT NULL,
  `version` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_licence_vehicle_inspectorate1_idx` (`enforcement_area_id` ASC),
  INDEX `fk_licence_traffic_area1_idx` (`traffic_area_id` ASC),
  INDEX `fk_licence_organisation1_idx` (`organisation_id` ASC),
  INDEX `fk_licence_user1_idx` (`created_by` ASC),
  INDEX `fk_licence_user2_idx` (`last_modified_by` ASC),
  INDEX `fk_licence_ref_data1_idx` (`goods_or_psv` ASC),
  INDEX `fk_licence_ref_data2_idx` (`licence_type` ASC),
  INDEX `fk_licence_ref_data3_idx` (`status` ASC),
  INDEX `fk_licence_ref_data4_idx` (`tachograph_ins` ASC),
  UNIQUE INDEX `licence_lic_no_idx` (`lic_no` ASC),
  INDEX `fk_licence_contact_details1_idx` (`correspondence_cd_id` ASC),
  INDEX `fk_licence_contact_details2_idx` (`establishment_cd_id` ASC),
  INDEX `fk_licence_contact_details3_idx` (`transport_consultant_cd_id` ASC),
  CONSTRAINT `fk_licence_vehicle_inspectorate1`
    FOREIGN KEY (`enforcement_area_id`)
    REFERENCES `enforcement_area` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_traffic_area1`
    FOREIGN KEY (`traffic_area_id`)
    REFERENCES `traffic_area` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_organisation1`
    FOREIGN KEY (`organisation_id`)
    REFERENCES `organisation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_user1`
    FOREIGN KEY (`created_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_user2`
    FOREIGN KEY (`last_modified_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_ref_data1`
    FOREIGN KEY (`goods_or_psv`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_ref_data2`
    FOREIGN KEY (`licence_type`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_ref_data3`
    FOREIGN KEY (`status`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_ref_data4`
    FOREIGN KEY (`tachograph_ins`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_contact_details1`
    FOREIGN KEY (`correspondence_cd_id`)
    REFERENCES `contact_details` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_contact_details2`
    FOREIGN KEY (`establishment_cd_id`)
    REFERENCES `contact_details` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_contact_details3`
    FOREIGN KEY (`transport_consultant_cd_id`)
    REFERENCES `contact_details` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_licence_no_gen_licence1_idx` (`licence_id`),
  CONSTRAINT `fk_licence_no_gen_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `ad_placed` tinyint(1) NOT NULL,
  `ad_placed_in` varchar(70) DEFAULT NULL,
  `ad_placed_date` date DEFAULT NULL,
  `sufficient_parking` tinyint(1) NOT NULL,
  `permission` tinyint(1) NOT NULL,
  `no_of_trailers_required` int(11) DEFAULT NULL,
  `no_of_vehicles_required` int(11) DEFAULT NULL,
  `no_of_vehicles_possessed` int(11) DEFAULT NULL,
  `no_of_trailers_possessed` int(11) DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `is_interim` tinyint(1) DEFAULT NULL,
  `publication_appropriate` tinyint(1) DEFAULT NULL,
  `s4_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_LicenceOperatingCentre_licence_idx` (`licence_id`),
  KEY `fk_LicenceOperatingCentre_OperatingCentre1_idx` (`operating_centre_id`),
  KEY `fk_licence_operating_centre_user1_idx` (`created_by`),
  KEY `fk_licence_operating_centre_user2_idx` (`last_modified_by`),
  KEY `fk_licence_operating_centre_s41_idx` (`s4_id`),
  CONSTRAINT `fk_LicenceOperatingCentre_licence` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_LicenceOperatingCentre_OperatingCentre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_operating_centre_s41` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `licence_status` varchar(32) NOT NULL COMMENT 'The status the licence will inherit on the start date',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `start_processed_date` datetime DEFAULT NULL COMMENT 'Date processed by batch job',
  `end_processed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_licence_status_rule_licence1_idx` (`licence_id`),
  KEY `fk_licence_status_rule_ref_data1_idx` (`licence_status`),
  KEY `fk_licence_status_rule_user1_idx` (`created_by`),
  KEY `fk_licence_status_rule_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_licence_status_rule_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_ref_data1` FOREIGN KEY (`licence_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_status_rule_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `interim_application_id` int(11) DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `removal_date` datetime DEFAULT NULL COMMENT 'Date vehicle removed from licence',
  `removal_letter_seed_date` datetime DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `warning_letter_seed_date` datetime DEFAULT NULL,
  `warning_letter_sent_date` datetime DEFAULT NULL,
  `specified_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_licence_vehicle_vehicle1_idx` (`vehicle_id`),
  KEY `fk_licence_vehicle_user1_idx` (`created_by`),
  KEY `fk_licence_vehicle_user2_idx` (`last_modified_by`),
  KEY `fk_licence_vehicle_application1_idx` (`application_id`),
  KEY `fk_licence_vehicle_application2_idx` (`interim_application_id`),
  KEY `fk_licence_vehicle_licence1` (`licence_id`),
  CONSTRAINT `fk_licence_vehicle_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_vehicle1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_application2` FOREIGN KEY (`interim_application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_vehicle_id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_licence_vehicle_fee_fee1_idx` (`fee_id`),
  KEY `fk_licence_vehicle_fee_user1_idx` (`created_by`),
  KEY `fk_licence_vehicle_fee_user2_idx` (`last_modified_by`),
  KEY `fk_licence_vehicle_fee_licence_vehicle1` (`licence_vehicle_id`),
  CONSTRAINT `fk_licence_vehicle_fee_licence_vehicle1` FOREIGN KEY (`licence_vehicle_id`) REFERENCES `licence_vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_fee1` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_vehicle_fee_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `email_address` varchar(45) DEFAULT NULL,
  `txc_name` varchar(255) DEFAULT NULL,
  `naptan_code` char(3) DEFAULT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_local_authority_user1_idx` (`created_by`),
  KEY `fk_local_authority_user2_idx` (`last_modified_by`),
  KEY `fk_local_authority_traffic_area1_idx` (`traffic_area_id`),
  CONSTRAINT `fk_local_authority_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_local_authority_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_local_authority_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(4000) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '0',
  `note_type` varchar(32) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `irfo_gv_permit_id` int(11) DEFAULT NULL,
  `irfo_psv_auth_id` int(11) DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_note_application1_idx` (`application_id`),
  KEY `fk_note_licence1_idx` (`licence_id`),
  KEY `fk_note_case1_idx` (`case_id`),
  KEY `fk_note_irfo_gv_permit1_idx` (`irfo_gv_permit_id`),
  KEY `fk_note_irfo_psv_auth1_idx` (`irfo_psv_auth_id`),
  KEY `fk_note_user1_idx` (`created_by`),
  KEY `fk_note_user2_idx` (`last_modified_by`),
  KEY `fk_note_ref_data1_idx` (`note_type`),
  KEY `fk_note_bus_reg1_idx` (`bus_reg_id`),
  CONSTRAINT `fk_note_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_irfo_gv_permit1` FOREIGN KEY (`irfo_gv_permit_id`) REFERENCES `irfo_gv_permit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_irfo_psv_auth1` FOREIGN KEY (`irfo_psv_auth_id`) REFERENCES `irfo_psv_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_ref_data1` FOREIGN KEY (`note_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note`
--

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;
/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_centre`
--

DROP TABLE IF EXISTS `operating_centre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operating_centre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_OperatingCentre_Address1_idx` (`address_id`),
  KEY `fk_operating_centre_user1_idx` (`created_by`),
  KEY `fk_operating_centre_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_OperatingCentre_Address1` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opposition_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_opposition_operating_centre_opposition1_idx` (`opposition_id`),
  KEY `fk_opposition_operating_centre_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_operating_centre_opposition_user1_idx` (`created_by`),
  KEY `fk_operating_centre_opposition_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_opposition_operating_centre_opposition1` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_operating_centre_operating_centre1` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_opposition_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_operating_centre_opposition_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_details_id` int(11) NOT NULL,
  `opposer_type` varchar(32) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_opposer_contact_details1_idx` (`contact_details_id`),
  KEY `fk_opposer_ref_data1_idx` (`opposer_type`),
  KEY `fk_opposer_user1_idx` (`created_by`),
  KEY `fk_opposer_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_opposer_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_ref_data1` FOREIGN KEY (`opposer_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposer_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `opposer_id` int(11) NOT NULL,
  `opposition_type` varchar(32) NOT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `is_copied` tinyint(1) NOT NULL DEFAULT '0',
  `raised_date` date DEFAULT NULL,
  `is_in_time` tinyint(1) NOT NULL DEFAULT '0',
  `is_public_inquiry` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '0',
  `is_withdrawn` tinyint(1) NOT NULL DEFAULT '0',
  `valid_notes` varchar(4000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_opposition_application1_idx` (`application_id`),
  KEY `fk_opposition_opposer1_idx` (`opposer_id`),
  KEY `fk_opposition_user1_idx` (`created_by`),
  KEY `fk_opposition_user2_idx` (`last_modified_by`),
  KEY `fk_opposition_cases1_idx` (`case_id`),
  KEY `fk_opposition_licence1_idx` (`licence_id`),
  KEY `fk_opposition_ref_data1_idx` (`opposition_type`),
  CONSTRAINT `fk_opposition_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_opposer1` FOREIGN KEY (`opposer_id`) REFERENCES `opposer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_ref_data1` FOREIGN KEY (`opposition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opposition_id` int(11) NOT NULL,
  `is_representation` tinyint(1) NOT NULL DEFAULT '0',
  `grounds` varchar(32) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_opposition_ground_opposition1_idx` (`opposition_id`),
  KEY `fk_opposition_ground_ref_data1_idx` (`grounds`),
  KEY `fk_opposition_grounds_user1_idx` (`created_by`),
  KEY `fk_opposition_grounds_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_opposition_ground_opposition1` FOREIGN KEY (`opposition_id`) REFERENCES `opposition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_ground_ref_data1` FOREIGN KEY (`grounds`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_grounds_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_opposition_grounds_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` INT NOT NULL AUTO_INCREMENT,
  `company_or_llp_no` VARCHAR(20) NULL,
  `name` VARCHAR(160) NULL,
  `irfo_name` VARCHAR(160) NULL COMMENT 'Hold irfo company name separate from normal name.  Dont want changes to one affecting other on licences.',
  `contact_details_id` INT NULL COMMENT 'Registered office details',
  `type` VARCHAR(32) NOT NULL,
  `vi_action` VARCHAR(1) NULL,
  `is_mlh` TINYINT(1) NOT NULL DEFAULT 0,
  `company_cert_seen` TINYINT(1) NOT NULL DEFAULT 0,
  `irfo_nationality` VARCHAR(45) NULL,
  `is_irfo` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_email` TINYINT(1) NOT NULL DEFAULT 0,
  `lead_tc_area_id` CHAR(1) NULL,
  `created_by` INT NULL,
  `last_modified_by` INT NULL,
  `last_modified_on` DATETIME NULL,
  `created_on` DATETIME NULL,
  `version` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_organisation_user1_idx` (`created_by` ASC),
  INDEX `fk_organisation_user2_idx` (`last_modified_by` ASC),
  INDEX `fk_organisation_ref_data1_idx` (`type` ASC),
  INDEX `fk_organisation_traffic_area1_idx` (`lead_tc_area_id` ASC),
  INDEX `organisation_name_idx` (`name` ASC),
  INDEX `fk_organisation_contact_details1_idx` (`contact_details_id` ASC),
  CONSTRAINT `fk_organisation_user1`
    FOREIGN KEY (`created_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user2`
    FOREIGN KEY (`last_modified_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_ref_data1`
    FOREIGN KEY (`type`)
    REFERENCES `ref_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_traffic_area1`
    FOREIGN KEY (`lead_tc_area_id`)
    REFERENCES `traffic_area` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_contact_details1`
    FOREIGN KEY (`contact_details_id`)
    REFERENCES `contact_details` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB AUTO_INCREMENT = 1000000 DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_data_id` varchar(32) NOT NULL,
  `organisation_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_org_nob_ref_data1_idx` (`ref_data_id`),
  KEY `fk_org_nob_organisation1_idx` (`organisation_id`),
  KEY `fk_org_nob_user1_idx` (`created_by`),
  KEY `fk_org_nob_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_org_nob_ref_data1` FOREIGN KEY (`ref_data_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_nob_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_nob_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_nob_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `added_date` varchar(45) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_owner_person1_idx` (`person_id`),
  KEY `fk_owner_organisation1_idx` (`organisation_id`),
  KEY `fk_owner_user1_idx` (`created_by`),
  KEY `fk_owner_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_owner_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_owner_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_owner_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_owner_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_type_id` varchar(32) NOT NULL COMMENT 'LTD, Partnership etc.',
  `org_person_type_id` varchar(32) NOT NULL COMMENT 'Type if officers in org. Partners, directors etc.',
  PRIMARY KEY (`id`),
  KEY `fk_organisation_type_ref_data1_idx` (`org_type_id`),
  KEY `fk_organisation_type_ref_data2_idx` (`org_person_type_id`),
  CONSTRAINT `fk_organisation_type_ref_data1` FOREIGN KEY (`org_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_type_ref_data2` FOREIGN KEY (`org_person_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Organisation meta info.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_administrator` tinyint(1) NOT NULL DEFAULT '0',
  `added_date` datetime DEFAULT NULL,
  `removed_date` datetime DEFAULT NULL,
  `sftp_access` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `organisation_user_unique` (`organisation_id`,`user_id`),
  KEY `fk_organisation_has_user_user1_idx` (`user_id`),
  KEY `fk_organisation_has_user_organisation1_idx` (`organisation_id`),
  KEY `fk_organisation_user_user1_idx` (`created_by`),
  KEY `fk_organisation_user_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_organisation_has_user_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_has_user_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organisation_user_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation_user`
--

LOCK TABLES `organisation_user` WRITE;
/*!40000 ALTER TABLE `organisation_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `legacy_status` int(11) DEFAULT NULL,
  `legacy_method` int(11) DEFAULT NULL,
  `legacy_choice` int(11) DEFAULT NULL,
  `legacy_guid` varchar(255) DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `status` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_user1_idx` (`created_by`),
  KEY `fk_payment_user2_idx` (`last_modified_by`),
  KEY `fk_payment_ref_data1_idx` (`status`),
  CONSTRAINT `fk_payment_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_ref_data1` FOREIGN KEY (`status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(45) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_permission_user1_idx` (`created_by`),
  KEY `fk_permission_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_permission_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permission_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forename` varchar(35) DEFAULT NULL,
  `family_name` varchar(35) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(35) DEFAULT NULL,
  `other_name` varchar(35) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `title_other` varchar(20) DEFAULT NULL COMMENT 'Populated it title is other in dropdown',
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_person_user1_idx` (`created_by`),
  KEY `fk_person_user2_idx` (`last_modified_by`),
  KEY `person_family_name_idx` (`family_name`),
  KEY `person_forename_idx` (`forename`),
  CONSTRAINT `fk_person_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_contact_type` varchar(32) NOT NULL,
  `phone_number` varchar(45) DEFAULT NULL,
  `details` varchar(45) DEFAULT NULL,
  `contact_details_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_phone_contact_contact_details1_idx` (`contact_details_id`),
  KEY `fk_phone_contact_ref_data1_idx` (`phone_contact_type`),
  KEY `fk_phone_contact_user1_idx` (`created_by`),
  KEY `fk_phone_contact_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_phone_contact_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_ref_data1` FOREIGN KEY (`phone_contact_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_contact_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `agreed_by_tc_id` int(11) DEFAULT NULL COMMENT 'TC who agreed the PI',
  `decided_by_tc_id` int(11) DEFAULT NULL COMMENT 'TC who presided over PI decision',
  `agreed_by_tc_role` varchar(32) DEFAULT NULL COMMENT 'e.g. Traffic Commissioner or Deputy Traffic Commissioner',
  `decided_by_tc_role` varchar(32) DEFAULT NULL COMMENT 'e.g. Traffic Commissioner or Deputy Traffic Commissioner',
  `agreed_date` date DEFAULT NULL,
  `witnesses` int(11) DEFAULT NULL COMMENT 'Witnesses for the PI decision',
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
  `assigned_to` int(11) DEFAULT NULL COMMENT 'User PI is assigned to.',
  `comment` varchar(4000) DEFAULT NULL,
  `call_up_letter_date` date DEFAULT NULL,
  `brief_to_tc_date` date DEFAULT NULL,
  `written_outcome` varchar(32) DEFAULT NULL COMMENT 'Reason, decision or none if was verbal in hearing.',
  `written_reason_date` date DEFAULT NULL,
  `decision_letter_sent_date` date DEFAULT NULL,
  `tc_written_reason_date` date DEFAULT NULL,
  `tc_written_decision_date` date DEFAULT NULL,
  `written_reason_letter_date` date DEFAULT NULL,
  `dec_sent_after_written_dec_date` date DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL COMMENT 'Date pi closed.For showing important, open records to user.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_detail_cases1_idx` (`case_id`),
  KEY `fk_pi_detail_ref_data2_idx` (`pi_status`),
  KEY `fk_pi_detail_user1_idx` (`created_by`),
  KEY `fk_pi_detail_user2_idx` (`last_modified_by`),
  KEY `fk_pi_user1_idx` (`assigned_to`),
  KEY `fk_pi_presiding_tc1_idx` (`agreed_by_tc_id`),
  KEY `fk_pi_presiding_tc2_idx` (`decided_by_tc_id`),
  KEY `fk_pi_ref_data1_idx` (`agreed_by_tc_role`),
  KEY `fk_pi_ref_data2_idx` (`decided_by_tc_role`),
  KEY `fk_pi_ref_data3_idx` (`written_outcome`),
  CONSTRAINT `fk_pi_detail_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_detail_ref_data2` FOREIGN KEY (`pi_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_detail_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_detail_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_user1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_presiding_tc1` FOREIGN KEY (`agreed_by_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_presiding_tc2` FOREIGN KEY (`decided_by_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_ref_data1` FOREIGN KEY (`agreed_by_tc_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_ref_data2` FOREIGN KEY (`decided_by_tc_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_ref_data3` FOREIGN KEY (`written_outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `pi_id` int(11) NOT NULL,
  `decision_id` int(11) NOT NULL,
  PRIMARY KEY (`pi_id`,`decision_id`),
  KEY `fk_pi_has_decision_decision1_idx` (`decision_id`),
  KEY `fk_pi_has_decision_pi1_idx` (`pi_id`),
  CONSTRAINT `fk_pi_has_decision_pi1` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_has_decision_decision1` FOREIGN KEY (`decision_id`) REFERENCES `decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pi_definition_category` varchar(32) NOT NULL,
  `section_code` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_ni` tinyint(1) NOT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_definition_ref_data1_idx` (`goods_or_psv`),
  KEY `fk_pi_definition_user1_idx` (`created_by`),
  KEY `fk_pi_definition_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_pi_definition_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_definition_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_definition_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pi_id` int(11) NOT NULL,
  `hearing_date` datetime DEFAULT NULL,
  `presiding_tc_id` int(11) DEFAULT NULL,
  `presiding_tc_other` varchar(45) DEFAULT NULL,
  `presided_by_role` varchar(32) DEFAULT NULL,
  `pi_venue_other` varchar(255) DEFAULT NULL,
  `pi_venue_id` int(11) DEFAULT NULL COMMENT 'The venue at the time of selection is stored in pi_venue_other. If venue data changes, other still stores data at time of selection.',
  `witnesses` int(11) DEFAULT NULL,
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `cancelled_reason` varchar(4000) DEFAULT NULL,
  `cancelled_date` date DEFAULT NULL,
  `is_adjourned` tinyint(1) NOT NULL DEFAULT '0',
  `adjourned_date` date DEFAULT NULL,
  `adjourned_reason` varchar(4000) DEFAULT NULL,
  `details` varchar(4000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_reschedule_dates_pi_detail1_idx` (`pi_id`),
  KEY `fk_pi_reschedule_dates_presiding_tc1_idx` (`presiding_tc_id`),
  KEY `fk_pi_reschedule_date_user1_idx` (`created_by`),
  KEY `fk_pi_reschedule_date_user2_idx` (`last_modified_by`),
  KEY `fk_pi_hearing_ref_data1_idx` (`presided_by_role`),
  KEY `fk_pi_hearing_pi_venue1_idx` (`pi_venue_id`),
  CONSTRAINT `fk_pi_reschedule_dates_pi_detail1` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reschedule_dates_presiding_tc1` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reschedule_date_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reschedule_date_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_ref_data1` FOREIGN KEY (`presided_by_role`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_hearing_pi_venue1` FOREIGN KEY (`pi_venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `pi_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  PRIMARY KEY (`pi_id`,`reason_id`),
  KEY `fk_pi_has_reason_reason1_idx` (`reason_id`),
  KEY `fk_pi_has_reason_pi1_idx` (`pi_id`),
  CONSTRAINT `fk_pi_has_reason_pi1` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_has_reason_reason1` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `pi_id` int(11) NOT NULL,
  `pi_type_id` varchar(32) NOT NULL,
  PRIMARY KEY (`pi_id`,`pi_type_id`),
  KEY `fk_pi_type_pi1_idx` (`pi_id`),
  KEY `fk_pi_type_ref_data1_idx` (`pi_type_id`),
  CONSTRAINT `fk_pi_type_pi1` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_type_ref_data1` FOREIGN KEY (`pi_type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `address_id` int(11) NOT NULL,
  `traffic_area_id` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_venue_address1_idx` (`address_id`),
  KEY `fk_pi_venue_user1_idx` (`created_by`),
  KEY `fk_pi_venue_user2_idx` (`last_modified_by`),
  KEY `fk_pi_venue_traffic_area1_idx` (`traffic_area_id`),
  CONSTRAINT `fk_pi_venue_address1` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_venue_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enforcement_area_id` varchar(4) NOT NULL,
  `postcode_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `postcode_enforcement_area_unique` (`enforcement_area_id`,`postcode_id`),
  KEY `fk_PostcodeVehicleInspectorate_VehicleInspectorate1_idx` (`enforcement_area_id`),
  KEY `fk_postcode_enforcement_area_user1_idx` (`created_by`),
  KEY `fk_postcode_enforcement_area_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_PostcodeVehicleInspectorate_VehicleInspectorate1` FOREIGN KEY (`enforcement_area_id`) REFERENCES `enforcement_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_postcode_enforcement_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_postcode_enforcement_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `conviction_date` date DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `forename` varchar(35) NOT NULL,
  `family_name` varchar(35) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `category_text` varchar(1024) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `court_fpn` varchar(70) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `application_id` int(11) NOT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_previous_convictions_application1_idx` (`application_id`),
  CONSTRAINT `fk_previous_convictions_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_conviction`
--

LOCK TABLES `previous_conviction` WRITE;
/*!40000 ALTER TABLE `previous_conviction` DISABLE KEYS */;
/*!40000 ALTER TABLE `previous_conviction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `previous_licence`
--

DROP TABLE IF EXISTS `previous_licence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `previous_licence` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `lic_no` varchar(18) DEFAULT NULL,
  `holder_name` varchar(90) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `will_surrender` tinyint(1) DEFAULT NULL,
  `disqualification_date` date DEFAULT NULL,
  `disqualification_length` varchar(255) DEFAULT NULL,
  `previous_licence_type` varchar(32) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_previous_licence_application1_idx` (`application_id`),
  KEY `fk_previous_licence_ref_data1_idx` (`previous_licence_type`),
  KEY `fk_previous_licence_user1_idx` (`created_by`),
  KEY `fk_previous_licence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_previous_licence_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_previous_licence_ref_data1` FOREIGN KEY (`previous_licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_previous_licence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_previous_licence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `previous_licence`
--

LOCK TABLES `previous_licence` WRITE;
/*!40000 ALTER TABLE `previous_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `previous_licence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_queue`
--

DROP TABLE IF EXISTS `print_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_printer_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `added_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_print_queue_team_printer1_idx` (`team_printer_id`),
  KEY `fk_print_queue_document1_idx` (`document_id`),
  CONSTRAINT `fk_print_queue_team_printer1` FOREIGN KEY (`team_printer_id`) REFERENCES `team_printer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_print_queue_document1` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `printer_tray` varchar(45) DEFAULT NULL,
  `printer_name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `private_hire_licence_no` varchar(10) NOT NULL,
  `contact_details_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_hackney_licence_licence1_idx` (`licence_id`),
  KEY `fk_hackney_licence_contact_details1_idx` (`contact_details_id`),
  KEY `fk_hackney_licence_user1_idx` (`created_by`),
  KEY `fk_hackney_licence_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_hackney_licence_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hackney_licence_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hackney_licence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_hackney_licence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `prohibition_date` date NOT NULL,
  `cleared_date` date DEFAULT NULL,
  `is_trailer` tinyint(1) NOT NULL DEFAULT '0',
  `prohibition_type` varchar(32) NOT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `imposed_at` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_prohibition_case1_idx` (`case_id`),
  KEY `fk_prohibition_user1_idx` (`created_by`),
  KEY `fk_prohibition_user2_idx` (`last_modified_by`),
  KEY `fk_prohibition_ref_data1_idx` (`prohibition_type`),
  CONSTRAINT `fk_prohibition_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohibition_ref_data1` FOREIGN KEY (`prohibition_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prohibition_id` int(11) NOT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `defect_type` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_prohoibition_defect_prohibition1_idx` (`prohibition_id`),
  KEY `fk_prohoibition_defect_user1_idx` (`created_by`),
  KEY `fk_prohoibition_defect_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_prohoibition_defect_prohibition1` FOREIGN KEY (`prohibition_id`) REFERENCES `prohibition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohoibition_defect_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_prohoibition_defect_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `presiding_tc_id` int(11) NOT NULL,
  `ptr_agreed_date` datetime DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `comment` varchar(4000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_propose_to_revoke_cases1_idx` (`case_id`),
  KEY `fk_propose_to_revoke_presiding_tc1_idx` (`presiding_tc_id`),
  KEY `fk_propose_to_revoke_user1_idx` (`created_by`),
  KEY `fk_propose_to_revoke_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_propose_to_revoke_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_presiding_tc1` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `disc_no` varchar(50) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `ceased_date` datetime DEFAULT NULL,
  `is_copy` tinyint(1) NOT NULL DEFAULT '0',
  `reprint_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_printing` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_psv_disc_licence1_idx` (`licence_id`),
  KEY `fk_psv_disc_user1_idx` (`created_by`),
  KEY `fk_psv_disc_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_psv_disc_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_psv_disc_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_psv_disc_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `propose_to_revoke_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  PRIMARY KEY (`propose_to_revoke_id`,`reason_id`),
  KEY `fk_propose_to_revoke_has_pi_reason_pi_reason1_idx` (`reason_id`),
  KEY `fk_propose_to_revoke_has_pi_reason_propose_to_revoke1_idx` (`propose_to_revoke_id`),
  CONSTRAINT `fk_propose_to_revoke_has_pi_reason_propose_to_revoke1` FOREIGN KEY (`propose_to_revoke_id`) REFERENCES `propose_to_revoke` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_propose_to_revoke_has_pi_reason_pi_reason1` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_holiday_date` date NOT NULL,
  `is_england` tinyint(1) DEFAULT NULL,
  `is_wales` tinyint(1) DEFAULT NULL,
  `is_scotland` tinyint(1) DEFAULT NULL,
  `is_ni` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_public_holiday_user1_idx` (`created_by`),
  KEY `fk_public_holiday_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_public_holiday_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_public_holiday_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_no` int(11) NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  `pub_type` varchar(3) NOT NULL COMMENT 'Either A&D or N&P',
  `pub_date` date DEFAULT NULL,
  `pub_status` varchar(32) NOT NULL,
  `doc_name` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_publication_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_publication_ref_data1_idx` (`pub_status`),
  KEY `fk_publication_user1_idx` (`created_by`),
  KEY `fk_publication_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_publication_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_ref_data1` FOREIGN KEY (`pub_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `pi_id` int(11) DEFAULT NULL,
  `tm_pi_hearing_id` int(11) DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `text1` text,
  `text2` text,
  `text3` text,
  `publication_section_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_publication_has_licence_licence1_idx` (`licence_id`),
  KEY `fk_publication_has_licence_publication1_idx` (`publication_id`),
  KEY `fk_licence_publication_pi_detail1_idx` (`pi_id`),
  KEY `fk_licence_publication_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_licence_publication_application1_idx` (`application_id`),
  KEY `fk_licence_publication_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_licence_publication_publication_section1_idx` (`publication_section_id`),
  KEY `fk_licence_publication_user1_idx` (`created_by`),
  KEY `fk_licence_publication_user2_idx` (`last_modified_by`),
  KEY `fk_licence_publication_tm_pi_hearing1_idx` (`tm_pi_hearing_id`),
  CONSTRAINT `fk_publication_has_licence_publication1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_has_licence_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_pi_detail1` FOREIGN KEY (`pi_id`) REFERENCES `pi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_publication_section1` FOREIGN KEY (`publication_section_id`) REFERENCES `publication_section` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_licence_publication_tm_pi_hearing1` FOREIGN KEY (`tm_pi_hearing_id`) REFERENCES `tm_pi_hearing` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_link_id` int(11) NOT NULL,
  `forename` varchar(35) DEFAULT NULL,
  `family_name` varchar(35) DEFAULT NULL,
  `birth_date` date DEFAULT NULL COMMENT 'If null, police report will replace with not given.',
  `olbs_dob` varchar(20) DEFAULT NULL COMMENT 'Legacy DOB. Was stred as varchar and format was not consistand',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `olbs_id` int(11) DEFAULT NULL COMMENT 'Used in ETL for updates. No use in olcs app normal processing. Can be dropped when etl complete.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_olbs_id` (`olbs_id`) COMMENT 'Only required when etl active',
  KEY `fk_publication_police_data_publication_link1_idx` (`publication_link_id`),
  KEY `fk_publication_police_data_user1_idx` (`created_by`),
  KEY `fk_publication_police_data_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_publication_police_data_publication_link1` FOREIGN KEY (`publication_link_id`) REFERENCES `publication_link` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_police_data_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_police_data_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Police recipients of a publication get extra data. Currently this is the date of birth of people in the publication.';
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(70) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_publication_section_user1_idx` (`created_by`),
  KEY `fk_publication_section_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_publication_section_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publication_section_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `section_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_read_only` tinyint(1) NOT NULL,
  `is_ni` tinyint(1) NOT NULL COMMENT 'Northern Ireland or not',
  `is_propose_to_revoke` tinyint(1) NOT NULL COMMENT 'Used in Propose to Revoke',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pi_reason_user1_idx` (`created_by`),
  KEY `fk_pi_reason_user2_idx` (`last_modified_by`),
  KEY `fk_reason_ref_data1_idx` (`goods_or_psv`),
  CONSTRAINT `fk_pi_reason_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pi_reason_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reason_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_app_decision` tinyint(1) NOT NULL DEFAULT '0',
  `send_notices_procs` tinyint(1) NOT NULL DEFAULT '0',
  `is_police` tinyint(1) NOT NULL DEFAULT '0',
  `is_objector` tinyint(1) NOT NULL DEFAULT '0',
  `contact_name` varchar(100) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_recipient_user1_idx` (`created_by`),
  KEY `fk_recipient_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_recipient_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_recipient_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `recipient_id` int(11) NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  PRIMARY KEY (`recipient_id`,`traffic_area_id`),
  KEY `fk_recipient_has_traffic_area_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_recipient_has_traffic_area_recipient1_idx` (`recipient_id`),
  CONSTRAINT `fk_recipient_has_traffic_area_recipient1` FOREIGN KEY (`recipient_id`) REFERENCES `recipient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_recipient_has_traffic_area_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(32) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `ref_data_category_id` varchar(32) NOT NULL,
  `olbs_key` varchar(20) DEFAULT NULL,
  `parent_id` varchar(32) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ref_data_ref_data1_idx` (`parent_id`),
  KEY `ref_data_category_id_idx` (`ref_data_category_id`),
  CONSTRAINT `fk_ref_data_ref_data1` FOREIGN KEY (`parent_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_role_user1_idx` (`created_by`),
  KEY `fk_role_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_role_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_role_has_permission_permission1_idx` (`permission_id`),
  KEY `fk_role_has_permission_role1_idx` (`role_id`),
  KEY `fk_role_permission_user1_idx` (`created_by`),
  KEY `fk_role_permission_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_role_has_permission_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_has_permission_permission1` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) DEFAULT NULL,
  `licence_id` int(11) NOT NULL,
  `agreed_date` datetime DEFAULT NULL,
  `received_date` datetime NOT NULL,
  `outcome` varchar(20) DEFAULT NULL,
  `surrender_licence` tinyint(1) NOT NULL DEFAULT '0',
  `is_true_s4` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_s4_application1_idx` (`application_id`),
  KEY `fk_s4_licence1_idx` (`licence_id`),
  KEY `fk_s4_user1_idx` (`created_by`),
  KEY `fk_s4_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_s4_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `condition_id` int(11) NOT NULL,
  `s4_id` int(11) NOT NULL,
  PRIMARY KEY (`condition_id`,`s4_id`),
  KEY `fk_s4_condition_Condition1_idx` (`condition_id`),
  KEY `fk_s4_condition_s41_idx` (`s4_id`),
  CONSTRAINT `fk_s4_condition_Condition1` FOREIGN KEY (`condition_id`) REFERENCES `condition_undertaking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_s4_condition_s41` FOREIGN KEY (`s4_id`) REFERENCES `s4` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s4_condition`
--

LOCK TABLES `s4_condition` WRITE;
/*!40000 ALTER TABLE `s4_condition` DISABLE KEYS */;
/*!40000 ALTER TABLE `s4_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serious_infringement`
--

DROP TABLE IF EXISTS `serious_infringement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serious_infringement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `check_date` date DEFAULT NULL,
  `erru_response_sent` tinyint(1) NOT NULL DEFAULT '0',
  `erru_response_user_id` int(11) DEFAULT NULL,
  `erru_response_time` datetime DEFAULT NULL,
  `infringement_date` date DEFAULT NULL,
  `member_state_code` varchar(8) DEFAULT NULL,
  `notification_number` varchar(36) DEFAULT NULL COMMENT 'ERRU guid',
  `reason` varchar(500) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `si_category_id` varchar(8) NOT NULL,
  `si_category_type_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_serious_infringement_cases1_idx` (`case_id`),
  KEY `fk_serious_infringement_user1_idx` (`erru_response_user_id`),
  KEY `fk_serious_infringement_country1_idx` (`member_state_code`),
  KEY `fk_serious_infringement_si_category1_idx` (`si_category_id`),
  KEY `fk_serious_infringement_si_category_type1_idx` (`si_category_type_id`),
  KEY `fk_serious_infringement_user2_idx` (`created_by`),
  KEY `fk_serious_infringement_user3_idx` (`last_modified_by`),
  CONSTRAINT `fk_serious_infringement_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_user1` FOREIGN KEY (`erru_response_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_country1` FOREIGN KEY (`member_state_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_si_category1` FOREIGN KEY (`si_category_id`) REFERENCES `si_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_si_category_type1` FOREIGN KEY (`si_category_type_id`) REFERENCES `si_category_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_user2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serious_infringement_user3` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_category_user1_idx` (`created_by`),
  KEY `fk_si_category_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_category_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `si_category_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_category_type_si_category1_idx` (`si_category_id`),
  KEY `fk_si_category_type_user1_idx` (`created_by`),
  KEY `fk_si_category_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_category_type_si_category1` FOREIGN KEY (`si_category_id`) REFERENCES `si_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_category_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serious_infringement_id` int(11) NOT NULL,
  `imposed` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason_not_imposed` varchar(500) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `si_penalty_type_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_serious_infringement1_idx` (`serious_infringement_id`),
  KEY `fk_si_penalty_si_penalty_type1_idx` (`si_penalty_type_id`),
  KEY `fk_si_penalty_user1_idx` (`created_by`),
  KEY `fk_si_penalty_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_serious_infringement1` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_si_penalty_type1` FOREIGN KEY (`si_penalty_type_id`) REFERENCES `si_penalty_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `final_decision_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `executed` tinyint(1) DEFAULT NULL,
  `serious_infringement_id` int(11) NOT NULL,
  `si_penalty_imposed_type_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_erru_mposed_user1_idx` (`created_by`),
  KEY `fk_si_penalty_erru_mposed_user2_idx` (`last_modified_by`),
  KEY `fk_si_penalty_erru_mposed_serious_infringement1_idx` (`serious_infringement_id`),
  KEY `fk_si_penalty_erru_mposed_si_penalty_imposed_type1_idx` (`si_penalty_imposed_type_id`),
  CONSTRAINT `fk_si_penalty_erru_mposed_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_mposed_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_mposed_serious_infringement1` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_mposed_si_penalty_imposed_type1` FOREIGN KEY (`si_penalty_imposed_type_id`) REFERENCES `si_penalty_imposed_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duration` int(11) DEFAULT NULL COMMENT 'Number of months.',
  `deleted_date` datetime DEFAULT NULL,
  `serious_infringement_id` int(11) NOT NULL,
  `si_penalty_requested_type_id` varchar(8) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_erru_requested_serious_infringement1_idx` (`serious_infringement_id`),
  KEY `fk_si_penalty_erru_requested_si_penalty_requested_type1_idx` (`si_penalty_requested_type_id`),
  KEY `fk_si_penalty_erru_requested_user1_idx` (`created_by`),
  KEY `fk_si_penalty_erru_requested_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_erru_requested_serious_infringement1` FOREIGN KEY (`serious_infringement_id`) REFERENCES `serious_infringement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_si_penalty_requested_type1` FOREIGN KEY (`si_penalty_requested_type_id`) REFERENCES `si_penalty_requested_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_erru_requested_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_imposed_type_user1_idx` (`created_by`),
  KEY `fk_si_penalty_imposed_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_imposed_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_imposed_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_requested_type_user1_idx` (`created_by`),
  KEY `fk_si_penalty_requested_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_requested_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_requested_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_si_penalty_type_user1_idx` (`created_by`),
  KEY `fk_si_penalty_type_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_si_penalty_type_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_si_penalty_type_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(32) DEFAULT NULL COMMENT 'e.g. PI',
  `field` varchar(32) DEFAULT NULL COMMENT 'Field holding source of sla',
  `compare_to` varchar(32) DEFAULT NULL COMMENT 'Field holding result',
  `days` int(11) DEFAULT NULL COMMENT 'Number of days between source and result for succes.',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `public_holiday` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Include public holidays in SLA calculation',
  `weekend` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Include weekends in SLA calculation',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Service level agreements. Number of days from one value to another defines if sla met.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla`
--

LOCK TABLES `sla` WRITE;
/*!40000 ALTER TABLE `sla` DISABLE KEYS */;
/*!40000 ALTER TABLE `sla` ENABLE KEYS */;
UNLOCK TABLES;

-- -----------------------------------------------------
-- Table `sub_category_description`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sub_category_description`;
CREATE TABLE `sub_category_description` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sub_category_id` INT NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sub_category_description_sub_category1_idx` (`sub_category_id` ASC),
  CONSTRAINT `fk_sub_category_description_sub_category1`
    FOREIGN KEY (`sub_category_id`)
    REFERENCES `sub_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT = 'Possible values to be used in task or document description field for the sub category.';

--
-- Table structure for table `statement`
--

DROP TABLE IF EXISTS `statement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `statement_type` varchar(32) NOT NULL,
  `vrm` varchar(20) DEFAULT NULL,
  `stopped_date` datetime DEFAULT NULL,
  `requested_date` datetime DEFAULT NULL,
  `authorisers_title` varchar(40) DEFAULT NULL,
  `authorisers_decision` varchar(4000) DEFAULT NULL,
  `contact_type` varchar(32) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `licence_no` varchar(20) DEFAULT NULL,
  `licence_type` varchar(32) DEFAULT NULL,
  `requestors_body` varchar(40) DEFAULT NULL,
  `requestors_address_id` int(11) DEFAULT NULL,
  `requestors_family_name` varchar(35) DEFAULT NULL,
  `requestors_forename` varchar(35) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_statement_case1_idx` (`case_id`),
  KEY `fk_statement_address1_idx` (`requestors_address_id`),
  KEY `fk_statement_user1_idx` (`created_by`),
  KEY `fk_statement_user2_idx` (`last_modified_by`),
  KEY `fk_statement_ref_data2_idx` (`contact_type`),
  KEY `fk_statement_ref_data1_idx` (`statement_type`),
  CONSTRAINT `fk_statement_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_address1` FOREIGN KEY (`requestors_address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_ref_data2` FOREIGN KEY (`contact_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_statement_ref_data1` FOREIGN KEY (`statement_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT NULL,
  `withdrawn_date` datetime DEFAULT NULL,
  `decision_date` datetime DEFAULT NULL,
  `outcome` varchar(32) DEFAULT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  `stay_type` varchar(32) NOT NULL COMMENT 'TC or UT',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_stay_case1_idx` (`case_id`),
  KEY `fk_stay_user1_idx` (`created_by`),
  KEY `fk_stay_user2_idx` (`last_modified_by`),
  KEY `fk_stay_ref_data1_idx` (`outcome`),
  KEY `fk_stay_ref_data2_idx` (`stay_type`),
  CONSTRAINT `fk_stay_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_ref_data1` FOREIGN KEY (`outcome`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stay_ref_data2` FOREIGN KEY (`stay_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sub_category_name` varchar(64) NOT NULL,
  `is_scan` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Category used for scanning documents',
  `is_doc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is a valid document category',
  `is_task` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is a valid task category',
  `is_free_text` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User can enter freetext description - applied to task etc when creating.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_document_sub_category_document_category1_idx` (`category_id`),
  KEY `fk_document_sub_category_user1_idx` (`created_by`),
  KEY `fk_document_sub_category_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_document_sub_category_document_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_sub_category_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_sub_category_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Used to categorise documents, tasks and scans.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_category`
--

LOCK TABLES `sub_category` WRITE;
/*!40000 ALTER TABLE `sub_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission`
--

DROP TABLE IF EXISTS `submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `submission_type` varchar(32) NOT NULL,
  `data_snapshot` text COMMENT 'Contains data for each submission section concatenated togather as a JSon string.',
  `closed_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_submission_case1_idx` (`case_id`),
  KEY `fk_submission_user1_idx` (`created_by`),
  KEY `fk_submission_user2_idx` (`last_modified_by`),
  KEY `fk_submission_ref_data1_idx` (`submission_type`),
  CONSTRAINT `fk_submission_case1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_ref_data1` FOREIGN KEY (`submission_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_decision` tinyint(1) NOT NULL,
  `comment` text,
  `urgent` tinyint(1) DEFAULT NULL,
  `submission_id` int(11) NOT NULL,
  `sender_user_id` int(11) NOT NULL,
  `recipient_user_id` int(11) NOT NULL,
  `submission_action_status` varchar(32) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_submission_action_user1_idx` (`sender_user_id`),
  KEY `fk_submission_action_user2_idx` (`recipient_user_id`),
  KEY `fk_submission_action_user3_idx` (`created_by`),
  KEY `fk_submission_action_user4_idx` (`last_modified_by`),
  KEY `fk_submission_action_submission1_idx` (`submission_id`),
  KEY `fk_submission_action_ref_data1_idx` (`submission_action_status`),
  CONSTRAINT `fk_submission_action_user1` FOREIGN KEY (`sender_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_user2` FOREIGN KEY (`recipient_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_user3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_user4` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_submission1` FOREIGN KEY (`submission_id`) REFERENCES `submission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_ref_data1` FOREIGN KEY (`submission_action_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `submission_action_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  PRIMARY KEY (`submission_action_id`,`reason_id`),
  KEY `fk_submission_action_has_reason_reason1_idx` (`reason_id`),
  KEY `fk_submission_action_has_reason_submission_action1_idx` (`submission_action_id`),
  CONSTRAINT `fk_submission_action_has_reason_submission_action1` FOREIGN KEY (`submission_action_id`) REFERENCES `submission_action` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_action_has_reason_reason1` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text,
  `submission_id` int(11) NOT NULL,
  `submission_section` varchar(32) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_submission_section_submission1_idx` (`submission_id`),
  KEY `fk_submission_section_comments_ref_data1_idx` (`submission_section`),
  KEY `fk_submission_section_comments_user1_idx` (`created_by`),
  KEY `fk_submission_section_comments_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_submission_section_submission1` FOREIGN KEY (`submission_id`) REFERENCES `submission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comments_ref_data1` FOREIGN KEY (`submission_section`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comments_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_section_comments_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_internal` tinyint(1) NOT NULL,
  `activate_date` date NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(1024) NOT NULL,
  `importance` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_system_info_message_user1_idx` (`created_by`),
  KEY `fk_system_info_message_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_system_info_message_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_info_message_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` varchar(32) NOT NULL,
  `param_value` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `assigned_to_team_id` int(11) DEFAULT NULL,
  `assigned_by_user_id` int(11) DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `case_id` int(11) DEFAULT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `bus_reg_id` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `irfo_organisation_id` int(11) DEFAULT NULL,
  `urgent` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_task_user1_idx` (`assigned_to_user_id`),
  KEY `fk_task_team1_idx` (`assigned_to_team_id`),
  KEY `fk_task_user2_idx` (`assigned_by_user_id`),
  KEY `fk_task_licence1_idx` (`licence_id`),
  KEY `fk_task_application1_idx` (`application_id`),
  KEY `fk_task_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_task_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_task_organisation1_idx` (`irfo_organisation_id`),
  KEY `fk_task_user3_idx` (`created_by`),
  KEY `fk_task_user4_idx` (`last_modified_by`),
  KEY `fk_task_category1_idx` (`category_id`),
  KEY `fk_task_cases1_idx` (`case_id`),
  KEY `fk_task_task_sub_category1_idx` (`sub_category_id`),
  CONSTRAINT `fk_task_user1` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_team1` FOREIGN KEY (`assigned_to_team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_user2` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_organisation1` FOREIGN KEY (`irfo_organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_user3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_user4` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_task_sub_category1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `team_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goods_or_psv` varchar(32) DEFAULT NULL,
  `is_mlh` tinyint(1) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_allocation_rule_category1_idx` (`category_id`),
  KEY `fk_task_allocation_rule_team1_idx` (`team_id`),
  KEY `fk_task_allocation_rule_user1_idx` (`user_id`),
  KEY `fk_task_allocation_rule_ref_data1_idx` (`goods_or_psv`),
  KEY `fk_task_allocation_rule_traffic_area1_idx` (`traffic_area_id`),
  CONSTRAINT `fk_task_allocation_rule_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_allocation_rule_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_allocation_rule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `split_from_inclusive` varchar(2) NOT NULL,
  `split_to_inclusive` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_alpha_split_task_allocation_rule1_idx` (`task_allocation_rule_id`),
  KEY `fk_task_alpha_split_user1_idx` (`user_id`),
  CONSTRAINT `fk_task_alpha_split_task_allocation_rule1` FOREIGN KEY (`task_allocation_rule_id`) REFERENCES `task_allocation_rule` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_alpha_split_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_alpha_split`
--

LOCK TABLES `task_alpha_split` WRITE;
/*!40000 ALTER TABLE `task_alpha_split` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_alpha_split` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_note`
--

DROP TABLE IF EXISTS `task_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `note_text` varchar(1800) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_task_note_task1_idx` (`task_id`),
  KEY `fk_task_note_user1_idx` (`created_by`),
  KEY `fk_task_note_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_task_note_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_note_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_note_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_note`
--

LOCK TABLES `task_note` WRITE;
/*!40000 ALTER TABLE `task_note` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `traffic_area_id` varchar(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(70) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_team_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_team_user2_idx` (`last_modified_by`),
  KEY `fk_team_user1_idx` (`created_by`),
  CONSTRAINT `fk_team_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `printer_id` int(11) NOT NULL,
  `document_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_team_has_printer_printer1_idx` (`printer_id`),
  KEY `fk_team_has_printer_team1_idx` (`team_id`),
  CONSTRAINT `fk_team_has_printer_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_has_printer_printer1` FOREIGN KEY (`printer_id`) REFERENCES `printer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_printer`
--

LOCK TABLES `team_printer` WRITE;
/*!40000 ALTER TABLE `team_printer` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_printer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_case_decision`
--

DROP TABLE IF EXISTS `tm_case_decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_case_decision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `decision_date` date DEFAULT NULL,
  `notified_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `decision` varchar(32) NOT NULL,
  `is_msi` tinyint(1) NOT NULL DEFAULT '0',
  `repute_not_lost_reason` varchar(4000) DEFAULT NULL,
  `unfitness_start_date` date DEFAULT NULL,
  `unfitness_end_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tm_case_decision_ref_data1_idx` (`decision`),
  KEY `fk_tm_case_decision_user1_idx` (`created_by`),
  KEY `fk_tm_case_decision_user2_idx` (`last_modified_by`),
  KEY `fk_tm_case_decision_cases1_idx` (`case_id`),
  CONSTRAINT `fk_tm_case_decision_ref_data1` FOREIGN KEY (`decision`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `tm_case_decision_rehab_id` int(11) NOT NULL,
  `rehab_measure_id` varchar(32) NOT NULL,
  PRIMARY KEY (`tm_case_decision_rehab_id`, `rehab_measure_id`),
  KEY `fk_tm_case_decision_rehab_tm_case_decision1_idx` (`tm_case_decision_rehab_id`),
  KEY `fk_tm_case_decision_rehab_ref_data1_idx` (`rehab_measure_id`),
  CONSTRAINT `fk_tm_case_decision_rehab_tm_case_decision1` FOREIGN KEY (`tm_case_decision_rehab_id`) REFERENCES `tm_case_decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_rehab_ref_data1` FOREIGN KEY (`rehab_measure_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `tm_case_decision_unfitness_id` int(11) NOT NULL,
  `unfitness_reason_id` varchar(32) NOT NULL,
  PRIMARY KEY (`tm_case_decision_unfitness_id`,`unfitness_reason_id`),
  KEY `fk_tm_case_decision_unfitness_tm_case_decision1_idx` (`tm_case_decision_unfitness_id`),
  KEY `fk_tm_case_decision_unfitness_ref_data1_idx` (`unfitness_reason_id`),
  CONSTRAINT `fk_tm_case_decision_unfitness_tm_case_decision1` FOREIGN KEY (`tm_case_decision_unfitness_id`) REFERENCES `tm_case_decision` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_case_decision_unfitness_ref_data1` FOREIGN KEY (`unfitness_reason_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_case_decision_unfitness`
--

LOCK TABLES `tm_case_decision_unfitness` WRITE;
/*!40000 ALTER TABLE `tm_case_decision_unfitness` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_case_decision_unfitness` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_merge`
--

DROP TABLE IF EXISTS `tm_merge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_merge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tm_from_id` int(11) NOT NULL COMMENT 'The TM that is being merged from',
  `tm_to_id` int(11) NOT NULL COMMENT 'The TM merging into',
  `tm_application_id` int(11) DEFAULT NULL COMMENT 'Application being moved from TM to TM',
  `tm_licence_id` int(11) DEFAULT NULL COMMENT 'Licence being moved from TM to TM',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tm_merge_transport_manager1_idx` (`tm_from_id`),
  KEY `fk_tm_merge_transport_manager2_idx` (`tm_to_id`),
  KEY `fk_tm_merge_transport_manager_application1_idx` (`tm_application_id`),
  KEY `fk_tm_merge_transport_manager_licence1_idx` (`tm_licence_id`),
  KEY `fk_tm_merge_user1_idx` (`created_by`),
  KEY `fk_tm_merge_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_tm_merge_transport_manager1` FOREIGN KEY (`tm_from_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_transport_manager2` FOREIGN KEY (`tm_to_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_transport_manager_application1` FOREIGN KEY (`tm_application_id`) REFERENCES `transport_manager_application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_transport_manager_licence1` FOREIGN KEY (`tm_licence_id`) REFERENCES `transport_manager_licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_merge_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_merge`
--

LOCK TABLES `tm_merge` WRITE;
/*!40000 ALTER TABLE `tm_merge` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_merge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_pi_hearing`
--

DROP TABLE IF EXISTS `tm_pi_hearing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_pi_hearing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `witnesses` int(11) NOT NULL DEFAULT '0',
  `adjourned_date` datetime DEFAULT NULL,
  `agreed_date` date DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `scheduled_on` datetime DEFAULT NULL,
  `rescheduled_on` datetime DEFAULT NULL,
  `presided_by` varchar(32) DEFAULT NULL,
  `reason_id` varchar(32) NOT NULL,
  `type_id` varchar(32) NOT NULL,
  `presiding_tc_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tm_pi_hearing_cases1_idx` (`case_id`),
  KEY `fk_tm_pi_hearing_ref_data1_idx` (`presided_by`),
  KEY `fk_tm_pi_hearing_ref_data2_idx` (`reason_id`),
  KEY `fk_tm_pi_hearing_ref_data3_idx` (`type_id`),
  KEY `fk_tm_pi_hearing_presiding_tc1_idx` (`presiding_tc_id`),
  KEY `fk_tm_pi_hearing_user1_idx` (`created_by`),
  KEY `fk_tm_pi_hearing_user2_idx` (`last_modified_by`),
  KEY `fk_tm_pi_hearing_pi_venue1_idx` (`venue_id`),
  CONSTRAINT `fk_tm_pi_hearing_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_ref_data1` FOREIGN KEY (`presided_by`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_ref_data2` FOREIGN KEY (`reason_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_ref_data3` FOREIGN KEY (`type_id`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_presiding_tc1` FOREIGN KEY (`presiding_tc_id`) REFERENCES `presiding_tc` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_pi_hearing_pi_venue1` FOREIGN KEY (`venue_id`) REFERENCES `pi_venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_pi_hearing`
--

LOCK TABLES `tm_pi_hearing` WRITE;
/*!40000 ALTER TABLE `tm_pi_hearing` DISABLE KEYS */;
/*!40000 ALTER TABLE `tm_pi_hearing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_qualification`
--

DROP TABLE IF EXISTS `tm_qualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_qualification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_manager_id` int(11) NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `qualification_type` varchar(32) NOT NULL,
  `issued_date` date DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `serial_no` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_qualification_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_qualification_country1_idx` (`country_code`),
  KEY `fk_qualification_ref_data1_idx` (`qualification_type`),
  KEY `fk_tm_qualification_user1_idx` (`created_by`),
  KEY `fk_tm_qualification_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_qualification_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_qualification_country1` FOREIGN KEY (`country_code`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_qualification_ref_data1` FOREIGN KEY (`qualification_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_qualification_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Trading name used by organisations in different areas',
  `name` varchar(160) NOT NULL,
  `licence_id` int(11) DEFAULT NULL COMMENT 'populated for non irfo records',
  `organisation_id` int(11) DEFAULT NULL COMMENT 'Used by IRFO',
  `deleted_date` datetime DEFAULT NULL,
  `vi_action` varchar(1) DEFAULT NULL COMMENT 'Triggers entry in batch export to mobile compliance system',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_trading_name_licence1_idx` (`licence_id`),
  KEY `fk_trading_name_organisation1_idx` (`organisation_id`),
  KEY `fk_trading_name_user1_idx` (`created_by`),
  KEY `fk_trading_name_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_trading_name_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trading_name_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` char(1) NOT NULL,
  `name` varchar(70) NOT NULL,
  `txc_name` varchar(70) DEFAULT NULL,
  `contact_details_id` int(11) NOT NULL,
  `is_scotland` tinyint(1) NOT NULL DEFAULT '0',
  `is_wales` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni` tinyint(1) NOT NULL DEFAULT '0',
  `is_england` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_traffic_area_user1_idx` (`created_by`),
  KEY `fk_traffic_area_user2_idx` (`last_modified_by`),
  KEY `fk_traffic_area_contact_details1_idx` (`contact_details_id`),
  CONSTRAINT `fk_traffic_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traffic_area_id` varchar(1) NOT NULL,
  `enforcement_area_id` varchar(4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `traffic_area_enforcement_area_unique` (`traffic_area_id`,`enforcement_area_id`),
  KEY `fk_TrafficAreaVehicleInspectorate_TrafficArea1_idx` (`traffic_area_id`),
  KEY `fk_TrafficAreaVehicleInspectorate_VehicleInspectorate1_idx` (`enforcement_area_id`),
  KEY `fk_traffic_area_enforcement_area_user1_idx` (`created_by`),
  KEY `fk_traffic_area_enforcement_area_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_TrafficAreaVehicleInspectorate_TrafficArea1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_TrafficAreaVehicleInspectorate_VehicleInspectorate1` FOREIGN KEY (`enforcement_area_id`) REFERENCES `enforcement_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_enforcement_area_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traffic_area_enforcement_area_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `trailer_no` varchar(20) NOT NULL,
  `specified_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_trailer_licence1_idx` (`licence_id`),
  KEY `fk_trailer_user1_idx` (`created_by`),
  KEY `fk_trailer_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_trailer_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trailer_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trailer_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tm_status` varchar(32) NOT NULL,
  `tm_type` varchar(32) NOT NULL,
  `home_cd_id` int(11) NOT NULL,
  `work_cd_id` int(11) NOT NULL,
  `disqualification_tm_case_id` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `nysiis_family_name` varchar(100) DEFAULT NULL,
  `nysiis_forename` varchar(100) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_transport_manager_ref_data1_idx` (`tm_status`),
  KEY `fk_transport_manager_ref_data2_idx` (`tm_type`),
  KEY `fk_transport_manager_home_cd_idx` (`home_cd_id`),
  KEY `fk_transport_manager_work_cd_idx` (`work_cd_id`),
  KEY `fk_transport_manager_user1_idx` (`created_by`),
  KEY `fk_transport_manager_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_transport_manager_ref_data1` FOREIGN KEY (`tm_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_ref_data2` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_home_cd_idx` FOREIGN KEY (`home_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_work_cd_idx` FOREIGN KEY (`work_cd_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_manager_id` int(11) NOT NULL,
  `tm_type` varchar(32) DEFAULT NULL,
  `tm_application_status` varchar(32) NOT NULL,
  `application_id` int(11) NOT NULL,
  `action` varchar(1) DEFAULT NULL COMMENT 'A or D for Add or Delete',
  `hours_mon` int(11) DEFAULT NULL,
  `hours_tue` int(11) DEFAULT NULL,
  `hours_wed` int(11) DEFAULT NULL,
  `hours_thu` int(11) DEFAULT NULL,
  `hours_fri` int(11) DEFAULT NULL,
  `hours_sat` int(11) DEFAULT NULL,
  `hours_sun` int(11) DEFAULT NULL,
  `additional_information` varchar(4000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `olbs_key` INT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transport_manager_application_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_transport_manager_application_application1_idx` (`application_id`),
  KEY `fk_transport_manager_application_user1_idx` (`created_by`),
  KEY `fk_transport_manager_application_user2_idx` (`last_modified_by`),
  KEY `fk_transport_manager_application_ref_data1_idx` (`tm_type`),
  KEY `fk_transport_manager_application_ref_data2_idx` (`tm_application_status`),
  CONSTRAINT `fk_transport_manager_application_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_application1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_ref_data1` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_application_ref_data2` FOREIGN KEY (`tm_application_status`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_manager_id` int(11) NOT NULL,
  `tm_type` varchar(32) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `hours_mon` int(11) DEFAULT NULL,
  `hours_tue` int(11) DEFAULT NULL,
  `hours_wed` int(11) DEFAULT NULL,
  `hours_thu` int(11) DEFAULT NULL,
  `hours_fri` int(11) DEFAULT NULL,
  `hours_sat` int(11) DEFAULT NULL,
  `hours_sun` int(11) DEFAULT NULL,
  `additional_information` varchar(4000) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `olbs_key` INT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transport_manager_licence_transport_manager1_idx` (`transport_manager_id`),
  KEY `fk_transport_manager_licence_licence1_idx` (`licence_id`),
  KEY `fk_transport_manager_licence_user1_idx` (`created_by`),
  KEY `fk_transport_manager_licence_user2_idx` (`last_modified_by`),
  KEY `fk_transport_manager_licence_ref_data1_idx` (`tm_type`),
  CONSTRAINT `fk_transport_manager_licence_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transport_manager_licence_ref_data1` FOREIGN KEY (`tm_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_manager_licence`
--

LOCK TABLES `transport_manager_licence` WRITE;
/*!40000 ALTER TABLE `transport_manager_licence` DISABLE KEYS */;
/*!40000 ALTER TABLE `transport_manager_licence` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `tm_application_oc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_application_oc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_manager_application_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_tm_application_oc_application1_idx` (`transport_manager_application_id`),
  KEY `fk_tm_application_oc_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_tm_application_oc_user1_idx` (`created_by`),
  KEY `fk_tm_application_oc_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_tm_application_oc_user1_idx` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_application_oc_user2_idx` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_application_oc_application1_idx` FOREIGN KEY (`transport_manager_application_id`) REFERENCES `transport_manager_application` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_application_oc_operating_centre1_idx` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `tm_licence_oc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tm_licence_oc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_manager_licence_id` int(11) NOT NULL,
  `operating_centre_id` int(11) NOT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_tm_licence_oc_licence1_idx` (`transport_manager_licence_id`),
  KEY `fk_tm_licence_oc_operating_centre1_idx` (`operating_centre_id`),
  KEY `fk_tm_licence_oc_user1_idx` (`created_by`),
  KEY `fk_tm_licence_oc_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_tm_licence_oc_user1_idx` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_licence_oc_user2_idx` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_licence_oc_application1_idx` FOREIGN KEY (`transport_manager_licence_id`) REFERENCES `transport_manager_licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_licence_oc_operating_centre1_idx` FOREIGN KEY (`operating_centre_id`) REFERENCES `operating_centre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `txc_inbox`
--

DROP TABLE IF EXISTS `txc_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `txc_inbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_read` tinyint(1) DEFAULT NULL,
  `bus_reg_id` int(11) NOT NULL,
  `local_authority_id` int(11) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `zip_document_id` int(11) NOT NULL,
  `route_document_id` int(11) NOT NULL,
  `pdf_document_id` int(11) NOT NULL,
  `variation_no` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_txc_inbox_bus_reg1_idx` (`bus_reg_id`),
  KEY `fk_txc_inbox_local_authority1_idx` (`local_authority_id`),
  KEY `fk_txc_inbox_organisation1_idx` (`organisation_id`),
  KEY `fk_txc_inbox_document1_idx` (`zip_document_id`),
  KEY `fk_txc_inbox_document2_idx` (`route_document_id`),
  KEY `fk_txc_inbox_document3_idx` (`pdf_document_id`),
  KEY `fk_txc_inbox_user1_idx` (`created_by`),
  KEY `fk_txc_inbox_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_txc_inbox_bus_reg1` FOREIGN KEY (`bus_reg_id`) REFERENCES `bus_reg` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_local_authority1` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_organisation1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_document1` FOREIGN KEY (`zip_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_document2` FOREIGN KEY (`route_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_document3` FOREIGN KEY (`pdf_document_id`) REFERENCES `document` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_txc_inbox_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_address` varchar(45) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL COMMENT 'If user is also a transport manager.',
  `login_id` varchar(40) DEFAULT NULL,
  `team_id` int(11) NOT NULL,
  `account_disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Account locked by DVSA. Cannot be unlocked by non DVSA user.',
  `locked_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL COMMENT 'Logical delete.',
  `local_authority_id` int(11) DEFAULT NULL COMMENT 'If user is a member of a local authority a link to the LA details.',
  `contact_details_id` int(11) DEFAULT NULL,
  `partner_contact_details_id` int(11) DEFAULT NULL COMMENT 'If user is part of a partner, such as HMRC a link to the partners details.',
  `attempts` int(11) DEFAULT NULL COMMENT 'Count of unsuccessful login attempts. Resets on successful login.',
  `last_successful_login_date` datetime DEFAULT NULL,
  `hint_questions_id1` int(11) DEFAULT NULL COMMENT 'Question for user self password reset.',
  `hint_questions_id2` int(11) DEFAULT NULL COMMENT 'Question for user self password reset.',
  `hint_answer_1` varchar(50) DEFAULT NULL COMMENT 'Password reset answer.',
  `hint_answer_2` varchar(50) DEFAULT NULL COMMENT 'Password reset answer.',
  `memorable_word` varchar(10) DEFAULT NULL COMMENT 'Part of non internal user login. User challenged to enter 2 letters of word.',
  `memorable_word_digit1` int(11) DEFAULT NULL COMMENT 'Letter used in last user login challenge. Used to ensure different challenge on next login.',
  `memorable_word_digit2` int(11) DEFAULT NULL COMMENT 'Letter used in last user login challenge. Used to ensure different challenge on next login.',
  `must_reset_password` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'On next login user must reset password.',
  `password_expiry_date` datetime DEFAULT NULL COMMENT 'Typically a year after account created.',
  `reset_password_expiry_date` datetime DEFAULT NULL COMMENT 'After password reset by admin user has x number of days to login and change password or account is locked.',
  `password_reminder_sent` tinyint(1) DEFAULT NULL COMMENT 'At X number of days before password expiry a warning email is sent to user.',
  `locked_datetime` datetime DEFAULT NULL COMMENT 'To stop brute force password attack.  When number of attemps greate than X date set. User cant attempt again until date plus X number of minutes.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `job_title` varchar(100) DEFAULT NULL,
  `division_group` varchar(100) DEFAULT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_team1_idx` (`team_id`),
  KEY `fk_user_local_authority1_idx` (`local_authority_id`),
  KEY `fk_user_user1_idx` (`created_by`),
  KEY `fk_user_user2_idx` (`last_modified_by`),
  KEY `fk_user_contact_details1_idx` (`contact_details_id`),
  KEY `fk_user_contact_details2_idx` (`partner_contact_details_id`),
  KEY `fk_user_hint_questions1_idx` (`hint_questions_id1`),
  KEY `fk_user_hint_questions2_idx` (`hint_questions_id2`),
  KEY `fk_user_transport_manager1_idx` (`transport_manager_id`),
  CONSTRAINT `fk_user_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_local_authority1` FOREIGN KEY (`local_authority_id`) REFERENCES `local_authority` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_contact_details2` FOREIGN KEY (`partner_contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_hint_questions1` FOREIGN KEY (`hint_questions_id1`) REFERENCES `hint_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_hint_questions2` FOREIGN KEY (`hint_questions_id2`) REFERENCES `hint_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_transport_manager1` FOREIGN KEY (`transport_manager_id`) REFERENCES `transport_manager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_user_has_role_role1_idx` (`role_id`),
  KEY `fk_user_has_role_user1_idx` (`user_id`),
  KEY `fk_user_role_user1_idx` (`created_by`),
  KEY `fk_user_role_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_user_has_role_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_role_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variation_reason`
--

DROP TABLE IF EXISTS `variation_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variation_reason` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variation_reason`
--

LOCK TABLES `variation_reason` WRITE;
/*!40000 ALTER TABLE `variation_reason` DISABLE KEYS */;
/*!40000 ALTER TABLE `variation_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_novelty` tinyint(1) DEFAULT NULL,
  `vrm` varchar(20) DEFAULT NULL COMMENT 'Nullable for PSVs',
  `plated_weight` int(11) DEFAULT NULL,
  `certificate_no` varchar(50) DEFAULT NULL COMMENT 'psv only',
  `vi_action` varchar(1) DEFAULT NULL,
  `section_26` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_curtail` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_revoked` tinyint(1) NOT NULL DEFAULT '0',
  `section_26_suspend` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  `psv_type` varchar(32) DEFAULT NULL COMMENT 'small, medium or large',
  `make_model` varchar(100) DEFAULT NULL COMMENT 'For small PSV vehicles the make and model are recorded.',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_vehicle_user1_idx` (`created_by`),
  KEY `fk_vehicle_user2_idx` (`last_modified_by`),
  KEY `fk_vehicle_ref_data1_idx` (`psv_type`),
  CONSTRAINT `fk_vehicle_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vehicle_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vehicle_ref_data1` FOREIGN KEY (`psv_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_or_psv` varchar(32) NOT NULL,
  `licence_type` varchar(32) NOT NULL,
  `serial_start` int(11) DEFAULT NULL,
  `serial_end` int(11) DEFAULT NULL,
  `traffic_area_id` char(1) DEFAULT NULL,
  `is_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `is_ni_self_serve` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_void_disc_ref_data1_idx` (`goods_or_psv`),
  KEY `fk_void_disc_ref_data2_idx` (`licence_type`),
  KEY `fk_void_disc_traffic_area1_idx` (`traffic_area_id`),
  KEY `fk_void_disc_user1_idx` (`created_by`),
  KEY `fk_void_disc_user2_idx` (`last_modified_by`),
  CONSTRAINT `fk_void_disc_ref_data1` FOREIGN KEY (`goods_or_psv`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_ref_data2` FOREIGN KEY (`licence_type`) REFERENCES `ref_data` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_traffic_area1` FOREIGN KEY (`traffic_area_id`) REFERENCES `traffic_area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_void_disc_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licence_id` int(11) NOT NULL,
  `is_external` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is garage or workshop.',
  `maintenance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Carries out maintenance.',
  `safety_inspection` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Carries out own safety inspections.',
  `removed_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `last_modified_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `contact_details_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_workshop_licence1_idx` (`licence_id`),
  KEY `fk_workshop_user1_idx` (`created_by`),
  KEY `fk_workshop_user2_idx` (`last_modified_by`),
  KEY `fk_workshop_contact_details1_idx` (`contact_details_id`),
  CONSTRAINT `fk_workshop_licence1` FOREIGN KEY (`licence_id`) REFERENCES `licence` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_user1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_user2` FOREIGN KEY (`last_modified_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_workshop_contact_details1` FOREIGN KEY (`contact_details_id`) REFERENCES `contact_details` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oc_complaint`
--

DROP TABLE IF EXISTS `oc_complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oc_complaint` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `complaint_id` INT NOT NULL,
  `operating_centre_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_oc_complaint_complaint1_idx` (`complaint_id` ASC),
  INDEX `fk_oc_complaint_operating_centre1_idx` (`operating_centre_id` ASC),
  CONSTRAINT `fk_oc_complaint_complaint1`
    FOREIGN KEY (`complaint_id`)
    REFERENCES `complaint` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_oc_complaint_operating_centre1`
    FOREIGN KEY (`operating_centre_id`)
    REFERENCES `operating_centre` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

LOCK TABLES `oc_complaint` WRITE;
/*!40000 ALTER TABLE `oc_complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `oc_complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `workshop`
--

LOCK TABLES `workshop` WRITE;
/*!40000 ALTER TABLE `workshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `workshop` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `scan` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `application_id` INT NULL,
  `irfo_organisation_id` INT NULL,
  `bus_reg_id` INT NULL,
  `licence_id` INT NULL,
  `case_id` INT NULL,
  `transport_manager_id` INT NULL,
  `category_id` INT NOT NULL,
  `sub_category_id` INT NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  `created_by` INT NULL,
  `last_modified_by` INT NULL,
  `created_on` DATETIME NULL,
  `last_modified_on` DATETIME NULL,
  `version` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_scan_application1_idx` (`application_id` ASC),
  INDEX `fk_scan_irfo_organisation1_idx` (`irfo_organisation_id` ASC),
  INDEX `fk_scan_bus_reg1_idx` (`bus_reg_id` ASC),
  INDEX `fk_scan_licence1_idx` (`licence_id` ASC),
  INDEX `fk_scan_cases1_idx` (`case_id` ASC),
  INDEX `fk_scan_transport_manager1_idx` (`transport_manager_id` ASC),
  INDEX `fk_scan_category1_idx` (`category_id` ASC),
  INDEX `fk_scan_sub_category1_idx` (`sub_category_id` ASC),
  INDEX `fk_scan_user1_idx` (`created_by` ASC),
  INDEX `fk_scan_user2_idx` (`last_modified_by` ASC),
  CONSTRAINT `fk_scan_application1`
    FOREIGN KEY (`application_id`)
    REFERENCES `application` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_irfo_organisation1`
    FOREIGN KEY (`irfo_organisation_id`)
    REFERENCES `organisation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_bus_reg1`
    FOREIGN KEY (`bus_reg_id`)
    REFERENCES `bus_reg` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_licence1`
    FOREIGN KEY (`licence_id`)
    REFERENCES `licence` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_cases1`
    FOREIGN KEY (`case_id`)
    REFERENCES `cases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_transport_manager1`
    FOREIGN KEY (`transport_manager_id`)
    REFERENCES `transport_manager` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_sub_category1`
    FOREIGN KEY (`sub_category_id`)
    REFERENCES `sub_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_user1`
    FOREIGN KEY (`created_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_scan_user2`
    FOREIGN KEY (`last_modified_by`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-26 15:05:08
