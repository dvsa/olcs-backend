<?php
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantPeople as GrantPeopleCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Variation\GrantDirectorChange as Cmd;


class GrantDirectorChange extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Cmd */
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $result->merge($this->createSnapshot($command->getId()));

        $application->setStatus($this->getRepo()->getRefdataReference(ApplicationEntity::APPLICATION_STATUS_VALID));
        $application->setGrantedDate(new DateTime());

        $this->getRepo()->save($application);

        $result->merge($this->proxyCommand($command, GrantPeopleCmd::class));

        return $result;
    }

    private function createSnapshot($applicationId)
    {
        $data = [
            'id' => $applicationId,
            'event' => CreateSnapshot::ON_GRANT
        ];

        return $this->handleSideEffectAsSystemUser(CreateSnapshot::create($data));
    }
}
