<?php

namespace Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Processes EBSR pack
 */
class ProcessPackFailed extends AbstractIdOnlyCommand
{
    protected $organisation;

    protected $ebsrSub;

    /**
     * @return int
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    public function getEbsrSub()
    {
        return $this->ebsrSub;
    }
}
