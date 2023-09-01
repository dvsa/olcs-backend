<?php

/**
 * Message Consumer Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue;

use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManagerFactory;

/**
 * Message Consumer Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MessageConsumerManagerFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new MessageConsumerManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testInvoke()
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

        $mcm = $this->sut->__invoke($this->sm, MessageConsumerManager::class);

        $this->assertInstanceOf(MessageConsumerManager::class, $mcm);
        $this->assertTrue($mcm->has('foo'));
        $this->assertFalse($mcm->has('bar'));
    }
}
