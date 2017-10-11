<?php

/**
 * Create Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Complaint;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Complaint\CreateComplaint;
use Dvsa\Olcs\Api\Domain\Repository\Complaint;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Complaint\CreateComplaint as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases;

/**
 * Create Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CreateComplaintTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateComplaint();
        $this->mockRepo('Complaint', Complaint::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'ct_complainant',
            'ct_cov',
            'cs_ack'
        ];

        $this->references = [
            Cases::class => [
                24 => m::mock(Cases::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
            'case' => 24,
            "closedDate" => '2017-10-03',
            "complainantFamilyName" => "Anthony",
            "complainantForename" => "David",
            "complaintDate" => "2015-01-16",
            "complaintType" => "ct_cov",
            "createdBy" => null,
            "description" => "Some major complaint about condition of vehicle",
            "driverFamilyName" => "Driver L Smith",
            "driverForename" => "Driver F John",
            "status" => "cs_ack",
            "vrm" => "VRM123T"
            ]
        );

        $this->repoMap['Complaint']
            ->shouldReceive('save')
            ->with(m::type(ComplaintEntity::class))
            ->once()
            ->andReturnUsing(
                function (ComplaintEntity $complaint) {
                    $complaint->setId(99);
                    $this->assertSame(24, $complaint->getCase()->getId());
                    $this->assertSame(true, $complaint->getIsCompliance());
                    $this->assertSame('cs_ack', (string) $complaint->getStatus());
                    $this->assertEquals(new DateTime('2015-01-16'), $complaint->getComplaintDate());
                    $this->assertEquals(new DateTime('2017-10-03'), $complaint->getClosedDate());
                    $this->assertSame('Some major complaint about condition of vehicle', $complaint->getDescription());
                    $this->assertSame('Driver L Smith', $complaint->getDriverFamilyName());
                    $this->assertSame('Driver F John', $complaint->getDriverForename());
                    $this->assertSame('VRM123T', $complaint->getVrm());
                    $this->assertSame('David', $complaint->getComplainantContactDetails()->getPerson()->getForename());
                    $this->assertSame(
                        'Anthony',
                        $complaint->getComplainantContactDetails()->getPerson()->getFamilyName()
                    );
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Complaint created', $result->getMessages());
    }
}
