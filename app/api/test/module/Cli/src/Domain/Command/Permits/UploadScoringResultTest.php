<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;

/**
 * Upload scoring result test
 *
 */
class UploadScoringResultTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $sut = UploadScoringResult::create(
            [
                'csvContent' => 'TEST',
                'fileDescription' => 'TEST DESCRIPTION'
            ]
        );

        static::assertEquals('TEST', $sut->getCsvContent());
        static::assertEquals('TEST DESCRIPTION', $sut->getFileDescription());
    }
}
