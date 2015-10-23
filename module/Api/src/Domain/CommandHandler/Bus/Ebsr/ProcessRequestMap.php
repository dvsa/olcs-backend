<?php

/**
 * Request new Ebsr map
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareTrait;
use Olcs\XmlTools\Xml\TemplateBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Request new Ebsr map
 */
final class ProcessRequestMap extends AbstractCommandHandler
    implements TransactionedInterface, UploaderAwareInterface, TransExchangeAwareInterface
{
    use UploaderAwareTrait;
    use TransExchangeAwareTrait;

    protected $repoServiceName = 'bus';

    protected $templatePaths;

    /**
     * @var TemplateBuilder
     */
    protected $templateBuilder;

    /**
     * @var FileProcessorInterface
     */
    protected $fileProcesor;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileProcesor = $mainServiceLocator->get(FileProcessorInterface::class);

        if (!isset($config['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }

        $config = $config['transexchange_publisher'];
        if (!isset($config['templates'])) {
            throw new \RuntimeException('Missing templates');
        }

        $this->templatePaths = $config['templates'];

        $this->templateBuilder = new TemplateBuilder();

        return parent::createService($serviceLocator);
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var RequestMapCmd $command */
        $result = new Result();

        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchUsingId($command);
        $ebsrSubmissions = $busReg->getEbsrSubmissions();

        /** @var EbsrSubmissionEntity $submission */
        $submission = $ebsrSubmissions->first();

        try {
            $xmlFilename = $this->fileProcesor->fetchXmlFileNameFromDocumentStore($submission->getDocument()->getIdentifier());

            $template = $this->createRequestMapTemplate($command->getTemplate(), $xmlFilename, $command->getScale());
            $documents = $this->getTransExchange()->makeRequest($template);

            if (!isset($documents['files'])) {
                throw new Exception\RunTimeException('Invalid response from transXchange publisher');
            }

            foreach ($documents['files'] as $document) {
                $result->merge(
                    $this->handleSideEffect($this->generateDocument(file_get_contents($document), $busReg, $document))
                );
            }

            if ($command->getUser() !== null) {
                $result->merge(
                    $this->handleSideEffect(
                        $this->createTaskCommand($busReg, $command->getUser())
                    )
                );
            }

        } catch (\Exception $e) {
            if ($command->getUser() !== null) {
                //@TODO handle case where there is no user... eg it's been uploaded by an operator
                $result->merge(
                    $this->handleSideEffect(
                        $this->createFailedTaskCommand($busReg, $command->getUser())
                    )
                );
            }

            throw $e;
        }


        return $result;
    }

    /**
     * @param $xmlFilename
     * @param $scale
     * @return ProcessRequestMap
     */
    private function createRequestMapTemplate($template, $xmlFilename, $scale)
    {
        $dir = dirname($xmlFilename);

        $substitutions = [
            'DocumentPath' => $dir,
            'DocumentName' => basename($xmlFilename),
            'OutputPath' => $dir,
            'RouteScale' => $scale
        ];

        return $this->templateBuilder->buildTemplate($this->templatePaths[$template], $substitutions);
    }

    /**
     * @param $content
     * @param BusRegEntity $busReg
     * @return UploadCmd
     */
    private function generateDocument($content, BusRegEntity $busReg, $filename)
    {
        $data = [
            'content' => base64_encode(trim($content)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_OTHER_DOCUMENTS,
            'filename' => $filename,
            'description' => 'TransXchange file'
        ];

        return UploadCmd::create($data);
    }

    /**
     * @param BusRegEntity $busReg
     * @return CreateTaskCmd
     */
    private function createTaskCommand(BusRegEntity $busReg, $userId)
    {
        $actionDate = date('Y-m-d H:i:s');
        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => 'New route map available: [' . $busReg->getRegNo() . ']',
            'actionDate' => $actionDate,
            'assignedToUser' => $userId,
            'assignedToTeam' => 6,
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }

    /**
     * @param BusRegEntity $busReg
     * @return CreateTaskCmd
     */
    private function createFailedTaskCommand(BusRegEntity $busReg, $userId)
    {
        $actionDate = date('Y-m-d H:i:s');
        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => 'Route map generation for: [' . $busReg->getRegNo() . '] failed',
            'actionDate' => $actionDate,
            'assignedToUser' => $userId,
            'assignedToTeam' => 6,
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }
}
