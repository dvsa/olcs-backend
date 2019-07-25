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

        //don't test last line or first line cos they are comments
        unset($actualCertLines[0]);
        unset($actualCertLines[count($actualCertLines) - 1]);

        // then the last line maybe not 64 chars so don't check it equals 64 just less than
        for ($x = 1; $x < count($actualCertLines); $x++) {
            if ($x == count($actualCertLines)-1) {
                $this->assertLessThanOrEqual(64, strlen($actualCertLines[$x]));
                break;
            }
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
