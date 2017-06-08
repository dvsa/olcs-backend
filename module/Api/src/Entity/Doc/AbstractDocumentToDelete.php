<?php

namespace Dvsa\Olcs\Api\Entity\Doc;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentToDelete Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="document_to_delete")
 */
abstract class AbstractDocumentToDelete implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=false)
     */
    protected $createdOn;

    /**
     * Document id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="document_id", nullable=false)
     */
    protected $documentId;

    /**
     * Document store id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_store_id", length=255, nullable=false)
     */
    protected $documentStoreId;

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
     * Set the created on
     *
     * @param \DateTime $createdOn new value being set
     *
     * @return DocumentToDelete
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the document id
     *
     * @param int $documentId new value being set
     *
     * @return DocumentToDelete
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * Get the document id
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set the document store id
     *
     * @param string $documentStoreId new value being set
     *
     * @return DocumentToDelete
     */
    public function setDocumentStoreId($documentStoreId)
    {
        $this->documentStoreId = $documentStoreId;

        return $this;
    }

    /**
     * Get the document store id
     *
     * @return string
     */
    public function getDocumentStoreId()
    {
        return $this->documentStoreId;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return DocumentToDelete
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }


    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
