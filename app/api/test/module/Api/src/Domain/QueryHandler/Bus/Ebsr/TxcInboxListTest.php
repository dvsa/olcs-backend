<?php

/**
 * TxcInboxList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\Common\Collections\ArrayCollectionTest;
use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr\TxcInboxList;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\Olcs\Transfer\Query\Bus\Ebsr\TxcInboxList as Qry;
use Mockery as m;

/**
 * TxcInboxListTest
 */
class TxcInboxListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TxcInboxList();
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);

        $this->mockedSmServices = [
            'ZfcRbac\Service\AuthorizationService' => m::mock('ZfcRbac\Service\AuthorizationService')
        ];

        parent::setUp();
    }

    private function getCurrentUser()
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);

        $mockUser->shouldReceive('getLocalAuthority')
            ->andReturnNull();

        $mockUser->shouldReceive('getRelatedOrganisation')
            ->andReturnNull();

        $organisationUsers = new ArrayCollection([$mockUser]);
        $mockUser->shouldReceive('getOrganisationUsers')
            ->andReturn($organisationUsers);

        return $mockUser;
    }

    private function getCurrentOrganisationUser()
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        $mockUser->shouldReceive('getLocalAuthority')
            ->andReturnNull();

        $organisation = m::mock(Organisation::class)->makePartial();

        $mockUser->shouldReceive('getRelatedOrganisation')
            ->andReturn($organisation);

        $organisationUsers = new ArrayCollection([$mockUser]);
        $mockUser->shouldReceive('getOrganisationUsers')
            ->andReturn($organisationUsers);

        return $mockUser;
    }

    public function testHandleQueryForLocalAuthority()
    {
        $query = Qry::create([]);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($this->getCurrentUser());

        $mockResult = m::mock(TxcInboxEntity::class)->makePartial();

        $this->repoMap['TxcInbox']->shouldReceive('fetchUnreadListForLocalAuthority')
            ->andReturn([$mockResult]);
        $result = $this->sut->handleQuery($query);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result['count']);
    }

    public function testHandleQueryForOrganisation()
    {
        $query = Qry::create([]);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->twice()
            ->andReturn($this->getCurrentOrganisationUser());

        $mockResult = m::mock(TxcInboxEntity::class)->makePartial();

        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')
            ->andReturn([$mockResult]);
        $result = $this->sut->handleQuery($query);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result['count']);
    }
}
