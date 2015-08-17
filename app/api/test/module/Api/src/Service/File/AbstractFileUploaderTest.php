<?php

/**
 * Abstract File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\File;

/**
 * Abstract File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractFileUploaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFile()
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

        $abstractFileUploader = $this->getMockForAbstractClass('\Dvsa\Olcs\Api\Service\File\AbstractFileUploader');

        $abstractFileUploader->setFile($data);
        $file = $abstractFileUploader->getFile();

        $this->assertInstanceOf('Dvsa\Olcs\Api\Service\File\File', $file);
        $this->assertEquals($expected, $file->toArray());
    }

    public function testSetServiceLocator()
    {
        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager');

        $abstractFileUploader = $this->getMockForAbstractClass('\Dvsa\Olcs\Api\Service\File\AbstractFileUploader');

        $abstractFileUploader->setServiceLocator($mockServiceLocator);
        $this->assertEquals($mockServiceLocator, $abstractFileUploader->getServiceLocator());
    }
}
