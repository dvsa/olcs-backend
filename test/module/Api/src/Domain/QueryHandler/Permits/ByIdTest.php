<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\ById as EcmtApplicationByIdHandler;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Transfer\Query\Permits\ById as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractQueryByIdHandlerTest;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;

/**
 * ById Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ByIdTest extends AbstractQueryByIdHandlerTest
{
    protected $sutClass = EcmtApplicationByIdHandler::class;
    protected $sutRepo = 'EcmtPermitApplication';
    protected $bundle = ['licence'=>['trafficArea', 'licenceType', 'organisation'],
        'sectors' => ['sectors'],
        'countrys' => ['country'],
        'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock', 'emissionsCategory']],
        'fees' => ['feeStatus', 'feeType' => ['feeType']]
    ];
    protected $qryClass = QryClass::class;
    protected $repoClass = EcmtPermitApplicationRepo::class;
    protected $entityClass = EcmtPermitApplicationEntity::class;
}
