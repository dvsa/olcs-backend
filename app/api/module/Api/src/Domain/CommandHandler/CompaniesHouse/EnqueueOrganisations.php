<?php

/**
 * Companies House Enqueue Organisations
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler as DomainAbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class EnqueueOrganisations extends DomainAbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Queue';

    /**
     * @inheritdoc
     */
    public function handleCommand(CommandInterface $command)
    {
        $rows = $this->getRepo()->enqueueAllOrganisations(Queue::TYPE_COMPANIES_HOUSE_COMPARE);

        $result = new Result();
        $result->addMessage('Enqueued ' . $rows . ' messages');

        return $result;
    }
}
