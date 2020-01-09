<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKeyLocation Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation_key_location",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_location_translation_key1_idx",
     *     columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_location_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_location_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class TranslationKeyLocation extends AbstractTranslationKeyLocation
{

}
