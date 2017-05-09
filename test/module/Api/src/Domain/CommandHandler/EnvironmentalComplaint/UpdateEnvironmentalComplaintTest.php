<?php

/**
 * Update Environmental Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\EnvironmentalComplaint;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\EnvironmentalComplaint\UpdateEnvironmentalComplaint;
use Dvsa\Olcs\Api\Domain\Repository\Complaint;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\EnvironmentalComplaint\UpdateEnvironmentalComplaint as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;

/**
 * Update Environmental Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateEnvironmentalComplaintTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEnvironmentalComplaint();
        $this->mockRepo('Complaint', Complaint::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ComplaintEntity::COMPLAIN_STATUS_OPEN,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 99,
            'version' => 1,
            "complaintDate" => "2015-01-16",
            "description" => "Some major complaint about condition of vehicle",
            "status" => ComplaintEntity::COMPLAIN_STATUS_OPEN,
            'operatingCentres' => [101, 102],
            'complainantContactDetails' => [
                'person' => [
                    'forename' => 'David',
                    'familyName' => 'Anthony',
                ],
                'address' => [
                    'addressLine1' => 'a12',
                    'addressLine2' => 'a23',
                    'addressLine3' => 'a34',
                    'addressLine4' => 'a45',
                    'town' => 'town',
                    'postcode' => 'LS1 2AB',
                    'countryCode' => m::mock(Country::class),
                ],
            ],
        ];

        $command = Cmd::create($data);

        /** @var ContactDetailsEntity $complainantContactDetails */
        $complainantContactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $complainantContactDetails->shouldReceive('update')
            ->once()
            ->with($data['complainantContactDetails'])
            ->andReturnSelf();

        /** @var ComplaintEntity $complaint */
        $complaint = m::mock(ComplaintEntity::class)->makePartial();
        $complaint->setId($command->getId());
        $complaint->setIsCompliance(false);
        $complaint->setComplainantContactDetails($complainantContactDetails);

        $this->repoMap['Complaint']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($complaint)
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ComplaintEntity::class))
            ->andReturnUsing(
                function (ComplaintEntity $complaint) {
                    $complaint->setId(99);
                }
            )
            ->once();

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['complainantContactDetails'])
            ->andReturn($data['complainantContactDetails']);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Environmental Complaint updated', $result->getMessages());
    }
}
