<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Common\ArrayCollectionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;

class RestrictedCountriesAnswerSaver implements AnswerSaverInterface
{
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

    /**
     * Create service instance
     *
     * @param IrhpApplicationRepository $irhpApplicationRepo
     * @param CountryRepository $countryRepo
     * @param ArrayCollectionFactory $arrayCollectionFactory
     * @param NamedAnswerFetcher $namedAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return RestrictedCountriesAnswerSaver
     */
    public function __construct(
        IrhpApplicationRepository $irhpApplicationRepo,
        CountryRepository $countryRepo,
        ArrayCollectionFactory $arrayCollectionFactory,
        NamedAnswerFetcher $namedAnswerFetcher,
        GenericAnswerWriter $genericAnswerWriter
    ) {
        $this->irhpApplicationRepo = $irhpApplicationRepo;
        $this->countryRepo = $countryRepo;
        $this->arrayCollectionFactory = $arrayCollectionFactory;
        $this->namedAnswerFetcher = $namedAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
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

            foreach (RestrictedCountryCodes::CODES as $code) {
                if (in_array($code, $selectedCountries)) {
                    $countryReferences->add(
                        $this->irhpApplicationRepo->getReference(Country::class, $code)
                    );
                }
            }
        }

        $this->genericAnswerWriter->write(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $hasRestrictedCountries,
            Question::QUESTION_TYPE_BOOLEAN
        );

        $irhpApplicationEntity->updateCountries($countryReferences);
        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
