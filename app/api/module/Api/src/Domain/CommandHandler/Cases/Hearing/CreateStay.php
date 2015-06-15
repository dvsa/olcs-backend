<?php

/**
 * Create Stay
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Stay;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Transfer\Command\Cases\Hearing\CreateStay as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

    /**
 * Create Stay
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateStay extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Stay';

    /**
     * Creates Stay and associated entities
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();


        $stay = $this->createStayObject($command);

        $this->getRepo()->save($stay);
        $result->addMessage('Stay created');

        $result->addId('stay', $stay->getId());

        return $result;
    }

    /**
     * Create the stay object
     *
     * @param Cmd $command
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @return Stay
     */
    private function createStayObject(Cmd $command)
    {
        // If doesnt have an appeal, raise exception
        if (!($this->getRepo()->getReference(
            Cases::class,
            $command->getCase()
        )->hasAppeal()
        )) {
            throw new ValidationException(
                ['appeal' => 'An appeal must exist against the case before a stay can be added']
            );
        }

        // see if stay already exists
        if ($this->getRepo()->getReference(
            Cases::class,
            $command->getCase()
            )->hasStayType(
                $this->getRepo()->getRefdataReference($command->getStayType())
            )
        ) {
            throw new ValidationException(['stayType' => 'Stay of this type already exists against this case']);
        }

        $stay = new Stay(
            $this->getRepo()->getReference(Cases::class, $command->getCase()),
            $this->getRepo()->getRefdataReference($command->getStayType())
        );

        $stay->setRequestDate(new \DateTime($command->getRequestDate()));

        if ($command->getDecisionDate() !== null) {
            $stay->setDecisionDate(new \DateTime($command->getDecisionDate()));
        }

        if ($command->getOutcome() !== null) {
            $stay->setOutcome($this->getRepo()->getRefdataReference($command->getOutcome()));
        }

        if ($command->getNotes() !== null) {
            $stay->setNotes($command->getNotes());
        }

        if ($command->getIsWithdrawn() === 'Y') {
            if ($command->getWithdrawnDate() !== null) {
                $stay->setWithdrawnDate(new \DateTime($command->getWithdrawnDate()));
            }
        } else {
            $stay->setWithdrawnDate(null);
        }

        return $stay;
    }
}
