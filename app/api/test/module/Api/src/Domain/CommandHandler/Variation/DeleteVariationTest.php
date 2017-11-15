<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\DeleteVariation as Sut;
use Dvsa\Olcs\Api\Domain\Exception\BadVariationTypeException;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepository;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Application\DeletePeople;
use Dvsa\Olcs\Transfer\Command\Document\DeleteDocuments;
use Dvsa\Olcs\Transfer\Command\PreviousConviction\DeletePreviousConviction;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteVariation;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class DeleteVariationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('Application', ApplicationRepository::class);
        $this->sut = new Sut();
        parent::setUp();
    }

    public function testThatCommandIsTransactional()
    {
        $this->assertInstanceOf(TransactionedInterface::class, $this->sut);
    }

    public function testThatApplicationsAreRejected()
    {
        $this->createMockApplication(false, null);
        $this->expectExceptionWithMessage("Applications can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatStandardVariationsAreRejected()
    {
        $this->createMockApplication(true, null);
        $this->expectExceptionWithMessage("Standard variations can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatOtherVariationTypesAreRejected()
    {
        $this->createMockApplication(true, 'vtyp_foo');
        $this->expectExceptionWithMessage("Variations of type 'vtyp_foo' can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatDirectorChangeVariationsAreDeleted()
    {
        $application = $this->createMockApplication(true, Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $this->expectDeletePersons($application);
        $this->expectDeleteDocuments($application);
        $this->expectDeletePreviousConvictions($application);
        $this->expectDeleteApplication($application);

        $this->assertResult(
            $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID'])),
            [
                'id' => ['application DUMMY_APPLICATION_ID' => 'DUMMY_APPLICATION_ID'],
                'messages' => ['Application with id DUMMY_APPLICATION_ID was deleted']
            ]
        );
    }

    private function createMockApplication($isVariation, $variationType)
    {
        /** @var Application|m\Mock $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId('DUMMY_APPLICATION_ID');
        $application->setIsVariation($isVariation);
        if ($variationType !== null) {
            $application->setVariationType(new RefData($variationType));
        }
        $this->repoMap['Application']->shouldReceive('fetchById')->with($application->getId())->andReturn(
            $application
        );
        return $application;
    }

    private function createMockApplicationOrganisationPerson($personId)
    {
        return m::mock(ApplicationOrganisationPerson::class)->shouldReceive('getPerson')->with()->andReturn(
            m::mock(Person::class)->shouldReceive('getId')->with()->andReturn($personId)->getMock()
        )->getMock();
    }

    private function createMockDocument($documentId)
    {
        return m::mock(Document::class)->shouldReceive('getId')->with()->andReturn($documentId)->getMock();
    }

    private function createMockConviction($convictionId)
    {
        return m::mock(PreviousConviction::class)->shouldReceive('getId')->with()->andReturn($convictionId)->getMock();
    }

    private function expectDeletePersons(m\MockInterface $application)
    {
        $application->shouldReceive('getApplicationOrganisationPersons')->with()->andReturn(
            [
                $this->createMockApplicationOrganisationPerson('DUMMY_PERSON_ID_1'),
                $this->createMockApplicationOrganisationPerson('DUMMY_PERSON_ID_2'),
            ]
        );

        $this->expectedSideEffect(
            DeletePeople::class,
            ['id' => 'DUMMY_APPLICATION_ID', 'personIds' => ['DUMMY_PERSON_ID_1', 'DUMMY_PERSON_ID_2',]],
            new Result()
        );
    }

    private function expectDeleteDocuments(m\MockInterface $application)
    {
        $application->shouldReceive('getDocuments')->with()->andReturn(
            [$this->createMockDocument('DUMMY_DOCUMENT_ID_1'), $this->createMockDocument('DUMMY_DOCUMENT_ID_2')]
        );

        $this->expectedSideEffect(
            DeleteDocuments::class,
            ['ids' => ['DUMMY_DOCUMENT_ID_1', 'DUMMY_DOCUMENT_ID_2',]],
            new Result()
        );
    }

    private function expectDeletePreviousConvictions(m\MockInterface $application)
    {
        $application->shouldReceive('getPreviousConvictions')->with()->andReturn(
            [$this->createMockConviction('DUMMY_CONVICTION_ID_1'), $this->createMockConviction('DUMMY_CONVICTION_ID_2')]
        );

        $this->expectedSideEffect(
            DeletePreviousConviction::class,
            ['ids' => ['DUMMY_CONVICTION_ID_1', 'DUMMY_CONVICTION_ID_2',]],
            new Result()
        );
    }

    private function expectDeleteApplication(Application $application)
    {
        $this->repoMap['Application']->shouldReceive('delete')->once()->with($application);
    }

    private function assertResult(Result $result, $data)
    {
        $this->assertSame($data, $result->toArray());
    }

    /**
     * @param string $message
     */
    private function expectExceptionWithMessage($message)
    {
        $this->expectException(BadVariationTypeException::class);
        $this->expectExceptionMessage($message);
    }
}
