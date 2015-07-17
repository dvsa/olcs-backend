<?php

/**
 * CeaseDiscs.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Discs;

/**
 * Class CeaseDisc
 *
 * Cease discs dto.
 *
 * @package Dvsa\Olcs\Api\Domain\Command\Discs
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class CeaseGoodsDiscs extends \Dvsa\Olcs\Transfer\Command\AbstractCommand
{
    protected $licenceVehicles;

    /**
     * @return mixed
     */
    public function getLicenceVehicles()
    {
        return $this->licenceVehicles;
    }
}
