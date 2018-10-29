<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Create as Cmd;
use Dvsa\Olcs\Api\Entity\Surrender;

final class Create extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface, ToggleRequiredInterface
{
    use \Dvsa\Olcs\Api\Domain\AuthAwareTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];

    protected $repoServiceName = 'Surrender';

    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var Cmd $command */
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
