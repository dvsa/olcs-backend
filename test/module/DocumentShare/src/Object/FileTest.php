<?php

namespace Dvsa\OlcsTest\DocumentShare\Data\Object;

use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * File Test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRealType()
    {
        $sut = new File();
        $sut->setContent('<html></html>');

        $this->assertEquals('text/html', $sut->getRealType());
    }

    public function testGetContent()
    {
        $sut = new File();
        $this->assertEmpty($sut->getContent());
    }

    public function testSetContent()
    {
        $sut = new File();
        $sut->setContent('testing');
        $this->assertEquals('testing', $sut->getContent());
    }

    public function testGetArrayCopy()
    {
        $data = array(
            'content' => 'testing'
        );

        $sut = new File();
        $sut->setContent($data['content']);

        $this->assertEquals($data, $sut->getArrayCopy());
    }

    public function testExchangeArray()
    {
        $data = array(
            'content' => 'testing'
        );

        $sut = new File();
        $sut->exchangeArray($data);
        $this->assertEquals($data['content'], $sut->getContent());
    }
}
