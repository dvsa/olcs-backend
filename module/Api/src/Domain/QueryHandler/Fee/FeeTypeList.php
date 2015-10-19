<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Doctrine\ORM\Query as DoctrineQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fee Type List
 */
class FeeTypeList extends AbstractQueryHandler
{
    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'FeeType';

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeTypeRepo $repo */
        $repo = $this->getRepo();

        $feeTypes = $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList($feeTypes),
            'count' => $repo->fetchCount($query),
        ];
    }
}
