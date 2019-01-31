<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\ActiveApplication as ActiveApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ActiveApplication as QryClass;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * ActiveApplication Test
 */
class ActiveApplicationTest extends QueryHandlerTestCase
{
    protected $sutClass = ActiveApplicationHandler::class;
    protected $sutRepo = 'IrhpApplication';
    protected $bundle = [
        'licence',
        'irhpPermitType',
    ];
    protected $qryClass = QryClass::class;
    protected $repoClass = IrhpApplicationRepo::class;
    protected $entityClass = IrhpApplicationEntity::class;

    /**
     * Set up test
     */
    public function setUp()
    {
        $this->sut = new $this->sutClass();
        $this->mockRepo($this->sutRepo, $this->repoClass);
        parent::setUp();
    }

    /**
     * tests handle query
     */
    public function testHandleQuery()
    {
        $licence = 1;
        $irhpPermitType = 2;

        $query = $this->qryClass::create(['licence' => $licence, 'irhpPermitType' => $irhpPermitType]);

        $resultArray = [
            0 => [
                'id' => 3,
                'licence' => ['id' => $licence],
                'irhpPermitType' => ['id' => $irhpPermitType]
            ],
        ];

        $mockEntity = m::mock($this->entityClass)
            ->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn($resultArray[0])->getMock();

        $mockEntity->shouldReceive('getIrhpPermitType->getId')->andReturn(2);

        $mockEntity->shouldReceive('isActive')->andReturn(true);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchByLicence')
            ->with($licence)
            ->once()
            ->andReturn([$mockEntity]);

        $result = $this->sut->handleQuery($query)->serialize();

        $this->assertEquals($irhpPermitType, $result['irhpPermitType']['id']);
        $this->assertEquals($licence, $result['licence']['id']);
    }

    public function testNull() {
        $licence = 1;
        $irhpPermitType = 2;

        $query = $this->qryClass::create(['licence' => $licence, 'irhpPermitType' => $irhpPermitType]);

        $resultArray = [];

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchByLicence')
            ->with($licence)
            ->once()
            ->andReturn($resultArray);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(null, $result);
    }

    public function testDifferentType()
    {
        $licence = 1;
        $irhpPermitType = 2;

        $query = $this->qryClass::create(['licence' => $licence, 'irhpPermitType' => $irhpPermitType]);

        $mockEntity = m::mock($this->entityClass);

        $mockEntity->shouldReceive('getIrhpPermitType->getId')->andReturn(3);

        $mockEntity->shouldReceive('isActive')->andReturn(true);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchByLicence')
            ->with($licence)
            ->once()
            ->andReturn([$mockEntity]);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(null, $result);
    }
}
