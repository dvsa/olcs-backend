<?php

/**
 * ContactDetails Service
 *  - Takes care of the CRUD actions PhoneContact entities
 */

namespace Olcs\Db\Service;

/**
 * ContactDetails Service
 *  - Takes care of the CRUD actions PhoneContact entities
 */
class ContactDetails extends ServiceAbstract
{
    protected function doUpdate($id, $data)
    {
        if (isset($data['phoneContacts']) && is_array($data['phoneContacts'])) {
            $phoneContact = $this->getEntityManager()->find($this->getEntityName(), $id);
            $manager = $this->getServiceLocator()->get('Olcs\Db\Service\ContactDetails\PhoneContactsManager');

            $data['phoneContacts'] = $manager->processPhoneContacts($phoneContact, $data['phoneContacts']);
        }

        return parent::doUpdate($id, $data);
    }
}
