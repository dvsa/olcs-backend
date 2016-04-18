<?php

/**
 * Request new Ebsr map
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
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
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareInterface;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareTrait;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Olcs\XmlTools\Xml\TemplateBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Request new Ebsr map
 */
final class ProcessRequestMap extends AbstractCommandHandler implements
    TransactionedInterface,
    UploaderAwareInterface,
    TransExchangeAwareInterface,
    EmailAwareInterface,
    ConfigAwareInterface,
    FileProcessorAwareInterface
{
    use UploaderAwareTrait;
    use TransExchangeAwareTrait;
    use EmailAwareTrait;
    use ConfigAwareTrait;
    use FileProcessorAwareTrait;
    use QueueAwareTrait;

    const TASK_SUCCESS_DESC = 'New route map available: %s';
    const TASK_FAIL_DESC = 'Route map generation for: %s failed';

    protected $repoServiceName = 'Bus';

    protected $templatePaths;

    /**
     * @var TemplateBuilder
     */
    protected $templateBuilder;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->templateBuilder = $mainServiceLocator->get(TemplateBuilder::class);

        return parent::createService($serviceLocator);
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var RequestMapCmd $command
         * @var BusRegEntity $busReg
         * @var EbsrSubmissionEntity $submission
         */
        $result = new Result();
        $busReg = $this->getRepo()->fetchUsingId($command);
        $ebsrSubmissions = $busReg->getEbsrSubmissions();
        $submission = $ebsrSubmissions->first();

        try {
            $xmlFilename = $this->getFileProcessor()->fetchXmlFileNameFromDocumentStore(
                $submission->getDocument()->getIdentifier()
            );

            $template = $this->createRequestMapTemplate($command->getTemplate(), $xmlFilename, $command->getScale());

            $documents = $this->getTransExchange()->makeRequest($template);

            if (!isset($documents['files'])) {
                throw new \Exception('Invalid response from transXchange publisher');
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
        } catch (\Exception $e) {
            $failedTaskCmd = $this->createTaskCmd($busReg, self::TASK_FAIL_DESC);
            $this->handleSideEffect($failedTaskCmd);

            throw ($e);
        }

        return $result;
    }

    /**
     * @param string $xmlFilename
     * @param string $scale
     * @throws \Exception
     * @return ProcessRequestMap
     */
    private function createRequestMapTemplate($template, $xmlFilename, $scale)
    {
        $config = $this->getConfig();

        if (!isset($config['ebsr']['transexchange_publisher']['templates'][$template])) {
            throw new \Exception('Missing template');
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
     * @param string $document
     * @param BusRegEntity $busReg
     * @param int $userId
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
     * @param BusRegEntity $busReg
     * @param string $description
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
     * @param int $busRegId
     * @param int $documentId
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
