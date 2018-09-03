<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitJurisdictionQuota Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_jurisdiction_quota",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_jurisdiction_quotas_irhp_jurisdictions1_idx",
     *     columns={"irhp_jurisdiction_id"}),
 *        @ORM\Index(name="fk_irhp_jurisdiction_quotas_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"})
 *    }
 * )
 */
class IrhpPermitJurisdictionQuota extends AbstractIrhpPermitJurisdictionQuota
{

}
