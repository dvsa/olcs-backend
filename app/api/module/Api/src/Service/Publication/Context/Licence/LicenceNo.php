<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Licence;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Licence No
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class LicenceNo extends AbstractContext
{
    /**
     * @param PublicationLink $publicationLink
     * @param \ArrayObject $context
     */
    public function provide(PublicationLink $publicationLink, \ArrayObject $context)
    {
        $licenceNo = '';
        $licence = $publicationLink->getLicence();

        if (empty($licence)) {
            $application = $publicationLink->getApplication();
            if (!empty($application)) {
                $licence = $application->getLicence();
            }
        }

        if (isset($licence)) {
            $licenceNo = $licence->getLicNo();
        }

        $context->offsetSet('licenceNo', $licenceNo);
    }
}
