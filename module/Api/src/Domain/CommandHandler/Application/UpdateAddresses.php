<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Application Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        /** @var \Dvsa\Olcs\Api\Entity\Licence\Licence $licence */
        $licence = $application->getLicence();

        $params = $command->getArrayCopy();
        $params['id'] = $licence->getId();

        $result = $this->handleSideEffect(
            SaveAddresses::create($params)
        );

        $result->merge(
            $this->handleSideEffect(
                UpdateApplicationCompletionCommand::create(
                    ['id' => $application->getId(), 'section' => 'addresses']
                )
            )
        );

        return $result;
    }
}
