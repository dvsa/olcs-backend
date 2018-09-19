<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\NextIrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class NextIrhpPermitStockTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new NextIrhpPermitStock();

        parent::setUp();
    }

    public function testHandleQuery()
    {

    }
}
