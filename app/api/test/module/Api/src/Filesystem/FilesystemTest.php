<?php

namespace Dvsa\OlcsTest\Api\Filesystem;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class FilesystemTest
 * @package Dvsa\OlcsTest\Api\Filesystem
 */
class FilesystemTest extends TestCase
{
    public function testCreateTmpDir()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpDir(vfsStream::url('tmp/'), '', false);

        $this->assertTrue(is_dir($dir));
    }

    public function testCreateTmpDirCleanup()
    {
        if (!function_exists('Dvsa\Olcs\Api\Filesystem\register_shutdown_function')) {
            eval(
                'namespace Dvsa\Olcs\Api\Filesystem; function register_shutdown_function ($callback) { $callback(); }'
            );
        }

        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpDir(vfsStream::url('tmp/'), '');

        $this->assertFalse(is_dir($dir));
    }

    public function testCreateTmpFile()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'), '', false);

        $this->assertTrue(file_exists($dir));
    }

    public function testCreateTmpFileCleanup()
    {
        if (!function_exists('Dvsa\Olcs\Api\Filesystem\register_shutdown_function')) {
            eval(
                'namespace Dvsa\Olcs\Api\Filesystem; function register_shutdown_function ($callback) { $callback(); }'
            );
        }

        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'), '');

        $this->assertFalse(file_exists($dir));
    }
}
