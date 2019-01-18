<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Generator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Snapshot extends AbstractSurrenderCommandHandler implements TransactionedInterface
{

    /**
     * @var Generator
     */
    protected $snapshotService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->snapshotService = $serviceLocator->getServiceLocator()->get(Generator::class);

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $surrender = $this->getRepo()->fetchOneByLicenceId($command->getId(), Query::HYDRATE_OBJECT);
        $this->snapshotService->generate($surrender);

        $this->result->addMessage('Snapshot generated');

        return $this->result;
    }
}
