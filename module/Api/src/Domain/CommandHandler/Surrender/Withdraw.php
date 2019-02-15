<?php


namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;


use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateSurrender;

class Withdraw extends AbstractSurrenderCommandHandler
{

    protected $extraRepos = ['Licence'];

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = $this->handleSideEffect(UpdateSurrender::create(
            [
                'id' => $command->getId(),
                'status' => Surrender::SURRENDER_STATUS_WITHDRAWN
            ]
        ));
        $this->result->addMessage($result);

        /** @var Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($command->getId());
        $status = $this->getRepo()->getRefdataReference($command->getStatus());
        $licence->setStatus($status);

        $this->getRepo('Licence')->save($licence);

        $this->result->addMessage('Licence ' . $licence->getId() . ' surrender withdrawn');

        return $this->result;
    }
}