<?php

/**
 * Class CpidOrganisationExport
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\LockHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;

/**
 * Class CpidOrganisationExport
 *
 * @package Dvsa\Olcs\Cli\Service\Queue\Consumer
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
class CpidOrganisationExport implements MessageConsumerInterface
{
    protected $path = null;

    protected $organisationRepo = null;

    protected $commandHandler = null;

    protected $fileUploader = null;

    protected $fileSystem = null;

    protected $lockHandler = null;

    public function __construct(
        $path,
        Organisation $organisation,
        CommandHandlerInterface $commandHandler,
        FileUploaderInterface $fileUploader,
        Filesystem $fileSystem,
        LockHandler $lockHandler
    ) {
        $this->path = $path;
        $this->organisationRepo = $organisation;
        $this->commandHandler = $commandHandler;
        $this->fileUploader = $fileUploader;
        $this->fileSystem = $fileSystem;
        $this->lockHandler = $lockHandler;
    }

    public function processMessage(QueueEntity $item)
    {
        $options = (array)json_decode($item->getOptions());

        $iterableResult = $this->organisationRepo
            ->fetchAllByStatusForCpidExport($options['status']);

        $filename = $this->createTmpFile($this->path);

        $handle = fopen($filename, 'w');
        while (($row = $iterableResult->next()) !== false) {
            fputcsv($handle, $row[key($row)]);
        }
        fclose($handle);

        $file = $this->uploadFile(file_get_contents($filename));

        unlink($filename);

        $result = $this->commandHandler
            ->handleCommand(
                CreateDocumentSpecific::create(
                    [
                        'filename' => 'cpid-classification.csv',
                        'identifier' => $file->getIdentifier(),
                        'category' => Category::CATEGORY_LICENSING,
                        'subCategory' => Category::DOC_SUB_CATEGORY_CPID,
                        'description' => 'CPID Classifications',
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
            );

        if ($result !== false) {
            return $this->success($item, 'Organisation list exported.');
        }

        return $this->failed($item, 'Unable to export list.');
    }

    private function uploadFile($contents)
    {
        return $this->fileUploader
            ->setFile(
                [
                    'content' => $contents
                ]
            )->upload();
    }

    private function createTmpFile($path, $prefix = '')
    {
        do {
            $filename = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($this->fileSystem->exists($filename));

        $this->fileSystem->touch($filename);

        $this->lockHandler->release();

        return $filename;
    }

    protected function success(QueueEntity $item, $message = null)
    {
        $command = CompleteCmd::create(['item' => $item]);
        $this->commandHandler->handleCommand($command);

        return 'Successfully processed message: '
        . $item->getId() . ' ' . $item->getOptions()
        . ($message ? ' ' . $message : '');
    }

    protected function failed(QueueEntity $item, $reason = null)
    {
        $command = FailedCmd::create(['item' => $item]);
        $this->commandHandler->handleCommand($command);

        return 'Failed to process message: '
        . $item->getId() . ' ' . $item->getOptions()
        . ' ' .  $reason;
    }
}
