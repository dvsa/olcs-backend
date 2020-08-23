<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartialTagLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="partial_tag_link",
 *    indexes={
 *        @ORM\Index(name="fk_partial_tag_link_partial1_idx", columns={"partial_id"}),
 *        @ORM\Index(name="fk_partial_tag_link_tags1_idx", columns={"tag_id"}),
 *        @ORM\Index(name="fk_partial_tag_link_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_tag_link_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PartialTagLink extends AbstractPartialTagLink
{

}
