<?php

namespace Dvsa\Olcs\Api\Entity\Elastic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * ElasticUpdate Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="elastic_update")
 */
abstract class AbstractElasticUpdate implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * Index name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="index_name", length=32, nullable=false)
     */
    protected $indexName;

    /**
     * Previous runtime
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="previous_runtime", nullable=true)
     */
    protected $previousRuntime;

    /**
     * Runtime
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="runtime", nullable=true)
     */
    protected $runtime;

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return ElasticUpdate
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
     * Set the index name
     *
     * @param string $indexName new value being set
     *
     * @return ElasticUpdate
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * Get the index name
     *
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * Set the previous runtime
     *
     * @param int $previousRuntime new value being set
     *
     * @return ElasticUpdate
     */
    public function setPreviousRuntime($previousRuntime)
    {
        $this->previousRuntime = $previousRuntime;

        return $this;
    }

    /**
     * Get the previous runtime
     *
     * @return int
     */
    public function getPreviousRuntime()
    {
        return $this->previousRuntime;
    }

    /**
     * Set the runtime
     *
     * @param int $runtime new value being set
     *
     * @return ElasticUpdate
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * Get the runtime
     *
     * @return int
     */
    public function getRuntime()
    {
        return $this->runtime;
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
