<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * FeeType Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class FeeTypeEntityTest extends EntityTester
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

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->instantiate($this->entityClass);
    }

    public function testIsMiscellaneous()
    {
        $this->assertFalse($this->sut->isMiscellaneous());

        $this->sut->setIsMiscellaneous(true);

        $this->assertTrue($this->sut->isMiscellaneous());
    }

    /**
     * @param string $fixedValue
     * @param string $fiveYearValue
     * @param array $expected
     * @dataProvider bundleDataProvider
     */
    public function testGetCalculatedBundleValues($fixedValue, $fiveYearValue, $expected)
    {
        $this->sut->setFixedValue($fixedValue);
        $this->sut->setFiveYearValue($fiveYearValue);

        $this->assertEquals($expected, $this->sut->getCalculatedBundleValues());
    }

    public function bundleDataProvider()
    {
        return [
            [
                '10.00',
                null,
                [
                    'displayValue' => '10.00',
                ]
            ],
            [
                null,
                '50.00',
                [
                    'displayValue' => '50.00',
                ]
            ],
        ];
    }

    /**
     * @param mixed $isNi
     * @param boolean $expected
     *
     * @dataProvider countryCodeProvider
     */
    public function testGetCountryCode($isNi, $expected)
    {
        $this->sut->setIsNi($isNi);

        $this->assertSame($expected, $this->sut->getCountryCode());
    }

    /**
     * @return array
     */
    public function countryCodeProvider()
    {
        return [
            ['Y', 'NI'],
            ['N', 'GB'],
            [null, 'GB'],
        ];
    }

    public function testIsShowQuantity()
    {
        $feeType = new RefData();
        $feeType->setId(Entity::FEE_TYPE_IRFOPSVANN);
        $this->sut->setFeeType($feeType);
        $this->assertTrue($this->sut->isShowQuantity());

        $feeType->setId(Entity::FEE_TYPE_IRFOPSVAPP);
        $this->sut->setFeeType($feeType);
        $this->assertFalse($this->sut->isShowQuantity());
    }

    /**
     * @dataProvider dpIsEcmtApplication
     */
    public function testIsEcmtApplication(string $feeType, bool $expectedResult)
    {
        $feeTypeRefData = new RefData($feeType);
        $this->sut->setFeeType($feeTypeRefData);
        $this->assertEquals($expectedResult, $this->sut->isEcmtApplication());
    }

    public function dpIsEcmtApplication(): array
    {
        return $this->listTypes([Entity::FEE_TYPE_ECMT_APP]);
    }

    /**
     * @dataProvider dpIsEcmtIssue
     */
    public function testIsEcmtIssue(string $feeType, bool $expectedResult)
    {
        $feeTypeRefData = new RefData($feeType);
        $this->sut->setFeeType($feeTypeRefData);
        $this->assertEquals($expectedResult, $this->sut->isEcmtIssue());
    }

    public function dpIsEcmtIssue(): array
    {
        return $this->listTypes([Entity::FEE_TYPE_ECMT_ISSUE]);
    }

    /**
     * @dataProvider dpIsIrhpApplicationIssue
     */
    public function testIsIrhpApplicationIssue(string $feeType, bool $expectedResult)
    {
        $feeTypeRefData = new RefData($feeType);
        $this->sut->setFeeType($feeTypeRefData);
        $this->assertEquals($expectedResult, $this->sut->isIrhpApplicationIssue());
    }

    public function dpIsIrhpApplicationIssue(): array
    {
        return $this->listTypes([Entity::FEE_TYPE_IRHP_ISSUE]);
    }

    /**
     * @dataProvider dpIsIrhpApplication
     */
    public function testIsIrhpApplication(string $feeType, bool $expectedResult)
    {
        $feeTypeRefData = new RefData($feeType);
        $this->sut->setFeeType($feeTypeRefData);
        $this->assertEquals($expectedResult, $this->sut->isIrhpApplication());
    }

    public function dpIsIrhpApplication(): array
    {
        return $this->listTypes([Entity::FEE_TYPE_IRHP_ISSUE, Entity::FEE_TYPE_IRHP_APP, Entity::FEE_TYPE_IRFOGVPERMIT]);
    }

    /**
     * Build a list of valid fee types, that can be fed into a method and return a true/false
     * Avoids duplication in data providers
     *
     * @param array $matchingTypes array of the types that will return true
     *
     * @return array
     */
    private function listTypes(array $matchingTypes): array
    {
        $returnTypes = [];

        $types = [
            Entity::FEE_TYPE_APP,
            Entity::FEE_TYPE_GRANT,
            Entity::FEE_TYPE_CONT,
            Entity::FEE_TYPE_VEH,
            Entity::FEE_TYPE_GRANTINT,
            Entity::FEE_TYPE_INTVEH,
            Entity::FEE_TYPE_DUP,
            Entity::FEE_TYPE_APP,
            Entity::FEE_TYPE_ANN,
            Entity::FEE_TYPE_GRANTVAR,
            Entity::FEE_TYPE_BUSAPP,
            Entity::FEE_TYPE_BUSVAR,
            Entity::FEE_TYPE_GVANNVEH,
            Entity::FEE_TYPE_INTUPGRADEVEH,
            Entity::FEE_TYPE_INTAMENDED,
            Entity::FEE_TYPE_IRFOPSVAPP,
            Entity::FEE_TYPE_IRFOPSVANN,
            Entity::FEE_TYPE_IRFOPSVCOPY,
            Entity::FEE_TYPE_IRFOGVPERMIT,
            Entity::FEE_TYPE_ADJUSTMENT,
            Entity::FEE_TYPE_ECMT_APP,
            Entity::FEE_TYPE_ECMT_ISSUE,
            Entity::FEE_TYPE_IRHP_ISSUE,
            Entity::FEE_TYPE_IRHP_APP,
        ];

        foreach ($types as $type) {
            $returnTypes[] = [
                $type,
                in_array($type, $matchingTypes)
            ];
        }

        return $returnTypes;
    }

    public function testUpdateNewFeeType()
    {
        $this->sut->setEffectiveFrom(new \DateTime('2014-10-26T00:00:00+00:00'));
        $this->sut->setAnnualValue(10);
        $this->sut->setFiveYearValue(10);
        $this->sut->setFixedValue(10);

        $expectedNew = $this->instantiate($this->entityClass);
        $expectedNew->setEffectiveFrom(new \DateTime('2020-10-10T00:00:00+00:00'));
        $expectedNew->setAnnualValue(99);
        $expectedNew->setFiveYearValue(99);
        $expectedNew->setFixedValue(99);

        $this->assertEquals(
            $expectedNew,
            $this->sut->updateNewFeeType(
                '2020-10-10T00:00:00+00:00',
                99,
                99,
                99,
                $this->sut
            )
        );
    }
}
