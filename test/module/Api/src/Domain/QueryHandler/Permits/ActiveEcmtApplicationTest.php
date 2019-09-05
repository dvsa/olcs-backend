<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\ActiveEcmtApplication as ActiveEcmtApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Query\Permits\ActiveEcmtApplication as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * ActiveEcmtApplication Test
 */
class ActiveEcmtApplicationTest extends QueryHandlerTestCase
{
    protected $sutClass = ActiveEcmtApplicationHandler::class;
    protected $sutRepo = 'EcmtPermitApplication';
    protected $bundle = [
        'licence'
    ];
    protected $qryClass = QryClass::class;
    protected $repoClass = EcmtPermitApplicationRepo::class;

    /**
     * Set up test
     */
    public function setUp()
    {
        $this->sut = new $this->sutClass();
        $this->mockRepo($this->sutRepo, $this->repoClass);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        parent::setUp();
    }

    /**
     * tests handle query
     */
    public function testHandleQuery()
    {
        $licence = 1;

        $query = $this->qryClass::create(['licence' => $licence, 'year' => 2029]);

        $resultArray = [
            0 => [
                'id' => 3,
                'licence' => ['id' => $licence]
            ],
        ];

        $ecmtPermitApp = m::mock(EcmtPermitApplication::class);
        $ecmtPermitAppWrongStock = m::mock(EcmtPermitApplication::class);
        $ecmtPermitInactive = m::mock(EcmtPermitApplication::class);
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $appsByLicence = [$ecmtPermitAppWrongStock, $ecmtPermitInactive, $ecmtPermitApp];

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchByLicence')
            ->with($query->getLicence())
            ->once()
            ->andReturn($appsByLicence);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                m::type(\DateTime::class),
                Query::HYDRATE_OBJECT,
                $query->getYear()
            )
            ->once()
            ->andReturn($irhpPermitWindow);

        $ecmtPermitAppWrongStock->shouldReceive('getFirstIrhpPermitApplication->getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(12);

        $ecmtPermitInactive->shouldReceive('getFirstIrhpPermitApplication->getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(11);

        $ecmtPermitInactive->shouldReceive('isActive')
            ->withNoArgs()
            ->once()
            ->andReturn(false);

        $ecmtPermitApp->shouldReceive('getFirstIrhpPermitApplication->getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(11);

        $ecmtPermitApp->shouldReceive('isActive')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $ecmtPermitApp->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn($resultArray[0]);

        $irhpPermitWindow->shouldReceive('getIrhpPermitStock->getId')
            ->withNoArgs()
            ->times(3)
            ->andReturn(11);

        $result = $this->sut->handleQuery($query)->serialize();

        $this->assertEquals($licence, $result['licence']['id']);
    }

    public function testNull()
    {
        $licence = 1;

        $query = $this->qryClass::create(['licence' => $licence, 'year' => 3030]);


        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchByLicence')
            ->with($query->getLicence())
            ->once()
            ->andReturn([]);

        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                m::type(\DateTime::class),
                Query::HYDRATE_OBJECT,
                $query->getYear()
            )
            ->once()
            ->andReturn($irhpPermitWindow);

        $result = $this->sut->handleQuery($query);

        $this->assertNull($result);
    }
}
