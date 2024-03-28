<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessDocument;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\Role;
use Dvsa\Olcs\GdsVerify\Exception;
use LmcRbacMvc\Identity\IdentityInterface;
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

    private const IS_INTERNAL_USER = 1;
    private const IS_EXTERNAL_USER = 2;
    private const IS_LOCAL_AUTHORITY_USER = 3;

    public function setUp(): void
    {
        $this->sut = m::mock(CanAccessDocument::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    /**
     * @dataProvider userCanAccessDocumentDataProvider
     * @return void
     * @throws Exception
     */
    public function testUserCanAccessDocument($userType, $document, $expectedResult)
    {
        $this->getMockIdentity($userType);

        $this->mockRepo(\Dvsa\Olcs\Api\Domain\Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document);

        $this->mockRepo(\Dvsa\Olcs\Api\Domain\Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->andReturn([]);

        $this->setIsValid('isOwner', [$document], true);

        $this->assertEquals($expectedResult, $this->sut->isValid(123));
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

        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->once()->andReturn(false);

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

        $this->auth->shouldReceive('isGranted')->once()->andReturn(false);
        $this->auth->shouldReceive('getIdentity')->once()->andReturn(false);


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

        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->once()->andReturn(false);

        $this->assertTrue($this->sut->isValid($txcInboxId));
    }

    private function getMockDocument(bool $isExternal): m\MockInterface {

        $mockDoc = m::mock(Document::class);

        $mockDoc->shouldReceive('getIsExternal')->andReturn($isExternal);
        $mockDoc->allows('getId')->andReturn(123);

        return $mockDoc;
    }

    private function getMockIdentity(int $userType): m\MockInterface {

        $mockIdentity = m::mock(IdentityInterface::class);
        $mockIdentity->shouldReceive('getUser->getOrganisationUsers')->andReturn(new ArrayCollection([]));
        $mockIdentity->shouldReceive('getUser->isSystemUser')->andReturn(false);

        $this->auth->shouldReceive('getIdentity')->andReturn($mockIdentity);

        switch($userType) {
            case static::IS_INTERNAL_USER:
                $this->auth->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true);
                $this->auth->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->twice()->andReturn(false);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_EXTERNAL_USER:
                $this->auth->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_LOCAL_AUTHORITY_USER:
                $this->auth->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(true);
                $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(true);
                break;
            default:
                throw new Exception();
        }

        return $mockIdentity;
    }

    public function userCanAccessDocumentDataProvider(): array
    {
        return [
            'internalUserCanAccess' => [
                'userType' => static::IS_INTERNAL_USER,
                'document' => null,
                'expected' => true
            ],

            'externalUserCanAccessExternalOwnedDocument' => [
                'userType' => static::IS_EXTERNAL_USER,
                'document' => $this->getMockDocument(true),
                'expected' => true
            ],

            'externalUserCannotAccessInternalDocument' => [
                'userType' => static::IS_EXTERNAL_USER,
                'document' => $this->getMockDocument(false),
                'expected' => false
            ],
        ];
    }
}
