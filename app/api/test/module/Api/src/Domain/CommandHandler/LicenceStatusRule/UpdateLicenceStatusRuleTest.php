<?php

/**
 * UpdateLicenceStatusRuleTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\LicenceStatusRule as StatusRuleRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule\UpdateLicenceStatusRule;

use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule;

use Dvsa\Olcs\Transfer\Command\LicenceStatusRule\UpdateLicenceStatusRule as Cmd;

/**
 * Class UpdateLicenceStatusRuleTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\GracePeriod
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class UpdateLicenceStatusRuleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateLicenceStatusRule();
        $this->mockRepo('LicenceStatusRule', StatusRuleRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 1,
            'status' => 'lsts_curtailed',
            'startDate' => '2015-01-01 00:00:00',
            'endDate' => '2015-02-01 00:00:00'
        ];

        $command = Cmd::create($data);

        $this->repoMap['LicenceStatusRule']
            ->shouldReceive('fetchById')
            ->once()
            ->andReturn(
                m::mock(LicenceStatusRule::class)
                    ->shouldReceive('setStartDate')
                    ->once()
                    ->shouldReceive('setEndDate')
                    ->once()
                    ->shouldReceive('getId')
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('save')
            ->once()
            ->with(m::type(LicenceStatusRule::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'licence-status-rule' => null
            ],
            'messages' => [
                'Licence status rule updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
