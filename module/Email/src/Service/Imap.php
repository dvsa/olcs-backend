<?php

namespace Dvsa\Olcs\Email\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Storage;
use Zend\Mail\Exception\RuntimeException as ZendMailRuntimeException;

/**
 * Class Imap
 *
 * @package Olcs\Email\Service
 */
class Imap implements FactoryInterface
{
    private $store;

    private $connected = false;

    private $config;

    /**
     * Setup the factory, with a service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        if (!isset($config['mailboxes'])) {
            throw new ZendMailRuntimeException('No mailbox config found');
        }

        $this->setConfig($config['mailboxes']);

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get list of email messages
     *
     * @return array|string
     */
    public function getMessages()
    {
        try {
            $store = $this->getStore();

            $counter = 0;
            $messages = [];
            $numMessages = $store->countMessages();
            if ($numMessages) {
                do {
                    $counter++; // counter starts at 1
                    $messages[$counter] = $store->getUniqueId($counter);
                } while ($counter < $numMessages);
            }

            return $messages;

        } catch (ZendMailRuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get an individual message by id
     *
     * @param string $id Unique Id
     * @return array|string
     */
    public function getMessage($id)
    {
        try {
            $store = $this->getStore();
            $number = $store->getNumberByUniqueId($id);
            $message = $store->getMessage($number);
            return [
                'number'   => $number,
                'uniqueId' => $id,
                'subject'  => $message->subject,
                'content'  => $message->getContent(),
            ];
        } catch (ZendMailRuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Delete an individual message by id
     *
     * @param int $id
     * @return array|string
     */
    public function removeMessage($id)
    {
        try {
            $store = $this->getStore();
            $number = $store->getNumberByUniqueId($id);
            return $store->removeMessage($number);
        } catch (ZendMailRuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get Store.
     *
     * @return \Zend\Mail\Storage\AbstractStorage
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set Store.
     *
     * @param Zend\Mail\Storage\AbstractStorage
     * @return $this
     */
    public function setStore(Storage\AbstractStorage $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Connect to the Imap store with given credentials
     *
     * @param string $mailbox
     * @return $this
     */
    public function connect($mailbox)
    {
        $config = $this->getConfig();
        if (!isset($config[$mailbox])) {
            throw new ZendMailRuntimeException("No config found for mailbox '$mailbox'");
        }

        $this->store = new Storage\Imap($config[$mailbox]);
        $this->connected = true;

        return $this;
    }
}
