<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\CountryGenerator;
use Dvsa\Olcs\Api\Domain\Query\IrhpApplication\BilateralMetadata as BilateralMetadataQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Bilateral metadata
 */
class BilateralMetadata extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['Country'];

    /** @var CountryGenerator */
    private $countryGenerator;

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

        $this->countryGenerator = $mainServiceLocator->get('PermitsBilateralMetadataCountryGenerator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|BilateralMetadataQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplicationId = $query->getIrhpApplication();

        $irhpApplication = new IrhpApplication();
        if (!is_null($irhpApplicationId)) {
            $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);
        }

        $countries = $this->getRepo('Country')->fetchAvailableCountriesForIrhpApplication(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            new DateTime()
        );

        $countryResponses = [];

        foreach ($countries as $country) {
            $countryResponses[] = $this->countryGenerator->generate($country, $irhpApplication);
        }

        return ['countries' => $countryResponses];
    }
}
