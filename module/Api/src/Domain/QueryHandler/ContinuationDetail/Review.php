<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Generator;

/**
 * Continuation detail review
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Review extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';

    /**
     * @var Generator
     */
    protected $continuationReviewService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->continuationReviewService = $container->get('ContinuationReview');
        return parent::__invoke($container, $requestedName, $options);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        return [
            'markup' => $this->continuationReviewService
                ->generate(
                    $this->getRepo()->fetchUsingId($query)
                )
        ];
    }
}
