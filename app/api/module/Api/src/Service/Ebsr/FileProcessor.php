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

    public function fetchXmlFileNameFromDocumentStore($identifier)
    {
        $file = $this->fileUploader->download($identifier);

        $filePath = $this->fileSystem->createTmpFile($this->tmpDir, 'ebsr');
        $this->fileSystem->dumpFile($filePath, $file->getContent());

        $tmpDir = $this->fileSystem->createTmpDir($this->tmpDir, 'zip');

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
