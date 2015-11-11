<?php

/**
 * Companies house / GetList
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Companies house / GetList
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    /**
     * @var \Dvsa\Olcs\Api\Service\CompaniesHouseService
     */
    private $companiesHouseService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->companiesHouseService = $mainServiceLocator->get('CompaniesHouseService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        $result = $this->companiesHouseService->getList($query->getType(), $query->getValue());
        $finalResult = $this->resultList(
            $result['Result']
        );
        return [
            'result' => $finalResult,
            'count' => $result['Count']
        ];
    }
}
