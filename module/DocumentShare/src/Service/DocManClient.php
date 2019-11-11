<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Olcs\Logging\Log\Logger;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Http\Response;

class DocManClient implements DocumentStoreInterface
{
    const ERR_RESP_FAIL = 'Document store returns invalid response';

    const DS_DOWNLOAD_FILE_PREFIX = 'ds_dwnld_';

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
        $this->baseUri = $baseUri;
        $this->workspace = $workspace;
    }

    /**
     * Set the UUID
     *
     * @param string $uuid UUID
     *
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
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
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json;charset=UTF-8');

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
    public function read($path): ?File
    {
        $tmpFileName = tempnam(sys_get_temp_dir(), self::DS_DOWNLOAD_FILE_PREFIX);

        try {
            /** @var  \Zend\Http\Response\Stream $response */
            $response = $this->getHttpClient()
                ->setRequest($this->getRequest())
                ->setUri(
                    $this->getContentUri($path)
                )
                ->setStream($tmpFileName)
                ->setMethod(Request::METHOD_GET)
                ->send();

            if (!$response->isSuccess()) {
                Logger::logResponse($response->getStatusCode(), self::ERR_RESP_FAIL);

                return null;
            }

            $file = new File();
            $file->setContentFromDsStream($tmpFileName);

            if ($file->getSize() !== 0) {
                return $file;
            }

            $data = (array)json_decode(file_get_contents($tmpFileName));
        } catch (\Exception $e) {
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
    public function remove($path, $hard = false): bool
    {
        $request = $this->getRequest();
        $request->setUri($this->getContentUri($path, $hard))
            ->setMethod(Request::METHOD_DELETE);

        $response = $this->getHttpClient()->setRequest($request)->send();
        return $response->isOk();
    }

    /**
     * Store file on remote storage
     *
     * @param string $path File Path on storage
     * @param File   $file File
     *
     * @return bool
     * @throws \Exception
     */
    public function write($path, File $file): bool
    {
        try {
            $fh = fopen($file->getResource(), 'rb');

            //  set filter for auto base 64 encode on read from file
            $filter = stream_filter_append($fh, 'convert.base64-encode', STREAM_FILTER_READ);

            //  prepare json for sending to DocMan
            $fhT = fopen('php://temp', 'w+b');

            fwrite(
                $fhT,
                '{' .
                '"hubPath":"' . $path . '",' .
                '"mime":"' . $file->getMimeType() . '",' .
                '"content":"'
            );
            stream_copy_to_stream($fh, $fhT);

            //  do not remove following 2 lines, it is fix of last character
            stream_filter_remove($filter);
            stream_copy_to_stream($fh, $fhT);

            fwrite($fhT, '"}');
            rewind($fhT);

            //  send file to DocMan
            $request = $this->getRequest();
            $request
                ->setUri($this->getContentUri(''))
                ->setMethod(Request::METHOD_POST)
                ->setContent(stream_get_contents($fhT));

            $request->getHeaders()
                ->addHeaderLine('Content-Length', fstat($fhT)['size']);

            $response = $this->getHttpClient()->setRequest($request)->send();
            return $response->isOk();
        } finally {
            @fclose($fh);
            @fclose($fhT);
        }
    }

    /**
     * Returns path to get content of file on remote storage
     *
     * @param string $path   Path to file
     * @param bool   $prefix Add Version folder
     *
     * @return string
     */
    protected function getContentUri($path, $prefix = false): string
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
    protected function getUri($path, $prefix, $folder): string
    {
        if ($prefix) {
            $folder = 'version/' . $folder;
        }

        $path = (!empty($path) ? '/' . ltrim($path, '/') : '');

        return rtrim($this->baseUri, '/') . '/' . $folder . '/' . $this->workspace . $path;
    }
}
