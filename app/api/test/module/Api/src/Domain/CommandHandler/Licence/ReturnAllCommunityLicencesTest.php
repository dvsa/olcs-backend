<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ReturnAllCommunityLicences as CommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * ReturnAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ReturnAllCommunityLicencesTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('CommunityLic', \Dvsa\Olcs\Api\Domain\Repository\CommunityLic::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLic::STATUS_RETURNDED => m::mock(RefData::class)->makePartial()->setDescription('returned'),
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 608,
        ];
        $command = Cmd::create($data);

        $licence = new Licence(new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(), new RefData());
        $licence->setId(608);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(608)
            ->andReturn($licence);

        $this->repoMap['CommunityLic']->shouldReceive('expireAllForLicence')
            ->with(608, CommunityLic::STATUS_RETURNDED)
            ->once();

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences::class,
            ['id' => 608],
            (new \Dvsa\Olcs\Api\Domain\Command\Result())->addMessage('UpdateTotalCommunityLicences')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Community licence(s) updated to returned',
                'UpdateTotalCommunityLicences',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
