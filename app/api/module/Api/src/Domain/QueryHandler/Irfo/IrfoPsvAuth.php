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
        $isGrantable = false;

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($query);

        $applicationFee = $this->getRepo('Fee')->fetchApplicationFeeByPsvAuthId($irfoPsvAuth->getId());

        if ($applicationFee instanceof FeeEntity) {
            $applicationFeeStatusId = $applicationFee->getFeeStatus()->getId();

            if ($irfoPsvAuth->isGrantable($applicationFeeStatusId)
            ) {
                $isGrantable = true;
            }
        }

        return $this->result(
            $irfoPsvAuth,
            [
                'irfoPsvAuthType',
                'irfoPsvAuthNumbers',
                'countrys'
            ],
            [
                'isGrantable' => $isGrantable
            ]
        );
    }
}
