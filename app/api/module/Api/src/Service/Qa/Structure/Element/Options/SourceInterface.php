<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

interface SourceInterface
{
    /**
     * Populate the OptionList object with options from the specified source data
     *
     * @param OptionList $optionList
     * @param array $options
     */
    public function populateOptionList(OptionList $optionList, array $options);
}
