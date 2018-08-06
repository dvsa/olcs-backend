<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtCheckAnswers as UpdateEcmtCheckAnswersCmd;

/**
 * Update checked answers information
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class UpdateEcmtCheckAnswers extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Update the checked answers field
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication $application
         * @var UpdateEcmtCheckAnswersCmd    $command
         */
        $application = $this->getRepo()->fetchById($command->getId());
        $application->setCheckedAnswers($command->getCheckAnswers());

        $this->getRepo()->save($application);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $application->getId());
        $result->addMessage('Permit application updated');

        return $result;
    }
}
