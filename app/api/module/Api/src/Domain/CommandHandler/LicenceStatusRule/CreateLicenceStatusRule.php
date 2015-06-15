<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use \Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\LicenceStatusRule\LicenceStatusRule;

final class CreateLicenceStatusRule extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleCommand(CommandInterface $command)
    {
        $licence = $this->getRepo()
            ->getReference(Licence::class, $command->getId());

        $trailer = new LicenceStatusRule();
        $trailer->setStatus($command->getStatus());
        $trailer->setStartDate($command->getStartDate());
        $trailer->setEndDate($command->getEndDate());
        $trailer->setSpecifiedDate(new \DateTime($command->getSpecifiedDate()));
        $trailer->setLicence($licence);

        $this->getRepo()->save($trailer);

        $result = new Result();
        $result->addId('trailer', $trailer->getId());
        $result->addMessage('Trailer created successfully');

        return $result;
    }
}