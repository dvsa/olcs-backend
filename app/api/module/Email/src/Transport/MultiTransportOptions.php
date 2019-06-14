<?php
namespace Dvsa\Olcs\Email\Transport;

use Zend\Mail\Transport\Factory;
use Zend\Stdlib\AbstractOptions;

/**
 * MultiTransport Options
 */
class MultiTransportOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $transport = [];

    protected $mail;

    public $sl;

    /**
     * Set the Transports
     *
     * @param array $transports Array of transport array specs for the Factory
     *
     * @return void
     */
    public function setTransport(array $transports)
    {
        foreach ($transports as $transport) {
            $mailTransport = Factory::create($transport);
            if ($mailTransport instanceof S3File) {
                $mailTransport->setOptions($this->sl->get('S3FileOptions'));
            }
            $this->transport[] = $mailTransport;
        }
    }

    /**
     * Get list of Mail transports
     *
     * @return array of Zend\Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }
}
