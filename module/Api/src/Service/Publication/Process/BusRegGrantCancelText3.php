<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

class BusRegGrantCancelText3 implements ProcessInterface
{
    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $busReg = $publication->getBusReg();

        $text = 'Operating between %s and %s given service number %s effective from %s.';

        $effectiveDate = new \DateTime($busReg->getEffectiveDate());

        $result = sprintf(
            $text,
            $busReg->getStartPoint(),
            $busReg->getFinishPoint(),
            $context->offsetGet('busServices'),
            $effectiveDate->format('d F Y')
        );

        $publication->setText3($result);

        return $publication;
    }
}
