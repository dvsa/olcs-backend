<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

/**
 * PreviousConviction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="previous_conviction",
 *    indexes={
 *        @ORM\Index(name="ix_previous_conviction_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_previous_conviction_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_previous_conviction_title", columns={"title"})
 *    }
 * )
 */
class PreviousConviction extends AbstractPreviousConviction implements OrganisationProviderInterface
{
    protected function getCalculatedValues()
    {
        return [
            'application' => null
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelatedOrganisation()
    {
        if (!$this->getApplication()) {
            return null;
        }

        return $this->getApplication()->getLicence()->getOrganisation();
    }
}
