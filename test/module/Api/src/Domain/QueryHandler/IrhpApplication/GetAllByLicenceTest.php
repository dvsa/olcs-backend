<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\GetAllByLicence as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplicationView as Repo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\GetAllByLicence as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * GetAllByLicence Test
 */
class GetAllByLicenceTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpApplicationView';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
