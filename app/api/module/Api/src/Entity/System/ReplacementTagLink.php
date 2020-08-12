<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReplacementTagLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="replacement_tag_link",
 *    indexes={
 *        @ORM\Index(name="fk_replacement_tag_link_replacement1_idx", columns={"replacement_id"}),
 *        @ORM\Index(name="fk_replacement_tag_link_tags1_idx", columns={"tag_id"}),
 *        @ORM\Index(name="fk_replacement_tag_link_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_replacement_tag_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class ReplacementTagLink extends AbstractReplacementTagLink
{

}
