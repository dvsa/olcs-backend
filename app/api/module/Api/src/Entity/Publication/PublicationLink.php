<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Cases\Impounding as ImpoundingEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * PublicationLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication_link",
 *    indexes={
 *        @ORM\Index(name="ix_publication_link_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_publication_link_publication_id", columns={"publication_id"}),
 *        @ORM\Index(name="ix_publication_link_pi_id", columns={"pi_id"}),
 *        @ORM\Index(name="ix_publication_link_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_publication_link_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_publication_link_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_publication_link_publication_section_id", columns={"publication_section_id"}),
 *        @ORM\Index(name="ix_publication_link_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_link_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_publication_link_transport_manager1_idx", columns={"transport_manager_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_publication_link_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class PublicationLink extends AbstractPublicationLink
{
    /**
     * Creates Application publication entry
     *
     * @param ApplicationEntity $application
     * @param LicenceEntity $licence
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     */
    public function createApplication(
        ApplicationEntity $application,
        LicenceEntity $licence,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea
    ) {
        $this->application = $application;
        $this->licence = $licence;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Creates a Bus Registration publication entry
     *
     * @param BusRegEntity $busReg
     * @param LicenceEntity $licence
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     * @param string $text1
     */
    public function createBusReg(
        BusRegEntity $busReg,
        LicenceEntity $licence,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea,
        $text1
    ) {
        $this->busReg = $busReg;
        $this->licence = $licence;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
        $this->text1 = $text1;
    }

    /**
     * Creates a Pi hearing publication entry
     *
     * @param LicenceEntity $licence
     * @param PiEntity $pi
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     */
    public function createPiHearing(
        LicenceEntity $licence,
        PiEntity $pi,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea
    ) {
        $this->licence = $licence;
        $this->pi = $pi;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Creates a Tm Pi Hearing publication entry
     *
     * @param TransportManagerEntity $transportManager
     * @param PiEntity $pi
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     */
    public function createTmPiHearing(
        TransportManagerEntity $transportManager,
        PiEntity $pi,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea
    ) {
        $this->transportManager = $transportManager;
        $this->pi = $pi;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Create a publicaction link for a licence
     *
     * @param LicenceEntity $licence
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     */
    public function createLicence(
        LicenceEntity $licence,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea
    ) {
        $this->licence = $licence;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Create a publicaction link for an Impounding
     *
     * @param ImpoundingEntity $impounding
     * @param PublicationEntity $publication
     * @param PublicationSectionEntity $publicationSection
     * @param TrafficAreaEntity $trafficArea
     */
    public function createImpounding(
        ImpoundingEntity $impounding,
        PublicationEntity $publication,
        PublicationSectionEntity $publicationSection,
        TrafficAreaEntity $trafficArea
    ) {
        $this->impounding = $impounding;
        $this->publication = $publication;
        $this->publicationSection = $publicationSection;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Updates text fields on the publication link
     *
     * @param string $text1
     * @param string $text2
     * @param string $text3
     *
     * @throws ForbiddenException
     */
    public function updateText($text1, $text2, $text3)
    {
        if (!$this->publication->isNew()) {
            throw new ForbiddenException('Only publications with status of New may be edited');
        }

        $this->text1 = $text1;
        $this->text2 = $text2;
        $this->text3 = $text3;
    }
}
