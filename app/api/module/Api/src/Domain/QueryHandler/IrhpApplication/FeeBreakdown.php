<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\FeeBreakdown as FeeBreakdownQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Permits\FeeBreakdown\FeeBreakdownGeneratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Fee breakdown
 */
class FeeBreakdown extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var array */
    private $feeBreakdownGenerators;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, FeeBreakdown::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|FeeBreakdownQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplication = $this->getRepo()->fetchUsingId($query);

        $irhpPermitTypeId = $irhpApplication->getIrhpPermitType()->getId();
        if (!isset($this->feeBreakdownGenerators[$irhpPermitTypeId])) {
            // return an empty table so that the frontend knows not to display it
            return [];
        }

        $feeBreakdownGenerator = $this->feeBreakdownGenerators[$irhpPermitTypeId];
        return $feeBreakdownGenerator->generate($irhpApplication);
    }

    /**
     * Register a service to generate the fee breakdown for a specific permit type
     *
     * @param int $irhpPermitTypeId
     * @param FeeBreakdownGeneratorInterface $feeBreakdownGenerator
     */
    private function registerGenerator($irhpPermitTypeId, FeeBreakdownGeneratorInterface $feeBreakdownGenerator)
    {
        $this->feeBreakdownGenerators[$irhpPermitTypeId] = $feeBreakdownGenerator;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FeeBreakdown
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->registerGenerator(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $container->get('PermitsBilateralFeeBreakdownGenerator')
        );
        $this->registerGenerator(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            $container->get('PermitsMultilateralFeeBreakdownGenerator')
        );
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
