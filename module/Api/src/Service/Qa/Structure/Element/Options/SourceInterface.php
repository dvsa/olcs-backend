<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

interface SourceInterface
{
    /**
     * Get an key/value array representing the options from the specified source data
     *
     * @param array $data
     *
     * @return array
     */
    public function generateOptions(array $data);
}
