<?php

/**
 * Scanning service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Scanning\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Http\Request;

/**
 * Scanning service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScanningService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Holds the data from the request
     */
    private $data = [];

    /**
     * Set the array of data from a Zend Http Request
     */
    public function setDataFromRequest(Request $request)
    {
        $this->data = array_merge(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
    }

    /**
     * Retrieve previously set data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Is the previously supplied request valid?
     */
    public function isValidRequest()
    {
        if (!isset($this->data['image'])
            || !is_array($this->data['image'])
            || $this->data['image']['error'] !== UPLOAD_ERR_OK
        ) {
            return false;
        }

        if (empty($this->data['description'])) {
            return false;
        }

        return true;
    }
}
