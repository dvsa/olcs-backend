<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Domain\Exception\EbsrPackException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Laminas\Filter\Decompress;
use Laminas\Filter\Exception\RuntimeException as LaminasFilterRuntimeException;
use Symfony\Component\Finder\Finder;
use Psr\Log\LoggerInterface;

class ZipProcessor implements EbsrProcessingInterface
{
    const DECOMPRESS_ERROR_PREFIX = 'There was a problem with the pack file: ';

    const BUS_REGISTRATION_FILE_LOCATION = '/documents/Bus_Registration/TransXChange_File/';

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
    private string $subDirPath = '';
    private LoggerInterface $logger;

    private Finder $finder;
    private string $targetDir;

    /**
     * @return string
     */
    public function getSubDirPath(): string
    {
        return $this->subDirPath;
    }

    /**
     * @param string $subDirPath
     */
    public function setSubDirPath(string $subDirPath): void
    {
        $this->subDirPath = $subDirPath;
    }



    public function __construct(
        FileUploaderInterface $fileUploader,
        Filesystem $fileSystem,
        Decompress $decompressFilter,
        string $tmpDir,
        LoggerInterface $logger,
        Finder $finder
    ) {
        $this->fileUploader = $fileUploader;
        $this->fileSystem = $fileSystem;
        $this->decompressFilter = $decompressFilter;
        $this->tmpDir = $tmpDir;
        $this->logger = $logger;
        $this->finder = $finder;
    }

    /**
     * @param string $identifier
     * @param array $options
     * @return string
     * @throws EbsrPackException
     * @throws \RuntimeException
     */
    public function process(string $identifier, array $options = []): string
    {
        $targetDir = $this->tmpDir . $this->subDirPath;

        if (!$this->fileSystem->exists($targetDir)) {
            throw new \RuntimeException('The specified tmp directory does not exist', ['targetDir' => $targetDir]);
        }

        $file = $this->fileUploader->download($identifier);

        $filePath = $this->fileSystem->createTmpFile($targetDir, 'zip');
        $this->fileSystem->dumpFile($filePath, $file->getContent());

        $this->targetDir = $this->fileSystem->createTmpDir($targetDir, 'ebsr');
        $this->decompressFilter->setTarget($this->targetDir);

        //attempt to decompress the zip file
        try {
            $this->decompressFilter->filter($filePath);
        } catch (LaminasFilterRuntimeException $e) {
            throw new EbsrPackException(self::DECOMPRESS_ERROR_PREFIX . $e->getMessage());
        }


        return $this->extractedXmlFile();

    }

    /**
     * @return string
     * @throws EbsrPackException
     */
    private function extractedXmlFile(): string
    {

        $files = iterator_to_array($this->finder->files()->name('*.xml')->in($this->targetDir));

        if (count($files) > 1) {
            throw new EbsrPackException('There is more than one XML file in the pack');
        } elseif (!count($files)) {
            throw new EbsrPackException('Could not find an XML file in the pack');
        }

        return $this->storeXmlFile((string) key($files));
    }

    private function storeXmlFile( string $tmpfile ): string
    {
        $this->logger->debug('Storing transxchange xml file in content store', ['tmpfile' => $tmpfile]);
        $file = new File();
        $file->setContent(file_get_contents($tmpfile));
        $file->setMimeType('text/xml');
        $filename = self::BUS_REGISTRATION_FILE_LOCATION.date_format(new DateTime(), 'Y\/m\/').str_replace('/', '_', $tmpfile);
        return $this->fileUploader->upload($filename, $file)->getIdentifier();
    }
    public function getOutputType(): string
    {
        return self::OUTPUT_TYPE;
    }



}
