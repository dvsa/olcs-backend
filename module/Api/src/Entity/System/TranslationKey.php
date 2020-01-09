<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKey Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation_key",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class TranslationKey extends AbstractTranslationKey
{

}
