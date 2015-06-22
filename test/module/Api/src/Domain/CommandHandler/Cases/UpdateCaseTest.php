<?php

/**
 * Update Case Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\UpdateCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\UpdateCase as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;

/**
 * Update Case Test
 */
class UpdateCaseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateCase();
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            ApplicationEntity::class => [
                5 => m::mock(ApplicationEntity::class)
            ],
            LicenceEntity::class => [
                7 => m::mock(LicenceEntity::class)
            ],
            TransportManagerEntity::class => [
                9 => m::mock(TransportManagerEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            [
                'id' => 99,
                'categorys' => ['case_cat_compl_proh'],
                'outcomes' => ['case_o_opr'],
                'application' => 5,
                'licence' => 7,
                'transportManager' => 9
            ]
        );

        /** @var CasesEntity $cases */
        $cases = m::mock(CasesEntity::class)->makePartial();
        $cases->shouldReceive('update')
            ->once();

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($cases)
            ->shouldReceive('save')
            ->with(m::type(CasesEntity::class))
            ->once();


        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
    }
}
