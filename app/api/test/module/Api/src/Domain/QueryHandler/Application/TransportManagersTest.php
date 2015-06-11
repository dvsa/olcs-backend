<?php

/**
 * TransportManagersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\TransportManagers as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Application\TransportManagers as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as TransportManagerApplicationRepo;

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
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('TransportManagerApplication', TransportManagerApplicationRepo::class);
        $this->mockRepo('TransportManagerLicence', \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1066]);

        $application = $this->getApplication();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($application);
        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchWithContactDetailsByApplication')
            ->with(1066)->once();
        $this->repoMap['TransportManagerLicence']->shouldReceive('fetchWithContactDetailsByLicence')
            ->with(624)->once();

        /* @var $result \Dvsa\Olcs\Api\Domain\QueryHandler\Result */
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

    /**
     * @return \Dvsa\Olcs\Api\Entity\Application\Application
     */
    protected function getApplication($licence = null)
    {
        if ($licence === null) {
            $licence = $this->getLicence(624);
        }

        $status = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, $status, false);
        $application->setId(1066);

        return $application;
    }
}
