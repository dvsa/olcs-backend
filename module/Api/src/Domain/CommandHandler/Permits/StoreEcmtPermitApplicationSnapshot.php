<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCreateSnapshotHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\EcmtAnnualGenerator;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * StoreEcmtPermitApplicationSnapshot
 */
final class StoreEcmtPermitApplicationSnapshot extends AbstractCreateSnapshotHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $generatorClass = EcmtAnnualGenerator::class;
    protected $documentCategory = Category::CATEGORY_PERMITS;
    protected $documentSubCategory = SubCategory::DOC_SUB_CATEGORY_PERMIT_APPLICATION;
    protected $documentDescription = 'Permit Application %s Snapshot (app submitted)';
    protected $documentLinkId = 'ecmtApplication';

    /**
     * @inheritDoc
     */
    protected function getDocumentDescription($entity): string
    {
        /** @var EcmtPermitApplication $entity */
        return sprintf($this->documentDescription, $entity->getApplicationRef());
    }
}
