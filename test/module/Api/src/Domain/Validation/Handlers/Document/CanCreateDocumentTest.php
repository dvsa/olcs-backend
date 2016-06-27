<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Document;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument;

/**
 * @covers CanCreateDocumentTest
 */
class CanCreateDocumentTest extends AbstractHandlerTestCase
{
    /**
     * @var CanCreateDocument
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanCreateDocument();

        parent::setUp();
    }

    public function testIsValid()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getLicence')->andReturn(176);
        $dto->shouldReceive('getApplication')->andReturn(276);
        $dto->shouldReceive('getCase')->andReturn(376);
        $dto->shouldReceive('getTransportManager')->andReturn(476);
        $dto->shouldReceive('getOperatingCentre')->andReturn(576);
        $dto->shouldReceive('getBusReg')->andReturn(676);
        $dto->shouldReceive('getIrfoOrganisation')->andReturn(776);
        $dto->shouldReceive('getSubmission')->andReturn(876);

        $this->setIsValid('canAccessLicence', [176], true);
        $this->setIsValid('canAccessApplication', [276], true);
        $this->setIsValid('canAccessCase', [376], true);
        $this->setIsValid('canAccessTransportManager', [476], true);
        $this->setIsValid('canAccessOperatingCentre', [576], true);
        $this->setIsValid('canAccessBusReg', [676], true);
        $this->setIsValid('canAccessOrganisation', [776], true);
        $this->setIsValid('canAccessSubmission', [876], true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidOnFalse()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getLicence')->andReturn(176);
        $dto->shouldReceive('getApplication')->andReturn(276);
        $dto->shouldReceive('getCase')->andReturn(376);
        $dto->shouldReceive('getTransportManager')->andReturn(476);
        $dto->shouldReceive('getOperatingCentre')->andReturn(576);
        $dto->shouldReceive('getBusReg')->andReturn(676);
        $dto->shouldReceive('getIrfoOrganisation')->andReturn(776);
        $dto->shouldReceive('getSubmission')->andReturn(876);

        $this->setIsValid('canAccessLicence', [176], true);
        $this->setIsValid('canAccessApplication', [276], true);
        $this->setIsValid('canAccessCase', [376], true);
        $this->setIsValid('canAccessTransportManager', [476], true);
        $this->setIsValid('canAccessOperatingCentre', [576], false);
        $this->setIsValid('canAccessBusReg', [676], true);
        $this->setIsValid('canAccessOrganisation', [776], true);
        $this->setIsValid('canAccessSubmission', [876], true);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testIsValidNoChecks()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getLicence')->andReturn(null);
        $dto->shouldReceive('getApplication')->andReturn(null);
        $dto->shouldReceive('getCase')->andReturn(null);
        $dto->shouldReceive('getTransportManager')->andReturn(null);
        $dto->shouldReceive('getOperatingCentre')->andReturn(null);
        $dto->shouldReceive('getBusReg')->andReturn(null);
        $dto->shouldReceive('getIrfoOrganisation')->andReturn(null);
        $dto->shouldReceive('getSubmission')->andReturn(null);

        $this->assertFalse($this->sut->isValid($dto));
    }
}
