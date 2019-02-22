<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;


use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class OpenTasks extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];
    protected $repoServiceName = 'Tasks';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchOpenTasksForSurrender($query->getId());
    }
}
