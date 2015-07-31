<?php

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\SetDefaultTrafficAreaAndEnforcementArea as SetTaAndEa;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateOperatingCentre as Cmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateOperatingCentre extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = [
        'Document',
        'OperatingCentre',
        'ApplicationOperatingCentre'
    ];

    /**
     * @var \Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->helper = $mainServiceLocator->get('OperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $this->helper->validate($application, $command);

        // Create an OC record
        $operatingCentre = $this->helper->createOperatingCentre(
            $command,
            $this->getCommandHandler(),
            $this->result,
            $this->getRepo('OperatingCentre')
        );

        // Link, unlinked documents to the OC
        $this->helper->saveDocuments($application, $operatingCentre, $this->getRepo('Document'));

        // Create a AOC record
        $this->createApplicationOperatingCentre($application, $operatingCentre, $command);

        $data = ['id' => $command->getApplication(), 'operatingCentre' => $operatingCentre->getId()];
        $this->result->merge($this->handleSideEffect(SetTaAndEa::create($data)));

        $completionData = ['id' => $command->getApplication(), 'section' => 'operatingCentres'];
        $this->result->merge($this->handleSideEffect(UpdateApplicationCompletionCmd::create($completionData)));

        return $this->result;
    }

    /**
     * @param Application $application
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createApplicationOperatingCentre(
        Application $application,
        OperatingCentre $operatingCentre,
        Cmd $command
    ) {
        $aoc = new ApplicationOperatingCentre($application, $operatingCentre);
        $aoc->setAction('A');
        $application->addOperatingCentres($aoc);

        $this->helper->updateOperatingCentreLink(
            $aoc,
            $application,
            $command,
            $this->getRepo('ApplicationOperatingCentre')
        );
    }
}
