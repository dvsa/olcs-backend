<?php

/**
 * Q&A Form Control Service Manager Factory
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;
use Dvsa\Olcs\Api\Domain\FormControlServiceManager;

/**
 * Q&A Form Control Service Manager Factory
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FormControlServiceManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'form_control_services';

    protected $serviceManagerClass = FormControlServiceManager::class;
}
