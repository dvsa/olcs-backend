<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * Class Client
 */
class Client
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var Request
     */
    protected $requestTemplate;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $workspace;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param string $baseUri
     * @return $this
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param \Zend\Http\Client $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return Request
     */
    public function getRequestTemplate()
    {
        return $this->requestTemplate;
    }

    /**
     * @param Request $requestTemplate
     */
    public function setRequestTemplate(Request $requestTemplate)
    {
        $this->requestTemplate = $requestTemplate;
    }

    /**
     * @param string $workspace
     * @return $this
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = trim($workspace, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param $path
     * @return File|null
     */
    public function read($path)
    {
        if (!isset($this->cache[$path])) {

            $request = clone $this->requestTemplate;

            $request->setUri($this->getContentUri($path))
                ->setMethod('GET');

            $response = $this->getHttpClient()->setRequest($request)->send();

            if ($response->getStatusCode() == 200) {
                $data = (array) json_decode($response->getBody());
                $data['content'] = base64_decode($data['content']);
                $file = new File();
                $file->exchangeArray($data);
                $this->cache[$path] = $file;
            } else {
                $this->cache[$path] = null;
            }
        }

        return $this->cache[$path];
    }

    /**
     * @param $path
     * @param $hard
     * @return \Zend\Http\Response
     */
    public function remove($path, $hard = false)
    {
        $request = clone $this->requestTemplate;
        $request->setUri($this->getContentUri($path, $hard))
            ->setMethod('DELETE');

        return $this->getHttpClient()->setRequest($request)->send();
    }

    /**
     * @param $path
     * @param File $file
     * @return \Zend\Http\Response
     */
    public function write($path, File $file)
    {
        $data = $file->getArrayCopy();
        $data['hubPath'] = $path;
        $data['mime'] = $file->getRealType();
        $data['content'] = base64_encode($data['content']);
        $requestJson = json_encode($data, JSON_UNESCAPED_SLASHES);

        $request = clone $this->requestTemplate;
        $request->setUri($this->getContentUri(''))
            ->setMethod('POST')
            ->setContent($requestJson);

        $request->getHeaders()
            ->addHeaderLine('Content-Type', 'application/json');

        return $this->getHttpClient()->setRequest($request)->send();
    }

    /**
     * @param $path
     * @return string
     */
    protected function getContentUri($path, $prefix = false)
    {
        return $this->getUri($path, $prefix, 'content');
    }

    /**
     * @param $path
     * @return string
     */
    protected function getUri($path, $prefix, $folder)
    {
        if ($prefix) {
            $folder = 'version/' . $folder;
        }
        if ($path) {
            return $this->getBaseUri() . '/' . $folder . '/' . $this->getWorkspace() . '/' . ltrim($path, '/');
        } else {
            return $this->getBaseUri() . '/' . $folder . '/' . $this->getWorkspace();
        }
    }
}
