<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;
use Dvsa\Olcs\Api\Service\Publication\Process\Text1 as AbstractText1;

/**
 * Class Text2
 * @package Dvsa\Olcs\Api\Service\Publication\Process\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class Text2 extends AbstractText1 implements ProcessInterface
{
    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $licType = $publication->getApplication()->getGoodsOrPsv()->getId();
        $publicationSection = $publication->getPublicationSection()->getId();

        $text = [];

        //licence cancellation
        if ($context->offsetExists('licenceCancelled')) {
            //PSV licence cancellation
            if ($licType == LicenceEntity::LICENCE_CATEGORY_PSV) {
                $text = $this->getPsvCancelled($publication, $context, $text);
            } else {
                $text = $this->getGvCancelled($publication, $context, $text);
            }
        } elseif ($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE) {
            //non cancellation GV
            switch ($publicationSection) {
                case PublicationSectionEntity::APP_GRANTED_SECTION:
                    $text = $this->getAllData($publication, $text);
                    break;
                case PublicationSectionEntity::APP_WITHDRAWN_SECTION:
                    $text[] = $this->getLicenceInfo($publication->getLicence());
                    break;
                case PublicationSectionEntity::APP_REFUSED_SECTION:
                default:
                    $text = $this->getAllData($publication, $text);
                    break;
            }
        } else {
            //non cancellation PSV
            $text = $this->getAllData($publication, $text);
        }

        $publication->setText2(implode("\n", $text));

        return $publication;
    }

    /**
     * @param PublicationLink $publication
     * @param array $text
     * @return array
     */
    public function getAllData(PublicationLink $publication, $text)
    {
        $text[] = $this->getLicenceInfo($publication->getLicence());
        $text[] = $this->getPersonInfo($publication->getLicence()->getOrganisation());

        return $text;
    }

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    public function getGvCancelled(PublicationLink $publication, ImmutableArrayObject $context, $text)
    {
        $text[] = $context->offsetGet('licenceCancelled');
        $text[] = $this->getLicenceInfo($publication->getLicence()) . "\n";

        return $text;
    }

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @param array $text
     * @return array
     */
    public function getPsvCancelled(PublicationLink $publication, ImmutableArrayObject $context, $text)
    {
        $text = $this->getGvCancelled($publication, $context, $text);
        $text[] = $this->getPersonInfo($publication->getLicence()->getOrganisation());

        return $text;
    }

    /**
     * @param LicenceEntity $licence
     * @return string
     */
    public function getLicenceInfo(LicenceEntity $licence)
    {
        $organisation = $licence->getOrganisation();
        $tradingNames = $organisation->getTradingNames();

        $text = $organisation->getName();

        if (!$tradingNames->isEmpty()) {
            $latestTradingName = $tradingNames->last();
            $text .= " " . sprintf($this->tradingAs, $latestTradingName->getName());
        }

        return strtoupper($text);
    }
}
