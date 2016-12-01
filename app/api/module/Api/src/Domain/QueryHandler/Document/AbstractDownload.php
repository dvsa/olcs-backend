<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        $response = new \Zend\Http\Response\Stream();
        $response->setStatusCode(Response::STATUS_CODE_200);

        $res = $file->getResource();
        $response->setStream(fopen($res, 'rb'));
        $response->setStreamName($res);

        $headers = $response->getHeaders();
        $headers->addHeaders(
            [
                'Content-Type' => $this->getMimeType($file, $path),
                'Content-Length' => $file->getSize(),
            ]
        );

        if (
            $this->isInline === false
            && !preg_match('/\.html$/', $identifier)
        ) {
            $headers->addHeaderLine('Content-Disposition: attachment; filename="' . basename($identifier) . '"');
        }

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
        $ext = substr(strrchr($path, '.'), 1);

        $cfgDs = $this->config['document_share'];
        $mimeExclude = (isset($cfgDs['mime_exclude']) ? $cfgDs['mime_exclude'] : []);

        if (isset($mimeExclude[$ext])) {
            return $mimeExclude[$ext];
        }

        return $file->getMimeType();
    }
}
