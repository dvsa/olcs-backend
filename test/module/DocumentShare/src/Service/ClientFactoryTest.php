<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\DocumentShare\Service\ClientFactory;
use Dvsa\Olcs\DocumentShare\Service\DocManClient;
use Dvsa\Olcs\DocumentShare\Service\WebDavClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Client Factory Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ClientFactoryTest extends MockeryTestCase
{
    /**
     * @dataProvider provideSetOptions
     *
     * @param $config
     * @param $expected
     */
    public function testGetOptions($config, $expected)
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->once()->with('Configuration')->andReturn($config);

        $sut = new ClientFactory();

        if ($expected instanceof \Exception) {
            $passed = false;
            try {
                $sut->getOptions($mockSl, 'testkey');
            } catch (\Exception $e) {
                if ($e->getMessage() == $expected->getMessage() && get_class($e) == get_class($expected)) {
                    $passed = true;
                }
            }

            $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match expected value');
        } else {
            $data = $sut->getOptions($mockSl, 'testkey');
            $this->assertEquals($expected, $data);
        }
    }

    public function provideSetOptions()
    {
        return array(
            array(array(), new \RuntimeException('Options could not be found in "document_share.testkey".')),
            array(
                array('document_share' => array()),
                new \RuntimeException('Options could not be found in "document_share.testkey".')
            ),
            array(
                array('document_share' => array('testkey' => array('foo' => 'bar'))),
                array('foo' => 'bar')
            )
        );
    }

    /**
     * @dataProvider provideCreateService
     *
     * @param $config
     * @param $expected
     */
    public function testCreateService($config, $expected, $client)
    {
        $sut = new ClientFactory();

        $mockSl = m::mock(ServiceLocatorInterface::class);

        $mockUser = m::mock(User::class)
            ->shouldReceive('getOstype')
            ->andReturn($client)->getMock();

        $authService = m::mock(AuthorizationService::class)
            ->shouldReceive('getIdentity')->once()
            ->andReturn(
                m::mock(IdentityInterface::class)->shouldReceive('getUser')->once()
                    ->andReturn($mockUser)->getMock()
            )->getMock();

        $mockSl->shouldReceive('get')
            ->once()
            ->with(AuthorizationService::class)
            ->andReturn(
                $authService
            )->getMock();
        $mockSl->shouldReceive('get')->once()->with('Configuration')->andReturn($config);
        if ($expected instanceof \Exception) {
            $passed = false;
            try {
                $service = $sut->createService($mockSl);
            } catch (\Exception $e) {
                if ($e->getMessage() === $expected->getMessage() && get_class($e) === get_class($expected)) {
                    $passed = true;
                }
            }

            $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match expected value');
        } else {
            $service = $sut->createService($mockSl);

            if ($client === User::USER_OS_TYPE_WINDOWS_7) {
                $this->assertInstanceOf(DocManClient::class, $service);
                $this->assertInstanceOf('\Zend\Http\Client', $service->getHttpClient());
                $this->assertEquals($config['document_share']['client']['workspace'], $service->getWorkspace());
                $this->assertEquals($config['document_share']['client']['baseuri'], $service->getBaseUri());
                if (isset($config['document_share']['client']['uuid'])) {
                    $this->assertEquals(
                        $config['document_share']['client']['uuid'],
                        $service->getUuid()
                    );
                }
            } else {
                $this->assertInstanceOf(WebDavClient::class, $service);
            }
        }
    }

    public function provideCreateService()
    {
        $configMissingBaseUri = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'workspace' => 'test'
                ]
            ],
        ];

        $configMissingWorkspace = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'baseuri' => 'http://testdocument_share'
                ]
            ],
        ];

        $config = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'baseuri' => 'http://testdocument_share',
                    'workspace' => 'test'
                ]
            ],
        ];

        $configWebDavMissingUsername = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'webdav_baseuri' => 'http://testdocument_share',
                    'workspace' => 'test',
                    'password' => 'test'
                ]
            ]
        ];

        $configWebDavMissingPassword = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'webdav_baseuri' => 'http://testdocument_share',
                    'workspace' => 'test',
                    'username' => 'test'
                ]
            ]
        ];

        $webDavConfig = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'baseuri' => 'http://testdocument_share',
                    'workspace' => 'testwebdav',
                    'username' => 'testwebdav',
                    'password' => 'ttestwebdavest',
                    'webdav_baseuri' => 'http://testdocument_share',
                    'uuid' => 'u1234'
                ]
            ]
        ];

        $webDavConfigMissingBaseUri = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'workspace' => 'testwebdav',
                    'username' => 'testwebdav',
                    'password' => 'ttestwebdavest',
                    'uuid' => 'u1234'
                ]
            ]
        ];

        $webDavConfigMissingWorkspace = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'username' => 'testwebdav',
                    'password' => 'ttestwebdavest',
                    'webdav_baseuri' => 'http://testdocument_share',
                    'uuid' => 'u1234'
                ]
            ]
        ];

        $configWithUuid = [
            'document_share' => [
                'http' => [],
                'client' => [
                    'baseuri' => 'http://testdocument_share',
                    'workspace' => 'test',
                    'uuid' => 'u1234'
                ]
            ],
        ];

        return [
            "missingBaseUri" => [
                $configMissingBaseUri,
                new \RuntimeException('Missing required option document_share.client.baseuri'),
                User::USER_OS_TYPE_WINDOWS_7
            ],
            "missingWorkspace" => [
                $configMissingWorkspace,
                new \RuntimeException('Missing required option document_share.client.workspace'),
                User::USER_OS_TYPE_WINDOWS_7
            ],
            "goodDocManConfig" => [
                $config,
                null,
                User::USER_OS_TYPE_WINDOWS_7
            ],
            "docManwithUuId" => [
                $configWithUuid,
                null,
                User::USER_OS_TYPE_WINDOWS_7
            ],
            "goodWebDavConfig" => [
                $webDavConfig,
                null,
                User::USER_OS_TYPE_WINDOWS_10
            ],

            "WebDavConfigMissingUsername" => [
                $configWebDavMissingUsername,
                new \RuntimeException('Missing required option document_share.client.username'),
                User::USER_OS_TYPE_WINDOWS_10

            ],
            "WebDavConfigMissingPassword" => [
                $configWebDavMissingPassword,
                new \RuntimeException('Missing required option document_share.client.password'),
                User::USER_OS_TYPE_WINDOWS_10

            ],
            "WebDavConfigMissingWorkspace" => [
                $webDavConfigMissingWorkspace,
                new \RuntimeException('Missing required option document_share.client.workspace'),
                User::USER_OS_TYPE_WINDOWS_10

            ],
            "WebDavConfigMissingPWebDavBaseUri" => [
                $webDavConfigMissingBaseUri,
                new \RuntimeException('Missing required option document_share.client.webdav_baseuri'),
                User::USER_OS_TYPE_WINDOWS_10

            ]

        ];
    }
}
