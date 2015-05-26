<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinancialStandingRate Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="financial_standing_rate",
 *    indexes={
 *        @ORM\Index(name="ix_financial_standing_rate_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_financial_standing_rate_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_financial_standing_rate_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_financial_standing_rate_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class FinancialStandingRate extends AbstractFinancialStandingRate
{

}
