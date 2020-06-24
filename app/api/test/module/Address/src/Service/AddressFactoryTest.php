<?php

/**
 * Address Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Address\Service;

use Dvsa\Olcs\Address\Service\Address;
use Dvsa\Olcs\Address\Service\AddressFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Address Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressFactoryTest extends MockeryTestCase
{
    /**
     * @var AddressFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new AddressFactory();
    }

    public function testCreateServiceWithoutConfig()
    {
        $this->expectException(\RuntimeException::class);

        $config = [];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $this->sut->createService($sm);
    }

    public function testCreateService()
    {
        $config = [
            'address' => [
                'client' => [
                    'baseuri' => 'foo'
                ]
            ]
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $address = $this->sut->createService($sm);

        $this->assertInstanceOf(Address::class, $address);
    }
}
