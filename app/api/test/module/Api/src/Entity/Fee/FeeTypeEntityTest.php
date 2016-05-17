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

    protected $sut;

    public function setUp()
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

    public function testGetShowQuantity()
    {
        $feeType = new RefData();
        $feeType->setId(Entity::FEE_TYPE_IRFOPSVANN);
        $this->sut->setFeeType($feeType);
        $this->assertTrue($this->sut->getShowQuantity());

        $feeType->setId(Entity::FEE_TYPE_IRFOPSVAPP);
        $this->sut->setFeeType($feeType);
        $this->assertFalse($this->sut->getShowQuantity());
    }
}
