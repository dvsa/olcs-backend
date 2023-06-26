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
        $this->sut = new PasswordService();
    }

    /**
     * @test
     */
    public function generatePassword_ThrowsException_WhenRequestedLengthIsTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PasswordService::ERR_MESSAGE_TOO_SHORT);

        $length = (PasswordService::MINIMUM_LENGTH - 1);
        $this->sut->generatePassword($length);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function generatePassword_ReturnsPassword_ThatConformsToPolicy(): void
    {
        /**
         * Run this test 10000 times since we generate random passwords.
         * Gives more assurance of successful password generation conforming to the password policy.
         **/
        for ($i = 0; $i < 10000; $i++) {
            $password = $this->sut->generatePassword($this->sut::MINIMUM_LENGTH);
            $this->assertCount(
                $this->sut::MINIMUM_LENGTH,
                str_split($password),
                sprintf(
                    "Generated password '%s' does not have the expected length of PasswordService::MINIMUM_LENGTH (%s)",
                    $password,
                    $this->sut::MINIMUM_LENGTH
                )
            );
            $this->assertMatchesRegularExpression(
                "/(?=.*\d)/",
                $password,
                sprintf(
                    "Generated password '%s' does not contain at least one digit",
                    $password
                )
            );
            $this->assertMatchesRegularExpression(
                "/(?=.*[a-z])/",
                $password,
                sprintf(
                    "Generated password '%s' does not contain at least one lowercase character a-z",
                    $password
                )
            );
            $this->assertMatchesRegularExpression(
                "/(?=.*[A-Z])/",
                $password,
                sprintf(
                    "Generated password '%s' does not contain at least one uppercase character A-Z",
                    $password
                )
            );
            $this->assertMatchesRegularExpression(
                "/(?=.*[-=~!@#$%^&*()_+,.\/?;:])/",
                $password,
                sprintf(
                    "Generated password '%s' does not contain at least one symbol",
                    $password
                )
            );
        }
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
