<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessDocument;
use Dvsa\Olcs\Api\Entity\User\Permission;
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

    public function setUp()
    {
        $this->sut = new CanAccessDocument();

        parent::setUp();
    }

    public function testIsValidParentTrue()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(true, $this->sut->isValid(111));
    }

    public function testIsValidTxcInboxTrue()
    {
        // make parent isValid return false
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $documentEntity = m::mock();
        $repo = $this->mockRepo('Document');
        $repo->shouldReceive('fetchById')->with('723')->andReturn($documentEntity);
        $this->setIsValid('isOwner', [$documentEntity], false);

        $mockTxcInboxEntity = m::mock();
        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchLinkedToDocument')->with('723')->andReturn([$mockTxcInboxEntity]);
        $this->setIsValid('isOwner', [$mockTxcInboxEntity], true);

        $this->assertEquals(true, $this->sut->isValid('723'));
    }

    public function testIsValidTxcInboxFalse()
    {
        // make parent isValid return false
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $documentEntity = m::mock();
        $repo = $this->mockRepo('Document');
        $repo->shouldReceive('fetchById')->with('723')->andReturn($documentEntity);
        $this->setIsValid('isOwner', [$documentEntity], false);

        $mockTxcInboxEntity = m::mock();
        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchLinkedToDocument')->with('723')->andReturn([$mockTxcInboxEntity]);
        $this->setIsValid('isOwner', [$mockTxcInboxEntity], false);

        $this->assertEquals(false, $this->sut->isValid('723'));
    }

    public function testIsValidTxcInboxNotFound()
    {
        // make parent isValid return false
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $documentEntity = m::mock();
        $repo = $this->mockRepo('Document');
        $repo->shouldReceive('fetchById')->with('723')->andReturn($documentEntity);
        $this->setIsValid('isOwner', [$documentEntity], false);

        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchLinkedToDocument')->with('723')->andReturn([]);

        $this->assertEquals(false, $this->sut->isValid('723'));
    }
}
