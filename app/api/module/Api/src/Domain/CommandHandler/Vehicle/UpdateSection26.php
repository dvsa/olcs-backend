<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update a Vehicle Section 26 status
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateSection26 extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Vehicle';

    /**
     * @var \Olcs\Db\Service\Search\Search
     */
    private $searchService;

    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->searchService = $serviceLocator->getServiceLocator()->get('ElasticSearch\Search');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        foreach ($command->getIds() as $id) {
            /* @var $vehicle \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle */
            $vehicle = $this->getRepo()->fetchById($id);
            $vehicle->setSection26($command->getSection26() === 'Y');
            $this->getRepo()->save($vehicle);
        }

        $success = $this->searchService->updateVehicleSection26($command->getIds(), $command->getSection26() === 'Y');
        if ($success) {
            $this->result->addMessage('Search index updated');
        } else {
            $this->result->addMessage('Search index update error');
        }

        $this->result->addMessage(sprintf('Updated Section26 on %d Vehicle(s).', count($command->getIds())));

        return $this->result;
    }
}
