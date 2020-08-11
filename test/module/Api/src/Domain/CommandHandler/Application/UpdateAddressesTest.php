<?php

/**
 * Update Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateAddresses;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Application\UpdateAddresses as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UpdateAddressesTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateAddresses();
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommandWithDirtyAddresses()
    {
        $data = [
            'id' => 123,
            'correspondence' => 'corr',
            'contact' => 'contact',
            'correspondenceAddress' => 'c_address',
            'establishment' => 'est',
            'establishmentAddress' => 'e_address',
            'consultant' => 'consultant'
        ];
        $command = Cmd::create($data);

        $result = new Result();

        $result->setFlag('isDirty', true);

        // licence ID overrides application ID
        $addressData = array_merge($data, ['id' => 456]);

        $this->expectedSideEffect(
            SaveAddresses::class,
            $addressData,
            $result
        );

        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            [
                'id' => 123,
                'section' => 'addresses'
            ],
            $result
        );

        $application = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock(LicenceEntity::class)
                ->shouldReceive('getId')
                ->andReturn(456)
                ->getMock()
            )
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [],
            'flags' => ['isDirty' => 1]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
