<?php

/**
 * Variation Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Utils\Helper\ValueHelper;

/**
 * Variation Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationUndertakingsReviewService extends AbstractReviewService
{
    const GV81 = 'markup-application_undertakings_GV81';
    const GV81_STANDARD = 'markup-application_undertakings_GV81-Standard';

    const GV81NI = 'markup-application_undertakings_GV81-NI';
    const GV81NI_STANDARD = 'markup-application_undertakings_GV81-NI-Standard';

    const GV80A = 'markup-application_undertakings_GV80A';

    const GV80ANI = 'markup-application_undertakings_GV80A-NI';

    const PSV430 = 'markup-application_undertakings_PSV430';
    const PSV430_STANDARD = 'markup-application_undertakings_PSV430-Standard';

    const SIGNATURE = 'markup-application_undertakings_signature';

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

    private function getMarkup($data)
    {
        if ($this->isPsv($data)) {
            return $this->getPsv430($data);
        }

        if ($this->isUpgrade($data)) {
            if (ValueHelper::isOn($data['niFlag'])) {
                return $this->getNiGv80A();
            }

            return $this->getGv80A();
        }

        if (ValueHelper::isOn($data['niFlag'])) {
            return $this->getGv81Ni($data);
        }

        return $this->getGv81($data);
    }

    private function getGv81(array $data)
    {
        $isStandard = $this->isStandard($data);

        $additionalParts = [
            $this->translate(self::SIGNATURE),
            $isStandard ? $this->translate(self::GV81_STANDARD) : ''
        ];

        return $this->translateReplace(self::GV81, $additionalParts);
    }

    private function getGv81Ni(array $data)
    {
        $isStandard = $this->isStandard($data);

        $additionalParts = [
            $this->translate(self::SIGNATURE),
            $isStandard ? $this->translate(self::GV81NI_STANDARD) : ''
        ];

        return $this->translateReplace(self::GV81NI, $additionalParts);
    }

    private function getGv80A()
    {
        $additionalParts = [$this->translate(self::SIGNATURE)];

        return $this->translateReplace(self::GV80A, $additionalParts);
    }

    private function getNiGv80A()
    {
        $additionalParts = [$this->translate(self::SIGNATURE)];

        return $this->translateReplace(self::GV80ANI, $additionalParts);
    }

    private function getPsv430(array $data)
    {
        $isStandard = $this->isStandard($data);

        $additionalParts = [
            $this->translate(self::SIGNATURE),
            $isStandard ? $this->translate(self::PSV430_STANDARD) : ''
        ];

        return $this->translateReplace(self::PSV430, $additionalParts);
    }

    private function isStandard(array $data)
    {
        return in_array($data['licenceType']['id'], $this->standardOptions);
    }

    /**
     * If the variation is upgrading from restricted to standard
     *
     * @param array $data
     * @return bool
     */
    private function isUpgrade(array $data)
    {
        return ($data['licence']['licenceType']['id'] === Licence::LICENCE_TYPE_RESTRICTED && $this->isStandard($data));
    }
}
