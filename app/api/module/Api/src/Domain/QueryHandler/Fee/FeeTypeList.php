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
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;

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

        // get fee types where traffic area *is not* specified
        $genericFeetypes = $this->filterDuplicates($feeTypes, null);
        // get fee types where traffic area *is* specified
        $specificFeeTypes = $this->filterDuplicates($feeTypes, $this->getTrafficArea($query));
        // merge fee types together so that fee types with specified traffic area will overwrite non specified
        $filteredFeeTypes = $specificFeeTypes + $genericFeetypes;

        $valueOptions = [];
        foreach ($filteredFeeTypes as $ft) {
            $valueOptions['feeType'][$ft->getId()] = $ft->getDescription();
        }

        $valueOptions['irfoGvPermit'] = $this->getIrfoGvPermitValueOptions($query);
        $valueOptions['irfoPsvAuth'] = $this->getIrfoPsvAuthValueOptions($query);

        return [
            'result' => $this->resultList($filteredFeeTypes),
            'count' => count($filteredFeeTypes),
            'valueOptions' => $valueOptions,
        ];
    }

    /**
     * Get a Traffic area from the query params
     *
     * @param QueryInterface $query
     *
     * @return TrafficArea|null
     */
    private function getTrafficArea(QueryInterface $query)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->getReference(Licence::class, $query->getLicence());
        if ($licence) {
            return $licence->getTrafficArea();
        }

        /* @var $application Application */
        $application = $this->getRepo()->getReference(Application::class, $query->getApplication());
        if ($application) {
            return $application->getLicence()->getTrafficArea();
        }

        return null;
    }

    /**
     * This is in lieu of being able to do proper groupwise max in the
     * repository method using Doctrine
     *
     * @param array $feeTypes Array of fee types
     * @param TrafficArea $trafficArea TrafficArea to get fee types for, default to null
     *
     * @return array
     */
    private function filterDuplicates($feeTypes, \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $trafficArea = null)
    {
        $filtered = [];
        foreach ($feeTypes as $ft) {
            /* @var $ft \Dvsa\Olcs\Api\Entity\Fee\FeeType */

            if ($ft->getTrafficArea() !== $trafficArea) {
                continue;
            }

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

            foreach ($irfoGvPermits as $irfoGvPermit) {
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

            foreach ($irfoPsvAuths as $irfoPsvAuth) {
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
