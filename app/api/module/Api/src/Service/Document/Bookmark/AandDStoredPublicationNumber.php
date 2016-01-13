<?php

/**
 * AandDStoredPublicationNumber bookmark
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Doctrine\Common\Collections\Criteria;

/**
 * AandDStoredPublicationNumber bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AandDStoredPublicationNumber extends AbstractAandDStoredPublication
{
    const APP_NO_PUBLISHED = '[Application Not Published - No Date]';

    public function render()
    {
        // unfortunately criteria doesn't work in this case, so we need to filter records manually
        $publicationLinks = $this->filterPublicationLinks($this->data['publicationLinks']);

        if (!count($publicationLinks)) {
            return self::APP_NO_PUBLISHED;
        }
        $publicationLinks = $this->sortPublicationLinks($publicationLinks);

        return $publicationLinks[0]['publication']['publicationNo'];
    }
}
