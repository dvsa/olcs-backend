<?php

namespace Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\User;

/**
 * Process Ebsr map request
 */
class ProcessRequestMap extends AbstractIdOnlyCommand
{
    use User;

    protected $scale;

    protected $licence;

    protected $regNo;

    protected $fromNewEbsr;

    /**
     * @return string
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return int
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * @return bool
     */
    public function getFromNewEbsr()
    {
        return $this->fromNewEbsr;
    }
}
