<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Dvsa\Olcs\Api\Domain\QueryPartial\WithIrhpApplicationFactory;
use Dvsa\Olcs\Api\Domain\QueryPartial\WithIrhpApplication;
use Dvsa\Olcs\Api\Domain\QueryPartial\With;
use Mockery as m;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;

class WithIrhpApplicationFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var WithIrhpApplicationFactory|null
     */
    protected $sut;

    /**
     * @test
     */
    public function createService_ReturnsInstanceOfWithIrhpApplication(): void
    {
        // Setup
        $this->setUpSut();

        // Result
        $result = $this->sut->createService($this->serviceManager);

        // Assert
        $this->assertInstanceOf(WithIrhpApplication::class, $result);
    }

    protected function setUpSut()
    {
        $this->sut = new WithIrhpApplicationFactory();
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->withQueryPartial();
    }

    /**
     * @return With
     */
    protected function withQueryPartial(): With
    {
        if (! $this->serviceManager->has('with')) {
            $instance = new With();
            $this->serviceManager->setService('with', $instance);
        }
        return $this->serviceManager->get('with');
    }
}
