<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\TransportManager;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

class TransportManagerName extends AbstractContext
{
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $tm = $publication->getTransportManager()->getHomeCd()->getPerson();
        $tmName = $tm->getTitle()->getDescription() . ' ' . $tm->getForename() . ' ' . $tm->getFamilyName();
        $context->offsetSet('transportManagerName', $tmName);

        return $context;
    }
}
