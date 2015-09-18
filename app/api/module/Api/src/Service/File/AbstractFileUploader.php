<?php

/**
 * Abstract File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\File;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFileUploader implements FileUploaderInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Holds the file
     *
     * @var File
     */
    protected $file;

    /**
     * Holds the config array
     *
     * @var array
     */
    protected $config;

    /**
     * Setter for file
     *
     * @param mixed $file
     */
    public function setFile($file)
    {
        if (is_array($file)) {
            $file = $this->createFileFromData($file);
        }

        $this->file = $file;

        return $this;
    }

    /**
     * Getter for file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the config
     *
     * @return array
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $config = $this->getServiceLocator()->get('Config');
            $this->config = $config['file_uploader']['config'];
        }

        return $this->config;
    }

    /**
     * Create a file object
     *
     * @param array $data
     * @return \Dvsa\Olcs\Api\Service\File\File
     */
    protected function createFileFromData(array $data = array())
    {
        $file = new File();
        $file->fromData($data);
        return $file;
    }
}
