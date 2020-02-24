<?php

namespace Dvsa\Olcs\Api\Entity\Doc;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DocParagraphBookmark Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_paragraph_bookmark",
 *    indexes={
 *        @ORM\Index(name="ix_doc_paragraph_bookmark_doc_paragraph_id", columns={"doc_paragraph_id"}),
 *        @ORM\Index(name="ix_doc_paragraph_bookmark_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_doc_paragraph_bookmark_created_by", columns={"created_by"}),
 *        @ORM\Index(name="IDX_34C39149C1FDC79C", columns={"doc_bookmark_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_doc_paragraph_bookmark_doc_bookmark_id_doc_paragraph_id",
     *     columns={"doc_bookmark_id","doc_paragraph_id"})
 *    }
 * )
 */
abstract class AbstractDocParagraphBookmark implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
     */
    protected $createdBy;

    /**
     * Doc bookmark
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\DocBookmark
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Doc\DocBookmark",
     *     fetch="LAZY",
     *     inversedBy="docParagraphBookmarks"
     * )
     * @ORM\JoinColumn(name="doc_bookmark_id", referencedColumnName="id", nullable=false)
     */
    protected $docBookmark;

    /**
     * Doc paragraph
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\DocParagraph
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\DocParagraph", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_paragraph_id", referencedColumnName="id", nullable=false)
     */
    protected $docParagraph;

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
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return DocParagraphBookmark
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the doc bookmark
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\DocBookmark $docBookmark entity being set as the value
     *
     * @return DocParagraphBookmark
     */
    public function setDocBookmark($docBookmark)
    {
        $this->docBookmark = $docBookmark;

        return $this;
    }

    /**
     * Get the doc bookmark
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\DocBookmark
     */
    public function getDocBookmark()
    {
        return $this->docBookmark;
    }

    /**
     * Set the doc paragraph
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\DocParagraph $docParagraph entity being set as the value
     *
     * @return DocParagraphBookmark
     */
    public function setDocParagraph($docParagraph)
    {
        $this->docParagraph = $docParagraph;

        return $this;
    }

    /**
     * Get the doc paragraph
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\DocParagraph
     */
    public function getDocParagraph()
    {
        return $this->docParagraph;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return DocParagraphBookmark
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return DocParagraphBookmark
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return DocParagraphBookmark
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
}
