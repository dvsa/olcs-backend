<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\PermitUsageSelectionGenerator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * PermitUsageSelectionGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageSelectionGeneratorTest extends MockeryTestCase
{
    private $permitUsageSelectionGenerator;

    public function setUp(): void
    {
        $this->permitUsageSelectionGenerator = new PermitUsageSelectionGenerator();
    }

    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($requiredPermits, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->permitUsageSelectionGenerator->generate($requiredPermits)
        );
    }

    public function dpGenerate()
    {
        return [
            [
                [
                    'standard-journey_single' => 5,
                    'cabotage-journey_single' => 5
                ],
                RefData::JOURNEY_SINGLE
            ],
            [
                [
                    'standard-journey_single' => 5
                ],
                RefData::JOURNEY_SINGLE
            ],
            [
                [
                    'cabotage-journey_single' => 5
                ],
                RefData::JOURNEY_SINGLE
            ],
            [
                [
                    'standard-journey_multiple' => 5,
                    'cabotage-journey_multiple' => 5
                ],
                RefData::JOURNEY_MULTIPLE
            ],
            [
                [
                    'standard-journey_multiple' => 5
                ],
                RefData::JOURNEY_MULTIPLE
            ],
            [
                [
                    'cabotage-journey_multiple' => 5
                ],
                RefData::JOURNEY_MULTIPLE
            ],
        ];
    }

    public function testGenerateException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Found zero or multiple journey types in input data');

        $requiredPermits = [
            'standard-journey_single' => 5,
            'cabotage-journey_multiple' => 2
        ];

        $this->permitUsageSelectionGenerator->generate($requiredPermits);
    }
}
