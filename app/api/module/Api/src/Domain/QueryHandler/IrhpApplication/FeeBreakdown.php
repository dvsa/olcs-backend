<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\FeeBreakdown as FeeBreakdownQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Permits\FeeBreakdown\FeeBreakdownGeneratorInterface;

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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->registerGenerator(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $mainServiceLocator->get('PermitsBilateralFeeBreakdownGenerator')
        );

        $this->registerGenerator(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            $mainServiceLocator->get('PermitsMultilateralFeeBreakdownGenerator')
        );

        return parent::createService($serviceLocator);
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
}
