<?php

/**
 * RemoveLicenceVehicleTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle as Cmd;

/**
 * Class RemoveLicenceVehicleTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class RemoveLicenceVehicleTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new RemoveLicenceVehicle();
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 123
        ];

        $command = Cmd::create($data);

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('removeAllForLicence')
            ->once()
            ->with(123);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
            ],
            'messages' => [
                'Removed vehicles for licence.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
