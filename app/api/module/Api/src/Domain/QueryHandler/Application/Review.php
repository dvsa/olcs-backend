<?php

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Review extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->reviewSnapshotService = $serviceLocator->getServiceLocator()->get('ReviewSnapshot');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $markup = $this->reviewSnapshotService->generate($application, $this->isGranted(Permission::INTERNAL_USER));

        return ['markup' => $markup];
    }
}
