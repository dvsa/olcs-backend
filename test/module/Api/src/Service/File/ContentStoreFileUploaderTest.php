<?php

/**
 * Content Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\File;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Zend\ServiceManager\ServiceManager;

/**
 * Content Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContentStoreFileUploaderTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ContentStoreFileUploader();
        // @todo rewrite this
    }

    public function testDownload()
    {
        $this->markTestSkipped();
    }
}
