<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Organisation;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

/**
 * Trading As
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class TradingAs extends AbstractContext
{
    /**
     * @param PublicationLink $publicationLink
     * @param \ArrayObject $context
     */
    public function provide(PublicationLink $publicationLink, \ArrayObject $context)
    {
        $tradingAs = '';
        $licence = $publicationLink->getLicence();
        $application = $publicationLink->getApplication();

        if (!empty($licence)) {
            $organisation = $licence->getOrganisation();
        } elseif (!empty($application)) {
            $organisation = $application->getLicence()->getOrganisation();
        }

        if (isset($organisation) && $organisation instanceOf OrganisationEntity) {
            $tradingAs = $organisation->getName();

            $tradingName = $organisation->getTradingAs();

            if (!empty($tradingName)) {
                $tradingAs .= ' T/A ' . $tradingName;
            }
        }

        $context->offsetSet('tradingAs', $tradingAs);
    }
}
