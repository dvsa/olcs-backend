<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Tm\UpdateNysiisName as UpdateNysiisNameCmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisRestClient;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

/**
 * Queue request to update TM name with Nysiis values
 */
final class UpdateNysiisName extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TransportManager';

    /**
     * Client to connect to Nysiis servers
     *
     * @var NysiisRestClient
     */
    private $nysiisClient;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->nysiisClient = $mainServiceLocator->get(NysiisRestClient::class);

        return parent::createService($serviceLocator);
    }

    /**
     * Command to queue a request to update TM with Nysiis data
     *
     * @param CommandInterface|UpdateNysiisNameCmd $command command to update nysiis name
     *
     * @return Result
     * @throws NysiisException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var TransportManager $transportManager
         */
        $transportManager = $this->getRepo()->fetchUsingId($command);
        $person = $transportManager->getHomeCd()->getPerson();

        $nysiisData = $this->nysiisClient->makeRequest($person->getForename(), $person->getFamilyName());

        $transportManager->setNysiisForename($nysiisData['nysiisFirstName']);
        $transportManager->setNysiisFamilyName($nysiisData['nysiisFamilyName']);

        $this->getRepo('TransportManager')->save($transportManager);

        $this->result->addMessage('TM NYSIIS name was requested and updated');

        return $this->result;
    }
}
