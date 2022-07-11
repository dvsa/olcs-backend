<?php

/**
 * Form Control Service Manager
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;

/**
 * Form Control Service Manager
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FormControlServiceManager extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = FormControlStrategyInterface::class;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    /**
     * Get the form control strategy corresponding to the provided application step
     *
     * @param ApplicationStep $applicationStep
     *
     * @return FormControlStrategyInterface
     */
    public function getByApplicationStep(ApplicationStep $applicationStep)
    {
        return $this->get(
            $applicationStep->getQuestion()->getFormControlType()->getId()
        );
    }
}
