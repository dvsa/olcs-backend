<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Licence;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class LicenceAddress
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Licence
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class LicenceAddress extends AbstractContext implements AddressFormatterAwareInterface
{
    use AddressFormatterAwareTrait;

    public function provide(PublicationLink $publicationLink, \ArrayObject $context)
    {
        $licence = $publicationLink->getLicence();
        if (!empty($licence)) {
            $licenceAddress = $publicationLink->getLicence()->getCorrespondenceCd();

            if ($licenceAddress === null) {
                return $context;
            }

            $context->offsetSet('licenceAddress', $this->getAddressFormatter()->format($licenceAddress->getAddress()));
        } else {
            $context->offsetSet('licenceAddress', '');
        }

        return $context;
    }
}
