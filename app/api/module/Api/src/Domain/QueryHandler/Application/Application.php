<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [
                'licence' => [
                    'organisation' => ['disqualifications'],
                ],
                'applicationCompletion',
                's4s' => [
                    'outcome'
                ]
            ],
            [
                'sections' => $this->sectionAccessService->getAccessibleSections($application),
                'outstandingFeeTotal' => $this->getOutstandingFeeTotal($application),
                'variationCompletion' => $application->getVariationCompletion(),
                'canCreateCase' => $application->canCreateCase(),
            ]
        );
    }

    protected function getOutstandingFeeTotal($application)
    {
        $outstandingFees = $this->feesHelper->getOutstandingFeesForApplication($application->getId());

        $total = 0;

        if (is_array($outstandingFees)) {
            foreach ($outstandingFees as $fee) {
                $total += $fee->getAmount();
            }
        }

        return number_format($total, 2, null, null);
    }
}
