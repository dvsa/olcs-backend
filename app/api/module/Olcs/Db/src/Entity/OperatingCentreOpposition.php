<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperatingCentreOpposition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="operating_centre_opposition",
 *    indexes={
 *        @ORM\Index(name="fk_opposition_operating_centre_opposition1_idx", 
 *            columns={"opposition_id"}),
 *        @ORM\Index(name="fk_opposition_operating_centre_operating_centre1_idx", 
 *            columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_operating_centre_opposition_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_operating_centre_opposition_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class OperatingCentreOpposition implements Interfaces\EntityInterface
{

    /**
     * Opposition
     *
     * @var \Olcs\Db\Entity\Opposition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposition", fetch="LAZY")
     * @ORM\JoinColumn(name="opposition_id", referencedColumnName="id", nullable=false)
     */
    protected $opposition;

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
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre", fetch="LAZY")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=true)
     */
    protected $operatingCentre;

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
     * Set the opposition
     *
     * @param \Olcs\Db\Entity\Opposition $opposition
     * @return OperatingCentreOpposition
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
     * Set the operating centre
     *
     * @param \Olcs\Db\Entity\OperatingCentre $operatingCentre
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
