<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\DocumentShare\Exception\InvalidMimeTypeException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

/**
 * Class Client
 */
class WebDavClient implements DocumentStoreInterface
{
    const DS_DOWNLOAD_FILE_PREFIX = 'ds_dwnld_';

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var array
     */
    private $validMimeTypes = [];

    /**
     * Client constructor.
     *
     * @param FilesystemInterface $filesystem File System
     * @param array $validMimeTypes Array of valid mime types to upload
     */
    public function __construct(FilesystemInterface $filesystem, array $validMimeTypes)
    {
        $this->filesystem = $filesystem;
        $this->validMimeTypes = $validMimeTypes;
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
            $readStream = $this->filesystem->readStream($path);
            file_put_contents($tmpFileName, $readStream);

            $file = new File();
            $file->setContentFromStream($tmpFileName);

            if ($file->getSize() !== 0) {
                return $file;
            }
        } catch (FileNotFoundException $e) {
            unset($file);
            return false;
        } finally {
            if (is_file($tmpFileName)) {
                unlink($tmpFileName);
            }
        }

        return false;
    }

    /**
     * Remove file on storage
     *
     * @param string $path Path to file on storage
     *
     * @param bool   $hard
     *
     * @return bool
     */
    public function remove($path, $hard = false): bool
    {
        try {
            return $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    /**
     * Store file on remote storage
     *
     * @param string $path File Path on storage
     * @param File   $file File
     *
     * @return WebDavResponse
     * @throws \Exception
     */
    public function write($path, File $file)
    {
        $response = new WebDavResponse();

        try {
            $fh = fopen($file->getResource(), 'rb');
            $this->checkFileMimeType($file);
            $response->setResponse($this->filesystem->writeStream($path, $fh));
            $statusCode = $response->isSuccess() ? WebDavResponse::STATUS_CODE_200 : WebDavResponse::STATUS_CODE_500;
            $response->setStatusCode($statusCode);
        } catch (FileExistsException $e) {
            $response->setResponse(false);
            $response->setStatusCode(WebDavResponse::STATUS_CODE_500);
        } catch (InvalidMimeTypeException $exception) {
            $response->setResponse(false);
            $response->setStatusCode(WebDavResponse::STATUS_CODE_415);
        } finally {
            @fclose($fh);
        }
        return $response;
    }

    /**
     * @param File $file
     * @throws InvalidMimeTypeException
     */
    protected function checkFileMimeType(File $file)
    {
        if (!in_array($file->getMimeType(), $this->validMimeTypes)) {
            throw new InvalidMimeTypeException();
        }
    }
}
