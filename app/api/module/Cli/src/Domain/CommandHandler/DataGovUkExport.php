<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Command\Email\SendPsvOperatorListReport;
use Dvsa\Olcs\Api\Domain\Command\Email\SendInternationalGoods as SendIntlGoodsEmailCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\AbstractDataExport;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Cli\Service\Utils\ExportToCsv;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Service\Exception;

/**
 * Export data to csv files for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
final class DataGovUkExport extends AbstractDataExport
{
    use QueueAwareTrait;

    const ERR_INVALID_REPORT = 'Invalid report name';
    const ERR_NO_TRAFFIC_AREAS = 'Traffic areas is empty';

    const OPERATOR_LICENCE = 'operator-licence';
    const BUS_REGISTERED_ONLY = 'bus-registered-only';
    const BUS_VARIATION = 'bus-variation';
    const PSV_OPERATOR_LIST = 'psv-operator-list';
    const INTERNATIONAL_GOODS = 'international-goods';

    const FILE_DATETIME_FORMAT = 'Ymd_His';

    /**
     * @var string
     */
    protected $repoServiceName = 'DataGovUk';

    /**
     * @var string
     */
    private $reportName;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Repository\DataGovUk
     */
    private $dataGovUkRepo;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport $command Command
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
        } elseif ($this->reportName === self::BUS_VARIATION) {
            $this->processBusVariation();
        } elseif ($this->reportName === self::PSV_OPERATOR_LIST) {
            $this->processPsvOperatorList();
        } elseif ($this->reportName === self::INTERNATIONAL_GOODS) {
            $this->processInternationalGoodsList();
        } else {
            throw new \Exception(self::ERR_INVALID_REPORT);
        }

        return $this->result;
    }

    /**
     * Process PSV Operator list and email
     *
     * @return Result
     */
    private function processPsvOperatorList()
    {
        $this->result->addMessage('Fetching data from DB for PSV Operators');
        $stmt = $this->dataGovUkRepo->fetchPsvOperatorList();

        $csvContent = $this->makeCsvForPsvOperatorList($stmt);

        $document = $this->handleSideEffect(
            $this->generatePsvOperatorListDocumentCmd($csvContent)
        );

        // Send email
        $emailQueue = $this->emailQueue(
            SendPsvOperatorListReport::class,
            ['id' => $document->getId('document')],
            $document->getId('document')
        );

        $email = $this->handleSideEffect($emailQueue);
        $this->result->merge($email);

        return $this->result;
    }

    /**
     * Process international goods list and email
     *
     * @return Result
     */
    private function processInternationalGoodsList()
    {
        $this->result->addMessage('Fetching data for international goods list');

        /** @var Repository\Licence $repo */
        $repo = $this->getRepo('Licence');
        $stmt = $repo->internationalGoodsReport();

        $csvContent = $this->singleCsvFromStatement($stmt, 'international_goods');

        $document = $this->handleSideEffect(
            $this->generateInternationalGoodsDocumentCmd($csvContent)
        );

        // Send email
        $emailQueue = $this->emailQueue(
            SendIntlGoodsEmailCmd::class,
            ['id' => $document->getId('document')],
            $document->getId('document')
        );

        $email = $this->handleSideEffect($emailQueue);
        $this->result->merge($email);

        return $this->result;
    }

    /**
     * Creates a command to upload the international goods csv
     *
     * @param string $document document content
     *
     * @return UploadCmd
     */
    private function generateInternationalGoodsDocumentCmd($document)
    {
        $data = [
            'content' => base64_encode($document),
            'category' => Category::CATEGORY_REPORT,
            'subCategory' => SubCategory::REPORT_SUB_CATEGORY_GV,
            'filename' => 'international-goods-list.csv',
            'description' => 'International goods list ' . date('d/m/Y'),
            'user' => \Dvsa\Olcs\Api\Rbac\PidIdentityProvider::SYSTEM_USER,
        ];

        return UploadCmd::create($data);
    }

    /**
     * Creates a command to upload the PSV Operator list CSV file
     *
     * @param string $document document content
     *
     * @return UploadCmd
     */
    private function generatePsvOperatorListDocumentCmd($document)
    {
        $data = [
            'content' => base64_encode($document),
            'category' => Category::CATEGORY_REPORT,
            'subCategory' => SubCategory::REPORT_SUB_CATEGORY_PSV,
            'filename' => 'psv-operator-list.csv',
            'description' => 'PSV Operator list',
            'user' => \Dvsa\Olcs\Api\Rbac\PidIdentityProvider::SYSTEM_USER,
        ];

        return UploadCmd::create($data);
    }

    /**
     * Process operator licences
     *
     * @return void
     */
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

    /**
     * Process Bus Reg Only
     *
     * @return void
     */
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
     * Process Bus Variation
     *
     * @return void
     */
    private function processBusVariation()
    {
        $areas = array_map(
            function (TrafficAreaEntity $item) {
                return $item->getId();
            },
            $this->getTrafficAreas()
        );

        $this->result->addMessage('Fetching data from DB for Bus Variation');
        $stmt = $this->dataGovUkRepo->fetchBusVariation($areas);

        $this->makeCsvsFromStatement($stmt, 'Current Traffic Area', 'Bus_Variation');
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

        $exportCfg = (!empty($config['data-gov-uk-export']) ? $config['data-gov-uk-export'] : []);

        if (isset($exportCfg['path'])) {
            $this->path = $exportCfg['path'];
        }

        return parent::createService($sm);
    }
}
