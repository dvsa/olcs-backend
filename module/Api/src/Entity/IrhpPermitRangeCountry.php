<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitRangeCountry Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_range_country",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_restricted_countries_irhp_permit_stock_ranges1_idx",
     *     columns={"irhp_permit_stock_range_id"}),
 *        @ORM\Index(name="fk_irhp_permit_range_restricted_countries_restricted_coun_idx",
     *     columns={"country_id"})
 *    }
 * )
 */
class IrhpPermitRangeCountry extends AbstractIrhpPermitRangeCountry
{

}
