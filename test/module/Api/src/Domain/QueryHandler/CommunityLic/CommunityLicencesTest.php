<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CommunityLic;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic\CommunityLicences as CommunityLicencesQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\CommandHandlerManagerMockBuilder;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLicences as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandlerManagerMockBuilder;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Doctrine\ORM\Query;
use ZfcRbac\Service\AuthorizationService;
use Mockery\MockInterface;
use Dvsa\OlcsTest\MocksRepositoriesTrait;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Dvsa\OlcsTest\Api\Domain\Repository\MocksLicenceRepositoryTrait;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicenceEntity;
use Dvsa\OlcsTest\Api\Entity\Licence\LicenceBuilder;
use Dvsa\Olcs\Api\Entity\System\RefData;

class CommunityLicencesTest extends QueryHandlerTestCase
{
    use MocksRepositoriesTrait;
    use MocksServicesTrait;
    use MocksLicenceRepositoryTrait;

    public function testHandleQueryIsDefined()
    {
        // Assert
        $this->assertIsCallable([$this->sut, 'handleQuery']);
    }

    /**
     * @depends testHandleQueryIsDefined
     */
    public function testHandleQueryReturnsAnArray()
    {
        // SetUp
        $licence = LicenceBuilder::aLicence()->build();
        $this->injectEntities($licence);
        $query = Qry::create(['licence' => $licence->getId()]);
        $queryHandler = $this->initializeQueryHandler($this->sut, $this->serviceManager());

        // Execute
        $result = $queryHandler->handleQuery($query);

        // Assert
        $this->assertIsArray($result);

        return $result;
    }

    /**
     * @depends testHandleQueryReturnsAnArray
     * @param array $result
     */
    public function testHandleQueryReturnsAnArrayWithTheTotActiveCommunityLicencesKey(array $result)
    {
        $this->assertArrayHasKey('totActiveCommunityLicences', $result);
    }

    /**
     * @depends testHandleQueryReturnsAnArrayWithTheTotActiveCommunityLicencesKey
     */
    public function testHandleQueryReturnsAnArrayWithTheTotActiveCommunityLicencesValue()
    {
        // SetUp
        $licence = LicenceBuilder::aLicence()->build();
        $this->injectEntities($licence);
        $query = Qry::create(['licence' => $licence->getId()]);
        $queryHandler = $this->initializeQueryHandler($this->sut, $this->serviceManager());
        $expectedCount = 1234;
        $this->communityLicenceRepository()->shouldReceive('countActiveByLicenceId')->andReturn($expectedCount);

        // Execute
        $result = $queryHandler->handleQuery($query);

        // Assert
        $this->assertEquals($expectedCount, $result['totActiveCommunityLicences']);
    }

    /**
     * @depends testHandleQueryIsDefined
     */
    public function testHandleQuery()
    {
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        parent::setUp();

        $licenceId = 1;
        $query = Qry::create(['licence' => $licenceId]);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getTotCommunityLicences')
            ->andReturn(2)
            ->once()
            ->getMock()
            ->shouldIgnoreMissing();

        $mockLicence->shouldReceive('getId')->andReturn(1);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $mockOfficeCopy = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class)->shouldReceive('serialize')->andReturn(['item'])->getMock();
        $mockComLic = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn('result')
            ->getMock();
        $comLics = new ArrayCollection();
        $comLics->add($mockComLic);

        $this->repoMap['CommunityLic']->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn($mockOfficeCopy)
            ->once()
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($comLics)
            ->once()
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(15)
            ->shouldReceive('hasRows')
            ->with(m::type(Qry::class))
            ->andReturn(1)
            ->once()
            ->getMock()
            ->shouldIgnoreMissing();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => ['result'],
            'count' =>  15,
            'count-unfiltered' => 1,
            'totCommunityLicences' => 2,
            'totActiveCommunityLicences' => 0,
            'officeCopy' => ['item']
        ];

        $this->assertEquals($result, $expected);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
        $this->sut = new CommunityLicencesQueryHandler();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    public function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->setUpQueryHandler($serviceManager);
        $this->repositoryServiceManager();
        $this->licenceRepository();
        $this->communityLicenceRepository();
        $serviceManager->setService(AuthorizationService::class, $this->setUpAuthorizationService());
        $serviceManager->setService(CommandHandlerManagerMockBuilder::ALIAS, (new CommandHandlerManagerMockBuilder($serviceManager))->build());
    }

    /**
     * @return MockInterface|CommunityLicRepo
     */
    protected function communityLicenceRepository(): MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('CommunityLic')) {
            $instance = $this->setUpMockService(CommunityLicRepo::class);
            $instance->allows('fetchOfficeCopy')->andReturnUsing(function (int $id) {
                $entity = new CommunityLicenceEntity();
                $entity->setStatus(new RefData(CommunityLicenceEntity::STATUS_ACTIVE));
                $entity->setId($id);
                return $entity;
            })->byDefault();
            $instance->allows('fetchList')->andReturn([])->byDefault();
            $instance->allows('fetchCount')->andReturn(0)->byDefault();
            $instance->allows('hasRows')->andReturn(true)->byDefault();
            $instance->allows('countActiveByLicenceId')->andReturn(0)->byDefault();
            $repositoryServiceManager->setService('CommunityLic', $instance);
        }
        return $repositoryServiceManager->get('CommunityLic');
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpQueryHandler(ServiceManager $serviceManager)
    {
        $queryHandlerBuilder = new QueryHandlerManagerMockBuilder();
        $queryHandler = $queryHandlerBuilder->build($serviceManager);
        $queryHandlerBuilder->register($queryHandler);
    }

    /**
     * @return MockInterface|AuthorizationService
     */
    protected function setUpAuthorizationService(): MockInterface
    {
        $service = m::mock(AuthorizationService::class);
        $service->shouldReceive('isGranted')->andReturn(false)->byDefault();
        return $service;
    }
}
