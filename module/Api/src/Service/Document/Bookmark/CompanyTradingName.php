<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Company trading name bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CompanyTradingName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $bundle = [
            'correspondenceCd' => [
                'address'
            ],
            'organisation' => [
                'tradingNames'
            ]
        ];
        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        $address = isset($this->data['correspondenceCd']['address']) ? $this->data['correspondenceCd']['address'] : [];

        $formatter = new Formatter\OrganisationName();
        $formatter->setSeparator("\n");

        return implode(
            "\n",
            array_filter(
                [
                    $formatter->format($this->data['organisation']),
                    Formatter\Address::format($address)
                ]
            )
        );
    }
}
