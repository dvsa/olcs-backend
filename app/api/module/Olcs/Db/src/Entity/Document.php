<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Document Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
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
 *        @ORM\Index(name="ix_document_opposition_id", columns={"opposition_id"}),
 *        @ORM\Index(name="ix_document_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_document_irfo_organisation_id", columns={"irfo_organisation_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_document_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Document implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\IrfoOrganisationManyToOne,
        Traits\IsScanField,
        Traits\IssuedDateField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="documents")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg", inversedBy="documents")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="documents")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

    /**
     * Filename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="filename", length=255, nullable=true)
     */
    protected $filename;

    /**
     * Identifier
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_store_id", length=255, nullable=false)
     */
    protected $identifier;

    /**
     * Is external
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_external", nullable=false, options={"default": 0})
     */
    protected $isExternal = 0;

    /**
     * Is read only
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_read_only", nullable=true)
     */
    protected $isReadOnly;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="documents")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre", inversedBy="adDocuments")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=true)
     */
    protected $operatingCentre;

    /**
     * Opposition
     *
     * @var \Olcs\Db\Entity\Opposition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposition", inversedBy="documents")
     * @ORM\JoinColumn(name="opposition_id", referencedColumnName="id", nullable=true)
     */
    protected $opposition;

    /**
     * Size
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="size", nullable=true)
     */
    protected $size;

    /**
     * Sub category
     *
     * @var \Olcs\Db\Entity\SubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SubCategory")
     * @ORM\JoinColumn(name="sub_category_id", referencedColumnName="id", nullable=true)
     */
    protected $subCategory;

    /**
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", inversedBy="documents")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", inversedBy="documents")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManager;

    /**
     * Continuation detail
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ContinuationDetail", mappedBy="checklistDocument")
     */
    protected $continuationDetails;

    /**
     * Template
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\DocTemplate", mappedBy="document")
     */
    protected $templates;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->continuationDetails = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return Document
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return Document
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Document
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the filename
     *
     * @param string $filename
     * @return Document
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the identifier
     *
     * @param string $identifier
     * @return Document
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get the identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the is external
     *
     * @param boolean $isExternal
     * @return Document
     */
    public function setIsExternal($isExternal)
    {
        $this->isExternal = $isExternal;

        return $this;
    }

    /**
     * Get the is external
     *
     * @return boolean
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }

    /**
     * Set the is read only
     *
     * @param string $isReadOnly
     * @return Document
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return string
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return Document
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the operating centre
     *
     * @param \Olcs\Db\Entity\OperatingCentre $operatingCentre
     * @return Document
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Olcs\Db\Entity\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }

    /**
     * Set the opposition
     *
     * @param \Olcs\Db\Entity\Opposition $opposition
     * @return Document
     */
    public function setOpposition($opposition)
    {
        $this->opposition = $opposition;

        return $this;
    }

    /**
     * Get the opposition
     *
     * @return \Olcs\Db\Entity\Opposition
     */
    public function getOpposition()
    {
        return $this->opposition;
    }

    /**
     * Set the size
     *
     * @param int $size
     * @return Document
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the sub category
     *
     * @param \Olcs\Db\Entity\SubCategory $subCategory
     * @return Document
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * Get the sub category
     *
     * @return \Olcs\Db\Entity\SubCategory
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * Set the traffic area
     *
     * @param \Olcs\Db\Entity\TrafficArea $trafficArea
     * @return Document
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Olcs\Db\Entity\TrafficArea
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return Document
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the continuation detail
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $continuationDetails
     * @return Document
     */
    public function setContinuationDetails($continuationDetails)
    {
        $this->continuationDetails = $continuationDetails;

        return $this;
    }

    /**
     * Get the continuation details
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContinuationDetails()
    {
        return $this->continuationDetails;
    }

    /**
     * Add a continuation details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $continuationDetails
     * @return Document
     */
    public function addContinuationDetails($continuationDetails)
    {
        if ($continuationDetails instanceof ArrayCollection) {
            $this->continuationDetails = new ArrayCollection(
                array_merge(
                    $this->continuationDetails->toArray(),
                    $continuationDetails->toArray()
                )
            );
        } elseif (!$this->continuationDetails->contains($continuationDetails)) {
            $this->continuationDetails->add($continuationDetails);
        }

        return $this;
    }

    /**
     * Remove a continuation details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $continuationDetails
     * @return Document
     */
    public function removeContinuationDetails($continuationDetails)
    {
        if ($this->continuationDetails->contains($continuationDetails)) {
            $this->continuationDetails->removeElement($continuationDetails);
        }

        return $this;
    }

    /**
     * Set the template
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return Document
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * Get the templates
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Add a templates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return Document
     */
    public function addTemplates($templates)
    {
        if ($templates instanceof ArrayCollection) {
            $this->templates = new ArrayCollection(
                array_merge(
                    $this->templates->toArray(),
                    $templates->toArray()
                )
            );
        } elseif (!$this->templates->contains($templates)) {
            $this->templates->add($templates);
        }

        return $this;
    }

    /**
     * Remove a templates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return Document
     */
    public function removeTemplates($templates)
    {
        if ($this->templates->contains($templates)) {
            $this->templates->removeElement($templates);
        }

        return $this;
    }
}
