<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Laminas\Filter\Decompress;
use Dvsa\Olcs\Api\Domain\Exception\EbsrPackException;
use Laminas\Filter\Exception\RuntimeException as LaminasFilterRuntimeException;

/**
 * Class FileProcessor
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
class FileProcessor implements FileProcessorInterface, EbsrProcessingInterface
{

    const OUTPUT_TYPE = 'xmlFileName';

    /**
     * @var Filesystem
     */
    private $fileSystem;
    /**
     * @var Decompress
     */
    private $decompressFilter;
    /**
     * @var FileUploaderInterface
     */
    private $fileUploader;
    /**
     * @var string
     */
    private $tmpDir;
    /**
     * @var string
     */
    private $subDirPath = '';
    private ZipProcessor $zipProcessor;

    /**
     * FileProcessor constructor.
     *
     * @param FileUploaderInterface $fileUploader     file uploader
     * @param Filesystem            $fileSystem       symphony file system component
     * @param Decompress            $decompressFilter decompression filter
     * @param string $tmpDir           the temporary directory
     */
    public function __construct(
        FileUploaderInterface $fileUploader,
        Filesystem $fileSystem,
        Decompress $decompressFilter,
        ZipProcessor $zipProcessor,
        string $tmpDir
    ) {
        $this->fileUploader = $fileUploader;
        $this->fileSystem = $fileSystem;
        $this->decompressFilter = $decompressFilter;
        $this->tmpDir = $tmpDir;
        $this->zipProcessor = $zipProcessor;
    }

    /**
     * Sets the sub directory path, allows outputting to different directories within /tmp and makes the file processor
     * a bit more reusable in future
     *
     * @param string $subDirPath the sub directory path
     *
     * @return void
     */
    public function setSubDirPath($subDirPath): void
    {
        $this->subDirPath = $subDirPath;
    }

    /**
     * Returns the filename of extracted EBSR xml file
     *
     * @param string $identifier     document identifier
     * @param bool   $isTransXchange whether this is a transXchange request, requiring extra file permissions to be set
     *
     * @return string
     * @throws \RuntimeException
     * @throws EbsrPackException
     */
    public function fetchXmlFileNameFromDocumentStore($identifier, $isTransXchange = false): string
    {
        $targetDir = $this->tmpDir . $this->subDirPath;
        try {
            $xmlFilename = $this->zipProcessor->process($identifier);
            if(!$this->fileSystem->exists($targetDir)) {
                throw new \RuntimeException('The specified tmp directory does not exist');
            }


            //transxchange runs through tomcat, therefore tomcat needs permissions on the files we've just created
            if ($isTransXchange) {
                $execCmd = 'setfacl -bR -m u:tomcat:rwx ' . dirname($xmlFilename);
                exec(escapeshellcmd($execCmd));
            }
        }
        catch (LaminasFilterRuntimeException $e) {
            throw new EbsrPackException('Cannot unzip file : ' . $e->getMessage());
        }
        return $xmlFilename;
    }

    /**
     * @param string $identifier
     * @param array $options
     * @throws EbsrPackException
     */
    public function process(string $identifier, array $options = []): string
    {
        if (!empty($options['isTransXchange'])) {
            return $this->fetchXmlFileNameFromDocumentStore($identifier, true);
        }
        return $this->fetchXmlFileNameFromDocumentStore($identifier);
    }

    public function getOutputType(): string
    {
        return  self::OUTPUT_TYPE;
    }
}
