<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Export data to csv files for data.gov.uk
 *
 */
final class DataDvaNiExport extends AbstractDataExport
{
    use QueueAwareTrait;

    const FILE_DATETIME_FORMAT = 'YmdHis';
    const NI_OPERATOR_LICENCE = 'ni-operator-licence';


    /**
     * @var string
     */
    protected $repoServiceName = 'DataDvaNi';

    /**
     * @var string
     */
    private $reportName;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Repository\DataDvaNi
     */
    private $dataDvaNiRepo;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataDvaNiExport $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->path = (trim($command->getPath()) ?: $this->path);
        $this->reportName = $command->getReportName();

        $this->dataDvaNiRepo = $this->getRepo();

        if ($this->reportName === self::NI_OPERATOR_LICENCE) {
            $this->processNiOperatorLicences();
        } else {
            throw new \Exception(self::ERR_INVALID_REPORT);
        }

        return $this->result;
    }

    /**
     * Process Nothern Ireland operator licences
     *
     * @return void
     */
    private function processNiOperatorLicences()
    {

        $this->result->addMessage('Fetching data from DB for NI Operator Licences');
        $stmt = $this->dataDvaNiRepo->fetchNiOperatorLicences();

        $this->singleCsvFromStatement($stmt, 'NiGvLicences', '-');
    }



    /**
     * Create service
     *
     * @param \Dvsa\Olcs\Api\Domain\CommandHandlerManager $sm Service Manager
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        /** @var ServiceLocatorInterface $sl */
        $sl = $sm->getServiceLocator();
        $config = $sl->get('Config');

        $exportCfg = (!empty($config['data-dva-ni-export']) ? $config['data-dva-ni-export'] : []);

        if (isset($exportCfg['path'])) {
            $this->path = $exportCfg['path'];
        }

        return parent::createService($sm);
    }
}
