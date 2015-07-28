<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as TransportManagerApplicationEntity;

class TransportManagers extends AbstractContext
{
    /**
     * @param PublicationLink $publication
     * @param \ArrayObject $context
     * @return \ArrayObject
     */
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $tmData = $publication->getApplication()->getTransportManagers();

        $newTmData = [];

        /**
         * @var TransportManagerApplicationEntity $tm
         */
        foreach ($tmData as $tm) {
            $forename = $tm->getTransportManager()->getHomeCd()->getPerson()->getForename();
            $familyName = $tm->getTransportManager()->getHomeCd()->getPerson()->getFamilyName();

            $newTmData[] = trim($forename . ' ' . $familyName);
        }

        if (!empty($newTmData)) {
            $context->offsetSet('transportManagers', implode(', ', $newTmData));
        }

        return $context;
    }
}
