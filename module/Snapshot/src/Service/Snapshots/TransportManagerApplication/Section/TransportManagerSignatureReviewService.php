<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Transport Manager Responsibility Review Service
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerSignatureReviewService extends AbstractReviewService
{

    const SIGNATURE = 'markup-tma-declaration-signature';
    const SIGNATURE_DIGITAL = 'markup-tma-declaration-signature-digital';
    const SIGNATURE_DIGITAL_BOTH = 'markup-tma-declaration-signature-digital-both';
    const ADDRESS = 'tm-review-return-address';
    const SIGNATURE_DIGITAL_OPERATOR_TM = 'markup-tma-declaration-signature-digital-operator-tm';

    /**
     * Format the readonly config from the given data
     *
     * @param TransportManagerApplication $tma
     *
     * @return array
     */
    public function getConfig(TransportManagerApplication $tma): array
    {
        $partial = $this->getPartial($tma);

        $replaceData = $this->getReplaceData($tma);

        $markup = $this->translateReplace(
            $partial,
            $replaceData
        );

        return [
            'markup' => $markup
        ];
    }

    private function getPartial(TransportManagerApplication $tma): string
    {
        $opDigitalSignature = $tma->getOpDigitalSignature();

        $partial = !empty($opDigitalSignature) && !empty($opDigitalSignature->getSignatureName()) ? self::SIGNATURE_DIGITAL_BOTH : self::SIGNATURE_DIGITAL;

        if ($tma->getIsOwner() === 'Y') {
            $partial = self::SIGNATURE_DIGITAL_OPERATOR_TM;
        }
        $tmDigitalSignature = $tma->getTmDigitalSignature();
        return !empty($tmDigitalSignature) && !empty($tmDigitalSignature->getSignatureName()) ? $partial : self::SIGNATURE;
    }

    /**
     * getReplaceData
     *
     * @param TransportManagerApplication $tma
     *
     * @return array
     */
    private function getReplaceData(TransportManagerApplication $tma): array
    {
        $replaceData = [
            $this->getOwnerLabel($tma),
            $this->translate(self::ADDRESS)
        ];

        if (!empty($tma->getTmDigitalSignature())
            && empty($tma->getOpDigitalSignature())) {
            $tmSignature = $tma->getTmDigitalSignature();
            $tmFullName = $tmSignature->getSignatureName();
            $tmDateOfBirth = $tmSignature->getDateOfBirth();
            $signatureDate = $tmSignature->getCreatedOn();
            array_unshift(
                $replaceData,
                $tmFullName,
                $tmDateOfBirth,
                $signatureDate
            );
        } elseif (!empty($tma->getOpDigitalSignature())) {
            $tmSignature = $tma->getTmDigitalSignature();
            $tmFullName = $tmSignature->getSignatureName();
            $tmDateOfBirth = $tmSignature->getDateOfBirth();
            $signatureDate = $tmSignature->getCreatedOn();
            $opSignature = $tma->getOpDigitalSignature();
            $opFullName = $opSignature->getSignatureName();
            $opDateOfBirth = $opSignature->getDateOfBirth();
            $OpSignature = $opSignature->getCreatedOn();
            array_unshift(
                $replaceData,
                $tmFullName,
                $tmDateOfBirth,
                $signatureDate,
                $opFullName,
                $opDateOfBirth,
                $OpSignature
            );
        }
        return $replaceData;
    }

    /**
     * Get the the label for the owner signature box
     *
     * @param TransportManagerApplication $tma
     *
     * @return string
     */
    private function getOwnerLabel(TransportManagerApplication $tma)
    {
        $map = [
            Organisation::ORG_TYPE_LLP => 'directors-signature',
            Organisation::ORG_TYPE_REGISTERED_COMPANY => 'directors-signature',
            Organisation::ORG_TYPE_PARTNERSHIP => 'partners-signature',
            Organisation::ORG_TYPE_SOLE_TRADER => 'owners-signature',
        ];

        $organisationType = $tma->getApplication()->getLicence()->getOrganisation()->getType()->getId();

        if (isset($map[$organisationType])) {
            $label = $map[$organisationType];
        } else {
            $label = 'responsible-person-signature';
        }

        return $this->translate($label);
    }
}
