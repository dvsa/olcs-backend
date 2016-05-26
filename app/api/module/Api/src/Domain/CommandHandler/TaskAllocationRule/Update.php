<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TaskAllocationRule;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TaskAllocationRule\Update as Cmd;

/**
 * Update TaskAllocationRule
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'TaskAllocationRule';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Cmd */
        $repo = $this->getRepo();

        /* @var $taskAllocationRule \Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule */
        $taskAllocationRule = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $taskAllocationRule->setCategory(
            $repo->getReference(\Dvsa\Olcs\Api\Entity\System\Category::class, $command->getCategory())
        );
        $taskAllocationRule->setTeam(
            $repo->getReference(\Dvsa\Olcs\Api\Entity\User\Team::class, $command->getTeam())
        );
        $taskAllocationRule->setUser(
            $repo->getReference(\Dvsa\Olcs\Api\Entity\User\User::class, $command->getUser())
        );
        $taskAllocationRule->setGoodsOrPsv($repo->getRefdataReference($command->getGoodsOrPsv()));
        $isMlh = null;
        if ($command->getIsMlh() === 'Y') {
            $isMlh = true;
        } elseif ($command->getIsMlh() === 'N') {
            $isMlh = false;
        }
        $taskAllocationRule->setIsMlh($isMlh);
        $taskAllocationRule->setTrafficArea(
            $repo->getReference(\Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea::class, $command->getTrafficArea())
        );

        $repo->save($taskAllocationRule);

        $this->result->addId('task-allocation-rule', $taskAllocationRule->getId());
        $this->result->addMessage('TaskAllocationRule updated');

        return $this->result;
    }
}
