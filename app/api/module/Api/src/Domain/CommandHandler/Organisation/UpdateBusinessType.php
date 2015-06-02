<?php

/**
 * Update Business Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\User\Permission;
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
final class UpdateBusinessType extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    const ERROR_NO_TYPE = 'ORG-BT-1';
    const ERROR_CANT_CHANGE_TYPE = 'ORG-BT-2';

    protected $repoServiceName = 'Organisation';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Organisation $organisation */
        $organisation = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $canChangeBusinessType = $this->canChangeBusinessType($command, $organisation);

        // If we can change business type...
        if ($canChangeBusinessType) {
            // A) But we don't have a business type set
            if ($command->getBusinessType() === null) {
                throw new ValidationException([self::ERROR_NO_TYPE => 'Missing business type']);
            }

            // B) But we are not changing business type
            if (!$this->businessTypeWillChange($organisation, $command)) {
                $result->addMessage('Business type unchanged');
                return $result;
            }
        } else {
            // If we can't change business type...

            // A) But we are attempting to change it
            if ($this->businessTypeWillChange($organisation, $command)) {

                throw new ValidationException(
                    [self::ERROR_CANT_CHANGE_TYPE => 'Attempted to change business type when update is not allowed']
                );
            }

            // B) Otherwise...
            $result->addMessage('Can\'t update business type');

            $this->maybeUpdateApplicationCompletion($command, $result);

            return $result;
        }

        // If we have got here then we CAN change the business type and we ARE changing the business type
        try {

            $this->getRepo()->beginTransaction();

            $organisation->setType($this->getRepo()->getRefdataReference($command->getBusinessType()));

            $this->getRepo()->save($organisation);

            $result->addMessage('Business type updated');

            $this->maybeUpdateApplicationCompletion($command, $result);

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {

            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    private function maybeUpdateApplicationCompletion(Cmd $command, Result $result)
    {
        if ($command->getApplication() !== null) {
            $result->merge(
                $this->getCommandHandler()->handleCommand(
                    $this->createUpdateApplicationCompletionCommand($command->getApplication())
                )
            );
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
        if (!$this->isGranted(Permission::SELFSERVE_USER)) {
            return true;
        }

        if (($command->getLicence() !== null || $command->getVariation() !== null)) {
            return false;
        }

        if ($command->getApplication() !== null && $organisation->hasInforceLicences()) {
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
}
