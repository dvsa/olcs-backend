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

        $mockClient->shouldReceive('registerUser')->with(
            $loginId,
            $pid,
            $emailAddress,
            $loginId,
            $loginId,
            Client::REALM_INTERNAL,
            $password
        );

        $sut = new User($mockClient, $mockRandom);

        $sut->registerUser($loginId, $emailAddress, Client::REALM_INTERNAL);
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
            $mockClient->shouldReceive('updateUser')->with($loginId, $expected);
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
                        'field' => 'olcsInActive',
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
}
