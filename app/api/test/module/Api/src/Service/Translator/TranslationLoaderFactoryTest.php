<?php

namespace Dvsa\OlcsTest\Api\Service\Translator;

use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoaderFactory;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * TranslationLoaderFactoryTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockCache = m::mock(CacheEncryption::class);
        $mockRepo = m::mock(TranslationKeyText::class);
        $mockRepoManager = m::mock(RepositoryServiceManager::class);
        $mockRepoManager->expects('get')->with('TranslationKeyText')->andReturn($mockRepo);

        $parentSl = m::mock(ServiceLocatorInterface::class);
        $parentSl->expects('get')->with('RepositoryServiceManager')->andReturn($mockRepoManager);
        $parentSl->expects('get')->with(CacheEncryption::class)->andReturn($mockCache);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('getServiceLocator')->withNoArgs()->andReturn($parentSl);

        $sut = new TranslationLoaderFactory();
        $service = $sut->createService($mockSl);

        self::assertInstanceOf(TranslationLoader::class, $service);
    }
}
