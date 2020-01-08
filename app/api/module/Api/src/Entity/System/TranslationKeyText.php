<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKeyText Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation_key_text",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_text_languages1_idx", columns={"language_id"}),
 *        @ORM\Index(name="fk_translation_key_text_keys1_idx", columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_text_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_text_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class TranslationKeyText extends AbstractTranslationKeyText
{

}
