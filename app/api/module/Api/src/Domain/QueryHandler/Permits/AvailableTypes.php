<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType as IrhpPermitTypeRepo;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableTypes as AvailableTypesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Permits\Availability\WindowAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Available types
 */
class AvailableTypes extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitType';

    /** @var WindowAvailabilityChecker */
    private $windowAvailabilityChecker;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->windowAvailabilityChecker = $mainServiceLocator->get('PermitsAvailabilityWindowAvailabilityChecker');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableTypesQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $now = new DateTime();

        /** @var IrhpPermitTypeRepo $repo */
        $repo = $this->getRepo('IrhpPermitType');
        $availableTypes = $repo->fetchAvailableTypes($now);

        $filteredAvailableTypes = [];
        foreach ($availableTypes as $type) {
            $includeType = true;
            if ($type->isEcmtShortTerm()) {
                $includeType = $this->windowAvailabilityChecker->hasAvailability($now);
            }

            if ($includeType) {
                $filteredAvailableTypes[] = $type;
            }
        }

        return [
            'types' => $filteredAvailableTypes,
            'hasTypes' => !empty($filteredAvailableTypes),
        ];
    }
}
