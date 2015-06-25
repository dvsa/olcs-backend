<?php

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehicles extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licence,
            [],
            [
                'canReprint' => true,
                'canTransfer' => $this->canTransfer($licence),
                'canExport' => $this->isGranted(Permission::SELFSERVE_USER),
                'canPrintVehicle' => $this->isGranted(Permission::INTERNAL_USER)
            ]
        );
    }

    private function canTransfer(LicenceEntity $licence)
    {
        $criteria = Criteria::create();

        // Where statuses are active
        $criteria->andWhere(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED
                ]
            )
        );

        // Ignore the current licence
        $criteria->andWhere(
            $criteria->expr()->neq('id', $licence->getId())
        );

        // Where the licence category is the same
        $criteria->andWhere(
            $criteria->expr()->eq('goodsOrPsv', $licence->getGoodsOrPsv())
        );

        // Where not special restricted
        if ($licence->getGoodsOrPsv()->getId() == LicenceEntity::LICENCE_CATEGORY_PSV) {
            $criteria->andWhere(
                $criteria->expr()->neq('licenceType', LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED)
            );
        }

        $otherLicences = $licence->getOrganisation()->getLicences()->matching($criteria);

        return $otherLicences->count() > 0;
    }
}
