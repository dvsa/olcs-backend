<?php

namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Service\Document\PrintLetter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @covers \Dvsa\Olcs\Api\Service\Document\PrintLetter
 */
class PrintLetterTest extends MockeryTestCase
{
    const TEMPLATE_ID = 7777;

    /** @var  PrintLetter */
    private $sut;
    /** @var  m\MockInterface | Entity\Doc\Document */
    private $mockDocE;
    /** @var  m\MockInterface */
    private $mockLicE;
    /** @var  m\MockInterface */
    private $mockOrgE;
    /** @var  m\MockInterface | ServiceLocatorInterface */
    private $mockSm;
    /** @var  m\MockInterface */
    private $mockRepoSm;

    public function setUp(): void
    {
        $this->sut = new PrintLetter();

        $this->mockOrgE = m::mock(Entity\Organisation\Organisation::class);

        $this->mockLicE = m::mock(Entity\Licence\Licence::class);
        $this->mockLicE->shouldReceive('getOrganisation')->andReturn($this->mockOrgE);

        $this->mockDocE = m::mock(Entity\Doc\Document::class);
        $this->mockDocE->shouldReceive('getRelatedLicence')->andReturn($this->mockLicE);

        $this->mockRepoSm = m::mock(\Dvsa\Olcs\Api\Domain\RepositoryServiceManager::class);

        $this->mockSm = m::mock(ServiceLocatorInterface::class);
        $this->mockSm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->mockRepoSm);
    }

    public function testCanEmailFalseNoLicence()
    {
        /** @var m\MockInterface | Entity\Doc\Document $mockDocE */
        $mockDocE = m::mock(Entity\Doc\Document::class);
        $mockDocE->shouldReceive('getRelatedLicence')->once()->andReturn(null);

        static::assertFalse($this->sut->canEmail($mockDocE));
    }

    public function testCanEmailFalseNotAllowEmail()
    {
        $this->mockOrgE->shouldReceive('getAllowEmail')->once()->andReturn(false);

        static::assertFalse($this->sut->canEmail($this->mockDocE));
    }

    public function testCanEmailFalseHasntEmails()
    {
        $this->mockOrgE
            ->shouldReceive('getAllowEmail')->once()->andReturn(true)
            ->shouldReceive('hasAdminEmailAddresses')->once()->andReturn(false);

        static::assertFalse($this->sut->canEmail($this->mockDocE));
    }

    public function testCanEmailFalseMetadataInvalid()
    {
        $this->mockOrgE
            ->shouldReceive('getAllowEmail')->once()->andReturn(true)
            ->shouldReceive('hasAdminEmailAddresses')->once()->andReturn(true);

        $this->mockDocE->shouldReceive('getMetadata')->once()->andReturn('{}');

        static::assertFalse($this->sut->canEmail($this->mockDocE));
    }

    public function testCanEmailTrue()
    {
        $this->mockOrgE
            ->shouldReceive('getAllowEmail')->once()->andReturn(true)
            ->shouldReceive('hasAdminEmailAddresses')->once()->andReturn(true);

        $this->mockDocE
            ->shouldReceive('getMetadata')
            ->andReturn('{"details": {"documentTemplate" : ' . self::TEMPLATE_ID . '}}');

        $mockDocTemplateE = m::mock(Entity\Doc\DocTemplate::class);
        $mockDocTemplateE->shouldReceive('getSuppressFromOp')->andReturn(false);

        $mockDocTemplateRepo = m::mock(Repository\DocTemplate::class);
        $mockDocTemplateRepo
            ->shouldReceive('fetchById')
            ->with(self::TEMPLATE_ID)
            ->andReturn($mockDocTemplateE);

        $this->mockRepoSm->shouldReceive('get')->with('DocTemplate')->andReturn($mockDocTemplateRepo);

        $this->sut->createService($this->mockSm);

        static::assertTrue($this->sut->canEmail($this->mockDocE));
    }

    public function testCanPrintTrue()
    {
        $this->mockLicE->shouldReceive('getTranslateToWelsh')->once()->andReturn(false);

        static::assertTrue($this->sut->canPrint($this->mockDocE));
    }
}
