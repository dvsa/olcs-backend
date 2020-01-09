<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="language",
 *    indexes={
 *        @ORM\Index(name="fk_language_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_language_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Language extends AbstractLanguage
{

}
