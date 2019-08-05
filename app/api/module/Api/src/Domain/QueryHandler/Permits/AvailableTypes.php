<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\AvailableTypes as AvailableTypesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\WindowAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Available types
 */
class AvailableTypes extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

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

        $this->windowAvailabilityChecker = $mainServiceLocator->get('PermitsShortTermEcmtWindowAvailabilityChecker');

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
        $availableTypes = $this->getRepo()->fetchAvailableTypes($now);

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

        return ['types' => $filteredAvailableTypes];
    }
}
