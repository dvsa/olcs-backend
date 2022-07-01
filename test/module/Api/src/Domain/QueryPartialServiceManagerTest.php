<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\QueryPartialServiceManager;
use Laminas\ServiceManager\ConfigInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * QueryPartialServiceManagerTest
 */
class QueryPartialServiceManagerTest extends MockeryTestCase
{
    /**
     * @var QueryPartialServiceManager
     */
    protected $sut;

    public function setUp(): void
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')
            ->with(m::type(QueryPartialServiceManager::class))
            ->once();

        $this->sut = new QueryPartialServiceManager($config);
    }

    public function testValidate()
    {
        $this->assertNull($this->sut->validate(null));
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $this->assertNull($this->sut->validatePlugin(null));
    }
}
