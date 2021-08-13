<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

trait ProvidesOperatingCentreVehicleAuthorizationConstraintsTrait
{
    /**
     * @return array
     */
    public function operatingCentreVehicleAuthorisationConstraintsDataProvider(): array
    {
        return [
            'no operating centres' => [
                /* Operating Centres ...[vehicles]: */ [],
                /* Constraints: */ [
                    'minVehicleAuth' => 0,
                    'maxVehicleAuth' => 0,
                ]
            ],
            'one operating centre with vehicles' => [
                /* Operating Centres ...[vehicles]: */ [[1]],
                /* Constraints: */ [
                    'minVehicleAuth' => 1,
                    'maxVehicleAuth' => 1,
                ]
            ],
            'more then one operating centre with vehicles' => [
                /* Operating Centres ...[vehicles]: */ [[1], [2]],
                /* Constraints: */ [
                    'minVehicleAuth' => 2,
                    'maxVehicleAuth' => 3,
                ]
            ],
        ];
    }
}
