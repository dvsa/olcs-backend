<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * Config Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ConfigAwareInterface
{
    /**
     * @param array $config
     */
    public function setConfig(array $config = []);

    /**
     * @return array
     */
    public function getConfig();
}
