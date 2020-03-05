<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedRestrictedCountryIdsProvider;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Common\ArrayCollectionFactory;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class RestrictedCountriesAnswerSaver implements AnswerSaverInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /** @var CountryRepository */
    private $countryRepo;

    /** @var ArrayCollectionFactory */
    private $arrayCollectionFactory;

    /** @var NamedAnswerFetcher */
    private $namedAnswerFetcher;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var StockBasedRestrictedCountryIdsProvider */
    private $stockBasedRestrictedCountryIdsProvider;

    /**
     * Create service instance
     *
     * @param IrhpApplicationRepository $irhpApplicationRepo
     * @param CountryRepository $countryRepo
     * @param ArrayCollectionFactory $arrayCollectionFactory
     * @param NamedAnswerFetcher $namedAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param StockBasedRestrictedCountryIdsProvider $stockBasedRestrictedCountryIdsProvider
     *
     * @return RestrictedCountriesAnswerSaver
     */
    public function __construct(
        IrhpApplicationRepository $irhpApplicationRepo,
        CountryRepository $countryRepo,
        ArrayCollectionFactory $arrayCollectionFactory,
        NamedAnswerFetcher $namedAnswerFetcher,
        GenericAnswerWriter $genericAnswerWriter,
        StockBasedRestrictedCountryIdsProvider $stockBasedRestrictedCountryIdsProvider
    ) {
        $this->irhpApplicationRepo = $irhpApplicationRepo;
        $this->countryRepo = $countryRepo;
        $this->arrayCollectionFactory = $arrayCollectionFactory;
        $this->namedAnswerFetcher = $namedAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->stockBasedRestrictedCountryIdsProvider = $stockBasedRestrictedCountryIdsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();
        $irhpApplicationEntity = $qaContext->getQaEntity();

        $hasRestrictedCountries = $this->namedAnswerFetcher->fetch(
            $applicationStepEntity,
            $postData,
            'restrictedCountries'
        );

        $countryReferences = $this->arrayCollectionFactory->create();

        if ($hasRestrictedCountries) {
            $selectedCountries = $this->namedAnswerFetcher->fetch(
                $applicationStepEntity,
                $postData,
                'yesContent'
            );

            $stockId = $irhpApplicationEntity->getFirstIrhpPermitApplication()
                ->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getId();

            $countryIds = $this->stockBasedRestrictedCountryIdsProvider->getIds($stockId);

            foreach ($countryIds as $countryId) {
                if (in_array($countryId, $selectedCountries)) {
                    $countryReferences->add(
                        $this->irhpApplicationRepo->getReference(Country::class, $countryId)
                    );
                }
            }
        }

        $this->genericAnswerWriter->write($qaContext, $hasRestrictedCountries, Question::QUESTION_TYPE_BOOLEAN);

        $irhpApplicationEntity->updateCountries($countryReferences);
        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
