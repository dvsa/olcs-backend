<?php

namespace Dvsa\Olcs\GdsVerify\Data\Metadata;

use Dvsa\Olcs\GdsVerify\Exception;

/**
 * Class MatchingServiceAdapter
 */
class MatchingServiceAdapter
{
    /**
     * @var \SAML2\XML\md\EntitiesDescriptor
     */
    private $metadataDocument;

    /**
     * MatchingServiceAdapter constructor.
     *
     * @param string $xml XML
     *
     * @throws Exception
     */
    public function __construct($xml)
    {
        $document = new \DOMDocument();
        $document->loadXML($xml);

        $element = $document->documentElement;

        $this->metadataDocument = new \SAML2\XML\md\EntitiesDescriptor($element);
    }

    /**
     * Get the base64 encoded signing certificate
     *
     * @return string base64 encoded
     * @throws Exception
     */
    public function getSigningCertificate()
    {
        try {
            /** @var \SAML2\XML\md\IDPSSODescriptor $roleDescriptor */
            $roleDescriptor = $this->metadataDocument->children[0]->RoleDescriptor[0];

            /** @var \SAML2\XML\md\KeyDescriptor $keyDescriptor */
            foreach ($roleDescriptor->KeyDescriptor as $keyDescriptor) {
                if ($keyDescriptor->use === 'signing') {
                    return trim($keyDescriptor->KeyInfo->info[1]->data[0]->certificate);
                }
            }
        } catch (\Exception $e) {
            throw new Exception('Matching Service Adapter signing certificate not found : ' .$e->getMessage());
        }

        throw new Exception('Matching Service Adapter signing certificate not found');
    }

    /**
     * Get the SSO Url
     *
     * @return string
     * @throws Exception
     */
    public function getSsoUrl()
    {
        try {
            /** @var \SAML2\XML\md\IDPSSODescriptor $roleDescriptor */
            foreach ($this->metadataDocument->children[0]->RoleDescriptor as $roleDescriptor) {
                if ($roleDescriptor instanceof \SAML2\XML\md\IDPSSODescriptor) {
                    return trim($roleDescriptor->SingleSignOnService[0]->Location);
                }
            }
        } catch (\Exception $e) {
            throw new Exception('SSO URL not found in metadata : '. $e->getMessage());
        }

        throw new Exception('SSO URL not found in metadata');
    }
}
