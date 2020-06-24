<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessDocument;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Mockery as m;

/**
 * Can Access Document Test
 */
class CanAccessDocumentTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessDocument
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = m::mock(CanAccessDocument::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    public function testIsValidParentTrue()
    {
        $txcInboxId = 52;

        $this->sut->shouldReceive('callParentIsValid')
            ->with($txcInboxId)
            ->andReturn(true);

        $this->assertEquals(true, $this->sut->isValid($txcInboxId));
    }

    public function testIsValidNoTxcEntitiesFalse()
    {
        $txcInboxId = 47;

        $this->sut->shouldReceive('callParentIsValid')
            ->with($txcInboxId)
            ->andReturn(false);

        $linkedTxcInboxEntities = [];

        $txcInboxRepo = $this->mockRepo('TxcInbox');
        $txcInboxRepo->shouldReceive('fetchLinkedToDocument')
            ->andReturn($linkedTxcInboxEntities);

        $this->assertFalse($this->sut->isValid($txcInboxId));
    }

    public function testIsValidNoMatchingLocalAuthoritiesFalse()
    {
        $txcInboxId = 47;

        $this->sut->shouldReceive('callParentIsValid')
            ->with($txcInboxId)
            ->andReturn(false);

        $localAuthority1 = m::mock(LocalAuthority::class)->makePartial();
        $localAuthority1->setId(1);

        $localAuthority2 = m::mock(LocalAuthority::class)->makePartial();
        $localAuthority2->setId(2);

        $this->sut->shouldReceive('getCurrentLocalAuthority')
            ->withNoArgs()
            ->andReturn($localAuthority1);

        $txcInboxEntity1 = m::mock(TxcInboxEntity::class);
        $txcInboxEntity1->shouldReceive('getLocalAuthority')
            ->andReturn(null);

        $txcInboxEntity2 = m::mock(TxcInboxEntity::class);
        $txcInboxEntity2->shouldReceive('getLocalAuthority')
            ->andReturn($localAuthority2);

        $linkedTxcInboxEntities = [$txcInboxEntity1, $txcInboxEntity2];

        $txcInboxRepo = $this->mockRepo('TxcInbox');
        $txcInboxRepo->shouldReceive('fetchLinkedToDocument')
            ->andReturn($linkedTxcInboxEntities);

        $this->assertFalse($this->sut->isValid($txcInboxId));
    }

    public function testIsValidMatchingLocalAuthoritiesTrue()
    {
        $txcInboxId = 47;

        $this->sut->shouldReceive('callParentIsValid')
            ->with($txcInboxId)
            ->andReturn(false);

        $localAuthority1 = m::mock(LocalAuthority::class)->makePartial();
        $localAuthority1->setId(1);

        $localAuthority2 = m::mock(LocalAuthority::class)->makePartial();
        $localAuthority2->setId(2);

        $this->sut->shouldReceive('getCurrentLocalAuthority')
            ->withNoArgs()
            ->andReturn($localAuthority2);

        $txcInboxEntity1 = m::mock(TxcInboxEntity::class);
        $txcInboxEntity1->shouldReceive('getLocalAuthority')
            ->andReturn($localAuthority1);

        $txcInboxEntity2 = m::mock(TxcInboxEntity::class);
        $txcInboxEntity2->shouldReceive('getLocalAuthority')
            ->andReturn(null);

        $txcInboxEntity3 = m::mock(TxcInboxEntity::class);
        $txcInboxEntity3->shouldReceive('getLocalAuthority')
            ->andReturn($localAuthority2);

        $linkedTxcInboxEntities = [$txcInboxEntity1, $txcInboxEntity2, $txcInboxEntity3];

        $txcInboxRepo = $this->mockRepo('TxcInbox');
        $txcInboxRepo->shouldReceive('fetchLinkedToDocument')
            ->andReturn($linkedTxcInboxEntities);

        $this->assertTrue($this->sut->isValid($txcInboxId));
    }
}
