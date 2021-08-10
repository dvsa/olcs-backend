<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm;

use Dvsa\Olcs\Auth\Service\OpenAm\Callback\PasswordCallback;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PasswordCallbackTest extends MockeryTestCase
{
    public function testCallback(): void
    {
        $sut = new PasswordCallback('UserPassword', 'ID1', 'test', false);
        $result = $sut->toArray();
        $expected = [
            'type' => 'PasswordCallback',
            'output' => [['name' => 'prompt', 'value' => 'UserPassword']],
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
