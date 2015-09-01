<?php

/**
 * Update Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Organisation\UpdateTradingNames;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateBusinessDetails as Cmd;

/**
 * Update Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateBusinessDetails extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Organisation'];

    private $isDirty = false;
    private $hasChangedOrg = false;

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        $organisation = $licence->getOrganisation();

        // If we can't update the org details
        if (!$this->canUpdateOrganisation($organisation)) {

            // and we are trying to update the name
            if ($command->getName() !== null && $command->getName() !== $organisation->getName()) {
                throw new ForbiddenException('You are not allowed to update the organisation name');
            }

            // and we are trying to update the company number
            if ($command->getCompanyOrLlpNo() !== null
                && $command->getCompanyOrLlpNo() !== $organisation->getCompanyOrLlpNo()
            ) {
                throw new ForbiddenException('You are not allowed to update the company number');
            }
        }

        // Optimistic locking on the org
        $this->getRepo('Organisation')->lock($organisation, $command->getVersion());

        $this->updateTradingNames(
            $licence->getId(),
            $organisation->getId(),
            $command->getTradingNames()
        );

        $this->maybeSaveRegisteredAddress($command, $organisation);

        $this->maybeUpdateOrganisation($command, $organisation);

        if ($organisation->getNatureOfBusiness() !== $command->getNatureOfBusiness()) {
            $organisation->setNatureOfBusiness($command->getNatureOfBusiness());
            $this->hasChangedOrg = true;
        }

        if ($this->hasChangedOrg) {
            $this->isDirty = true;
            $this->getRepo('Organisation')->save($organisation);
            $this->result->addMessage('Organisation updated');
        } else {
            $this->result->addMessage('Organisation unchanged');
        }

        if ($this->isDirty && $this->isGranted(Permission::SELFSERVE_USER)) {
            $taskData = [
                'category' => Category::CATEGORY_LICENSING,
                'subCategory' => Category::TASK_SUB_CATEGORY_BUSINESS_DETAILS_CHANGE,
                'description' => 'Change to business details',
                'licence' => $licence->getId()
            ];

            $this->result->merge($this->handleSideEffect(CreateTask::create($taskData)));
        }

        $this->result->setFlag('hasChanged', $this->isDirty);

        return $this->result;
    }

    private function canUpdateOrganisation($organisation)
    {
        return $this->isGranted(Permission::INTERNAL_USER) || !$organisation->hasInforceLicences();
    }

    private function saveRegisteredAddress(array $address)
    {
        $address['contactType'] = ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS;

        return $this->handleSideEffect(
            SaveAddress::create($address)
        );
    }

    private function updateTradingNames($licenceId, $organisationId, $tradingNames)
    {
        $result = $this->handleSideEffect(
            UpdateTradingNames::create(
                [
                    'licence' => $licenceId,
                    'organisation' => $organisationId,
                    'tradingNames' => $tradingNames
                ]
            )
        );

        $this->handleSideEffectResult($result);
    }

    private function handleSideEffectResult(Result $result)
    {
        $this->result->merge($result);

        if ($result->getFlag('hasChanged')) {
            $this->isDirty = true;
        }
    }

    private function maybeSaveRegisteredAddress(Cmd $command, Organisation $organisation)
    {
        if (!empty($command->getRegisteredAddress())) {

            $result = $this->saveRegisteredAddress($command->getRegisteredAddress());

            $this->handleSideEffectResult($result);

            if ($result->getId('contactDetails') !== null) {
                $this->hasChangedOrg = true;
                $organisation->setContactDetails(
                    $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
                );
            }
        }
    }

    private function maybeUpdateOrganisation(Cmd $command, Organisation $organisation)
    {
        if ($this->canUpdateOrganisation($organisation)) {

            if (!empty($command->getName()) && $organisation->getName() != $command->getName()) {
                $this->hasChangedOrg = true;
                $organisation->setName($command->getName());
            }

            if (!empty($command->getCompanyOrLlpNo())
                && $organisation->getCompanyOrLlpNo() != $command->getCompanyOrLlpNo()
            ) {
                $this->hasChangedOrg = true;
                $organisation->setCompanyOrLlpNo($command->getCompanyOrLlpNo());
            }
        }
    }
}
