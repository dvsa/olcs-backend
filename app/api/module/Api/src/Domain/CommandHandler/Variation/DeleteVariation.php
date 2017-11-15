<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadVariationTypeException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson;
use Dvsa\Olcs\Transfer\Command\Application\DeletePeople;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteVariation as DeleteVariationCommand;

/**
 * Class GrantDirectorChange
 */
class DeleteVariation extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @param DeleteVariationCommand|CommandInterface $command GrantDirectorChange Command
     *
     * @return Result
     * @throws BadVariationTypeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        if (!$application->getIsVariation()) {
            throw new BadVariationTypeException("Applications can not be deleted");
        }

        if ($application->getVariationType() === null) {
            throw new BadVariationTypeException("Standard variations can not be deleted");
        }

        $variationType = $application->getVariationType()->getId();
        if ($variationType !== Application::VARIATION_TYPE_DIRECTOR_CHANGE) {
            throw new BadVariationTypeException("Variations of type '{$variationType}' can not be deleted");
        }

        $personIds = [];

        /** @var ApplicationOrganisationPerson $applicationOrganisationPerson */
        foreach ($application->getApplicationOrganisationPersons() as $applicationOrganisationPerson) {
            $personIds[] = $applicationOrganisationPerson->getPerson()->getId();
        }

        $this->handleSideEffect(DeletePeople::create(['id' => $command->getId(), 'personIds' => $personIds]));

        $this->getRepo()->delete($application);

        $this->result->addId('application ' . $command->getId(), $command->getId());
        $this->result->addMessage('Application with id ' . $command->getId() . ' was deleted');

        return $this->result;
    }
}
