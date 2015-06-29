<?php

/**
 * CeasePsvDiscs.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Discs;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Class CeasePsvDiscs
 *
 * Cease discs dto.
 *
 * @package Dvsa\Olcs\Api\Domain\Command\Discs
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class CeasePsvDiscs extends AbstractIdOnlyCommand
{
    protected $licence;

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }
}
