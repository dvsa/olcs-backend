<?php

/**
 * VoidAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\VoidAllCommunityLicences as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences as Command;

/**
 * VoidAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 *
 */
class VoidAllCommunityLicencesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Entity\Licence\Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 717,
        ];

        $command = Command::create($data);

        $cl1 = new \Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic();
        $cl1->setId(43);
        $cl2 = new \Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic();
        $cl2->setId(38);

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('getCommunityLics')->with()->once()->andReturn([$cl1, $cl2]);
        $mockLicence->shouldReceive('getId')->with()->once()->andReturn(717);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717)->once()->andReturn($mockLicence);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\CommunityLic\Void::class,
            ['licence' => 717, 'communityLicenceIds' => [43, 38], 'checkOfficeCopy' => false],
            new Result()
        );

        $this->sut->handleCommand($command);
    }
}
