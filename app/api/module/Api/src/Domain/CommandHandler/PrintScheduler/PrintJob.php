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
class PrintJob extends AbstractCommandHandler implements UploaderAwareInterface, ConfigAwareInterface
{
    use UploaderAwareTrait,
        ConfigAwareTrait;

    protected $repoServiceName = 'Document';

    protected $extraRepos = ['User', 'SystemParameter', 'Printer'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var User $user */
        $user = $this->getRepo('User')->fetchById($command->getUser());

        /* @var $document Document */
        $document = $this->getRepo('Document')->fetchById($command->getDocument());

        // get destination ie the CUPS print queue name
        if ($user->getTeam()) {
            $printer = $this->findPrinterForUserAndDocument($user, $document);
            $destination = $printer->getPrinterName();
        } else {
            // If user does NOT have a team, they must be selfserve. Therefore get Printer from system parameter
            $destination = $this->getSelfserveUserPrinter();
        }

        // Print server doesn't have perm to gen PDF unless the user exists on the box :(
        // $username = $user->getContactDetails()->getPerson()->getFullName();
        // Allow override the username from config
        //if ($this->getConfigUser() !== false) {
        $username = $this->getConfigUser();
        //}

        // if the destination (queue name) is TESTING-STUB-LICENCE:n then attach it to a licence
        // This allows testing without have to actually connect to multiple printers/queues
        if (strpos($destination, 'TESTING-STUB-LICENCE:') === 0) {
            $licenceId = (int) substr($destination, strlen('TESTING-STUB-LICENCE:'));

            $this->stubPrint($document, $licenceId);

            $this->result->addMessage('Printed successfully (stub to licence '. $licenceId .')');

            return $this->result;
        }

        $file = $this->getUploader()->download($document->getIdentifier());
        if ($file === null) {
            throw new Exception('Can\'t find document');
        }

        try {
            $fileName = $this->createTmpFile($file, $command->getId(), basename($document->getFilename()));

            $this->printFile(
                $fileName,
                basename($fileName),
                $destination,
                $username
            );
        } finally {
            // if something goes wrong, still delete temp files
            $this->deleteTempFiles($fileName);
        }

        $this->result->addMessage('Printed successfully');

        return $this->result;
    }

    /**
     * Create a temporary file
     *
     * @param File   $file
     * @param string $prefix
     * @param string $fileSuffix
     *
     * @return string path of temporary file
     *
     * @throws Exception
     */
    protected function createTmpFile(File $file, $prefix = '', $fileSuffix = '')
    {
        $tmpFile = str_replace(' ', '_', '/tmp/' . $prefix . '-' . uniqid() . '-' . $fileSuffix);

        if (file_put_contents($tmpFile, trim($file->getContent()))) {
            return $tmpFile;
        }

        throw new Exception('Can\'t create tmp file');
    }

    /**
     * Execute a system command
     *
     * @param string $command
     * @param array  $output
     * @param int    $result
     */
    protected function executeCommand($command, &$output, &$result)
    {
        exec($command, $output, $result);
    }

    /**
     * Delete any temp files
     *
     * @param string $fileName
     */
    protected function deleteTempFiles($fileName)
    {
        // remove temporary rtf file
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        // remove temporary pdf file
        $pdfFilename = str_replace('.rtf', '.pdf', $fileName);
        if (file_exists($pdfFilename)) {
            unlink($pdfFilename);
        }
    }

    protected function printFile($fileName, $jobTitle, $destination, $username)
    {
        $printServer = $this->getConfigPrintServer();
        if ($printServer === false) {
            throw new RuntimeException('print.server is not set in config');
        }

        $commandPdf = sprintf(
            'soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp "%s"',
            $fileName
        );
        $this->executeCommand($commandPdf, $outputPdf, $resultPdf);
        if ($resultPdf !== 0) {
            $exception = new NotReadyException('Print service not available: ' . implode("\n", $outputPdf));
            $exception->setRetryAfter(60);
            throw $exception;
        }

        $commandPrint = sprintf(
            'lpr "%s" -H %s -C "%s" -h -P %s -U "%s"',
            str_replace('.rtf', '.pdf', $fileName),
            $printServer,
            $jobTitle,
            $destination,
            $username
        );
        $this->executeCommand($commandPrint, $outputPrint, $resultPrint);
        if ($resultPrint !== 0) {
            $exception = new NotReadyException('Print service not available: ' . implode("\n", $outputPrint));
            $exception->setRetryAfter(60);
            throw $exception;
        }
    }

    /**
     * Find the printer to be used for a user
     *
     * @param User $user
     * @param Document $document
     *
     * @return Printer
     */
    protected function findPrinterForUserAndDocument(User $user, Document $document)
    {
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

        $teamPrinters = $user->getTeam()->getTeamPrinters();
        foreach ($criteria as $criterion) {
            $filteredPrinters = $teamPrinters->matching($criterion);

            if ($filteredPrinters->isEmpty() === false) {
                return $filteredPrinters->first()->getPrinter();
            }
        }

        throw new Exception('Cannot find printer for User '. $user->getLoginId());
    }

    /**
     * Get the printer for selfserve users
     *
     * @return Printer
     */
    private function getSelfserveUserPrinter()
    {
        $printerQueue = $this->getRepo('SystemParameter')
            ->fetchValue(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER);

        return $printerQueue;
    }

    /**
     * Get print server address from config
     *
     * @return boolean|string
     */
    private function getConfigPrintServer()
    {
        $config = $this->getConfig();

        if (isset($config['print']['server'])) {
            return $config['print']['server'];
        }

        return false;
    }

    /**
     * Get username from config
     *
     * @return boolean|string
     */
    private function getConfigUser()
    {
        $config = $this->getConfig();

        if (isset($config['print']['options']['user'])) {
            return $config['print']['options']['user'];
        }

        return false;
    }

    /**
     * Stub printing by add a document to licence 7
     *
     * @todo remove this method when stubbing no longer required
     * @codeCoverageIgnore
     *
     * @param Document $document  Document to be printed
     * @param int      $licenceId Licence to attach to
     */
    private function stubPrint(Document $document, $licenceId = 7)
    {
        $printDocument = new \Dvsa\Olcs\Api\Entity\Doc\Document($document->getIdentifier());

        $printDocument->setDescription("PRINT ". $document->getDescription());
        $printDocument->setFilename(str_replace(' ', '_', $document->getDescription()) . '.rtf');
        // hard coded simply so we can demo against *something*
        $printDocument->setLicence(
            $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Licence\Licence::class, $licenceId)
        );
        $printDocument->setCategory(
            $this->getRepo()->getCategoryReference(\Dvsa\Olcs\Api\Entity\System\Category::CATEGORY_LICENSING)
        );
        $printDocument->setSubCategory(
            $this->getRepo()->getSubCategoryReference(
                \Dvsa\Olcs\Api\Entity\System\Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST
            )
        );
        $printDocument->setIsExternal(false);
        $printDocument->setIsReadOnly('Y');
        $printDocument->setIssuedDate(new \Datetime());

        $this->getRepo()->save($printDocument);
    }
}
