<?php

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Safety extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = [
        'Licence'
    ];

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $licence = $application->getLicence();

        $this->getRepo('Licence')->fetchSafetyDetailsUsingId($licence);

        $data = $application->jsonSerialize();

        $goodsOrPsv = $application->getGoodsOrPsv()->getId();

        $data['canHaveTrailers'] = ($goodsOrPsv === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
        $data['hasTrailers'] = $application->getTotAuthTrailers() > 0;

        return $data;
    }
}
