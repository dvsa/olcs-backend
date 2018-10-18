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

        $partial = !empty($tma->getOpDigitalSignature()->getSignatureName()) ? self::SIGNATURE_DIGITAL_BOTH : self::SIGNATURE_DIGITAL;

        if ($tma->getIsOwner() === 'Y') {
            $partial = self::SIGNATURE_DIGITAL_OPERATOR_TM;
        }
        return !empty($tma->getTmDigitalSignature()->getSignatureName()) ? $partial : self::SIGNATURE;
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

        if (!empty($tma->getTmDigitalSignature()->getSignatureName())
            && empty($tma->getOpDigitalSignature()->getSignatureName())) {
            $tmFullName = $tma->getTmDigitalSignature()->getSignatureName();
            $tmDateOfBirth = $tma->getTmDigitalSignature()->getDateOfBirth();
            $signatureDate = $tma->getTmDigitalSignature()->getCreatedOn();
            array_unshift(
                $replaceData,
                $tmFullName,
                $tmDateOfBirth,
                $signatureDate
            );
        } elseif (!empty($tma->getOpDigitalSignature()->getSignatureName())) {
            $tmFullName = $tma->getTmDigitalSignature()->getSignatureName();
            $tmDateOfBirth = $tma->getTmDigitalSignature()->getDateOfBirth();
            $signatureDate = $tma->getTmDigitalSignature()->getCreatedOn();
            $opFullName = $tma->getOpDigitalSignature()->getSignatureName();
            $opDateOfBirth = $tma->getTmDigitalSignature()->getDateOfBirth();
            $OpSignature = $tma->getTmDigitalSignature()->getCreatedOn();
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
