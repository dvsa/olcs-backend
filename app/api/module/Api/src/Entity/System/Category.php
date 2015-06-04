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
    // copy constants from old Common\Service\Data\CategoryDataService as required
    const CATEGORY_LICENSING = 1;
    const CATEGORY_APPLICATION = 9;
}
