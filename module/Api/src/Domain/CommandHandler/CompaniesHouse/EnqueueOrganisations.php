<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler as DomainAbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class EnqueueOrganisations extends DomainAbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Queue';

    /**
     * Process handler
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\EnqueueOrganisations $command Command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Queue $repo */
        $repo = $this->getRepo();
        $rows = $repo->enqueueAllOrganisations(Queue::TYPE_COMPANIES_HOUSE_COMPARE);

        return (new Result())
            ->addMessage('Enqueued ' . $rows . ' messages');
    }
}
