<?php

namespace Dvsa\Olcs\Cpms\Unit;

use Dvsa\Olcs\Cpms\Authenticate\AccessToken;
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    /**
     * @test
     * @dataProvider isExpiredDataProvider
     * @param int $issuedAt
     * @param int $expiresIn
     * @param bool $isExpired
     */
    public function isExpired(int $issuedAt, int $expiresIn, bool $isExpired)
    {
        $accessToken = new AccessToken(
            "accessToken",
            $expiresIn,
            $issuedAt,
            "scope",
            "Bearer"
        );

        $this->assertEquals($isExpired, $accessToken->isExpired());
    }

    /**
     * @test
     */
    public function getAuthorisationHeader()
    {
        $accessToken = new AccessToken(
            "accessToken",
            12345,
            12344,
            "scope",
            "Bearer"
        );

        $this->assertStringStartsWith('Bearer ', $accessToken->getAuthorisationHeader());
    }


    public function isExpiredDataProvider()
    {
        return [
            'has expired' => [
                'issuedAt' => time() - 300,
                'expiresIn' => 240,
                'isExpired' => true
            ],
            "hasn't expired" => [
                'issuedAt' => time(),
                'expiresIn' => 60,
                'isExpired' => false
            ]
        ];
    }
}
