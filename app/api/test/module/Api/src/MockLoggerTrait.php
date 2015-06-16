<?php

/**
 * Mock Logger Trait
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api;

/**
 * Mock Logger Trait
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
trait MockLoggerTrait
{
    /**
     * @var \Zend\Log\Writer\Mock
     */
    protected $logWriter;

    public function mockLogger()
    {
         // Mock the logger
        $this->logWriter = new \Zend\Log\Writer\Mock();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($this->logWriter);
        return $logger;
    }
}
