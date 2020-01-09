<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKeyTagLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation_key_tag_link",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_tag_link_tags1_idx", columns={"tag_id"}),
 *        @ORM\Index(name="fk_translation_key_tag_link_translation_key1_idx",
     *     columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_tag_link_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_tag_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class TranslationKeyTagLink extends AbstractTranslationKeyTagLink
{

}
