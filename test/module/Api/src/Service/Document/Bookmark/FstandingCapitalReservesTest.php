<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\FstandingCapitalReserves;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * FstandingCapitalReserves bookmark test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FstandingCapitalReservesTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new FstandingCapitalReserves();
    }

    public function testGetQuery()
    {
        $organisation = m::mock(Organisation::class);
        $data = [
            'organisation' => $organisation,
        ];
        $query = $this->sut->getQuery($data);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
        $this->assertSame($organisation, $query->getOrganisation());
    }

    public function testRender()
    {
        $this->sut->setData('12345');
        $this->assertEquals('£12,345.00', $this->sut->render());
    }
}
