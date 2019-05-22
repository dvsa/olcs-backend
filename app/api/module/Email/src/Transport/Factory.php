<?php
namespace Dvsa\Olcs\Email\Transport;

use Zend\Mail\Transport\Factory as ZendFactory;
use Zend\Mail\Transport\TransportInterface;

/**
 * Class Factory
 */
abstract class Factory extends ZendFactory
{
    /**
     * Factory create
     *
     * @param array $spec Spec for the transport
     *
     * @return TransportInterface
     */
    public static function create($spec = [])
    {
        $transport = parent::create($spec);

        if ($transport instanceof MultiTransport && isset($spec['options'])) {
            $transport->setOptions(new MultiTransportOptions($spec['options']));
        }
        if ($transport instanceof S3File && isset($spec['options'])) {
            $transport->setOptions(new S3FileOptions($spec['options']));
        }

        return $transport;
    }
}
