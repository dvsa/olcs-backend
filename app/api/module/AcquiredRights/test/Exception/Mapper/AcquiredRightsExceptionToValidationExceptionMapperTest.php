<?php

namespace Dvsa\Olcs\AcquiredRights\Exception\Mapper;

use Dvsa\Olcs\AcquiredRights\Client\Mapper\ApplicationReferenceMapper;
use Dvsa\Olcs\AcquiredRights\Exception\AcquiredRightsException;
use Dvsa\Olcs\AcquiredRights\Exception\MapperParseException;
use Dvsa\Olcs\AcquiredRights\Model\ApplicationReference;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AcquiredRightsExceptionToValidationExceptionMapperTest extends MockeryTestCase
{
    protected const INPUT_NAME = 'testInputFieldName';

    /**
     * @test
     * @dataProvider dataProvider_exceptionMap
     */
    public function mapException_ThrowsValidationException_OnMappedExceptions(string $exception, string $key, string $message)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            var_export(
                [
                self::INPUT_NAME => [
                    $key => $message
                ]
                ],
                true
            )
        );

        AcquiredRightsExceptionToValidationExceptionMapper::mapException(new $exception(), self::INPUT_NAME);
    }

    /**
     * @test
     */
    public function mapException_RethrowsException_WhenMappingDoesNotExistForException_AndRethrowIsTrue()
    {
        $exception = new AcquiredRightsException();

        $this->expectException(AcquiredRightsException::class);
        $this->expectExceptionObject($exception);

        AcquiredRightsExceptionToValidationExceptionMapper::mapException($exception, self::INPUT_NAME);
    }

    /**
     * @test
     */
    public function mapException_ReturnsFalse_WhenMappingDoesNotExistForException_AndRethrowIsFalse()
    {
        $result = AcquiredRightsExceptionToValidationExceptionMapper::mapException(new AcquiredRightsException(), self::INPUT_NAME, false);

        $this->assertFalse($result);
    }

    public function dataProvider_exceptionMap(): array
    {
        $result = [];

        foreach (AcquiredRightsExceptionToValidationExceptionMapper::MAP as $exception => $config) {
            $result[$exception] = [
                $exception,
                array_keys($config)[0],
                array_values($config)[0],
            ];
        }

        return $result;
    }
}
