<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Domain\Command\Cases\Si\SendResponse as SendResponseCmd;
use Laminas\Http\Response;
use Laminas\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\InrClientException;

/**
 * SendResponse
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class SendResponse extends AbstractCommandHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'ErruRequest';

    protected $extraRepos = [
        'Document'
    ];

    /**
     * @var InrClient
     */
    protected $inrClient;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->inrClient = $mainServiceLocator->get(InrClientInterface::class);

        return parent::createService($serviceLocator);
    }

    /**
     * SendResponse
     *
     * @param CommandInterface $command the command
     *
     * @throws InrClientException
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var ErruRequestEntity $erruRequest
         * @var SendResponseCmd $command
         */
        $erruRequest = $this->getRepo()->fetchUsingId($command);

        /** @var File $xmlFile */
        $xmlFile = $this->getUploader()->download($erruRequest->getResponseDocument()->getIdentifier());

        //here is where we would expect the response from national register.
        try {
            $responseCode = $this->inrClient->makeRequest($xmlFile->getContent());
        } catch (AdapterRuntimeException $e) {
            $this->updateStatus($erruRequest, ErruRequestEntity::FAILED_CASE_TYPE);
            throw new InrClientException('There was an error sending the INR response ' . $e->getMessage());
        }

        $this->inrClient->close();

        if ($responseCode !== Response::STATUS_CODE_202) {
            $this->updateStatus($erruRequest, ErruRequestEntity::FAILED_CASE_TYPE);
            throw new InrClientException('INR Http response code was ' . $responseCode);
        }

        $this->updateStatus($erruRequest, ErruRequestEntity::SENT_CASE_TYPE);

        $result = new Result();
        $result->addMessage('Msi Response sent');
        $result->addId('Erru request', $erruRequest->getId());

        return $result;
    }

    /**
     * Sets the erru request status to the specified status key
     *
     * @param ErruRequestEntity $erruRequest erru request entity
     * @param string            $statusKey   erru request status key
     *
     * @return ErruRequestEntity
     */
    private function updateStatus(ErruRequestEntity $erruRequest, $statusKey)
    {
        $erruStatus = $this->getRepo()->getRefdataReference($statusKey);
        $erruRequest->setMsiType($erruStatus);
        $this->getRepo('ErruRequest')->save($erruRequest);

        return $erruRequest;
    }
}
