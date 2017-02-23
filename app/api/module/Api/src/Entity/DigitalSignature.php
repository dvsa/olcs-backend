<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DigitalSignature Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="digital_signature",
 *    indexes={
 *        @ORM\Index(name="ix_digital_signature_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_digital_signature_tma_id", columns={"tma_id"})
 *    }
 * )
 */
class DigitalSignature extends AbstractDigitalSignature
{
    /**
     * Set the attributes
     *
     * @param array $attributes new value being set
     *
     * @return DigitalSignature
     */
    public function setAttributesArray(array $attributes)
    {
        $this->attributes = json_encode($attributes);

        return $this;
    }

    /**
     * Get the attributes
     *
     * @return array
     */
    public function getAttributesArray()
    {
        $array = json_decode($this->attributes, true);
        return is_array($array) ? $array : [];
    }

    /**
     * Get the full name of the person for attributes
     *
     * @return string
     */
    public function getSignatureName()
    {
        $attributes = $this->getAttributesArray();

        $names = [];
        if (!empty($attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::FIRST_NAME])) {
            $names[] = $attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::FIRST_NAME];
        }
        if (!empty($attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::MIDDLE_NAME])) {
            $names[] = $attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::MIDDLE_NAME];
        }
        if (!empty($attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::SURNAME])) {
            $names[] = $attributes[\Dvsa\Olcs\GdsVerify\Data\Attributes::SURNAME];
        }

        foreach ($names as &$namePart) {
            $namePart = ucfirst(strtolower($namePart));
        }

        return implode(' ', $names);
    }
}
