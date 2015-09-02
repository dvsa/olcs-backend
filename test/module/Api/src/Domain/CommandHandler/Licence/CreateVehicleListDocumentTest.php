<?php

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateVehicleListDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as LicenceCmd;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use ZfcRbac\Service\AuthorizationService;
//use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateVehicleListDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateVehicleListDocument();
        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            'DocumentGenerator' => m::mock(DocumentGenerator::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];
        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];
        $command = Cmd::create($data);

        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn(123)
            ->shouldReceive('getSize')
            ->andReturn(1500);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateFromTemplate')
            ->with('GVVehiclesList', ['licence' => 111, 'user' => $mockUser])
            ->andReturn('CONTENT')
            ->shouldReceive('uploadGeneratedContent')
            ->with('CONTENT')
            ->andReturn($file);

        $data = [
            'fileIdentifier' => 123,
            'jobName' => 'Goods Vehicle List'
        ];
        $result1 = new Result();
        $this->expectedSideEffect(Enqueue::class, $data, $result1);

        $data = [
            'licence' => 111,
            'identifier' => 123,
            'description' => 'Goods Vehicle List',
            'filename' => date('YmdHi') . '_Goods_Vehicle_List.rtf',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal' => false,
            'isReadOnly' => true,
            'size' => 1500,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result2 = new Result();
        $this->expectedSideEffect(CreateDocument::class, $data, $result2);

        $this->assertSame($result2, $this->sut->handleCommand($command));
    }

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testHandleCommandWithTypeDp($niFlag, $template)
    {
        $data = [
            'id' => 111,
            'type' => 'dp'
        ];
        $command = Cmd::create($data);

        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $mockLicence = m::mock()
            ->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
            ->once()
            ->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(111)->andReturn($mockLicence)->once();

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn(123)
            ->shouldReceive('getSize')
            ->andReturn(1500);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateFromTemplate')
            ->with($template, ['licence' => 111, 'user' => $mockUser])
            ->andReturn('CONTENT')
            ->shouldReceive('uploadGeneratedContent')
            ->with('CONTENT')
            ->andReturn($file);

        $data = [
            'fileIdentifier' => 123,
            'jobName' => 'Goods Vehicle List'
        ];
        $result1 = new Result();
        $this->expectedSideEffect(Enqueue::class, $data, $result1);

        $data = [
            'licence' => 111,
            'identifier' => 123,
            'description' => 'Goods Vehicle List',
            'filename' => date('YmdHi') . '_Goods_Vehicle_List.rtf',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal' => false,
            'isReadOnly' => true,
            'size' => 1500,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result2 = new Result();
        $this->expectedSideEffect(CreateDocument::class, $data, $result2);

        $this->assertSame($result2, $this->sut->handleCommand($command));
    }

    public function licenceTypeProvider()
    {
        return [
            ['N', 'GB/GVDiscLetter'],
            ['Y', 'NI/GVDiscLetter']
        ];
    }
}
