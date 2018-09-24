<?php

/**
 * UpdatePermitFee
 *
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Class UpdatePermitFee
 *
 * Updates fees associated with Permit Application on change of # permits required/type.
 *
 */
final class UpdatePermitFee extends AbstractCommand
{
    protected $ecmtPermitApplicationId;

    protected $licenceId;

    protected $permitsRequired;

    protected $permitType;

    protected $receivedDate;

    /**
     * @return mixed
     */
    public function getEcmtPermitApplicationId()
    {
        return $this->ecmtPermitApplicationId;
    }

    /**
     * @return mixed
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }

    /**
     * @return mixed
     */
    public function getPermitsRequired()
    {
        return $this->permitsRequired;
    }

    /**
     * @return mixed
     */
    public function getPermitType()
    {
        return $this->permitType;
    }

    /**
    * @return mixed
    */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }
}
