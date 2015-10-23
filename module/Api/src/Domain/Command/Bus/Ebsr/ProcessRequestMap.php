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

    private $scale;

    private $template;

    /**
     * @return string
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
