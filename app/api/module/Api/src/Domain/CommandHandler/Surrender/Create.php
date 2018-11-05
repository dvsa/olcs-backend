<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Create as Cmd;
use Dvsa\Olcs\Api\Entity\Surrender;

final class Create extends AbstractSurrenderCommandHandler
{
    protected $extraRepos = ['Licence'];

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());
        $status = $this->getRepo()->getRefdataReference($command->getStatus());

        $surrender = new Surrender();
        $surrender->setLicence($licence);
        $surrender->setStatus($status);

        $this->getRepo()->save($surrender);

        $this->result->addId('surrender', $surrender->getId());
        $this->result->addMessage('Surrender successfully created.');

        return $this->result;
    }
}
