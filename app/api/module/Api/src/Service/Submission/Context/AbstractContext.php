<?php

namespace Dvsa\Olcs\Api\Service\Submission\Context;

use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class AbstractContext
 * @package Dvsa\Olcs\Api\Service\Submission\Context
 */
abstract class AbstractContext implements ContextInterface
{
    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    public function __construct(QueryHandlerInterface $queryHandler)
    {
        $this->queryHandler = $queryHandler;
    }

    protected function handleQuery($query)
    {
        return $this->queryHandler->handleQuery($query);
    }
}
