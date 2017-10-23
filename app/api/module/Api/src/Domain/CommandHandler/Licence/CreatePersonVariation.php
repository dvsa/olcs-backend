<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation;

class CreatePersonVariation extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Variation'];

    /**
     * @param \Dvsa\Olcs\Transfer\Command\Licence\CreatePersonVariation|CommandInterface $command
     *
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $personCommand = $this->proxyCommand($command, '\Dvsa\Olcs\Api\Domain\Command\Person\Create');

        $createVariationResult = $this->handleSideEffect(
            CreateVariation::create(
                [
                    'id' => $command->getId(),
                    'variationType' => Application::VARIATION_TYPE_DIRECTOR_CHANGE,
                ]
            )
        );

        //@TODO Get ID from created variation, load it

        //@TODO Create Person using CreatePeople command, attach to Organisation on variation






        //@TODO Grant variation using Grant command / handler

        //@TODO Return Variation ID in result


        $result = new Result();
        return $result;
    }
}
