<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Metadata\Federation;

/**
 * Federation test
 */
class FederationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSsoUrl()
    {
        $federationMetadata = $this->getSut();
        $this->assertSame(
            'https://compliance-tool-reference.ida.digital.cabinet-office.gov.uk:443/SAML2/SSO',
            $federationMetadata->getSsoUrl()
        );
    }

    public function testGetSigningCertificate()
    {
        $federationMetadata = $this->getSut();
        $this->assertSame(
            'MIIERjCCAy6gAwIBAgIQduox6jrRdJ3RjXW4po1xKzANBgkqhkiG9w0BAQsFADBL
                        MQswCQYDVQQGEwJHQjEXMBUGA1UEChMOQ2FiaW5ldCBPZmZpY2UxDDAKBgNVBAsT
                        A0dEUzEVMBMGA1UEAxMMSURBUCBUZXN0IENBMB4XDTE1MDgyNzAwMDAwMFoXDTE3
                        MDgyNjIzNTk1OVowfTELMAkGA1UEBhMCR0IxDzANBgNVBAgTBkxvbmRvbjEPMA0G
                        A1UEBxMGTG9uZG9uMRcwFQYDVQQKFA5DYWJpbmV0IE9mZmljZTEMMAoGA1UECxQD
                        R0RTMSUwIwYDVQQDExxIVUIgU2lnbmluZyAoMjAxNTA4MjYxMjU0MjEpMIIBIjAN
                        BgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA469b0ts/BA+YufxHGJSaCEdqaNvp
                        EGAukMk0zgDwGiyA/Rx+urM7VOEQUFngD8urH5lGjfzf9yQLsQeq090HSJhs535A
                        QE4HJ2V0/vj8hCRS3qOp61cBqd+tlJpAypLTwJwNzvpJvVPoVHN5ste2AnPjxpfn
                        FozOH/+V7DeiA8iSSzQzqXktT0tILiubPmbCggjyDtf0e5FHdoeHwNhcOHZld7P5
                        CB7cKTS/nAoLokTaX1fPeoI/qSGoL+IikUYveACKPs0S+upB5J/IAaLjRbpWbS3x
                        fn8Gl1cA+mgeYz1qMtJmilla5dOcX1FrDa/DVL85I8P+pub5V/S/6L1AGQIDAQAB
                        o4HzMIHwMAwGA1UdEwEB/wQCMAAwVQYDVR0fBE4wTDBKoEigRoZEaHR0cDovL29u
                        c2l0ZWNybC50cnVzdHdpc2UuY29tL0NhYmluZXRPZmZpY2VJREFQVGVzdENBL0xh
                        dGVzdENSTC5jcmwwDgYDVR0PAQH/BAQDAgeAMB0GA1UdDgQWBBTFB8gNC1GV+Jas
                        m4CZQrccMIEz6zAfBgNVHSMEGDAWgBRqEU1kUN/eY29XoYgf+AOVDiIEtDA5Bggr
                        BgEFBQcBAQQtMCswKQYIKwYBBQUHMAGGHWh0dHA6Ly9zdGQtb2NzcC50cnVzdHdp
                        c2UuY29tMA0GCSqGSIb3DQEBCwUAA4IBAQAxWna2Nq9jcV4AiQvDXaEmdsAY24Pg
                        Du+gDyKm0KNtKSnKUzYhYqCyILd+yHPYg3zaiWKDjgkMMnLJeAktcUWk+pEgf5OF
                        QV8KDg/zlj/886P5pJzTqcESMnV4yHuTZUKeu/6DwI+0Ugv3J3JMyh+tBqFWzXDo
                        FFpsGjPTlYI3aNdZ0QNr3ElEKeI9W8sE/aCsnZDCkxQAphGyp4hVmuFGV+Vm7OUD
                        ULoa9oW6jiFzXNNRYvFEDTu5mSvMdlYq5XLZEKmK4MBCAJp/4LAs7mCirExUsfGo
                        +ObVs/MJBa0wWyiU/BfXbAXBlyURDzAd4eu/VSC1p4YQe+GY5Pui+GM8
                    ',
            $federationMetadata->getSigningCertificate()
        );
    }

    public function testGetSigningCertificateMissing()
    {
        $federationMetadata = $this->getSut('missing-cert-federation.xml');
        $this->expectException(\Dvsa\Olcs\GdsVerify\Exception::class, 'Federation signing certificate not found');
        $federationMetadata->getSigningCertificate();
    }

    /**
     * @return Federation
     */
    private function getSut($xmlFilename = 'federation.xml')
    {
        $xml = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $xmlFilename);

        return new Federation($xml);
    }
}
