<?php

/**
 * Generic REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Mvc\MvcEvent;

use Zend\Filter\Word\DashToCamelCase;

/**
 * Generic REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericController extends AbstractBasicRestServerController
{
    /**
     * Grab the service name from the URL and then call the onDispatch method
     *
     * @param MvcEvent $e
     * @return object
     */
    public function onDispatch(MvcEvent $e)
    {
        $service = $this->params()->fromRoute('service');

        $this->setServiceName($this->getDashToCamelCaseFilter()->filter($service));

        return parent::onDispatch($e);
    }

    /**
     * Return an instance of getDashToCamelCaseFilter
     *
     * @return \Zend\Filter\Word\DashToCamelCase
     */
    public function getDashToCamelCaseFilter()
    {
        return new DashToCamelCase();
    }
}
