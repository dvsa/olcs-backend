<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="category",
 *    indexes={
 *        @ORM\Index(name="ix_category_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_category_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_category_task_allocation_type", columns={"task_allocation_type"})
 *    }
 * )
 */
class Category extends AbstractCategory
{
    const CATEGORY_LICENSING = 1;
    const CATEGORY_COMPLIANCE = 2;
    const CATEGORY_BUS_REGISTRATION = 3;
    const CATEGORY_PERMITS = 4;
    const CATEGORY_TRANSPORT_MANAGER = 5;
    const CATEGORY_ENVIRONMENTAL = 7;
    const CATEGORY_IRFO = 8;
    const CATEGORY_APPLICATION = 9;
    const CATEGORY_SUBMISSION = 10;

    // @NOTE create constants for all sub categories as required. Only a subset
    // will ever be needed programatically so this list should be manageable
    const TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL = 3;
    const TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE = 11;
    const TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL = 14;
    const TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL = 15;
    const TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL = 25;
    const TASK_SUB_CATEGORY_HEARINGS_APPEALS = 49;
    const TASK_SUB_CATEGORY_DECISION = 96;
    const TASK_SUB_CATEGORY_RECOMMENDATION = 97;
    const TASK_SUB_CATEGORY_ASSIGNMENT = 114;
    const TASK_SUB_CATEGORY_REVIEW_COMPLAINT = 61;
    const TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK = 77;
    const TASK_SUB_CATEGORY_INSPECTION_REQUEST_SEMINAR = 78;
    const TASK_SUB_CATEGORY_CHANGE_TO_TAXI_PHV_DIGITAL = 165;
    const TASK_SUB_CATEGORY_BUSINESS_DETAILS_CHANGE = 169;
    const TASK_SUB_CATEGORY_SUR_41_ASSISTED_DIGITAL = 82;

    const SCAN_SUB_CATEGORY_CHANGE_OF_ENTITY = 85;

    const DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL = 5;
    const DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST = 91;
    const DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL = 112;
    const DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION = 98;
    const DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL = 100;
    const DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL = 13;
    const DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS = 74;
    const DOC_SUB_CATEGORY_OTHER_DOCUMENTS = 79;
    const DOC_SUB_CATEGORY_FEE_REQUEST = 110;
    const DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE = 74;
    const DOC_SUB_CATEGORY_CPID = 170;
}
