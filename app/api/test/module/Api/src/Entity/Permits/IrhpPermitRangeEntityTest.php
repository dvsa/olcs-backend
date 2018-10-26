<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Mockery as m;

/**
 * IrhpPermitRange Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitRangeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCreateUpdate()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $prefix = "UK";
        $fromNo = "1";
        $toNo = "150";
        $isReserve = 0;
        $isReplacement = 0;
        $countrys = [];

        $updatedPrefix = "AU";
        $updatedFromNo = "10";
        $updatedToNo = "1500";
        $updatedIsReserve = 1;
        $updatedCountrys = ['AU'];

        $entity = Entity::create($irhpPermitStock, $prefix, $fromNo, $toNo, $isReserve, $isReplacement, $countrys);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($prefix, $entity->getPrefix());
        $this->assertEquals($fromNo, $entity->getFromNo());
        $this->assertEquals($toNo, $entity->getToNo());
        $this->assertEquals($isReserve, $entity->getSsReserve());
        $this->assertEquals($isReplacement, $entity->getLostReplacement());
        $this->assertEquals($countrys, $entity->getCountrys());

        $entity->update($irhpPermitStock, $updatedPrefix, $updatedFromNo, $updatedToNo, $updatedIsReserve, $isReplacement, $updatedCountrys);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($updatedPrefix, $entity->getPrefix());
        $this->assertEquals($updatedFromNo, $entity->getFromNo());
        $this->assertEquals($updatedToNo, $entity->getToNo());
        $this->assertEquals($updatedIsReserve, $entity->getSsReserve());
        $this->assertEquals($isReplacement, $entity->getLostReplacement());
        $this->assertEquals($updatedCountrys, $entity->getCountrys());
    }

    /**
     * Test the canDelete method
     *
     * @dataProvider provider
     */
    public function testCanDelete($data, $expected)
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $prefix = "UK";
        $fromNo = "1";
        $toNo = "150";
        $isReserve = 0;
        $isReplacement = 0;

        $entity = Entity::create($irhpPermitStock, $prefix, $fromNo, $toNo, $isReserve, $isReplacement, $data['countrys']);
        $entity->setIrhpPermits($data['irhpPermits']);
        $entity->setIrhpCandidatePermits($data['irhpCandidatePermits']);
        $this->assertEquals($expected, $entity->canDelete($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'valid delete' => [
                [
                    'irhpCandidatePermits' => [],
                    'countrys' => [],
                    'irhpPermits' => []
                ],
                true,
            ],
            'existing candidate permits' => [
                [
                    'irhpCandidatePermits' => [m::mock(IrhpCandidatePermit::class)],
                    'countrys' => [],
                    'irhpPermits' => []
                ],
                false
            ],
            'existing countries' => [
                [
                    'irhpCandidatePermits' => [],
                    'countrys' => [m::mock(Country::class)],
                    'irhpPermits' => []
                ],
                false
            ],
            'existing irhp permits' => [
                [
                    'irhpCandidatePermits' => [],
                    'countrys' => [],
                    'irhpPermits' => [m::mock(IrhpPermit::class)]
                ],
                false
            ],
            'candidate permits and countries' => [
                [
                    'irhpCandidatePermits' => [m::mock(IrhpCandidatePermit::class)],
                    'countrys' => [m::mock(Country::class)],
                    'irhpPermits' => []
                ],
                false
            ],
            'candidate permits and irhp permits' => [
                [
                    'irhpCandidatePermits' => [m::mock(IrhpCandidatePermit::class)],
                    'countrys' => [],
                    'irhpPermits' => [m::mock(IrhpPermit::class)]
                ],
                false
            ],
            'countries and irhp permits' => [
                [
                    'irhpCandidatePermits' => [],
                    'countrys' => [m::mock(Country::class)],
                    'irhpPermits' => [m::mock(IrhpPermit::class)]
                ],
                false
            ],
            'candidate permits, countries and irhp permits' => [
                [
                    'irhpCandidatePermits' => [m::mock(IrhpCandidatePermit::class)],
                    'countrys' => [m::mock(Country::class)],
                    'irhpPermits' => [m::mock(IrhpPermit::class)]
                ],
                false
            ],
        ];
    }

    public function testGetSize()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $prefix = "UK";
        $fromNo = "75";
        $toNo = "150";
        $isReserve = 0;
        $isReplacement = 0;
        $countrys = [];

        $entity = Entity::create($irhpPermitStock, $prefix, $fromNo, $toNo, $isReserve, $isReplacement, $countrys);

        $this->assertEquals(76, $entity->getSize());
    }
}
