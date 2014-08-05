<?php

/**
 * CustomBaseEntity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Entity\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CustomBaseEntity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CustomBaseEntity
{
    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
