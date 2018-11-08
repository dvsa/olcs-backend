<?php

/**
 * Create Irhp Permit Application
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

final class CreateIrhpPermitApplication extends AbstractCommand
{
    protected $window;

    protected $ecmtPermitApplication;

    /**
     * @return int
     */
    public function getWindow()
    {
        return $this->window;
    }

    /**
     * @return int
     */
    public function getEcmtPermitApplication()
    {
        return $this->ecmtPermitApplication;
    }
}
