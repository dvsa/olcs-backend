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

}
