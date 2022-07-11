<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\AbstractReviewServiceServices;
use Laminas\I18n\Translator\TranslatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\AbstractReviewServiceServices
 */
class AbstractReviewServiceServicesTest extends MockeryTestCase
{
    public function testGetTranslator()
    {
        $translator = m::mock(TranslatorInterface::class);

        $sut = new AbstractReviewServiceServices($translator);

        $this->assertSame(
            $translator,
            $sut->getTranslator()
        );
    }
}
