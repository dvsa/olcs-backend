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
    const GV_CONTINUATION_CHECKLIST = 1252;
    const GV_CONTINUATION_CHECKLIST_NI = 1501;
    const PSV_CONTINUATION_CHECKLIST = 1302;
    const PSV_CONTINUATION_CHECKLIST_SR = 1303;

    const GV_LICENCE_GB     = 1254; //templates/GB/GV_LICENCE_V1.rtf
    const PSV_LICENCE_GB    = 1255; //templates/GB/PSV_LICENCE_V1.rtf
    const PSR_SR_LICENCE_GB = 1310; //templates/GB/PSVSRLicence.rtf
    const GV_LICENCE_NI     = 1512; //templates/NI/GV_LICENCE_V1.rtf
    const PSV_LICENCE_NI    = 1516; //templates/NI/PSV_LICENCE_V1.rtf
    const PSR_SR_LICENCE_NI = 1518; //templates/NI/PSVSRLicence.rtf

    const BUS_REG_NEW = 1236;           //  /templates/GB/BUS_REG_NEW_REGISTRATION_TAN21.rtf
    const BUS_REG_VARIATION = 1237;     //  /templates/GB/BUS_REG_VARIATION_TAN21.rtf
    const BUS_REG_CANCELLATION = 1238;   //  /templates/GB/BUS_REG_CANCELLATION.rtf
    const BUS_REG_NEW_REFUSE_SHORT_NOTICE = 1239;  //  /templates/GB/BUS_REG_NEW_REGISTRATION_REFUSE_SHORT_NOTICE.rtf
    const BUS_REG_VARIATION_REFUSE_SHORT_NOTICE = 1240;  //  /templates/GB/BUS_REG_VARIATION_REFUSE_SHORT_NOTICE.rtf
    const BUS_REG_CANCELLATION_REFUSE_SHORT_NOTICE = 1241;   //  /te.../GB/BUS_REG_CANCELLATION_REFUSE_SHORT_NOTICE.rtf

    const GV_DISC_LETTER_GB  = 1730; // /templates/GB/GVDiscLetter.rtf
    const GV_DISC_LETTER_NI  = 1731; // /templates/NI/GVDiscLetter.rtf
    const GV_VEHICLE_LIST_GB = 1258; // /templates/GB/GVVehiclesList.rtf
    const GV_VEHICLE_LIST_NI = 1513; // /templates/NI/GVVehiclesList.rtf

    const LICENCE_TERMINATED_CONT_FEE_NOT_PAID_GB = 1041; // /tempates/GB/CNS_Letter_to_operator.rtf
    const LICENCE_TERMINATED_CONT_FEE_NOT_PAID_NI = 1433; // /tempates/NI/CNS_Letter_to_operator.rtf

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

        return null;
    }
}
