<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DocBookmark Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_bookmark",
 *    indexes={
 *        @ORM\Index(name="fk_doc_bookmark_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_doc_bookmark_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class DocBookmark implements Interfaces\EntityInterface
{

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=50, nullable=false)
     */
    protected $name;

    /**
     * Doc paragraph bookmark
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\DocParagraphBookmark", mappedBy="docBookmark")
     */
    protected $docParagraphBookmarks;

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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->docParagraphBookmarks = new ArrayCollection();
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return DocBookmark
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
     * Set the doc paragraph bookmark
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docParagraphBookmarks
     * @return DocBookmark
     */
    public function setDocParagraphBookmarks($docParagraphBookmarks)
    {
        $this->docParagraphBookmarks = $docParagraphBookmarks;

        return $this;
    }

    /**
     * Get the doc paragraph bookmarks
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocParagraphBookmarks()
    {
        return $this->docParagraphBookmarks;
    }

    /**
     * Add a doc paragraph bookmarks
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docParagraphBookmarks
     * @return DocBookmark
     */
    public function addDocParagraphBookmarks($docParagraphBookmarks)
    {
        if ($docParagraphBookmarks instanceof ArrayCollection) {
            $this->docParagraphBookmarks = new ArrayCollection(
                array_merge(
                    $this->docParagraphBookmarks->toArray(),
                    $docParagraphBookmarks->toArray()
                )
            );
        } elseif (!$this->docParagraphBookmarks->contains($docParagraphBookmarks)) {
            $this->docParagraphBookmarks->add($docParagraphBookmarks);
        }

        return $this;
    }

    /**
     * Remove a doc paragraph bookmarks
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docParagraphBookmarks
     * @return DocBookmark
     */
    public function removeDocParagraphBookmarks($docParagraphBookmarks)
    {
        if ($this->docParagraphBookmarks->contains($docParagraphBookmarks)) {
            $this->docParagraphBookmarks->removeElement($docParagraphBookmarks);
        }

        return $this;
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
     * Set the description
     *
     * @param string $description
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
