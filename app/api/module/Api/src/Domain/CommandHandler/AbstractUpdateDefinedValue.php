<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Abstract update command handler
 * Designed for "one question per page"
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class AbstractUpdateDefinedValue extends AbstractCommandHandler
{
    protected $repoServiceName = 'changeMe';
    protected $entityMethodName = 'changeMe';
    protected $definedValue = 'changeMe';
    protected $isRefData = false;

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $entityMethod = $this->entityMethodName;

        /** @var RepositoryInterface $repo */
        $repo = $this->getRepo($this->repoServiceName);

        //fetch the record from the DB
        $recordObject = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $value = $this->definedValue;

        //convert to ref data if necessary
        if ($this->isRefData) {
            $value = $this->refDataOrNull($value);
        }

        //call update method on the entity
        $recordObject->$entityMethod($value);

        //save the record
        $repo->save($recordObject);

        $this->result->addId($this->repoServiceName, $recordObject->getId());
        $this->result->addMessage($this->repoServiceName . ' updated');

        return $this->result;
    }
}
