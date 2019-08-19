<?php

namespace Dvsa\Olcs\Api\Entity\Doc;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

/**
 * Document Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="document",
 *    indexes={
 *        @ORM\Index(name="ix_document_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_document_category_id", columns={"category_id"}),
 *        @ORM\Index(name="ix_document_sub_category_id", columns={"sub_category_id"}),
 *        @ORM\Index(name="ix_document_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_document_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_document_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_document_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_document_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_document_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_document_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_document_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_document_irfo_organisation_id", columns={"irfo_organisation_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_document_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Document extends AbstractDocument implements OrganisationProviderInterface
{
    const GV_CONTINUATION_CHECKLIST = 'GVChecklist';
    const GV_CONTINUATION_CHECKLIST_NI = 'GVChecklist';
    const PSV_CONTINUATION_CHECKLIST = 'PSVChecklist';
    const PSV_CONTINUATION_CHECKLIST_SR = 'PSVSRChecklist';

    const GV_LICENCE_GB     = 'GV_LICENCE_V1';
    const PSV_LICENCE_GB    = 'PSV_LICENCE_V1';
    const PSR_SR_LICENCE_GB = 'PSVSRLicence';
    const GV_LICENCE_NI     = 'GV_LICENCE_V1';
    const PSV_LICENCE_NI    = 'PSV_LICENCE_V1';
    const PSR_SR_LICENCE_NI = 'PSVSRLicence';

    const BUS_REG_NEW = 'BUS_REG_NEW_REGISTRATION_TAN21';
    const BUS_REG_VARIATION = 'BUS_REG_VARIATION_TAN21';
    const BUS_REG_CANCELLATION = 'BUS_REG_CANCELLATION';
    const BUS_REG_NEW_REFUSE_SHORT_NOTICE = 'BUS_REG_NEW_REGISTRATION_REFUSE_SHORT_NOTICE';
    const BUS_REG_VARIATION_REFUSE_SHORT_NOTICE = 'BUS_REG_VARIATION_REFUSE_SHORT_NOTICE';
    const BUS_REG_CANCELLATION_REFUSE_SHORT_NOTICE = 'BUS_REG_CANCELLATION_REFUSE_SHORT_NOTICE';

    const GV_DISC_LETTER_GB  = 'GVDiscLetter';
    const GV_DISC_LETTER_NI  = 'GVDiscLetter';
    const GV_VEHICLE_LIST_GB = 'GVVehiclesList';
    const GV_VEHICLE_LIST_NI = 'GVVehiclesList';

    const LICENCE_TERMINATED_CONT_FEE_NOT_PAID_GB = 'LICENCE_TERMINATED_CONT_FEE_NOT_PAID';
    const LICENCE_TERMINATED_CONT_FEE_NOT_PAID_NI = 'LICENCE_TERMINATED_CONT_FEE_NOT_PAID';

    const GV_UK_COMMUNITY_LICENCE_GB = 'UK_licence_for_the_Community_GV_GB';
    const GV_UK_COMMUNITY_LICENCE_GB_COVER_LETTER = 'UK_licence_for_the_Community_Cover_Letter_GV_GB';
    const GV_UK_COMMUNITY_LICENCE_NI = 'UK_licence_for_the_Community_GV_NI';
    const GV_UK_COMMUNITY_LICENCE_NI_COVER_LETTER = 'UK_licence_for_the_Community_Cover_Letter_GV_NI';
    const GV_UK_COMMUNITY_LICENCE_PSV = 'PSV_certified_copy';

    const IRHP_PERMIT_SHORT_TERM_ECMT = 'IRHP_PERMIT_SHORT_TERM_ECMT';
    const IRHP_PERMIT_SHORT_TERM_ECMT_COVER_LETTER = 'IRHP_PERMIT_SHORT_TERM_ECMT_COVER_LETTER';

    const IRHP_PERMIT_ANN_BILAT_AUSTRIA = 'IRHP_PERMIT_ANN_BILAT_AUSTRIA';
    const IRHP_PERMIT_ANN_BILAT_BELGIUM = 'IRHP_PERMIT_ANN_BILAT_BELGIUM';
    const IRHP_PERMIT_ANN_BILAT_BULGARIA = 'IRHP_PERMIT_ANN_BILAT_BULGARIA';
    const IRHP_PERMIT_ANN_BILAT_CROATIA = 'IRHP_PERMIT_ANN_BILAT_CROATIA';
    const IRHP_PERMIT_ANN_BILAT_CYPRUS = 'IRHP_PERMIT_ANN_BILAT_CYPRUS';
    const IRHP_PERMIT_ANN_BILAT_CZECH_REPUBLIC = 'IRHP_PERMIT_ANN_BILAT_CZECH_REPUBLIC';
    const IRHP_PERMIT_ANN_BILAT_DENMARK = 'IRHP_PERMIT_ANN_BILAT_DENMARK';
    const IRHP_PERMIT_ANN_BILAT_ESTONIA = 'IRHP_PERMIT_ANN_BILAT_ESTONIA';
    const IRHP_PERMIT_ANN_BILAT_FINLAND = 'IRHP_PERMIT_ANN_BILAT_FINLAND';
    const IRHP_PERMIT_ANN_BILAT_FRANCE = 'IRHP_PERMIT_ANN_BILAT_FRANCE';
    const IRHP_PERMIT_ANN_BILAT_GERMANY = 'IRHP_PERMIT_ANN_BILAT_GERMANY';
    const IRHP_PERMIT_ANN_BILAT_GREECE = 'IRHP_PERMIT_ANN_BILAT_GREECE';
    const IRHP_PERMIT_ANN_BILAT_HUNGARY = 'IRHP_PERMIT_ANN_BILAT_HUNGARY';
    const IRHP_PERMIT_ANN_BILAT_ICELAND = 'IRHP_PERMIT_ANN_BILAT_ICELAND';
    const IRHP_PERMIT_ANN_BILAT_IRELAND = 'IRHP_PERMIT_ANN_BILAT_IRELAND';
    const IRHP_PERMIT_ANN_BILAT_ITALY = 'IRHP_PERMIT_ANN_BILAT_ITALY';
    const IRHP_PERMIT_ANN_BILAT_LATVIA = 'IRHP_PERMIT_ANN_BILAT_LATVIA';
    const IRHP_PERMIT_ANN_BILAT_LIECHTENSTEIN = 'IRHP_PERMIT_ANN_BILAT_LIECHTENSTEIN';
    const IRHP_PERMIT_ANN_BILAT_LITHUANIA = 'IRHP_PERMIT_ANN_BILAT_LITHUANIA';
    const IRHP_PERMIT_ANN_BILAT_LUXEMBOURG = 'IRHP_PERMIT_ANN_BILAT_LUXEMBOURG';
    const IRHP_PERMIT_ANN_BILAT_MALTA = 'IRHP_PERMIT_ANN_BILAT_MALTA';
    const IRHP_PERMIT_ANN_BILAT_NETHERLANDS = 'IRHP_PERMIT_ANN_BILAT_NETHERLANDS';
    const IRHP_PERMIT_ANN_BILAT_NORWAY = 'IRHP_PERMIT_ANN_BILAT_NORWAY';
    const IRHP_PERMIT_ANN_BILAT_POLAND = 'IRHP_PERMIT_ANN_BILAT_POLAND';
    const IRHP_PERMIT_ANN_BILAT_PORTUGAL = 'IRHP_PERMIT_ANN_BILAT_PORTUGAL';
    const IRHP_PERMIT_ANN_BILAT_ROMANIA = 'IRHP_PERMIT_ANN_BILAT_ROMANIA';
    const IRHP_PERMIT_ANN_BILAT_SLOVAKIA = 'IRHP_PERMIT_ANN_BILAT_SLOVAKIA';
    const IRHP_PERMIT_ANN_BILAT_SLOVENIA = 'IRHP_PERMIT_ANN_BILAT_SLOVENIA';
    const IRHP_PERMIT_ANN_BILAT_SPAIN = 'IRHP_PERMIT_ANN_BILAT_SPAIN';
    const IRHP_PERMIT_ANN_BILAT_SWEDEN = 'IRHP_PERMIT_ANN_BILAT_SWEDEN';
    const IRHP_PERMIT_ANN_BILAT_COVERING_LETTER = 'IRHP_PERMIT_ANN_BILAT_COVERING_LETTER';

    const IRHP_PERMIT_ANN_MULTILAT = 'IRHP_PERMIT_ANN_MULTILATERAL';
    const IRHP_PERMIT_ANN_MULTILAT_COVERING_LETTER = 'IRHP_PERMIT_ANN_MULTILAT_COVERING_LETTER';

    /**
     * Document constructor.
     *
     * @param string $identifier document identifier
     *
     * @return void
     */
    public function __construct($identifier)
    {
        parent::__construct();

        $this->setIdentifier($identifier);
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        parent::setCreatedOnBeforePersist();

        if ($this->getIssuedDate() === null) {
            $this->setIssuedDate(new \DateTime());
        }
    }

    /**
     * Get organisations this entity is linked to
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation|\Dvsa\Olcs\Api\Entity\Organisation\Organisation[]|null
     */
    public function getRelatedOrganisation()
    {
        if ($this->getLicence()) {
            return $this->getLicence()->getRelatedOrganisation();
        }

        if ($this->getApplication()) {
            return $this->getApplication()->getRelatedOrganisation();
        }

        if ($this->getTransportManager()) {
            return $this->getTransportManager()->getRelatedOrganisation();
        }

        if ($this->getCase()) {
            return $this->getCase()->getRelatedOrganisation();
        }

        if ($this->getOperatingCentre()) {
            return $this->getOperatingCentre()->getRelatedOrganisation();
        }

        if ($this->getBusReg()) {
            return $this->getBusReg()->getRelatedOrganisation();
        }

        if ($this->getIrfoOrganisation()) {
            return $this->getIrfoOrganisation()->getRelatedOrganisation();
        }

        if ($this->getSubmission()) {
            return $this->getSubmission()->getRelatedOrganisation();
        }

        if ($this->getStatement()) {
            return $this->getStatement()->getRelatedOrganisation();
        }

        if ($this->getEbsrSubmission()) {
            return $this->getEbsrSubmission()->getRelatedOrganisation();
        }

        if ($this->getContinuationDetail()) {
            return $this->getContinuationDetail()->getRelatedOrganisation();
        }

        if ($this->getIrhpApplication()) {
            return $this->getIrhpApplication()->getRelatedOrganisation();
        }

        return null;
    }

    /**
     * Return licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence|null
     */
    public function getRelatedLicence()
    {
        if ($this->getLicence() !== null) {
            return $this->getLicence();
        }

        $application = $this->getApplication();
        if ($application !== null) {
            return $application->getLicence();
        }

        $case = $this->getCase();
        if ($case !== null) {
            return $case->getLicence();
        }

        $busReg = $this->getBusReg();
        if ($busReg !== null) {
            return $busReg->getLicence();
        }

        $irhpApplication = $this->getIrhpApplication();
        if ($irhpApplication !== null) {
            return $irhpApplication->getLicence();
        }

        return null;
    }
}
