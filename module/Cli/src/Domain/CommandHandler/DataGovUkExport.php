<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Service\Exception;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Export data to csv files for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
final class DataGovUkExport extends AbstractCommandHandler
{
    const ERR_INVALID_REPORT = 'Invalid report name';
    const ERR_CANT_CREATE_DIR = 'Can\'t create directory to csv file: ';
    const ERR_CANT_CREATE_FILE = 'Can\'t create file ';

    const OPERATOR_LICENCE = 'operator-licence';
    const BUS_REG_LICENCE_CURRENT = 'bus-reg-licence-current';
    const BUS_REG_LICENCE_ALL = 'bus-reg-licence-all';

    const FILE_DATETIME_FORMAT = 'Ymd_His';

    protected $repoServiceName = 'DataGovUk';

    /** @var  string */
    private $path;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->path = (trim($command->getPath()) ?: $this->path);

        $reportName = $command->getReportName();

        /** @var Repository\DataGovUk $repo */
        $repo = $this->getRepo();

        if ($reportName === self::OPERATOR_LICENCE) {
            $this->result->addMessage('Export operator licences to file for data.gov.uk:');

            $this->result->addMessage("\t- Fetching data from DB");
            $stmt = $repo->fetchOperatorLicences();

            $this->exportToCsv(self::OPERATOR_LICENCE, $stmt);

            unset($stmt);
        } else {
            throw new \Exception(self::ERR_INVALID_REPORT);
        }

        $this->result->addMessage('done.');

        return $this->result;
    }

    private function exportToCsv($fileName, Statement $stmt)
    {
        $now = new DateTime();

        $filePath = $this->path . '/' . $fileName . '-' . $now->format(self::FILE_DATETIME_FORMAT) . '.csv';
        $this->result->addMessage("\t- Export data to csv file " . $filePath);

        //  create folders
        $dir = dirname($filePath);
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new Exception(self::ERR_CANT_CREATE_DIR . $filePath);
        }

        //  open file & add rows
        $output = @fopen($filePath, 'w');
        if ($output === false) {
            throw new Exception(self::ERR_CANT_CREATE_FILE . $filePath);
        }

        $isColumnNamesSet = false;
        while (($row = $stmt->fetch()) !== false) {
            //  add first row with column names
            if (!$isColumnNamesSet) {
                fputcsv($output, array_keys($row));

                $isColumnNamesSet = true;
            }

            //  add row
            fputcsv($output, $row);
        }

        fclose($output);
    }

    /**
     * Create service
     *
     * @param \Dvsa\Olcs\Api\Domain\CommandHandlerManager $sm
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        /** @var ServiceLocatorInterface $sl */
        $sl = $sm->getServiceLocator();

        $config = $sl->get('Config');

        $exportCfg = (!empty($config['data-gov-uk-export']) ? $config['data-gov-uk-export'] : []);

        if (isset($exportCfg['path'])) {
            $this->path = $exportCfg['path'];
        }

        return parent::createService($sm);
    }
}
