<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\Serializer\Adapter\Json as ZendJson;
use Dvsa\Olcs\Api\Domain\Command\Tm\UpdateNysiisName as UpdateNysiisNameCmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Service\Data\Nysiis as NysiisService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\Logger;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

/**
 * Queue request to update TM name with Nysiis values
 */
final class UpdateNysiisName extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TransportManager';

    /**
     * Service to connect to Nysiis servers
     *
     * @var NysiisService
     */
    protected $nysiisService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        try {
            $this->nysiisService = $mainServiceLocator->get(NysiisService::class);
        } catch (\Exception $e) {
            // do nothing, let the queue handle the Nysiis exception in order to trigger a message requeue
            throw new NysiisException('Failed to create Nysiis service: ' . $e->getMessage());
        }

        return parent::createService($serviceLocator);
    }

    /**
     * Command to queue a request to update TM with Nysiis data
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var TransportManager $transportManager
         * @var UpdateNysiisNameCmd $command
         */
        $transportManager = $this->getRepo()->fetchUsingId($command);
        $person = $transportManager->getHomeCd()->getPerson();

        $nysiisData = $this->requestNysiisData(
            [
                'nysiisForename' => $person->getForename(),
                'nysiisFamilyname' => $person->getFamilyName()
            ]
        );
        $transportManager->setNysiisForename($nysiisData['forename']);
        $transportManager->setNysiisFamilyName($nysiisData['familyName']);

        $this->getRepo('TransportManager')->save($transportManager);

        $this->result->addMessage('TM NYIIS name was requested and updated');

        return $this->result;
    }

    /**
     * Connect to Nysiis with given params and return values returned by Nysiis
     * @param $nysiisParams
     * @return array
     * @throws NysiisException
     */
    private function requestNysiisData($nysiisParams)
    {
        if ($this->nysiisService instanceof NysiisService) {
            $nysiisData = $this->nysiisService->getNysiisSearchKeys($nysiisParams);

            // connect to Nysiis here and return whatever Nysiis returns
            return [
                'forename' => $nysiisData->FirstName,
                'familyName' => $nysiisData->FamilyName
            ];
        }

        throw new NysiisException('Failed to instantiate SOAP Client. Service Down.');
    }
}
