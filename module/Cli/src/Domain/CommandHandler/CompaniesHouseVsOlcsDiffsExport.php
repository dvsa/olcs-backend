<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Cli\Service\Utils\ExportToCsv;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Export difference between company house and OLCS data to csv files
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
final class CompaniesHouseVsOlcsDiffsExport extends AbstractCommandHandler
{
    const FILE_DATETIME_FORMAT = 'Ymd_His';

    protected $repoServiceName = 'CompanyHouseVsOlcsDiffs';

    /** @var  string */
    private $path;

    /** @var Repository\CompaniesHouseVsOlcsDiffs */
    private $chVsOlcsDiffs;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\CompaniesHouseVsOlcsDiffsExport $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->path = (trim($command->getPath()) ?: $this->path);

        $this->chVsOlcsDiffs = $this->getRepo();

        $this->processOrgNameDiff();
        $this->processOrgStatusNotActice();
        $this->processOrgAddressDiff();
        $this->processOrgOfficersDiff();

        return $this->result;
    }

    /**
     * create csv file with organisation officer (people) differences
     *
     * @return void
     */
    private function processOrgOfficersDiff()
    {
        $this->result->addMessage('Fetching data from DB for Company house and Organisation Officers differences');
        $stmt = $this->chVsOlcsDiffs->fetchOfficerDiffs();

        $this->makeCsvsFromStatement($stmt, 'CompanyOfficerDiffs');
    }

    /**
     * create csv file with organisation address differences
     *
     * @return void
     */
    private function processOrgAddressDiff()
    {
        $this->result->addMessage('Fetching data from DB for Company house and Organisation Address differences');
        $stmt = $this->chVsOlcsDiffs->fetchAddressDiffs();

        $this->makeCsvsFromStatement($stmt, 'CompanyAddressDiffs');
    }

    /**
     * create csv file with organisation whose name not match to name in companies house
     *
     * @return void
     */
    private function processOrgNameDiff()
    {
        $this->result->addMessage('Fetching data from DB for Company house and Organisation Name differences');
        $stmt = $this->chVsOlcsDiffs->fetchNameDiffs();

        $this->makeCsvsFromStatement($stmt, 'CompanyNameDiffs');
    }

    /**
     * create csv file with organisation which has 'not active' status in companies house
     *
     * @return void
     */
    private function processOrgStatusNotActice()
    {
        $this->result->addMessage('Fetching data from DB where Organisation not active in Company house');
        $stmt = $this->chVsOlcsDiffs->fetchWithNotActiveStatus();

        $this->makeCsvsFromStatement($stmt, 'CompanyNotActive');
    }

    /**
     * Fill csv files with data. Csv created by value of Key Field and File name.
     *
     * @param Statement|boolean $stmt     DB Records set or false if failure
     * @param string            $fileName main part of file name
     *
     * @return void
     */
    private function makeCsvsFromStatement($stmt, $fileName)
    {
        //  create csv file
        $filePath = $this->path . '/' . $fileName . '.csv';

        $this->result->addMessage('create csv file: ' . $filePath);
        $fh = ExportToCsv::createFile($filePath);

        if ($stmt instanceof Statement) {
            //  add title & first row
            $row = $stmt->fetch();

            if ($row !== false) {
                fputcsv($fh, array_keys($row));
                fputcsv($fh, $row);
            }

            //  add rows
            while (($row = $stmt->fetch()) !== false) {
                //  add rows to csv from pool
                fputcsv($fh, $row);
            }
        }

        //  close file
        fclose($fh);
    }

    /**
     * Create service
     *
     * @param \Dvsa\Olcs\Api\Domain\CommandHandlerManager $sm Handler Manager
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        /** @var ServiceLocatorInterface $sl */
        $sl = $sm->getServiceLocator();
        $config = $sl->get('Config');

        $exportCfg = (!empty($config['ch-vs-olcs-export']) ? $config['ch-vs-olcs-export'] : []);

        if (isset($exportCfg['path'])) {
            $this->path = $exportCfg['path'];
        }

        return parent::createService($sm);
    }
}
