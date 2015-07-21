<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Publication;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousPublicationForPiBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

class PreviousPublicationNo extends AbstractContext
{
    private static $bundle = [];

    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $params = [
            'pi' => $publication->getPi()->getId(),
            'pubType' => $publication->getPublication()->getPubType(),
            'trafficArea' => $publication->getTrafficArea(),
            'publicationNo' => $publication->getPublication()->getPublicationNo(),
            'bundle' => self::$bundle
        ];

        $query = PreviousPublicationForPiBundle::create($params);
        $previousPublication = $this->handleQuery($query);

        if (!empty($previousPublication)) {
            $context->offsetSet('previousPublication', $previousPublication->getPublication->getPublicationNo());
        }

        return $context;
    }
}
