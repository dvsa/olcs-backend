<?php

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerTestCase extends MockeryTestCase
{
    /**
     * @var AbstractQueryHandler
     */
    protected $sut;

    /**
     * @var QueryHandlerManager
     */
    protected $queryHandler;

    /**
     * @var m\MockInterface|ServiceLocatorInterface
     */
    protected $repoManager;

    /** @var m\MockInterface[]  */
    protected $repoMap = [];

    /** @var m\MockInterface[]  */
    protected $mockedSmServices = [];

    /** @var array  */
    protected $refData = [];

    /** @var array  */
    protected $references = [];

    /** @var array  */
    protected $categoryReferences = [];

    /** @var array  */
    protected $subCategoryReferences = [];

    /** @var bool  */
    private $initRefdata = false;

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn(null);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        // if not already mocked AuthorizationService then do it
        if (!isset($this->mockedSmServices[AuthorizationService::class])) {
            $sm->shouldReceive('get')->with(AuthorizationService::class)
                ->andReturn(
                    m::mock(AuthorizationService::class)->shouldReceive('isGranted')->andReturn(false)->getMock()
                );
        }

        $this->queryHandler = m::mock(QueryHandlerManager::class);
        $this->queryHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->initReferences();

        $this->sut = $this->sut->createService($this->queryHandler);
    }

    protected function initReferences()
    {
        if (!$this->initRefdata) {
            foreach ($this->refData as $id => $mock) {
                if (is_numeric($id) && is_string($mock)) {
                    $this->refData[$mock] = m::mock(RefData::class)->makePartial()->setId($mock);
                } else {
                    $mock->makePartial();
                    $mock->setId($id);
                }
            }

            foreach ($this->categoryReferences as $id => $mock) {
                $mock->makePartial();
                $mock->setId($id);
            }

            foreach ($this->subCategoryReferences as $id => $mock) {
                $mock->makePartial();
                $mock->setId($id);
            }

            foreach ($this->references as $mocks) {
                foreach ($mocks as $id => $mock) {
                    if ($mock instanceof m\MockInterface) {
                        $mock->makePartial();
                    }

                    $mock->setId($id);
                }
            }

            $this->initRefdata = true;
        }
    }

    public function mapRefData($key)
    {
        return isset($this->refData[$key]) ? $this->refData[$key] : null;
    }

    public function mapCategoryReference($key)
    {
        return isset($this->categoryReferences[$key]) ? $this->categoryReferences[$key] : null;
    }

    public function mapSubCategoryReference($key)
    {
        return isset($this->subCategoryReferences[$key]) ? $this->subCategoryReferences[$key] : null;
    }

    public function mapReference($class, $id)
    {
        return isset($this->references[$class][$id]) ? $this->references[$class][$id] : null;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset(
            $this->sut,
            $this->queryHandler,
            $this->repoManager,
            $this->repoMap,
            $this->refData,
            $this->references,
            $this->categoryReferences,
            $this->subCategoryReferences,
            $this->initRefdata,
            $this->mockedSmServices
        );
    }

    protected function mockRepo($name, $class)
    {
        if (!$class instanceof m\MockInterface) {
            $class = m::mock($class);
        }

        //if statements here are for BC. We have some existing tests which implement this themselves
        if (!empty($this->refData)) {
            $class->shouldReceive('getRefdataReference')->andReturnUsing([$this, 'mapRefData']);
        }

        if (!empty($this->references)) {
            $class->shouldReceive('getReference')->andReturnUsing([$this, 'mapReference']);
        }

        if (!empty($this->categoryReferences)) {
            $class->shouldReceive('getCategoryReference')->andReturnUsing([$this, 'mapCategoryReference']);
        }

        if (!empty($this->subCategoryReferences)) {
            $class->shouldReceive('getSubCategoryReference')->andReturnUsing([$this, 'mapSubCategoryReference']);
        }

        $this->repoMap[$name] = $class;

        return $class;
    }
}
