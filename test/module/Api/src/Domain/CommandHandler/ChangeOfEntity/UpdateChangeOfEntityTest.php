<?php

/**
 * Update Change Of Entity test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ChangeOfEntity;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\ChangeOfEntity\UpdateChangeOfEntity as Sut;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\ChangeOfEntity as ChangeOfEntityRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\ChangeOfEntity\UpdateChangeOfEntity as Cmd;
use Dvsa\Olcs\Api\Entity\Organisation\ChangeOfEntity as ChangeOfEntityEntity;

/**
 * Update Change Of Entity test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UpdateChangeOfEntityTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('ChangeOfEntity', ChangeOfEntityRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'id' => 11,
                'oldLicenceNo' => 'AB1234',
                'oldOrganisationName' => 'Foo',
            ]
        );

        /** @var ChangeOfEntityEntity $changeOfEntity */
        $changeOfEntity = m::mock(ChangeOfEntityEntity::class)
            ->makePartial()
            ->setId(11);

        $this->repoMap['ChangeOfEntity']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($changeOfEntity)
            ->shouldReceive('save')
            ->with($changeOfEntity)
            ->once();
        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('ChangeOfEntity Updated', $result->getMessages());
        $this->assertEquals(11, $result->getId('changeOfEntity'));
        $this->assertEquals('AB1234', $changeOfEntity->getOldLicenceNo());
        $this->assertEquals('Foo', $changeOfEntity->getOldOrganisationName());
    }
}
