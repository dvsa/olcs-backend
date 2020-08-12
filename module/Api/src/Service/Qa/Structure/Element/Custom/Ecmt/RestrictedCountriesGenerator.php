<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedPermitTypeConfigProvider;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class RestrictedCountriesGenerator implements ElementGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var RestrictedCountriesFactory */
    private $restrictedCountriesFactory;

    /** @var RestrictedCountryFactory */
    private $restrictedCountryFactory;

    /** @var CountryRepository */
    private $countryRepo;

    /** @var GenericAnswerProvider */
    private $genericAnswerProvider;

    /** @var StockBasedPermitTypeConfigProvider */
    private $stockBasedPermitTypeConfigProvider;

    /**
     * Create service instance
     *
     * @param RestrictedCountriesFactory $restrictedCountriesFactory
     * @param RestrictedCountryFactory $restrictedCountryFactory
     * @param CountryRepository $countryRepo
     * @param GenericAnswerProvider $genericAnswerProvider
     * @param StockBasedPermitTypeConfigProvider $stockBasedPermitTypeConfigProvider
     *
     * @return RestrictedCountriesGenerator
     */
    public function __construct(
        RestrictedCountriesFactory $restrictedCountriesFactory,
        RestrictedCountryFactory $restrictedCountryFactory,
        CountryRepository $countryRepo,
        GenericAnswerProvider $genericAnswerProvider,
        StockBasedPermitTypeConfigProvider $stockBasedPermitTypeConfigProvider
    ) {
        $this->restrictedCountriesFactory = $restrictedCountriesFactory;
        $this->restrictedCountryFactory = $restrictedCountryFactory;
        $this->countryRepo = $countryRepo;
        $this->genericAnswerProvider = $genericAnswerProvider;
        $this->stockBasedPermitTypeConfigProvider = $stockBasedPermitTypeConfigProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getQaEntity();
        $qaContext = $context->getQaContext();

        $yesNo = null;
        try {
            $yesNo = $this->genericAnswerProvider->get($qaContext)->getValue();
        } catch (NotFoundException $e) {
        }

        $irhpPermitStockId = $irhpApplication->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getId();

        $permitTypeConfig = $this->stockBasedPermitTypeConfigProvider->getPermitTypeConfig($irhpPermitStockId);

        $restrictedCountries = $this->restrictedCountriesFactory->create(
            $yesNo,
            $permitTypeConfig->getRestrictedCountriesQuestionKey()
        );

        $restrictedCountryIds = $permitTypeConfig->getRestrictedCountryIds();

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
