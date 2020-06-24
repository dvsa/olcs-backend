<?php

/**
 * DeletePartner Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Partner as PartnerRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeletePartner;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\User\DeletePartner as Cmd;

/**
 * Class DeletePartnerTest
 */
class DeletePartnerTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeletePartner();
        $this->mockRepo('Partner', PartnerRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];

        $command = Cmd::create($data);

        $contactDetailsEntity = m::mock(ContactDetails::class)->makePartial();
        $contactDetailsEntity->setId(1);

        $this->repoMap['Partner']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->andReturn($contactDetailsEntity)
            ->shouldReceive('delete')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'partner' => 1
            ],
            'messages' => [
                'Partner deleted successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
