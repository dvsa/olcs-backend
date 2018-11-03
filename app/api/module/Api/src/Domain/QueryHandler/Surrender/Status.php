<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;


use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

final class Status extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];
    protected $repoServiceName = 'Surrender';


    /**
     * handleQuery
     *
     * @param QueryInterface $query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {

        $status = $this->getRepo()->fetchOneById($query, Query::HYDRATE_OBJECT);
        return [
            'result' => new Result(
                $status,
                ['status']
            ),
        ];
    }
}
