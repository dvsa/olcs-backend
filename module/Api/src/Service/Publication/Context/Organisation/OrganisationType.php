<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Organisation;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Type of organisation
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class OrganisationType extends AbstractContext
{
    /**
     * @param PublicationLink $publicationLink
     * @param \ArrayObject $context
     */
    public function provide(PublicationLink $publicationLink, \ArrayObject $context)
    {
        $organisationType = [];
        $licence = $publicationLink->getLicence();
        $application = $publicationLink->getApplication();

        if (!empty($licence)) {
            $organisation = $licence->getOrganisation();
        } elseif (!empty($application)) {
            $organisation = $application->getLicence()->getOrganisation();
        }

        if (isset($organisation) && $organisation instanceOf OrganisationEntity) {
            /** @var RefDataEntity $organisationType */
            $organisationType = [
                'id' => $organisation->getType()->getId(),
                'description' => $organisation->getType()->getDescription()
            ];
        }

        $context->offsetSet('organisationType', $organisationType);
    }
}
