<?php

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers;

use Composer\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\Validation\Validators\IsOwner;
use Dvsa\Olcs\Api\Domain\ValidatorManager;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use ZfcRbac\Service\AuthorizationService;

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractHandlerTestCase extends MockeryTestCase
{
    /**
     * @var AbstractHandler
     */
    protected $sut;

    /**
     * @var RepositoryServiceManager
     */
    protected $repoManager;

    /**
     * @var ValidatorManager
     */
    protected $validatorManager;

    /**
     * @var AuthorizationService
     */
    protected $auth;

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->auth = m::mock(AuthorizationService::class);
        $this->validatorManager = m::mock(ValidatorManager::class)->makePartial();

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->shouldReceive('getServiceLocator')->andReturnSelf();
        $sm->setService('RepositoryServiceManager', $this->repoManager);
        $sm->setService(AuthorizationService::class, $this->auth);
        $sm->setService('DomainValidatorManager', $this->validatorManager);

        $this->sut->createService($sm);
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
        $this->setExpectedException(\RuntimeException::class);

        $user = $this->mockUser();

        $organisationProvider = m::mock(OrganisationProviderInterface::class);

        $this->sut->isOwner($organisationProvider, $user);
    }

    public function mockRepo($repoName)
    {
        $mockRepo = m::mock(RepositoryInterface::class);
        $this->repoManager->shouldReceive('get')->with($repoName)->andReturn($mockRepo);

        return $mockRepo;
    }

    public function mockUser()
    {
        $user = m::mock(User::class);
        $this->auth->shouldReceive('getIdentity->getUser')->andReturn($user);

        return $user;
    }

    public function setIsGranted($permission, $return, $context = null)
    {
        $this->auth->shouldReceive('isGranted')->with($permission, $context)->once()->andReturn($return);
    }

    public function setIsValid($validator, $arguments, $isValid = true)
    {
        $mockValidator = m::mock();
        $this->validatorManager->setService($validator, $mockValidator);

        $mockValidator->shouldReceive('isValid')
            ->withArgs($arguments)
            ->andReturn($isValid);
    }
}
