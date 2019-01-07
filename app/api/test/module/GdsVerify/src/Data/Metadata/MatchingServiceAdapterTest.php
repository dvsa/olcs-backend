<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter;

/**
 * MatchingServiceAdapter test
 */
class MatchingServiceAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSigningCertificate()
    {
        $msaMetadata = $this->getSut();
        $this->assertSame(
            'MIIEnDCCA4SgAwIBAgIQCwWULb4gaQGJgUzy0xEVjjANBgkqhkiG9w0BAQsFADBZ
    MQswCQYDVQQGEwJHQjEXMBUGA1UEChMOQ2FiaW5ldCBPZmZpY2UxDDAKBgNVBAsT
    A0dEUzEjMCEGA1UEAxMaSURBUCBSZWx5aW5nIFBhcnR5IFRlc3QgQ0EwHhcNMTcw
    MjAyMDAwMDAwWhcNMTgwMjAyMjM1OTU5WjCBuDELMAkGA1UEBhMCR0IxDjAMBgNV
    BAgTBUxlZWRzMQ4wDAYDVQQHEwVMZWVkczEsMCoGA1UEChQjRHJpdmVyIGFuZCBW
    ZWhpY2xlIFN0YW5kYXJkcyBBZ2VuY3kxIzAhBgNVBAsUGlZlaGljbGUgT3BlcmF0
    b3IgTGljZW5zaW5nMTYwNAYDVQQDEy1hcGkub2xjcy5xYS5kZXYtZHZzYWNsb3Vk
    LnVrIFNBTUwgU2lnbmluZyAwMDIwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEK
    AoIBAQDfOpXn5IHcZlUBBCfyXUSTSg8h12WLnX2aSSyhrAoO8DpNviYnzKEckIu3
    ISEDc6lanHQO/Elgc2vUHSe6ZlWkZyOBnx36OksanmTXMmaOFJj9LyMch5iNeyyD
    K8uaYM9w03PTAIHxb+F2g1i1bhO07fv69TVZbZqyTWHtZJ8V6a7XLo4GgbYzeSLK
    1PilTP6YWhcyQyJVkz6Tjt1tscLywzSnW3RF6bZYhGON25RW0Mxz9T5e7hcPy5Dr
    b6fklZczZcCRdJLPoYkVCURNUl2Qmk9pa7KAiWZPFRrJVoYOCWm0ERr3qgbLTcit
    zy3dC9T8ZtuRc4QGEAy6vxc8YnClAgMBAAGjgf8wgfwwDAYDVR0TAQH/BAIwADBh
    BgNVHR8EWjBYMFagVKBShlBodHRwOi8vb25zaXRlY3JsLnRydXN0d2lzZS5jb20v
    Q2FiaW5ldE9mZmljZUlEQVBSZWx5aW5nUGFydHlUZXN0Q0EvTGF0ZXN0Q1JMLmNy
    bDAOBgNVHQ8BAf8EBAMCB4AwHQYDVR0OBBYEFFSDbWvnEmwtBsxiatpOlx/ieINL
    MB8GA1UdIwQYMBaAFN3k9V0aCg860UgiHk7A+G429sUlMDkGCCsGAQUFBwEBBC0w
    KzApBggrBgEFBQcwAYYdaHR0cDovL3N0ZC1vY3NwLnRydXN0d2lzZS5jb20wDQYJ
    KoZIhvcNAQELBQADggEBAGSSd7KfMBIpUn/1xS+DitN9XsldKwob5K/OHV7tJAno
    YFIp+yQWWdKtEDRdXo87hdLFLKVpq4zIbK2PDumdkgJQD4EPwfEASsxoabdej47G
    nMAPXJAg6VjWO9C4IdUSvpeTGFX3Qk5dfsWawaQwVgbr7rhTkJK7uph3b2v2GW+7
    AqjQwz1CHo8vXHpnrxqZvHSyciAahk8VEswQkwiwCi8+sJ6nFgmEq99bMeAw8lso
    aD23AFgWTseujSVbVZ2NW0zfBTGZZk2CL542O7HI70hILDAJDvlfoFWy0qSxAQrK
    t+Cqdi5wrgzmDGGg3g98ZbLlT/8fTnnfM/nfB1mFK+g=',
            trim($msaMetadata->getSigningCertificate())
        );
    }

    public function testGetSigningCertificateGeneralError()
    {
        $metadata = $this->getSut('msa-meta-missing-cert1.xml');
        $this->expectException(
            \Dvsa\Olcs\GdsVerify\Exception::class,
            'Matching Service Adapter signing certificate not found : Undefined offset: 0'
        );
        $metadata->getSigningCertificate();
    }

    public function testGetSigningCertificateNoSiginingCerts()
    {
        $metadata = $this->getSut('msa-meta-missing-cert2.xml');
        $this->expectException(
            \Dvsa\Olcs\GdsVerify\Exception::class,
            'Matching Service Adapter signing certificate not found'
        );
        $metadata->getSigningCertificate();
    }

    public function testGetSsoUrl()
    {
        $msaMetadata = $this->getSut();
        $this->assertSame(
            'https://www.integration.signin.service.gov.uk/SAML2/SSO',
            $msaMetadata->getSsoUrl()
        );
    }

    public function testGetSsoUrlGeneralError()
    {
        $metadata = $this->getSut('msa-meta-missing-sso2.xml');
        $this->expectException(
            \Dvsa\Olcs\GdsVerify\Exception::class,
            'SSO URL not found in metadata : Undefined offset: 0'
        );
        $metadata->getSsoUrl();
    }

    public function testGetSsoUrlMissing()
    {
        $metadata = $this->getSut('msa-meta-missing-sso1.xml');
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'SSO URL not found in metadata');
        $metadata->getSsoUrl();
    }

    /**
     * @return MatchingServiceAdapter
     */
    private function getSut($xmlFilename = 'msa-meta.xml')
    {
        $xml = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $xmlFilename);

        return new MatchingServiceAdapter($xml);
    }
}
