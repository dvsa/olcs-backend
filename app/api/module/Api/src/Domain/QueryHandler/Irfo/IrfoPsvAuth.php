<?php

/**
 * Irfo Psv Auth
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;

/**
 * Irfo Psv Auth
 */
class IrfoPsvAuth extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['Fee'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var IrfoPsvAuthEntity $psvAuth */
        $psvAuth = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $psvAuth,
            [
                'irfoPsvAuthType',
                'irfoPsvAuthNumbers',
                'countrys'
            ],
            [
                'actions' => $this->getActions($psvAuth)
            ]
        );
    }

    /**
     * Determine which actions can be performed on this entity. Array returned to mapper and used to hide any buttons
     * that are not valid actions.
     *
     * @param $psvAuth
     * @return array
     */
    private function getActions($psvAuth)
    {
        $actions = [];
        if ($this->isGrantable($psvAuth)) {
            $actions[] = 'grant';
        }
        return $actions;
    }

    /**
     * Is grantable? Yes if entity status is grantable, and application fee is paid or waived
     *
     * @param $psvAuth
     * @return bool
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function isGrantable($psvAuth)
    {
        $applicationFee = $this->getRepo('Fee')->fetchApplicationFeeByPsvAuthId($psvAuth->getId());
        $applicationFeeStatusId = $applicationFee->getFeeStatus()->getId();

        if ($psvAuth->isGrantable() &&
            in_array($applicationFeeStatusId, [FeeEntity::STATUS_PAID, FeeEntity::STATUS_WAIVED])
        ) {
            return true;
        }
        return false;
    }
}
