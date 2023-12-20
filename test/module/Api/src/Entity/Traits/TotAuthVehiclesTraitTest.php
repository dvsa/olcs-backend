<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Entity\Traits;

/**
 * @see \Dvsa\Olcs\Api\Entity\Traits\TotAuthVehiclesTrait
 */
trait TotAuthVehiclesTraitTest
{
    /**
     * @test
     */
    public function updateTotAuthHgvVehiclesIsCallable()
    {
        // Assert
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'updateTotAuthHgvVehicles']);
    }

    /**
     * @test
     * @depends updateTotAuthHgvVehiclesIsCallable
     */
    public function updateTotAuthHgvVehiclesReturnsSelf()
    {
        // Assert
        $this->setUpSut();
        $aNumberOfVehicles = 2;

        // Execute
        $result = $this->sut->updateTotAuthHgvVehicles($aNumberOfVehicles);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @return array
     */
    public function validTotAuthHgvVehiclesCountsDataProvider(): array
    {
        return [
            'zero' => [0],
            'positive integer' => [1],
            'null' => [null],
        ];
    }

    /**
     * @param mixed $count
     * @test
     * @depends updateTotAuthHgvVehiclesIsCallable
     * @dataProvider validTotAuthHgvVehiclesCountsDataProvider
     */
    public function updateTotAuthHgvVehiclesSetsTotAuthHgvVehicles($count)
    {
        // Assert
        $this->setUpSut();

        // Execute
        $this->sut->updateTotAuthHgvVehicles($count);

        // Assert
        $this->assertSame($count, $this->sut->getTotAuthHgvVehicles());
    }

    /**
     * @return array
     */
    public function invalidTotAuthHgvVehiclesCountsDataProvider(): array
    {
        return [
            'zero string' => ['0'],
            'positive integer string' => ['1'],
            'empty string' => [''],
            'empty array' => [[]],
        ];
    }

    /**
     * @param mixed $count
     * @test
     * @depends updateTotAuthHgvVehiclesIsCallable
     * @dataProvider invalidTotAuthHgvVehiclesCountsDataProvider
     */
    public function updateTotAuthHgvVehiclesRejectsInvalidValues($count)
    {
        // Assert
        $this->setUpSut();

        // Expect
        $this->expectException('TypeError');
        $this->expectExceptionMessageMatches('/.*Argument 1 passed.*must be of the type int.*/');

        // Execute
        $this->sut->updateTotAuthHgvVehicles($count);
    }

    /**
     * @test
     * @depends updateTotAuthHgvVehiclesIsCallable
     */
    public function updateTotAuthHgvVehiclesSetsTotAuthVehiclesToTheTotalOfLgvsAndHgvs()
    {
        // Assert
        $this->setUpSut();
        $aNumberOfVehicles = 2;
        $expectedNumber = $aNumberOfVehicles + $aNumberOfVehicles;

        // Execute
        $this->sut->setTotAuthLgvVehicles($aNumberOfVehicles);
        $this->sut->updateTotAuthHgvVehicles($aNumberOfVehicles);

        // Assert
        $this->assertSame($expectedNumber, $this->sut->getTotAuthVehicles());
    }

    /**
     * @test
     */
    public function updateTotAuthLgvVehiclesIsCallable()
    {
        // Assert
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'updateTotAuthLgvVehicles']);
    }

    /**
     * @test
     * @depends updateTotAuthLgvVehiclesIsCallable
     */
    public function updateTotAuthLgvVehiclesReturnsSelf()
    {
        // Assert
        $this->setUpSut();
        $aNumberOfVehicles = 2;

        // Execute
        $result = $this->sut->updateTotAuthLgvVehicles($aNumberOfVehicles);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @return array
     */
    public function validTotAuthLgvVehiclesCountsDataProvider(): array
    {
        return [
            'zero' => [0],
            'positive integer' => [1],
            'null' => [null],
        ];
    }

    /**
     * @param mixed $count
     * @test
     * @depends updateTotAuthLgvVehiclesIsCallable
     * @dataProvider validTotAuthLgvVehiclesCountsDataProvider
     */
    public function updateTotAuthLgvVehiclesSetsTotAuthHgvVehicles($count)
    {
        // Assert
        $this->setUpSut();

        // Execute
        $this->sut->updateTotAuthLgvVehicles($count);

        // Assert
        $this->assertSame($count, $this->sut->getTotAuthLgvVehicles());
    }

    /**
     * @return array
     */
    public function invalidTotAuthLgvVehiclesCountsDataProvider(): array
    {
        return [
            'zero string' => ['0'],
            'positive integer string' => ['1'],
            'empty string' => [''],
            'empty array' => [[]],
        ];
    }

    /**
     * @param mixed $count
     * @test
     * @depends updateTotAuthLgvVehiclesIsCallable
     * @dataProvider invalidTotAuthLgvVehiclesCountsDataProvider
     */
    public function updateTotAuthLgvVehiclesRejectsInvalidValues($count)
    {
        // Assert
        $this->setUpSut();

        // Expect
        $this->expectException('TypeError');
        $this->expectExceptionMessageMatches('/.*Argument 1 passed.*must be of the type int.*/');

        // Execute
        $this->sut->updateTotAuthLgvVehicles($count);
    }

    /**
     * @test
     * @depends updateTotAuthLgvVehiclesIsCallable
     */
    public function updateTotAuthLgvVehiclesSetsTotAuthVehiclesToTheTotalOfLgvsAndHgvs()
    {
        // Assert
        $this->setUpSut();
        $aNumberOfVehicles = 2;
        $expectedNumber = $aNumberOfVehicles + $aNumberOfVehicles;

        // Execute
        $this->sut->setTotAuthHgvVehicles($aNumberOfVehicles);
        $this->sut->updateTotAuthLgvVehicles($aNumberOfVehicles);

        // Assert
        $this->assertSame($expectedNumber, $this->sut->getTotAuthVehicles());
    }
}
