<?php

namespace Dvsa\Olcs\GdsVerify\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use \RobRichards\XMLSecLibs;
use Dvsa\Olcs\GdsVerify\Data;

/**
 * Class GdsVerify
 *
 * @todo This class needs unit testing !!!
 * NOTE php mcrypt extension needs installing, for this to work
 *
 * @package Dvsa\Olcs\GdsVerify\Service
 */
class GdsVerify implements \Zend\ServiceManager\FactoryInterface
{
    const CONFIG_KEY = 'gds_verify';

    /**
     * The Identifier used in SAML requests to Verify
     *
     * @var string
     */
    private $entityIdentifier;

    /**
     * @var XMLSecLibs\XMLSecurityKey
     */
    private $signatureKey;

    /**
     * @var XMLSecLibs\XMLSecurityKey
     */
    private $encryptionKey;

    /**
     * @var Data\Metadata\Federation
     */
    private $federationMetadata;

    /**
     * @var string
     */
    private $federationMetadataUrl;

    /**
     * @var Data\Metadata\MatchingServiceAdapter
     */
    private $matchingServiceAdapterMetadata;

    /**
     * @var string
     */
    private $matchingServiceAdapterMetadataUrl;

    /**
     * @var Data\Loader;
     */
    private $metadataLoader;

    /**
     * Factory create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger = $serviceLocator->get('logger');
        $container = new Data\Container($logger);
        \SAML2\Compat\ContainerSingleton::setContainer($container);

        $config = [];
        $globalConfig = $serviceLocator->get('config');
        if (isset($globalConfig[self::CONFIG_KEY])) {
            $config = $globalConfig[self::CONFIG_KEY];
        }

        $cache = null;
        if (!empty($config['cache']) && is_array($config['cache'])) {
            $cache = \Zend\Cache\StorageFactory::factory($config['cache']);
        }
        $this->metadataLoader = new Data\Loader($cache);
        if ($serviceLocator->has(\Dvsa\Olcs\Utils\Client\HttpExternalClientFactory::class)) {
            $this->metadataLoader->setHttpClient(
                $serviceLocator->get(\Dvsa\Olcs\Utils\Client\HttpExternalClientFactory::class)
            );
        }

        if (!empty($config['entity_identifier'])) {
            $this->setEntityIdentifier($config['entity_identifier']);
        }

        if (!empty($config['signature_key'])) {
            $this->loadSignatureKey($config['signature_key']);
        }

        if (!empty($config['encryption_key'])) {
            $this->loadEncryptionKey($config['encryption_key']);
        }

        if (!empty($config['federation_metadata_url'])) {
            $this->setFederationMetadataUrl($config['federation_metadata_url']);
        }

        if (!empty($config['msa_metadata_url'])) {
            $this->setMatchingServiceAdapterMetadataUrl($config['msa_metadata_url']);
        }

        return $this;
    }

    /**
     * Get authentication request data for use in a form
     *
     * @return array Auth request data ['url' => 'http://abc.com', 'samlRequest' => 'akshjdg762354....']]
     */
    public function getAuthenticationRequest()
    {
        // Set up an AuthnRequest
        $request = new \SAML2\AuthnRequest();

        $request->setIssuer($this->getEntityIdentifier());
        $request->setDestination($this->getFederationMetaData()->getSsoUrl());
        // Set our signing key so that request can be signed
        $request->setSignatureKey($this->getSignatureKey());

        // Set the request into a POST form
        $binding = new \Dvsa\Olcs\GdsVerify\SAML2\Binding();

        return $binding->send($request);
    }

    /**
     * Process a SAML response and return account attributes
     *
     * @param string $response Encoded SAML response
     *
     * @return Data\Attributes
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getAttributesFromResponse($response)
    {
        $binding = new \Dvsa\Olcs\GdsVerify\SAML2\Binding();
        $samlResponse = $binding->processResponse($response);

        if (!$samlResponse->isSuccess()) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('SAML Response is not a success message');
        }

        // Cannot validate signature at the moment !!! Seems to be missing from Compliance Tool, this is due to the new
        // SAML Profile. Yet to establish full implications of this
//        try {
//            if (!$samlResponse->validate($this->getFederationSigningCertificate())) {
//                // Assume it should be signed, if not we can remove this
//                throw new \Dvsa\Olcs\GdsVerify\Exception('Error SAML response is not signed');
//            }
//        } catch (\Exception $e) {
//            // Exception thrown if signature validation fails
//            throw new \Dvsa\Olcs\GdsVerify\Exception($e->getMessage());
//        }

        // get the our encryption  key
        $encryptionKey = $this->getEncryptionKey();
        // Get MSA signing certificate
        $msaCert = $this->getMatchingServiceAdapterSigningCertificate();

        $attributes = [];
        foreach ($samlResponse->getAssertions() as $assertion) {
            /* @var $assertion \SAML2\EncryptedAssertion */

            // Decrypy the assertion
            $decryptedAssertion = $assertion->getAssertion($encryptionKey);

            // Validate that the assertion is signed by the Matching Service Adapter
            $decryptedAssertion->validate($msaCert);

