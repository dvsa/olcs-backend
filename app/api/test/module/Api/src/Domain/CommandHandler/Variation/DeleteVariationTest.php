<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\DeleteVariation as Sut;
use Dvsa\Olcs\Api\Domain\Exception\BadVariationTypeException;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepository;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\RefData;
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

    public function testThatApplicationsAreRejected()
    {
        $this->createMockApplication(false, null);
        $this->expectException(BadVariationTypeException::class);
        $this->expectExceptionMessage("Applications can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatStandardVariationsAreRejected()
    {
        $this->createMockApplication(true, null);
        $this->expectException(BadVariationTypeException::class);
        $this->expectExceptionMessage("Standard variations can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatOtherVariationTypesAreRejected()
    {
        $this->createMockApplication(true, 'vtyp_foo');
        $this->expectException(BadVariationTypeException::class);
        $this->expectExceptionMessage("Variations of type 'vtyp_foo' can not be deleted");
        $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
    }

    public function testThatDirectorChangeVariationsAreDeleted()
    {
        $application = $this->createMockApplication(true, Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $this->repoMap['Application']->shouldReceive('delete')->once()->with($application);
        $result = $this->sut->handleCommand(DeleteVariation::create(['id' => 'DUMMY_APPLICATION_ID']));
        self::assertInstanceOf(Result::class, $result);
        $this->assertSame(
            ['id' => ['application DUMMY_APPLICATION_ID' => 'DUMMY_APPLICATION_ID'], 'messages' => [
                'Application with id DUMMY_APPLICATION_ID was deleted'
            ]],
            $result->toArray()
        );
    }

    /**
     * @param $isVariation
     * @param $variationType
     *
     * @return Application|m\Mock
     */
    private function createMockApplication($isVariation, $variationType)
    {
        /** @var Application|m\Mock $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setIsVariation($isVariation);
        if ($variationType !== null) {
            $application->setVariationType(new RefData($variationType));
        }
        $this->repoMap['Application']->shouldReceive('fetchById')->with('DUMMY_APPLICATION_ID')->andReturn(
            $application
        );
        return $application;
    }
}
