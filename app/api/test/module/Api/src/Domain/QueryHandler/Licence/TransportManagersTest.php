<?php

/**
 * TransportManagersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\TransportManagers as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Licence\TransportManagers as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;

/**
 * TransportManagersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagersTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('TransportManagerLicence', \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1066]);

        $licence = $this->getLicence(6546);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($licence);
        $this->repoMap['TransportManagerLicence']->shouldReceive('fetchWithContactDetailsByLicence')
            ->with(6546)->once();

        $this->sut->handleQuery($query);
    }

    /**
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    protected function getLicence($id)
    {
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $status = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, $status);
        $licence->setId($id);

        return $licence;
    }
}
