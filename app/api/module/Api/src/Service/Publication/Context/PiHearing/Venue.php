<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\VenueBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Venue as VenueEntity;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class Venue
 * @package Dvsa\Olcs\Api\Service\Publication\Context\PiHearing
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class Venue extends AbstractContext implements AddressFormatterAwareInterface
{
    private static $bundle = [];

    use AddressFormatterAwareTrait;

    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        if ($context->offsetGet('venue')) {
            $query = VenueBundle::create(['id' => $context->offsetGet('venue'), 'bundle' => self::$bundle]);

            /**
             * @var VenueEntity $venue
             */
            $venue = $this->handleQuery($query);

            $venueDetails = $venue->getName() . ', ' . $this->getAddressFormatter()->format($venue->getAddress());

            $context->offsetSet('venueOther', $venueDetails);
        }

        return $context;
    }
}
