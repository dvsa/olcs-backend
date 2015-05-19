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
 *        @ORM\Index(name="ix_organisation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_organisation_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_organisation_type", columns={"type"}),
 *        @ORM\Index(name="ix_organisation_lead_tc_area_id", columns={"lead_tc_area_id"}),
 *        @ORM\Index(name="ix_organisation_name", columns={"name"}),
 *        @ORM\Index(name="ix_organisation_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_organisation_irfo_contact_details_id", columns={"irfo_contact_details_id"})
 *    }
 * )
 */
class Organisation implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CompanyOrLlpNo20Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TypeManyToOneAlt1,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Allow email
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="allow_email", nullable=false, options={"default": 0})
     */
    protected $allowEmail = 0;

    /**
     * Company cert seen
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="company_cert_seen", nullable=false, options={"default": 0})
     */
    protected $companyCertSeen = 0;

    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $contactDetails;

    /**
     * Irfo contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="irfo_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoContactDetails;

    /**
     * Irfo name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_name", length=160, nullable=true)
     */
    protected $irfoName;

    /**
     * Irfo nationality
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_nationality", length=45, nullable=true)
     */
    protected $irfoNationality;

    /**
     * Is irfo
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_irfo", nullable=false, options={"default": 0})
     */
    protected $isIrfo = 0;

    /**
     * Is mlh
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_mlh", nullable=false, options={"default": 0})
     */
    protected $isMlh = 0;

    /**
     * Lead tc area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea")
     * @ORM\JoinColumn(name="lead_tc_area_id", referencedColumnName="id", nullable=true)
     */
    protected $leadTcArea;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=160, nullable=true)
     */
    protected $name;

    /**
     * Nature of businesse
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="organisations")
     * @ORM\JoinTable(name="organisation_nature_of_business",
     *     joinColumns={
     *         @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="ref_data_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $natureOfBusinesses;

    /**
     * Irfo partner
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\IrfoPartner", mappedBy="organisation", cascade={"persist"})
     */
    protected $irfoPartners;

    /**
     * Licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Licence", mappedBy="organisation")
     */
    protected $licences;

    /**
     * Organisation person
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationPerson", mappedBy="organisation")
     */
    protected $organisationPersons;

    /**
     * Organisation user
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationUser", mappedBy="organisation")
     */
    protected $organisationUsers;

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
        $this->natureOfBusinesses = new ArrayCollection();
        $this->irfoPartners = new ArrayCollection();
        $this->licences = new ArrayCollection();
        $this->organisationPersons = new ArrayCollection();
        $this->organisationUsers = new ArrayCollection();
        $this->tradingNames = new ArrayCollection();
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
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
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
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Set the irfo contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $irfoContactDetails
     * @return Organisation
     */
    public function setIrfoContactDetails($irfoContactDetails)
    {
        $this->irfoContactDetails = $irfoContactDetails;

        return $this;
    }

    /**
     * Get the irfo contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getIrfoContactDetails()
    {
        return $this->irfoContactDetails;
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
     * Set the is irfo
     *
     * @param string $isIrfo
     * @return Organisation
     */
    public function setIsIrfo($isIrfo)
    {
        $this->isIrfo = $isIrfo;

        return $this;
    }

    /**
     * Get the is irfo
     *
     * @return string
     */
    public function getIsIrfo()
    {
        return $this->isIrfo;
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
     * Set the nature of businesse
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesses
     * @return Organisation
     */
    public function setNatureOfBusinesses($natureOfBusinesses)
    {
        $this->natureOfBusinesses = $natureOfBusinesses;

        return $this;
    }

    /**
     * Get the nature of businesses
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getNatureOfBusinesses()
    {
        return $this->natureOfBusinesses;
    }

    /**
     * Add a nature of businesses
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesses
     * @return Organisation
     */
    public function addNatureOfBusinesses($natureOfBusinesses)
    {
        if ($natureOfBusinesses instanceof ArrayCollection) {
            $this->natureOfBusinesses = new ArrayCollection(
                array_merge(
                    $this->natureOfBusinesses->toArray(),
                    $natureOfBusinesses->toArray()
                )
            );
        } elseif (!$this->natureOfBusinesses->contains($natureOfBusinesses)) {
            $this->natureOfBusinesses->add($natureOfBusinesses);
        }

        return $this;
    }

    /**
     * Remove a nature of businesses
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $natureOfBusinesses
     * @return Organisation
     */
    public function removeNatureOfBusinesses($natureOfBusinesses)
    {
        if ($this->natureOfBusinesses->contains($natureOfBusinesses)) {
            $this->natureOfBusinesses->removeElement($natureOfBusinesses);
        }

        return $this;
    }

    /**
     * Set the irfo partner
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPartners
     * @return Organisation
     */
    public function setIrfoPartners($irfoPartners)
    {
        $this->irfoPartners = $irfoPartners;

        return $this;
    }

    /**
     * Get the irfo partners
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrfoPartners()
    {
        return $this->irfoPartners;
    }

    /**
     * Add a irfo partners
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPartners
     * @return Organisation
     */
    public function addIrfoPartners($irfoPartners)
    {
        if ($irfoPartners instanceof ArrayCollection) {
            $this->irfoPartners = new ArrayCollection(
                array_merge(
                    $this->irfoPartners->toArray(),
                    $irfoPartners->toArray()
                )
            );
        } elseif (!$this->irfoPartners->contains($irfoPartners)) {
            $this->irfoPartners->add($irfoPartners);
        }

        return $this;
    }

    /**
     * Remove a irfo partners
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPartners
     * @return Organisation
     */
    public function removeIrfoPartners($irfoPartners)
    {
        if ($this->irfoPartners->contains($irfoPartners)) {
            $this->irfoPartners->removeElement($irfoPartners);
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
     * Set the organisation user
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return Organisation
     */
    public function setOrganisationUsers($organisationUsers)
    {
        $this->organisationUsers = $organisationUsers;

        return $this;
    }

    /**
     * Get the organisation users
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrganisationUsers()
    {
        return $this->organisationUsers;
    }

    /**
     * Add a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return Organisation
     */
    public function addOrganisationUsers($organisationUsers)
    {
        if ($organisationUsers instanceof ArrayCollection) {
            $this->organisationUsers = new ArrayCollection(
                array_merge(
                    $this->organisationUsers->toArray(),
                    $organisationUsers->toArray()
                )
            );
        } elseif (!$this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->add($organisationUsers);
        }

        return $this;
    }

    /**
     * Remove a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return Organisation
     */
    public function removeOrganisationUsers($organisationUsers)
    {
        if ($this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->removeElement($organisationUsers);
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
