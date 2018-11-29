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

        $replaceData = $this->getReplaceData($tma, $partial);

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
    private function getReplaceData(TransportManagerApplication $tma, string $partial): array
    {
        $ownerLabel = $this->getOwnerLabel($tma);
        $returnAddress = $this->translate(self::ADDRESS);
        $tmSignature = $tma->getTmDigitalSignature();
        $opSignature = $tma->getOpDigitalSignature();

        if ($tmSignature !== null) {
            $tmFullName = $tmSignature->getSignatureName();
            $tmDateOfBirth = $this->formatDate($tmSignature->getDateOfBirth());
            $tmSignatureDate = $tmSignature->getCreatedOn(true) instanceof \DateTime ?
                $this->formatDate($tmSignature->getCreatedOn(true)) :
                null;
        }

        if ($opSignature !== null) {
            $opFullName = $opSignature->getSignatureName();
            $opDateOfBirth = $this->formatDate($opSignature->getDateOfBirth());
            $opSignatureDate = $opSignature->getCreatedOn(true) instanceof \DateTime ?
                $this->formatDate($opSignature->getCreatedOn(true)) :
                null;
        }

        switch ($partial) {
            case self::SIGNATURE:
                // no digital signature
                $replaceData = [
                    $ownerLabel,
                    $returnAddress,
                ];
                break;
            case self::SIGNATURE_DIGITAL:
                // only the TM signed digitally
                $replaceData = [
                    $tmFullName,
                    $tmDateOfBirth,
                    $tmSignatureDate,
                    $ownerLabel,
                    $returnAddress,
                ];
                break;
            case self::SIGNATURE_DIGITAL_BOTH:
                // Both TM and Operator signed digitally
                $replaceData = [
                    $tmFullName,
                    $tmDateOfBirth,
                    $tmSignatureDate,
                    $ownerLabel,
                    $opFullName,
                    $opDateOfBirth,
                    $opSignatureDate,
                ];
                break;
            case self::SIGNATURE_DIGITAL_OPERATOR_TM:
                // The Operator is also the TM an signed digitally
                $replaceData = [
                    $opFullName,
                    $opDateOfBirth,
                    $opSignatureDate,
                ];
                break;
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
