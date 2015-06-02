<?php

/**
 * Result Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command;

use Dvsa\Olcs\Api\Domain\Command\Result;
use PHPUnit_Framework_TestCase;

/**
 * Result Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResultTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Result();
    }

    public function testResultId()
    {
        $this->sut->addId('application', 111);
        $this->sut->addId('licence', 333);
        $this->sut->addId('application', 222);

        $this->assertEquals(333, $this->sut->getId('licence'));
        $this->assertEquals(222, $this->sut->getId('application'));

        $this->assertEquals(['application' => 222, 'licence' => 333], $this->sut->getIds());
    }

    public function testResultMessage()
    {
        $this->sut->addMessage('foo');
        $this->sut->addMessage('bar');

        $this->assertEquals(['foo', 'bar'], $this->sut->getMessages());
    }

    public function testToArray()
    {
        $this->sut->addId('foo', 111);
        $this->sut->addId('bar', 222);
        $this->sut->addMessage('foo was successful');
        $this->sut->addMessage('bar failed');

        $expected = [
            'id' => ['foo' => 111, 'bar' => 222],
            'messages' => ['foo was successful', 'bar failed']
        ];

        $this->assertEquals($expected, $this->sut->toArray());
    }

    public function testMerge()
    {
        $this->sut->addId('foo', 111);
        $this->sut->addId('bar', 222);
        $this->sut->addMessage('foo was successful');
        $this->sut->addMessage('bar failed');

        $result = new Result();
        $result->addId('foo', 333);
        $result->addMessage('foo was updated');

        $this->sut->merge($result);

        $expected = [
            'id' => ['foo' => 333, 'bar' => 222],
            'messages' => ['foo was successful', 'bar failed', 'foo was updated']
        ];

        $this->assertEquals($expected, $this->sut->toArray());
    }
}
