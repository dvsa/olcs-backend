<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
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

    protected $extraRepos = ['IrfoGvPermit', 'IrfoPsvAuth'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeTypeRepo $repo */
        $repo = $this->getRepo();

        $feeTypes = $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT);

        $feeTypes = $this->filterDuplicates($feeTypes);

        $valueOptions = [];
        foreach ($feeTypes as $ft) {
            $valueOptions['feeType'][$ft->getId()] = $ft->getDescription();
        }

        $valueOptions['irfoGvPermit'] = $this->getIrfoGvPermitValueOptions($query);
        $valueOptions['irfoPsvAuth'] = $this->getIrfoPsvAuthValueOptions($query);

        return [
            'result' => $this->resultList($feeTypes),
            'count' => count($feeTypes),
            'valueOptions' => $valueOptions,
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

    private function getIrfoGvPermitValueOptions($query)
    {
        $valueOptions = [];
        if ($query->getOrganisation() !== null) {
            $organisation = $this->getRepo()->getReference(OrganisationEntity::class, $query->getOrganisation());

            $irfoGvPermits = $this->getRepo('IrfoGvPermit')->fetchByOrganisation($organisation);

            foreach ($irfoGvPermits  as $irfoGvPermit) {
                $valueOptions[$irfoGvPermit->getId()] = sprintf(
                    '%d (%s)',
                    $irfoGvPermit->getId(),
                    $irfoGvPermit->getIrfoGvPermitType()->getDescription()
                );
            }
        }
        return $valueOptions;
    }

    private function getIrfoPsvAuthValueOptions($query)
    {
        $valueOptions = [];
        if ($query->getOrganisation() !== null) {
            $organisation = $this->getRepo()->getReference(OrganisationEntity::class, $query->getOrganisation());

            $irfoPsvAuths = $this->getRepo('IrfoPsvAuth')->fetchByOrganisation($organisation);

            foreach ($irfoPsvAuths  as $irfoPsvAuth) {
                $valueOptions[$irfoPsvAuth->getId()] = sprintf(
                    '%d (%s)',
                    $irfoPsvAuth->getId(),
                    $irfoPsvAuth->getIrfoPsvAuthType()->getDescription()
                );
            }
        }
        return $valueOptions;
    }
}
