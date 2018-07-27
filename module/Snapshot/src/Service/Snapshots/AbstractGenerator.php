<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\View\Model\ViewModel;

/**
 * Abstract Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGenerator extends AbstractPluginManager
{

    protected function generateReadonly(array $config, $template = 'review')
    {
        $model = new ViewModel($config);
        $model->setTerminal(true);
        $model->setTemplate('layout/' . $template);

        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        return $renderer->render($model);
    }

    public function getServiceLocator()
    {
        return $this->creationContext;
    }
}
