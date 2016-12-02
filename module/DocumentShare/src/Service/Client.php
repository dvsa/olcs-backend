<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Olcs\Logging\Log\Logger;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Class Client
 */
class Client
{
    const ERR_RESP_FAIL = 'Document store returns invalid response';

    /** @var HttpClient */
    protected $httpClient;
    /** @var string */
    protected $baseUri;
    /** @var string */
    protected $workspace;
    /** @var string */
    protected $uuid;

    /** @var array */
    protected $cache = [];

    /**
     * Client constructor.
     *
     * @param HttpClient $httpClient Http Client
     * @param string     $baseUri    base uri path to storage
     * @param string     $workspace  path
     */
    public function __construct(
        HttpClient $httpClient,
        $baseUri,
        $workspace
    ) {
        $this->httpClient = $httpClient;
        $this->baseUri = trim($baseUri);
        $this->workspace = trim($workspace);
    }

    /**
     * Set the UUID
     *
     * @param string $uuid UUID
     *
     * @return @void
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get the UID
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Return base url
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Return Http Client
     *
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Get a Request object to send to the HFS service
     *
     * @return Request
     */
    private function getRequest()
    {
        $request = new Request();
        if ($this->getUuid()) {
            $request->getHeaders()->addHeaderLine('uuid', $this->getUuid());
        }
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        return $request;
    }

    /**
     * Returns workspace
     *
     * @return string
     */
    public function getWorkspace()
    {
        return $this->workspace;
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
        $path = $this->getContentUri($path);

        try {
            $tmpFileName = tempnam(sys_get_temp_dir(), 'download');

            /** @var  \Zend\Http\Response\Stream $response */
            $response = $this->getHttpClient()
                ->setRequest($this->getRequest())
                ->setUri($path)
                ->setStream($tmpFileName)
                ->setMethod(Request::METHOD_GET)
                ->send();

            if (!$response->isSuccess()) {
                Logger::logResponse($response->getStatusCode(), self::ERR_RESP_FAIL);

                return null;
            }

            $data = (array)json_decode(file_get_contents($tmpFileName));

        } finally {
            if (is_file($tmpFileName)) {
                unlink($tmpFileName);
            }
        }

        //  process file content
        $content = (isset($data['content']) ? base64_decode($data['content']) : false);
        if ($content !== false) {
            $file = new File();
            $file->setContent($content);
            return $file;
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
     * @return Response
     */
    public function remove($path, $hard = false)
    {
        $request = $this->getRequest();
        $request->setUri($this->getContentUri($path, $hard))
            ->setMethod(Request::METHOD_DELETE);

        return $this->getHttpClient()->setRequest($request)->send();
    }

    /**
     * Store file on remote storage
     *
     * @param string $path File Path on storage
     * @param File   $file File
     *
     * @return Response
     * @throws \Exception
     */
    public function write($path, File $file)
    {
        //  don't use here json_encode it consume too much memory
        $requestJson =
            '{' .
                '"hubPath": "' . $path . '",' .
                '"mime": "' . $file->getMimeType() . '",' .
                '"content": "' . base64_encode($file->getContent()) . '"' .
            '}';

        $request = $this->getRequest();
        $request
            ->setUri($this->getContentUri(''))
            ->setMethod(Request::METHOD_POST)
            ->setContent($requestJson);

        $request->getHeaders()
            ->addHeaderLine('Content-Length', strlen($requestJson));

        return $this->getHttpClient()->setRequest($request)->send();
    }

    /**
     * Returns path to get content of file on remote storage
     *
     * @param string $path   Path to file
     * @param bool   $prefix Add Version folder
     *
     * @return string
     */
    protected function getContentUri($path, $prefix = false)
    {
        return $this->getUri($path, $prefix, 'content');
    }

    /**
     * Returns full path at Doc Store
     *
     * @param string $path   File Path
     * @param string $prefix isPrefix
     * @param string $folder Folder
     *
     * @return string
     */
    protected function getUri($path, $prefix, $folder)
    {
        if ($prefix) {
            $folder = 'version/' . $folder;
        }

        $path = (!empty($path) ? '/' . ltrim($path, '/') : '');

        return rtrim($this->baseUri, '/') . '/' . $folder . '/' . $this->workspace . $path;
    }
}
