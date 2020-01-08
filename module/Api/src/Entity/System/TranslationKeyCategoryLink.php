<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKeyCategoryLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation_key_category_link",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_category_link_translation_key1_idx",
     *     columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_category_link_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_translation_key_category_link_sub_category1_idx",
     *     columns={"sub_category_id"}),
 *        @ORM\Index(name="fk_translation_key_category_link_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_category_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class TranslationKeyCategoryLink extends AbstractTranslationKeyCategoryLink
{

}
