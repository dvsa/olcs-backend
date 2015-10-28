<?php

/**
 * CPMS Identity Provider Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsIdentityProviderFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * CPMS Identity Provider Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CpmsIdentityProviderFactoryTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');

        parent::setUp();
    }

    private function createService($config)
    {
        $this->sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new CpmsIdentityProviderFactory();
        return $sut->createService($this->sm);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required CPMS configuration
     */
    public function testCreateServiceWithMissingCredentials()
    {
        $this->createService([]);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option cpms.client_id
     */
    public function testCreateServiceWithMissingClientId()
    {
        $this->createService(
            [
                'cpms_credentials' => [
                ]
            ]
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option cpms.client_secret
     */
    public function testCreateServiceWithMissingClientSecret()
    {
        $this->createService(
            [
                'cpms_credentials' => [
                    'client_id' => 4321
                ]
            ]
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage The logged in user must have a PID
     */
    public function testCreateServiceWithMissingUserPid()
    {
        $mockIdentity = m::mock();
        $mockIdentity->shouldReceive('getIdentity->getUser->getPid')->with()->once()->andReturn(null);
        $this->sm->shouldReceive('get')->with(\ZfcRbac\Service\AuthorizationService::class)->once()
            ->andReturn($mockIdentity);

        $this->createService(
            [
                'cpms_credentials' => [
                    'client_id' => 4321,
                    'client_secret' => 'secret'
                ]
            ]
        );
    }


    public function testCreateServiceWithValidCredentials()
    {
        $mockIdentity = m::mock();
        $mockIdentity->shouldReceive('getIdentity->getUser->getPid')->with()->once()->andReturn('XYZ');
        $this->sm->shouldReceive('get')->with(\ZfcRbac\Service\AuthorizationService::class)->once()
            ->andReturn($mockIdentity);

        $service = $this->createService(
            [
                'cpms_credentials' => [
                    'client_id' => 4321,
                    'client_secret' => 'secret'
                ]
            ]
        );

        $this->assertEquals('XYZ', $service->getUserId());
        $this->assertEquals(4321, $service->getClientId());
        $this->assertEquals('secret', $service->getClientSecret());
    }
}
