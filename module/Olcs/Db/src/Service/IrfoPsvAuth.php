<?php

/**
 * IRFO PSV Auth Service
 */

namespace Olcs\Db\Service;

use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;

/**
 * IRFO PSV Auth Service
 */
class IrfoPsvAuth extends ServiceAbstract
{
    /**
     * Should enter a value into the database and return the identifier for the record that has been created.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data)
    {
        if (empty($data['irfoFileNo'])) {
            // set IRFO file number as it's not nullable
            $data['irfoFileNo'] = '';

            // create IRFO PSV Auth
            // update which follows will deal with irfoPsvAuthNumbers as well
            $id = parent::create(array_merge($data, ['irfoPsvAuthNumbers' => null]));

            // update the record
            // sets IRFO file number and auth numbers correctly
            // merge the data with the record just created in case create sets some extra fields
            $this->update($id, array_merge($data, $this->get($id)));
        } else {
            // create IRFO PSV Auth
            $id = parent::create($data);
        }

        return $id;
    }

    /**
     * Update an entity
     *
     * @param mixed $id
     * @param array $data
     *
     * @return mixed
     */
    protected function doUpdate($id, $data)
    {
        if (!empty($data['irfoPsvAuthType'])) {
            // get IrfoPsvAuthType
            $irfoPsvAuthType = $this->getEntityManager()->find(
                IrfoPsvAuthType::class,
                $data['irfoPsvAuthType']
            );

            // update IRFO file number
            $data['irfoFileNo'] = sprintf('%s/%d', $irfoPsvAuthType->getSectionCode(), $id);
        }

        if (isset($data['irfoPsvAuthNumbers']) && is_array($data['irfoPsvAuthNumbers'])) {
            $irfoPsvAuth = $this->getEntityManager()->find($this->getEntityName(), $id);

            $data['irfoPsvAuthNumbers'] = $this->getServiceLocator()
                ->get('Olcs\Db\Service\IrfoPsvAuth\IrfoPsvAuthNumbersManager')
                ->processIrfoPsvAuthNumbers($irfoPsvAuth, $data['irfoPsvAuthNumbers']);
        }

        return parent::doUpdate($id, $data);
    }
}
