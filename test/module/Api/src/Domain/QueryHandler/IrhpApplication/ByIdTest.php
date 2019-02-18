<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\ById as IrhpApplicationByIdHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ById as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractQueryByIdHandlerTest;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

/**
 * ById Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class ByIdTest extends AbstractQueryByIdHandlerTest
{
    protected $sutClass = IrhpApplicationByIdHandler::class;
    protected $sutRepo = 'IrhpApplication';
    protected $bundle = [
        'licence' => ['trafficArea', 'organisation'],
        'irhpPermitType' => ['name'],
        'fees' => ['feeType' => ['feeType'], 'feeStatus'],
        'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock' => ['country']]],
    ];
    protected $qryClass = QryClass::class;
    protected $repoClass = IrhpApplicationRepo::class;
    protected $entityClass = IrhpApplicationEntity::class;
}
