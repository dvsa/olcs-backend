<?php
namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Service\GdsVerify;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\Log\Writer\Noop;

/**
 * GdsVerifyTest test
 */
class GdsVerifyTest extends TestCase
{
    public function testCreateService()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'enable_debug_log' => true,
                    'cache' => [
                        'adapter' => [
                            'name'    => 'memory',
                        ],
                    ],

                ]
            ]
        );

        $this->assertInstanceOf(GdsVerify::class, $sut);
    }

    public function testGetAuthenticationRequest()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'entity_identifier' => 'foo',
                    'msa_metadata_url' => 'http://meta.com',
                    'signature_key' => __DIR__ .'/signing.key'
                ]
            ]
        );

        $metadata = m::mock(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class);
        $metadata->shouldReceive('getSsoUrl')->with()->once()->andReturn('sso.url');
        $metadataLoader = m::mock(\Dvsa\Olcs\GdsVerify\Data\Loader::class);
        $metadataLoader->shouldReceive('loadMatchingServiceAdapterMetadata')->with('http://meta.com')
            ->once()->andReturn($metadata);

        $sut->setMetadataLoader($metadataLoader);

        $request = $sut->getAuthenticationRequest();

        $this->assertSame('sso.url', $request['url']);
        $this->assertArrayHasKey('samlRequest', $request);
    }

    public function testGetAttributesFromResponse()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'entity_identifier' => 'foo',
                    'msa_metadata_url' => 'http://meta.com',
                    'signature_key' => __DIR__ .'/signing.key',
                    'encryption_keys' => [__DIR__ .'/signing.key', __DIR__ .'/enc.key'],
                ]
            ]
        );

        $metadata = m::mock(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class);
        $metadata->shouldReceive('getSigningCertificate')->with()->once()->andReturn(
            'MIIDXTCCAkWgAwIBAgIJAOlKkJ8iwQH3MA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
            BAYTAkdCMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBX
            aWRnaXRzIFB0eSBMdGQwHhcNMTYwMzE3MTMxNTU1WhcNNDMwODAyMTMxNTU1WjBF
            MQswCQYDVQQGEwJHQjETMBEGA1UECAwKU29tZS1TdGF0ZTEhMB8GA1UECgwYSW50
            ZXJuZXQgV2lkZ2l0cyBQdHkgTHRkMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
            CgKCAQEAodvk319G7TFMR5NHExFCyLF82E2yLw22a3q1AughBHCwhliDcDEgakKu
            +qClwfampRcvxGQUViWQ7fiFAtX7U7dZ+gwvHA5QXpCoCTDjll67GgrLazuxxUMF
            IdzFXJlL6iLuKfb9rPw6xUzVwpXrWq8hRVNhsV1K6cg/0eZm4Abh83ISlxSbJIH7
            Eg/Ms93Y8KG6sw7qYdbtRd8dV7BOTczLmPLtwIiflR+beUNyLPeSvFwjSsSDadD4
            OvtRuhQrg/zX8+ZeIKxJSHQBTlwne6PGfmp9ZdcYxuZGVg84AwRDrqVk83hPACRU
            5YfhUKxeVUp3hka6A176pzxYoo/4nwIDAQABo1AwTjAdBgNVHQ4EFgQUGlYCLUl2
            v4CfX6DUqsbVs/hhdKswHwYDVR0jBBgwFoAUGlYCLUl2v4CfX6DUqsbVs/hhdKsw
            DAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAcg/DeAs4Qv7YLiZ4Q3Qe
            19HN7lhUoBARryCC2FBsVfKP5wVNEDGHTtdcVXdem83uDwKjq6XqoMx0Xzha3cE2
            lMCTqSnWeB4HH3OYLnDnS0a3DwEaIKa5sMCnr5eTr1InLy7mCos4XgCo8qACDmqO
            0kUkK2LSKiNGk3hm3mz+PM9nAETdFXHy9bWNHnTQ4xHfBFQSBCN1oFQFY0pErakj
            TwEb7qrOF9mj4toTXouxSZpsWrOAw4q5EC+wiKwNx149SG7VLvc498VLdOOkfSHG
            Ib8/+KdN84WLI/x0/72eRR+DhBMrtCT6DR00sBK3B/hLUSxIDGUXdRedUNr/51uC
            6w=='
        );
        $metadataLoader = m::mock(\Dvsa\Olcs\GdsVerify\Data\Loader::class);
        $metadataLoader->shouldReceive('loadMatchingServiceAdapterMetadata')->with('http://meta.com')
            ->once()->andReturn($metadata);

        $sut->setMetadataLoader($metadataLoader);

        $samlResponse = file_get_contents(__DIR__ .'/saml-response.txt');
        $request = @$sut->getAttributesFromResponse($samlResponse);

        $this->assertSame(
            [
                'middlenameverified' => 'false',
                'dateofbirth' => '1977-07-21 11:15:34AM',
                'firstnameverified' => 'false',
                'firstname' => 'Screaming',
                'surnameverified' => 'false',
                'dateofbirthverified' => 'false',
                'middlename' => 'Jay',
                'surname' => 'Hawkins',
            ],
            $request->getArrayCopy()
        );
    }

    public function testGetAttributesFromResponseCannotDecrypt()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'entity_identifier' => 'foo',
                    'msa_metadata_url' => 'http://meta.com',
                    'signature_key' => __DIR__ .'/signing.key',
                    'encryption_keys' => [__DIR__ .'/signing.key'],
                ]
            ]
        );

        $metadata = m::mock(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class);
        $metadata->shouldReceive('getSigningCertificate')->with()->once()->andReturn(
            'MIIDXTCCAkWgAwIBAgIJAOlKkJ8iwQH3MA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
            BAYTAkdCMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBX
            aWRnaXRzIFB0eSBMdGQwHhcNMTYwMzE3MTMxNTU1WhcNNDMwODAyMTMxNTU1WjBF
            MQswCQYDVQQGEwJHQjETMBEGA1UECAwKU29tZS1TdGF0ZTEhMB8GA1UECgwYSW50
            ZXJuZXQgV2lkZ2l0cyBQdHkgTHRkMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
            CgKCAQEAodvk319G7TFMR5NHExFCyLF82E2yLw22a3q1AughBHCwhliDcDEgakKu
            +qClwfampRcvxGQUViWQ7fiFAtX7U7dZ+gwvHA5QXpCoCTDjll67GgrLazuxxUMF
            IdzFXJlL6iLuKfb9rPw6xUzVwpXrWq8hRVNhsV1K6cg/0eZm4Abh83ISlxSbJIH7
            Eg/Ms93Y8KG6sw7qYdbtRd8dV7BOTczLmPLtwIiflR+beUNyLPeSvFwjSsSDadD4
            OvtRuhQrg/zX8+ZeIKxJSHQBTlwne6PGfmp9ZdcYxuZGVg84AwRDrqVk83hPACRU
            5YfhUKxeVUp3hka6A176pzxYoo/4nwIDAQABo1AwTjAdBgNVHQ4EFgQUGlYCLUl2
            v4CfX6DUqsbVs/hhdKswHwYDVR0jBBgwFoAUGlYCLUl2v4CfX6DUqsbVs/hhdKsw
            DAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAcg/DeAs4Qv7YLiZ4Q3Qe
            19HN7lhUoBARryCC2FBsVfKP5wVNEDGHTtdcVXdem83uDwKjq6XqoMx0Xzha3cE2
            lMCTqSnWeB4HH3OYLnDnS0a3DwEaIKa5sMCnr5eTr1InLy7mCos4XgCo8qACDmqO
            0kUkK2LSKiNGk3hm3mz+PM9nAETdFXHy9bWNHnTQ4xHfBFQSBCN1oFQFY0pErakj
            TwEb7qrOF9mj4toTXouxSZpsWrOAw4q5EC+wiKwNx149SG7VLvc498VLdOOkfSHG
            Ib8/+KdN84WLI/x0/72eRR+DhBMrtCT6DR00sBK3B/hLUSxIDGUXdRedUNr/51uC
            6w=='
        );
        $metadataLoader = m::mock(\Dvsa\Olcs\GdsVerify\Data\Loader::class);
        $metadataLoader->shouldReceive('loadMatchingServiceAdapterMetadata')->with('http://meta.com')
            ->once()->andReturn($metadata);
        $sut->setMetadataLoader($metadataLoader);

        $samlResponse = file_get_contents(__DIR__ .'/saml-response.txt');

        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Cannot decrypt the SAML Assertion');
        $sut->getAttributesFromResponse($samlResponse);
    }

    public function testGetAttributesFromResponseSignatureError()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'entity_identifier' => 'foo',
                    'msa_metadata_url' => 'http://meta.com',
                    'signature_key' => __DIR__ .'/signing.key',
                    'encryption_keys' => [__DIR__ .'/enc.key'],
                ]
            ]
        );

        $metadata = m::mock(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class);
        $metadata->shouldReceive('getSigningCertificate')->with()->once()->andReturn(
            'MIIDXTCCAkWgAwIBAgIJAOdbvV4W2wl2MA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
            BAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBX
            aWRnaXRzIFB0eSBMdGQwHhcNMTYwMzE3MTMxNzM0WhcNNDMwODAyMTMxNzM0WjBF
            MQswCQYDVQQGEwJBVTETMBEGA1UECAwKU29tZS1TdGF0ZTEhMB8GA1UECgwYSW50
            ZXJuZXQgV2lkZ2l0cyBQdHkgTHRkMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
            CgKCAQEA55s1nQZdqDIf46hP8dfnsNYh5laVLIbaNobmPOzmRzGgyus5kYNmr9Fe
            9caoREykkzyu2Z6vsEZ6VH/GMk9+9w0C7fCX3HB5sWZU+5Mq8q9fkCvu0d88TclA
            soGHkzl/mWy8ES8eunL8Zop0iiIwLZjpTWnAuPBLNtu+hBqPqPX41+F1EEsmJiMo
            Wuj8oumxj7ds7k5hlg0PH/IGOV6hS0Yy8Pi3N5glnM/Xi0M/bLUmbQXJxMPMIsp/
            s/8DvyYl7l5T0l+CO+2O/5U7zXQD455Vtwcid8o3wzWty34KzWadmE0Zv55KXx3e
            a6ZWTZQ2ft0BAfucogF7CQ+JmNNkAwIDAQABo1AwTjAdBgNVHQ4EFgQUOSYsLqnP
            JkamHA8LR0KfGAAxcK0wHwYDVR0jBBgwFoAUOSYsLqnPJkamHA8LR0KfGAAxcK0w
            DAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAjdDYrz90NZlccXMduffX
            c0k/hUfhWiayEVjVQ7dhei5uSZpELPrMLbNWq1WAy5hDT6Y8KS3bX7MPC+rmjWu4
            3yoJXl3Cgk713P9wjfjMRfsB1IEzMbMWAHR5tQo/YmUHJrXhjuYhTNVZDl1RE6vH
            3yknEVK+oqTsyFw6Oy1E0o+EMDojNbhbJs17k5iLGRxZdzzAUjDupDZ+0OZj25Fj
            7KWNQui0Uc7Yuu+mP6s1yCNpJS8fJj36eVABDHXsATLeSjsllPCvLiRjAuKJKa5u
            xb1knbCw6oSTSglUlRChYh8rPmdnPuM82l9mYW2GROZX37u8OpfmRGZdEiFssZPD
            SA=='
        );

        $metadataLoader = m::mock(\Dvsa\Olcs\GdsVerify\Data\Loader::class);
        $metadataLoader->shouldReceive('loadMatchingServiceAdapterMetadata')->with('http://meta.com')
            ->once()->andReturn($metadata);

        $sut->setMetadataLoader($metadataLoader);

        $samlResponse = file_get_contents(__DIR__ .'/saml-response.txt');

        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'SAML Assertion signature error');
        @$sut->getAttributesFromResponse($samlResponse);
    }

    public function testGetAttributesFromResponseNotSuccessMessage()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'entity_identifier' => 'foo',
                    'msa_metadata_url' => 'http://meta.com',
                    'signature_key' => __DIR__ .'/signing.key',
                    'encryption_keys' => [__DIR__ .'/enc.key'],
                ]
            ]
        );

        $samlResponse = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c2FtbDJwOlJlc3BvbnNlIHhtbG5zOnNhbWwycD0id
            XJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnByb3RvY29sIiB4bWxuczp4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1
            hLWluc3RhbmNlIiBEZXN0aW5hdGlvbj0iaHR0cDovL29sY3Mtc2VsZnNlcnZlLm9sY3MuZ292LnVrL3ZlcmlmeS9wcm9jZXNzLXJlc3Bvb
            nNlIiBJRD0iX2I1ZTkwYjAwLWI3YzUtNDFlYy1iOTU3LTI0OWZlMTNhZjUxNyIgSW5SZXNwb25zZVRvPSJHZHNWZXJpZnkxNDkxNDk4MjY
            2LjQwNjgiIElzc3VlSW5zdGFudD0iMjAxNy0wNC0wNlQxNzowNDozNS40MTlaIiBWZXJzaW9uPSIyLjAiIHhzaTp0eXBlPSJzYW1sMnA6U
            mVzcG9uc2VUeXBlIj48c2FtbDJwOlN0YXR1cyB4c2k6dHlwZT0ic2FtbDJwOlN0YXR1c1R5cGUiPjxzYW1sMnA6U3RhdHVzQ29kZSBWYWx
            1ZT0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnN0YXR1czpSZXNwb25kZXIiIHhzaTp0eXBlPSJzYW1sMnA6U3RhdHVzQ29kZVR5c
            GUiPjxzYW1sMnA6U3RhdHVzQ29kZSBWYWx1ZT0idXJuOnVrOmdvdjpjYWJpbmV0LW9mZmljZTp0YzpzYW1sOnN0YXR1c2NvZGU6bm8tbWF
            0Y2giIHhzaTp0eXBlPSJzYW1sMnA6U3RhdHVzQ29kZVR5cGUiLz48L3NhbWwycDpTdGF0dXNDb2RlPjwvc2FtbDJwOlN0YXR1cz48L3Nhb
            WwycDpSZXNwb25zZT4=';

        $request = $sut->getAttributesFromResponse($samlResponse);
        $this->assertSame([], $request->getArrayCopy());
    }

    public function testGetSignatureKeyMissing()
    {
        $sut = new GdsVerify();
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Signature key is not set');
        $sut->getSignatureKey();
    }

    public function testSetGetSignatureKey()
    {
        $key = m::mock(\RobRichards\XMLSecLibs\XMLSecurityKey::class);

        $sut = new GdsVerify();
        $sut->setSignatureKey($key);
        $this->assertSame($key, $sut->getSignatureKey());
    }

    public function testLoadSignatureKeyFileNotExists()
    {
        $sut = new GdsVerify();
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Signature key file not found');
        $sut->loadSignatureKey('a-file-that-does-not-exist.txt');
    }

    public function testSetGetEncryptionKey()
    {
        $key = m::mock(\RobRichards\XMLSecLibs\XMLSecurityKey::class);

        $sut = new GdsVerify();
        $sut->setEncryptionKey($key, 2);
        $this->assertSame($key, $sut->getEncryptionKey(2));
    }

    public function testLoadEncryptionKeyFileNotExists()
    {
        $sut = new GdsVerify();
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Encryption key file not found');
        $sut->loadEncryptionKey('a-file-that-does-not-exist.txt');
    }

    public function testGetMatchingServiceAdapterMetadataMissing()
    {
        $sut = new GdsVerify();
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'MatchingServiceAdapter metadata not set');
        $sut->getMatchingServiceAdapterMetadata();
    }

    public function testGetMatchingServiceAdapterMetadataLoadAndCache()
    {
        $sut = $this->getSut(
            [
                'gds_verify' => [
                    'msa_metadata_url' => 'http://meta.com',
                ]
            ]
        );

        $metadata = m::mock(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class);
        $metadataLoader = m::mock(\Dvsa\Olcs\GdsVerify\Data\Loader::class);
        $metadataLoader->shouldReceive('loadMatchingServiceAdapterMetadata')->with('http://meta.com')
            ->once()->andReturn($metadata);
        $sut->setMetadataLoader($metadataLoader);

        $metadata1 = $sut->getMatchingServiceAdapterMetadata();
        $metadata2 = $sut->getMatchingServiceAdapterMetadata();

        $this->assertSame($metadata, $metadata1);
        $this->assertSame($metadata, $metadata2);
    }

    public function testGetEntityIdentifierMissing()
    {
        $sut = new GdsVerify();
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Entity identifier is not specified');
        $sut->getEntityIdentifier();
    }

    private function getSut(array $config = [])
    {
        $writer = new Noop();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $serviceLocator = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('get')->with('config')->once()->andReturn($config);
        $serviceLocator->shouldReceive('get')->with('logger')->once()->andReturn($logger);
        $serviceLocator->shouldReceive('has')->with(\Dvsa\Olcs\Utils\Client\HttpExternalClientFactory::class)
            ->once()->andReturn(true);
        $serviceLocator->shouldReceive('get')->with(\Dvsa\Olcs\Utils\Client\HttpExternalClientFactory::class)
            ->once()->andReturn(m::mock(\Zend\Http\Client::class));

        $factory = new GdsVerify();

        return $factory->createService($serviceLocator);
    }
}
