<?php

/**
 * File Upload Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\File;

/**
 * File Upload Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface FileUploaderInterface
{
    /**
     * Get the config
     *
     * @return array
     */
    public function getConfig();

    /**
     * Set the file
     *
     * @param array $file
     */
    public function setFile($file);

    /**
     * Get the file
     *
     * @return File
     */
    public function getFile();

    /**
     * Process the file upload
     */
    public function upload($namespace = null, $key = null);

    /**
     * Process the file download
     */
    public function download($identifier, $name, $namespace = null, $download = true);

    /**
     * Process the file removal
     */
    public function remove($identifier, $namespace = null);
}
