<?php

/**
 * Correspondence inbox test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\CorrespondenceInbox as CorrespondenceInboxRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Correspondence inbox test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CorrespondenceInboxTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(CorrespondenceInboxRepo::class);
    }

    public function testGetAllRequiringPrint()
    {
        $minDate = '2015-01-01';
        $maxDate = '2016-01-01';

        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lo.organisationUsers', 'lou')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lou.user', 'louu')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('louu.contactDetails', 'louucd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('document', 'd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('d.continuationDetails', 'cd')->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('l.translateToWelsh', 0)->once()->andReturn('condition1');
        $qb->shouldReceive('andWhere')->with('condition1')->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.accessed', 0)->once()->andReturn('condition2');
        $qb->shouldReceive('andWhere')->with('condition2')->once()->andReturnSelf();

        $qb->shouldReceive('expr->gte')->with('m.createdOn', ':minDate')->once()->andReturn('condition3');
        $qb->shouldReceive('andWhere')->with('condition3')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('minDate', $minDate)->once()->andReturnSelf();

        $qb->shouldReceive('expr->lte')->with('m.createdOn', ':maxDate')->once()->andReturn('condition4');
        $qb->shouldReceive('andWhere')->with('condition4')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('maxDate', $maxDate)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.printed', 0)->once()->andReturn('condition5');
        $qb->shouldReceive('andWhere')->with('condition5')->once()->andReturnSelf();

        $qb->shouldReceive('expr->isNotNull')->with('l.id')->once()->andReturn('condition6');
        $qb->shouldReceive('andWhere')->with('condition6')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($qb);
        $qb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->getAllRequiringPrint($minDate, $maxDate);
    }

    public function testGetAllRequiringReminder()
    {
        $minDate = '2015-01-01';
        $maxDate = '2016-01-01';

        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lo.organisationUsers', 'lou')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lou.user', 'louu')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('louu.contactDetails', 'louucd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('document', 'd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('d.continuationDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.checklistDocument', 'cdd')->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.accessed', 0)->once()->andReturn('condition1');
        $qb->shouldReceive('andWhere')->with('condition1')->once()->andReturnSelf();

        $qb->shouldReceive('expr->gte')->with('m.createdOn', ':minDate')->once()->andReturn('condition2');
        $qb->shouldReceive('andWhere')->with('condition2')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('minDate', $minDate)->once()->andReturnSelf();

        $qb->shouldReceive('expr->lte')->with('m.createdOn', ':maxDate')->once()->andReturn('condition3');
        $qb->shouldReceive('andWhere')->with('condition3')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('maxDate', $maxDate)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.emailReminderSent', 0)->once()->andReturn('condition4');
        $qb->shouldReceive('andWhere')->with('condition4')->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.printed', 0)->once()->andReturn('condition5');
        $qb->shouldReceive('andWhere')->with('condition5')->once()->andReturnSelf();

        $qb->shouldReceive('expr->isNotNull')->with('l.id')->once()->andReturn('condition6');
        $qb->shouldReceive('andWhere')->with('condition6')->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('l.translateToWelsh', 0)->once()->andReturn('condition7');
        $qb->shouldReceive('andWhere')->with('condition7')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($qb);
        $qb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->getAllRequiringReminder($minDate, $maxDate);
    }
}
