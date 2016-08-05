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

    const TASK_SUCCESS_DESC = 'New route map available: %s';

    protected $repoServiceName = 'Bus';

    protected $templatePaths;

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

        $template = $this->createRequestMapTemplate($command->getTemplate(), $xmlFilename, $command->getScale());

        try {
            $documents = $this->getTransExchange()->makeRequest($template);
        } catch (AdapterRuntimeException $e) {
            throw new TransxchangeException($e->getMessage());
        }

        if (!isset($documents['files'])) {
            throw new TransxchangeException('Invalid response from transXchange publisher');
        }

        foreach ($documents['files'] as $document) {
            $result->merge(
                $this->handleSideEffect($this->generateDocumentCmd($document, $busReg, $command->getUser()))
            );
        }

        $ebsrId = $submission->getId();

        //update txc inbox records with the new document id, create a task and send confirmation email
        $result->merge(
            $this->handleSideEffects(
                [
                    $this->createUpdateTxcInboxPdfCmd($busReg->getId(), $result->getId('document')),
                    $this->createTaskCmd($busReg, self::TASK_SUCCESS_DESC),
                    $this->emailQueue(SendEbsrRequestMapCmd::class, ['id' => $ebsrId], $ebsrId)
                ]
            )
        );

        return $result;
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
     * @param string       $document document content
     * @param BusRegEntity $busReg   bus reg entity
     * @param int          $user     user id
     *
     * @return UploadCmd
     */
    private function generateDocumentCmd($document, BusRegEntity $busReg, $user)
    {
        $data = [
            'content' => base64_encode(file_get_contents($document)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_OTHER_DOCUMENTS,
            'filename' => basename($document),
            'description' => 'TransXchange file',
            'user' => $user
        ];

        return UploadCmd::create($data);
    }

    /**
     * Creates a command to add a task
     *
     * @param BusRegEntity $busReg      bus reg entity
     * @param string       $description task description
     *
     * @return CreateTaskCmd
     */
    private function createTaskCmd(BusRegEntity $busReg, $description)
    {
        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => sprintf($description, $busReg->getRegNo()),
            'actionDate' => date('Y-m-d'),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }

    /**
     * Creates a command to update TxcInbox records with the new document id
     *
     * @param int $busRegId   bus reg id
     * @param int $documentId document id
     *
     * @return UpdateTxcInboxPdfCmd
     */
    private function createUpdateTxcInboxPdfCmd($busRegId, $documentId)
    {
        $data = [
            'id' => $busRegId,
            'document' => $documentId
        ];

        return UpdateTxcInboxPdfCmd::create($data);
    }
}
