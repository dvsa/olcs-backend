<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Service\Exception;
use Dvsa\Olcs\Cli\Service\Utils\ExportToCsv;
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
    const ERR_NO_TRAFFIC_AREAS = 'Traffic areas is empty';

    const OPERATOR_LICENCE = 'operator-licence';
    const BUS_REGISTERED_ONLY = 'bus-registered-only';
    const BUS_VARIATION = 'bus-variation';

    const FILE_DATETIME_FORMAT = 'Ymd_His';

    protected $repoServiceName = 'DataGovUk';
    protected $extraRepos = ['TrafficArea'];

    /** @var  string */
    private $reportName;
    /** @var  string */
    private $path;

    /** @var Repository\DataGovUk */
    private $dataGovUkRepo;
    /** @var  array */
    private $csvPool = [];

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
        $this->reportName = $command->getReportName();

        $this->dataGovUkRepo = $this->getRepo();

        if ($this->reportName === self::OPERATOR_LICENCE) {
            $this->processOperatorLicences();
        } elseif ($this->reportName === self::BUS_REGISTERED_ONLY) {
            $this->processBusRegOnly();
        } else {
            throw new \Exception(self::ERR_INVALID_REPORT);
        }

        return $this->result;
    }

    private function processOperatorLicences()
    {
        $areas = array_map(
            function (TrafficAreaEntity $item) {
                return $item->getName();
            },
            $this->getTrafficAreas()
        );

        $this->result->addMessage('Fetching data from DB for Operator Licences');
        $stmt = $this->dataGovUkRepo->fetchOperatorLicences($areas);

        $this->makeCsvsFromStatement($stmt, 'GeographicRegion', 'OLBSLicenceReport');
    }

    private function processBusRegOnly()
    {
        $areas = array_map(
            function (TrafficAreaEntity $item) {
                return $item->getId();
            },
            $this->getTrafficAreas()
        );

        $this->result->addMessage('Fetching data from DB for Bus Registered Only');
        $stmt = $this->dataGovUkRepo->fetchBusRegisteredOnly($areas);

        $this->makeCsvsFromStatement($stmt, 'Current Traffic Area', 'Bus_RegisteredOnly');
    }

    /**
     * Fill csv files with data. Csv created by value of Key Field and File name.
     *
     * @param Statement $stmt db records set
     * @param string    $keyFld name of Key field in data set
     * @param string    $fileName main part of file name
     */
    private function makeCsvsFromStatement(Statement $stmt, $keyFld, $fileName)
    {
        //  add rows
        while (($row = $stmt->fetch()) !== false) {
            $key = $row[$keyFld];

            if (!isset($this->csvPool[$key])) {
                //  create csv file
                $filePath = $this->path . '/' . $fileName . '_' . $key . '.csv';

                $this->result->addMessage('create csv file: ' . $filePath);
                $fh = ExportToCsv::createFile($filePath);

                //  add title & first row
                fputcsv($fh, array_keys($row));
                fputcsv($fh, $row);

                $this->csvPool[$key] = $fh;

                continue;
            }

            //  add rows to csv from pool
            $fh = $this->csvPool[$key];

            fputcsv($fh, $row);
        }

        //  close files
        foreach ($this->csvPool as $fh) {
            fclose($fh);
        }
    }

    /**
     * Define list of traffic areas for which should be created report(s)
     *
     * @return TrafficAreaEntity[]
     */
    private function getTrafficAreas()
    {
        /** @var Repository\TrafficArea $repo */
        $repo = $this->getRepo('TrafficArea');

        //  remove Northern Ireland
        $items = array_filter(
            $repo->fetchAll(),
            function (TrafficAreaEntity $item) {
                return ($item->getId() !== TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
            }
        );

        if (count($items) === 0) {
            throw new Exception(self::ERR_NO_TRAFFIC_AREAS);
        }

        return $items;
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
