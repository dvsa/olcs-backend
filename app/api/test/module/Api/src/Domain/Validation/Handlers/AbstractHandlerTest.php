<?php

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\Modify;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceManager;

/**
 * Abstract Handler Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractHandlerTest extends AbstractHandlerTestCase
{
    /**
     * @var Modify
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new Modify();

        parent::setUp();
    }

    public function testGetValidatorManager()
    {
        $this->assertSame($this->validatorManager, $this->sut->getValidatorManager());
    }

    public function testGetRepo()
    {
        $repo = $this->mockRepo('SomeRepo');

        $this->assertSame($repo, $this->sut->getRepo('SomeRepo'));
    }

    public function testCall()
    {
        $user = $this->mockUser();

        $organisationProvider = m::mock(OrganisationProviderInterface::class);

        $this->setIsValid('isOwner', [$organisationProvider, $user], true);

        $this->assertEquals(true, $this->sut->isOwner($organisationProvider, $user));
    }

    public function testCallMissing()
    {
        $this->expectException(\RuntimeException::class);

        $user = $this->mockUser();

        $organisationProvider = m::mock(OrganisationProviderInterface::class);

        $this->sut->isOwner($organisationProvider, $user);
    }
}
