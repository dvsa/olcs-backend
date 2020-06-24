<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\User\Role;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\UsersReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use OlcsTest\Bootstrap;
use Zend\I18n\View\Helper\Translate;

class UsersReviewServiceTest extends MockeryTestCase
{
    /** @var UsersReviewService review service */
    protected $sut;

    public function setUp(): void
    {
        $mockTranslator = m::mock(Translate::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();
        $sm = Bootstrap::getServiceManager();
        $sm->setService('translator', $mockTranslator);

        $this->sut = new UsersReviewService();
        $this->sut->setServiceLocator($sm);
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();
        $organisationUsers = new ArrayCollection();

        $organisationUsers->add($this->aMockOrganisationUser('Test1 Test1', 'test1@test.com'));
        $organisationUsers->add($this->aMockOrganisationUser('Test2 Test2', 'test2@test.com'));

        $mockOrganisation = m::mock(Licence::class)
            ->shouldReceive('getOrganisationUsers')
            ->andReturn($organisationUsers)
            ->once()
            ->getMock();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrganisation)
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            [
                ['value' => 'continuations.users-section.table.name', 'header' => true],
                ['value' => 'continuations.users-section.table.email', 'header' => true],
                ['value' => 'continuations.users-section.table.permission', 'header' => true]
            ],
            [
                ['value' => 'Test1 Test1'],
                ['value' => 'test1@test.com'],
                ['value' => 'role.role1_translated,role.role2_translated']
            ],
            [
                ['value' => 'Test2 Test2'],
                ['value' => 'test2@test.com'],
                ['value' => 'role.role1_translated,role.role2_translated']
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    public function testGetConfigWithEmptyData()
    {
        $continuationDetail = new ContinuationDetail();
        $organisationUsers = new ArrayCollection();

        $mockOrganisation = m::mock(Licence::class)
            ->shouldReceive('getOrganisationUsers')
            ->andReturn($organisationUsers)
            ->once()
            ->getMock();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrganisation)
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
           'emptyTableMessage' => 'continuations.users-section-empty-table-message_translated'
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    /**
     * @return m\LegacyMockInterface|m\MockInterface
     */
    public function aMockOrganisationUser($name, $emailAddress)
    {
        $mockPerson = m::mock()
            ->shouldReceive('getFullName')
            ->andReturn($name)
            ->once()
            ->getMock();
        $mockContactDetails = m::mock()
            ->shouldReceive('getPerson')
            ->andReturn($mockPerson)
            ->once()
            ->shouldReceive('getEmailAddress')
            ->andReturn($emailAddress)
            ->once()
            ->getMock();

        $mockUser = m::mock()
            ->shouldReceive('getContactDetails')
            ->andReturn($mockContactDetails)
            ->twice()
            ->shouldReceive('getRoles')
            ->andReturn($this->mockRoles())
            ->once()
            ->getMock();

        return m::mock()
            ->shouldReceive('getUser')
            ->andReturn($mockUser)
            ->once()
            ->getMock();
    }

    /**
     * @return ArrayCollection
     */
    public function mockRoles(): ArrayCollection
    {
        $mockRoles = new ArrayCollection();
        $mockRole1 = m::mock(Role::class)
            ->shouldReceive('getRole')
            ->andReturn('role1')
            ->getMock();
        $mockRole2 = m::mock(Role::class)
            ->shouldReceive('getRole')
            ->andReturn('role2')
            ->getMock();
        $mockRoles->add($mockRole1);
        $mockRoles->add($mockRole2);
        return $mockRoles;
    }
}
