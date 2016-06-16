<?php

/**
 * Save LVA Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses as Cmd;

/**
 * Save LVA Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class SaveAddresses extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['ContactDetails', 'PhoneContact'];

    private $phoneTypes = array(
        'business' => 'phone_t_tel',
        'home' => 'phone_t_home',
        'mobile' => 'phone_t_mobile',
        'fax' => 'phone_t_fax'
    );

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        $this->result->setFlag('isDirty', false);

        $this->saveCorrespondenceAddress($command, $licence);

        $this->updateCorrespondencePhoneContacts($command, $licence);

        $this->maybeSaveEstablishmentAddress($command, $licence);

        $this->maybeAddOrRemoveTransportConsultant($command, $licence);

        $this->getRepo()->save($licence);

        return $this->result;
    }

    private function handleSideEffectResult(Result $result)
    {
        $this->result->merge($result);

        if ($result->getFlag('hasChanged')) {
            $this->result->setFlag('isDirty', true);
        }
    }

    private function saveCorrespondenceAddress(Cmd $command, Licence $licence)
    {
        $address = $command->getCorrespondenceAddress();
        $address['contactType'] = ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS;
        $result = $this->handleSideEffect(
            SaveAddress::create($address)
        );

        if ($result->getId('contactDetails') !== null) {
            $licence->setCorrespondenceCd(
                $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
            );
        }

        $correspondenceCd = $licence->getCorrespondenceCd();

        $correspondenceCd->setFao($command->getCorrespondence()['fao']);
        $correspondenceCd->setEmailAddress($command->getContact()['email']);

        $version = $correspondenceCd->getVersion();
        $this->getRepo('ContactDetails')->save($correspondenceCd);

        if ($correspondenceCd->getVersion() != $version) {
            $result->setFlag('hasChanged', true);
            $result->addMessage('Contact details updated');
        }

        $this->handleSideEffectResult($result);
    }

    private function updateCorrespondencePhoneContacts(Cmd $command, Licence $licence)
    {
        $this->updatePhoneContacts(
            $command->getContact(),
            $licence->getCorrespondenceCd()
        );
    }

    private function updatePhoneContacts($data, ContactDetails $contactDetails)
    {
        foreach ($this->phoneTypes as $phoneType => $phoneRefName) {

            $result = new Result();
            $version = null;

            $result->setFlag('hasChanged', false);

            if (!empty($data['phone_' . $phoneType . '_id'])) {
                $version = $data['phone_' . $phoneType . '_version'];
                $contact = $this->getRepo('PhoneContact')->fetchById(
                    $data['phone_' . $phoneType . '_id'],
                    Query::HYDRATE_OBJECT,
                    $version
                );
            } else {
                $contact = new PhoneContact(
                    $this->getRepo()->getRefdataReference($phoneRefName)
                );
                $contact->setContactDetails($contactDetails);
                $contactDetails->getPhoneContacts()->add($contact);
            }

            if (!empty($data['phone_' . $phoneType])) {

                $contact->setPhoneNumber($data['phone_' . $phoneType]);

                $this->getRepo('PhoneContact')->save($contact);

                if ($version === null) {
                    $result->addMessage('Phone contact ' . $phoneType . ' created');
                    $result->setFlag('hasChanged', true);
                } elseif ($contact->getVersion() != $version) {
                    $result->addMessage('Phone contact ' . $phoneType . ' updated');
                    $result->setFlag('hasChanged', true);
                }

            } elseif ($contact->getId() > 0) {

                $contactDetails->getPhoneContacts()->removeElement($contact);

                $this->getRepo('PhoneContact')->delete($contact);
                $result->addMessage('Phone contact ' . $phoneType . ' deleted');
                $result->setFlag('hasChanged', true);
            }

            $this->handleSideEffectResult($result);
        }
    }

    private function maybeSaveEstablishmentAddress(Cmd $command, Licence $licence)
    {
        if (empty($command->getEstablishmentAddress())) {
            return;
        }

        $address = $command->getEstablishmentAddress();
        $address['contactType'] = ContactDetails::CONTACT_TYPE_ESTABLISHMENT_ADDRESS;
        $result = $this->handleSideEffect(SaveAddress::create($address));

        if ($result->getId('contactDetails') !== null) {
            $licence->setEstablishmentCd(
                $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
            );
        }

        $this->handleSideEffectResult($result);
    }

    private function maybeAddOrRemoveTransportConsultant(Cmd $command, Licence $licence)
    {
        if (empty($command->getConsultant())) {
            return;
        }

        $params = $command->getConsultant();

        $result = new Result();

        $result->setFlag('hasChanged', false);

        if ($params['add-transport-consultant'] === 'Y') {
            $address = $params['address'];
            $address['contactType'] = ContactDetails::CONTACT_TYPE_TRANSPORT_CONSULTANT;
            $result = $this->handleSideEffect(SaveAddress::create($address));

            $this->handleSideEffectResult($result);

            if ($result->getId('contactDetails') !== null) {
                $licence->setTransportConsultantCd(
                    $this->getRepo()->getReference(ContactDetails::class, $result->getId('contactDetails'))
                );
            }

            $transportConsultant = $licence->getTransportConsultantCd();

            $version = $transportConsultant->getVersion();

            $transportConsultant->setFao($params['transportConsultantName']);
            $transportConsultant->setWrittenPermissionToEngage($params['writtenPermissionToEngage']);
            $transportConsultant->setEmailAddress($params['contact']['email']);

            $this->getRepo('ContactDetails')->save($transportConsultant);

            if ($transportConsultant->getVersion() != $version) {
                $result->setFlag('hasChanged', true);
                $result->addMessage('Transport consultant updated');
            }

            $this->updatePhoneContacts($params['contact'], $transportConsultant);
        } elseif ($licence->getTransportConsultantCd()) {

            $licence->setTransportConsultantCd(null);

            $result->setFlag('hasChanged', true);
            $result->addMessage('Transport consultant deleted');
        }

        $this->handleSideEffectResult($result);
    }
}
