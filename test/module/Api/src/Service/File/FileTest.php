<?php

/**
 * Test File class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\File;

use Dvsa\Olcs\Api\Service\File\File;

/**
 * Test File class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testFromData()
    {
        $data = array(
            'name' => 'Bob',
            'tmp_name' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => 'foo'
        );

        $expected = array(
            'identifier' => null,
            'name' => 'Bob',
            'path' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => 'foo'
        );

        $file = new File();
        $file->fromData($data);
        $this->assertEquals($expected, $file->toArray());
    }

    public function testFromDataWithIdentifier()
    {
        $data = array(
            'name' => 'Bob',
            'tmp_name' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646
        );

        $expected = array(
            'identifier' => 'dfghjkl',
            'name' => 'Bob',
            'path' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => null
        );

        $file = new File();
        $file->setIdentifier('dfghjkl');
        $file->fromData($data);
        $this->assertEquals($expected, $file->toArray());
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGetExtension($name, $extension)
    {
        $file = new File();
        $file->setName($name);

        $this->assertEquals(
            $extension,
            $file->getExtension()
        );
    }

    public function extensionProvider()
    {
        return array(
            array('', ''),
            array('A file with no extension', ''),
            array('Another file.txt', 'txt'),
            array('article.jpg.doc', 'doc')
        );
    }

    public function testGetRealType()
    {
        $file = new File();
        $file->setContent('plain text');

        $this->assertEquals(
            'text/plain',
            $file->getRealType()
        );
    }

    public function testFluentInterface()
    {
        $file = new File();

        $this->assertSame($file, $file->setIdentifier('foo'));
        $this->assertSame($file, $file->setPath('foo'));
        $this->assertSame($file, $file->setSize('foo'));
    }
}
