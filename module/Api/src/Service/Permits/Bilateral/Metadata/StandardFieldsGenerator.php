<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use RuntimeException;

class StandardFieldsGenerator implements FieldsGeneratorInterface
{
    /** @var CurrentFieldValuesGenerator */
    private $currentFieldValuesGenerator;

    /**
     * Create service instance
     *
     * @param CurrentFieldValuesGenerator $currentFieldValuesGenerator
     *
     * @return StandardFieldsGenerator
     */
    public function __construct(CurrentFieldValuesGenerator $currentFieldValuesGenerator)
    {
        $this->currentFieldValuesGenerator = $currentFieldValuesGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(IrhpPermitStock $irhpPermitStock, ?IrhpPermitApplication $irhpPermitApplication)
    {
        $applicationPathGroupId = $irhpPermitStock->getApplicationPathGroup()->getId();
        switch ($applicationPathGroupId) {
            case ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID:
                $cabotageOptions = [
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED
                ];
                break;
            case ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID:
            case ApplicationPathGroup::BILATERALS_TURKEY_ID:
            case ApplicationPathGroup::BILATERALS_UKRAINE_ID:
                $cabotageOptions = [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED
                ];
                break;
            case ApplicationPathGroup::BILATERALS_STANDARD_AND_CABOTAGE_PERMITS_ID:
                $cabotageOptions = [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED
                ];
                break;
            default:
                throw new RuntimeException('Unexpected application path group');
        }

        $currentFieldValues = $this->currentFieldValuesGenerator->generate($irhpPermitStock, $irhpPermitApplication);

        $journeyOptions = $irhpPermitStock->getPermitUsageList();

        $responseFields = [];
        foreach ($cabotageOptions as $cabotageOption) {
            foreach ($journeyOptions as $journeyOption) {
                $journeyOptionId = $journeyOption->getId();

                $responseFields[] = [
                    'journey' => $journeyOptionId,
                    'cabotage' => $cabotageOption,
                    'value' => $currentFieldValues[$journeyOptionId][$cabotageOption],
                ];
            }
        }

        return $responseFields;
    }
}
