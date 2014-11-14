<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prohibition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="prohibition",
 *    indexes={
 *        @ORM\Index(name="fk_prohibition_case1_idx", 
 *            columns={"case_id"}),
 *        @ORM\Index(name="fk_prohibition_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_prohibition_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_prohibition_ref_data1_idx", 
 *            columns={"prohibition_type"})
 *    }
 * )
 */
class Prohibition implements Interfaces\EntityInterface
{

    /**
     * Prohibition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="prohibition_type", referencedColumnName="id", nullable=false)
     */
    protected $prohibitionType;

    /**
     * Prohibition date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="prohibition_date", nullable=false)
     */
    protected $prohibitionDate;

    /**
     * Cleared date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cleared_date", nullable=true)
     */
    protected $clearedDate;

    /**
     * Is trailer
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_trailer", nullable=false)
     */
    protected $isTrailer = 0;

    /**
     * Imposed at
     *
     * @var string
     *
     * @ORM\Column(type="string", name="imposed_at", length=255, nullable=true)
     */
    protected $imposedAt;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the prohibition type
     *
     * @param \Olcs\Db\Entity\RefData $prohibitionType
     * @return Prohibition
     */
    public function setProhibitionType($prohibitionType)
    {
        $this->prohibitionType = $prohibitionType;

        return $this;
    }

    /**
     * Get the prohibition type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getProhibitionType()
    {
        return $this->prohibitionType;
    }

    /**
     * Set the prohibition date
     *
     * @param \DateTime $prohibitionDate
     * @return Prohibition
     */
    public function setProhibitionDate($prohibitionDate)
    {
        $this->prohibitionDate = $prohibitionDate;

        return $this;
    }

    /**
     * Get the prohibition date
     *
     * @return \DateTime
     */
    public function getProhibitionDate()
    {
        return $this->prohibitionDate;
    }

    /**
     * Set the cleared date
     *
     * @param \DateTime $clearedDate
     * @return Prohibition
     */
    public function setClearedDate($clearedDate)
    {
        $this->clearedDate = $clearedDate;

        return $this;
    }

    /**
     * Get the cleared date
     *
     * @return \DateTime
     */
    public function getClearedDate()
    {
        return $this->clearedDate;
    }

    /**
     * Set the is trailer
     *
     * @param string $isTrailer
     * @return Prohibition
     */
    public function setIsTrailer($isTrailer)
    {
        $this->isTrailer = $isTrailer;

        return $this;
    }

    /**
     * Get the is trailer
     *
     * @return string
     */
    public function getIsTrailer()
    {
        return $this->isTrailer;
    }

    /**
     * Set the imposed at
     *
     * @param string $imposedAt
     * @return Prohibition
     */
    public function setImposedAt($imposedAt)
    {
        $this->imposedAt = $imposedAt;

        return $this;
    }

    /**
     * Get the imposed at
     *
     * @return string
     */
    public function getImposedAt()
    {
        return $this->imposedAt;
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the vrm
     *
     * @param string $vrm
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
