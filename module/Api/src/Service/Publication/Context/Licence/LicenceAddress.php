<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Licence;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;

/**
 * Class LicenceAddress
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Licence
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceAddress extends AbstractContext
{
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $licenceAddress = $publication->getLicence()->getCorrespondenceCd();

        if ($licenceAddress === null) {
            return $publication;
        }

        $address = new FormatAddress();
        $context->offsetSet('licenceAddress', $address->format($licenceAddress->getAddress()));

        return $context;
    }
}
