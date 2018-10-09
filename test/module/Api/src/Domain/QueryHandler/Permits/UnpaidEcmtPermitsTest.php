<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\UnpaidEcmtPermits as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as Repo;
use Dvsa\Olcs\Transfer\Query\Permits\UnpaidEcmtPermits as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * UnpaidEcmtPermits Test
 */
class UnpaidEcmtPermitsTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpCandidatePermit';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
