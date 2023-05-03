<?php

/**
 * Declaration Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationUndertakingsReviewService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Declaration Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeclarationUndertakings extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var ApplicationUndertakingsReviewService
     */
    protected $reviewService;

    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, DeclarationUndertakings::class);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [
                'licence' => [
                    'organisation' => [
                        'type'
                    ]
                ]
            ],
            [
                'undertakings' => $this->getUndertakings($application),
            ]
        );
    }

    protected function getUndertakings(ApplicationEntity $application)
    {
        $data = $application->serialize();
        $data['isGoods'] = $application->isGoods();
        $data['isInternal'] = false;

        return $this->reviewService->getMarkup($data);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DeclarationUndertakings
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->reviewService = $container->get('Review\ApplicationUndertakings');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
