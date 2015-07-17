<?php

/**
 * Undo Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\UndoGrant as Cmd;
use Dvsa\Olcs\Api\Entity\Task\Task;

/**
 * Undo Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UndoGrant extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Task'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);
        $licence = $application->getLicence();

        $this->updateStatusAndDate($application, ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION);
        $this->updateStatusAndDate($licence, Licence::LICENCE_STATUS_UNDER_CONSIDERATION);
        $this->getRepo()->save($application);

        $result->merge($this->handleSideEffect(CancelLicenceFees::create(['id' => $licence->getId()])));
        $count = $this->closeGrantTask($application);

        $result->addMessage($count . ' Task(s) closed');

        return $result;
    }

    /**
     * @param ApplicationEntity|Licence $entity
     * @param $status
     */
    protected function updateStatusAndDate($entity, $status)
    {
        $entity->setStatus($this->getRepo()->getRefdataReference($status));
        $entity->setGrantedDate(null);
    }

    protected function closeGrantTask(ApplicationEntity $application)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq(
                'category',
                $this->getRepo()->getRefdataReference(Category::CATEGORY_APPLICATION)
            )
        );
        $criteria->andWhere(
            $criteria->expr()->eq(
                'subCategory',
                $this->getRepo()->getRefdataReference(Category::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE)
            )
        );
        $criteria->andWhere(
            $criteria->expr()->eq(
                'isClosed',
                'N'
            )
        );

        $grantTasks = $application->getTasks()->matching($criteria);

        /** @var Task $grantTask */
        foreach ($grantTasks as $grantTask) {
            $grantTask->setIsClosed('Y');
            $this->getRepo('Task')->save($grantTask);
        }

        return $grantTasks->count();
    }
}
