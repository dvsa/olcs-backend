<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Common\ArrayCollectionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\RestrictedCountriesAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountriesAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesAnswerSaverTest extends MockeryTestCase
{
    private $applicationStep;

    private $irhpApplication;

    private $arrayCollection;

    private $irhpApplicationRepo;

    private $countryRepo;

    private $arrayCollectionFactory;

    private $namedAnswerFetcher;

    private $genericAnswerWriter;

    private $restrictedCountriesAnswerSaver;

    public function setUp()
    {
        $this->applicationStep = m::mock(ApplicationStepEntity::class);

        $this->irhpApplication = m::mock(IrhpApplicationEntity::class);

        $this->arrayCollection = m::mock(ArrayCollection::class);

        $this->irhpApplicationRepo = m::mock(IrhpApplicationRepository::class);

        $this->countryRepo = m::mock(CountryRepository::class);

        $this->arrayCollectionFactory = m::mock(ArrayCollectionFactory::class);
        $this->arrayCollectionFactory->shouldReceive('create')
            ->withNoArgs()
            ->andReturn($this->arrayCollection);

        $this->namedAnswerFetcher = m::mock(NamedAnswerFetcher::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->restrictedCountriesAnswerSaver = new RestrictedCountriesAnswerSaver(
            $this->irhpApplicationRepo,
            $this->countryRepo,
            $this->arrayCollectionFactory,
            $this->namedAnswerFetcher,
            $this->genericAnswerWriter
        );
    }

    public function testSaveWhenYes()
    {
        $selectedCountry1Code = 'HU';
        $selectedCountry1Reference = m::mock(Country::class);

        $selectedCountry2Code = 'IT';
        $selectedCountry2Reference = m::mock(Country::class);

        $hasRestrictedCountries = 1;
        $selectedRestrictedCountries = ['HU', 'IT'];

        $postData = [
            'qa' => [
                'fieldset12' => [
                    'restrictedCountries' => $hasRestrictedCountries,
                    'yesContent' => $selectedRestrictedCountries
                ]
            ]
        ];

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $postData, 'restrictedCountries')
            ->andReturn($hasRestrictedCountries);
        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $postData, 'yesContent')
            ->andReturn($selectedRestrictedCountries);

        $this->irhpApplicationRepo->shouldReceive('getReference')
            ->with(Country::class, $selectedCountry1Code)
            ->andReturn($selectedCountry1Reference);
        $this->irhpApplicationRepo->shouldReceive('getReference')
            ->with(Country::class, $selectedCountry2Code)
            ->andReturn($selectedCountry2Reference);

        $this->arrayCollection->shouldReceive('add')
            ->with($selectedCountry1Reference)
            ->once()
            ->globally()
            ->ordered();
        $this->arrayCollection->shouldReceive('add')
            ->with($selectedCountry2Reference)
            ->once()
            ->globally()
            ->ordered();

        $this->irhpApplication->shouldReceive('updateCountries')
            ->with($this->arrayCollection)
            ->once()
            ->globally()
            ->ordered();

        $this->irhpApplicationRepo->shouldReceive('save')
            ->with($this->irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->genericAnswerWriter->shouldReceive('write')
            ->with(
                $this->applicationStep,
                $this->irhpApplication,
                $hasRestrictedCountries,
                Question::QUESTION_TYPE_BOOLEAN
            )
            ->once();

        $this->restrictedCountriesAnswerSaver->save($this->applicationStep, $this->irhpApplication, $postData);
    }

    public function testSaveWhenNo()
    {
        $hasRestrictedCountries = 0;
        $selectedRestrictedCountries = null;

        $postData = [
            'qa' => [
                'fieldset12' => [
                    'restrictedCountries' => $hasRestrictedCountries,
                    'yesContent' => null
                ]
            ]
        ];

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $postData, 'restrictedCountries')
            ->andReturn($hasRestrictedCountries);
        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $postData, 'yesContent')
            ->andReturn($selectedRestrictedCountries);

        $this->irhpApplication->shouldReceive('updateCountries')
            ->with($this->arrayCollection)
            ->once()
            ->globally()
            ->ordered();

        $this->irhpApplicationRepo->shouldReceive('save')
            ->with($this->irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->genericAnswerWriter->shouldReceive('write')
            ->with(
                $this->applicationStep,
                $this->irhpApplication,
                $hasRestrictedCountries,
                Question::QUESTION_TYPE_BOOLEAN
            )
            ->once();

        $this->restrictedCountriesAnswerSaver->save($this->applicationStep, $this->irhpApplication, $postData);
    }
}
