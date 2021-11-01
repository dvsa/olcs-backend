<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Interim Licence - Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimVehicles extends DynamicBookmark
{
    /**
     * Get the data required for the bookmark
     *
     * @return QueryInterface
     */
    public function getQuery(array $data): QueryInterface
    {
        return Qry::create(['id' => $data['application']]);
    }

    /**
     * Render the bookmark
     *
     * @return string
     */
    public function render()
    {
        if (!empty($this->data['interimAuthHgvVehicles']) && !empty($this->data['interimAuthLgvVehicles'])) {
            // HGV and LGV
            return sprintf(
                "%d Heavy goods vehicles\n\n%d Light goods vehicles",
                $this->data['interimAuthHgvVehicles'],
                $this->data['interimAuthLgvVehicles']
            );
        } elseif (!empty($this->data['interimAuthLgvVehicles'])) {
            // LGV only
            return sprintf(
                '%d Light goods vehicles',
                $this->data['interimAuthLgvVehicles']
            );
        }

        // HGV only
        return $this->data['interimAuthVehicles'];
    }
}
