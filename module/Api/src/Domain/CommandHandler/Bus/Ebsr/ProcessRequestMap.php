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
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

/**
 * Request new Ebsr map
 */
final class ProcessRequestMap extends AbstractCommandHandler
    implements AuthAwareInterface, TransactionedInterface, UploaderAwareInterface, TransExchangeAwareInterface
{
    use AuthAwareTrait;
    use UploaderAwareTrait;
    use TransExchangeAwareTrait;

    protected $repoServiceName = 'bus';

    protected $template;

    /**
     * @var
     */
    protected $fileStructure;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileStructure = $mainServiceLocator->get('EbsrFileStructure');

        $this->template = $mainServiceLocator->get('EbsrRequestMapTemplate');

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

        if ($ebsrSubmissions->isEmpty()) {
            //throw exception
        }

        /** @var EbsrSubmissionEntity $submission */
        $submission = $ebsrSubmissions->first();
        $file = $this->getUploader()->download($submission->getDocument()->getIdentifier());

        $xmlFilename = $this->fileStructure->filter($file);
        $template = $this->createRequestMapTemplate($xmlFilename, $command->getScale());
        $mapContent = $this->getTransExchange()->makeRequest($template);

        $this->handleSideEffects(
            [
                $this->generateDocument($mapContent, $busReg, basename($xmlFilename)),
                $this->createTaskCommand($busReg->getEbsrSubmissions()->first())
            ]
        );

        return $result;
    }

    /**
     * @param $xmlFilename
     * @param $scale
     * @return ProcessRequestMap
     */
    private function createRequestMapTemplate($xmlFilename, $scale)
    {
        $dir = dirname($xmlFilename);

        $substitutions = [
            'DocumentPath' => $dir,
            'DocumentName' => basename($xmlFilename),
            'OutputPath' => $dir,
            'RouteScale' => $scale
        ];

        $this->template->setVariables($substitutions);

        return $this->template;
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
            'description' => 'Ebsr Map'
        ];

        return UploadCmd::create($data);
    }

    /**
     * @param BusRegEntity $busReg
     * @return CreateTaskCmd
     */
    private function createTaskCommand(BusRegEntity $busReg)
    {
        $currentUser = $this->getCurrentUser();

        $actionDate = date('Y-m-d H:i:s');
        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => 'New route map available: [' . $busReg->getRegNo() . ']',
            'actionDate' => $actionDate,
            'assignedToUser' => $currentUser->getId(),
            'assignedToTeam' => 6,
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }
}
