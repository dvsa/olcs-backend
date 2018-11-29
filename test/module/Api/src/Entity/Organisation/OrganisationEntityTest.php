<?php

namespace Dvsa\OlcsTest\Api\Entity\Organisation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Organisation Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class OrganisationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testHasInforceLicences()
    {
        /** @var Entity $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('inForceDate', $expr->getField());
                    $this->assertEquals('<>', $expr->getOperator());
                    $this->assertEquals(null, $expr->getValue()->getValue());

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['foo']);

                    return $collection;
                }
            );

        $this->assertTrue($organisation->hasInforceLicences());
    }

    /**
     * @dataProvider organisationDataProvider
     */
    public function testUpdateOrganisation($isIrfo, $lastName, $expectedName, $allowEmail)
    {
        $organisation = new Entity();

        $organisation->updateOrganisation(
            'name',
            '12345678',
            'fname',
            $lastName,
            $isIrfo,
            'nob',
            ['cpid'],
            $allowEmail
        );

        $this->assertEquals($organisation->getCpid(), ['cpid']);
        $this->assertEquals($organisation->getName(), $expectedName);
        $this->assertEquals($organisation->getCompanyOrLlpNo(), '12345678');
        $this->assertEquals($organisation->getIsIrfo(), $isIrfo);
        $this->assertEquals($organisation->getNatureOfBusiness(), 'nob');
        $this->assertEquals($organisation->getAllowEmail(), $allowEmail);
    }

    public function organisationDataProvider()
    {
        return [
            ['Y', 'lname', 'fname lname', 'Y'],
            ['N', '', 'name','N']
        ];
    }

    public function testGetAdminOrganisationUsers()
    {
        $mockOrgUser1 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')
            ->once()
            ->andReturn(
                m::mock(UserEntity::class)
                    ->shouldReceive('getAccountDisabled')->andReturn('N')->once()
                    ->getMock()
            )
            ->getMock();

        $mockOrgUser2 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')
            ->once()
            ->andReturn(
                m::mock(UserEntity::class)
                    ->shouldReceive('getAccountDisabled')->andReturn('Y')->once()
                    ->getMock()
            )
            ->getMock();

        //  not user entity
        $mockOrgUser3 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')->once()->andReturn(m::mock())
            ->getMock();

        $collection = new ArrayCollection([$mockOrgUser1, $mockOrgUser2, $mockOrgUser3]);

        /** @var Entity | m\MockInterface $sut */
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getOrganisationUsers->matching')
            ->with(m::type(Criteria::class))
            ->andReturn($collection);

        $expected = new ArrayCollection();
        $expected->add($mockOrgUser1);

        $this->assertEquals($expected, $sut->getAdminOrganisationUsers());
    }

    public function testGetAdminOrganisationUsersWithException()
    {
        $mockOrgUser1 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')
            ->once()
            ->andReturn(
                m::mock(UserEntity::class)
                    ->shouldReceive('getAccountDisabled')->andThrow(EntityNotFoundException::class)->once()
                    ->getMock()
            )
            ->getMock();

        $mockOrgUser2 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')
            ->once()
            ->andReturn(
                m::mock(UserEntity::class)
                    ->shouldReceive('getAccountDisabled')->andReturn('Y')->once()
                    ->getMock()
            )
            ->getMock();

        //  not user entity
        $mockOrgUser3 = m::mock(OrganisationUser::class)
            ->shouldReceive('getUser')->once()->andReturn(m::mock())
            ->getMock();

        $collection = new ArrayCollection([$mockOrgUser1, $mockOrgUser2, $mockOrgUser3]);

        /** @var Entity | m\MockInterface $sut */
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getOrganisationUsers->matching')
            ->with(m::type(Criteria::class))
            ->andReturn($collection);

        $this->assertEquals(new ArrayCollection(), $sut->getAdminOrganisationUsers());
    }

    /**
     * @dataProvider dpTestHasAdminEmailAddresses
     */
    public function testHasAdminEmailAddresses(array $emailsMap, $expect)
    {
        /** @var Entity | m\MockInterface $sut */
        $sut = m::mock(Entity::class)->makePartial();

        $orgUsers = [];
        foreach ($emailsMap as $email) {
            /** @var OrganisationUser | m\MockInterface $mockOrgUser */
            $mockOrgUser = m::mock(OrganisationUser::class)->makePartial();
            $mockOrgUser->setIsAdministrator(true);
            $mockOrgUser->shouldReceive('getUser->getContactDetails->getEmailAddress')->andReturn($email);

            $orgUsers[] = $mockOrgUser;
        }

        $sut->shouldReceive('getAdminOrganisationUsers')->andReturn($orgUsers);

        static::assertEquals($expect, $sut->hasAdminEmailAddresses());
    }

    public function dpTestHasAdminEmailAddresses()
    {
        return [
            [
                'emailsMap' => [
                    null,
                    '',
                    'unit_broken_emailaddress',
                ],
                'expect' => false,
            ],
            [
                'emailsMap' => [
                    null,
                    'aaa.bbb@domain.com',
                ],
                'expect' => true,
            ],
        ];
    }

    public function dpOrgTypes()
    {
        return [
            [Entity::ORG_TYPE_SOLE_TRADER],
            [Entity::ORG_TYPE_IRFO],
            [Entity::ORG_TYPE_LLP],
            [Entity::ORG_TYPE_OTHER],
            [Entity::ORG_TYPE_PARTNERSHIP],
            [Entity::ORG_TYPE_REGISTERED_COMPANY],
        ];
    }

    /**
     * @dataProvider dpOrgTypes
     *
     * @param string $typeId
     */
    public function testIsSoleTrader($typeId)
    {
        $type = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $type->setId($typeId);
        $organisation = new Entity();
        $organisation->setType($type);

        $this->assertSame($typeId === Entity::ORG_TYPE_SOLE_TRADER, $organisation->isSoleTrader());
    }

    /**
     * @dataProvider dpOrgTypes
     *
     * @param string $typeId
     */
    public function testIsPartnership($typeId)
    {
        $type = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $type->setId($typeId);
        $organisation = new Entity();
        $organisation->setType($type);

        $this->assertSame($typeId === Entity::ORG_TYPE_PARTNERSHIP, $organisation->isPartnership());
    }

    public function testIsUnlicensed()
    {
        /** @var Entity $organisation */
        $organisation = $this->instantiate($this->entityClass);

        $this->assertFalse($organisation->isUnlicensed());

        $organisation->setIsUnlicensed(true);

        $this->assertTrue($organisation->isUnlicensed());
    }

    /**
     * Tests fetching a licence from the organisation using the licNo
     */
    public function testGetLicenceByLicNo()
    {
        $licNo = 'PD8538936';

        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {
                    $licNo = 'PD8538936';

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('licNo', $expr->getField());
                    $this->assertEquals('=', $expr->getOperator());
                    $this->assertEquals($licNo, $expr->getValue()->getValue());

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['licence']);

                    return $collection;
                }
            );

        $this->assertEquals(
            ['licence'],
            $organisation->getLicenceByLicNo($licNo)->toArray()
        );
    }

    public function testGetActiveLicences()
    {
        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('status', $expr->getField());
                    $this->assertEquals('IN', $expr->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_STATUS_VALID,
                            LicenceEntity::LICENCE_STATUS_SUSPENDED,
                            LicenceEntity::LICENCE_STATUS_CURTAILED,
                        ],
                        $expr->getValue()->getValue()
                    );

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['active licences']);

                    return $collection;
                }
            );

        $this->assertEquals(
            ['active licences'],
            $organisation->getActiveLicences()->toArray()
        );
    }

    public function testHasActiveLicences()
    {
        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression()->getExpressionList();

                    $this->assertEquals('status', $expr[0]->getField());
                    $this->assertEquals('IN', $expr[0]->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_STATUS_VALID,
                            LicenceEntity::LICENCE_STATUS_SUSPENDED,
                            LicenceEntity::LICENCE_STATUS_CURTAILED,
                        ],
                        $expr[0]->getValue()->getValue()
                    );

                    $this->assertEquals('goodsOrPsv', $expr[1]->getField());
                    $this->assertEquals('IN', $expr[1]->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_CATEGORY_PSV,
                        ],
                        $expr[1]->getValue()->getValue()
                    );

                    $collection = m::mock();
                    $collection->shouldReceive('isEmpty')
                        ->andReturn(false);

                    return $collection;
                }
            );

        $this->assertEquals(true, $organisation->hasActiveLicences(LicenceEntity::LICENCE_CATEGORY_PSV));
    }

    public function testGetRelatedLicences()
    {
        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('status', $expr->getField());
                    $this->assertEquals('IN', $expr->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED,
                            LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
                            LicenceEntity::LICENCE_STATUS_GRANTED,
                            LicenceEntity::LICENCE_STATUS_VALID,
                            LicenceEntity::LICENCE_STATUS_SUSPENDED,
                            LicenceEntity::LICENCE_STATUS_CURTAILED,
                        ],
                        $expr->getValue()->getValue()
                    );

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['related licences']);

                    return $collection;
                }
            );

        $this->assertEquals(
            ['related licences'],
            $organisation->getRelatedLicences()->toArray()
        );
    }

    public function testGetDisqualificationNull()
    {
        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(null, $organisation->getDisqualification());
    }

    public function testGetDisqualification()
    {
        $disqualification = new Disqualification(m::mock(Entity::class));

        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame($disqualification, $organisation->getDisqualification());
    }

    public function testGetDisqualificationStatusNone()
    {
        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(Disqualification::STATUS_NONE, $organisation->getDisqualificationStatus());
    }

    public function testGetDisqualificationStatusActive()
    {
        $disqualification = new Disqualification(m::mock(Entity::class));
        $disqualification->setIsDisqualified('Y');
        $disqualification->setStartDate('2015-01-01');

        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame(Disqualification::STATUS_ACTIVE, $organisation->getDisqualificationStatus());
    }

    public function testGetLinkedLicences()
    {
        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();
                    $this->assertEquals('status', $expr->getField());
                    $this->assertEquals('NIN', $expr->getOperator());
                    $this->assertTrue(is_array($expr->getValue()->getValue()));

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['foo']);

                    return $collection;
                }
            );

        $this->assertEquals(['foo'], $organisation->getLinkedLicences()->toArray());
    }

    public function testGetContextValue()
    {
        $entity = new Entity();
        $entity->setId(111);

        $this->assertEquals(111, $entity->getContextValue());
    }

    /**
     * Tests we're retreiving admin email addresses correctly
     */
    public function testGetAdminEmailAddresses()
    {
        $entity = new Entity();

        $email1 = 'foo@bar.com';
        $email2 = 'bar@foo.com';

        $expectedEmails = [
            0 => $email1,
            1 => $email2
        ];

        $user1 = new OrganisationUser();
        $user1->setIsAdministrator('N');

        $user2 = m::mock(OrganisationUser::class)->makePartial();
        $user2->setIsAdministrator('Y');

        $user2->shouldReceive('getUser->getContactDetails->getEmailAddress')->once()->andReturn($email1);

        $user3 = m::mock(OrganisationUser::class)->makePartial();
        $user3->setIsAdministrator('Y');
        $user3->shouldReceive('getUser->getContactDetails->getEmailAddress')->once()->andReturn($email2);

        $entity->setOrganisationUsers(new ArrayCollection([$user1, $user2, $user3]));

        $this->assertEquals($expectedEmails, $entity->getAdminEmailAddresses());
    }

    /**
     *  test if org user not found due to soft delete
     *
     */
    public function testGetAdminEmailAddressesWhenOrganisationUserNotFound()
    {
        $entity = new Entity();

        $email1 = 'bar@foo.com';

        $expectedEmails = [
            0 => $email1
        ];
        $user1 = m::mock(OrganisationUser::class)->makePartial();
        $user1->setIsAdministrator('Y');
        $user1->shouldReceive('getUser')->once()->andThrow(EntityNotFoundException::class);
        $user2 = m::mock(OrganisationUser::class)->makePartial();
        $user2->setIsAdministrator('Y');
        $user2->shouldReceive('getUser->getContactDetails->getEmailAddress')->once()->andReturn($email1);

        $entity->setOrganisationUsers(new ArrayCollection([$user1, $user2]));

        $this->assertEquals($expectedEmails, $entity->getAdminEmailAddresses());
    }

    /**
     * Test get allowed operator location from applications
     *
     * @param int $niFlag
     * @param string $allowedOperatorLocation
     * @dataProvider allowedOperatorLocationProviderApplications
     */
    public function testGetAllowedOperatorLocationFromApplications($niFlag, $allowedOperatorLocation)
    {
        $mockOutstandingApplications = new ArrayCollection();
        $mockOutstandingApplications->add(
            m::mock()
            ->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
            ->twice()
            ->getMock()
        );
        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getOutstandingApplications')
            ->with(true)
            ->andReturn($mockOutstandingApplications)
            ->once()
            ->getMock();

        $this->assertEquals($allowedOperatorLocation, $organisation->getAllowedOperatorLocation());
    }

    /**
     * Allowed operator location provider (applications)
     */
    public function allowedOperatorLocationProviderApplications()
    {
        return [
            ['N', 'GB'],
            ['Y', 'NI'],
        ];
    }

    /**
     * Test get allowed operator location from licences
     *
     * @param string $trafficArea
     * @param string $allowedOperatorLocation
     * @dataProvider allowedOperatorLocationProviderLicences
     */
    public function testGetAllowedOperatorLocationFromLicences($trafficArea, $allowedOperatorLocation, $licenceStatus)
    {
        $mockTrafficArea = m::mock()
            ->shouldReceive('getId')
            ->andReturn($trafficArea)
            ->getMock();

        $mockStatus = m::mock()
            ->shouldReceive('getId')
            ->andReturn($licenceStatus)
            ->getMock();

        $mockOutstandingApplications = new ArrayCollection();
        $mockLicences = new ArrayCollection();
        $mockLicences->add(
            m::mock()
            ->shouldReceive('getTrafficArea')
            ->andReturn($mockTrafficArea)
            ->shouldReceive('getStatus')
            ->andReturn($mockStatus)
            ->getMock()
        );

        /** @var Entity | m\MockInterface  $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getOutstandingApplications')
            ->with(true)
            ->andReturn($mockOutstandingApplications)
            ->once()
            ->shouldReceive('getLicences')
            ->andReturn($mockLicences)
            ->once()
            ->getMock();

        $this->assertEquals($allowedOperatorLocation, $organisation->getAllowedOperatorLocation());
    }

    /**
     * Allowed operator location provider (licences)
     */
    public function allowedOperatorLocationProviderLicences()
    {
        return [
            ['N', 'NI', LicenceEntity::LICENCE_STATUS_GRANTED],
            ['B', 'GB', LicenceEntity::LICENCE_STATUS_GRANTED],
            ['B', null, LicenceEntity::LICENCE_STATUS_WITHDRAWN],
        ];
    }

    /**
     * Test get allowed operator location with no licences and no outstanding applications
     */
    public function testGetAllowedOperatorLocationDefault()
    {
        $mockOutstandingApplications = new ArrayCollection();
        $mockLicences = new ArrayCollection();

        /** @var Entity | m\MockInterface  $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getOutstandingApplications')
            ->with(true)
            ->andReturn($mockOutstandingApplications)
            ->once()
            ->shouldReceive('getLicences')
            ->andReturn($mockLicences)
            ->once()
            ->getMock();

        $this->assertNull($organisation->getAllowedOperatorLocation());
    }

    public function testIsMlh()
    {
        $mockValidLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getStatus')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID)
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn(LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->getMock();
        $mockLicences = new ArrayCollection();
        $mockLicences->add($mockValidLicence);

        $mockNewApplication = m::mock(ApplicationEntity::class)
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->once()
            ->getMock();
        $mockNewApplications = new ArrayCollection();
        $mockNewApplications->add($mockNewApplication);

        $mockOutstandingLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getStatus')
            ->andReturn(LicenceEntity::LICENCE_STATUS_GRANTED)
            ->shouldReceive('getApplications')
            ->andReturn($mockNewApplications)
            ->once()
            ->getMock();
        $mockLicences->add($mockOutstandingLicence);

        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences')
            ->andReturn($mockLicences)
            ->twice()
            ->getMock();

        $this->assertTrue($organisation->isMlh());
    }

    public function testNonMlh()
    {
        $mockValidLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getStatus')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID)
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn(LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->getMock();
        $mockLicences = new ArrayCollection();
        $mockLicences->add($mockValidLicence);

        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences')
            ->andReturn($mockLicences)
            ->twice()
            ->getMock();

        $this->assertFalse($organisation->isMlh());
    }

    public function testHasUnlicencedLicences()
    {
        $mockLic1 = m::mock(LicenceEntity::class)
            ->shouldReceive('getLicNo')
            ->andReturn('FOO')
            ->getMock();

        $mockLic2 = m::mock(LicenceEntity::class)
            ->shouldReceive('getLicNo')
            ->andReturn('UA123')
            ->getMock();

        $licences = new ArrayCollection();
        $licences->add($mockLic1);
        $licences->add($mockLic2);

        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences')
            ->andReturn($licences)
            ->once()
            ->getMock();

        $this->assertTrue($organisation->hasUnlicencedLicences());
    }

    /**
     * Test whether the organisation is eligible for permits.
     * Use 3 mock licences
     * The first one will not meet the criteria and be skipped
     * The second one will meet the criteria
     * The third licence won't need to be checked at all
     */
    public function testIsEligibleForPermitsWhenTrue()
    {
        $mockLicence1 = m::mock(LicenceEntity::class);
        $mockLicence1->shouldReceive('isEligibleForPermits')->once()->andReturn(false);
        $mockLicence2 = m::mock(LicenceEntity::class);
        $mockLicence2->shouldReceive('isEligibleForPermits')->once()->andReturn(true);
        $mockLicence3 = m::mock(LicenceEntity::class);
        $mockLicence3->shouldReceive('isEligibleForPermits')->never();

        $licences = new ArrayCollection([$mockLicence1, $mockLicence2, $mockLicence3]);

        $entity = $this->instantiate(Entity::class);
        $entity->setLicences($licences);
        $this->assertEquals(true, $entity->isEligibleForPermits());
    }

    /**
     * Test whether the organisation is eligible for permits.
     * Use 3 mock licences, all 3 won't meet the criteria hence we return false
     */
    public function testIsEligibleForPermitsWhenFalse()
    {
        $mockLicence1 = m::mock(LicenceEntity::class);
        $mockLicence1->shouldReceive('isEligibleForPermits')->once()->andReturn(false);
        $mockLicence2 = m::mock(LicenceEntity::class);
        $mockLicence2->shouldReceive('isEligibleForPermits')->once()->andReturn(false);
        $mockLicence3 = m::mock(LicenceEntity::class);
        $mockLicence3->shouldReceive('isEligibleForPermits')->once()->andReturn(false);

        $licences = new ArrayCollection([$mockLicence1, $mockLicence2, $mockLicence3]);

        $entity = $this->instantiate(Entity::class);
        $entity->setLicences($licences);
        $this->assertEquals(false, $entity->isEligibleForPermits());
    }

    /**
     * @todo have marked this test as skipped, we need to fix it first, then make sure it asserts something meaningful
     */
    public function testGetStandardInternationalLicences()
    {
        $this->markTestSkipped();

        /** @var Entity | m\MockInterface $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('status', $expr->getExpressionList()[0]->getExpressionList()[0]->getField());
                    $this->assertEquals('IN', $expr->getExpressionList()[0]->getExpressionList()[0]->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_STATUS_VALID,
                            LicenceEntity::LICENCE_STATUS_SUSPENDED,
                            LicenceEntity::LICENCE_STATUS_CURTAILED,
                        ],
                        $expr->getExpressionList()[0]->getExpressionList()[0]->getValue()->getValue()
                    );

                    $this->assertEquals('goodsOrPsv', $expr->getExpressionList()[0]->getExpressionList()[1]->getField());
                    $this->assertEquals('IN', $expr->getExpressionList()[0]->getExpressionList()[1]->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE
                        ],
                        $expr->getExpressionList()[0]->getExpressionList()[1]->getValue()->getValue()
                    );

                    $this->assertEquals('licenceType', $expr->getExpressionList()[1]->getField());
                    $this->assertEquals('IN', $expr->getExpressionList()[1]->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                            LicenceEntity::LICENCE_TYPE_RESTRICTED
                        ],
                        $expr->getExpressionList()[1]->getValue()->getValue()
                    );

                    $collection = m::mock();
                    $licencesArr = array();
                    $collection->shouldReceive('toArray')
                        ->andReturn($licencesArr);

                    return $collection;
                }
            );

        $this->assertInternalType('array', $organisation->getStandardInternationalLicences());
    }
}
