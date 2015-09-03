<?php

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Safety extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchSafetyDetailsUsingId($query);

        $data = $licence->jsonSerialize();

        $goodsOrPsv = $licence->getGoodsOrPsv()->getId();
        $data['canHaveTrailers'] = ($goodsOrPsv === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
        $data['hasTrailers'] = $licence->getTotAuthTrailers() > 0;

        return $data;
    }
}
