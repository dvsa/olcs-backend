<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Metadata\Federation;

/**
 * Federation test
 */
class FederationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSsoUrl()
    {
        $federationMetadata = $this->getSut();
        $this->assertSame(
            'https://compliance-tool-reference.ida.digital.cabinet-office.gov.uk:443/SAML2/SSO',
            $federationMetadata->getSsoUrl()
        );
    }

    public function testGetSigningCertificateFormat()
    {
        $federationMetadata = $this->getSut();

        $actualCertLines = explode(PHP_EOL, $federationMetadata->getSigningCertificate());

        //don't test last line
        unset($actualCertLines[count($actualCertLines) - 1]);
        for ($x = 0; $x < count($actualCertLines); $x++) {
            echo $actualCertLines[$x];
            $this->assertEquals(64, strlen($actualCertLines[$x]), "is not 64 chars long");
        }
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
