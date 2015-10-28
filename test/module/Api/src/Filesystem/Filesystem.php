<?php

namespace Dvsa\OlcsTest\Api\Filesystem;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class FilesystemTest extends TestCase
{
    public function testCreateTmpDir()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpDir(vfsStream::url('tmp/'), '', false);

        $this->assertTrue(is_dir($dir));
    }

    public function testCreateTmpFile()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'), '', false);

        $this->assertTrue(file_exists($dir));
    }

    public function testCleanup()
    {
        if (!function_exists('Dvsa\OlcsTest\Api\Filesystem\register_shutdown_function')) {
            eval('namespace Dvsa\OlcsTest\Api\Filesystem; function register_shutdown_function ($callback) { $callback(); }');
        }

        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'), '');

        $this->assertFalse(file_exists($dir));
    }
}