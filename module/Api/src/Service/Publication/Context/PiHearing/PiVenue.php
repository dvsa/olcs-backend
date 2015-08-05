<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PiVenueBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue as PiVenueEntity;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class PiVenue
 * @package Dvsa\Olcs\Api\Service\Publication\Context\PiHearing
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class PiVenue extends AbstractContext implements AddressFormatterAwareInterface
{
    private static $bundle = [];

    use AddressFormatterAwareTrait;

    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        if ($context->offsetGet('piVenue')) {
            $query = PiVenueBundle::create(['id' => $context->offsetGet('piVenue'), 'bundle' => self::$bundle]);

            /**
             * @var PiVenueEntity $piVenue
             */
            $piVenue = $this->handleQuery($query);

            $venueDetails = $piVenue->getName() . ', ' . $this->getAddressFormatter()->format($piVenue->getAddress());

            $context->offsetSet('piVenueOther', $venueDetails);
        }

        return $context;
    }
}
