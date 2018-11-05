<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;


use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

final class Status extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];
    protected $repoServiceName = 'Surrender';
    protected $extraRepos = ['Licence'];

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
        $licence = $this->getRepo('Licence')->fetchById($query->getId(), Query::HYDRATE_OBJECT);
        $surrender = $this->getRepo()->fetchOneByLicence($licence->getId(), Query::HYDRATE_OBJECT);
        $status = $surrender->getStatus();
        return $this->result(
            $status
        );
    }
}
