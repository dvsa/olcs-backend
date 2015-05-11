<?php

/**
 * Abstract Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;

/**
 * Abstract Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractService implements ServiceInterface
{
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
     * Get the injected repository
     *
     * @return RepositoryInterface
     */
    protected function getRepo()
    {
        return $this->repo;
    }
}
