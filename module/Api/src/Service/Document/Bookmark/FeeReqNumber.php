<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\FeeBundle as FeeQry;

/**
 * FeeReqNumber
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeReqNumber extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return FeeQry::create(['id' => $data['fee']]);
    }

    public function render()
    {
        if (isset($this->data[1]['interimStatus']['id']) &&
            $this->data[1]['interimStatus']['id'] == Application::INTERIM_STATUS_INFORCE) {

            if ($this->data[1]['interimStart'] instanceof \DateTime) {
                return $this->data[1]['interimStart']->format('d/m/Y');
            }

            $timeStamp = strtotime($this->data[1]['interimStart']);
            return \DateTime::createFromFormat('U', $timeStamp)->format('d/m/Y');
        }

        if ($this->data[0]['specifiedDate'] instanceof \DateTime) {
            return $this->data[0]['specifiedDate']->format('d/m/Y');
        }

        return $this->data['fee']['licence']['id'] . '/' . $this->data['fee']['id'];
    }
}
