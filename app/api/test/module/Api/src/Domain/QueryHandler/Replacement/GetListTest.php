<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Replacement;

use Dvsa\Olcs\Api\Domain\QueryHandler\Replacement\GetList as Handler;
use Dvsa\Olcs\Api\Domain\Repository\Replacement as Repo;
use Dvsa\Olcs\Transfer\Query\Replacement\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * GetList Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class GetListTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = Handler::class;
    protected $sutRepo = 'Replacement';
    protected $qryClass = Query::class;
    protected $repoClass = Repo::class;
}
