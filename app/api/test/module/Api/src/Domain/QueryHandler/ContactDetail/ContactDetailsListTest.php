<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContactDetail;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContactDetail\ContactDetailsList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as Repo;
use Dvsa\Olcs\Transfer\Query\ContactDetail\ContactDetailsList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;

/**
 * Get country list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ContactDetailsListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('ContactDetails', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $contactDetails = m::mock(ContactDetailsEntity::class);
        $contactDetails->shouldReceive('serialize')->once()->andReturn('SERIALIZED');

        $this->repoMap['ContactDetails']->shouldReceive('fetchList')
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)->andReturn([$contactDetails]);
        $this->repoMap['ContactDetails']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
