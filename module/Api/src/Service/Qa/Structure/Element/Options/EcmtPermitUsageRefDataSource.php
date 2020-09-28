<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use RuntimeException;

class EcmtPermitUsageRefDataSource implements SourceInterface
{
    const LABEL_KEY = 'label';
    const HINT_KEY = 'hint';

    /** @var RefDataSource */
    private $refDataSource;

    /** @var array */
    private $transformations;

    /**
     * Create service instance
     *
     * @param RefDataSource $refDataSource
     * @param array $transformations
     *
     * @return EcmtPermitUsageRefDataSource
     */
    public function __construct(RefDataSource $refDataSource, array $transformations)
    {
        $this->refDataSource = $refDataSource;
        $this->transformations = $transformations;
    }

    /**
     * {@inheritdoc}
     */
    public function populateOptionList(OptionList $optionList, array $options)
    {
        $this->refDataSource->populateOptionList($optionList, $options);

        foreach ($optionList->getOptions() as $option) {
            $optionValue = $option->getValue();

            if (!isset($this->transformations[$optionValue])) {
                throw new RuntimeException('Unable to find transformations for option value ' . $optionValue);
            }

            $translationKeys = $this->transformations[$optionValue];

            $option->setLabel($translationKeys[self::LABEL_KEY]);

            if (isset($translationKeys[self::HINT_KEY])) {
                $option->setHint($translationKeys[self::HINT_KEY]);
            }
        }
    }
}
