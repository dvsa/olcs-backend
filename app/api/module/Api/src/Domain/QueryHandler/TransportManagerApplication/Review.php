<?php

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Generator;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Review extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->reviewSnapshotService = $serviceLocator->getServiceLocator()->get('TmReviewSnapshot');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var TransportManagerApplication $tma */
        $tma = $this->getRepo()->fetchUsingId($query);

        $isInternalUser = $this->isGranted(Permission::INTERNAL_USER);

        $markup = $this->reviewSnapshotService->generate($tma, $isInternalUser);

        return ['markup' => $markup];
    }
}
