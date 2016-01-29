<?php

/**
 * DeleteLicenceStatusRuleTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\LicenceStatusRule as LicenceStatusRuleRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule\DeleteLicenceStatusRule;
use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule;
use Dvsa\Olcs\Transfer\Command\LicenceStatusRule\DeleteLicenceStatusRule as Cmd;

/**
 * Class DeleteGracePeriodTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class DeleteLicenceStatusRuleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteLicenceStatusRule();
        $this->mockRepo('LicenceStatusRule', LicenceStatusRuleRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];

        $command = Cmd::create($data);

        $this->repoMap['LicenceStatusRule']
            ->shouldReceive('fetchById')
            ->once()
            ->andReturn(m::mock(LicenceStatusRule::class))
            ->shouldReceive('delete')
            ->once()
            ->with(m::type(LicenceStatusRule::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'licence-status-rule' => 1
            ],
            'messages' => [
                'Licence status rule deleted.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

    }
}
