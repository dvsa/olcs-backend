<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TmlRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete as DeleteDto;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteUpdateOptOutTmLetter as DeleteUpdateOptOutTmLetterDto;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;

class DeleteUpdateOptOutTmLetter extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerLicence';

    /**
     * handle command
     *
     * @param CommandInterface|DeleteUpdateOptOutTmLetterDto $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var $tmlRepo TmlRepo
         * @var $tmlEntity TmlEntity
         */
        $result = new Result();

        $tmlRepo = $this->getRepo();
        $tmlId = $command->getId();
        $tmlEntity = $tmlRepo->fetchById($tmlId);
        $licence = $tmlEntity->getLicence();
        $last = ($licence->getTmLicences()->count() === 1 ? true : false);
        $ids = [$tmlId];

        if ($last) {
            $optOutTmLetterValue = $command->getYesNo() === 'Y' ? 0 : 1;
            $licence->setOptOutTmLetter($optOutTmLetterValue);
            $result->addMessage("Success");
            $result->merge($this->handleSideEffect(DeleteDto::create(['ids' => $ids])));
            return $result;
        }

        throw new ValidationException(["Error: Not last Transport Manager"]);
    }
}
