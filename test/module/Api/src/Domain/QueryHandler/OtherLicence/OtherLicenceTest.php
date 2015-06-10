<?php

/**
 * Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\OtherLicence;

use Dvsa\Olcs\Api\Domain\QueryHandler\OtherLicence\OtherLicence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\OtherLicence\OtherLicence as Qry;
use Dvsa\Olcs\Api\Domain\Repository\OtherLicence as OtherLicenceRepo;
use Doctrine\ORM\Query;

/**
 * Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OtherLicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OtherLicence();
        $this->mockRepo('OtherLicence', OtherLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['OtherLicence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn('otherLicence')
            ->once();

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, 'otherLicence');
    }
}
