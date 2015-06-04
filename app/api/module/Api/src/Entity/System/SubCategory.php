<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubCategory Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sub_category",
 *    indexes={
 *        @ORM\Index(name="ix_sub_category_category_id", columns={"category_id"}),
 *        @ORM\Index(name="ix_sub_category_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_sub_category_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SubCategory extends AbstractSubCategory
{
    // copy constants from old Common\Service\Data\CategoryDataService as required
    const DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL = 112;
    const DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL = 13;
}
