<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\BusReg;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType;

class ServiceTypes extends AbstractContext
{
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $busReg = $publication->getBusReg();
        $busServiceTypes = $busReg->getBusServiceTypes();

        $serviceTypes = [];

        if ($busServiceTypes->count()) {
            /** @var BusServiceType $serviceType */
            foreach ($busServiceTypes as $serviceType) {
                $serviceTypes[] = $serviceType->getDescription();
            }
        }

        $context->offsetSet('busServiceTypes', implode(' / ', $serviceTypes));

        return $context;
    }
}
