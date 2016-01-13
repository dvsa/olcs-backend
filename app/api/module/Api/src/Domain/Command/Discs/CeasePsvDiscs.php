<?php

/**
 * CeasePsvDiscs.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Discs;

/**
 * Class CeasePsvDiscs
 *
 * Cease discs dto.
 *
 * @package Dvsa\Olcs\Api\Domain\Command\Discs
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class CeasePsvDiscs extends \Dvsa\Olcs\Transfer\Command\AbstractCommand
{
    protected $discs;

    /**
     * @return mixed
     */
    public function getDiscs()
    {
        return $this->discs;
    }
}
