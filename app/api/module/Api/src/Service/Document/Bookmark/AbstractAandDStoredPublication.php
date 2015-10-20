<?php

/**
 * Abstract AandDStoredPublication class
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;
use  Dvsa\Olcs\Api\Entity\Publication\PublicationSection;

/**
 * Abstract AandDStoredPublication class
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractAandDStoredPublication extends DynamicBookmark
{
    protected $allowedSections = [
        PublicationSection::APP_NEW_SECTION,
        PublicationSection::VAR_NEW_SECTION,
        PublicationSection::SCHEDULE_4_NEW,
        PublicationSection::SCHEDULE_1_NI_NEW,
        PublicationSection::SCHEDULE_1_NI_UNTRUE
    ];

    public function getQuery(array $data)
    {
        $bundle = [
            'publicationLinks' => [
                'publication',
                'publicationSection'
            ]
        ];
        return Qry::create(['id' => $data['application'], 'bundle' => $bundle]);
    }

    protected function sortPublicationLinks($publicationLinks)
    {
        usort(
            $publicationLinks,
            function ($a, $b) {
                if ($a['publication']['pubDate'] == $b['publication']['pubDate']) {
                    if ($a['publication']['id'] > $b['publication']['id']) {
                        return -1;
                    } elseif ($a['publication']['id'] < $b['publication']['id']) {
                        return 1;
                    } else {
                        return 0;
                    }
                } elseif ($a['publication']['pubDate'] > $b['publication']['pubDate']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        return $publicationLinks;
    }

    protected function filterPublicationLinks($publicationLinks)
    {
        $filteredPublicationLinks = [];

        foreach ($publicationLinks as $publicationLink) {
            if (in_array($publicationLink['publicationSection']['id'], $this->allowedSections)) {
                $filteredPublicationLinks[] = $publicationLink;
            }
        }
        return $filteredPublicationLinks;
    }
}
