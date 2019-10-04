<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedRestrictedCountryIdsProvider;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;

class RestrictedCountriesGenerator implements ElementGeneratorInterface
{
    /** @var RestrictedCountriesFactory */
    private $restrictedCountriesFactory;

    /** @var RestrictedCountryFactory */
    private $restrictedCountryFactory;

    /** @var CountryRepository */
    private $countryRepo;

    /** @var GenericAnswerProvider */
    private $genericAnswerProvider;

    /** @var StockBasedRestrictedCountryIdsProvider */
    private $stockBasedRestrictedCountryIdsProvider;

    /**
     * Create service instance
     *
     * @param RestrictedCountriesFactory $restrictedCountriesFactory
     * @param RestrictedCountryFactory $restrictedCountryFactory
     * @param CountryRepository $countryRepo
     * @param GenericAnswerProvider $genericAnswerProvider
     * @param StockBasedRestrictedCountryIdsProvider
     *
     * @return RestrictedCountriesGenerator
     */
    public function __construct(
        RestrictedCountriesFactory $restrictedCountriesFactory,
        RestrictedCountryFactory $restrictedCountryFactory,
        CountryRepository $countryRepo,
        GenericAnswerProvider $genericAnswerProvider,
        StockBasedRestrictedCountryIdsProvider $stockBasedRestrictedCountryIdsProvider
    ) {
        $this->restrictedCountriesFactory = $restrictedCountriesFactory;
        $this->restrictedCountryFactory = $restrictedCountryFactory;
        $this->countryRepo = $countryRepo;
        $this->genericAnswerProvider = $genericAnswerProvider;
        $this->stockBasedRestrictedCountryIdsProvider = $stockBasedRestrictedCountryIdsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getIrhpApplicationEntity();
        $applicationStep = $context->getApplicationStepEntity();

        $yesNo = null;
        try {
            $yesNo = $this->genericAnswerProvider->get($applicationStep, $irhpApplication)->getValue();
        } catch (NotFoundException $e) {
        }

        $restrictedCountries = $this->restrictedCountriesFactory->create($yesNo);

        $irhpPermitStockId = $irhpApplication->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getId();

        $restrictedCountryIds = $this->stockBasedRestrictedCountryIdsProvider->getIds($irhpPermitStockId);

        foreach ($restrictedCountryIds as $countryId) {
            $country = $this->countryRepo->fetchById($countryId);

            $restrictedCountry = $this->restrictedCountryFactory->create(
                $countryId,
                $country->getCountryDesc(),
                $irhpApplication->hasCountryId($countryId)
            );

            $restrictedCountries->addRestrictedCountry($restrictedCountry);
        }

        return $restrictedCountries;
    }
}
