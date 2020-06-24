<?php

/**
 * Validation Helper Test Case Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\ValidatorManager;

/**
 * Validation Helper Test Case Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ValidationHelperTestCaseTrait
{
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

    public function setUp(): void
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->auth = m::mock(AuthorizationService::class);
        $this->validatorManager = m::mock(ValidatorManager::class)->makePartial();

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->shouldReceive('getServiceLocator')->andReturnSelf();
        $sm->setService('RepositoryServiceManager', $this->repoManager);
        $sm->setService(AuthorizationService::class, $this->auth);
        $sm->setService('DomainValidatorManager', $this->validatorManager);
        $sm->setService('config', ['config']);

        $this->sut->createService($sm);
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
        if ($this->validatorManager->has($validator) === false) {
            $mockValidator = m::mock();
            $this->validatorManager->setService($validator, $mockValidator);
        } else {
            $mockValidator = $this->validatorManager->get($validator);
        }

        $mockValidator->shouldReceive('isValid')->withArgs($arguments)->andReturn($isValid);
    }
}
