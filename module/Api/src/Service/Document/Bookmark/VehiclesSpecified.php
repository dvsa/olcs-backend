<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;
use Doctrine\Common\Collections\Criteria;

/**
 * VehiclesSpecified bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehiclesSpecified extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->eq('removalDate', null));
        $bundle = [
            'licenceVehicles' => [
                'vehicle' => [
                    'psvType'
                ],
                'criteria' => $criteria
            ]
        ];
        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }
        $psvType = [
            Vehicle::PSV_TYPE_SMALL => 'Max 8 seats',
            Vehicle::PSV_TYPE_MEDIUM => '9 to 16 seats',
            Vehicle::PSV_TYPE_LARGE => 'Over 16 seats'
        ];

        $isGoods = $this->data['goodsOrPsv']['id'] === Licence::LICENCE_CATEGORY_GOODS_VEHICLE;

        $header[] = [
            'BOOKMARK1' => 'Registration mark',
            'BOOKMARK2' => $isGoods ? 'Plated weight' : 'Vehicle type',
            'BOOKMARK3' => 'To continue to be specified on licence (Y/N)'
        ];

        $rows = [];
        foreach ($this->data['licenceVehicles'] as $licenceVehicle) {
            $vehicle = $licenceVehicle['vehicle'];
            $rows[] = [
                'BOOKMARK1' => $vehicle['vrm'],
                'BOOKMARK2' => $isGoods ? $vehicle['platedWeight'] : $psvType[$vehicle['psvType']['id']],
                'BOOKMARK3' => ''
            ];
        }

        $sortedVehicles = $this->sortVehicles($rows);

        $rows = array_pad($sortedVehicles, 15, ['BOOKMARK1' => '', 'BOOKMARK2' => '', 'BOOKMARK3' => '']);

        $allRows = array_merge($header, $rows);
        $snippet = $this->getSnippet('CHECKLIST_3CELL_TABLE');
        $parser  = $this->getParser();

        $str = '';
        foreach ($allRows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    protected function sortVehicles($rows)
    {
        usort(
            $rows,
            function ($a, $b) {
                if ($a['BOOKMARK1'] == $b['BOOKMARK1']) {
                    return 0;
                } elseif ($a['BOOKMARK1'] < $b['BOOKMARK1']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        return $rows;
    }
}
