<?php

namespace Dvsa\OlcsTest\Api\Entity\Traits;

use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;

class StubClearPropertiesWithCollectionsTrait
{
    public $property;
    use ClearPropertiesWithCollectionsTrait;

    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
