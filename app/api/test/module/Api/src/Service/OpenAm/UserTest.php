<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RandomLib\Generator;

/**
 * @covers Dvsa\Olcs\Api\Service\OpenAm\User
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
            ->shouldReceive('generateString')->with(1, Generator::CHAR_UPPER)->andReturn('A')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_LOWER)->andReturn('b')
            ->shouldReceive('generateString')->with(1, Generator::CHAR_DIGITS)->andReturn('1')
            ->shouldReceive('generateString')->with(9, Generator::EASY_TO_READ)->andReturn('password1')
            ->getMock();

        $this->mockClient = m::mock(Client::class);

        $this->sut = new User($this->mockClient, $this->mockRandom);
    }

    public function testRegisterUser()
    {
        $loginId = 'login_id';
        $pid = hash('sha256', 'login_id');
        $emailAddress = 'email@test.com';
        $password = 'Ab1password1';

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
        $password = 'Ab1password1';

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
        $this->setExpectedException(\Exception::class, 'Invalid callback: unit_InvalidCallback');

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
}
