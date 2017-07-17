<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity;

/**
 * ProcessResponse
 */
class ProcessSignatureResponse extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DigitalSignature';

    protected $extraRepos = ['Application', 'ContinuationDetail'];

    /** @var  \Dvsa\Olcs\GdsVerify\Service\GdsVerify */
    private $gdsVerifyService;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->setGdsVerifyService($mainServiceLocator->get(\Dvsa\Olcs\GdsVerify\Service\GdsVerify::class));

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\GdsVerify\ProcessSignatureResponse $command Command to handle
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $attributes = $this->getGdsVerifyService()->getAttributesFromResponse($command->getSamlResponse());

        if (!$attributes->isValidSignature()) {
            // Not sure this is the right response code, for signature not valid? But it will work
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException(
                'Verify response does not qualify as a valid signature'
            );
        }

        $digitalSignature = new Entity\DigitalSignature();
        $digitalSignature->setAttributesArray($attributes->getArrayCopy())
            ->setSamlResponse(base64_decode($command->getSamlResponse()));
        $this->getRepo()->save($digitalSignature);
        $this->result->addMessage('Digital signature created');

        if ($command->getApplication()) {
            $this->updateApplication($command->getApplication(), $digitalSignature);
            $this->result->addMessage('Digital signature added to application '. $command->getApplication());
        }

        if ($command->getContinuationDetail()) {
            $this->updateContinuationDetail($command->getContinuationDetail(), $digitalSignature);
            $this->result->addMessage(
                'Digital signature added to continuationDetail'. $command->getContinuationDetail()
            );
        }

        return $this->result;
    }

    /**
     * Get the GDS Verify service
     *
     * @return \Dvsa\Olcs\GdsVerify\Service\GdsVerify
     */
    public function getGdsVerifyService()
    {
        return $this->gdsVerifyService;
    }

    /**
     * Set the GDS Verify service
     *
     * @param \Dvsa\Olcs\GdsVerify\Service\GdsVerify $gdsVerifyService Verify Service
     *
     * @return void
     */
    public function setGdsVerifyService(\Dvsa\Olcs\GdsVerify\Service\GdsVerify $gdsVerifyService)
    {
        $this->gdsVerifyService = $gdsVerifyService;
    }

    /**
     * Update application with the digital signature
     *
     * @param int                     $applicationId    Application ID
     * @param Entity\DigitalSignature $digitalSignature Digital signature
     *
     * @return void
     */
    private function updateApplication($applicationId, Entity\DigitalSignature $digitalSignature)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo('Application')->fetchById($applicationId);
        $application->setDigitalSignature($digitalSignature);
        $application->setSignatureType(
            $this->getRepo()->getRefdataReference(Entity\Application\Application::SIG_DIGITAL_SIGNATURE)
        );
        $this->getRepo('Application')->save($application);

        $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::create(
                ['id' => $applicationId, 'section' => 'undertakings']
            )
        );
    }

    /**
     * Update continuationDetail with the digital signature
     *
     * @param int                     $continuationDetailId Continuation detail ID
     * @param Entity\DigitalSignature $digitalSignature     Digital signature
     *
     * @return void
     */
    private function updateContinuationDetail($continuationDetailId, Entity\DigitalSignature $digitalSignature)
    {
        /** @var Entity\Licence\ContinuationDetail $continuationDetail */
        $continuationDetail = $this->getRepo('ContinuationDetail')->fetchById($continuationDetailId);
        $continuationDetail->setDigitalSignature($digitalSignature);
        $continuationDetail->setIsDigital(true);
        $continuationDetail->setSignatureType(
            $this->getRepo()->getRefdataReference(Entity\System\RefData::SIG_DIGITAL_SIGNATURE)
        );
        $this->getRepo('ContinuationDetail')->save($continuationDetail);
    }
}
