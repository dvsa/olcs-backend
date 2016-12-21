<?php

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateVehicleListDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as Cmd;
use Dvsa\Olcs\Api\Entity\System\Category;
use ZfcRbac\Service\AuthorizationService;

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
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    /**
     * @param $isDp
     * @param $isNi
     * @param $expectedTempateId
     * @param $expectedTemplateDescription
     *
     * @dataProvider dataProviderTestHandleCommandAll
     */
    public function testHandleCommandAll($isDp, $isNi, $expectedTempateId, $expectedTemplateDescription)
    {
        $data = [
            'id' => 111,
            'user' => 1
        ];
        if ($isDp) {
            $data['type'] = 'dp';
        }

        $command = Cmd::create($data);

        $licence = m::mock();
        $licence->shouldReceive('isNi')->once()->with()->andReturn($isNi);
        $this->repoMap['Licence']->shouldReceive('fetchById')->with(111)->once()->andReturn($licence);

        $data = [
            'documentId' => 123,
            'jobName' => $expectedTemplateDescription,
            'user' => 1
        ];
        $result1 = new Result();
        $this->expectedSideEffect(Enqueue::class, $data, $result1);

        $data = [
            'template' => $expectedTempateId,
            'query' => ['licence' => 111, 'user' => 1],
            'licence' => 111,
            'description' => $expectedTemplateDescription,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal' => false,
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
        $result2->addId('document', 123);
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 123
            ],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dataProviderTestHandleCommandAll()
    {
        return [
            // is DP type, is NI, Expected description, Expected template ID,
            [true, true, 1731, 'New disc notification'],
            [true, false, 1730, 'New disc notification'],
            [false, true, 1513, 'Goods Vehicle List'],
            [false, false, 1258, 'Goods Vehicle List'],
        ];
    }
}
