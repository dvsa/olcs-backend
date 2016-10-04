<?php

/**
 * Request new Ebsr map
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\UpdateTxcInboxPdf as UpdateTxcInboxPdfCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRequestMap as SendEbsrRequestMapCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareTrait;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareInterface;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareTrait;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Olcs\XmlTools\Xml\TemplateBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Exception\TransxchangeException;
use Zend\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;

/**
 * Request new Ebsr map
 */
final class ProcessRequestMap extends AbstractCommandHandler implements
    TransactionedInterface,
    UploaderAwareInterface,
    TransExchangeAwareInterface,
    ConfigAwareInterface,
    FileProcessorAwareInterface
{
    use UploaderAwareTrait;
    use TransExchangeAwareTrait;
    use ConfigAwareTrait;
    use FileProcessorAwareTrait;
    use QueueAwareTrait;

    const TASK_SUCCESS_DESC = 'New %s available: %s';
    const SCALE_DESC = ' (%s Scale)';

    const TXC_INBOX_TYPE_ROUTE = 'Route';
    const TXC_INBOX_TYPE_PDF = 'Pdf';

    protected $repoServiceName = 'Bus';

    protected $templatePaths;

    protected $documentDescriptions = [
        TransExchangeClient::REQUEST_MAP_TEMPLATE => "Route Track Map PDF",
        TransExchangeClient::TIMETABLE_TEMPLATE => "Timetable PDF",
        TransExchangeClient::DVSA_RECORD_TEMPLATE => "DVSA Record PDF",
    ];

    /**
     * @var TemplateBuilder
     */
    protected $templateBuilder;

    /**
     * Creates the service (injects template builder)
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->templateBuilder = $mainServiceLocator->get(TemplateBuilder::class);

        return parent::createService($serviceLocator);
    }

    /**
     * Transxchange map request
     *
     * @param CommandInterface|RequestMapCmd $command the command
     *
     * @throws TransxchangeException
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var BusRegEntity $busReg
         * @var EbsrSubmissionEntity $submission
         */
        $config = $this->getConfig();

        if (!isset($config['ebsr']['tmp_extra_path'])) {
            throw new TransxchangeException('No tmp directory specified in config');
        }

        $result = new Result();
        $busReg = $this->getRepo()->fetchUsingId($command);
        $ebsrSubmissions = $busReg->getEbsrSubmissions();
        $submission = $ebsrSubmissions->first();

        $this->getFileProcessor()->setSubDirPath($config['ebsr']['tmp_extra_path']);

        $xmlFilename = $this->getFileProcessor()->fetchXmlFileNameFromDocumentStore(
            $submission->getDocument()->getIdentifier(),
            true
        );

        $templateFile = $command->getTemplate();
        $scale = $command->getScale();

        $template = $this->createRequestMapTemplate($templateFile, $xmlFilename, $scale);

        try {
            $documents = $this->getTransExchange()->makeRequest($template);
        } catch (AdapterRuntimeException $e) {
            throw new TransxchangeException($e->getMessage());
        }

        if (!isset($documents['files'])) {
            throw new TransxchangeException('Invalid response from transXchange publisher');
        }

        $documentDesc = $this->getDocumentDescription($templateFile, $scale);

        foreach ($documents['files'] as $document) {
            $result->merge(
                $this->handleSideEffect(
                    $this->generateDocumentCmd($document, $busReg, $command->getUser(), $documentDesc)
                )
            );
        }

        $ebsrId = $submission->getId();

        //update txc inbox records with the new document id, create a task and send confirmation email
        $result->merge(
            $this->handleSideEffects(
                $this->createSideEffects($busReg, $result->getId('document'), $templateFile, $ebsrId, $documentDesc)
            )
        );

        return $result;
    }

    /**
     * Creates side effects, these are slightly different depending on which PDF we're generating
     *
     * @param BusRegEntity $busReg       Bus reg entity
     * @param int          $documentId   Document id
     * @param string       $templateFile Template file
     * @param int          $ebsrId       EBSR submission id
     * @param string       $documentDesc document description
     *
     * @return array
     */
    private function createSideEffects(BusRegEntity $busReg, $documentId, $templateFile, $ebsrId, $documentDesc)
    {
        $sideEffects = [
            $this->createTaskCmd($busReg, $documentDesc),
            $this->emailQueue(SendEbsrRequestMapCmd::class, ['id' => $ebsrId], $ebsrId)
        ];

        //add txc inbox pdf for all except timetables
        if ($templateFile !== TransExchangeClient::TIMETABLE_TEMPLATE) {
            $sideEffects[] = $this->createUpdateTxcInboxPdfCmd($busReg->getId(), $documentId, $templateFile);
        }

        return $sideEffects;
    }

    /**
     * Creates a transxchange xml file to request a map
     *
     * @param string $template    xml template
     * @param string $xmlFilename xml file name and path
     * @param string $scale       scale of route map
     *
     * @return string
     * @throws TransxchangeException
     */
    private function createRequestMapTemplate($template, $xmlFilename, $scale)
    {
        $config = $this->getConfig();

        if (!isset($config['ebsr']['transexchange_publisher']['templates'][$template])) {
            throw new TransxchangeException('Missing template');
        }

        $templatePath = $config['ebsr']['transexchange_publisher']['templates'][$template];
        $dir = dirname($xmlFilename);

        $substitutions = [
            'DocumentPath' => $dir,
            'DocumentName' => basename($xmlFilename),
            'OutputPath' => $dir,
            'RouteScale' => $scale
        ];

        return $this->templateBuilder->buildTemplate($templatePath, $substitutions);
    }

    /**
     * Creates a command to upload the transxchange map
     *
     * @param string       $document     document content
     * @param BusRegEntity $busReg       bus reg entity
     * @param int          $user         user id
     * @param string       $documentDesc document description
     *
     * @return UploadCmd
     */
    private function generateDocumentCmd($document, BusRegEntity $busReg, $user, $documentDesc)
    {
        $data = [
            'content' => base64_encode(file_get_contents($document)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_TRANSXCHANGE_PDF,
            'filename' => basename($document),
            'description' => $documentDesc,
            'user' => $user
        ];

        return UploadCmd::create($data);
    }

    /**
     * Creates a command to add a task
     *
     * @param BusRegEntity $busReg       bus reg entity
     * @param string       $documentDesc document description
     *
     * @return CreateTaskCmd
     */
    private function createTaskCmd(BusRegEntity $busReg, $documentDesc)
    {
        $desc = sprintf(self::TASK_SUCCESS_DESC, $documentDesc, $busReg->getRegNo());

        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => $desc,
            'actionDate' => date('Y-m-d'),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }

    /**
     * Creates a command to update TxcInbox records with the new document id
     * We don't do this for timetable pdfs
     *
     * @param int $busRegId     bus reg id
     * @param int $documentId   document id
     * @param int $templateFile the template file
     *
     * @return UpdateTxcInboxPdfCmd
     */
    private function createUpdateTxcInboxPdfCmd($busRegId, $documentId, $templateFile)
    {
        $pdfType = $templateFile === TransExchangeClient::REQUEST_MAP_TEMPLATE
                    ? self::TXC_INBOX_TYPE_ROUTE
                    : self::TXC_INBOX_TYPE_PDF;

        $data = [
            'id' => $busRegId,
            'document' => $documentId,
            'pdfType' => $pdfType
        ];

        return UpdateTxcInboxPdfCmd::create($data);
    }

    /**
     * Works out the document description (route maps also have a scale)
     *
     * @param string $templateFile template file
     * @param string $scale        scale of the route map
     *
     * @return string
     */
    public function getDocumentDescription($templateFile, $scale)
    {
        $scaleString = '';

        if ($templateFile ===  TransExchangeClient::REQUEST_MAP_TEMPLATE) {
            $scaleString = sprintf(self::SCALE_DESC, ucwords(strtolower($scale)));
        }

        return $this->documentDescriptions[$templateFile] . $scaleString;
    }

    /**
     * Returns the array of document descriptions
     *
     * @return array
     */
    public function getDocumentDescriptions()
    {
        return $this->documentDescriptions;
    }
}
