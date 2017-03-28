<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataRetentionRule Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="data_retention_rule",
 *    indexes={
 *        @ORM\Index(name="fk_data_retention_rule_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_data_retention_rule_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class DataRetentionRule extends AbstractDataRetentionRule
{

}
