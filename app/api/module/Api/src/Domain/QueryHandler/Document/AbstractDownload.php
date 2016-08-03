<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Zend\Http\Response;

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
        $response->setStream(fopen($res, 'r'));
        $response->setStreamName($res);

        $headers = $response->getHeaders();
        $headers->addHeaders(
            [
                'Content-Type' => $file->getMimeType(),
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
}
