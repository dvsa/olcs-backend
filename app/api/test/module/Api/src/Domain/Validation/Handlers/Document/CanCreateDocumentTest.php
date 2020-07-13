<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Document;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument
 */
class CanCreateDocumentTest extends AbstractHandlerTestCase
{
    /**
     * @var CanCreateDocument
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanCreateDocument();

        $this->sut->setAllowedExtensions(
            [
                CanCreateDocument::EXTENSIONS_KEY_EXTERNAL => 'pdf,RTF,jpG , EXT ',
                CanCreateDocument::EXTENSIONS_KEY_INTERNAL => 'pdf,RTF,jpG , INT ',
            ]
        );

        parent::setUp();
    }

    public function testIsValid()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')
            ->andReturn(false)
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
        $dto->shouldReceive('getContinuationDetail')->andReturn(943);

        $this->setIsValid('canAccessLicence', [176], true);
        $this->setIsValid('canAccessApplication', [276], true);
        $this->setIsValid('canAccessCase', [376], true);
        $this->setIsValid('canAccessTransportManager', [476], true);
        $this->setIsValid('canAccessOperatingCentre', [576], true);
        $this->setIsValid('canAccessBusReg', [676], true);
        $this->setIsValid('canAccessOrganisation', [776], true);
        $this->setIsValid('canAccessSubmission', [876], true);
        $this->setIsValid('canAccessContinuationDetail', [943], true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidEbsr()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setId(876);
        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('isSystemUser')->andReturn(false);
        $mockUser->shouldReceive('getOrganisationUsers->isEmpty')->with()->once()->andReturn(false);
        $mockUser->shouldReceive('getRelatedOrganisation')->with()->once()->andReturn($organisation);

        /** @var CommandInterface|m\MockInterface $dto */
        $dto = \Dvsa\Olcs\Transfer\Command\Document\Upload::create(
            [
                'filename' => 'foo.pdf',
                'isEbsrPack' => 1,
            ]
        );
        $this->setIsValid('canUploadEbsr', [876], true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidOnFalse()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')
            ->andReturn(false)
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
        $dto->shouldReceive('getContinuationDetail')->andReturn(943);

        $this->setIsValid('canAccessLicence', [176], true);
        $this->setIsValid('canAccessApplication', [276], true);
        $this->setIsValid('canAccessCase', [376], true);
        $this->setIsValid('canAccessTransportManager', [476], true);
        $this->setIsValid('canAccessOperatingCentre', [576], false);
        $this->setIsValid('canAccessBusReg', [676], true);
        $this->setIsValid('canAccessOrganisation', [776], true);
        $this->setIsValid('canAccessSubmission', [876], true);
        $this->setIsValid('canAccessContinuationDetail', [943], true);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testIsValidNoChecks()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')->andReturn(false);

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
        $dto->shouldReceive('getContinuationDetail')->andReturn(null);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testIsInternalUser()
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(true);
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.pdf');

        $this->assertTrue($this->sut->isValid($dto));
    }

    /**
     * @dataProvider dataProviderTestIsValidExtensionInternal
     *
     * @param $valid
     * @param $extension
     */
    public function testIsValidExtensionInternal($valid, $extension)
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->andReturn(true);
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.'. $extension);

        if ($valid) {
            $this->assertTrue($this->sut->isValid($dto));
        } else {
            $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
            $this->sut->isValid($dto);
        }
    }

    public function dataProviderTestIsValidExtensionInternal()
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
            // internal specific
            [true, 'INT'],
            // external specific
            [false, 'EXT'],
        ];
    }

    /**
     * @dataProvider dataProviderTestIsValidExtensionExternal
     *
     * @param $valid
     * @param $extension
     */
    public function testIsValidExtensionExternal($valid, $extension)
    {
        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $this->mockUser()->shouldReceive('isSystemUser')->andReturn(false);

        /** @var CommandInterface|m\MockInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getFilename')->with()->once()->andReturn('foo.'. $extension);
        $dto->shouldReceive('getLicence')->andReturn(176);
        $dto->shouldReceive('getApplication')->andReturn(null);
        $dto->shouldReceive('getCase')->andReturn(null);
        $dto->shouldReceive('getTransportManager')->andReturn(null);
        $dto->shouldReceive('getOperatingCentre')->andReturn(null);
        $dto->shouldReceive('getBusReg')->andReturn(null);
        $dto->shouldReceive('getIrfoOrganisation')->andReturn(null);
        $dto->shouldReceive('getSubmission')->andReturn(null);
        $dto->shouldReceive('getContinuationDetail')->andReturn(null);

        $this->setIsValid('canAccessLicence', [176], true);

        if ($valid) {
            $this->assertTrue($this->sut->isValid($dto));
        } else {
            $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
            $this->sut->isValid($dto);
        }
    }

    public function dataProviderTestIsValidExtensionExternal()
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
            // internal specific
            [false, 'INT'],
            // external specific
            [true, 'EXT'],
        ];
    }
}
