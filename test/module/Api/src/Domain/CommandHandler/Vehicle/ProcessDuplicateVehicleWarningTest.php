<?php

/**
 * Process Duplicate Vehicle Warning Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Vehicle;

use Common\Service\File\File;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle\ProcessDuplicateVehicleWarning;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\ProcessDuplicateVehicleWarning as Cmd;

/**
 * Process Duplicate Vehicle Warning Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ProcessDuplicateVehicleWarningTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ProcessDuplicateVehicleWarning();

        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocumentGenerator::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var Vehicle $vehicle */
        $vehicle = m::mock(Vehicle::class)->makePartial();
        $vehicle->setid(333);

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->setLicence($licence);
        $licenceVehicle->setVehicle($vehicle);
        $licenceVehicle->setId(111);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licenceVehicle)
            ->shouldReceive('save')
            ->once()
            ->with($licenceVehicle);

        $storedFile = m::mock();
        $storedFile->shouldReceive('getIdentifier')
            ->andReturn(12345);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->with('GV_Duplicate_vehicle_letter', ['licence' => 222, 'vehicle' => 333])
            ->andReturn($storedFile);

        $result1 = new Result();
        $result1->addMessage('CreateDocumentSpecific');
        $data = [
            'identifier'  => 12345,
            'description' => 'Duplicate vehicle letter',
            'filename'    => 'Duplicate_vehicle_letter.rtf',
            'licence'     => 222,
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isReadOnly'  => true,
            'isExternal'  => false
        ];
        $this->expectedSideEffect(CreateDocumentSpecific::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('Enqueue');
        $data = [
            'fileIdentifier' => 12345,
            'jobName' => 'Duplicate vehicle letter'
        ];
        $this->expectedSideEffect(Enqueue::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateDocumentSpecific',
                'Enqueue',
                'Licence vehicle ID: 111 duplication letter sent'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $today = new DateTime();
        $this->assertEquals($today->format('Y-m-d'), $licenceVehicle->getWarningLetterSentDate()->format('Y-m-d'));
    }
}
