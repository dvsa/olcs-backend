<?php

namespace Dvsa\Olcs\DocumentShare\Data\Object;

/**
 * Class File
 */
class File
{
    /**
     * @var string
     */
    protected $content;

    public function getRealType()
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($this->getContent());
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'content' => $this->getContent()
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public function exchangeArray(array $data)
    {
        $this->setContent($data['content']);

        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            return null;
        }

        return $this->content;
    }
}
