<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;

/**
 * Pi Hearing
 */
final class Hearing extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiHearing';

    public function handleQuery(QueryInterface $query)
    {
        /** @var PiHearingEntity $hearing */
        $hearing = $this->getRepo()->fetchUsingId($query);

        $values = [
            'isTm' => $hearing->getPi()->getCase()->isTm()
        ];

        return $this->result(
            $hearing,
            [
                'piVenue' => [],
                'presidingTc' => [],
                'presidedByRole' => [],
                'pi' => [
                    'publicationLinks' => [
                        'publication' => [
                            'pubStatus'
                        ]
                    ]
                ],
            ],
            $values
        );
    }
}
