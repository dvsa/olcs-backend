<?php
/**
 * BkmExpiry
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrfoPsvAuthBundle as Qry;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * BkmExpiry
 */
class BkmExpiry extends DynamicBookmark
{
    /**
     * Gets query to retrieve data
     *
     * @param array $data
     * @return Qry|null
     */
    public function getQuery(array $data)
    {
        return Qry::create(
            [
                'id' => $data['irfoPsvAuth']
            ]
        );
    }

    /**
     * Renders the bookmark
     *
     * @return string
     */
    public function render()
    {
        return (isset($this->data['expiryDate']) && ($this->data['expiryDate'] instanceof \DateTime))
            ? $this->data['expiryDate']->format('j F Y') : '';
    }
}
