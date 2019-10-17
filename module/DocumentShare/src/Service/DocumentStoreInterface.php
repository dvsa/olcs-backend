<?php


namespace Dvsa\Olcs\DocumentShare\Service;


use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Exception;

interface DocumentStoreInterface
{

    /**
     * Store file on remote storage
     *
     * @param string $path File Path on storage
     * @param File   $file File
     *
     * @return bool
     * @throws Exception
     */
    public function write($path, File $file);

    /**
     * Remove file on storage
     *
     * @param string $path Path to file on storage
     *
     * @return bool
     */
    public function remove($path);

    /**
     * Read content from document store
     *
     * @param string $path Path
     *
     * @return File|null
     */
    public function read($path);
}