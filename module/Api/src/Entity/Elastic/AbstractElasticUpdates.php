<?php

namespace Dvsa\Olcs\Api\Entity\Elastic;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElasticUpdates Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\Table(name="elastic_updates")
 */
abstract class AbstractElasticUpdates
{

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
     * @param int $id
     * @return ElasticUpdates
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
     * @param string $indexName
     * @return ElasticUpdates
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
     * @param int $previousRuntime
     * @return ElasticUpdates
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
     * @param int $runtime
     * @return ElasticUpdates
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


}
