<?php

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Revoke as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Licence\Revoke as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class RevokeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['lsts_revoked'];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertSame($this->refData['lsts_revoked'], $saveLicence->getStatus());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getRevokedDate());
                $this->assertSame((new \DateTime())->format('Y-m-d'), $saveLicence->getRevokedDate()->format('Y-m-d'));
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 revoked"], $result->getMessages());
    }
}
