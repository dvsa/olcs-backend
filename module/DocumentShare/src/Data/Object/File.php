<?php

namespace Dvsa\Olcs\DocumentShare\Data\Object;

/**
 * Class File
 */
class File
{
    /** @var  string */
    protected $file;
    /** @var  string */
    private $mimeType;

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
        if ($this->file === null) {
            $this->setResource(tempnam(sys_get_temp_dir(), 'ds'));
        }

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
}
