<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Exception;
use League\Flysystem\FilesystemInterface;
use Olcs\Logging\Log\Logger;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Class Client
 */
class Client
{
    const ERR_RESP_FAIL = 'Document store returns invalid response';

    const DS_DOWNLOAD_FILE_PREFIX = 'ds_dwnld_';

    /** @var FilesystemInterface */
    protected $filesystem;

    /** @var string */
    protected $uuid;

    /** @var array */
    protected $cache = [];

    /**
     * Client constructor.
     *
     * @param FilesystemInterface $filesystem File System
     */
    public function __construct(
        FilesystemInterface $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * Read content from document store
     *
     * @param string $path Path
     *
     * @return File|null
     */
    public function read($path)
    {
        $tmpFileName = tempnam(sys_get_temp_dir(), self::DS_DOWNLOAD_FILE_PREFIX);

        try {
            file_put_contents($tmpFileName, $this->filesystem->readStream($path));

            $file = new File();
            $file->setContentFromDsStream($tmpFileName);

            if ($file->getSize() !== 0) {
                return $file;
            }

            $data = (array) json_decode(file_get_contents($tmpFileName));
        } catch (Exception $e) {
            unset($file);

            throw $e;
        } finally {
            if (is_file($tmpFileName)) {
                unlink($tmpFileName);
            }
        }

        //  process error message
        $errMssg = (isset($data['message']) ? $data['message'] : false);
        if ($errMssg !== false) {
            Logger::logResponse(Response::STATUS_CODE_404, $errMssg);
        }

        return null;
    }

    /**
     * Remove file on storage
     *
     * @param string $path Path to file on storage
     * @param bool   $hard Something
     *
     * @return bool
     */
    public function remove($path, $hard = false)
    {
        return $this->filesystem->delete($path);
    }

    /**
     * Store file on remote storage
     *
     * @param string $path File Path on storage
     * @param File   $file File
     *
     * @return bool
     * @throws Exception
     */
    public function write($path, File $file)
    {
        try {
            $fh = fopen($file->getResource(), 'rb');

            //  set filter for auto base 64 encode on read from file
            stream_filter_append($fh, 'convert.base64-encode', STREAM_FILTER_READ);

            return $this->filesystem->writeStream($path, $fh);
        } finally {
            @fclose($fh);
        }
    }
}
