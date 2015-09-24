<?php

/**
 * Content Store File Uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\File;

use Zend\Http\Response;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\DocumentShare\Service\Client as ContentStoreClient;

/**
 * Content Store File Uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContentStoreFileUploader implements FileUploaderInterface, FactoryInterface
{
    /**
     * Holds the file
     *
     * @var File
     */
    private $file;

    /**
     * @var ContentStoreClient
     */
    private $contentStore;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setContentStore($serviceLocator->get('ContentStore'));

        return $this;
    }

    /**
     * @return ContentStoreClient
     */
    public function getContentStore()
    {
        return $this->contentStore;
    }

    /**
     * @param ContentStoreClient $contentStore
     */
    public function setContentStore($contentStore)
    {
        $this->contentStore = $contentStore;
    }

    /**
     * Getter for file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Setter for file
     *
     * @param mixed $file
     */
    public function setFile($file)
    {
        if (is_array($file)) {
            $file = $this->createFileFromData($file);
        }

        $this->file = $file;

        return $this;
    }

    /**
     * @param $identifier
     * @return File
     * @throws Exception
     */
    public function upload($identifier)
    {
        $file = $this->getFile();

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent());

        $response = $this->write($identifier, $storeFile);

        if ($response->isSuccess()) {
            $file->setPath($identifier);
            $file->setIdentifier($identifier);

            return $file;
        }

        if ($response->getStatusCode() === Response::STATUS_CODE_415) {
            throw new MimeNotAllowedException();
        }

        throw new Exception('Unable to store uploaded file: ' . $response->getBody());
    }

    /**
     * Download the file
     */
    public function download($identifier)
    {
        return $this->getContentStore()->read($identifier);
    }

    /**
     * Remove the file
     */
    public function remove($identifier)
    {
        return $this->getContentStore()->remove($identifier);
    }

    /**
     * @param $identifier
     * @param $file
     * @return Response
     */
    private function write($identifier, $file)
    {
        return $this->getContentStore()->write($identifier, $file);
    }

    /**
     * Create a file object
     *
     * @param array $data
     * @return \Dvsa\Olcs\Api\Service\File\File
     */
    private function createFileFromData(array $data = [])
    {
        $file = new File();
        $file->fromData($data);
        return $file;
    }
}
