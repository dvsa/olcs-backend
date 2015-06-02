<?php

/**
 * Create Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Impounding;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Impounding;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as Cmd;

/**
 * Create Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateImpounding extends AbstractCommandHandler
{
    protected $repoServiceName = 'Impounding';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        try {
            $this->getRepo()->beginTransaction();

            // save impounding entity
            $impounding = $this->createImpoundingObject($command);
            $this->getRepo()->save($impounding);
            $result->addMessage('Impounding created');
            $result->addId('impounding', $impounding->getId());

            // save legislation types
            $result->merge($this->createLegislationTypes($impounding);
            $result->addMessage('Impounding legislation types created');


            $this->getRepo()->commit();

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * Create the legislation types
     * 
     * @param Impounding $impounding
     * @return Result
     */
    private function createLegislationTypes(Impounding $impounding)
    {
        return $this->getCommandHandler()->handleCommand(CreateImpoundingLegislationCommand::create(['id' =>
            $impounding->getId(), $impounding->getImpoundingLegislationTypes()]));
    }

    /**
     * @param Cmd $command
     * @return Impounding
     */
    private function createImpoundingObject(Cmd $command)
    {
        $impounding = new Impounding($command->getCase(), $command->getImpoundingType());
        $impounding->setPiVenueProperties($command->getPiVenue(), $command->getPiVenueOther());
        $impounding->setImpoundingLegislationTypes($command->getImpoundingLegislationTypes());

        return $impounding;
    }
}
