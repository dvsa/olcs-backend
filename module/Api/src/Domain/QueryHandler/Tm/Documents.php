<?php

/**
 * Tm Qualifications Documents List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Role;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Tm Qualifications Documents List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Documents extends AbstractQueryHandler
{
    protected $repoServiceName = 'Document';

    public function handleQuery(QueryInterface $query)
    {
        $currentUser = $this->getCurrentUser();
        $roles = $currentUser->getRoles()->getValues();

        /** @var \Dvsa\Olcs\Api\Entity\User\Role $role */
        foreach ($roles as $role) {
            if ($role->getRole() == \Dvsa\Olcs\Api\Entity\User\Role::ROLE_INTERNAL_LIMITED_READ_ONLY) {
                return [
                    'result' => null,
                    'count' => 0
                ];
            }
        }

        $documents = $this->getRepo()->fetchListForTm($query->getId());
        return [
            'result' => $this->resultList($documents),
            'count'  => count($documents)
        ];
    }
}
