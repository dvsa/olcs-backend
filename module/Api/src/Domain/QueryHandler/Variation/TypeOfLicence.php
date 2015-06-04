<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicence extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity $entity */
        $entity = $this->getRepo()->fetchUsingId($query);

        $licence = $entity->getLicence();

        $data = $entity->jsonSerialize();

        $data['canBecomeSpecialRestricted'] = $licence->canBecomeSpecialRestricted();
        $data['canUpdateLicenceType'] = $this->isGranted(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence);
        $data['currentLicenceType'] = $licence->getLicenceType()->getId();

        return $data;
    }
}
