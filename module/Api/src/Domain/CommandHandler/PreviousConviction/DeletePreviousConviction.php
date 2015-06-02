<?php

/**
 * Delete Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PreviousConviction;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\PreviousConviction\CreatePreviousConviction as Cmd;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Create Previous Conviction
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class DeletePreviousConviction extends AbstractCommandHandler
{
    protected $repoServiceName = 'PreviousConviction';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        try {
            $this->getRepo()->beginTransaction();

            foreach ($command->getIds() as $id) {
                $this->getRepo()->delete(
                    $this->getRepo()->fetchById($id)
                );
            }

            $this->getRepo()->commit();

            $result->addMessage('Previous conviction(s) deleted');
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }

        return $result;
    }
}
