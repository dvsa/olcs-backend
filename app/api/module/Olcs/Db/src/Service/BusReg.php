<?php

/**
 * BusReg Service
 *  - Takes care of the CRUD actions BusReg entities
 */

namespace Olcs\Db\Service;

/**
 * BusReg Service
 *  - Takes care of the CRUD actions BusReg entities
 */
class BusReg extends ServiceAbstract
{
    protected function doUpdate($id, $data)
    {
        if (isset($data['otherServices']) && is_array($data['otherServices'])) {
            $busReg = $this->getEntityManager()->find($this->getEntityName(), $id);
            $manager = $this->getServiceLocator()->get('Olcs\Db\Service\BusReg\OtherServicesManager');

            $data['otherServices'] = $manager->processOtherServiceNumbers($busReg, $data['otherServices']);
        }
        return parent::doUpdate($id, $data);
    }
}
