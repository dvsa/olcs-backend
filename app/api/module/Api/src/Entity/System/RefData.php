<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefData Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ref_data",
 *    indexes={
 *        @ORM\Index(name="ix_ref_data_parent_id", columns={"parent_id"}),
 *        @ORM\Index(name="ix_ref_data_ref_data_category_id", columns={"ref_data_category_id"})
 *    }
 * )
 */
class RefData extends AbstractRefData
{
    const FEE_TYPE_APP = 'APP';
    const FEE_TYPE_VAR = 'VAR';
    const FEE_TYPE_GRANT = 'GRANT';
    const FEE_TYPE_CONT = 'CONT';
    const FEE_TYPE_VEH = 'VEH';
    const FEE_TYPE_GRANTINT = 'GRANTINT';
    const FEE_TYPE_INTVEH = 'INTVEH';
    const FEE_TYPE_DUP = 'DUP';
    const FEE_TYPE_ANN = 'ANN';
    const FEE_TYPE_GRANTVAR = 'GRANTVAR';
    const FEE_TYPE_BUSAPP = 'BUSAPP';
    const FEE_TYPE_BUSVAR = 'BUSVAR';
    const FEE_TYPE_GVANNVEH = 'GVANNVEH';
    const FEE_TYPE_INTUPGRADEVEH = 'INTUPGRADEVEH';
    const FEE_TYPE_INTAMENDED = 'INTAMENDED';
    const FEE_TYPE_IRFOPSVAPP = 'IRFOPSVAPP';
    const FEE_TYPE_IRFOPSVANN = 'IRFOPSVANN';
    const FEE_TYPE_IRFOPSVCOPY = 'IRFOPSVCOPY';
    const FEE_TYPE_IRFOGVPERMIT = 'IRFOGVPERMIT';

    const TASK_ACTION_DATE_TODAY = 'tdt_today';

    const SIG_PHYSICAL_SIGNATURE = 'sig_physical_signature';
    const SIG_DIGITAL_SIGNATURE = 'sig_digital_signature';
    const SIG_SIGNATURE_NOT_REQUIRED = 'sig_signature_not_required';

    const PHONE_NUMBER_PRIMARY_TYPE = 'phone_t_primary';
    const PHONE_NUMBER_SECONDARY_TYPE = 'phone_t_secondary';

    const LICENCE_STATUS = 'lic_status';

    const PERMIT_APP_STATUS_CANCELLED = 'permit_app_cancelled';
    const PERMIT_APP_STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';
    const PERMIT_APP_STATUS_UNDER_CONSIDERATION = 'permit_app_uc';
    const PERMIT_APP_STATUS_WITHDRAWN = 'permit_app_withdrawn';
    const PERMIT_APP_STATUS_AWAITING_FEE = 'permit_app_awaiting';
    const PERMIT_APP_STATUS_FEE_PAID = 'permit_app_fee_paid';
    const PERMIT_APP_STATUS_UNSUCCESSFUL = 'permit_app_unsuccessful';
    const PERMIT_APP_STATUS_ISSUING = 'permit_app_issuing';
    const PERMIT_APP_STATUS_VALID = 'permit_app_valid';

    //Surrenders
    const SURRENDER_STATUS_START='surr_sts_start';
    const SURRENDER_STATUS_CONTACTS_COMPLETE='surr_sts_contacts_complete';
    const SURRENDER_STATUS_DISCS_COMPLETE='surr_sts_discs_complete';
    const SURRENDER_STATUS_LIC_DOCS_COMPLETE='surr_sts_lic_docs_complete';
    const SURRENDER_STATUS_COMM_LIC_DOCS_COMPLETE='surr_sts_comm_lic_docs_complete';
    const SURRENDER_STATUS_DETAILS_CONFIRMED='surr_sts_details_confirmed';
    const SURRENDER_STATUS_SUBMITTED='surr_sts_submitted';
    const SURRENDER_STATUS_SIGNED='surr_sts_signed';
    const SURRENDER_STATUS_WITHDRAWN='surr_sts_withdrawn';
    const SURRENDER_STATUS_APPROVED='surr_sts_approved';
    const SURRENDER_DOC_STATUS_DESTROYED='doc_sts_destroyed';
    const SURRENDER_DOC_STATUS_LOST='doc_sts_lost';
    const SURRENDER_DOC_STATUS_STOLEN='doc_sts_stolen';

    // Report
    const REPORT_TYPE_COMM_LIC_BULK_REPRINT = 'rep_typ_comm_lic_bulk_reprint';

    /**
     * RefData constructor.
     *
     * @param string $id Refdata ID
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->setId($id);
        }
    }
}