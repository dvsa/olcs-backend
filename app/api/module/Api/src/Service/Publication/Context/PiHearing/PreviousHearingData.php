<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousHearingBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

/**
 * Class PreviousHearingData
 * @package Dvsa\Olcs\Api\Service\Publication\Context\PiHearing
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousHearingData extends AbstractContext
{
    private static $bundle = [];

    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $params = [
            'pi' => $publication->getPi()->getId(),
            'hearingDate' => $context->offsetGet('hearingDate'),
            'bundle' => self::$bundle
        ];

        $query = PreviousHearingBundle::create($params);
        $previousHearing = $this->handleQuery($query);

        if (!empty($previousHearing)) {
            $context->offsetSet('previousHearing', $previousHearing->getAdjournedDate());
        }

        return $context;
    }
}
