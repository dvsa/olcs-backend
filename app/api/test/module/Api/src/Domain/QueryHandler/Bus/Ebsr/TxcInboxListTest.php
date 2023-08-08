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
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;

/**
 * TxcInboxListTest
 */
class TxcInboxListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new TxcInboxList();
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);

        $this->mockedSmServices = [
            'LmcRbacMvc\Service\AuthorizationService' => m::mock('LmcRbacMvc\Service\AuthorizationService')
        ];

        parent::setUp();
    }

    /**
     * Set up a user for testing
     *
     * @param null $localAuthorityId
     * @param null $organisationId
     * @return m\Mock
     */
    private function getCurrentUser($localAuthorityId = null, $organisationId = null)
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        if (!empty($localAuthorityId)) {
            $localAuthority = new \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority();
            $localAuthority->setId($localAuthorityId);
        } else {
            $localAuthority = null;
        }
        $mockUser->setLocalAuthority($localAuthority);

        $organisationUsers = new ArrayCollection();

        if (!empty($organisationId)) {
            $organisation = new Organisation();
            $organisation->setId($organisationId);

            $organisationUser = new OrganisationUser();

            $organisationUser->setOrganisation($organisation);
            $organisationUsers->add($organisationUser);
        }
        $mockUser->setOrganisationUsers($organisationUsers);

        return $mockUser;
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->mockedSmServices['LmcRbacMvc\Service\AuthorizationService']
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($this->getCurrentUser(5));

        $mockResult = m::mock(TxcInboxEntity::class)->makePartial();

        $this->repoMap['TxcInbox']->shouldReceive('fetchList')
            ->andReturn([$mockResult])
            ->shouldReceive('fetchCount')
            ->andReturn(1);

        $result = $this->sut->handleQuery($query);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result['count']);
    }
}
