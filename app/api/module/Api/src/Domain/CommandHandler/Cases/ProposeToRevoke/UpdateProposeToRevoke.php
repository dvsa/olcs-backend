<?php

/**
 * Update ProposeToRevoke
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Pi\Reason;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update ProposeToRevoke
 */
final class UpdateProposeToRevoke extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ProposeToRevoke';

    public function handleCommand(CommandInterface $command)
    {
        $proposeToRevoke = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $reasons = array_map(
            function ($reasonId) {
                return $this->getRepo()->getReference(Reason::class, $reasonId);
            },
            $command->getReasons()
        );
        $presidingTc = $this->getRepo()->getReference(PresidingTc::class, $command->getPresidingTc());
        $ptrAgreedDate = new \DateTime($command->getPtrAgreedDate());

        $proposeToRevoke->update($reasons, $presidingTc, $ptrAgreedDate);

        if ($command->getClosedDate() !== null) {
            $proposeToRevoke->setClosedDate(new \DateTime($command->getClosedDate()));
        }

        if ($command->getComment() !== null) {
            $proposeToRevoke->setComment($command->getComment());
        }

        $this->getRepo()->save($proposeToRevoke);

        $result = new Result();
        $result->addId('proposeToRevoke', $proposeToRevoke->getId());
        $result->addMessage('Revocation updated successfully');

        return $result;
    }
}
