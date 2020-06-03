<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Faker\Generator;

/**
 * @covers \Dvsa\Olcs\Api\Service\OpenAm\User
 */
class UserTest extends MockeryTestCase
{
    /** @var  Generator | m\MockInterface */
    private $mockRandom;
    /** @var  m\MockInterface */
    private $mockClient;

    /** @var  User */
    private $sut;

    public function setUp()
    {
        $this->mockRandom = m::mock(Generator::class)
            ->shouldReceive('toUpper')->andReturn('A')
            ->shouldReceive('toLower')->andReturn('a')
            ->shouldReceive('randomNumber')->andReturn(1)
            ->shouldReceive('regexify')->with('[A-Za-z0-9]+\[A-Za-z]{5,7}$')->andReturn('password1')
            ->shouldReceive('format')->andReturn(self::anything())
            ->getMock();

        $this->mockRandom->randomLetter = 'a';

        $this->mockClient = m::mock(Client::class);

        $this->sut = new User($this->mockClient, $this->mockRandom);
    }

    public function testRegisterUser()
    {
        $loginId = 'login_id';
        $pid = hash('sha256', 'login_id');
        $emailAddress = 'email@test.com';
        $password = 'Aa1password1';

        $this->mockClient
            ->shouldReceive('registerUser')
            ->once()
            ->with(
                $loginId,
                $pid,
                $emailAddress,
                $loginId,
                $loginId,
                Client::REALM_INTERNAL,
                $password
            );

        $callbackParams = null;

        $this->sut->registerUser(
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

        if ($expected !== null) {
            $this->mockClient->shouldReceive('updateUser')->once()->with($pid, $expected);
        }

        $this->sut->updateUser($pid, $loginId, $emailAddress, $enabled);
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

        $this->mockClient->shouldReceive('updateUser')
            ->once()
            ->with($pid, $expected);

        $this->sut->disableUser($pid);
    }

    public function testResetPassword()
    {
        $password = 'Aa1password1';

        $pid = 'pid';
        $expected = [
            [
                'operation' => 'replace',
                'field' => 'password',
                'value' => $password
            ]
        ];

        $this->mockClient->shouldReceive('updateUser')
            ->once()
            ->with($pid, $expected);

        $callbackParams = null;

        $this->sut->resetPassword(
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

    public function testResetPasswordFail()
    {
        $this->mockClient->shouldReceive('updateUser');

        //  expect
        $this->expectException(\Exception::class, 'Invalid callback: unit_InvalidCallback');

        //  call
        $this->sut->resetPassword(9999, 'unit_InvalidCallback');
    }

    /**
     * @dataProvider provideIsActiveUser
     * @param $userData
     * @param $expected
     */
    public function testIsActiveUser($userData, $expected)
    {
        $pid = 'some-pid';

        $this->mockClient->shouldReceive('fetchUser')->once()->with($pid)->andReturn($userData);

        $this->assertSame($expected, $this->sut->isActiveUser($pid));
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

    public function testFetchUsers()
    {
        $expected = [
            [
                'pid' => 'some-pid-1'
            ],
            [
                'pid' => 'some-pid-2'
            ]
        ];

        $param = ['some-pid-1', 'some-pid-2'];

        $this->mockClient->shouldReceive('fetchUsers')
            ->once()
            ->with($param)
            ->andReturn($expected);

        $this->assertEquals($expected, $this->sut->fetchUsers($param));
    }
}
