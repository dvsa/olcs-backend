<?php
/**
 * Operating Centre Service
 *  - Takes care of the CRUD actions on Operating Centre entities
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Olcs\Db\Service;

use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class OperatingCentre extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array();
    }

    /**
     * Gets an organisation record by licenceId.
     * @todo Possibly use join... needs performence review
     *
     * @param int $id
     *
     * @return array
     */
    public function getByLicenceId($id)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $ocResult = $this->getEntityManager()->getRepository('OlcsEntities\Entity\OperatingCentre')->findBy(['licence' => $id]);
        if (empty($ocResult)) {
            return null;
        }

        // Assemble the data packets. Because of absence of child entity support,
        // we need to back-fill the addresses here.
        $data=Array();
        foreach($ocResult as $ocEntity) {
            $ocItem=$this->extract($ocEntity);
            $address = $this->getEntityManager()->getRepository('OlcsEntities\Entity\Address')->findOneBy(['id' => $ocItem['address']]);
            $ocItem['address']=$this->extract($address);
            array_push($data,$ocItem);
        }

        return $data;
    }

}
