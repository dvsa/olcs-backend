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

        $feeTypes =$this->filterDuplicates($feeTypes);

        return [
            'result' => $this->resultList($feeTypes),
            'count' => count($feeTypes),
        ];
    }

    /**
     * This is in lieu of being able to do proper groupwise max in the
     * repository method using Doctrine
     *
     * @param array
     * @return array
     */
    public function filterDuplicates($feeTypes)
    {
        $filtered = [];
        foreach ($feeTypes as $ft)
        {
            // if IRFO, we group by irfoFeeType id rather than feeType id
            $feeTypeId = $ft->getIrfoFeeType() ? $ft->getIrfoFeeType()->getId() : $ft->getFeeType()->getId();
            if (!isset($filtered[$feeTypeId]) || $ft->getEffectiveFrom() > $filtered[$feeTypeId]->getEffectiveFrom()) {
                $filtered[$feeTypeId] = $ft;
            }
        }

        return $filtered;
    }
}
