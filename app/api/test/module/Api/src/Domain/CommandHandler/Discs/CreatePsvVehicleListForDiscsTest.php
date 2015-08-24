<?php

/**
 * Create PSV vehicle list for discs test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\CreatePsvVehicleListForDiscs;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator as DocGenerator;
use Dvsa\Olcs\Api\Domain\Command\Discs\CreatePsvVehicleListForDiscs as Cmd;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Create PSV vehicle list for discs test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreatePsvVehicleListForDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreatePsvVehicleListForDiscs();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocGenerator::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);
        $this->mockAuthService();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'knownValues' => 'knownValues'
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateFromTemplate')
            ->andReturn('content')
            ->once()
            ->shouldReceive('uploadGeneratedContent')
            ->with('content')
            ->andReturn(
                m::mock()
                ->shouldReceive('getIdentifier')
                ->andReturn('id1')
                ->twice()
                ->shouldReceive('getSize')
                ->andReturn(100)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();


        $fileName = (new DateTime())->format('YmdHi') . '_Psv_Vehicle_List.rtf';

        $printData = [
            'fileIdentifier' => 'id1',
            'jobName' => 'PSV Vehicle List'
        ];
        $this->expectedSideEffect(Enqueue::class, $printData, new Result());

        $createDocData = [
            'filename'      => $fileName,
            'identifier'    => 'id1',
            'size'          => 100,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'category'      => Category::CATEGORY_LICENSING,
            'subCategory'   => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'description'   => 'PSV Vehicle List',
            'isExternal'    => false,
            'isReadOnly'    => true,
            'isScan' => 0,
            'issuedDate' => null,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'licence'       => 1
        ];

        $createDocResult = new Result();
        $createDocResult->addId('doc', 1);
        $createDocResult->addMessage('message');
        $this->expectedSideEffect(CreateDocument::class, $createDocData, $createDocResult);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'doc' => 1
            ],
            'messages' => [
                'message'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    protected function mockAuthService()
    {
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }
}
