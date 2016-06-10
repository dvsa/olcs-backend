<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Service\Nr\MsiResponse as MsiResponseService;
use Dvsa\Olcs\Transfer\Command\Cases\Si\SendResponse as SendErruResponseCmd;
use Zend\Http\Response;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Exception\RestResponseException;
use Zend\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;

/**
 * SendResponse
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class SendResponse extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Cases';

    protected $extraRepos = [
        'ErruRequest',
        'Document'
    ];

    /**
     * @var InrClient
     */
    protected $inrClient;

    /**
     * @var MsiResponseService
     */
    protected $msiResponseService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->inrClient = $mainServiceLocator->get(InrClientInterface::class);
        $this->msiResponseService = $mainServiceLocator->get(MsiResponseService::class);

        return parent::createService($serviceLocator);
    }

    /**
     * SendResponse
     *
     * @param CommandInterface $command
     * @throws RestResponseException
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var CasesEntity $case
         * @var SendErruResponseCmd $command
         */
        $case = $this->getRepo()->fetchById($command->getCase());

        //generate the xml to send to national register
        $xml = $this->msiResponseService->create($case);

        $erruRequest = $case->getErruRequest();

        $result = $this->handleSideEffect($this->CreateDocumentCommand($xml, $erruRequest->getWorkflowId()));

        $docRepo = $this->getRepo('Document');
        $document = $docRepo->fetchById($result->getId('document'));

        $erruRequest->setResponseDocument($document);
        $this->getRepo('ErruRequest')->save($erruRequest);

        //here is where we would expect the response from national register.
        try {
            $responseCode = $this->inrClient->makeRequest($xml);
        } catch (AdapterRuntimeException $e) {
            throw new RestResponseException('The was an error sending the INR response');
        }

        if ($responseCode !== Response::STATUS_CODE_202) {
            throw new RestResponseException('INR Http response code was ' . $responseCode);
        }

        $erruRequest->updateErruResponse(
            $this->getCurrentUser(),
            new \DateTime($this->msiResponseService->getResponseDateTime())
        );

        $this->getRepo('ErruRequest')->save($erruRequest);

        $result->addMessage('Msi Response sent');
        $result->addId('case', $case->getId());

        return $result;
    }

    /**
     * Returns an upload command to add the XML to the doc store
     *
     * @param string $content
     * @param string $workflowId this will be a GUID
     *
     * @return UploadCmd
     */
    private function CreateDocumentCommand($content, $workflowId)
    {
        $data = [
            'content' => base64_encode($content),
            'category' => CategoryEntity::CATEGORY_COMPLIANCE,
            'subCategory' => CategoryEntity::DOC_SUB_CATEGORY_NR,
            'filename' => 'msiresponse.xml',
            'description' => 'ERRU MSI response for workflow ID: ' . $workflowId
        ];

        return UploadCmd::create($data);
    }
}
