<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\GeneratePermit as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Domain\Command\Permits\GeneratePermit as Cmd;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GeneratePermitTest
 */
class GeneratePermitTest extends CommandHandlerTestCase
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
        $template = 'PERMIT_ECMT';
        $description = 'IRHP PERMIT ECMT 1';

        $command = Cmd::Create(
            [
                'ids' => [
                    $id
                ]
            ]
        );

        /** @var IrhpPermitEntity $irhpPermit */
        $irhpPermit = m::mock(IrhpPermitEntity::class);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($id, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitStock->shouldReceive('getIrhpPermitType')->andReturn($irhpPermitType);
        $irhpPermitType->shouldReceive('getName')->andReturn($template);

        $irhpPermitApplication->shouldReceive('getLicence->getId')
            ->once()
            ->andReturn($licenceId);

        $irhpPermit->shouldReceive('getId')->andReturn($id);

        $irhpPermitStock->shouldReceive('getId')->andReturn($irhpPermitStockId);

        $irhpPermitApplication
            ->shouldReceive('getLicence->getOrganisation->getId')
            ->once()
            ->andReturn($orgId);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => 'IRHP_' . $template,
                'query' => [
                    'licence' => $licenceId,
                    'irhpPermit' => $id,
                    'irhpPermitStock' => $irhpPermitStockId,
                    'organisation' => $orgId
                ],
                'knownValues' => [],
                'description' => $description,
                'category' => CategoryEntity::CATEGORY_PERMITS,
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT,
                'isExternal' => false,
                'isScan' => false
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
