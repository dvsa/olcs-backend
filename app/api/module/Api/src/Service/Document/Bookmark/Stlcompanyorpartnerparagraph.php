<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Stlcompanyorpartnerparagraph bookmark
 */
class Stlcompanyorpartnerparagraph extends AbstractLicenceType
{
    /**
     * Get the query to fetch data for the bookmark
     *
     * @param array $data Known data
     *
     * @return Qry
     */
    public function getQuery(array $data)
    {
        $queryClass = static::QUERY_CLASS;
        return $queryClass::create(['id' => $data[static::DATA_KEY], 'bundle' => ['organisation' => ['type']]]);
    }

    /**
     * Render the bookmark
     *
     * @return null|string
     */
    public function render()
    {
        if ($this->data['organisation']['type']['id'] === Organisation::ORG_TYPE_REGISTERED_COMPANY) {
            return $this->getSnippet('Stlcompanyorpartnerparagraph/ltd');
        }

        if ($this->data['organisation']['type']['id'] === Organisation::ORG_TYPE_LLP) {
            return $this->getSnippet('Stlcompanyorpartnerparagraph/llp');
        }

        return null;
    }
}
