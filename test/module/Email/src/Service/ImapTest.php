<?php

namespace Dvsa\OlcsTest\Email\Service;

use Dvsa\Olcs\Email\Service\Imap as ImapService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * ImapTest
 */
class ImapTest extends TestCase
{
    private $imapService;

    protected function setUp()
    {
        $this->imapService = new ImapService();
    }

    public function testCreateService()
    {
        $config = [
            'mailboxes' => [
                'foo' => [
                    'bar' => 'baz',
                ],
            ]
        ];

        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('Config', $config);

        $this->assertSame(
            $this->imapService,
            $this->imapService->createService($sl)
        );

        $this->assertEquals(
            [
                'foo' => [
                    'bar' => 'baz',
                ]
            ],
            $this->imapService->getConfig()
        );
    }

    public function testCreateServiceThrowsException()
    {
        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('Config', []);

        $this->expectException('Zend\Mail\Exception\RuntimeException', 'No mailbox config found');

        $this->imapService->createService($sl);
    }

    public function testConnectThrowsException()
    {
        $config = [
            'mailboxes' => [
                'foo' => [
                    'bar' => 'baz',
                ],
            ]
        ];

        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('Config', $config);

        $this->assertSame(
            $this->imapService,
            $this->imapService->createService($sl)
        );

        $this->imapService->createService($sl);

        $this->expectException('Zend\Mail\Exception\RuntimeException', 'No config found for mailbox \'bar\'');

        $this->imapService->connect('bar');
    }

    public function testGetSetStore()
    {
        $store = m::mock('\Zend\Mail\Storage\Imap');
        $store->shouldReceive('close'); // called by destructor

        $this->imapService->setStore($store);

        $this->assertSame(
            $store,
            $this->imapService->getStore()
        );
    }

    public function testGetMessage()
    {
        $id = 432432;
        $number = 99;

        $store = m::mock('\Zend\Mail\Storage\Imap');
        $message = m::mock();

        $store
            ->shouldReceive('getNumberByUniqueId')
            ->with($id)
            ->once()
            ->andReturn($number)
            ->shouldReceive('getMessage')
            ->with($number)
            ->once()
            ->andReturn($message)
            ->shouldReceive('close');

        $message->subject = 'SUBJECT';

        $message
            ->shouldReceive('getContent')
            ->once()
            ->andReturn('CONTENT');

        $this->imapService->setStore($store);

        $result = $this->imapService->getMessage($id);

        $this->assertEquals(
            [
                'number'   => $number,
                'uniqueId' => $id,
                'subject'  => 'SUBJECT',
                'content'  => 'CONTENT',
            ],
            $result
        );
    }

    public function testGetMessageWithExceptionFromStore()
    {
        $id = 99;

        $store = m::mock('\Zend\Mail\Storage\Imap');

        $store
            ->shouldReceive('getNumberByUniqueId')
            ->with($id)
            ->once()
            ->andThrow(new \Zend\Mail\Exception\RuntimeException('error'))
            ->shouldReceive('close'); // called by destructor!

        $this->imapService->setStore($store);

        $result = $this->imapService->getMessage($id);

        $this->assertEquals(
            'error',
            $result
        );
    }

    public function testGetMessages()
    {
        $store = m::mock('\Zend\Mail\Storage\Imap');

        $store
            ->shouldReceive('countMessages')
            ->once()
            ->andReturn(3)
            ->shouldReceive('getUniqueId')
            ->times(3)
            ->andReturn(2342, 4353, 5646)
            ->shouldReceive('close');

        $this->imapService->setStore($store);

        $result = $this->imapService->getMessages();

        $this->assertEquals(
            [
                // note indexed from 1
                1 => 2342,
                2 => 4353,
                3 => 5646,
            ],
            $result
        );
    }

    public function testGetMessagesWithExceptionFromStore()
    {
        $store = m::mock('\Zend\Mail\Storage\Imap');

        $store
            ->shouldReceive('countMessages')
            ->once()
            ->andThrow(new \Zend\Mail\Exception\RuntimeException('error'))
            ->shouldReceive('close'); // called by destructor!

        $this->imapService->setStore($store);

        $result = $this->imapService->getMessages();

        $this->assertEquals(
            'error',
            $result
        );
    }

    public function testRemoveMessage()
    {
        $id = 432432;
        $number = 99;

        $store = m::mock('\Zend\Mail\Storage\Imap');

        $store
            ->shouldReceive('getNumberByUniqueId')
            ->with($id)
            ->once()
            ->andReturn($number)
            ->shouldReceive('removeMessage')
            ->with($number)
            ->once()
            ->shouldReceive('close');

        $this->imapService->setStore($store);

        $this->assertNull($this->imapService->removeMessage($id));
    }

    public function testRemoveMessageThrowsException()
    {
        $id = 432432;

        $store = m::mock('\Zend\Mail\Storage\Imap');

        $store
            ->shouldReceive('getNumberByUniqueId')
            ->with($id)
            ->once()
            ->andThrow(new \Zend\Mail\Exception\RuntimeException('error'))
            ->shouldReceive('close');

        $this->imapService->setStore($store);

        $this->assertEquals('error', $this->imapService->removeMessage($id));
    }
}
