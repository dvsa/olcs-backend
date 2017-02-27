<?php

namespace Dvsa\Olcs\GdsVerify\Data\Metadata;

use Dvsa\Olcs\GdsVerify\Exception;

/**
 * Class MatchingServiceAdapter
 */
class MatchingServiceAdapter
{
    /**
     * @var \SAML2\XML\md\EntityDescriptor
     */
    private $metadataDocument;

    /**
     * MatchingServiceAdapter constructor.
     *
     * @param string $xml XML
     */
    public function __construct($xml)
    {
        $document = new \DOMDocument();
        $document->loadXML($xml);

        $this->metadataDocument = new \SAML2\XML\md\EntityDescriptor($document->documentElement);
    }

    /**
     * Get the base64 encoded signing certificate
     *
     * @return string base64 encoded
     * @throws Exception
     */
    public function getSigningCertificate()
    {
        /** @var \SAML2\XML\md\IDPSSODescriptor $roleDescriptor */
        $roleDescriptor = $this->metadataDocument->RoleDescriptor[0];

        /** @var \SAML2\XML\md\KeyDescriptor $keyDescriptor */
        foreach ($roleDescriptor->KeyDescriptor as $keyDescriptor) {
            if ($keyDescriptor->use === 'signing') {
                return $keyDescriptor->KeyInfo->info[1]->data[0]->certificate;
            }
        }

        throw new Exception('Matching Service Adapter signing certificate not found');
    }
}
