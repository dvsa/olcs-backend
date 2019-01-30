<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit\GetListByLicence as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as Repo;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;

/**
 * GetList Test
 */
class GetListByLicenceTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpPermit';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
    protected $bundle = [
        'irhpPermitApplication' => [
            'irhpApplication' => [
                'licence'
            ],
            'irhpPermitWindow' => [
                'irhpPermitStock' => [
                    'country'
                ]
            ]
        ]
    ];
    protected $entityClass = Entity::class;
}
