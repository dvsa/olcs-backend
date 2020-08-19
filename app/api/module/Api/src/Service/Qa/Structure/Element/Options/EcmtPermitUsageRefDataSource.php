<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Entity\System\RefData;
use RuntimeException;

class EcmtPermitUsageRefDataSource implements SourceInterface
{
    const LABEL_KEY = 'label';
    const HINT_KEY = 'hint';

    const TRANSLATION_KEYS = [
        RefData::ECMT_PERMIT_USAGE_BOTH => [
            self::LABEL_KEY => 'qanda.ecmt.permit-usage.option.both.label',
        ],
        RefData::ECMT_PERMIT_USAGE_CROSS_TRADE_ONLY => [
            self::LABEL_KEY => 'qanda.ecmt.permit-usage.option.cross-trade-only.label',
            self::HINT_KEY => 'qanda.ecmt.permit-usage.option.cross-trade-only.hint',
        ],
        RefData::ECMT_PERMIT_USAGE_TRANSIT_ONLY => [
            self::LABEL_KEY => 'qanda.ecmt.permit-usage.option.transit-only.label',
            self::HINT_KEY => 'qanda.ecmt.permit-usage.option.transit-only.hint',
        ],
    ];

    /** @var RefDataSource */
    private $refDataSource;

    /**
     * Create service instance
     *
     * @param RefDataSource $refDataSource
     *
     * @return EcmtPermitUsageRefDataSource
     */
    public function __construct(RefDataSource $refDataSource)
    {
        $this->refDataSource = $refDataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function populateOptionList(OptionList $optionList, array $options)
    {
        $this->refDataSource->populateOptionList($optionList, $options);

        foreach ($optionList->getOptions() as $option) {
            $optionValue = $option->getValue();

            if (!isset(self::TRANSLATION_KEYS[$optionValue])) {
                throw new RuntimeException('Unable to find translation keys for option ' . $optionValue);
            }

            $translationKeys = self::TRANSLATION_KEYS[$optionValue];

            $option->setLabel($translationKeys[self::LABEL_KEY]);

            if (isset($translationKeys[self::HINT_KEY])) {
                $option->setHint($translationKeys[self::HINT_KEY]);
            }
        }
    }
}
