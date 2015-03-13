<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ElasticUpdates Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="elastic_updates")
 */
class ElasticUpdates implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

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
