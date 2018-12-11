<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\GdsVerify;

/**
 * ProcessResponse
 */
class ProcessSignatureResponse extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DigitalSignature';

    protected $extraRepos = ['Application', 'ContinuationDetail', 'TransportManagerApplication', 'Licence'];

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
        try {
            $attributes = $this->getGdsVerifyService()->getAttributesFromResponse($command->getSamlResponse());
        } catch (GdsVerify\Exception $e) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException($e->getMessage(), 0, $e);
        }

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


        if ($command->getApplication() && empty($command->getTransportManagerApplication())) {
            $this->updateApplication($command->getApplication(), $digitalSignature);
            $this->result->addMessage('Digital signature added to application ' . $command->getApplication());
        }

        if ($command->getContinuationDetail()) {
            $this->updateContinuationDetail($command->getContinuationDetail(), $digitalSignature);
            $this->result->addMessage(
                'Digital signature added to continuationDetail' . $command->getContinuationDetail()
            );
        }

        if ($command->getTransportManagerApplication()) {
            $this->updateTMApplication(
                $command->getTransportManagerApplication(),
                $digitalSignature,
                $command->getRole()
            );

            $this->result->addMessage('Digital Signature added to transport manager application' . $command->getTransportManagerApplication());
        }

        if ($command->getLicence()) {
            $result = $this->updateSurrender(
                $digitalSignature,
                $command->getLicence()
            );
            $this->result->addMessage($result);

            $this->result->addMessage('Digital Signature added to surrender' . $command->getLicence());
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
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
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
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
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

    /**
     * updateTMApplication
     *
     * @param int                     $transportManagerApplicationId
     * @param Entity\DigitalSignature $digitalSignature
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateTMApplication(
        int $transportManagerApplicationId,
        Entity\DigitalSignature $digitalSignature,
        $role
    ): void {

        /** @var Entity\Tm\TransportManagerApplication $transportManagerApplication */
        $transportManagerApplication = $this->getRepo('TransportManagerApplication')->fetchById($transportManagerApplicationId);

        switch ($role) {
            case 'tma_sign_as_tm':
                $nextStatus = $transportManagerApplication::STATUS_TM_SIGNED;
                $this->setTmSignature($digitalSignature, $transportManagerApplication);
                break;
            case 'tma_sign_as_op':
                $nextStatus = $transportManagerApplication::STATUS_RECEIVED;
                $this->setOperatorSignature($digitalSignature, $transportManagerApplication);
                break;
            case 'tma_sign_as_top':
                $nextStatus = $transportManagerApplication::STATUS_RECEIVED;
                $this->setTmSignature($digitalSignature, $transportManagerApplication);
                $this->setOperatorSignature($digitalSignature, $transportManagerApplication);
                break;
        }


        $this->getRepo('TransportManagerApplication')->save($transportManagerApplication);

        $this->handleSideEffect(
            \Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Submit::create(
                ['id' => $transportManagerApplicationId, 'nextStatus' => $nextStatus]
            )
        );
    }


    private function setTmStatus(Entity\Tm\TransportManagerApplication $transportManagerApplication, $status)
    {
        $transportManagerApplication->setTmApplicationStatus($this->getRepo()->getRefdataReference($status));
    }

    /**
     * setTmSignature
     *
     * @param Entity\DigitalSignature $digitalSignature
     * @param                         $transportManagerApplication
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function setTmSignature(
        Entity\DigitalSignature $digitalSignature,
        Entity\Tm\TransportManagerApplication $transportManagerApplication
    ): void {

        $transportManagerApplication->setTmDigitalSignature($digitalSignature);
        $transportManagerApplication->setTmSignatureType($this->getRepo()->getRefdataReference(Entity\System\RefData::SIG_DIGITAL_SIGNATURE));
    }

    /**
     * setOperatorSignature
     *
     * @param Entity\DigitalSignature $digitalSignature
     * @param                         $transportManagerApplication
     *
     * @throws RuntimeException
     */
    private function setOperatorSignature(Entity\DigitalSignature $digitalSignature, $transportManagerApplication): void
    {
        $transportManagerApplication->setOpDigitalSignature($digitalSignature);
        $transportManagerApplication->setOpSignatureType($this->getRepo()->getRefdataReference(Entity\System\RefData::SIG_DIGITAL_SIGNATURE));
    }


    private function updateSurrender(Entity\DigitalSignature $digitalSignature, int $licenceId)
    {
        $result = $this->handleSideEffect(\Dvsa\Olcs\Transfer\Command\Surrender\Update::create(
            [
                'digitalSignature' => $digitalSignature,
                'id' => $licenceId,
                'status' => Entity\Surrender::SURRENDER_STATUS_SIGNED,
                'signatureType' => Entity\System\RefData::SIG_DIGITAL_SIGNATURE
            ]
        ));

        /**
         * @var Entity\Licence\Licence $licence
         */
        $licence = $this->getRepo('Licence')->fetchById($licenceId);
        $licence->setStatus(Entity\Licence\Licence::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION);
        $licence->save();
        return $result;
    }
}
