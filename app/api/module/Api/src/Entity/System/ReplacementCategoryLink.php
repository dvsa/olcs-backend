<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReplacementCategoryLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="replacement_category_link",
 *    indexes={
 *        @ORM\Index(name="fk_replacement_category_link_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_replacement_category_link_replacement1_idx", columns={"replacement_id"}),
 *        @ORM\Index(name="fk_replacement_category_link_sub_category1_idx",
     *     columns={"sub_category_id"}),
 *        @ORM\Index(name="fk_replacement_category_link_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_replacement_category_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class ReplacementCategoryLink extends AbstractReplacementCategoryLink
{

}
