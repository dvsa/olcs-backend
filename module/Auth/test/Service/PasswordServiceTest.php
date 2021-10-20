<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service;

use Dvsa\Olcs\Auth\Service\PasswordService;
use Faker\Factory;
use Olcs\TestHelpers\MockeryTestCase;

class PasswordServiceTest extends MockeryTestCase
{
    private PasswordService $sut;

    public function setUp(): void
    {
        $faker = Factory::create();
        $this->sut = new PasswordService($faker);
    }

    /**
     * @test
     */
    public function generatePassword_ThrowsException_WhenRequestedLengthIsTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage(PasswordService::ERR_MESSAGE_TOO_SHORT);

        $length = (PasswordService::MINIMUM_LENGTH - 1);
        $this->sut->generatePassword($length);
    }

    /**
     * @test
     * @dataProvider policyPatternProvider
     */
    public function generatePassword_ReturnsPassword_ThatConformsToPolicy(string $pattern): void
    {
        $password = $this->sut->generatePassword();

        $this->assertSame(1, preg_match($pattern, $password));
    }

    public function policyPatternProvider(): array
    {
        return [
            'contains symbol' => ['/[-=~!@#$%^&*()_+,.<>?;:]/'],
            'contains number' => ['/[0-9]/'],
            'contains uppercase' => ['/[A-Z]/'],
            'contains lowercase' => ['/[a-z]/'],
        ];
    }

    /**
     * @test
     */
    public function generatePassword_ReturnsPassword_ThatMatchesRequestedLength(): void
    {
        $password = $this->sut->generatePassword(15);

        self::assertEquals(15, strlen($password));
    }
}
