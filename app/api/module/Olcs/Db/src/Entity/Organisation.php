<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Organisation Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="organisation",
 *    indexes={
 *        @ORM\Index(name="fk_organisation_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_organisation_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_organisation_ref_data1_idx", columns={"type"}),
 *        @ORM\Index(name="fk_organisation_traffic_area1_idx", columns={"lead_tc_area_id"}),
 *        @ORM\Index(name="organisation_name_idx", columns={"name"})
 *    }
 * )
 */
class Organisation implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ViAction1Field,
        Traits\IsIrfoField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomCreatedOnField,
        Traits\CustomVersionField;

    /**
     * Lead tc area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="lead_tc_area_id", referencedColumnName="id", nullable=true)
     */
    protected $leadTcArea;

    /**
     * Type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * Company or llp no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_or_llp_no", length=20, nullable=true)
     */
    protected $companyOrLlpNo;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=160, nullable=true)
     */
    protected $name;

    /**
     * Irfo name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_name", length=160, nullable=true)
     */
    protected $irfoName;

    /**
     * Is mlh
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_mlh", nullable=false)
     */
    protected $isMlh = 0;

    /**
     * Company cert seen
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="company_cert_seen", nullable=false)
     */
    protected $companyCertSeen = 0;

    /**
     * Irfo nationality
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_nationality", length=45, nullable=true)
     */
    protected $irfoNationality;

    /**
     * Allow email
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="allow_email", nullable=false)
     */
    protected $allowEmail = 0;

    /**
     * Contact detail
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ContactDetails", mappedBy="organisation")
     */
    protected $contactDetails;

    /**
     * Licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Licence", mappedBy="organisation")
     */
    protected $licences;

    /**
     * Nature of business
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationNatureOfBusiness", mappedBy="organisation")
     */
    protected $natureOfBusinesss;

    /**
     * Organisation person
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationPerson", mappedBy="organisation")
     */
    protected $organisationPersons;

    /**
     * Trading name
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TradingName", mappedBy="organisation")
     */
    protected $tradingNames;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->contactDetails = new ArrayCollection();
        $this->licences = new ArrayCollection();
        $this->natureOfBusinesss = new ArrayCollection();
        $this->organisationPersons = new ArrayCollection();
        $this->tradingNames = new ArrayCollection();
    }

    /**
     * Set the lead tc area
     *
     * @param \Olcs\Db\Entity\TrafficArea $leadTcArea
     * @return Organisation
     */
    public function setLeadTcArea($leadTcArea)
    {
        $this->leadTcArea = $leadTcArea;

        return $this;
    }

    /**
     * Get the lead tc area
     *
     * @return \Olcs\Db\Entity\TrafficArea
     */
    public function getLeadTcArea()
    {
        return $this->leadTcArea;
    }

    /**
     * Set the type
     *
     * @param \Olcs\Db\Entity\RefData $type
     * @return Organisation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the company or llp no
     *
     * @param string $companyOrLlpNo
     * @return Organisation
     */
    public function setCompanyOrLlpNo($companyOrLlpNo)
    {
        $this->companyOrLlpNo = $companyOrLlpNo;

        return $this;
    }

    /**
     * Get the company or llp no
     *
     * @return string
     */
    public function getCompanyOrLlpNo()
    {
        return $this->companyOrLlpNo;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return Organisation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the irfo name
     *
     * @param string $irfoName
     * @return Organisation
     */
    public function setIrfoName($irfoName)
    {
        $this->irfoName = $irfoName;

        return $this;
    }

    /**
     * Get the irfo name
     *
     * @return string
     */
    public function getIrfoName()
    {
        return $this->irfoName;
    }

    /**
     * Set the is mlh
     *
     * @param string $isMlh
     * @return Organisation
     */
    public function setIsMlh($isMlh)
    {
        $this->isMlh = $isMlh;

        return $this;
    }

    /**
     * Get the is mlh
     *
     * @return string
     */
    public function getIsMlh()
    {
        return $this->isMlh;
    }

    /**
     * Set the company cert seen
     *
     * @param string $companyCertSeen
     * @return Organisation
     */
    public function setCompanyCertSeen($companyCertSeen)
    {
        $this->companyCertSeen = $companyCertSeen;

        return $this;
    }

    /**
     * Get the company cert seen
     *
     * @return string
     */
    public function getCompanyCertSeen()
    {
        return $this->companyCertSeen;
    }

    /**
     * Set the irfo nationality
     *
     * @param string $irfoNationality
     * @return Organisation
     */
    public function setIrfoNationality($irfoNationality)
    {
        $this->irfoNationality = $irfoNationality;

        return $this;
    }

    /**
     * Get the irfo nationality
     *
     * @return string
     */
    public function getIrfoNationality()
    {
        return $this->irfoNationality;
    }

    /**
     * Set the allow email
     *
     * @param string $allowEmail
     * @return Organisation
     */
    public function setAllowEmail($allowEmail)
    {
        $this->allowEmail = $allowEmail;

        return $this;
    }

    /**
     * Get the allow email
     *
     * @return string
     */
    public function getAllowEmail()
    {
        return $this->allowEmail;
    }

    /**
     * Set the contact detail
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Organisation
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Add a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Organisation
     */
    public function addContactDetails($contactDetails)
    {
        if ($contactDetails instanceof ArrayCollection) {
            $this->contactDetails = new ArrayCollection(
                array_merge(
                    $this->contactDetails->toArray(),
                    $contactDetails->toArray()
                )
            );
        } elseif (!$this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->add($contactDetails);
        }

        return $this;
    }

    /**
     * Remove a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Organisation
     */
    public function removeContactDetails($contactDetails)
    {
        if ($this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->removeElement($contactDetails);
        }

        return $this;
    }

    /**
     * Set the licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licences
     * @return Organisation
     */
    public function setLicences($licences)
    {
        $this->licences = $licences;

        return $this;
    }

    /**
     * Get the licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLicences()
    {
        return $this->licences;
    }

    /**
     * Add a licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licences
     * @return Organisation
     */
    public function addLicences($licences)
    {
        if ($licences instanceof ArrayCollection) {
            $this->licences = new ArrayCollection(
                array_merge(
                    $this->licences->toArray(),
                    $licences->toArray()
                )
            );
        } elseif (!$this->licences->contains($licences)) {
            $this->licences->add($licences);
        }

        return $this;
    }

    /**
     * Remove a licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licences
     * @return Organisation
     */
    public function removeLicences($licences)
    {
        if ($this->licences->contains($licences)) {
            $this->licences->removeElement($licences);
        }

        return $this;
    }

    /**
     * Set the nature of business
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesss
     * @return Organisation
     */
    public function setNatureOfBusinesss($natureOfBusinesss)
    {
        $this->natureOfBusinesss = $natureOfBusinesss;

        return $this;
    }

    /**
     * Get the nature of businesss
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getNatureOfBusinesss()
    {
        return $this->natureOfBusinesss;
    }

    /**
     * Add a nature of businesss
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesss
     * @return Organisation
     */
    public function addNatureOfBusinesss($natureOfBusinesss)
    {
        if ($natureOfBusinesss instanceof ArrayCollection) {
            $this->natureOfBusinesss = new ArrayCollection(
                array_merge(
                    $this->natureOfBusinesss->toArray(),
                    $natureOfBusinesss->toArray()
                )
            );
        } elseif (!$this->natureOfBusinesss->contains($natureOfBusinesss)) {
            $this->natureOfBusinesss->add($natureOfBusinesss);
        }

        return $this;
    }

    /**
     * Remove a nature of businesss
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesss
     * @return Organisation
     */
    public function removeNatureOfBusinesss($natureOfBusinesss)
    {
        if ($this->natureOfBusinesss->contains($natureOfBusinesss)) {
            $this->natureOfBusinesss->removeElement($natureOfBusinesss);
        }

        return $this;
    }

    /**
     * Set the organisation person
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationPersons
     * @return Organisation
     */
    public function setOrganisationPersons($organisationPersons)
    {
        $this->organisationPersons = $organisationPersons;

        return $this;
    }

    /**
     * Get the organisation persons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrganisationPersons()
    {
        return $this->organisationPersons;
    }

    /**
     * Add a organisation persons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationPersons
     * @return Organisation
     */
    public function addOrganisationPersons($organisationPersons)
    {
        if ($organisationPersons instanceof ArrayCollection) {
            $this->organisationPersons = new ArrayCollection(
                array_merge(
                    $this->organisationPersons->toArray(),
                    $organisationPersons->toArray()
                )
            );
        } elseif (!$this->organisationPersons->contains($organisationPersons)) {
            $this->organisationPersons->add($organisationPersons);
        }

        return $this;
    }

    /**
     * Remove a organisation persons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationPersons
     * @return Organisation
     */
    public function removeOrganisationPersons($organisationPersons)
    {
        if ($this->organisationPersons->contains($organisationPersons)) {
            $this->organisationPersons->removeElement($organisationPersons);
        }

        return $this;
    }

    /**
     * Set the trading name
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tradingNames
     * @return Organisation
     */
    public function setTradingNames($tradingNames)
    {
        $this->tradingNames = $tradingNames;

        return $this;
    }

    /**
     * Get the trading names
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTradingNames()
    {
        return $this->tradingNames;
    }

    /**
     * Add a trading names
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tradingNames
     * @return Organisation
     */
    public function addTradingNames($tradingNames)
    {
        if ($tradingNames instanceof ArrayCollection) {
            $this->tradingNames = new ArrayCollection(
                array_merge(
                    $this->tradingNames->toArray(),
                    $tradingNames->toArray()
                )
            );
        } elseif (!$this->tradingNames->contains($tradingNames)) {
            $this->tradingNames->add($tradingNames);
        }

        return $this;
    }

    /**
     * Remove a trading names
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tradingNames
     * @return Organisation
     */
    public function removeTradingNames($tradingNames)
    {
        if ($this->tradingNames->contains($tradingNames)) {
            $this->tradingNames->removeElement($tradingNames);
        }

        return $this;
    }
}
