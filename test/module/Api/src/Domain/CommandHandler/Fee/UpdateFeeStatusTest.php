<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Fee\UpdateFeeStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\UpdateFeeStatus;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update Fee Status Test
 */
class UpdateFeeStatusTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
        ];

        $this->sut = new UpdateFeeStatus();
        $this->mockRepo('Fee', Repository\FeeType::class);

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }

    public function testHandleCommand()
    {

        $command = Cmd::create([
            'id' => 1,
            'status' => Fee::STATUS_REFUNDED
        ]);

        $mockFee = m::mock(Fee::class);
        $mockFee->shouldReceive('setStatus')->once();

        $this->repoMap['Fee']
            ->shouldReceive('fetchById')
            ->once()
            ->with($command->getId())
            ->andReturn($mockFee);
        
        $this->repoMap['Fee']
            ->shouldReceive('getRefdataReference')
            ->once()
            ->with(Fee::STATUS_REFUNDED)
            ->andReturn(new RefData('1'));


        $result = $this->sut->handleCommand($command);

//        $expected = [
//            'id' => [],
//            'messages' => [
//                'Fee 100 reset to Outstanding',
//                'Fee 101 reset to Cancelled',
//            ]
//        ];
//
//        $this->assertEquals($expected, $result->toArray());
    }
}
