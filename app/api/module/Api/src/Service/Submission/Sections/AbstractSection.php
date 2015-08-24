<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class AbstractSection
 * @package Dvsa\Olcs\Api\Service\Submission\Section
 */
abstract class AbstractSection implements SectionGeneratorInterface
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
