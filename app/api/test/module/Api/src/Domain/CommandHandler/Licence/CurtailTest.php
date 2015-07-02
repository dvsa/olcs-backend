<?php

/**
 * CurtailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Curtail as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Licence\Curtail as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * CurtailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CurtailTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['lsts_curtailed'];

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
                $this->assertSame($this->refData['lsts_curtailed'], $saveLicence->getStatus());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getCurtailedDate());
                $this->assertSame(
                    (new \DateTime())->format('Y-m-d'),
                    $saveLicence->getCurtailedDate()->format('Y-m-d')
                );
            }
        );

        $removeRulesResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            [
                'licence' => 532
            ],
            $removeRulesResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 curtailed"], $result->getMessages());
    }
}
