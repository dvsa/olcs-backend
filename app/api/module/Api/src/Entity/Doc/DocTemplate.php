<?php

namespace Dvsa\Olcs\Api\Entity\Doc;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocTemplate Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="doc_template",
 *    indexes={
 *        @ORM\Index(name="ix_doc_template_sub_category_id", columns={"sub_category_id"}),
 *        @ORM\Index(name="ix_doc_template_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_doc_template_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_doc_template_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_doc_template_category_id", columns={"category_id"})
 *    }
 * )
 */
class DocTemplate extends AbstractDocTemplate
{

}
