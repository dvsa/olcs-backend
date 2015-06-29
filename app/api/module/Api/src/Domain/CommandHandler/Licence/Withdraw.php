<?php

/**
 * Withdraw a licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Withdraw a licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class Withdraw extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchById($command->getId());

        $licence->setStatus(
            $this->getRepo()->getRefdataReference(
                Licence::LICENCE_STATUS_WITHDRAWN
            )
        );

        $this->getRepo()->save($licence);

        $result = new Result();
        $result->addMessage('Licence' . $licence->getId() . ' has been withdrawn');

        return $result;
    }
}
