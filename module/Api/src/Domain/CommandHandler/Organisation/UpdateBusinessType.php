<?php

/**
 * Update Business Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Organisation\UpdateBusinessType as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Update Business Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateBusinessType extends AbstractCommandHandler
{
    const ERROR_NO_TYPE = 'ORG-BT-1';
    const ERROR_CANT_CHANGE_TYPE = 'ORG-BT-2';

    protected $repoServiceName = 'Organisation';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Organisation $organisation */
        $organisation = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $canChangeBusinessType = $this->canChangeBusinessType($command, $organisation);

        // If we can change business type, but we don't have a business type set
        if ($canChangeBusinessType && $command->getBusinessType() === null) {
            throw new ValidationException([self::ERROR_NO_TYPE => 'Missing business type']);
        }

        if (!$canChangeBusinessType && $this->businessTypeWillChange($organisation, $command)) {
            throw new ValidationException(
                [self::ERROR_CANT_CHANGE_TYPE => 'Attempted to change business type when update is not allowed']
            );
        }

        try {

            $this->getRepo()->beginTransaction();

            if ($canChangeBusinessType) {

                if ($this->businessTypeWillChange($organisation, $command)) {
                    $organisation->setType($this->getRepo()->getRefdataReference($command->getBusinessType()));

                    $this->getRepo()->save($organisation);
                    $result->addMessage('Business type updated');
                } else {
                    $result->addMessage('Business type unchanged');
                }
            } else {
                $result->addMessage('Can\'t update business type');
            }

            if ($command->getApplication() !== null) {

                $result->merge(
                    $this->getCommandHandler()->handleCommand(
                        $this->createUpdateApplicationCompletionCommand($command->getApplication())
                    )
                );
            }

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    private function businessTypeWillChange(Organisation $organisation, Cmd $command)
    {
        if ($command->getBusinessType() === null) {
            return false;
        }

        return ($organisation->getType() !== $this->getRepo()->getRefdataReference($command->getBusinessType()));
    }

    private function canChangeBusinessType(Cmd $command, Organisation $organisation)
    {
        if (!$this->isGranted('selfserve-user')) {
            return true;
        }

        if (($command->getLicence() !== null || $command->getVariation() !== null)) {
            return false;
        }

        if ($command->getApplication() !== null && $this->getRepo()->hasInforceLicences($organisation->getId())) {
            return false;
        }

        return true;
    }

    private function createUpdateApplicationCompletionCommand($applicationId)
    {
        return UpdateApplicationCompletion::create(
            ['id' => $applicationId, 'section' => 'businessType']
        );
    }

    /**
     * @TODO Need to replace this with a real way to determine between internal and selfserve users
     *
     * @param $permission
     * @return bool
     */
    private function isGranted($permission)
    {
        return true;
    }
}
