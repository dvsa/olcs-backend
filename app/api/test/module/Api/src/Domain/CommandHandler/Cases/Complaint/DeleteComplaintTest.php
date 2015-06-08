<?php

/**
 * Delete Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Complaint;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Complaint\DeleteComplaint;
use Dvsa\Olcs\Api\Domain\Repository\Complaint;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Complaint\DeleteComplaint as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Delete Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class DeleteComplaintTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteComplaint();
        $this->mockRepo('Complaint', Complaint::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'ct_complainant'
        ];

        $this->references = [
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            [
                'id' => 99,
                'version' => 1
            ]
        );

        /** @var PersonEntity $person */
        $person = m::mock(PersonEntity::class)->makePartial();
        $person->setId(44);

        /** @var ContactDetailsEntity $complainantContactDetails */
        $complainantContactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $complainantContactDetails->setId(33);
        $complainantContactDetails->setPerson($person);

        /** @var ComplaintEntity $complaint */
        $complaint = m::mock(ComplaintEntity::class)->makePartial();
        $complaint->setId($command->getId());
        $complaint->setComplainantContactDetails($complainantContactDetails);

        $this->repoMap['Complaint']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($complaint)
            ->once()
            ->shouldReceive('delete')
            ->with(m::type(ComplaintEntity::class))
            ->andReturnUsing(
                function (ComplaintEntity $complaint) use (&$imp) {
                    $imp = $complaint;
                    $complaint->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Complaint deleted', $result->getMessages());
    }
}
