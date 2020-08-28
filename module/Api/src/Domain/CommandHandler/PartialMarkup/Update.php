<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\PartialMarkup;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Update as UpdateCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as PartialMarkupRepo;

/**
 * Update partial markup entry
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'PartialMarkup';

    /**
     * Update partial markup for a Partial
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PartialMarkupRepo  $partialMarkupRepo
         * @var PartialMarkup      $partialMarkup
         * @var UpdateCmd          $command
         */

        $partialMarkupRepo = $this->getRepo();
        $partialMarkup = $partialMarkupRepo->fetchById($command->getId());

        $partialMarkup->update($command->getMarkup());
        $partialMarkupRepo->save($partialMarkup);

        $this->result->addId('PartialMarkup', $partialMarkup->getId());
        $this->result->addMessage('Partial Markup Updated: ' . $partialMarkup->getId());

        return $this->result;
    }
}
