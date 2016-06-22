<?php

namespace Dvsa\OlcsTest\Api\Service\Data;

use Dvsa\Olcs\Api\Service\Data\Nysiis;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class IrfoPsvAuthType Test
 * @package CommonTest\Service
 */
class NysiisTest extends MockeryTestCase
{
    /**
     *
     */
    public function testGetNysiisSearchKeys()
    {
        $source = [
            'nysiisForename' => 'fn',
            'nysiisFamilyname' => 'ln'
        ];
        $soapClient = 'soapclient';
        $config = ['foo' => 'bar'];

        $sut = new Nysiis($soapClient, $config);

        $this->assertEquals($source, $sut->getNysiisSearchKeys($source));
        $this->assertEquals($soapClient, $sut->getSoapClient());
        $this->assertEquals($config, $sut->getNysiisConfig());
    }
}
