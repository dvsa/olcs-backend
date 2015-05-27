<?php

namespace Dvsa\Olcs\Api\Entity\Traits;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Proxy\Proxy;

/**
 * Json Serializable Trait
 */
trait JsonSerializableTrait
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $output = [];

        $vars = get_object_vars($this);

        foreach ($vars as $property => $value) {

            $output[$property] = null;

            if ($value instanceof Proxy) {
                if ($value->__isInitialized()) {
                    $output[$property] = $value;
                }
                continue;
            }

            if ($value instanceof ArrayCollection) {
                $output[$property] = $value->toArray();
                continue;
            }

            if ($value instanceof AbstractLazyCollection) {
                if ($value->isInitialized()) {
                    $output[$property] = $value->toArray();
                }
                continue;
            }

            $output[$property] = $value;
        }

        return array_merge($output, $this->getCalculatedValues());
    }

    /**
     * @return array
     */
    protected function getCalculatedValues()
    {
        return [];
    }
}
