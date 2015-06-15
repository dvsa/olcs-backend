<?php

/**
 * Create Stay Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Hearing;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing\CreateStay;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Hearing\CreateStay as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Stay as StayEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Appeal as AppealEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Create Stay Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CreateStayTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateStay();
        $this->mockRepo('Stay', StayEntity::class);
        $this->mockRepo('Cases', CasesEntity::class);
        $this->mockRepo('Appeal', AppealEntity::class);

        $this->refData = [
            'stay_t_ut',
        ];

        parent::setUp();
    }

    public function testHandleCommandExistingAppeal()
    {
        $mockAppeal = m::mock(AppealEntity::class);
        $mockCase = m::mock(CasesEntity::class)->makePartial();
        $mockCase->shouldReceive('getAppeals')
            ->andReturn(new ArrayCollection([$mockAppeal]));

        $mockCase->shouldReceive('getStays')
            ->andReturn(new ArrayCollection([]));

        $this->references = [
            CasesEntity::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();

        $command = Cmd::create(
            [
                "case" => 24,
                "stayType" => "stay_t_ut",
                "requestDate" => "2015-01-05",
                "decisionDate" => "2015-01-09",
                "outcome" => "stay_s_granted",
                "notes" => "booo",
                "isWithdrawn" => "Y",
                "withdrawnDate" => "2015-05-05"
            ]
        );

        /** @var StayEntity $stay */
        $s = null;

        $this->repoMap['Stay']
            ->shouldReceive('save')
            ->with(m::type(StayEntity::class))
            ->andReturnUsing(
                function (StayEntity $stay) use (&$s) {
                    $s = $stay;
                    $stay->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Stay created', $result->getMessages());
    }

    public function testHandleCommandNoExistingAppeal()
    {
        $mockCase = m::mock(CasesEntity::class)->makePartial();
        $mockCase->shouldReceive('getAppeals')
            ->andReturn(new ArrayCollection([]));

        $this->references = [
            CasesEntity::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();

        $command = Cmd::create(
            [
                "case" => 24,
                "stayType" => "stay_t_ut",
                "requestDate" => "2015-01-05",
                "decisionDate" => "2015-01-09",
                "outcome" => "stay_s_granted",
                "notes" => "booo",
                "isWithdrawn" => "Y",
                "withdrawnDate" => "2015-05-05"
            ]
        );

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandStayTypeAlreadyExists()
    {
        $mockAppeal = m::mock(AppealEntity::class);

        $mockCase = m::mock(CasesEntity::class)->makePartial();
        $mockCase->shouldReceive('hasStayType')->andReturn(true);

        $mockCase->shouldReceive('getAppeals')
            ->andReturn(new ArrayCollection([$mockAppeal]));

        $this->references = [
            CasesEntity::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();

        $command = Cmd::create(
            [
                "case" => 24,
                "stayType" => "stay_t_ut",
                "requestDate" => "2015-01-05",
                "decisionDate" => "2015-01-09",
                "outcome" => "stay_s_granted",
                "notes" => "booo",
                "isWithdrawn" => "Y",
                "withdrawnDate" => "2015-05-05"
            ]
        );

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
