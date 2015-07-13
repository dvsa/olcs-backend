<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Delete a list of ConditionUndertaking
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeleteListConditionUndertaking extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ConditionUndertaking';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $cuId) {
            /* @var $conditionUndertaking \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking */
            $conditionUndertaking = $this->getRepo()->fetchById($cuId);

            if ($conditionUndertaking->getApplication()) {
                $this->getRepo()->delete($conditionUndertaking);
            } else {
                // create delta
                $deltaConditionUndertaking = clone $conditionUndertaking;
                $deltaConditionUndertaking
                    ->setLastModifiedOn(null)
                    ->setAction('D')
                    ->setLicConditionVariation($conditionUndertaking)
                    ->setLicence(null)
                    ->setApplication(
                        $this->getRepo()->getReference(Application::class, $command->getId())
                    );

                $this->getRepo()->save($deltaConditionUndertaking);
            }

            $result->addMessage("ConditionUndertaking ID {$cuId} deleted");
        }

        return $result;
    }
}
