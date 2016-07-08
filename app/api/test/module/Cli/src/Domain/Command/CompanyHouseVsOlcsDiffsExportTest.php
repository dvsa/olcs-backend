<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command;

use Dvsa\Olcs\Cli\Domain\Command\CompaniesHouseVsOlcsDiffsExport;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Cli\Domain\Command\CompanyHouseVsOlcsDiffsExport
 */
class CompanyHouseVsOlcsDiffsExportTest extends MockeryTestCase
{
    public function test()
    {
        $params = [
            'path'=> 'unit_Path',
        ];

        $sut = CompaniesHouseVsOlcsDiffsExport::create($params);

        static::assertEquals('unit_Path', $sut->getPath());
    }
}
