<?php

namespace OlcsTest\Db\Service\Search;

use Dvsa\Olcs\Api\Domain\Repository\SystemParameter;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\OlcsTest\MocksRepositoriesTrait;
use Elastica\Client as ElasticaClient;
use Elasticsearch\Client;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Olcs\Db\Service\Search\Search;
use Olcs\Db\Service\Search\SearchFactory;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Class SearchFactoryTest
 * @see SearchFactory
 */
class SearchFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksRepositoriesTrait;

    /**
     * @var SearchFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @test
     */
    public function createService_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     * @depends __invoke_IsCallable
     */
    public function createService_CallsInvoke()
    {
        // Setup
        $this->sut = m::mock(SearchFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(null, $requestedName, 'Expected requestedName to be NULL');
            return true;
        });

        // Execute
        $this->sut->createService($this->serviceManager());
    }

    /**
     * @test
     */
    public function __invoke_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfSearchFactory()
    {
        // Setup
        $this->setUpSut();

        //Expectations
        $repositoryServiceManager = $this->repositoryServiceManager();
        $repositoryServiceManager->expects('get')->with('SystemParameter')->andReturn(m::mock(SystemParameter::class));

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(Search::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new SearchFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->repositoryServiceManager();
        $serviceManager->setService(Client::class, m::mock(ElasticaClient::class));
        $serviceManager->setService(AuthorizationService::class, m::mock(AuthorizationService::class));
    }

    private function repositoryServiceManager()
    {
        if (!$this->serviceManager->has('RepositoryServiceManager')) {
            $instance = $this->setUpMockService(RepositoryServiceManager::class);
            $this->serviceManager->setService('RepositoryServiceManager', $instance);
        }
        $instance = $this->serviceManager->get('RepositoryServiceManager');

        return $instance;
    }
}
