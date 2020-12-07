<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Dvsa\Olcs\Utils\Helper\FileHelper;
use Laminas\Http\Response;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract class for download handler
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
abstract class AbstractDownload extends AbstractQueryHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'Document';

    private $isInline = false;
    /** @var array */
    private $config = [];

    /**
     * Create service
     *
     * @param \Dvsa\Olcs\Api\Domain\QueryHandlerManager $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->config = (array)$serviceLocator->getServiceLocator()->get('config');

        return parent::createService($serviceLocator);
    }

    /**
     * Process downloading file
     *
     * @param string      $identifier File name
     * @param string|null $path       Path to file
     *
     * @return Response\Stream
     * @throws NotFoundException
     */
    protected function download($identifier, $path = null)
    {
        if ($path === null) {
            $path = $identifier;
        }

        $file = $this->getUploader()->download($path);
        if ($file === null) {
            throw new NotFoundException();
        }

        $response = new \Laminas\Http\Response\Stream();
        $response->setStatusCode(Response::STATUS_CODE_200);

        $fileName = $file->getResource();
        $fileSize = $file->getSize();

        $response->setStream(fopen($fileName, 'rb'));
        $response->setStreamName($fileName);
        $response->setContentLength($fileSize);
        $response->setCleanup(true);

        $isInline = (
            $this->isInline === true
            || 'html' === FileHelper::getExtension($identifier)
        );

        // OLCS-14910 If file doesn't have an extension then add a '.txt' extension
        $downloadFileName = basename($identifier);
        if (FileHelper::getExtension($downloadFileName) === false) {
            $downloadFileName .= '.txt';
        }

        $headers = $response->getHeaders();
        $headers->addHeaders(
            [
                'Content-Type' => $this->getMimeType($file, $path) . ';charset=UTF-8',
                'Content-Length' => $fileSize,
                'Content-Disposition' => ($isInline ? 'inline' : 'attachment') .
                    ';filename="' . $downloadFileName . '"',
            ]
        );

        return $response;
    }

    /**
     * Setter for isInline property
     *
     * @param bool $inline True, if do not download
     *
     * @return $this
     */
    protected function setIsInline($inline)
    {
        $this->isInline = (bool)$inline;
        return $this;
    }

    /**
     * Define correct mimetype for file
     *
     * @param ContentStoreFile $file File
     * @param string           $path Path to file
     *
     * @return string
     */
    private function getMimeType(ContentStoreFile $file, $path)
    {
        $ext = FileHelper::getExtension($path);

        $cfgDs = $this->config['document_share'];
        $mimeExclude = (isset($cfgDs['invalid_defined_mime_types']) ? $cfgDs['invalid_defined_mime_types'] : []);

        if (isset($mimeExclude[$ext])) {
            return $mimeExclude[$ext];
        }

        return $file->getMimeType();
    }
}
