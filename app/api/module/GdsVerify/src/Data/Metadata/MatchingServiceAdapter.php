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

        $element = $document->documentElement;

        if ($element->tagName === 'md:EntitiesDescriptor') {
            $element = $document->documentElement->childNodes[1];
        }

        if ($element->tagName !== 'md:EntityDescriptor') {
            throw new \Exception('Cannot find md:EntityDescriptor element in metadata');
        }

        $this->metadataDocument = new \SAML2\XML\md\EntityDescriptor($element);
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

    /**
     * Get the SSO Url
     *
     * @return string
     * @throws Exception
     */
    public function getSsoUrl()
    {
        /** @var \SAML2\XML\md\IDPSSODescriptor $roleDescriptor */
        foreach ($this->metadataDocument->RoleDescriptor as $roleDescriptor) {
            if ($roleDescriptor instanceof \SAML2\XML\md\IDPSSODescriptor) {
                return $roleDescriptor->SingleSignOnService[0]->Location;
            }
        }

        throw new Exception('SSO URL not found in metadata');
    }
}
