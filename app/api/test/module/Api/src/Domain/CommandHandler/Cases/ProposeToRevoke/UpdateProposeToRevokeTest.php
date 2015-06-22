<?php

/**
 * Update ProposeToRevoke Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ProposeToRevoke;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ProposeToRevoke\UpdateProposeToRevoke;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke as ProposeToRevokeEntity;
use Dvsa\Olcs\Api\Entity\Pi\Reason;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Transfer\Command\Cases\ProposeToRevoke\UpdateProposeToRevoke as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Update ProposeToRevoke Test
 */
class UpdateProposeToRevokeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateProposeToRevoke();
        $this->mockRepo('ProposeToRevoke', ProposeToRevoke::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Reason::class => [
                221 => m::mock(Reason::class)
            ],
            PresidingTc::class => [
                1 => m::mock(PresidingTc::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'reasons' => [221],
            'presidingTc' => 1,
            'ptrAgreedDate' => '2015-01-01',
            'closedDate' => '2016-01-01',
            'comment' => 'testing',
        ];

        $command = Cmd::create($data);

        /** @var ProposeToRevokeEntity $proposeToRevoke */
        $proposeToRevoke = m::mock(ProposeToRevokeEntity::class)->makePartial();
        $proposeToRevoke->setId(1);

        $this->repoMap['ProposeToRevoke']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($proposeToRevoke);

        /** @var ProposeToRevokeEntity $savedProposeToRevoke */
        $savedProposeToRevoke = null;

        $this->repoMap['ProposeToRevoke']->shouldReceive('save')
            ->once()
            ->with(m::type(ProposeToRevokeEntity::class))
            ->andReturnUsing(
                function (ProposeToRevokeEntity $proposeToRevoke) use (&$savedProposeToRevoke) {
                    $savedProposeToRevoke = $proposeToRevoke;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'proposeToRevoke' => 1,
            ],
            'messages' => [
                'Revocation updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->references[Reason::class][$data['reasons'][0]],
            $savedProposeToRevoke->getReasons()[0]
        );
        $this->assertSame(
            $this->references[PresidingTc::class][$data['presidingTc']],
            $savedProposeToRevoke->getPresidingTc()
        );
        $this->assertEquals($data['ptrAgreedDate'], $savedProposeToRevoke->getPtrAgreedDate()->format('Y-m-d'));
        $this->assertEquals($data['closedDate'], $savedProposeToRevoke->getClosedDate()->format('Y-m-d'));
        $this->assertEquals($data['comment'], $savedProposeToRevoke->getComment());
    }
}
