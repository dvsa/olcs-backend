<?php

/**
 * Create Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Hearing;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing\CreateAppeal;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Hearing\CreateAppeal as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Appeal as AppealEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Create Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CreateAppealTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateAppeal();
        $this->mockRepo('Appeal', AppealEntity::class);
        $this->mockRepo('Cases', Cases::class);

        parent::setUp();
    }

    public function testHandleCommandNoExistingAppeal()
    {
        $this->refData = [
            'appeal_r_lic_non_pi',
            'appeal_o_dis'
        ];

        $mockCase = m::mock(Cases::class)->makePartial();
        $mockCase->shouldReceive('getAppeals')
            ->andReturn(new ArrayCollection([]));

        $this->references = [
            Cases::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();

        $command = Cmd::create(
            [
                "case" => 24,
                "appealDate" => "2015-01-05",
                "appealNo" => "1231234",
                "reason" => "appeal_r_lic_non_pi",
                "outcome" => "appeal_o_dis",
                "comment" => "booo",
                "isWithdrawn" => "Y",
                "withdrawnDate" => "2015-05-05",
                "deadlineDate" => "2015-05-05",
                "outlineGround" => "asdfasdf",
                "hearingDate" => "2015-05-05",
                "decisionDate" => "2015-05-05",
                "papersDueDate" => "2015-05-05",
                "papersDueTcDate" => "2015-05-06",
                "papersSentDate" => "2015-05-05",
                "papersSentTcDate" => "2015-07-07",
            ]
        );

        /** @var AppealEntity $app */
        $comp = null;

        $this->repoMap['Appeal']
            ->shouldReceive('save')
            ->with(m::type(AppealEntity::class))
            ->andReturnUsing(
                function (AppealEntity $appeal) use (&$comp) {
                    $appeal->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Appeal created', $result->getMessages());
    }

    /**
     *
     */
    public function testHandleCommandExistingAppeal()
    {
        $this->refData = [
            'appeal_r_lic_non_pi',
            'appeal_o_dis'
        ];

        $mockAppeal = m::mock(Appeal::class);
        $mockCase = m::mock(Cases::class)->makePartial();
        $mockCase->shouldReceive('getAppeal')
            ->andReturn($mockAppeal);

        $this->references = [
            Cases::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();

        $command = Cmd::create(
            [
                "case" => 24,
                "appealDate" => "2015-01-05",
                "appealNo" => "1231234",
                "outlineGround" => 'asdfasdf',
                "reason" => "appeal_r_lic_non_pi",
                "outcome" => "appeal_o_dis",
                "comment" => "booo",
                "isWithdrawn" => "Y",
                "hearingDate" => "2015-05-01",
                "decisionDate" => "2015-05-05",
                "papersDueDate" => "2015-05-05",
                "papersDueTcDate" => "2015-05-05",
                "papersSentDate" => "2015-05-05",
                "papersSentTcDate" => "2015-05-05",
                "withdrawnDate" => "2015-05-05"
            ]
        );

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
