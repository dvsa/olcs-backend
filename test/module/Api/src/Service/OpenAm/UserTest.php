<?php

/**
 * User Test
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RandomLib\Generator;

/**
 * User Test
 */
class UserTest extends MockeryTestCase
{
    public function testRegisterUser()
    {
        $loginId = 'login_id';
        $pid = hash('sha256', 'login_id');
        $emailAddress = 'email@test.com';
        $password = 'Ab1password1';

        $mockRandom = m::mock(Generator::class);
        $mockRandom
            ->shouldReceive('generateString')->with(1, Generator::CHAR_UPPER)->andReturn('A')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_LOWER)->andReturn('b')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_DIGITS)->andReturn('1')
            ->shouldReceive('generateString')->with(9, Generator::EASY_TO_READ)->andReturn('password1');

        $mockClient = m::mock(Client::class);

        $mockClient->shouldReceive('registerUser')->once()->with(
            $loginId,
            $pid,
            $emailAddress,
            $loginId,
            $loginId,
            Client::REALM_INTERNAL,
            $password
        );

        $sut = new User($mockClient, $mockRandom);

        $callbackParams = null;

        $sut->registerUser(
            $loginId,
            $emailAddress,
            Client::REALM_INTERNAL,
            function ($params) use (&$callbackParams) {
                $callbackParams = $params;
            }
        );

        $this->assertEquals(
            [
                'password' => $password
            ],
            $callbackParams
        );
    }

    /**
     * @dataProvider provideUpdateUser
     * @param $expected
     * @param $emailAddress
     * @param $enabled
     */
    public function testUpdateUser($expected, $loginId, $emailAddress, $enabled)
    {
        $pid = 'some-pid';

        $mockRandom = m::mock(\RandomLib\Generator::class);

        $mockClient = m::mock(Client::class);

        if ($expected !== null) {
            $mockClient->shouldReceive('updateUser')->once()->with($pid, $expected);
        }

        $sut = new User($mockClient, $mockRandom);

        $sut->updateUser($pid, $loginId, $emailAddress, $enabled);
    }

    public function provideUpdateUser()
    {
        return [
            'New Username' => [
                [
                    [
                        'operation' => 'replace',
                        'field' => 'userName',
                        'value' => 'new_login_id'
                    ]
                ],
                'new_login_id',
                null,
                null
            ],
            'New Email address' => [
                [
                    [
                        'operation' => 'replace',
                        'field' => 'emailAddress',
                        'value' => 'email@test.com'
                    ]
                ],
                null,
                'email@test.com',
                null
            ],
            'New State' => [
                [
                    [
                        'operation' => 'replace',
                        'field' => 'inActive',
                        'value' => true
                    ]
                ],
                null,
                null,
                true
            ],
            'Full Update' => [
                [
                    [
                        'operation' => 'replace',
                        'field' => 'userName',
                        'value' => 'new_login_id'
                    ],
                    [
                        'operation' => 'replace',
                        'field' => 'emailAddress',
                        'value' => 'email@test.com'
                    ],
                    [
                        'operation' => 'replace',
                        'field' => 'inActive',
                        'value' => true
                    ]
                ],
                'new_login_id',
                'email@test.com',
                true
            ],
            'No change' => [
                null,
                null,
                null,
                null
            ]
        ];
    }

    public function testDisableUser()
    {
        $pid = 'pid';
        $expected = [
            [
                'operation' => 'replace',
                'field' => 'inActive',
                'value' => true
            ]
        ];

        $mockRandom = m::mock(\RandomLib\Generator::class);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('updateUser')
            ->once()
            ->with($pid, $expected);

        $sut = new User($mockClient, $mockRandom);

        $sut->disableUser($pid);
    }

    public function testResetPassword()
    {
        $password = 'Ab1password1';

        $pid = 'pid';
        $expected = [
            [
                'operation' => 'replace',
                'field' => 'password',
                'value' => $password
            ]
        ];

        $mockRandom = m::mock(Generator::class);
        $mockRandom
            ->shouldReceive('generateString')->with(1, Generator::CHAR_UPPER)->andReturn('A')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_LOWER)->andReturn('b')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_DIGITS)->andReturn('1')
            ->shouldReceive('generateString')->with(9, Generator::EASY_TO_READ)->andReturn('password1');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('updateUser')
            ->once()
            ->with($pid, $expected);

        $sut = new User($mockClient, $mockRandom);

        $callbackParams = null;

        $sut->resetPassword(
            $pid,
            function ($params) use (&$callbackParams) {
                $callbackParams = $params;
            }
        );

        $this->assertEquals(
            [
                'password' => $password
            ],
            $callbackParams
        );
    }

    /**
     * @dataProvider provideIsActiveUser
     * @param $userData
     * @param $expected
     */
    public function testIsActiveUser($userData, $expected)
    {
        $pid = 'some-pid';

        $mockRandom = m::mock(\RandomLib\Generator::class);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('fetchUser')->once()->with($pid)->andReturn($userData);

        $sut = new User($mockClient, $mockRandom);

        $this->assertSame($expected, $sut->isActiveUser($pid));
    }

    public function provideIsActiveUser()
    {
        return [
            'user never logged in before' => [
                [
                    'pid' => 'some-pid',
                ],
                false
            ],
            'user logged in before' => [
                [
                    'pid' => 'some-pid',
                    'lastLoginTime' => '2016-02-02'
                ],
                true
            ],
        ];
    }
}
