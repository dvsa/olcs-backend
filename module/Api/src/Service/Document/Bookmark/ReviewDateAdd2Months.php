<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * ReviewDateAdd2Month bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReviewDateAdd2Months extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return Qry::create(['id' => $data['licence']]);
    }

    public function render()
    {
        if (isset($this->data['reviewDate'])) {
            $target = new \DateTime($this->data['reviewDate']);
            $target->add(new \DateInterval('P2M'));
            return $target->format('d/m/Y');
        }
        return '';
    }
}
