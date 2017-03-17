<?php

namespace Dvsa\Olcs\GdsVerify\Data;

/**
 * Class Container
 * @package Dvsa\Olcs\GdsVerify\Data
 */
class Container extends \SAML2\Compat\AbstractContainer
{
    private $logger;

    /**
     * Container constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Provide logger to SAML2
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * SAML show debug message
     *
     * @param string $message Message to log
     * @param string $type    Type of message
     *
     * @return void
     */
    public function debugMessage($message, $type)
    {
        // For debuggin SAML enable this line
        // $this->logger->debug($type .' - '. $message);
    }

    /**
     * Generate unique ID
     *
     * @return string
     */
    public function generateId()
    {
        return 'GdsVerify'. microtime(true);
    }

    /**
     * No op
     *
     * @param string $url  URL
     * @param array  $data Date
     *
     * @return void
     */
    public function postRedirect($url, $data = array())
    {
    }

    /**
     * No op
     *
     * @param string $url  URL
     * @param array  $data Date
     *
     * @return void
     */
    public function redirect($url, $data = array())
    {
    }
}
