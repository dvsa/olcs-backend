<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Licence;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;

/**
 * Class LicenceCancelled
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class LicenceCancelled extends AbstractContext
{
    public const LIC_TERMINATED = 'Licence terminated WEF ';
    public const LIC_SURRENDERED = 'Licence surrendered WEF ';
    public const LIC_CNS = 'Licence not continued WEF ';

    /**
     * @var string $date
     */
    private $date;

    /**
     * @param PublicationLink $publication
     * @param \ArrayObject $context
     * @return \ArrayObject
     */
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $publicationSection = $publication->getPublicationSection()->getId();

        switch ($publicationSection) {
            case PublicationSectionEntity::LIC_SURRENDERED_SECTION:
                $context->offsetSet('licenceCancelled', self::LIC_SURRENDERED . $this->createDate());
                break;
            case PublicationSectionEntity::LIC_TERMINATED_SECTION:
                $context->offsetSet('licenceCancelled', self::LIC_TERMINATED . $this->createDate());
                break;
            case PublicationSectionEntity::LIC_CNS_SECTION:
                $context->offsetSet('licenceCancelled', self::LIC_CNS . $this->createDate());
                break;
        }

        return $context;
    }

    /**
     * Allows easier unit testing
     */
    public function createDate()
    {
        if ($this->date === null) {
            $dateTime = new \DateTime();
            $this->date = $dateTime->format('j F Y');
        }

        return $this->date;
    }
}
