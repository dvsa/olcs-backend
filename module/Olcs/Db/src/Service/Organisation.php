<?php

/**
 * Organisation Service
 *  - Takes care of the CRUD actions IrfoPartners entities
 */

namespace Olcs\Db\Service;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions IrfoPartners entities
 */
class Organisation extends ServiceAbstract
{
    protected function doUpdate($id, $data)
    {
        if (isset($data['irfoPartners']) && is_array($data['irfoPartners'])) {
            $organisation = $this->getEntityManager()->find($this->getEntityName(), $id);
            $manager = $this->getServiceLocator()->get('Olcs\Db\Service\Organisation\IrfoPartnersManager');

            $data['irfoPartners'] = $manager->processIrfoPartners($organisation, $data['irfoPartners']);
        }

        return parent::doUpdate($id, $data);
    }
}
