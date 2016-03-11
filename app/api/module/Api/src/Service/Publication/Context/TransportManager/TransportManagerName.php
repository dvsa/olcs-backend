<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\TransportManager;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Class TransportManagerName
 * @package Dvsa\Olcs\Api\Service\Publication\Context\TransportManager
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class TransportManagerName extends AbstractContext
{
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $tm = $publication->getTransportManager()->getHomeCd()->getPerson();

        $tmName = $tm->getForename() . ' ' . $tm->getFamilyName();

        $title = $tm->getTitle();
        // title may not be set so only prepend if set
        if ($title instanceof RefDataEntity) {
            $tmName = $tm->getTitle()->getDescription() . ' ' . $tmName;
        }

        $context->offsetSet('transportManagerName', $tmName);

        return $context;
    }
}
