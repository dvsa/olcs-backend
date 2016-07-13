<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Lva\SaveCompanySubsidiary;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateCompanySubsidiary extends SaveCompanySubsidiary
{
    /**
     * Command Handler
     *
     * @param \Dvsa\Olcs\Transfer\Command\Application\UpdateCompanySubsidiary $command Command
     *
     * @return DomainCmd\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        //  update subsidiary data
        $this->result = $this->update($command);

        //  update application completion
        $this->result->merge(
            $this->handleSideEffect(
                DomainCmd\Application\UpdateApplicationCompletion::create(
                    [
                        'id' => $command->getApplication(),
                        'section' => 'businessDetails',
                        'data' => [
                            'hasChanged' => $this->result->getFlag('hasChanged'),
                        ],
                    ]
                )
            )
        );

        return $this->result;
    }
}
