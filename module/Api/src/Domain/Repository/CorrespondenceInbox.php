<?php

/**
 * Correspondence Inbox
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox as Entity;

/**
 * Correspondence Inbox
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CorrespondenceInbox extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getAllRequiringPrint($minDate, $maxDate)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.organisation', 'lo')
            ->with('lo.organisationUsers', 'lou')
            ->with('lou.user', 'louu')
            ->with('louu.contactDetails', 'louucd')
            ->with('document', 'd')
            ->with('d.continuationDetails', 'cd');

        $qb->andWhere($qb->expr()->eq('l.translateToWelsh', 0));
        $qb->andWhere($qb->expr()->eq($this->alias . '.accessed', 0));
        $qb->andWhere($qb->expr()->gte($this->alias . '.createdOn', ':minDate'));
        $qb->andWhere($qb->expr()->lte($this->alias . '.createdOn', ':maxDate'));
        // print queries don't care about the emailReminderSent flag;
        // whether a reminder has or hasn't been sent doesn't affect
        // whether it needs printing or not
        $qb->andWhere($qb->expr()->eq($this->alias . '.printed', 0));
        $qb->andWhere($qb->expr()->isNotNull('l.id'));

        $qb->setParameter('minDate', $minDate);
        $qb->setParameter('maxDate', $maxDate);

        return $qb->getQuery()->getResult();
    }

    public function getAllRequiringReminder($minDate, $maxDate)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.organisation', 'lo')
            ->with('lo.organisationUsers', 'lou')
            ->with('lou.user', 'louu')
            ->with('louu.contactDetails', 'louucd')
            ->with('document', 'd')
            ->with('d.continuationDetails', 'cd')
            ->with('cd.checklistDocument', 'cdd');

        $qb->andWhere($qb->expr()->eq($this->alias . '.accessed', 0));
        $qb->andWhere($qb->expr()->gte($this->alias . '.createdOn', ':minDate'));
        $qb->andWhere($qb->expr()->lte($this->alias . '.createdOn', ':maxDate'));
        // don't fetch ones we've already sent...
        $qb->andWhere($qb->expr()->eq($this->alias . '.emailReminderSent', 0));
        // ... but also ignore ones we may have printed but
        // *not* sent reminders for - e.g. if org has no email
        // addresses (somehow) - without this check we'd continually
        // try and email the reminder long after the print threshold
        // had been reached
        $qb->andWhere($qb->expr()->eq($this->alias . '.printed', 0));
        $qb->andWhere($qb->expr()->isNotNull('l.id'));
        $qb->andWhere($qb->expr()->eq('l.translateToWelsh', 0));

        $qb->setParameter('minDate', $minDate);
        $qb->setParameter('maxDate', $maxDate);

        return $qb->getQuery()->getResult();
    }
}
