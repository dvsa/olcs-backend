<?php

/**
 * PSV Discs Set isPrinting off and discNo
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\AbstractRawQuery;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Psv Discs Set isPrinting off and discNo
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDiscsSetIsPrintingOffAndDiscNo extends AbstractRawQuery
{
    protected $templateMap = [
        'pd' => PsvDisc::class
    ];

    protected $queryTemplate = 'UPDATE {pd},
       (SELECT @n := :startNumber - 1) m
          SET {pd.isPrinting} = 0, {pd.discNo} = @n := @n + 1, {pd.issuedDate} = :issuedDate,
            {pd.lastModifiedOn} = NOW(), {pd.lastModifiedBy} = :currentUserId
          WHERE {pd.id} IN (:ids)';

    /**
     * {@inheritdoc}
     */
    protected function getParams()
    {
        $today = new DateTime();

        return [
            'issuedDate' => $today->format('Y-m-d H:i:s')
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParamTypes()
    {
        return [
            'issuedDate' => \PDO::PARAM_STR
        ];
    }
}
