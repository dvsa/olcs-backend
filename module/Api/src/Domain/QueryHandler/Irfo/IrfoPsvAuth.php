<?php

/**
 * Irfo Psv Auth
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;

/**
 * Irfo Psv Auth
 */
class IrfoPsvAuth extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['Fee'];

    /**
     * Handle query
     *
     * @param QueryInterface $query
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $irfoPsvAuth,
            [
                'irfoPsvAuthType',
                'irfoPsvAuthNumbers',
                'countrys'
            ],
            [
                'isGrantable' => $irfoPsvAuth->isGrantable(
                    $this->getRepo('Fee')->fetchApplicationFeeByPsvAuthId($irfoPsvAuth->getId())
                ),
                'isRefusable' => $irfoPsvAuth->isRefusable(),
                'isWithdrawable' => $irfoPsvAuth->isWithdrawable(),
                'isCnsable' => $irfoPsvAuth->isCnsable(),
                'isResetable' => $irfoPsvAuth->isResetable()
            ]
        );
    }
}
