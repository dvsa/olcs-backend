<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\FeatureToggle;

use Dvsa\Olcs\Api\Domain\QueryHandler\FeatureToggle\GetList as ToggleListHandler;
use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\Olcs\Transfer\Query\FeatureToggle\GetList as ToggleListQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * GetList Test
 */
class GetListTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = ToggleListHandler::class;
    protected $sutRepo = 'FeatureToggle';
    protected $qryClass = ToggleListQry::class;
    protected $repoClass = FeatureToggleRepo::class;
}
