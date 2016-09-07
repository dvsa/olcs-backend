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
     * Handle Command
     *
     * @param CommandInterface $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $user User */
        if (!empty($command->getUser())) {
            $user = $this->getRepo('User')->fetchById($command->getUser());
            // get the username of the person who has sent the print job
            $username = $user->getLoginId();
        } else {
            $user = null;
            $username = 'Anonymous';
        }

        /* @var $document Document */
        $document = $this->getRepo('Document')->fetchById($command->getDocument());

        // get destination ie the CUPS print queue name
        if ($user !== null && $user->getTeam()) {
            $printer = $this->findPrinterForUserAndDocument($user, $document);
            $destination = $printer->getPrinterName();
        } else {
            // If user does NOT have a team, they must be selfserve. Therefore get Printer from system parameter
            $destination = $this->getSelfserveUserPrinter();
        }

        // This config allows us to override the username on the environments that use the CUPS PDF print driver
        // This is needed as the username has to exist on the CUPS box so that the PDF file can be created
        if ($this->getConfigUser() !== false) {
            $username = $this->getConfigUser();
        }

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
     * @param File   $file       File contents to be written to the tmp file
     * @param string $prefix     Temporary filename prefix
     * @param string $fileSuffix Temporary filename suffix
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
     * @param string $command CLI command to execute
     * @param array  &$output Output from command
     * @param int    &$result Result/exit code
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function executeCommand($command, &$output, &$result)
    {
        exec($command, $output, $result);
    }

    /**
     * Wrap file exists function
     *
     * @param string $file File to test
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * Delete temporary files, RTF and PDF
     *
     * @param string $fileName File name of the rtf file
     *
     * @return void
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

    /**
     * Print a file
     *
     * @param string $fileName    RTF file to print
     * @param string $jobTitle    Job name
     * @param string $destination Destination print queue
     * @param string $username    Username of person printing
     *
     * @return void
     * @throws NotReadyException
     * @throws RuntimeException
     */
    protected function printFile($fileName, $jobTitle, $destination, $username)
    {
        $printServer = $this->getConfigPrintServer();
        if ($printServer === false) {
            throw new RuntimeException('print.server is not set in config');
        }

        // convert to PDF using open office
        // 2>&1 redirect STDERR to STDOUT so that any errors are included in $outputPrint
        $commandPdf = sprintf(
            'soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp %s 2>&1',
            escapeshellarg($fileName)
        );
        $this->executeCommand($commandPdf, $outputPdf, $resultPdf);
        if ($resultPdf !== 0) {
            $exception = new NotReadyException('Error generating the PDF : ' . implode("\n", $outputPdf));
            $exception->setRetryAfter(60);
            throw $exception;
        }

        // If soffice has a problem with the RTF it can appear to have worked but it does actually create the PDF file
        $pdfFile = str_replace('.rtf', '.pdf', $fileName);
        if (!$this->fileExists($pdfFile)) {
            $exception = new NotReadyException('PDF file does not exist : ' . $pdfFile);
            $exception->setRetryAfter(60);
            throw $exception;
        }

        // send to CUPS server
        // 2>&1 redirect STDERR to STDOUT so that any errors are included in $outputPrint
        $commandPrint = sprintf(
            'lpr %s -H %s -C %s -h -P %s -U %s 2>&1',
            escapeshellarg($pdfFile),
            escapeshellarg($printServer),
            escapeshellarg($jobTitle),
            escapeshellarg($destination),
            escapeshellarg($username)
        );
        $this->executeCommand($commandPrint, $outputPrint, $resultPrint);
        if ($resultPrint !== 0) {
            $exception = new NotReadyException('Error executing lpr command : ' . implode("\n", $outputPrint));
            $exception->setRetryAfter(60);
            throw $exception;
        }
    }

    /**
     * Find the printer to be used for a user
     *
     * @param User     $user     User
     * @param Document $document Document
     *
     * @return Printer
     * @throws Exception
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
     * Get the printer queue for selfserve users
     *
     * @return string
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
     * @param Document $document  Document to be printed
     * @param int      $licenceId Licence to attach to
     *
     * @return void
     * @todo remove this method when stubbing no longer required
     * @codeCoverageIgnore
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
        $printDocument->setIssuedDate(new \Datetime());

        $this->getRepo()->save($printDocument);
    }
}
