<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\StoreEcmtPermitApplicationSnapshot;
use Dvsa\Olcs\Transfer\Command\Permits\StoreEcmtPermitApplicationSnapshot as Cmd;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\Licence;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * StoreEcmtPermitApplicationSnapshotTest
 */
class StoreEcmtPermitApplicationSnapshotTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StoreEcmtPermitApplicationSnapshot();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 3,
            'html' => 'HTML',
        ];
        $command = Cmd::create($data);

        /** @var EcmtPermitApplication $ecmtPermitApplication */
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getId')->andReturn('703');

        $ecmtPermitApplication->shouldReceive('getId')->andReturn('3');
        $ecmtPermitApplication->shouldReceive('getApplicationRef')->andReturn('OG9654321 / 3');
        $ecmtPermitApplication->shouldReceive('getLicence')->andReturn($licence);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($ecmtPermitApplication);

        $params = [
            'content' => 'SFRNTA==', // 'HTML' base 64 encoded
            'category' => Category::CATEGORY_PERMITS,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_PERMIT_APPLICATION,
            'isExternal' => false,
            'isScan' => false,
            'filename' => 'Permit Application OG9654321 / 3 Snapshot (app submitted).html',
            'description' => 'Permit Application OG9654321 / 3 Snapshot (app submitted)',
            'licence' => 703,
            'ecmtApplication' => 3,
        ];
        $this->expectedSideEffect(Upload::class, $params, new Result());

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'EcmtPermitApplication' => 3,
            ],
            'messages' => [
                'ECMT Permit Application snapshot created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
