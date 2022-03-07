<?php

/**
 * Application Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Utils\Helper\ValueHelper;

/**
 * Application Undertakings Review Service
 *
 * @NOTE This is also re-used in Dvsa\Olcs\Api\Domain\QueryHandler\Application\DeclarationUndertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationUndertakingsReviewService extends AbstractReviewService
{
    const GV79 = 'markup-application_undertakings_GV79';
    const GV79_STANDARD = 'markup-application_undertakings_GV79-Standard';
    const GV79_DECLARE = 'markup-application_undertakings_GV79-declare';

    const GV79_AUTH_LGV = 'markup-application_undertakings_GV79-auth-lgv';
    const GV79_AUTH_OTHER = 'markup-application_undertakings_GV79-auth-other';
    const GV79NI_AUTH_OTHER = 'markup-application_undertakings_GV79-NI-auth-other';

    const GV79NI = 'markup-application_undertakings_GV79-NI';
    const GV79NI_STANDARD = 'markup-application_undertakings_GV79-NI-Standard';
    const GV79NI_DECLARE = 'markup-application_undertakings_GV79-NI-declare';

    const PSV421 = 'markup-application_undertakings_PSV421';
    const PSV421_STANDARD = 'markup-application_undertakings_PSV421-Standard';
    const PSV421_DECLARE = 'markup-application_undertakings_PSV421-declare';

    const PSV356 = 'markup-application_undertakings_PSV356';

    private $standardOptions = [
        Licence::LICENCE_TYPE_STANDARD_NATIONAL,
        Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return [
            'markup' => $this->getMarkup($data)
        ];
    }

    public function getMarkup($data)
    {
        if ($this->isPsv($data)) {
            if ($data['licenceType']['id'] === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
                return $this->getPsv356();
            }

            return $this->getPsv421($data);
        }

        if (ValueHelper::isOn($data['niFlag'])) {
            return $this->getGv79Ni($data);
        }

        return $this->getGv79($data);
    }

    private function getGv79(array $data)
    {
        $isStandard = $this->isStandard($data);
        $isInternal = $this->isInternal($data);

        $additionalParts = [
            $this->translate($this->getGv79AuthTranslationKey($data)),
            $isStandard ? $this->translate(self::GV79_STANDARD) : '',
            $isInternal ? $this->translate(self::GV79_DECLARE) : ''
        ];

        return $this->translateReplace(self::GV79, $additionalParts);
    }

    private function getGv79Ni(array $data)
    {
        $isStandard = $this->isStandard($data);
        $isInternal = $this->isInternal($data);

        $additionalParts = [
            $this->translate($this->getGv79AuthTranslationKey($data)),
            $isStandard ? $this->translate(self::GV79NI_STANDARD) : '',
            $isInternal ? $this->translate(self::GV79NI_DECLARE) : ''
        ];

        return $this->translateReplace(self::GV79NI, $additionalParts);
    }

    /**
     * Get the translation key corresponding to the auth bullet points within the declaration
     *
     * @param array $data
     *
     * @return string
     */
    private function getGv79AuthTranslationKey(array $data)
    {
        $vehicleTypeId = $data['vehicleType']['id'];
        if ($vehicleTypeId == RefData::APP_VEHICLE_TYPE_LGV) {
            return self::GV79_AUTH_LGV;
        }

        if (ValueHelper::isOn($data['niFlag'])) {
            return self::GV79NI_AUTH_OTHER;
        }

        return self::GV79_AUTH_OTHER;
    }

    private function getPsv421(array $data)
    {
        $isStandard = $this->isStandard($data);
        $isInternal = $this->isInternal($data);

        $additionalParts = [
            $isStandard ? $this->translate(self::PSV421_STANDARD) : '',
            $isInternal ? $this->translate(self::PSV421_DECLARE) : ''
        ];

        return $this->translateReplace(self::PSV421, $additionalParts);
    }

    private function getPsv356()
    {
        return $this->translate(self::PSV356);
    }

    private function isStandard(array $data)
    {
        return in_array($data['licenceType']['id'], $this->standardOptions);
    }
}
