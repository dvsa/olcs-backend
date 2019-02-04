<?php

namespace Dvsa\Olcs\DocumentShare\Data\Object;

/**
 * Class File
 */
class File
{
    const CHUNK_SIZE = 8192;

    const ERR_CANT_OPEN_DOWNLOAD_STREAM = 'Can not access temp file with downloaded content';
    const ERR_CANT_OPEN_RES = 'Can not access temp file for record downloaded content';

    /** @var  string */
    protected $file;
    /** @var  string */
    private $mimeType;
    /** @var  string */
    private $identifier;

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
     * Destructor
     */
    public function __destruct()
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * Getter for identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for identifier
     *
     * @param string $identifier Identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set mime type of file content
     *
     * @param string $type Type
     *
     * @return $this
     */
    public function setMimeType($type)
    {
        $this->mimeType = $type;

        return $this;
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
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->file);
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
     * Set File content from stream (file)
     *
     * @param string $streamFileName File name
     *
     * @return void
     */
    public function setContentFromStream($streamFileName)
    {
        copy($streamFileName, $this->file);

        //  reset dependant properties
        $this->mimeType = null;
    }
}
