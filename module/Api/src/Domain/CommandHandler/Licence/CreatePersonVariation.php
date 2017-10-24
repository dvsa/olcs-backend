<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreatePeople;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePersonVariation as CreatePersonVariationCommand;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation;
use Dvsa\Olcs\Transfer\Command\Variation\Grant;

class CreatePersonVariation extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Variation'];

    /**
     * @param CreatePersonVariationCommand|CommandInterface $command
     *
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $createVariationResult = $this->handleSideEffect(
            CreateVariation::create(
                [
                    'id' => $command->getId(),
                    'variationType' => Application::VARIATION_TYPE_DIRECTOR_CHANGE,

                ]
            )
        );

        $this->handleSideEffect(
            CreatePeople::create(
                [
                    'id' => $createVariationResult->getId('application'),
                    'title' => $command->getTitle(),
                    'forename' => $command->getForename(),
                    'familyName' => $command->getFamilyName(),
                    'otherName' => $command->getOtherName(),
                    'birthDate' => $command->getBirthDate(),
                ]
            )
        );

        return $this->handleSideEffect(
            Grant::create(
                [
                    'id' => $createVariationResult->getId('application'),
                ]
            )
        );
    }
}
