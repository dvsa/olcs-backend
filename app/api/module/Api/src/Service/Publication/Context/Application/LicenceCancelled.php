<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;

/**
 * Class LicenceCancelled
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceCancelled extends AbstractContext
{
    const LIC_TERMINATED = 'Licence terminated WEF ';
    const LIC_SURRENDERED = 'Licence surrendered WEF ';
    const LIC_CNS = 'Licence not continued WEF ';

    /**
     * @var string $date
     */
    protected $date;

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
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Gets the date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    public function createDate()
    {
        if ($this->getDate() === null) {
            $dateTime = new \DateTime();
            $this->setDate($dateTime->format('j F Y'));
        }

        return $this->getDate();
    }
}
