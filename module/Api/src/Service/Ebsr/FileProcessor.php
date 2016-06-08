<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Symfony\Component\Finder\Finder;
use Zend\Filter\Decompress;

/**
 * Class FileProcessor
 * @package Dvsa\Api\Service\Ebsr
 */
class FileProcessor implements FileProcessorInterface
{

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

    /**
     * FileProcessor constructor.
     * @param $fileUploader
     * @param Filesystem $fileSystem
     * @param $decompressFilter
     * @param $tmpDir
     */
    public function __construct(
        FileUploaderInterface $fileUploader,
        Filesystem $fileSystem,
        Decompress $decompressFilter,
        $tmpDir
    ) {
        $this->fileUploader = $fileUploader;
        $this->fileSystem = $fileSystem;
        $this->decompressFilter = $decompressFilter;
        $this->tmpDir = $tmpDir;
    }

    /**
     * Sets the sub directory path, allows outputting to different directories within /tmp and makes the file processor
     * a bit more reusable in future
     */
    public function setSubDirPath($subDirPath)
    {
        $this->subDirPath = $subDirPath;
    }

    /**
     * Returns the filename of extracted EBSR xml file
     *
     * @param string $identifier
     * @return string
     * @throws \RuntimeException
     */
    public function fetchXmlFileNameFromDocumentStore($identifier)
    {
        $targetDir = $this->tmpDir . $this->subDirPath;

        if (!$this->fileSystem->exists($targetDir)) {
            throw new \RuntimeException('The specified tmp directory does not exist');
        }

        $file = $this->fileUploader->download($identifier);

        $filePath = $this->fileSystem->createTmpFile($targetDir, 'ebsr');
        $this->fileSystem->dumpFile($filePath, $file->getContent());

        $tmpDir = $this->fileSystem->createTmpDir($targetDir, 'zip');

        $this->decompressFilter->setTarget($tmpDir);
        $this->decompressFilter->filter($filePath);

        $finder = new Finder();
        $files = iterator_to_array($finder->files()->name('*.xml')->in($tmpDir));

        if (count($files) > 1) {
            throw new \RuntimeException('There is more than one XML file in the pack');
        } elseif (!count($files)) {
            throw new \RuntimeException('Could not find an XML file in the pack');
        }

        $xml = key($files);

        return $xml;
    }
}
