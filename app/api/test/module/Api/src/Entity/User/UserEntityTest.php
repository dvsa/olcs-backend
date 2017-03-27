<?php

namespace Dvsa\OlcsTest\Api\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\User\Role as RoleEntity;
use Dvsa\Olcs\Api\Entity\User\User as Entity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Mockery as m;

/**
 * User Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class UserEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * @dataProvider getUserTypeDataProvider
     *
     */
    public function testGetUserType(
        $team,
        $localAuthority,
        $transportManager,
        $partnerContactDetails,
        $expected,
        $expectedIsInternal
    ) {
        $this->entity->setTeam($team);
        $this->entity->setLocalAuthority($localAuthority);
        $this->entity->setTransportManager($transportManager);
        $this->entity->setPartnerContactDetails($partnerContactDetails);

        $this->assertEquals($expected, $this->entity->getUserType());
        $this->assertEquals($expectedIsInternal, $this->entity->isInternal());
    }

    public function getUserTypeDataProvider()
    {
        $team = m::mock(TeamEntity::class);
        $localAuthority = m::mock(LocalAuthorityEntity::class);
        $transportManager = m::mock(TransportManagerEntity::class);
        $partnerContactDetails = m::mock(ContactDetailsEntity::class);

        return [
            [$team, null, null, null, Entity::USER_TYPE_INTERNAL, true],
            [null, $localAuthority, null, null, Entity::USER_TYPE_LOCAL_AUTHORITY, false],
            [null, null, $transportManager, null, Entity::USER_TYPE_TRANSPORT_MANAGER, false],
            [null, null, null, $partnerContactDetails, Entity::USER_TYPE_PARTNER, false],
            [null, null, null, null, Entity::USER_TYPE_OPERATOR, false],
        ];
    }

    public function testCreateInternal()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_INTERNAL_LIMITED_READ_ONLY);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'N',
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        $entity = Entity::create('pid', Entity::USER_TYPE_INTERNAL, $data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertInstanceOf(\DateTime::class, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_INTERNAL, $entity->getUserType());
        $this->assertEquals($data['team'], $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals('DVSA', $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdateInternal()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_INTERNAL_LIMITED_READ_ONLY);

        $data = [
            'userType' => Entity::USER_TYPE_INTERNAL,
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'Y',
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_PARTNER,
            [
                'loginId' => 'currentLoginId',
                'translateToWelsh' => 'N',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertEquals(null, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_INTERNAL, $entity->getUserType());
        $this->assertEquals($data['team'], $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals('DVSA', $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testCreateTransportManager()
    {
        $adminRole = m::mock(RoleEntity::class)->makePartial();
        $adminRole->setRole(RoleEntity::ROLE_OPERATOR_ADMIN);

        $orgName = 'Org Name';
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setName($orgName);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$adminRole],
            'translateToWelsh' => 'N',
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [$org],
        ];

        $entity = Entity::create('pid', Entity::USER_TYPE_TRANSPORT_MANAGER, $data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertInstanceOf(\DateTime::class, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_TRANSPORT_MANAGER, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals($data['transportManager'], $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(1, $entity->getOrganisationUsers()->count());
        $this->assertEquals('Y', $entity->getOrganisationUsers()->first()->getIsAdministrator());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdateTransportManager()
    {
        $nonAdminRole = m::mock(RoleEntity::class)->makePartial();
        $nonAdminRole->setRole(RoleEntity::ROLE_OPERATOR_USER);

        $orgName = 'Org Name';
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setName($orgName);

        $data = [
            'userType' => Entity::USER_TYPE_TRANSPORT_MANAGER,
            'loginId' => 'loginId',
            'roles' => [$nonAdminRole],
            'translateToWelsh' => 'Y',
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [$org],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_PARTNER,
            [
                'loginId' => 'currentLoginId',
                'translateToWelsh' => 'N',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertEquals(null, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_TRANSPORT_MANAGER, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals($data['transportManager'], $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(1, $entity->getOrganisationUsers()->count());
        $this->assertEquals('N', $entity->getOrganisationUsers()->first()->getIsAdministrator());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testCreatePartner()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_PARTNER_USER);

        $orgName = 'Org Name';
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->setDescription($orgName);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'N',
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => $contactDetails,
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        $entity = Entity::create('pid', Entity::USER_TYPE_PARTNER, $data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertInstanceOf(\DateTime::class, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_PARTNER, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals($data['partnerContactDetails'], $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdatePartner()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_PARTNER_USER);

        $orgName = 'Org Name';
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->setDescription($orgName);

        $data = [
            'userType' => Entity::USER_TYPE_PARTNER,
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'Y',
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => $contactDetails,
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_LOCAL_AUTHORITY,
            [
                'loginId' => 'currentLoginId',
                'translateToWelsh' => 'N',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertEquals(null, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_PARTNER, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals($data['partnerContactDetails'], $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testCreateLocalAuthority()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_LOCAL_AUTHORITY_USER);

        $orgName = 'Org Name';
        $localAuthority = m::mock(LocalAuthorityEntity::class)->makePartial();
        $localAuthority->setDescription($orgName);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'N',
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => $localAuthority,
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        $entity = Entity::create('pid', Entity::USER_TYPE_LOCAL_AUTHORITY, $data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertInstanceOf(\DateTime::class, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_LOCAL_AUTHORITY, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals($data['localAuthority'], $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdateLocalAuthority()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_LOCAL_AUTHORITY_USER);

        $orgName = 'Org Name';
        $localAuthority = m::mock(LocalAuthorityEntity::class)->makePartial();
        $localAuthority->setDescription($orgName);

        $data = [
            'userType' => Entity::USER_TYPE_LOCAL_AUTHORITY,
            'loginId' => 'loginId',
            'roles' => [$role],
            'translateToWelsh' => 'Y',
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => $localAuthority,
            'organisations' => [
                m::mock(OrganisationEntity::class)
            ],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_PARTNER,
            [
                'loginId' => 'currentLoginId',
                'translateToWelsh' => 'N',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertEquals(null, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_LOCAL_AUTHORITY, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals($data['localAuthority'], $entity->getLocalAuthority());
        $this->assertEquals(0, $entity->getOrganisationUsers()->count());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testCreateOperator()
    {
        $adminRole = m::mock(RoleEntity::class)->makePartial();
        $adminRole->setRole(RoleEntity::ROLE_OPERATOR_ADMIN);

        $orgName = 'Org Name';
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setName($orgName);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$adminRole],
            'translateToWelsh' => 'N',
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [$org],
        ];

        $entity = Entity::create('pid', Entity::USER_TYPE_OPERATOR, $data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertInstanceOf(\DateTime::class, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_OPERATOR, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(1, $entity->getOrganisationUsers()->count());
        $this->assertEquals('Y', $entity->getOrganisationUsers()->first()->getIsAdministrator());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdateOperator()
    {
        $nonAdminRole = m::mock(RoleEntity::class)->makePartial();
        $nonAdminRole->setRole(RoleEntity::ROLE_OPERATOR_USER);

        $orgName = 'Org Name';
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setName($orgName);

        $data = [
            'userType' => Entity::USER_TYPE_OPERATOR,
            'loginId' => 'loginId',
            'roles' => [$nonAdminRole],
            'translateToWelsh' => 'Y',
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [$org],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_PARTNER,
            [
                'loginId' => 'currentLoginId',
                'translateToWelsh' => 'N',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());
        $this->assertEquals($data['translateToWelsh'], $entity->getTranslateToWelsh());
        $this->assertEquals($data['accountDisabled'], $entity->getAccountDisabled());
        $this->assertEquals(null, $entity->getDisabledDate());

        $this->assertEquals(Entity::USER_TYPE_OPERATOR, $entity->getUserType());
        $this->assertEquals(null, $entity->getTeam());
        $this->assertEquals(null, $entity->getTransportManager());
        $this->assertEquals(null, $entity->getPartnerContactDetails());
        $this->assertEquals(null, $entity->getLocalAuthority());
        $this->assertEquals(1, $entity->getOrganisationUsers()->count());
        $this->assertEquals('N', $entity->getOrganisationUsers()->first()->getIsAdministrator());
        $this->assertEquals($orgName, $entity->getRelatedOrganisationName());
        $this->assertEquals(false, $entity->isAnonymous());
    }

    public function testUpdateOperatorIsAdministratorOnly()
    {
        $adminRole = m::mock(RoleEntity::class)->makePartial();
        $adminRole->setRole(RoleEntity::ROLE_OPERATOR_ADMIN);

        $nonAdminRole = m::mock(RoleEntity::class)->makePartial();
        $nonAdminRole->setRole(RoleEntity::ROLE_OPERATOR_USER);

        $data = [
            'userType' => Entity::USER_TYPE_OPERATOR,
            'loginId' => 'loginId',
            'roles' => [$adminRole],
        ];

        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_OPERATOR,
            [
                'loginId' => 'currentLoginId',
                'roles' => [$nonAdminRole],
                'organisations' => [
                    m::mock(OrganisationEntity::class)->makePartial()
                ],
            ]
        );

        // update the entity
        $entity->update($data);

        $this->assertEquals($data['loginId'], $entity->getLoginId());
        $this->assertEquals($data['roles'], $entity->getRoles()->toArray());

        $this->assertEquals(Entity::USER_TYPE_OPERATOR, $entity->getUserType());
        $this->assertEquals(1, $entity->getOrganisationUsers()->count());
        $this->assertEquals('Y', $entity->getOrganisationUsers()->first()->getIsAdministrator());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testCreateThrowsInvalidRoleException()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_INTERNAL_ADMIN);

        $data = [
            'loginId' => 'loginId',
            'roles' => [$role],
            'accountDisabled' => 'Y',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)->makePartial()
            ],
        ];

        Entity::create('pid', Entity::USER_TYPE_OPERATOR, $data);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testUpdateThrowsInvalidRoleException()
    {
        $role = m::mock(RoleEntity::class)->makePartial();
        $role->setRole(RoleEntity::ROLE_INTERNAL_ADMIN);

        $data = [
            'userType' => Entity::USER_TYPE_OPERATOR,
            'loginId' => 'loginId',
            'roles' => [$role],
            'accountDisabled' => 'N',
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)->makePartial()
            ],
        ];

        // create an object of different type first
        $entity = Entity::create(
            'pid',
            Entity::USER_TYPE_PARTNER,
            [
                'loginId' => 'currentLoginId',
                'accountDisabled' => 'Y',
                'team' => m::mock(TeamEntity::class),
                'transportManager' => m::mock(TransportManagerEntity::class),
                'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
                'localAuthority' => m::mock(LocalAuthorityEntity::class),
                'organisations' => [
                    m::mock(OrganisationEntity::class)
                ],
            ]
        );

        // update the entity
        $entity->update($data);
    }

    /**
     * @dataProvider getPermissionProvider
     */
    public function testGetPermission($userType, $roleIds, $expected)
    {
        $roles = array_map(
            function ($id) {
                $role = m::mock(RoleEntity::class)->makePartial();
                $role->setRole($id);

                return $role;
            },
            $roleIds
        );

        $data = [
            'loginId' => 'loginId',
            'roles' => $roles,
            'team' => m::mock(TeamEntity::class),
            'transportManager' => m::mock(TransportManagerEntity::class),
            'partnerContactDetails' => m::mock(ContactDetailsEntity::class),
            'localAuthority' => m::mock(LocalAuthorityEntity::class),
            'organisations' => [
                m::mock(OrganisationEntity::class)->makePartial()
            ],
        ];

        $entity = Entity::create('pid', $userType, $data);

        $this->assertEquals($expected, $entity->getPermission());
    }

    public function getPermissionProvider()
    {
        return [
            // local authority - admin
            [
                Entity::USER_TYPE_LOCAL_AUTHORITY,
                Entity::getRolesByUserType(Entity::USER_TYPE_LOCAL_AUTHORITY, Entity::PERMISSION_ADMIN),
                Entity::PERMISSION_ADMIN
            ],
            // local authority - user
            [
                Entity::USER_TYPE_LOCAL_AUTHORITY,
                Entity::getRolesByUserType(Entity::USER_TYPE_LOCAL_AUTHORITY, Entity::PERMISSION_USER),
                Entity::PERMISSION_USER
            ],
            // operator - admin
            [
                Entity::USER_TYPE_OPERATOR,
                Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_ADMIN),
                Entity::PERMISSION_ADMIN
            ],
            // operator - user
            [
                Entity::USER_TYPE_OPERATOR,
                Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_USER),
                Entity::PERMISSION_USER
            ],
            // operator - tm
            [
                Entity::USER_TYPE_OPERATOR,
                Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_TM),
                Entity::PERMISSION_TM
            ],
            // operator - admin with tm role
            [
                Entity::USER_TYPE_OPERATOR,
                array_merge(
                    Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_ADMIN),
                    Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_TM)
                ),
                Entity::PERMISSION_ADMIN
            ],
            // operator - user with tm role
            [
                Entity::USER_TYPE_OPERATOR,
                array_merge(
                    Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_USER),
                    Entity::getRolesByUserType(Entity::USER_TYPE_OPERATOR, Entity::PERMISSION_TM)
                ),
                Entity::PERMISSION_USER
            ],
            // partner - admin
            [
                Entity::USER_TYPE_PARTNER,
                Entity::getRolesByUserType(Entity::USER_TYPE_PARTNER, Entity::PERMISSION_ADMIN),
                Entity::PERMISSION_ADMIN
            ],
            // partner - user
            [
                Entity::USER_TYPE_PARTNER,
                Entity::getRolesByUserType(Entity::USER_TYPE_PARTNER, Entity::PERMISSION_USER),
                Entity::PERMISSION_USER
            ],
            // internal - user
            [
                Entity::USER_TYPE_INTERNAL,
                Entity::getRolesByUserType(Entity::USER_TYPE_INTERNAL, Entity::PERMISSION_USER),
                null
            ],
        ];
    }

    public function testAnon()
    {
        $user = Entity::anon();

        $role = $user->getRoles()->current();

        $this->assertEquals(1, $user->getRoles()->count());
        $this->assertInstanceOf(RoleEntity::class, $role);
        $this->assertEquals(RoleEntity::ROLE_ANON, $role->getId());
        $this->assertEquals('anon', $user->getLoginId());
        $this->assertEquals(true, $user->isAnonymous());
    }

    public function testIsSystemUserYes()
    {
        $user = new Entity('PID', 'type');
        $user->setId(\Dvsa\Olcs\Api\Rbac\PidIdentityProvider::SYSTEM_USER);

        $this->assertTrue($user->isSystemUser());
    }

    public function testIsSystemUserNo()
    {
        $user = new Entity('PID', 'type');
        $user->setId(123);

        $this->assertFalse($user->isSystemUser());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testAnonUsernameReserved()
    {
        Entity::create('123456', Entity::USER_TYPE_INTERNAL, ['loginId' => 'anon']);
    }

    public function testHasActivePsvLicence()
    {
        $user = new Entity('pid', Entity::USER_TYPE_OPERATOR);
        $this->assertEquals(false, $user->hasActivePsvLicence());

        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->shouldReceive('hasActiveLicences')->with(LicenceEntity::LICENCE_CATEGORY_PSV)->andReturn(true);

        $orgUser = new OrganisationUserEntity();
        $orgUser->setUser($user);
        $orgUser->setOrganisation($org);

        $user->addOrganisationUsers($orgUser);
        $this->assertEquals(true, $user->hasActivePsvLicence());
    }

    public function testGetNumberOfVehicles()
    {
        $user = new Entity('pid', Entity::USER_TYPE_OPERATOR);

        $mockLicence = m::mock(LicenceEntity::class)->makePartial();
        $mockLicence->setStatus(LicenceEntity::LICENCE_STATUS_VALID);
        $mockLicence->setTotAuthVehicles(2);

        $activeLicences = new ArrayCollection();
        $activeLicences->add($mockLicence);

        $mockApplication = m::mock(ApplicationEntity::class)->makePartial();
        $mockApplication->setStatus(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION);
        $mockApplication->setTotAuthVehicles(1);

        $outstandingApplications = new ArrayCollection();
        $outstandingApplications->add($mockApplication);

        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->shouldReceive('getActiveLicences')->andReturn($activeLicences);
        $org->shouldReceive('getOutstandingApplications')->andReturn($outstandingApplications);

        $orgUser = new OrganisationUserEntity();
        $orgUser->setUser($user);
        $orgUser->setOrganisation($org);

        $user->addOrganisationUsers($orgUser);
        $this->assertEquals(3, $user->getNumberOfVehicles());
    }
}
