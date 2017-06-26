<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Report;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractListQueryHandlerTest;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Report\OpenList
 */
class OpenListTest extends AbstractListQueryHandlerTest
{
    protected $sutClass = \Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Report\OpenList::class;
    protected $sutRepo = 'Cases';
}
