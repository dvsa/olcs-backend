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
    public function upload($identifier);

    /**
     * Process the file download
     */
    public function download($identifier);

    /**
     * Process the file removal
     */
    public function remove($identifier);
}
