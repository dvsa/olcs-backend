<?php

/**
 * Update Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Hearing;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing\UpdateAppeal;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Hearing\UpdateAppeal as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Appeal as AppealEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Update Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateAppealTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateAppeal();
        $this->mockRepo('Appeal', AppealEntity::class);
        $this->mockRepo('Cases', Cases::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'appeal_r_lic_non_pi',
            'appeal_o_dis'
        ];

        $mockAppeal = m::mock(Apppeal::class);
        $mockCase = m::mock(Cases::class)->makePartial();
        $mockCase->shouldReceive('getAppeals')
            ->andReturn(new ArrayCollection([$mockAppeal]));

        $this->references = [
            Cases::class => [
                24 => $mockCase
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                "id" => 99,
                "version" => 2,
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
                "papersSentDate" => "2015-05-05",
                "withdrawnDate" => "2015-05-05"
            ]
        );

        /** @var AppealEntity $appeal */
        $appeal = m::mock(AppealEntity::class)->makePartial();
        $appeal->setId($command->getId());

        $this->repoMap['Appeal']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($appeal)
            ->once()
            ->shouldReceive('save')
            ->with(m::type(AppealEntity::class))
            ->andReturnUsing(
                function (AppealEntity $appeal) use (&$appeal) {
                    $appeal->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Appeal updated', $result->getMessages());
    }
}
