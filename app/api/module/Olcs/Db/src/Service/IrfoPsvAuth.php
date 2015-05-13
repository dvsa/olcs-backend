<?php

/**
 * IRFO PSV Auth Service
 */

namespace Olcs\Db\Service;

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
            $id = parent::create($data);

            // update the record which sets IRFO file number correctly
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
                '\Olcs\Db\Entity\IrfoPsvAuthType',
                $data['irfoPsvAuthType']
            );

            // update IRFO file number
            $data['irfoFileNo'] = sprintf('%s/%d', $irfoPsvAuthType->getSectionCode(), $id);
        }

        return parent::doUpdate($id, $data);
    }
}
