<?php

namespace Dvsa\OlcsTest\Email\Transport;

use Dvsa\Olcs\Email\Transport\S3FileOptions;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class S3FileOptionsTest
 */
class S3FileOptionsTest extends MockeryTestCase
{
    public function testSetGet()
    {
        $sut = new S3FileOptions();

        $this->assertSame(null, $sut->getS3Path());

        $sut->setS3Path('PATH');

        $this->assertSame('PATH', $sut->getS3Path());
    }
}
