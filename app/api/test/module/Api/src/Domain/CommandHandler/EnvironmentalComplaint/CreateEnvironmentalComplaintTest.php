<?php

/**
 * Create Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\EnvironmentalComplaint;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\EnvironmentalComplaint\CreateEnvironmentalComplaint;
use Dvsa\Olcs\Api\Domain\Repository\Complaint;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\EnvironmentalComplaint\CreateEnvironmentalComplaint as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases;

/**
 * Create Environmental Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CreateEnvironmentalComplaintTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateEnvironmentalComplaint();
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
            "closedDate" => "2015-02-16",
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

        /** @var ComplaintEntity $app */
        $comp = null;

        $this->repoMap['Complaint']
            ->shouldReceive('save')
            ->with(m::type(ComplaintEntity::class))
            ->andReturnUsing(
                function (ComplaintEntity $Complaint) use (&$comp) {
                    $comp = $Complaint;
                    $Complaint->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Environmental Complaint created', $result->getMessages());
    }
}