            $attributes = $decryptedAssertion->getAttributes();
            // flatten array
            foreach ($attributes as &$value) {
                $value = $value[0];
            }
        }

        return new Data\Attributes($attributes);
    }

    /**
     * Get the Hubs/Federation signing certificate
     *
     * @return XMLSecLibs\XMLSecurityKey
     */
    private function getFederationSigningCertificate()
    {
        $certificate = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_SHA1, ['type' => 'public']);
        $certificate->loadKey(
            "-----BEGIN CERTIFICATE-----\n"
            .$this->getFederationMetaData()->getSigningCertificate()
            ."\n-----END CERTIFICATE-----"
        );

        return $certificate;
    }

    /**
     * Get the Matching Service Adapters signing certificate
     *
     * @return XMLSecLibs\XMLSecurityKey
     */
    private function getMatchingServiceAdapterSigningCertificate()
    {
        $certificate = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_SHA1, ['type' => 'public']);
        $certificate->loadKey(
            "-----BEGIN CERTIFICATE-----\n"
            .$this->getMatchingServiceAdapterMetadata()->getSigningCertificate()
            ."\n-----END CERTIFICATE-----"
        );

        return $certificate;
    }

    /**
     * Get the Signature key to sign auth requests with
     *
     * @return XMLSecLibs\XMLSecurityKey
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getSignatureKey()
    {
        if (!$this->signatureKey instanceof XMLSecLibs\XMLSecurityKey) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Signature key is not set');
        }

        return $this->signatureKey;
    }

    /**
     * Set the signature key
     *
     * @param XMLSecLibs\XMLSecurityKey $key Signature key
     *
     * @return void
     */
    public function setSignatureKey(XMLSecLibs\XMLSecurityKey $key)
    {
        $this->signatureKey = $key;
    }

    /**
     * Load the encryption key from a file
     *
     * @param string $keyFilename Path and filename of the key
     *
     * @return void
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function loadSignatureKey($keyFilename)
    {
        if (!file_exists($keyFilename)) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Signature key file not found');
        }

        $key = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $key->loadKey($keyFilename, true);
        $this->setSignatureKey($key);
    }

    /**
     * Get the Encryption key to decrypt response from the hub
     *
     * @return XMLSecLibs\XMLSecurityKey
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getEncryptionKey()
    {
        if (!$this->encryptionKey instanceof XMLSecLibs\XMLSecurityKey) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Encryption key is not set');
        }

        return $this->encryptionKey;
    }

    /**
     * Set the encryption key
     *
     * @param XMLSecLibs\XMLSecurityKey $key Encryption key
     *
     * @return void
     */
    public function setEncryptionKey(XMLSecLibs\XMLSecurityKey $key)
    {
        $this->encryptionKey = $key;
    }

    /**
     * Load the encryption key from a file
     *
     * @param string $keyFilename Path and file name of key
     *
     * @return void
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function loadEncryptionKey($keyFilename)
    {
        if (!file_exists($keyFilename)) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Encryption key file not found');
        }

        $key = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'private']);
        $key->loadKey($keyFilename, true);
        $this->setEncryptionKey($key);
    }

    /**
     * Get the Federation/Hub metadata document
     *
     * @return Data\Metadata\Federation
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getFederationMetadata()
    {
        if ($this->federationMetadata === null && $this->getFederationMetadataUrl() !== null) {
            $this->federationMetadata = $this->metadataLoader->loadFederationMetadata(
                $this->getFederationMetadataUrl()
            );
        }

        if (!$this->federationMetadata instanceof Data\Metadata\Federation) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Federation metadata not set');
        }

        return $this->federationMetadata;
    }

    /**
     * Get the Matching Service Adapter metadata document
     *
     * @return Data\Metadata\MatchingServiceAdapter
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getMatchingServiceAdapterMetadata()
    {
        if ($this->matchingServiceAdapterMetadata === null && $this->getMatchingServiceAdapterMetadataUrl() !== null) {
            $this->matchingServiceAdapterMetadata = $this->metadataLoader->loadMatchingServiceAdapterMetadata(
                $this->getMatchingServiceAdapterMetadataUrl()
            );
        }

        if (!$this->matchingServiceAdapterMetadata instanceof Data\Metadata\MatchingServiceAdapter) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('MatchingServiceAdapter metadata not set');
        }

        return $this->matchingServiceAdapterMetadata;
    }

    /**
     * Get the Entity Identifier used in making Auth Request
     *
     * @return string
     */
    public function getEntityIdentifier()
    {
        return $this->entityIdentifier;
    }

    /**
     * Set the Entity Identifier used in making Auth Request
     *
     * @param string $entityIdentifier Entity identifier
     *
     * @return void
     */
    public function setEntityIdentifier($entityIdentifier)
    {
        $this->entityIdentifier = $entityIdentifier;
    }

    /**
     * Get the Federation Metadata URL
     *
     * @return string
     */
    public function getFederationMetadataUrl()
    {
        return $this->federationMetadataUrl;
    }

    /**
     * Set the Federation Metadata URL
     *
     * @param string $federationMetadataUrl URL
     *
     * @return void
     */
    public function setFederationMetadataUrl($federationMetadataUrl)
    {
        $this->federationMetadataUrl = $federationMetadataUrl;
    }

    /**
     * Get the Matching Service Adapter Metadata URL
     *
     * @return string
     */
    public function getMatchingServiceAdapterMetadataUrl()
    {
        return $this->matchingServiceAdapterMetadataUrl;
    }

    /**
     * Set the Matching Service Adapter Metadata URL
     *
     * @param string $matchingServiceAdapterMetadataUrl URL
     *
     * @return void
     */
    public function setMatchingServiceAdapterMetadataUrl($matchingServiceAdapterMetadataUrl)
    {
        $this->matchingServiceAdapterMetadataUrl = $matchingServiceAdapterMetadataUrl;
    }
}
