<?php

/**
 * Common Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CancelAllInterimFees;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantCommunityLicence;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantConditionUndertaking;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantPeople;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantTransportManager;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\CommonGrant;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\PrintLicence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant as CommonGrantCmd;

/**
 * Common Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonGrantTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommonGrant();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = CommonGrantCmd::create($data);

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(333);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result1 = new Result();
        $result1->addMessage('CancelAllInterimFees');
        $this->expectedSideEffect(CancelAllInterimFees::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('GrantConditionUndertaking');
        $this->expectedSideEffect(GrantConditionUndertaking::class, $data, $result2);

        $result3 = new Result();
        $result3->addMessage('GrantCommunityLicence');
        $this->expectedSideEffect(GrantCommunityLicence::class, $data, $result3);

        $result4 = new Result();
        $result4->addMessage('GrantTransportManager');
        $this->expectedSideEffect(GrantTransportManager::class, $data, $result4);

        $result5 = new Result();
        $result5->addMessage('GrantPeople');
        $this->expectedSideEffect(GrantPeople::class, $data, $result5);

        $result6 = new Result();
        $result6->addMessage('PrintLicence');
        $this->expectedSideEffect(PrintLicence::class, ['id' => 333], $result6);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CancelAllInterimFees',
                'GrantConditionUndertaking',
                'GrantCommunityLicence',
                'GrantTransportManager',
                'GrantPeople',
                'PrintLicence'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
