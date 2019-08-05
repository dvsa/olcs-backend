<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit\GetListByIrhpId as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as Repo;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByIrhpId as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;

/**
 * GetListByLicence Test
 */
class GetListByIrhpIdTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpPermit';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
    protected $bundle = [
        'replaces',
        'irhpPermitRange' => [
            'irhpPermitStock' => ['country'],
            'emissionsCategory',
        ],
    ];
    protected $entityClass = Entity::class;
}
