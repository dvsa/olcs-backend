<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ReturnAllCommunityLicences as CommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Licence;
use Mockery as m;

/**
 * ReturnAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ReturnAllCommunityLicencesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('CommunityLic', \Dvsa\Olcs\Api\Domain\Repository\CommunityLic::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLic::STATUS_RETURNDED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 608,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(608);
        $comLic1 = new CommunityLic();
        $comLic2 = new CommunityLic();
        $licence->setCommunityLics(new ArrayCollection([$comLic1, $comLic2]));

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(608)
            ->andReturn($licence);

        $this->repoMap['CommunityLic']->shouldReceive('save')->with($comLic1)->andReturnUsing(
            function (CommunityLic $communityLic) {
                $this->assertSame($this->refData[CommunityLic::STATUS_RETURNDED], $communityLic->getStatus());
                $this->assertEquals(new DateTime(), $communityLic->getExpiredDate());
            }
        );

        $this->repoMap['CommunityLic']->shouldReceive('save')->with($comLic2)->andReturnUsing(
            function (CommunityLic $communityLic) {
                $this->assertSame($this->refData[CommunityLic::STATUS_RETURNDED], $communityLic->getStatus());
                $this->assertEquals(new DateTime(), $communityLic->getExpiredDate());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences::class,
            ['id' => 608],
            (new \Dvsa\Olcs\Api\Domain\Command\Result())->addMessage('UpdateTotalCommunityLicences')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Community licence returned',
                'UpdateTotalCommunityLicences',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
