<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartialMarkup Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="partial_markup",
 *    indexes={
 *        @ORM\Index(name="fk_partial_markup_partial1", columns={"partial_id"}),
 *        @ORM\Index(name="fk_partial_markup_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_markup_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PartialMarkup extends AbstractPartialMarkup
{
    /**
     * @param Language $language
     * @param Partial $partial
     * @param string $markup
     * @return PartialMarkup
     */
    public static function create(Language $language, Partial $partial, string $markup)
    {
        $instance = new self;

        $instance->language = $language;
        $instance->partial = $partial;
        $instance->markup = $markup;

        return $instance;
    }

    /**
     * @param string $markup
     * @return $this
     */
    public function update(string $markup)
    {
        $this->markup = $markup;
        return $this;
    }
}
