<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;

class FieldsGenerator
{
    /** @var CurrentFieldValuesGenerator */
    private $currentFieldValuesGenerator;

    /**
     * Create service instance
     *
     * @param CurrentFieldValuesGenerator $currentFieldValuesGenerator
     *
     * @return FieldsGenerator
     */
    public function __construct(CurrentFieldValuesGenerator $currentFieldValuesGenerator)
    {
        $this->currentFieldValuesGenerator = $currentFieldValuesGenerator;
    }

    /**
     * Generate the fields part of the response
     *
     * @param IrhpPermitStock $irhpPermitStock
     * @param IrhpPermitApplication $irhpPermitApplication (optional)
     *
     * @return array
     */
    public function generate(IrhpPermitStock $irhpPermitStock, ?IrhpPermitApplication $irhpPermitApplication)
    {
        $currentFieldValues = $this->currentFieldValuesGenerator->generate($irhpPermitStock, $irhpPermitApplication);

        $applicationPathGroupId = $irhpPermitStock->getApplicationPathGroup()->getId();
        switch ($applicationPathGroupId) {
            case ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID:
                $cabotageOptions = [
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED
                ];
                break;
            case ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID:
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
