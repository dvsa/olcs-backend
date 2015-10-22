<?php

namespace Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Process Ebsr map request
 */
class ProcessRequestMap extends AbstractIdOnlyCommand
{
    public $scale;

    /**
     * @return string
     */
    public function getScale()
    {
        return $this->scale;
    }
}
