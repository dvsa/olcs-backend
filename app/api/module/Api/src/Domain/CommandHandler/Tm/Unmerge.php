<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Domain\Repository\AbstractReadonlyRepository;

/**
 * Unmerge Transport Manager
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Unmerge extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManager';
    protected $extraRepos = [
        'TransportManagerApplication',
        'TransportManagerLicence',
        'Cases',
        'Document',
        'Task',
        'Note',
        'EventHistory',
        'User'
    ];

    /**
     * Record of changes made during the merge
     * @var array
     */
    protected $changes = [];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $tm TransportManager */
        $tm = $this->getRepo()->fetchById($command->getId());

        $this->validate($tm);

        $this->unmerge($tm);

        // Remove the previous merge details
        $tm->setMergeToTransportManager(null);
        $tm->setMergeDetails(null);

        // Remove the removed date from the current transport manager
        $tm->setRemovedDate(null);
        $tm->setTmStatus(
            $this->getRepo()->getRefdataReference(TransportManager::TRANSPORT_MANAGER_STATUS_CURRENT)
        );
        $this->getRepo()->save($tm);

        $result = new Result();
        $result->addMessage(
            sprintf(
                'Unmerged Transport Manager id %d',
                $tm->getId()
            )
        );

        return $result;
    }

    /**
     * Validate the donor and recipient Transport Managers
     *
     * @param TransportManager $tm
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    protected function validate(TransportManager $tm)
    {
        if (empty($tm->getMergeToTransportManager())) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                ['TM_UNMERGE_NOT_MERGED' => 'Cannot unmerge as TM was not merged']
            );
        }
    }

    /**
     * Unmerge all the entities that previously were associated with the TM
     *
     * @param TransportManager $tm
     * @return void
     */
    protected function unmerge(TransportManager $tm)
    {
        $mergeDetails = $tm->getMergeDetails();
        foreach ($mergeDetails as $entityName => $ids) {
            foreach ($ids as $id) {
                $entity = $this->getEntity($entityName, $id);
                $entity->setTransportManager($tm);
            }
        }
    }

    /**
     * Get an entity
     *
     * @param string $entityName
     * @param int    $id
     *
     * @return object Entity
     * @throws \RuntimeException
     */
    protected function getEntity($entityName, $id)
    {
        $cleanEntityName = $this->cleanyProxyEntity($entityName);
        // map entity names to the repos they can be retrieved from
        $entityRepoMap = [
            \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication::class => 'TransportManagerApplication',
            \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence::class => 'TransportManagerLicence',
            \Dvsa\Olcs\Api\Entity\Cases\Cases::class => 'Cases',
            \Dvsa\Olcs\Api\Entity\Doc\Document::class => 'Document',
            \Dvsa\Olcs\Api\Entity\Task\Task::class => 'Task',
            \Dvsa\Olcs\Api\Entity\Note\Note::class => 'Note',
            \Dvsa\Olcs\Api\Entity\EventHistory\EventHistory::class => 'EventHistory',
            \Dvsa\Olcs\Api\Entity\User\User::class => 'User',
        ];

        // if mapping is not setup, then error
        if (!isset($entityRepoMap[$cleanEntityName])) {
            throw new \RuntimeException('Unable to unmerge entity '. $cleanEntityName);
        }

        /** @var AbstractReadonlyRepository $repo */
        $repo = $this->getRepo($entityRepoMap[$cleanEntityName]);
        return $repo->fetchById($id);
    }

    /**
     * @param string $entityName
     * @return null|string|string[]
     */
    protected function cleanyProxyEntity($entityName)
    {
        $cleanEntityName =  preg_replace('#^.*Proxy\\\\__CG__\\\\#', '', $entityName);
        return $cleanEntityName;
    }
}
