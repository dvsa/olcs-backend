<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\GetAllByOrganisation as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplicationView as Repo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\GetAllByOrganisation as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * GetAllByOrganisation Test
 */
class GetAllByOrganisationTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'IrhpApplicationView';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
