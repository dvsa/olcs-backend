<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\Create;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation;

class CreatePersonVariation extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $licence = $this->getRepo()->fetchUsingId($command);

        $this->proxyCommand($command, '\Dvsa\Olcs\Api\Domain\Command\Person\Create');






        //@TODO Create Variation - Copy code from CreateVariation handler, or create command and call?
        $createVariationCommand = CreateVariation::create(
            [
                'variationType' => Application::VARIATION_TYPE_DIRECTOR_CHANGE,
            ]
        );

        //@TODO Get data into command handler (somehow?), pass to command handler / service, get response

        //@TODO Get ID from created variation, load it

        //@TODO Create Person using CreatePeople command, attach to Organisation on variation

        //@TODO Grant variation using Grant command / handler

        //@TODO Return Variation ID in result


        $result = new Result();

        return $result;
    }
}
