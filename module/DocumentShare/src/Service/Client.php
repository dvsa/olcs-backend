<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mime\Mime;

/**
 * Class Client
 */
class Client
{
    /** @var HttpClient */
    protected $httpClient;
    /** @var Request */
    protected $requestTemplate;
    /** @var  Filesystem */
    private $fileSystem;
    /** @var string */
    protected $baseUri;
    /** @var string */
    protected $workspace;

    /** @var array */
    protected $cache = [];

    /**
     * Client constructor.
     *
     * @param HttpClient $httpClient      Http Client
     * @param Request    $requestTemplate Request
     * @param Filesystem $fileSystem      Filesystem
     * @param string     $baseUri         base uri path to storage
     * @param string     $workspace       path
     */
    public function __construct(
        HttpClient $httpClient,
        Request $requestTemplate,
        Filesystem $fileSystem,
        $baseUri,
        $workspace
    ) {
        $this->httpClient = $httpClient;
        $this->requestTemplate = $requestTemplate;
        $this->fileSystem = $fileSystem;
        $this->baseUri = trim($baseUri);
        $this->workspace = trim($workspace);
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
     * Return Templace of Request object
     *
     * @return Request
     */
    public function getRequestTemplate()
    {
        return $this->requestTemplate;
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
        if (isset($this->cache[$path])) {
            return $this->cache[$path];
        }

        $tmpFileName = $this->fileSystem->createTmpFile(sys_get_temp_dir(), 'download');

        $request = clone $this->requestTemplate;
        $request->setUri($this->getContentUri($path))
            ->setMethod(Request::METHOD_GET);

        /** @var  \Zend\Http\Response\Stream $response */
        $response = $this->getHttpClient()
            ->setStream($tmpFileName)
            ->setRequest($request)
            ->send();

        if ($response->isSuccess()) {
            $data = (array)json_decode($response->getBody());
            //  reuse file used for download
            file_put_contents($tmpFileName, base64_decode($data['content']));

            $file = new File();
            $file->setResource($tmpFileName);

            unset($data);

            return $this->cache[$path] = $file;
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
        $request = clone $this->requestTemplate;
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

        $request = clone $this->requestTemplate;
        $request
            ->setUri($this->getContentUri(''))
            ->setMethod(Request::METHOD_POST)
            ->setContent($requestJson);

        $request->getHeaders()
            ->addHeaderLine('Content-Length', strlen($requestJson))
            ->addHeaderLine('Content-Type', 'application/json');

        return $this->getHttpClient()->setRequest($request)->send();
    }

    /**
     * Return path to content
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

        return $this->baseUri . '/' . $folder . '/' . $this->workspace . $path;
    }
}
