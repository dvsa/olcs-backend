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
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as LicenceCmd;
use Dvsa\Olcs\Api\Entity\System\Category;

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

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];
        $command = Cmd::create($data);

        $data = [
            'fileIdentifier' => 123,
            'jobName' => 'Goods Vehicle List'
        ];
        $result1 = new Result();
        $this->expectedSideEffect(Enqueue::class, $data, $result1);

        $data = [
            'template' => 'GVVehiclesList',
            'query' => ['licence' => 111],
            'licence' => 111,
            'description' => 'Goods Vehicle List',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal' => false,
            'isReadOnly' => true,
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
        $result2->addId('identifier', 123);
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'identifier' => 123
            ],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithTypeDp()
    {
        $data = [
            'id' => 111,
            'type' => 'dp'
        ];
        $command = Cmd::create($data);

        $data = [
            'fileIdentifier' => 123,
            'jobName' => 'New disc notification'
        ];
        $result1 = new Result();
        $this->expectedSideEffect(Enqueue::class, $data, $result1);

        $data = [
            'template' => 'GVDiscLetter',
            'query' => ['licence' => 111],
            'licence' => 111,
            'description' => 'New disc notification',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal' => false,
            'isReadOnly' => true,
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
        $result2->addId('identifier', 123);
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'identifier' => 123
            ],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
