<?php

/**
 * Schedule41Approve.php
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Schedule41\ApproveS4;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Schedule41Approve Command Handler
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Schedule41Approve extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->eq('agreedDate', null)
        );

        $s4s = $application->getS4s()->matching($criteria);

        $result = new Result();

        /** @var \Dvsa\Olcs\Api\Entity\Application\S4 $s4 */
        foreach ($s4s as $s4) {
            $result->merge(
                $this->handleSideEffect(
                    ApproveS4::create(
                        [
                            'id' => $s4->getId(),
                            'isTrueS4' => $command->getTrueS4(),
                            'outcome' => null
                        ]
                    )
                )
            );
        }

        $result->addMessage('Schedule 4/1 approved.');

        return $result;
    }
}
