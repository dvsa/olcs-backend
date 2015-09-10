<?php

/**
 * Checklist reminders
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Checklist reminders
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ChecklistReminders extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';

    public function handleQuery(QueryInterface $query)
    {
        $reminders = $this->getRepo()->fetchChecklistReminders(
            $query->getMonth(),
            $query->getYear(),
            $query->getIds()
        );
        return [
            'result' => $reminders,
            'count' => count($reminders)
        ];
    }
}
