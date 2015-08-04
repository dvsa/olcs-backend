<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;

/**
 * Class Text3
 * @package Dvsa\Olcs\Api\Service\Publication\Process\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class Text3 implements ProcessInterface
{
    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $licType = $publication->getLicence()->getLicenceType();
        $publicationSection = $publication->getPublicationSection()->getId();

        $text = [];

        //GV
        if ($licType === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE) {
            if ($context->offsetExists('licenceCancelled')) {
                $text = $this->getPartialData($context, $text);
            } else {
                switch ($publicationSection) {
                    case PublicationSectionEntity::APP_GRANTED_SECTION:
                        $text = $this->getPartialData($context, $text);
                        break;
                    case PublicationSectionEntity::APP_WITHDRAWN_SECTION:
                    case PublicationSectionEntity::APP_REFUSED_SECTION:
                        $text = $this->getPartialData($context, $text);
                        break;
                    default:
                        $text = $this->getAllData($context, $text);
                        break;
                }
            }
        } else {
            //PSV
            $text = $this->getAllData($context, $text);
        }

        $publication->setText3(implode("\n", $text));

        return $publication;
    }

    public function getAllData(ImmutableArrayObject $context, $text)
    {
        $text = $this->addLicenceAddress($context, $text);
        $text = $this->addBusNote($context, $text);
        $text = $this->getPartialData($context, $text);

        return $text;
    }

    public function getPartialData(ImmutableArrayObject $context, $text)
    {
        $text = $this->addOcDetails($context, $text);
        $text = $this->addConditionUndertaking($context, $text);

        return $text;
    }

    /**
     * Adds oc details, including authorisation and tm
     *
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    private function addOcDetails(ImmutableArrayObject $context, $text)
    {
        //operating centre address
        if ($context->offsetExists('operatingCentres')) {
            foreach ($context->offsetGet('operatingCentres') as $oc) {
                $text[] = $oc;
            }
        }

        //transport managers
        if ($context->offsetExists('transportManagers')) {
            $text[] = 'Transport Manager(s): ' . $context->offsetGet('transportManagers');
        }

        return $text;
    }

    /**
     * Adds condition and undertaking
     *
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    private function addConditionUndertaking(ImmutableArrayObject $context, $text)
    {
        //conditions and undertakings
        $conditionUndertaking = $context->offsetGet('conditionUndertaking');

        foreach ($conditionUndertaking as $cuString) {
            $text[] = $cuString;
        }

        return $text;
    }

    /**
     * Adds licence address
     *
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    private function addLicenceAddress(ImmutableArrayObject $context, $text)
    {
        $text[] = $context->offsetGet('licenceAddress');
        return $text;
    }

    /**
     * If we have a bus note, add it in
     *
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    private function addBusNote(ImmutableArrayObject $context, $text)
    {
        if ($context->offsetExists('busNote')) {
            $text[] = $context->offsetGet('busNote');
        }

        return $text;
    }
}
