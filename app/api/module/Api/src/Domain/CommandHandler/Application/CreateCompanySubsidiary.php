<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Lva\SaveCompanySubsidiary;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateCompanySubsidiary extends SaveCompanySubsidiary
{
    /**
     * Command Handler
     *
     * @param \Dvsa\Olcs\Transfer\Command\Application\CreateCompanySubsidiary $command Command
     *
     * @return DomainCmd\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $app */
        $app = $this->getRepo('Application')->fetchById($command->getApplication());

        //  create subsidiary
        $this->result = $this->create($command, $app->getLicence()->getId());

        //  update Application Completion
        $this->result->merge(
            $this->handleSideEffect(
                DomainCmd\Application\UpdateApplicationCompletion::create(
                    [
                        'id' => $command->getApplication(),
                        'section' => 'businessDetails',
                        'data' => [
                            'hasChanged' => true,
                        ],
                    ]
                )
            )
        );

        return $this->result;
    }
}
