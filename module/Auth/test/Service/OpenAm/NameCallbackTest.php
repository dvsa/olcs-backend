<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm;

use Dvsa\Olcs\Auth\Service\OpenAm\Callback\NameCallback;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class NameCallbackTest extends MockeryTestCase
{
    public function testCallback(): void
    {
        $sut = new NameCallback('Username', 'ID1', 'test');
        $result = $sut->toArray();
        $expected = [
            'type' => 'NameCallback',
            'output' => [['name' => 'prompt', 'value' => 'Username']],
            'input' => [
                [
                    'name' => 'ID1',
                    'value' => 'test'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}
