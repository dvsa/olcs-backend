<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\GetList as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as Repo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * GetList Test
 */
class GetListTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpApplication';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
