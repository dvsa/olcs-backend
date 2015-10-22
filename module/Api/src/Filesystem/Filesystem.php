<?php

namespace Dvsa\Olcs\Api\Filesystem;

use Symfony\Component\Filesystem\Filesystem as BaseFileSystem;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Class Filesystem
 * @package Common\Filesystem
 */
class Filesystem extends BaseFileSystem
{
    /**
     * @param $path
     * @param string $prefix
     * @return string
     */
    public function createTmpDir($path, $prefix = '')
    {
        $lock = new LockHandler(hash('sha256', $path));
        $lock->lock(true);

        do {
            $dirname = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($this->exists($dirname));

        $this->mkdir($dirname);

        $lock->release();

        $this->cleanupTmp($dirname);

        return $dirname;
    }

    /**
     * @param $path
     * @param string $prefix
     * @return string
     */
    public function createTmpFile($path, $prefix = '')
    {
        $lock = new LockHandler(hash('sha256', $path));
        $lock->lock(true);

        do {
            $filename = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($this->exists($filename));

        $this->touch($filename);

        $lock->release();

        $this->cleanupTmp($filename);

        return $filename;
    }

    /**
     * @param $name
     */
    private function cleanupTmp($name)
    {
        register_shutdown_function(
            function () use ($name) {
                $this->remove($name);
            }
        );
    }
}
