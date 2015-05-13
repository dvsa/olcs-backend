<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication",
 *    indexes={
 *        @ORM\Index(name="ix_publication_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_publication_pub_status", columns={"pub_status"}),
 *        @ORM\Index(name="ix_publication_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_publication_document1_idx", columns={"document_id"}),
 *        @ORM\Index(name="fk_publication_doc_template1_idx", columns={"doc_template_id"})
 *    }
 * )
 */
class Publication extends AbstractPublication
{

}
