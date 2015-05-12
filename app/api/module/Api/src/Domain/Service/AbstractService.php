<?php

/**
 * Abstract Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Abstract Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractService implements ServiceInterface
{
    protected $queryMap = [];

    protected $commandMap = [];

    /**
     *
     * @var RepositoryInterface
     */
    private $repo;

    /**
     * Inject the Repository
     * @param RepositoryInterface $repo
     */
    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Handles all query objects passed to the service
     *
     * @param ArraySerializableInterface $query
     */
    public function handleQuery(ArraySerializableInterface $query)
    {
        if (!isset($this->queryMap[get_class($query)])) {
            throw new \RuntimeException(get_class($query) . ' DTO not found in the query map');
        }

        $method = $this->queryMap[get_class($query)];

        return $this->$method($query);
    }

    /**
     * Handles all commands objects passed to the service
     *
     * @param ArraySerializableInterface $command
     */
    public function handleCommand(ArraySerializableInterface $command)
    {
        if (!isset($this->commandMap[get_class($command)])) {
            throw new \RuntimeException(get_class($command) . ' DTO not found in the command map');
        }

        $method = $this->commandMap[get_class($command)];

        return $this->$method($command);
    }

    /**
     * Get the injected repository
     *
     * @return RepositoryInterface
     */
    protected function getRepo()
    {
        return $this->repo;
    }
}
