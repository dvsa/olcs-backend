<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\MeanDeviation;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class MeanDeviationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MeanDeviation();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licence1 = m::mock(Licence::class);
        $licence2 = m::mock(Licence::class);
        $licence1->shouldReceive('getId')->andReturn(1);
        $licence2->shouldReceive('getId')->andReturn(2);
        $licences = [$licence1, $licence2];

        $ecmtApp1 =  m::mock(EcmtPermitApplication::class);
        $ecmtApp2 =  m::mock(EcmtPermitApplication::class);
        $ecmtApp1->shouldReceive('getPermitsRequired')->andReturn(2);
        $ecmtApp2->shouldReceive('getPermitsRequired')->andReturn(4);

        $this->repoMap['Licence']->shouldReceive('getLicences')
            ->andReturn($licences);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchByLicenceId')
            ->with($licence1->getId())
            ->andReturn($ecmtApp1);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchByLicenceId')
            ->with($licence2->getId())
            ->andReturn($ecmtApp2);

        $query = m::mock(QueryInterface::class);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(3, $result);
        /*$this->assertTrue(isset($result['fee']));
        $this->assertCount(2, $result['fee']);

        $resultKeys = array_keys($result['fee']);
        $this->assertEquals($feeProductReference1, $resultKeys[0]);
        $this->assertEquals($feeProductReference2, $resultKeys[1]);

        $this->assertSame($feeType1, $result['fee'][$feeProductReference1]);
        $this->assertSame($feeType2, $result['fee'][$feeProductReference2]);*/
    }
}
