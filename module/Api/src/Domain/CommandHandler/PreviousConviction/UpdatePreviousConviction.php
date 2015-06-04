<?php

/**
 * Update Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PreviousConviction;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\PreviousConviction\UpdatePreviousConviction as Cmd;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdatePreviousConviction extends AbstractCommandHandler
{
    protected $repoServiceName = 'PreviousConviction';

    public function handleCommand(CommandInterface $command)
    {
        $conviction = $this->getRepo()
            ->fetchById(
                $command->getId(),
                \Doctrine\ORM\Query::HYDRATE_OBJECT,
                $command->getVersion()
            );

        $title = $this->getRepo()->getRefdataReference($command->getTitle());

        $conviction->setTitle($title);
        $conviction->setForename($command->getForename());
        $conviction->setFamilyName($command->getFamilyName());
        $conviction->setConvictionDate(
            new \DateTime($command->getConvictionDate())
        );
        $conviction->setCategoryText($command->getCategoryText());
        $conviction->setNotes($command->getNotes());
        $conviction->setCourtFpn($command->getCourtFpn());
        $conviction->setPenalty($command->getPenalty());

        $application = $conviction->getApplication();

        try {
            $result = new Result();

            $this->getRepo()->beginTransaction();

            $this->getRepo()->save($conviction);

            $result->merge(
                $this->updateApplicationCompletion($application->getId())
            );

            $this->getRepo()->commit();

            $result->addId('previousConviction', $conviction->getId());
            $result->addMessage('Previous conviction updated');
            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    private function updateApplicationCompletion($applicationId)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateApplicationCompletion::create(['id' => $applicationId, 'section' => 'convictionsPenalties'])
        );
    }
}
