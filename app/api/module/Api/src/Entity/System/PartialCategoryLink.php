<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartialCategoryLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="partial_category_link",
 *    indexes={
 *        @ORM\Index(name="fk_partial_category_link_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_partial_category_link_partial1_idx", columns={"partial_id"}),
 *        @ORM\Index(name="fk_partial_category_link_sub_category1_idx", columns={"sub_category_id"}),
 *        @ORM\Index(name="fk_partial_category_link_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_category_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class PartialCategoryLink extends AbstractPartialCategoryLink
{

}
