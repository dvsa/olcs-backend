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
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
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
final class UpdateBusinessDetails extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Organisation', 'TradingName'];

    private $isDirty = false;
    private $hasChangedOrg = false;
    private $result;

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

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

        $this->result = new Result();

        try {

            $this->getRepo()->beginTransaction();

            $this->updateTradingNames(
                $licence->getId(),
                $organisation->getId(),
                $command->getTradingNames()
            );

            $this->maybeSaveRegisteredAddress($command, $organisation);

            $this->maybeUpdateOrganisation($command, $organisation);

            $this->updateNatureOfBusinesses($command->getNatureOfBusinesses(), $organisation);

            if ($this->hasChangedOrg) {
                $this->isDirty = true;
                $this->getRepo('Organisation')->save($organisation);
                $this->result->addMessage('Organisation updated');
                $this->result->setFlag('hasChanged', true);
            } else {
                $this->result->addMessage('Organisation unchanged');
            }

            if ($this->isDirty) {
                // Do something
            }

            $this->getRepo()->commit();

            return $this->result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function updateNatureOfBusinesses(array $nobList, Organisation $organisation)
    {
        die('finish');

        $changed = false;

        // If the counts match
        if (count($nobList) === $organisation->getNatureOfBusinesses()->count()) {

            // try to match each one
            $matches = 0;
            foreach ($organisation->getNatureOfBusinesses()->getIterator() as $entity) {

                if (in_array($entity->getId(), $nobList)) {
                    $matches++;
                }
            }

            // if we match all, then there are no changes
            if ($matches === count($nobList)) {
                return false;
            }
        }

        $nobs = [];

        foreach ($nobList as $nob) {
            $nobs[] = $this->getRepo()->getRefdataReference($nob);
        }

        $organisation->setNatureOfBusinesses($nobs);
    }

    private function canUpdateOrganisation($organisation)
    {
        return $this->isGranted(Permission::INTERNAL_USER) || !$organisation->hasInforceLicences();
    }

    private function saveRegisteredAddress(array $address)
    {
        $address['contactType'] = ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS;

        return $this->getCommandHandler()->handleCommand(
            SaveAddress::create($address)
        );
    }

    private function updateTradingNames($licenceId, $organisationId, $tradingNames)
    {
        $result = $this->getCommandHandler()->handleCommand(
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
