<?php

/**
 * Grant Goods
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateGrantFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Grant Goods
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GrantGoods extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $this->updateStatusAndDate($application, ApplicationEntity::APPLICATION_STATUS_GRANTED);
        $result->addMessage('Application status updated');

        $this->updateStatusAndDate($application->getLicence(), Licence::LICENCE_STATUS_GRANTED);
        $result->addMessage('Licence status updated');

        $this->getRepo()->save($application);

        $result->merge($this->createGrantFee($application));

        return $result;
    }

    /**
     * @param ApplicationEntity|Licence $entity
     * @param $status
     */
    protected function updateStatusAndDate($entity, $status)
    {
        $entity->setStatus($this->getRepo()->getRefdataReference($status));
        $entity->setGrantedDate(new DateTime());
    }

    protected function createGrantFee($applicationId)
    {
        $data = [
            'id' => $applicationId
        ];

        return $this->handleSideEffect(CreateGrantFee::create($data));
    }
}
