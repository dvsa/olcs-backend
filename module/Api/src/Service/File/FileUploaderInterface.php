<?php

namespace Dvsa\Olcs\Api\Service\File;

use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;

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
     * @param File $file File
     *
     * @return $this
     */
    public function setFile(File $file);

    /**
     * Get the file
     *
     * @return File
     */
    public function getFile();

    /**
     * Upload file by identifier
     *
     * @param string $identifier File identifier
     *
     * @return ContentStoreFile
     */
    public function upload($identifier);

    /**
     * Process the file download
     *
     * @param string $identifier File identifier
     *
     * @return ContentStoreFile
     */
    public function download($identifier);

    /**
     * Process the file removal
     *
     * @param string $identifier File identifier
     *
     * @return ContentStoreFile
     */
    public function remove($identifier);
}
