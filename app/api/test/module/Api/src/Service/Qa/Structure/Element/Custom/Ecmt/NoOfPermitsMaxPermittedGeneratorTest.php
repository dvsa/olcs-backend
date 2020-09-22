<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt\NoOfPermitsMaxPermittedGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * NoOfPermitsMaxPermittedGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsMaxPermittedGeneratorTest extends MockeryTestCase
{
    private $irhpPermitType;

    private $irhpApplication;

    private $irhpPermitRepo;

    private $noOfPermitsMaxPermittedGenerator;

    public function setUp(): void
    {
        $this->irhpPermitType = m::mock(IrhpPermitTypeEntity::class);

        $this->irhpApplication = m::mock(IrhpApplicationEntity::class);
        $this->irhpApplication->shouldReceive('getIrhpPermitType')
            ->withNoArgs()
            ->andReturn($this->irhpPermitType);

        $this->irhpPermitRepo = m::mock(IrhpPermitRepository::class);

        $this->noOfPermitsMaxPermittedGenerator = new NoOfPermitsMaxPermittedGenerator($this->irhpPermitRepo);
    }

    public function testGenerateEcmtAnnual()
    {
        $licenceId = 707;
        $totAuthVehicles = 15;
        $validityYear = 2023;
        $allocatedPermitCount = 11;
        $expectedMaxPermitted = 4;

        $this->irhpPermitType->shouldReceive('isEcmtAnnual')
            ->withNoArgs()
            ->andReturnTrue();
        $this->irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->withNoArgs()
            ->andReturnFalse();

        $this->irhpApplication->shouldReceive('getAssociatedStock->getValidityYear')
            ->withNoArgs()
            ->andReturn($validityYear);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($licenceId);
        $licence->shouldReceive('getTotAuthVehicles')
            ->withNoArgs()
            ->andReturn($totAuthVehicles);

        $this->irhpApplication->shouldReceive('getLicence')
            ->withNoArgs()
            ->andReturn($licence);

        $this->irhpPermitRepo->shouldReceive('getEcmtAnnualPermitCountByLicenceAndStockEndYear')
            ->with($licenceId, $validityYear)
            ->andReturn($allocatedPermitCount);

        $this->assertEquals(
            $expectedMaxPermitted,
            $this->noOfPermitsMaxPermittedGenerator->generate($this->irhpApplication)
        );
    }

    public function testGenerateEcmtShortTerm()
    {
        $totAuthVehicles = 12;
        $totAuthVehiclesTimesTwo = 24;

        $this->irhpPermitType->shouldReceive('isEcmtAnnual')
            ->withNoArgs()
            ->andReturnFalse();
        $this->irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->withNoArgs()
            ->andReturnTrue();

        $this->irhpApplication->shouldReceive('getLicence->getTotAuthVehicles')
            ->withNoArgs()
            ->andReturn($totAuthVehicles);

        $this->assertEquals(
            $totAuthVehiclesTimesTwo,
            $this->noOfPermitsMaxPermittedGenerator->generate($this->irhpApplication)
        );
    }

    public function testGenerateInvalidType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(NoOfPermitsMaxPermittedGenerator::ERR_INVALID_TYPE);

        $this->irhpPermitType->shouldReceive('isEcmtAnnual')
            ->withNoArgs()
            ->andReturnFalse();
        $this->irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->withNoArgs()
            ->andReturnFalse();

        $this->noOfPermitsMaxPermittedGenerator->generate($this->irhpApplication);
    }
}
