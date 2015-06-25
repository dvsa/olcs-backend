<?php

namespace Dvsa\OlcsTest\Email\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Service\ClientFactory;

/**
 * ClientFactoryTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ClientFactoryTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ClientFactory();
    }

    public function testOptionsMissing()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn([]);

        $this->setExpectedException(\RuntimeException::class);
        $this->sut->createService($sl);
    }

    public function testOptionsBaseUriMissing()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
            [
                'email' => [
                    'http' => [],
                    'client' => [],
                ]
            ]
        );

        $this->setExpectedException(\RuntimeException::class, 'Missing required option email.client.baseuri');
        $this->sut->createService($sl);
    }

    public function testOptions()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
            [
                'email' => [
                    'http' => [],
                    'client' => [
                        'baseuri' => 'http://olcs-email/',
                        'from_name' => 'OLCS do not reply',
                        'from_email' => 'donotreply@otc.gsi.gov.uk',
                        'selfserve_uri' => 'http://olcs-selfserve/',
                        'send_all_mail_to' => 'matevans@gmail.com',
                    ],
                ]
            ]
        );
        $mockTranslator = m::mock(\Zend\I18n\Translator\Translator::class);
        $sl->shouldReceive('has')->with('translator')->once()->andReturn(true);
        $sl->shouldReceive('get')->with('translator')->once()->andReturn($mockTranslator);

        $service = $this->sut->createService($sl);

        $this->assertSame('http://olcs-email', $service->getBaseUri());
        $this->assertSame('OLCS do not reply', $service->getDefaultFromName());
        $this->assertSame('donotreply@otc.gsi.gov.uk', $service->getDefaultFromEmail());
        $this->assertSame('http://olcs-selfserve', $service->getSelfServeUri());
        $this->assertSame('matevans@gmail.com', $service->getSendAllMailTo());
        $this->assertSame($mockTranslator, $service->getTranslator());
    }
}
