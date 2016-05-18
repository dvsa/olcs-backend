<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TaskAllocationRule\Create as Cmd;

/**
 * Create TaskAllocationRule
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'TaskAllocationRule';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Cmd */
        $repo = $this->getRepo();

        $taskAllocationRule = new \Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule();
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
        $taskAllocationRule->setIsMlh($command->getIsMlh() === 'Y');
        $taskAllocationRule->setTrafficArea(
            $repo->getReference(\Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea::class, $command->getTrafficArea())
        );

        $repo->save($taskAllocationRule);

        $this->result->addId('task-allocation-rule', $taskAllocationRule->getId());
        $this->result->addMessage('TaskAllocationRule created');

        return $this->result;
    }
}
