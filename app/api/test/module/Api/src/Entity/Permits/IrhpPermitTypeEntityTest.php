<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as Entity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath as ApplicationPathEntity;
use Mockery as m;
use RuntimeException;

/**
 * IrhpPermitType Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitTypeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock(Entity::class)->makePartial();
    }

    public function testGetCalculatedBundleValues()
    {
        $this->sut->shouldReceive('isEcmtAnnual')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('isEcmtShortTerm')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isEcmtRemoval')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isBilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isApplicationPathEnabled')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        $this->assertSame(
            [
                'isEcmtAnnual' => true,
                'isEcmtShortTerm' => false,
                'isEcmtRemoval' => false,
                'isBilateral' => false,
                'isMultilateral' => false,
                'isApplicationPathEnabled' => false,
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    /**
    * @dataProvider dpIsEcmtAnnual
    */
    public function testIsEcmtAnnual($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isEcmtAnnual());
    }

    public function dpIsEcmtAnnual()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, true],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsEcmtShortTerm
    */
    public function testIsEcmtShortTerm($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isEcmtShortTerm());
    }

    public function dpIsEcmtShortTerm()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, true],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsEcmtRemoval
    */
    public function testIsEcmtRemoval($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isEcmtRemoval());
    }

    public function dpIsEcmtRemoval()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, true],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsBilateral
    */
    public function testIsBilateral($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isBilateral());
    }

    public function dpIsBilateral()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, true],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsMultilateral
    */
    public function testIsMultilateral($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isMultilateral());
    }

    public function dpIsMultilateral()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, true],
        ];
    }

    /**
    * @dataProvider dpIsApplicationPathEnabled
    */
    public function testIsApplicationPathEnabled($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isApplicationPathEnabled());
    }

    public function dpIsApplicationPathEnabled()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, true],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, true],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpGetActiveApplicationPath
    */
    public function testGetActiveApplicationPath($applicationPaths, $dateToCheck, $expected)
    {
        $this->sut->setApplicationPaths($applicationPaths);

        $this->assertEquals($expected, $this->sut->getActiveApplicationPath($dateToCheck));
    }

    public function dpGetActiveApplicationPath()
    {
        $inPast = new DateTime('last year');
        $lastWeek = new DateTime('-1 week');
        $yesterday = new DateTime('yesterday');
        $inFuture = new DateTime('next year');

        $applicationPathInPast = new ApplicationPathEntity();
        $applicationPathInPast->setEffectiveFrom($inPast);

        $applicationPathYesterday = new ApplicationPathEntity();
        $applicationPathYesterday->setEffectiveFrom($yesterday);

        $applicationPathInFuture = new ApplicationPathEntity();
        $applicationPathInFuture->setEffectiveFrom($inFuture);

        return [
            'only one app path - in the past' => [
                'applicationPaths' => new ArrayCollection([$applicationPathInPast]),
                'dateToCheck' => null,
                'expected' => $applicationPathInPast,
            ],
            'two app paths - both in the past' => [
                'applicationPaths' => new ArrayCollection([$applicationPathInPast, $applicationPathYesterday]),
                'dateToCheck' => null,
                'expected' => $applicationPathYesterday,
            ],
            'two app paths - both in the past - check against yesterdays date' => [
                'applicationPaths' => new ArrayCollection([$applicationPathInPast, $applicationPathYesterday]),
                'dateToCheck' => $yesterday,
                'expected' => $applicationPathYesterday,
            ],
            'two app paths - both in the past - check against last weeks date' => [
                'applicationPaths' => new ArrayCollection([$applicationPathInPast, $applicationPathYesterday]),
                'dateToCheck' => $lastWeek,
                'expected' => $applicationPathInPast,
            ],
            'three app paths - two in the past and one in the future' => [
                'applicationPaths' => new ArrayCollection(
                    [$applicationPathInPast, $applicationPathYesterday, $applicationPathInFuture]
                ),
                'dateToCheck' => null,
                'expected' => $applicationPathYesterday,
            ],
            'only one app path - in the future' => [
                'applicationPaths' => new ArrayCollection([$applicationPathInFuture]),
                'dateToCheck' => null,
                'expected' => null,
            ],
        ];
    }

    /**
     * @dataProvider dpGetAllocationMode
     */
    public function testGetAllocationMode($irhpPermitTypeId, $allocationMode)
    {
        $this->sut->setId($irhpPermitTypeId);

        $this->assertEquals(
            $allocationMode,
            $this->sut->getAllocationMode()
        );
    }

    public function dpGetAllocationMode()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, Entity::ALLOCATION_MODE_STANDARD],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, Entity::ALLOCATION_MODE_STANDARD],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, Entity::ALLOCATION_MODE_EMISSIONS_CATEGORIES],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, Entity::ALLOCATION_MODE_STANDARD_WITH_EXPIRY]
        ];
    }

    /**
     * @dataProvider dpGetAllocationModeException
     */
    public function testGetAllocationModeException($irhpPermitTypeId)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No allocation mode set for permit type ' . $irhpPermitTypeId);

        $this->sut->setId($irhpPermitTypeId);
        $this->sut->getAllocationMode();
    }

    public function dpGetAllocationModeException()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT],
        ];
    }

    /**
     * @dataProvider dpGetExpiryInterval
     */
    public function testGetExpiryInterval($irhpPermitTypeId, $expiryInterval)
    {
        $this->sut->setId($irhpPermitTypeId);

        $this->assertEquals(
            $expiryInterval,
            $this->sut->getExpiryInterval()
        );
    }

    public function dpGetExpiryInterval()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, Entity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL_EXPIRY_INTERVAL]
        ];
    }

    /**
     * @dataProvider dpGetExpiryIntervalException
     */
    public function testGetExpiryIntervalException($irhpPermitTypeId)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No expiry interval defined for permit type ' . $irhpPermitTypeId);

        $this->sut->setId($irhpPermitTypeId);
        $this->sut->getExpiryInterval();
    }

    public function dpGetExpiryIntervalException()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT],
        ];
    }
}
