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
    const ACTIVE_STATUS = 'always-active';
    const INACTIVE_STATUS = 'inactive';
    const CONDITIONAL_STATUS = 'conditionally-active';

    //constants describing known config keys
    const ADMIN_PERMITS = 'admin_permits';
    const INTERNAL_ECMT = 'internal_ecmt';
    const INTERNAL_PERMITS = 'internal_permits';
    const SELFSERVE_ECMT = 'ss_ecmt';
    const SELFSERVE_PERMITS = 'ss_permits';
    const BACKEND_ECMT = 'back_ecmt';
    const BACKEND_PERMITS = 'back_permits';

    const INTERNAL_SURRENDER = 'internal_surrender';
    const SELFSERVE_SURRENDER = 'ss_surrender';
    const BACKEND_SURRENDER = 'back_surrender';

    public static function create(/*string*/ $configName, /*string*/ $friendlyName, RefData $status)/*: FeatureToggle */
    {
        $instance = new self;
        $instance->configName = $configName;
        $instance->friendlyName = $friendlyName;
        $instance->status = $status;

        return $instance;
    }

    public function update(/*string*/ $configName, /*string*/ $friendlyName, RefData $status)/*: FeatureToggle */
    {
        $this->configName = $configName;
        $this->friendlyName = $friendlyName;
        $this->status = $status;

        return $this;
    }
}
