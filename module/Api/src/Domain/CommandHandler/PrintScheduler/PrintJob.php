<?php

/**
 * Print Job
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob as Cmd;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Print Job
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintJob extends AbstractCommandHandler implements UploaderAwareInterface, ConfigAwareInterface
{
    use UploaderAwareTrait,
        ConfigAwareTrait;

    protected $repoServiceName = 'Document';

    protected $extraRepos = ['User'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var User $user */
        $user = $this->getRepo('User')->fetchById($command->getUser());

        /** @var Document $document */
        $document = $this->getRepo('Document')->fetchById($command->getDocument());

        $printer = $this->findPrinterForUserAndDocument($user, $document);

        $file = $this->getUploader()->download($document->getIdentifier());

        if ($file === null) {
            throw new Exception('Can\'t find document');
        }

        $fileName = $this->createTmpFile($file, $command->getId(), basename($document->getFilename()));

        // @todo Get the destination from the printer
        //$destination = $printer->getPrinterName();
        $destination = 'OLCS';

        // @todo Get the real username
        //$username = $user->getContactDetails()->getPerson()->getFullName();
        // Hardcoded to my username for now, as this places the PDFs in my home dir, so I can check the output
        $username = 'caigerr';

        $this->printFile(
            $fileName,
            $command->getTitle(),
            $destination,
            $username
        );

        $this->result->addMessage('Printed successfully');

        return $this->result;
    }

    protected function createTmpFile(File $file, $prefix = '', $fileSuffix = '')
    {
        $tmpFile = str_replace(' ', '_', '/tmp/' . $prefix . '-' . uniqid() . '-' . $fileSuffix);

        if (file_put_contents($tmpFile, $file->getContent())) {
            return $tmpFile;
        }

        throw new Exception('Can\'t create tmp file');
    }

    protected function printFile($fileName, $jobTitle, $destination, $username)
    {
        $config = $this->getConfig();

        if (empty($config['print']['server'])) {
            throw new RuntimeException('print.server is not set in config');
        }

        $printServer = $config['print']['server'];
        $command = sprintf(
            'lpr "%s" -H %s -C "%s" -h -P %s -U "%s"',
            $fileName,
            $printServer,
            $jobTitle,
            $destination,
            $username
        );

        exec($command, $output, $result);

        if ($result !== 0) {
            $exception = new NotReadyException('Print service not available: ' . implode("\n", $output));
            $exception->setRetryAfter(60);
            throw $exception;
        }
    }

    /**
     * @param User $user
     * @param Document $document
     * @return Printer
     */
    protected function findPrinterForUserAndDocument(User $user, Document $document)
    {
        $teamPrinters = $user->getTeam()->getPrinters();

        $criteria = [
            // First check for user + sub cat
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('subCategory', $document->getSubCategory())),
            // Then check for team + sub cat
            Criteria::create()
                ->andWhere(Criteria::expr()->isNull('user'))
                ->andWhere(Criteria::expr()->eq('subCategory', $document->getSubCategory())),
            // Then check for user default
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->isNull('subCategory')),
            // Then check for team default
            Criteria::create()
                ->andWhere(Criteria::expr()->isNull('user'))
                ->andWhere(Criteria::expr()->isNull('subCategory'))
        ];

        foreach ($criteria as $criterion) {
            $filteredPrinters = $teamPrinters->matching($criterion);

            if ($filteredPrinters->isEmpty() === false) {
                return $filteredPrinters->first()->getPrinter();
            }
        }

        throw new Exception('Can\'t find printer');
    }
}
