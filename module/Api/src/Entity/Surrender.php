<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Surrender Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="surrender",
 *    indexes={
 *        @ORM\Index(name="surrender_licence_document_ref_data_id_fk",
     *     columns={"licence_document_status"}),
 *        @ORM\Index(name="surrender_fk_community_licence_document_status_ref_data_id",
     *     columns={"community_licence_document_status"}),
 *        @ORM\Index(name="surrender_fk_digital_signature_id_ref_data_id",
     *     columns={"digital_signature_id"}),
 *        @ORM\Index(name="surrender_fk_last_modified", columns={"last_modified_by"}),
 *        @ORM\Index(name="surrender_status_index", columns={"status"}),
 *        @ORM\Index(name="surrender_created_by_index", columns={"created_by"}),
 *        @ORM\Index(name="surrender__index_licence", columns={"licence_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="surrender_id_uindex", columns={"id"})
 *    }
 * )
 */
class Surrender extends AbstractSurrender
{
    const SURRENDER_STATUS_START='surrender_status_start';
    const SURRENDER_STATUS_CONTACTS_COMPLETE='surrender_status_contacts_complete';
    const SURRENDER_STATUS_DISCS_COMPLETE='surrender_status_discs_complete';
    const SURRENDER_STATUS_LIC_DOCS_COMPLETE='surrender_status_lic_docs_completE';
    const SURRENDER_STATUS_COMM_LIC_DOCS_COMPLETE='surrender_status_comm_lic_docs_complete';
    const SURRENDER_STATUS_DETAILS_CONFIRMED='surrender_status_details_confirmed';
    const SURRENDER_STATUS_SUBMITTED='surrender_status_submitted';
    const SURRENDER_STATUS_SIGNED='surrender_status_signed';
    const SURRENDER_STATUS_APPROVED='surrender_status_approved';
    const SURRENDER_DOC_STATUS_DESTROYED='doc_status_destroyed';
    const SURRENDER_DOC_STATUS_LOST='doc_status_lost';
    const SURRENDER_DOC_STATUS_STOLEN='doc_status_stolen';
}
