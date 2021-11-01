<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Licence - Total vehicle authority
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AuthorisedVehicles extends DynamicBookmark
{
    /**
     * Get the data required for the bookmark
     *
     * @return QueryInterface
     */
    public function getQuery(array $data): QueryInterface
    {
        return Qry::create(['id' => $data['licence']]);
    }

    /**
     * Render the bookmark
     *
     * @return string
     */
    public function render()
    {
        if (!empty($this->data['totAuthHgvVehicles']) && !empty($this->data['totAuthLgvVehicles'])) {
            // HGV and LGV
            return sprintf(
                "%d Heavy goods vehicles\n\n%d Light goods vehicles",
                $this->data['totAuthHgvVehicles'],
                $this->data['totAuthLgvVehicles']
            );
        } elseif (!empty($this->data['totAuthLgvVehicles'])) {
            // LGV only
            return sprintf(
                '%d Light goods vehicles',
                $this->data['totAuthLgvVehicles']
            );
        }

        // HGV only
        return $this->data['totAuthVehicles'];
    }
}
