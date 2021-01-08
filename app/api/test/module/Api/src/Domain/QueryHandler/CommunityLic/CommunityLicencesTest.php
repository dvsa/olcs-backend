<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CommunityLic;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic\CommunityLicences as CommunityLicencesQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\CommandHandlerManagerMockBuilder;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLicences as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandlerManagerMockBuilder;
use Dvsa\OlcsTest\Api\Domain\Repository\CommunityLicenceRepositoryMockBuilder;
use Dvsa\OlcsTest\Api\Domain\Repository\LicenceRepositoryMockBuilder;
use Dvsa\OlcsTest\Api\Domain\Repository\RepositoryServiceManagerBuilder;
use Dvsa\OlcsTest\Api\Domain\Repository\ResolvesMockRepositoriesFromServiceLocatorsTrait;
use Dvsa\OlcsTest\Builder\AuthorizationServiceMockBuilder;
use Dvsa\OlcsTest\Builder\ServiceManagerBuilder;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Doctrine\ORM\Query;
use ZfcRbac\Service\AuthorizationService;

/**
 * CommunityLic Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicencesTest extends QueryHandlerTestCase
{
    use ResolvesMockRepositoriesFromServiceLocatorsTrait;

    public function setUp(): void
    {
        $this->sut = new CommunityLicencesQueryHandler();
    }

    /**
     * @return array
     */
    protected function setUpDefaultRepositories(): array
    {
        return [
            CommunityLicenceRepositoryMockBuilder::ALIAS => (new CommunityLicenceRepositoryMockBuilder())->build(),
            LicenceRepositoryMockBuilder::ALIAS => (new LicenceRepositoryMockBuilder())->build(),
        ];
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    public function setUpDefaultServices(ServiceLocatorInterface $serviceLocator): array
    {
        return [
            RepositoryServiceManagerBuilder::ALIAS => (new RepositoryServiceManagerBuilder(static::setUpDefaultRepositories()))->build(),
            AuthorizationService::class => (new AuthorizationServiceMockBuilder())->build(),
            CommandHandlerManagerMockBuilder::ALIAS => (new CommandHandlerManagerMockBuilder($serviceLocator))->build(),
            QueryHandlerManager::class => (new QueryHandlerManagerMockBuilder($serviceLocator))->build(),
        ];
    }

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
        $serviceManager = (new ServiceManagerBuilder([$this, 'setUpDefaultServices']))->build();
        $query = Qry::create(['licence' => 1]);
        $queryHandler = $this->initializeQueryHandler($this->sut, $serviceManager);

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
        $serviceManager = (new ServiceManagerBuilder([$this, 'setUpDefaultServices']))->build();
        $query = Qry::create(['licence' => 1]);
        $queryHandler = $this->initializeQueryHandler($this->sut, $serviceManager);
        $communityLicenceRepository = $this->resolveMockRepository($serviceManager, CommunityLicenceRepositoryMockBuilder::ALIAS);
        $expectedCount = 1234;
        $communityLicenceRepository->shouldReceive('countActiveByLicenceId')->andReturn($expectedCount);

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
}
