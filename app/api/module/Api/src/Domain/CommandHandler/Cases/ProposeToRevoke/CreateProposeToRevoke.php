<?php

/**
 * Create ProposeToRevoke
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Pi\Reason;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Transfer\Command\Cases\ProposeToRevoke\CreateProposeToRevoke as Cmd;

/**
 * Create ProposeToRevoke
 */
final class CreateProposeToRevoke extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ProposeToRevoke';

    public function handleCommand(CommandInterface $command)
    {
        // create and save a record
        $proposeToRevoke = $this->createProposeToRevokeObject($command);
        $this->getRepo()->save($proposeToRevoke);

        $result = new Result();
        $result->addId('proposeToRevoke', $proposeToRevoke->getId());
        $result->addMessage('Revocation created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return ProposeToRevoke
     */
    private function createProposeToRevokeObject(Cmd $command)
    {
        $case = $this->getRepo()->getReference(Cases::class, $command->getCase());
        $reasons = array_map(
            function ($reasonId) {
                return $this->getRepo()->getReference(Reason::class, $reasonId);
            },
            $command->getReasons()
        );
        $presidingTc = $this->getRepo()->getReference(PresidingTc::class, $command->getPresidingTc());
        $ptrAgreedDate = new \DateTime($command->getPtrAgreedDate());

        $proposeToRevoke = new ProposeToRevoke($case, $reasons, $presidingTc, $ptrAgreedDate);

        if ($command->getClosedDate() !== null) {
            $proposeToRevoke->setClosedDate(new \DateTime($command->getClosedDate()));
        }

        if ($command->getComment() !== null) {
            $proposeToRevoke->setComment($command->getComment());
        }

        return $proposeToRevoke;
    }
}
