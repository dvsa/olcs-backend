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
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, UpdateNysiisName::class);
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
        $this->result->addMessage('TM NYSIIS currently disabled');
        return $this->result;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;
        
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->nysiisClient = $container->get(NysiisRestClient::class);
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
