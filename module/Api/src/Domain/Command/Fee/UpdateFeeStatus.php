<?php

namespace Dvsa\Olcs\Api\Domain\Command\Fee;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;

class UpdateFeeStatus extends AbstractCommand
{
    use Identity;

    protected $status;

    /*
     * return
     */
    public function getStatus()
    {
        return $this->status;
    }
}
