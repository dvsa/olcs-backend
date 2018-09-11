<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

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
     *     columns={"country_id"}),
 *        @ORM\Index(name="fk_irhp_permit_range_country_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_range_country_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitRangeCountry extends AbstractIrhpPermitRangeCountry
{

}
