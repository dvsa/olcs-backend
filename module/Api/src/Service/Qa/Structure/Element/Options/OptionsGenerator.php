<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use RuntimeException;

class OptionsGenerator
{
    /** @var array */
    private $sources = [];

    /** @var OptionListFactory */
    private $optionListFactory;

    /** @var OptionFactory */
    private $optionFactory;

    /**
     * Create service instance
     *
     * @param OptionListFactory $optionListFactory
     * @param OptionFactory $optionFactory
     *
     * @return OptionsGenerator
     */
    public function __construct(OptionListFactory $optionListFactory, OptionFactory $optionFactory)
    {
        $this->optionListFactory = $optionListFactory;
        $this->optionFactory = $optionFactory;
    }

    /**
     * Get the converted representation of the specified options to be returned by the API endpoint
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

        $optionList = $this->optionListFactory->create($this->optionFactory);
        $this->sources[$sourceName]->populateOptionList($optionList, $data['options']);

        return $optionList->getRepresentation();
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
