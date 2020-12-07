<?php

/**
 * Create a Transport Manager Delete Delta
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create a Transport Manager Delete Delta
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class TransportManagerDeleteDelta extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication
     */
    protected $tmaRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence
     */
    protected $tmlRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->tmaRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManagerApplication');

        $this->tmlRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManagerLicence');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        /* @var $application Application */
        $application = $this->getRepo()->fetchUsingId($command);

        $result = new Result();

        $applicationIds = [];
        foreach ($command->getTransportManagerLicenceIds() as $tmlId) {
            /* @var $tml \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence */
            $tml = $this->tmlRepo->fetchById($tmlId);

            // If the TML licence is different to the application licence something has gone wrong.
            if ($application->getLicence() !== $tml->getLicence()) {
                throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException(
                    "Transport Manager Licence ID $tmlId is not connected to this Application"
                );
            }

            // Create a new TMA record with action D(Delete)
            $tma = new TransportManagerApplication();
            $tma->setApplication($application);
            $tma->setTransportManager($tml->getTransportManager());
            $tma->setAction(TransportManagerApplication::ACTION_DELETE);

            $this->tmaRepo->save($tma);

            $result->addMessage('Transport manager application ID '. $tma->getId() .' delete Delata created');

            $applicationIds[$tma->getApplication()->getId()] = $tma->getApplication()->getId();
        }

        foreach ($applicationIds as $applicationId) {
            $result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::create(
                        ['id' => $applicationId, 'section' => 'transportManagers']
                    )
                )
            );
        }

        return $result;
    }
}
