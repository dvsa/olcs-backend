<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\GeneratePermitDocuments as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Domain\Command\Permits\GeneratePermitDocuments as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GeneratePermitDocumentsTest
 */
class GeneratePermitDocumentsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 1;
        $licenceId = 20;
        $irhpPermitStockId = 3;
        $orgId = 101;
        $expected = [
            [
                'template' => EcmtPermitApplicationEntity::PERMIT_TEMPLATE_NAME,
                'query' => [
                    'licence' => $licenceId,
                    'irhpPermit' => $id,
                    'irhpPermitStock' => $irhpPermitStockId,
                    'organisation' => $orgId
                ],
                'description' => 'IRHP PERMIT ECMT 1',
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT
            ],
            [
                'template' => EcmtPermitApplicationEntity::PERMIT_COVERING_LETTER_TEMPLATE_NAME,
                'query' => [
                    'licence' => $licenceId,
                    'irhpPermit' => $id,
                ],
                'description' => 'IRHP PERMIT ECMT COVERING LETTER 1',
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT_COVERING_LETTER
            ]
        ];

        $command = Cmd::Create(
            [
                'ids' => [
                    $id
                ]
            ]
        );

        $irhpPermit = m::mock(IrhpPermitEntity::class);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($id, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);

        $irhpPermitType = EcmtPermitApplicationEntity::PERMIT_TYPE;
        $irhpPermitStock->shouldReceive('getIrhpPermitType->getName')->andReturn($irhpPermitType);

        $irhpPermitApplication->shouldReceive('getEcmtPermitApplication->getLicence->getId')
            ->andReturn($licenceId);

        $irhpPermit->shouldReceive('getId')->andReturn($id);

        $irhpPermitStock->shouldReceive('getId')->andReturn($irhpPermitStockId);

        $irhpPermitApplication
            ->shouldReceive('getEcmtPermitApplication->getLicence->getOrganisation->getId')
            ->andReturn($orgId);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => $expected[0]['template'],
                'query' => $expected[0]['query'],
                'knownValues' => [],
                'description' => $expected[0]['description'],
                'category' => CategoryEntity::CATEGORY_PERMITS,
                'subCategory' => $expected[0]['subCategory'],
                'isExternal' => false,
                'isScan' => false
            ],
            (new Result())->addId('document', 100)
        );

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => $expected[1]['template'],
                'query' => $expected[1]['query'],
                'knownValues' => [],
                'description' => $expected[1]['description'],
                'category' => CategoryEntity::CATEGORY_PERMITS,
                'subCategory' => $expected[1]['subCategory'],
                'isExternal' => false,
                'isScan' => false
            ],
            (new Result())->addId('document', 101)
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permit' => 100,
                'coveringLetter' => 101
            ],
            'messages' => [
                'IRHP PERMIT ECMT 1 RTF created and stored',
                'IRHP PERMIT ECMT COVERING LETTER 1 RTF created and stored'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
