<?php

/**
 * User Test
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * User Test
 */
class UserTest extends MockeryTestCase
{
    public function testRegisterUser()
    {
        $pid = '1234567891234567891234567890aced';
        $loginId = 'login_id';
        $emailAddress = 'email@test.com';
        $password = 'password1234';

        $mockRandom = m::mock(\RandomLib\Generator::class);
        $mockRandom->shouldReceive('generateString')
            ->with(32, '0123456789abcdef')
            ->andReturn($pid);
        $mockRandom->shouldReceive('generateString')
            ->with(12)
            ->andReturn($password);

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
    public function testUpdateUser($expected, $emailAddress, $enabled)
    {
        $loginId = 'login_id';

        $mockRandom = m::mock(\RandomLib\Generator::class);

        $mockClient = m::mock(Client::class);

        if ($expected !== null) {
            $mockClient->shouldReceive('updateUser')->once()->with($loginId, $expected);
        }

        $sut = new User($mockClient, $mockRandom);

        $sut->updateUser($loginId, $emailAddress, $enabled);
    }

    public function provideUpdateUser()
    {
        return [
            [
                [
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
                'email@test.com',
                true
            ],
            [
                null,
                null,
                null
            ]
        ];
    }

    public function testDisableUser()
    {
        $loginId = 'login_id';
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
            ->with($loginId, $expected);

        $sut = new User($mockClient, $mockRandom);

        $sut->disableUser($loginId);
    }
}
