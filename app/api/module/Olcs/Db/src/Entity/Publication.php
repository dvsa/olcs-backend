<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="publication",
 *    indexes={
 *        @ORM\Index(name="fk_publication_traffic_area1_idx", 
 *            columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_publication_ref_data1_idx", 
 *            columns={"pub_status"}),
 *        @ORM\Index(name="fk_publication_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_publication_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class Publication implements Interfaces\EntityInterface
{

    /**
     * Pub status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="pub_status", referencedColumnName="id", nullable=false)
     */
    protected $pubStatus;

    /**
     * Pub date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="pub_date", nullable=true)
     */
    protected $pubDate;

    /**
     * Doc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="doc_name", length=255, nullable=true)
     */
    protected $docName;

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
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

    /**
     * Publication no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="publication_no", nullable=false)
     */
    protected $publicationNo;

    /**
     * Pub type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pub_type", length=3, nullable=false)
     */
    protected $pubType;

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
     * Set the pub status
     *
     * @param \Olcs\Db\Entity\RefData $pubStatus
     * @return Publication
     */
    public function setPubStatus($pubStatus)
    {
        $this->pubStatus = $pubStatus;

        return $this;
    }

    /**
     * Get the pub status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * Set the pub date
     *
     * @param \DateTime $pubDate
     * @return Publication
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    /**
     * Get the pub date
     *
     * @return \DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * Set the doc name
     *
     * @param string $docName
     * @return Publication
     */
    public function setDocName($docName)
    {
        $this->docName = $docName;

        return $this;
    }

    /**
     * Get the doc name
     *
     * @return string
     */
    public function getDocName()
    {
        return $this->docName;
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
     * Set the traffic area
     *
     * @param \Olcs\Db\Entity\TrafficArea $trafficArea
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the publication no
     *
     * @param int $publicationNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;

        return $this;
    }

    /**
     * Get the publication no
     *
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * Set the pub type
     *
     * @param string $pubType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPubType($pubType)
    {
        $this->pubType = $pubType;

        return $this;
    }

    /**
     * Get the pub type
     *
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
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
