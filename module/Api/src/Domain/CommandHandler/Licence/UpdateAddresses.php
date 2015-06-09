<?php

/**
 * Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateAddresses as Cmd;

/**
 * Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['ContactDetails'];

    private $isDirty = false;
    private $result;

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        $this->result = new Result();

        $this->maybeSaveCorrespondenceAddress($command, $licence);

        $this->maybeSaveEstablishmentAddress($command, $licence);

        $this->addOrRemoveTransportConsultant($command, $licence);

        $this->getRepo()->save($licence);

        /*
        if ($this->isDirty && $this->isGranted(Permission::SELFSERVE_USER)) {
            $taskData = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
                'description' => 'Change to business details',
                'licence' => $licence->getId()
            ];

            $this->result->merge($this->getCommandHandler()->handleCommand(CreateTask::create($taskData)));
        }
         */

        $this->result->setFlag('hasChanged', $this->isDirty);

        return $this->result;
    }

    private function handleSideEffectResult(Result $result)
    {
        $this->result->merge($result);

        if ($result->getFlag('hasChanged')) {
            $this->isDirty = true;
        }
    }

    private function maybeSaveCorrespondenceAddress(Cmd $command, Licence $licence)
    {
        if (empty($command->getCorrespondenceAddress())) {
            return;
        }

        $address = $command->getCorrespondenceAddress();
        $address['contactType'] = ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS;
        $result = $this->getCommandHandler()->handleCommand(
            SaveAddress::create($address)
        );

        $this->handleSideEffectResult($result);

        if ($result->getId('contactDetails') !== null) {
            $licence->setCorrespondenceCd(
                $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
            );
        }

        $correspondenceCd = $licence->getCorrespondenceCd();
        $correspondenceCd->setFao($command->getCorrespondence()['fao']);

        $version = $correspondenceCd->getVersion();
        $this->getRepo('ContactDetails')->save($correspondenceCd);

        if ($correspondenceCd->getVersion() != $version) {
            // we can't set this to false in an `else` branch because it might have already
            // been set when saving the address
            $result->setFlag('hasChanged', true);
        }
    }

    private function maybeSaveEstablishmentAddress(Cmd $command, Licence $licence)
    {
        if (empty($command->getEstablishmentAddress())) {
            return;
        }

        $address = $command->getEstablishmentAddress();
        $address['contactType'] = ContactDetails::CONTACT_TYPE_ESTABLISHMENT_ADDRESS;
        $result = $this->getCommandHandler()->handleCommand(
            SaveAddress::create($address)
        );

        $this->handleSideEffectResult($result);

        if ($result->getId('contactDetails') !== null) {
            $licence->setEstablishmentCd(
                $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
            );
        }
    }

    private function addOrRemoveTransportConsultant(Cmd $command, Licence $licence)
    {
        $params = $command->getConsultant();

        if ($params['add-transport-consultant'] === 'Y') {
            $address = $params['address'];
            $address['contactType'] = ContactDetails::CONTACT_TYPE_TRANSPORT_CONSULTANT;
            $result = $this->getCommandHandler()->handleCommand(
                SaveAddress::create($address)
            );

            $this->handleSideEffectResult($result);

            if ($result->getId('contactDetails') !== null) {
                $licence->setTransportConsultantCd(
                    $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
                );
            }

            $transportConsultant = $licence->getTransportConsultantCd();

            $transportConsultant->setFao($params['transportConsultantName']);
            $transportConsultant->setWrittenPermissionToEngage($params['writtenPermissionToEngage']);

            $version = $transportConsultant->getVersion();
            $this->getRepo('ContactDetails')->save($transportConsultant);

            if ($transportConsultant->getVersion() != $version) {
                // we can't set this to false in an `else` branch because it might have already
                // been set when saving the address
                $result->setFlag('hasChanged', true);
            }
        } else {
            $licence->setTransportConsultantCd(null);

            $result = new Result();
            $result->addMessage('Transport consultant deleted');
            $this->result->merge($result);
        }
    }
}
