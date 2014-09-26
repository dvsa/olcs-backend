<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocParagraph Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_paragraph",
 *    indexes={
 *        @ORM\Index(name="IDX_810BA403DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_810BA40365CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class DocParagraph implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Para title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="para_title", length=255, nullable=false)
     */
    protected $paraTitle;

    /**
     * Para text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="para_text", length=1000, nullable=true)
     */
    protected $paraText;

    /**
     * Set the para title
     *
     * @param string $paraTitle
     * @return DocParagraph
     */
    public function setParaTitle($paraTitle)
    {
        $this->paraTitle = $paraTitle;

        return $this;
    }

    /**
     * Get the para title
     *
     * @return string
     */
    public function getParaTitle()
    {
        return $this->paraTitle;
    }

    /**
     * Set the para text
     *
     * @param string $paraText
     * @return DocParagraph
     */
    public function setParaText($paraText)
    {
        $this->paraText = $paraText;

        return $this;
    }

    /**
     * Get the para text
     *
     * @return string
     */
    public function getParaText()
    {
        return $this->paraText;
    }
}
