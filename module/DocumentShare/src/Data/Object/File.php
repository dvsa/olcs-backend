<?php

namespace Dvsa\Olcs\DocumentShare\Data\Object;

/**
 * Class File
 */
class File
{
    const CHUNK_SIZE = 8192;

    const ERR_CANT_OPEN_STREAM = 'Can not access temp file with downloaded content';
    const ERR_CANT_OPEN_RES = 'Can not access temp file for record downloaded content';

    /** @var  string */
    protected $file;
    /** @var  string */
    private $mimeType;

    /**
     * File constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'ds_');
    }

    /**
     * Descructor
     */
    public function __destruct()
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * Return mime type of file content
     *
     * @return string
     */
    public function getMimeType()
    {
        if ($this->mimeType === null) {
            $this->mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($this->file);
        }

        return $this->mimeType;
    }

    /**
     * Store content and define mime type
     *
     * @param string $content Content
     *
     * @return $this
     */
    public function setContent($content)
    {
        file_put_contents($this->file, $content);

        //  reset dependant properties
        $this->mimeType = null;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->file);
    }

    /**
     * Return content size
     *
     * @return int
     */
    public function getSize()
    {
        return filesize($this->file);
    }

    /**
     * Set name of temp file which hold content
     *
     * @param string $file File name
     *
     * @return $this
     */
    public function setResource($file)
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }

        $this->file = $file;

        return $this;
    }

    /**
     * Get name of temp file which hold content
     *
     * @return string
     */
    public function getResource()
    {
        return $this->file;
    }

    /**
     * Set from content field downloader data and push it to File
     *
     * @param string $streamFileName Document Storeage Downloaded stream
     *
     * @return void
     */
    public function setContentFromDsStream($streamFileName)
    {
        $fhTrg = null;
        try {
            //  get content from stream
            $fhSrc = @fopen($streamFileName, 'rb');
            if ($fhSrc === false) {
                throw new \Exception(self::ERR_CANT_OPEN_STREAM);
            }

            //  get resouce file
            $fhTrg = @fopen($this->file, 'wb');
            if ($fhTrg === false) {
                throw new \Exception(self::ERR_CANT_OPEN_RES);
            }

            //  set filter for auto base 64 decoding on write to file
            $filter = stream_filter_append($fhTrg, 'convert.base64-decode', STREAM_FILTER_WRITE);

            //  read and push content
            $chunkPrev = '';
            $posStart = false;
            $tokenStart = '"content":"';
            $tokenEnd = '"';

            while (!feof($fhSrc)) {
                $chunk = fread($fhSrc, self::CHUNK_SIZE);

                //  looking for begin of content
                if ($posStart === false) {
                    if (false !== ($posStart = strpos($chunkPrev . $chunk, $tokenStart))) {
                        $chunk = substr($chunkPrev . $chunk, $posStart + strlen($tokenStart));
                    }

                    $chunkPrev = $chunk;
                }

                //  content is found, so write content to separate file until end
                if ($posStart !== false) {
                    if (false !== ($posEnd = strpos($chunk, $tokenEnd))) {
                        fwrite($fhTrg, $chunk, $posEnd);

                        break;
                    }

                    fwrite($fhTrg, $chunk);
                }
            }

            fflush($fhTrg);

            stream_filter_remove($filter);

        } finally {
            if (is_resource($fhTrg)) {
                fclose($fhTrg);
            }
            if (is_resource($fhSrc)) {
                fclose($fhSrc);
            }
        }
    }
}
