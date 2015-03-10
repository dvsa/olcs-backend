<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * CompanySubsidiary Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="company_subsidiary",
 *    indexes={
 *        @ORM\Index(name="fk_company_subsidiary_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_company_subsidiary_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CompanySubsidiary implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Company no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_no", length=12, nullable=true)
     */
    protected $companyNo;

    /**
     * Licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Licence", inversedBy="companySubsidiarys")
     * @ORM\JoinTable(name="company_subsidiary_licence",
     *     joinColumns={
     *         @ORM\JoinColumn(name="company_subsidiary_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="licence_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $licences;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=true)
     */
    protected $name;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->licences = new ArrayCollection();
    }

    /**
     * Set the company no
     *
     * @param string $companyNo
     * @return CompanySubsidiary
     */
    public function setCompanyNo($companyNo)
    {
        $this->companyNo = $companyNo;

        return $this;
    }

    /**
     * Get the company no
     *
     * @return string
     */
    public function getCompanyNo()
    {
        return $this->companyNo;
    }

    /**
     * Set the licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licences
     * @return CompanySubsidiary
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
     * @return CompanySubsidiary
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
     * @return CompanySubsidiary
     */
    public function removeLicences($licences)
    {
        if ($this->licences->contains($licences)) {
            $this->licences->removeElement($licences);
        }

        return $this;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return CompanySubsidiary
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
}
