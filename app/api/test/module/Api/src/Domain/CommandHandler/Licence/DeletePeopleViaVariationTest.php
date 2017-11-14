<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeletePeopleViaVariation;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\DeletePeople;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation;
use Dvsa\Olcs\Transfer\Command\Licence\DeletePeopleViaVariation as DeletePeopleViaVariationCommand;
use Dvsa\Olcs\Transfer\Command\Variation\GrantDirectorChange;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Zend\Stdlib\ArraySerializableInterface;

class DeletePeopleViaVariationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeletePeopleViaVariation();
        parent::setUp();
    }

    public function testSutIsCommandHandler()
    {
        $this->assertInstanceOf(AbstractCommandHandler::class, $this->sut);
    }

    public function testHandleCommand()
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type(CreateVariation::class), false)
            ->andReturnUsing(
                function (CreateVariation $command) {
                    $this->assertDtoSame(
                        CreateVariation::create(
                            [
                                'id' => 'TEST_LICENCE_ID',
                                'variationType' => Application::VARIATION_TYPE_DIRECTOR_CHANGE,
                            ]
                        ),
                        $command
                    );
                    $result = new Result();
                    $result->addId('application', 'TEST_VARIATION_ID');
                    return $result;
                }
            );

        $deleteCommandHasBeenCalled = false;

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type(DeletePeople::class), false)
            ->andReturnUsing(
                function (DeletePeople $command) use (&$deleteCommandHasBeenCalled) {
                    $this->assertDtoSame(
                        DeletePeople::create(
                            ['id' => 'TEST_VARIATION_ID', 'personIds' => ['TEST_PERSON_ID_1', 'TEST_PERSON_ID_2']]
                        ),
                        $command
                    );
                    $deleteCommandHasBeenCalled = true;
                    return new Result();
                }
            );

        $grantResult = new Result();
        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type(GrantDirectorChange::class), false)
            ->andReturnUsing(
                function (GrantDirectorChange $command) use ($grantResult, &$deleteCommandHasBeenCalled) {
                    $this->assertTrue($deleteCommandHasBeenCalled, 'Grant called before delete');
                    $this->assertDtoSame(GrantDirectorChange::create(['id' => 'TEST_VARIATION_ID',]), $command);
                    return $grantResult;
                }
            );

        $this->assertSame(
            $grantResult,
            $this->sut->handleCommand(
                DeletePeopleViaVariationCommand::create(
                    ['id' => 'TEST_LICENCE_ID', 'personIds' => ['TEST_PERSON_ID_1', 'TEST_PERSON_ID_2']]
                )
            )
        );
    }

    private function assertDtoSame(ArraySerializableInterface $expected, ArraySerializableInterface $actual)
    {
        $this->assertInstanceOf(get_class($expected), $actual);
        $this->assertEquals($expected->getArrayCopy(), $actual->getArrayCopy());
    }
}
