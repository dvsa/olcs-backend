<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use RuntimeException;

class OptionsGenerator
{
    /** @var array */
    private $sources = [];

    /**
     * Get a key/value array representing the options from the appropriate registered source
     *
     * @param array $data
     *
     * @return array
     *
     * @throws RuntimeException
     */
    public function generate(array $data)
    {
        $sourceName = $data['name'];

        if (!isset($this->sources[$sourceName])) {
            throw new RuntimeException('No source found for name ' . $sourceName);
        }

        return $this->sources[$sourceName]->generateOptions($data['options']);
    }

    /**
     * Registers an implementation of SourceInterface corresponding to the provided name
     *
     * @param string $name
     * @param SourceInterface $source
     */
    public function registerSource($name, SourceInterface $source)
    {
        $this->sources[$name] = $source;
    }
}
