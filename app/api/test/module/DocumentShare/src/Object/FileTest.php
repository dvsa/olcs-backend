<?php

namespace Dvsa\OlcsTest\DocumentShare\Data\Object;

use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * File Test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
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
            'content' => base64_encode('testing')
        );

        $sut = new File();
        $sut->setContent(base64_decode($data['content']));

        $this->assertEquals($data, $sut->getArrayCopy());
    }

    public function testExchangeArray()
    {
        $data = array(
            'content' => base64_encode('testing')
        );

        $sut = new File();
        $sut->exchangeArray($data);
        $this->assertEquals(base64_decode($data['content']), $sut->getContent());
    }
}
