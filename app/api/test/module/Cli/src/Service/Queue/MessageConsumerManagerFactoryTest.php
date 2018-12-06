<?php

/**
 * Message Consumer Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue;

use PHPUnit_Framework_TestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManagerFactory;

/**
 * Message Consumer Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MessageConsumerManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new MessageConsumerManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'message_consumer_manager' => [
                'invokables' => [
                    'foo' => '\stdClass'
                ]
            ]
        ];

        // Mocks
        $this->sm->setService('Config', $config);

        $mcm = $this->sut->createService($this->sm);

        $this->assertInstanceOf('\Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager', $mcm);
        $this->assertSame($this->sm, $mcm->getServiceLocator());
        $this->assertTrue($mcm->has('foo'));
        $this->assertFalse($mcm->has('bar'));
    }
}
