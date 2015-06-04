<?php

/**
 * Create a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'TransportManagerApplication';
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\User
     */
    protected $userRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManager
     */
    protected $tmRepo;


    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->userRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('User');
        $this->tmRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManager');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        try {
            $result = new Result();

            $this->getRepo()->beginTransaction();

            $tma = new TransportManagerApplication();
            $tma->setAction($command->getAction());
            $tma->setApplication($this->getRepo()->getReference(Application::class, $command->getApplication()));
            $tma->setTmApplicationStatus(
                $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_INCOMPLETE)
            );
            $tma->setTransportManager($this->getTransportManager($command));

            $this->getRepo()->save($tma);
            $result->addId('transportManagerApplication', $tma->getId());
            $result->addMessage('Transport Manager successfully created.');

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * Get the Transport Manager Enitity that the new TMA should reference
     *
     * @param \Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Create $command
     *
     * @return TransportManagerApplication
     * @throws \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    protected function getTransportManager(\Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Create $command)
    {
        if ($command->getTransportManager()) {
            // get reference to the transport manager parameter
            return $this->getRepo()->getReference(
                \Dvsa\Olcs\Api\Entity\Tm\TransportManager::class,
                $command->getTransportManager()
            );
        } else {
            if ($command->getUser()) {

                /* @var $user \Dvsa\Olcs\Api\Entity\User\User */
                $user = $this->userRepo->fetchById($command->getUser());

                // create a Transport Manager
                $transportManager = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
                $transportManager->setHomeCd($user->getContactDetails());
                $transportManager->setTmStatus(
                    $this->getRepo()->getRefdataReference(ContactDetails::TRANSPORT_MANAGER_STATUS_CURRENT)
                );
                $this->tmRepo->save($transportManager);

                // connect user to transport manager
                $user->setTransportManager($transportManager);
                $this->userRepo->save($user);

                return $transportManager;
            } else {
                throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                    ['You must specify either the transportManager or user']
                );
            }
        }
    }
}
