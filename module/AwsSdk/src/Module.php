<?php

namespace Dvsa\Olcs\AwsSdk;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Empty on purpose to defer loading to composer
     * @codeCoverageIgnore No value in testing an empty method
     */
    public function getAutoloaderConfig()
    {
    }
}
