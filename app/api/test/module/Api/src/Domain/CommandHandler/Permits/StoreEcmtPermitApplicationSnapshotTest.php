<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\StoreEcmtPermitApplicationSnapshot as Sut;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as Cmd;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication  as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\EcmtAnnualGenerator;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCreateSnapshotHandlerTest;
use Mockery as m;

/**
 * StoreEcmtPermitApplicationSnapshotTest
 */
class StoreEcmtPermitApplicationSnapshotTest extends AbstractCreateSnapshotHandlerTest
{
    protected $cmdClass = Cmd::class;
    protected $sutClass = Sut::class;
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $repoClass = EcmtPermitApplicationRepo::class;
    protected $entityClass = EcmtPermitApplicationEntity::class;
    protected $documentCategory = Category::CATEGORY_PERMITS;
    protected $documentSubCategory = SubCategory::DOC_SUB_CATEGORY_PERMIT_APPLICATION;
    protected $documentDescription = 'Permit Application OG9654321/3 Snapshot (app submitted)';
    protected $documentLinkId = 'ecmtPermitApplication';
    protected $generatorClass = EcmtAnnualGenerator::class;

    /**
     * Override this method in case of needing specific entity assertions i.e. for a permit application reference
     */
    protected function extraEntityAssertions(m\MockInterface $entity)
    {
        $entity->shouldReceive('getApplicationRef')->once()->withNoArgs()->andReturn('OG9654321/3');
        return $entity;
    }
}
