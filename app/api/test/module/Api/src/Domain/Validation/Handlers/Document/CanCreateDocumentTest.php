<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Document;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument;

/**
 * @covers Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument
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

        $this->sut->setAllowedExtensions(['pdf', 'RTF', 'jpG ']);

        parent::setUp();
    }

    public function testIsValid()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')
            ->andReturn(false)
            ->once()
            ->getMock();

        /** @var CommandInterface|m\MockInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.pdf');
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
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')
            ->andReturn(false)
            ->once()
            ->getMock();

        /** @var CommandInterface|m\MockInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.pdf');
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
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')->once()->andReturn(false);

        /** @var CommandInterface|m\MockInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.pdf');
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

    public function testIsInternalUser()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(true);
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.pdf');

        $this->assertTrue($this->sut->isValid($dto));
    }

    /**
     * @dataProvider dataProviderTestIsValidExtension
     *
     * @param $valid
     * @param $extension
     */
    public function testIsValidExtension($valid, $extension)
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->andReturn(true);
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.'. $extension);

        if ($valid) {
            $this->assertTrue($this->sut->isValid($dto));
        } else {
            $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
            $this->sut->isValid($dto);
        }
    }

    public function dataProviderTestIsValidExtension()
    {
        return [
            [true, 'jpg'],
            [true, 'JPG'],
            [true, 'JpG'],
            [true, 'rtf'],
            [true, 'pdf'],
            [false, 'GIF'],
            [false, 'gif'],
            [false, 'pd'],
            [false, 'pdfx'],
        ];
    }
}
