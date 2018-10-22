<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;

/**
 * Upload scoring result test
 *
 */
class UploadScoringResultTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = UploadScoringResult::create(
            [
                'csvContent' => 'TEST'
            ]
        );

        static::assertEquals('TEST', $sut->getCsvContent());
    }
}
