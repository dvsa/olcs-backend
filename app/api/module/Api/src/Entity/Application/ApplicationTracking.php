<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationTracking Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_tracking",
 *    indexes={
 *        @ORM\Index(name="fk_application_tracking_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_application_tracking_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_tracking_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="application_id_UNIQUE", columns={"application_id"})
 *    }
 * )
 */
class ApplicationTracking extends AbstractApplicationTracking
{
    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }

    protected function getCalculatedValues()
    {
        return ['application' => null];
    }
}
