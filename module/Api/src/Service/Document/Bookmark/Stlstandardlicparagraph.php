<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Api\Domain\Repository\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Stlstandardlicparagraph bookmark
 */
class Stlstandardlicparagraph extends DynamicBookmark
{
    /**
     * Get the DTO query used to fetch data for this bookmark
     *
     * @param array $data Known data
     *
     * @return QueryInterface
     */
    public function getQuery(array $data)
    {
        if (isset($data['application'])) {
            return ApplicationBundle::create(['id' => $data['application']]);
        }
        return LicenceBundle::create(['id' => $data['licence']]);
    }

    /**
     * Render bookmark
     *
     * @return null|string
     */
    public function render()
    {
        if ($this->data['licenceType']['id'] === Licence::LICENCE_TYPE_STANDARD_NATIONAL
            || $this->data['licenceType']['id'] === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ) {
            return $this->getSnippet('Stlstandardlicparagraph/standard');
        }

        return null;
    }
}
