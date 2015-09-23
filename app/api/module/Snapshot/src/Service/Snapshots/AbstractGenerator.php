<?php

/**
 * Abstract Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Model\ViewModel;

/**
 * Abstract Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGenerator implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected function generateReadonly(array $config)
    {
        $model = new ViewModel($config);
        $model->setTerminal(true);
        $model->setTemplate('layout/review');

        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        return $renderer->render($model);
    }
}
