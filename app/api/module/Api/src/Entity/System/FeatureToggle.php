<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureToggle Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="feature_toggle",
 *    indexes={
 *        @ORM\Index(name="ix_feature_toggle_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_feature_toggle_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_feature_toggle_status", columns={"status"})
 *    }
 * )
 */
class FeatureToggle extends AbstractFeatureToggle
{

}
