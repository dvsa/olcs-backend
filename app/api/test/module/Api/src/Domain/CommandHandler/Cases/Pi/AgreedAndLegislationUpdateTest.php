<?php

/**
 * Agreed and Legislation Update Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Pi\AgreedAndLegislationUpdate;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate as GenerateSlaTargetDateCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Pi\UpdateAgreedAndLegislation as Cmd;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Pi\Reason as ReasonEntity;

/**
 * Agreed and Legislation Update Test
 */
class AgreedAndLegislationUpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AgreedAndLegislationUpdate();
        $this->mockRepo('Pi', PiRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'tc_r_dhtru',
            PiEntity::STATUS_REGISTERED,
            'pi_t_imp',
            'pi_t_other'
        ];

        $this->references = [
            PresidingTcEntity::class => [
                44 => m::mock(PresidingTcEntity::class)
            ],
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ],
            ReasonEntity::class => [
                5 => m::mock(ReasonEntity::class)
            ],
            ReasonEntity::class => [
                7 => m::mock(ReasonEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $agreedByTc = 44;
        $agreedByTcRole = 'tc_r_dhtru';
        $agreedDate = '2015-06-12';
        $comment = 'comment';
        $piTypes = ['pi_t_imp', 'pi_t_other'];
        $reasons = [5,7];
        $id = 22;
        $version = 33;

        $command = Cmd::Create(
            [
                'id' => $id,
                'version' => $version,
                'comment' => $comment,
                'agreedByTc' => $agreedByTc,
                'agreedByTcRole' => $agreedByTcRole,
                'agreedDate' => $agreedDate,
                'piTypes' => $piTypes,
                'reasons' => $reasons,
            ]
        );

        $pi = m::mock(PiEntity::class)->makePartial();

        $this->repoMap['Pi']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($pi)
            ->shouldReceive('save')
            ->with(m::type(PiEntity::class))
            ->once()
            ->andReturnUsing(
                function (PiEntity $pi) use ($id) {
                    $pi->setId($id);
                }
            );

        $this->expectedSideEffect(
            GenerateSlaTargetDateCmd::class,
            [
                'pi' => $id
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
