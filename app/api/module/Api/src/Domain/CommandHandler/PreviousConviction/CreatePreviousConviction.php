<?php

/**
 * Create Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PreviousConviction;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\PreviousConviction\CreatePreviousConviction as Cmd;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Create Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class CreatePreviousConviction extends AbstractCommandHandler
{
    protected $repoServiceName = 'PreviousConviction';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        try {
            $this->getRepo()->beginTransaction();

            $application = $this->getRepo()->getReference(Application::class, $command->getApplication());

            $conviction = $this->createPreviousConvictionObject($command, $application);
            $result->addMessage('Previous conviction created');

            $this->getRepo()->save($conviction);

            $result->addId('previousConviction', $conviction->getId());

            $result->merge($this->updateApplicationCompletion($application->getId()));

            $this->getRepo()->commit();

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

    /**
     * @param Cmd $command
     * @return Application
     */
    private function createPreviousConvictionObject(Cmd $command, Application $application)
    {
        $title = $this->getRepo()->getRefdataReference($command->getTitle());

        $conviction = new PreviousConviction();

        if ($command->getTransportManager()) {
            // @TODO: as and when TM previous convictions are worked on
            // this will need to be implemented
        }

        $conviction->setTitle($title);
        $conviction->setApplication($application);
        $conviction->setForename($command->getForename());
        $conviction->setFamilyName($command->getFamilyName());
        $conviction->setConvictionDate(
            new \DateTime($command->getConvictionDate())
        );
        $conviction->setCategoryText($command->getCategoryText());
        $conviction->setNotes($command->getNotes());
        $conviction->setCourtFpn($command->getCourtFpn());
        $conviction->setPenalty($command->getPenalty());

        return $conviction;
    }
}
