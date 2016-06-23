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

    public function __construct($identifier)
    {
        parent::__construct();

        $this->setIdentifier($identifier);
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
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

        return null;
    }
}
