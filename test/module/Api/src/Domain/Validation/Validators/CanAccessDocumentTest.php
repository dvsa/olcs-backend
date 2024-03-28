<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessDocument;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer;
use Exception;
use LmcRbacMvc\Identity\IdentityInterface;
use Mockery as m;
use Mockery\MockInterface;

class CanAccessDocumentTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessDocument
     */
    protected $sut;

    private const IS_SYSTEM_USER = 0;
    private const IS_INTERNAL_USER = 1;
    private const IS_EXTERNAL_USER = 2;
    private const IS_LOCAL_AUTHORITY_USER = 3;
    private const IS_LOCAL_AUTHORITY_ADMIN = 4;
    private const IS_TRANSPORT_MANAGER_USER = 5;
    private const DOCUMENT_ID = 123;

    public function setUp(): void
    {
        $this->sut = m::mock(CanAccessDocument::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testSystemUserCanAccessDocument(): void
    {
        $this->setupMockIdentity(static::IS_SYSTEM_USER);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testInternalUserCanAccessDocument(): void
    {
        $this->setupMockIdentity(static::IS_INTERNAL_USER);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testExternalUserCanAccessDocumentIsExternalDocument(): void
    {
        $this->setupMockIdentity(static::IS_EXTERNAL_USER);

        $this->mockRepo(\Dvsa\Olcs\Api\Domain\Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(true));

        $this->setIsValid('isOwner', [$document], true);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testExternalUserCanAccessDocumentNotExternalDocumentIsCorrespondence(): void
    {
        $this->setupMockIdentity(static::IS_EXTERNAL_USER, $org = $this->getMockOrganisation());

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\Correspondence::class)
            ->shouldReceive('fetchList')
            ->withArgs(function ($query) use ($org) {
                $this->assertInstanceOf(Transfer\Query\QueryInterface::class, $query);
                $this->assertInstanceOf(Transfer\Query\Correspondence\Correspondences::class, $query);
                /** @var Transfer\Query\Correspondence\Correspondences $query */
                return $query->getOrganisation() === $org->getId();
            })
            ->andReturn(new ArrayCollection([
                ['document' => static::DOCUMENT_ID],
            ]));

        $this->setIsValid('isOwner', [$document], true);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testExternalUserCanAccessDocumentNotExternalDocumentNotCorrespondenceIsTxcDocument(): void
    {
        $this->setupMockIdentity(static::IS_EXTERNAL_USER, $org = $this->getMockOrganisation());

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\Correspondence::class)
            ->shouldReceive('fetchList')
            ->withArgs(function ($query) use ($org) {
                $this->assertInstanceOf(Transfer\Query\QueryInterface::class, $query);
                $this->assertInstanceOf(Transfer\Query\Correspondence\Correspondences::class, $query);
                /** @var Transfer\Query\Correspondence\Correspondences $query */
                return $query->getOrganisation() === $org->getId();
            })
            ->andReturn(new ArrayCollection([]));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                [m::mock(Entity\Ebsr\TxcInbox::class)]
            ]);

        $this->setIsValid('isOwner', [$document], true);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testExternalUserCannotAccessDocumentNotExternalDocumentNotCorrespondenceNotTxcDocument(): void
    {
        $this->setupMockIdentity(static::IS_EXTERNAL_USER, $org = $this->getMockOrganisation());

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($this->getMockDocument(false));
        $this->mockRepo(Repository\Correspondence::class)
            ->shouldReceive('fetchList')
            ->withArgs(function ($query) use ($org) {
                $this->assertInstanceOf(Transfer\Query\QueryInterface::class, $query);
                $this->assertInstanceOf(Transfer\Query\Correspondence\Correspondences::class, $query);
                /** @var Transfer\Query\Correspondence\Correspondences $query */
                return $query->getOrganisation() === $org->getId();
            })
            ->andReturn(new ArrayCollection([]));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([]);

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testExternalUserWithNoOrgCannotAccessDocumentNotExternalDocumentNotCorrespondenceAsNoOrg(): void
    {
        $this->setupMockIdentity(static::IS_EXTERNAL_USER);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\Correspondence::class)
            ->shouldReceive('fetchList')
            ->never()
            ->andReturn(new ArrayCollection([]));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([]);

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testTransportManagerCanAccessDocument(): void
    {
        $this->setupMockIdentity(static::IS_TRANSPORT_MANAGER_USER, null, $userId = 437924);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(true, $userId));

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function testTransportManagerCannotAccessDocumentIfNotCreatedBySameUser(): void
    {
        $this->setupMockIdentity(static::IS_TRANSPORT_MANAGER_USER, null, 9000);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(true, 8999));

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCanAccessTxcDocumentForTheirAuthority(int $localAuthorityUserType): void
    {
        $mockLocalAuthority = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthority
            ->allows('getId')
            ->andReturn(780245);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthority);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                m::mock(Entity\Ebsr\TxcInbox::class)
                ->shouldReceive('getLocalAuthority')
                ->andReturn($mockLocalAuthority)
                ->getMock()
            ]);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCanAccessTxcDocumentForTheirAuthorityMultipleTxcRecordsMatchingCurrentAuthority(int $localAuthorityUserType): void
    {
        $mockLocalAuthority = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthority
            ->allows('getId')
            ->andReturn(780245);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthority);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn($mockLocalAuthority)
                    ->getMock(),
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn($mockLocalAuthority)
                    ->getMock(),
            ]);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCanAccessTxcDocumentForTheirAuthorityMultipleTxcRecordsOneMatchingCurrentAuthorityAnotherNoAuthority(int $localAuthorityUserType): void
    {
        $mockLocalAuthority = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthority
            ->allows('getId')
            ->andReturn(780245);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthority);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn($mockLocalAuthority)
                    ->getMock(),
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn(null)
                    ->getMock(),
            ]);

        $this->assertTrue($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCannotAccessTxcDocumentForDifferentAuthority(int $localAuthorityUserType): void
    {
        $mockLocalAuthorityA = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthorityA
            ->allows('getId')
            ->andReturn(780245);

        $mockLocalAuthorityB = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthorityB
            ->allows('getId')
            ->andReturn(824578);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthorityA);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn($mockLocalAuthorityB)
                    ->getMock()
            ]);

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCannotAccessTxcDocumentForDocumentWithNoAuthority(int $localAuthorityUserType): void
    {
        $mockLocalAuthority = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthority
            ->allows('getId')
            ->andReturn(780245);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthority);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([
                m::mock(Entity\Ebsr\TxcInbox::class)
                    ->shouldReceive('getLocalAuthority')
                    ->andReturn(null)
                    ->getMock()
            ]);

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @dataProvider localAuthorityTypeProvider
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function testLocalAuthorityUserCannotAccessDocumentIfNoTxcInboxRecordsFoundForDocumentId(int $localAuthorityUserType): void
    {
        $mockLocalAuthority = m::mock(Entity\Bus\LocalAuthority::class);
        $mockLocalAuthority
            ->allows('getId')
            ->andReturn(780245);

        $this->setupMockIdentity($localAuthorityUserType, $org = $this->getMockOrganisation(), 123456, $mockLocalAuthority);

        $this->mockRepo(Repository\Document::class)
            ->shouldReceive('fetchById')
            ->andReturn($document = $this->getMockDocument(false));
        $this->mockRepo(Repository\TxcInbox::class)
            ->shouldReceive('fetchLinkedToDocument')
            ->with(static::DOCUMENT_ID)
            ->andReturn([]);

        $this->assertFalse($this->sut->isValid(static::DOCUMENT_ID));
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    private function getMockDocument(bool $isExternal, int $createdById = 123456, m\MockInterface $relatedOrganisation = null): m\MockInterface
    {
        if ($relatedOrganisation === null) {
            $relatedOrganisation = m::mock(Entity\Organisation\Organisation::class);
            $relatedOrganisation->allows('getId')->andReturn(567890);
        }

        $mockDoc = m::mock(Entity\Doc\Document::class);

        $mockDoc->allows('getIsExternal')->andReturn($isExternal);
        $mockDoc->allows('getId')->andReturn(static::DOCUMENT_ID);
        $mockDoc->allows('getCreatedBy->getId')->andReturn($createdById);
        $mockDoc->allows('getRelatedOrganisation')->andReturn($relatedOrganisation);

        return $mockDoc;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    private function setupMockIdentity(int $userType, m\MockInterface $organisation = null, int $userId = 123456, m\MockInterface $localAuthority = null): void
    {
        $mockIdentity = m::mock(IdentityInterface::class);
        if ($organisation === null) {
            $mockIdentity->allows('getUser->getOrganisationUsers')->andReturn(new ArrayCollection([]));
            $mockIdentity->allows('getUser->getRelatedOrganisation')->andReturn(null);
        } else {
            $mockIdentity->allows('getUser->getOrganisationUsers')->andReturn(new ArrayCollection([$organisation]));
            $mockIdentity->allows('getUser->getRelatedOrganisation')->andReturn($organisation);
        }

        $mockIdentity->allows('getUser->isSystemUser')->andReturn($userType === static::IS_SYSTEM_USER);
        $mockIdentity->allows('getUser->getId')->andReturn($userId);

        if ($userType === static::IS_LOCAL_AUTHORITY_USER || $userType === static::IS_LOCAL_AUTHORITY_ADMIN) {
            $mockIdentity->allows('getUser->getLocalAuthority')->andReturn($localAuthority);
        } else {
            $mockIdentity->allows('getUser->getLocalAuthority')->andReturn(null);
        }

        $this->auth->allows('getIdentity')->andReturn($mockIdentity);

        switch ($userType) {
            case static::IS_SYSTEM_USER:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_INTERNAL_USER:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_EXTERNAL_USER:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_LOCAL_AUTHORITY_USER:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            case static::IS_LOCAL_AUTHORITY_ADMIN:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(true);
                break;
            case static::IS_TRANSPORT_MANAGER_USER:
                $this->auth->allows('isGranted')->with(Permission::TRANSPORT_MANAGER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)->andReturn(false);
                $this->auth->allows('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)->andReturn(false);
                break;
            default:
                /** @noinspection PhpUnhandledExceptionInspection */
                throw new Exception("Unexpected user type provided");
        }
    }

    /**
     * @return MockInterface|Entity\Organisation\Organisation
     */
    private function getMockOrganisation(): m\MockInterface
    {
        $mockOrganisation = m::mock(Entity\Organisation\Organisation::class);
        $mockOrganisation->allows('getId')->andReturn(567890);

        return $mockOrganisation;
    }

    public function localAuthorityTypeProvider(): array
    {
        return [
            'LOCAL AUTHORITY USER' => [static::IS_LOCAL_AUTHORITY_USER],
            'LOCAL AUTHORITY ADMIN' => [static::IS_LOCAL_AUTHORITY_ADMIN],
        ];
    }
}
