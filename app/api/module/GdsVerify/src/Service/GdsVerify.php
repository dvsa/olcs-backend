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
    const CONFIG_ENTITY_ID = 'entity_identifier';
    const CONFIG_SIGNATURE_KEY = 'signature_key';
    const CONFIG_ENCRYPTION_KEYS = 'encryption_keys';
    const CONFIG_MSA_METADATA_URL = 'msa_metadata_url';

    /**
     * @var array Config
     */
    private $config = [];

    /**
     * @var XMLSecLibs\XMLSecurityKey
     */
    private $signatureKey;

    /**
     * @var array of XMLSecLibs\XMLSecurityKey
     */
    private $encryptionKeys = [];

    /**
     * @var Data\Metadata\Federation
     */
//    private $federationMetadata;

    /**
     * @var Data\Metadata\MatchingServiceAdapter
     */
    private $matchingServiceAdapterMetadata;

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
        $logger = new \Olcs\Logging\Log\ZendLogPsr3Adapter($serviceLocator->get('logger'));
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
        $this->config = $config;

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
        $request->setDestination($this->getMatchingServiceAdapterMetadata()->getSsoUrl());
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

        // Get MSA signing certificate
        $msaCert = $this->getMatchingServiceAdapterSigningCertificate();

        $attributes = [];
        foreach ($samlResponse->getAssertions() as $assertion) {
            /* @var $assertion \SAML2\EncryptedAssertion */

            // decrypt the assertion
            $decryptedAssertion = $this->decrytAssertion($assertion);

            // Validate that the assertion is signed by the Matching Service Adapter
            try {
                $decryptedAssertion->validate($msaCert);
            } catch (\Exception $e) {
                throw new \Dvsa\Olcs\GdsVerify\Exception('SAML Assertion signature error', 0, $e);
            }

            $attributes = $decryptedAssertion->getAttributes();
            // flatten array
            foreach ($attributes as &$value) {
                $value = $value[0];
            }
        }

        return new Data\Attributes($attributes);
    }

    /**
     * Decrypt an assertion
     *
     * @param \SAML2\EncryptedAssertion $assertion The assertion to decrypt
     *
     * @return \SAML2\Assertion
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    private function decrytAssertion(\SAML2\EncryptedAssertion $assertion)
    {
        $keyNumber = 0;
        while ($encryptionKey = $this->getEncryptionKey($keyNumber)) {
            try {
                return $assertion->getAssertion($encryptionKey);
            } catch (\Exception $e) {
                // Swallow the exception, and try the next key
            }
            $keyNumber++;
        }

        throw new \Dvsa\Olcs\GdsVerify\Exception('Cannot decrypt the SAML Assertion');
    }

    /**
     * Get the Hubs/Federation signing certificate
     *
     * @return XMLSecLibs\XMLSecurityKey
     */
//    private function getFederationSigningCertificate()
//    {
//        $certificate = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_SHA1, ['type' => 'public']);
//        $certificate->loadKey(
//            "-----BEGIN CERTIFICATE-----\n"
//            .$this->getFederationMetaData()->getSigningCertificate()
//            ."\n-----END CERTIFICATE-----"
//        );
//
//        return $certificate;
//    }

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
        if ($this->signatureKey instanceof XMLSecLibs\XMLSecurityKey) {
            return $this->signatureKey;
        }

        if (!empty($this->config[self::CONFIG_SIGNATURE_KEY])) {
            return $this->loadSignatureKey($this->config[self::CONFIG_SIGNATURE_KEY]);
        }

        throw new \Dvsa\Olcs\GdsVerify\Exception('Signature key is not set');
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
     * @return XMLSecLibs\XMLSecurityKey
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

        return $key;
    }

    /**
     * Get the Encryption key to decrypt response from the hub
     *
     * @param int $n Index number of key to get
     *
     * @return XMLSecLibs\XMLSecurityKey|false
     */
    public function getEncryptionKey($n = 0)
    {
        if (isset($this->encryptionKeys[$n]) && $this->encryptionKeys[$n] instanceof XMLSecLibs\XMLSecurityKey) {
            return $this->encryptionKeys[$n];
        }

        if (!empty($this->config[self::CONFIG_ENCRYPTION_KEYS][$n])) {
            return $this->loadEncryptionKey($this->config[self::CONFIG_ENCRYPTION_KEYS][$n], $n);
        }

        return false;
    }

    /**
     * Set the encryption key
     *
     * @param XMLSecLibs\XMLSecurityKey $key Encryption key
     * @param int                       $n   Index number of key to set
     *
     * @return void
     */
    public function setEncryptionKey(XMLSecLibs\XMLSecurityKey $key, $n = 0)
    {
        $this->encryptionKeys[$n] = $key;
    }

    /**
     * Load the encryption key from a file
     *
     * @param string $keyFilename Path and file name of key
     * @param int    $n           Index number of key to load
     *
     * @return XMLSecLibs\XMLSecurityKey
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function loadEncryptionKey($keyFilename, $n = 0)
    {
        if (!file_exists($keyFilename)) {
            throw new \Dvsa\Olcs\GdsVerify\Exception('Encryption key file not found');
        }

        $key = new XMLSecLibs\XMLSecurityKey(XMLSecLibs\XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'private']);
        $key->loadKey($keyFilename, true);
        $this->setEncryptionKey($key, $n);

        return $key;
    }

    /**
     * Get the Federation/Hub metadata document
     *
     * @return Data\Metadata\Federation
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
//    public function getFederationMetadata()
//    {
//        if ($this->federationMetadata === null && $this->getFederationMetadataUrl() !== null) {
//            $this->federationMetadata = $this->metadataLoader->loadFederationMetadata(
//                $this->getFederationMetadataUrl()
//            );
//        }
//
//        if (!$this->federationMetadata instanceof Data\Metadata\Federation) {
//            throw new \Dvsa\Olcs\GdsVerify\Exception('Federation metadata not set');
//        }
//
//        return $this->federationMetadata;
//    }

    /**
     * Get the Matching Service Adapter metadata document
     *
     * @return Data\Metadata\MatchingServiceAdapter
     * @throws \Dvsa\Olcs\GdsVerify\Exception
     */
    public function getMatchingServiceAdapterMetadata()
    {
        if ($this->matchingServiceAdapterMetadata instanceof Data\Metadata\MatchingServiceAdapter) {
            return $this->matchingServiceAdapterMetadata;
        }

        if (!empty($this->config[self::CONFIG_MSA_METADATA_URL])) {
            $this->matchingServiceAdapterMetadata = $this->metadataLoader->loadMatchingServiceAdapterMetadata(
                $this->config[self::CONFIG_MSA_METADATA_URL]
            );
            return $this->matchingServiceAdapterMetadata;
        }

        throw new \Dvsa\Olcs\GdsVerify\Exception('MatchingServiceAdapter metadata not set');
    }

    /**
     * Get the Entity Identifier used in making Auth Request
     *
     * @return string
     * @throws \Exception
     */
    public function getEntityIdentifier()
    {
        if (!empty($this->config[self::CONFIG_ENTITY_ID])) {
            return $this->config[self::CONFIG_ENTITY_ID];
        }

        throw new \Exception('Entity identifier is not specified');
    }
}
