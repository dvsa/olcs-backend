<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * DocBookmark Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_bookmark",
 *    indexes={
 *        @ORM\Index(name="ix_doc_bookmark_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_doc_bookmark_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class DocBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
}
