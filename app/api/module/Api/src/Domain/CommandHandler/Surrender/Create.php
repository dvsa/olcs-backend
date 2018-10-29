<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Create as Cmd;
use Dvsa\Olcs\Api\Entity\Surrender;

final class Create extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use \Dvsa\Olcs\Api\Domain\AuthAwareTrait;

    protected $repoServiceName = 'Surrender';

    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var Cmd $command */
        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());
        $surrender = new Surrender();
        $surrender->setLicence($licence);

        $this->getRepo()->save($surrender);

        $result = new Result();
        $result->addId('surrender', $surrender->getId());
        $result->addMessage('Surrender successfully created.');

        return $result;
    }
}
