<?php


namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\Licence;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;

/**
 * Class LicenceTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator
 */
class LicenceTest extends MockeryTestCase
{
    public function testIsValidNoLicence()
    {
        $sut = new Licence();

        $licNo = 'PD5783498';
        $value = ['licNo' => $licNo];

        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getLicenceByLicNo')->with($licNo)->once()->andReturn(new ArrayCollection());

        $context['organisation'] = $organisation;

        $this->assertEquals(false, $sut->isValid($value, $context));
    }

    /**
     * Returns whether a licence is allowed to receive ebsr submissions based on the status
     *
     * @dataProvider isValidProvider
     *
     * @param $status
     * @param $goodsOrPsv
     * @param $valid
     */
    public function testIsValid($status, $goodsOrPsv, $valid)
    {
        $sut = new Licence();

        $licNo = 'PD5783498';
        $value = ['licNo' => $licNo];

        $licence = new LicenceEntity(new OrganisationEntity(), new RefData($status));
        $licence->setGoodsOrPsv(new RefData($goodsOrPsv));

        $matchedLicence = new ArrayCollection();
        $matchedLicence->add($licence);

        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getLicenceByLicNo')->with($licNo)->once()->andReturn($matchedLicence);

        $context['organisation'] = $organisation;

        $this->assertEquals($valid, $sut->isValid($value, $context));
    }

    /**
     * Provides an array of licence statuses and whether the licence is goods or psv, alongside whether these are valid
     *
     * @return array
     */
    public function isValidProvider()
    {
        return [
            [LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_SUSPENDED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_VALID, LicenceEntity::LICENCE_CATEGORY_PSV, true],
            [LicenceEntity::LICENCE_STATUS_CURTAILED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_GRANTED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_SURRENDERED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_WITHDRAWN, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_REFUSED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_REVOKED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_NOT_TAKEN_UP, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_TERMINATED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_UNLICENSED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_CANCELLED, LicenceEntity::LICENCE_CATEGORY_PSV, false],
            [LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_SUSPENDED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_VALID, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_CURTAILED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_GRANTED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_SURRENDERED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_WITHDRAWN, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_REFUSED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_REVOKED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_NOT_TAKEN_UP, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_TERMINATED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [
                LicenceEntity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                false
            ],
            [LicenceEntity::LICENCE_STATUS_UNLICENSED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
            [LicenceEntity::LICENCE_STATUS_CANCELLED, LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, false],
        ];
    }
}
