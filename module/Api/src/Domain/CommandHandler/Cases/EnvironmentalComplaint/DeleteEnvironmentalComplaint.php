<?php

/**
 * Delete Environmental Complaint
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\EnvironmentalComplaint;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cases\EnvironmentalComplaint\DeleteEnvironmentalComplaint as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Delete Environmental Complaint
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class DeleteEnvironmentalComplaint extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Complaint';

    /**
     * Delete complaint
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $complaint = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->getRepo()->delete($complaint);

        $result->addMessage('Complaint deleted');

        return $result;
    }
}
