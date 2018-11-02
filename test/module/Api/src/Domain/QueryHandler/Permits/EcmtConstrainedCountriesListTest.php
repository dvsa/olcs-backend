<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\EcmtConstrainedCountriesList as Handler;
use Dvsa\Olcs\Api\Domain\Repository\Country as Repo;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * EcmtConstrainedCountriesList Test
 */
class EcmtConstrainedCountriesListTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'Country';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
