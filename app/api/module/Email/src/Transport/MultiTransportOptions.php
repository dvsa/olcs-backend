<?php
namespace Dvsa\Olcs\Email\Transport;

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
            $this->transport[] = Factory::create($transport);
            if ($transport instanceof S3File) {
                $transport->setOptions($this->sl->get('S3FileOptions'));
            }
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
