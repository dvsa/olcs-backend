<?php

/**
 * BusRegSearchViewList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\BusRegSearchView;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView\BusRegSearchViewList;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * BusRegSearchViewList Test
 */
class BusRegSearchViewListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusRegSearchViewList();
        $this->mockRepo('BusRegSearchView', Repository\BusRegSearchView::class);
        $this->mockedSmServices = [
            'ZfcRbac\Service\AuthorizationService' => m::mock('ZfcRbac\Service\AuthorizationService')
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
            $organisation = new OrganisationEntity();
            $organisation->setId($organisationId);

            $organisationUser = new OrganisationUserEntity();

            $organisationUser->setOrganisation($organisation);
            $organisationUsers->add($organisationUser);
        }
        $mockUser->setOrganisationUsers($organisationUsers);

        return $mockUser;
    }

    public function testHandleQueryOperator()
    {
        $organisationId = 1;
        $localAuthorityId = null;

        $currentUser = $this->getCurrentUser($localAuthorityId, $organisationId);

        // check for operator
        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']->shouldReceive('isGranted')
            ->with(m::type('string'), null)->andReturn(true);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->andReturn($currentUser);

        $query = Qry::create(
            [
                'licNo' => 'UB1234567',
                'status' => 'breg_s_cancellation',
                'organisationName' => 'ORG1'
            ]
        );

        $mockRecord = m::mock();
        $mockRecord->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $this->repoMap['BusRegSearchView']
            ->shouldReceive('fetchList')
            ->once()
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockRecord])
            ->shouldReceive('fetchCount')
            ->once()
            ->with($query)
            ->andReturn(1);

        $expected = [
            'result' => [
                ['foo' => 'bar']
            ],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
        $this->assertNotEmpty($query->getOrganisationId());
    }

    public function testHandleQueryLocalAuthority()
    {
        $organisationId = null;
        $localAuthorityId = 1;
        $currentUser = $this->getCurrentUser($localAuthorityId, $organisationId);

        // checks for operator before local authority so we mock these first
        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::OPERATOR_ADMIN, null)->andReturn(false);
        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::OPERATOR_USER, null)->andReturn(false);
        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::LOCAL_AUTHORITY_USER, null)->andReturn(true);
        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(true);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->andReturn($currentUser);

        $query = Qry::create(
            [
                'licNo' => 'UB1234567',
                'status' => 'breg_s_cancellation',
                'organisationName' => 'ORG1'
            ]
        );

        $mockRecord = m::mock();
        $mockRecord->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $this->repoMap['BusRegSearchView']
            ->shouldReceive('fetchList')
            ->once()
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockRecord])
            ->shouldReceive('fetchCount')
            ->once()
            ->with($query)
            ->andReturn(1);

        $expected = [
            'result' => [
                ['foo' => 'bar']
            ],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
        $this->assertEmpty($query->getOrganisationId());
    }
}
