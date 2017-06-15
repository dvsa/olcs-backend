<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence LicenceEntity */
        $licence = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licence,
            [],
            [
                'canBecomeSpecialRestricted' => $licence->canBecomeSpecialRestricted(),
                'canUpdateLicenceType' => $this->isGranted(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence) &&
                    !$licence->isSpecialRestricted() && $this->isGranted(Permission::INTERNAL_USER),
                'doesChangeRequireVariation' => $this->isGranted(Permission::SELFSERVE_USER)
            ]
        );
    }
}
