<?php

/**
 * DeleteTransportManagerLicenceTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceStatusRule;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TransportManagerLicenceRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
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

        $this->mockedSmServices = [
            CacheEncryption::class => m::mock(CacheEncryption::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 7
        ];

        $command = Cmd::create($data);

        $tm = m::mock(TransportManager::class);
        $tm = $this->expectedCacheClearFromUserCollection($tm);

        $tmLicence = m::mock(TransportManagerLicence::class);
        $tmLicence->expects('getTransportManager')->withNoArgs()->andReturn($tm);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchForLicence')
            ->once()
            ->with(7)
            ->andReturn(
                [
                    $tmLicence
                ]
            )
            ->shouldReceive('delete')
            ->with($tmLicence);

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
