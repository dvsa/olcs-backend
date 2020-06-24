<?php

/**
 * DeleteTransportManagerLicenceTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TransportManagerLicenceRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence as Cmd;

/**
 * Class DeleteTransportManagerLicenceTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class DeleteTransportManagerLicenceTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeleteTransportManagerLicence();
        $this->mockRepo('TransportManagerLicence', TransportManagerLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 7
        ];

        $command = Cmd::create($data);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchForLicence')
            ->once()
            ->with(7)
            ->andReturn(
                [
                    m::mock(TransportManagerLicence::class)->makePartial()
                ]
            )
            ->shouldReceive('delete')
            ->with(m::type(TransportManagerLicence::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
            ],
            'messages' => [
                'Removed transport managers for licence.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
