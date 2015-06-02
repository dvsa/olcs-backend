<?php

namespace Dvsa\Olcs\Api\Entity\OtherLicence;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * OtherLicence Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="other_licence",
 *    indexes={
 *        @ORM\Index(name="ix_other_licence_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_other_licence_previous_licence_type", columns={"previous_licence_type"}),
 *        @ORM\Index(name="ix_other_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_other_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_other_licence_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(
 *          name="ix_other_licence_transport_manager_application_id", columns={"transport_manager_application_id"}
 *        ),
 *        @ORM\Index(name="fk_other_licence_transport_manager_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_other_licence_ref_data1_idx", columns={"role"})
 *    }
 * )
 */
class OtherLicence extends AbstractOtherLicence
{
    const TYPE_CURRENT = 'prev_has_licence';
    const TYPE_APPLIED = 'prev_had_licence';
    const TYPE_REFUSED = 'prev_been_refused';
    const TYPE_REVOKED = 'prev_been_revoked';
    const TYPE_PUBLIC_INQUIRY = 'prev_been_at_pi';
    const TYPE_DISQUALIFIED = 'prev_been_disqualified_tc';
    const TYPE_HELD = 'prev_purchased_assets';

    const ERROR_TYPE_NOT_IMPLEMENTED = 'AP-LH-OL-1';
    const ERROR_FIELD_IS_REQUIRED = 'AP-LH-OL-2';
    const ERROR_DATE_IN_FUTURE = 'AP-LH-OL-3';
    const ERROR_TYPE_IS_EMPTY = 'AP-LH-OL-4';

    protected $requiredFields = [
        self::TYPE_CURRENT => ['licNo', 'holderName', 'willSurrender'],
        self::TYPE_APPLIED => ['licNo', 'holderName'],
        self::TYPE_REFUSED => ['licNo', 'holderName'],
        self::TYPE_REVOKED => ['licNo', 'holderName'],
        self::TYPE_PUBLIC_INQUIRY => ['licNo', 'holderName'],
        self::TYPE_DISQUALIFIED => ['licNo', 'holderName', 'disqualificationDate', 'disqualificationLength'],
        self::TYPE_HELD => ['licNo', 'holderName', 'purchaseDate']
    ];

    public function updateOtherLicence(
        $licNo,
        $holderName,
        $willSurrender,
        $disqualificationDate,
        $disqualificationLength,
        $purchaseDate
    ) {
        if (
            $this->validateOtherLicence(
                $willSurrender,
                $disqualificationDate,
                $disqualificationLength,
                $purchaseDate
            )
        ) {
            $previousLicenceType = $this->previousLicenceType->getId();
            foreach ($this->requiredFields[$previousLicenceType] as $field) {
                if (substr($field, -4) == 'Date') {
                    $this->$field = new \DateTime($$field);
                } else {
                    $this->$field = $$field;
                }
            }
            return true;
        }
    }

    protected function validateOtherLicence(
        $willSurrender,
        $disqualificationDate,
        $disqualificationLength,
        $purchaseDate
    ) {
        $errors = [];

        $previousLicenceType = $this->previousLicenceType;

        // need to remove it when other sections with otherLicence will be done
        $this->checkEmptyPreviousLicenceType($previousLicenceType);
        $previousLicenceTypeId = $previousLicenceType->getId();

        switch ($previousLicenceTypeId) {
            case self::TYPE_CURRENT:
                $errors = $this->checkRequiredField($willSurrender, 'willSurrender', $errors);
                break;
            case self::TYPE_DISQUALIFIED:
                $errors = $this->checkDateNotEmptyAndNotInFuture(
                    $disqualificationDate,
                    'disqualificationDate',
                    $errors
                );
                $errors = $this->checkRequiredField($disqualificationLength, 'disqualificationLength', $errors);

                break;
            case self::TYPE_HELD:
                $errors = $this->checkDateNotEmptyAndNotInFuture(
                    $purchaseDate,
                    'purchaseDate',
                    $errors
                );
                break;
            case self::TYPE_APPLIED:
            case self::TYPE_REFUSED:
            case self::TYPE_REVOKED:
            case self::TYPE_PUBLIC_INQUIRY:
                break;
            default:
                $errors[] = [
                    'previousLicenceType' => [
                        self::ERROR_TYPE_NOT_IMPLEMENTED => 'Other licence type is not implemented'
                    ]
                ];
                break;
        }
        if (!$errors) {
            return true;
        }
        throw new ValidationException($errors);
    }

    protected function isDateInFuture($value)
    {
        $dateObject = new \DateTime($value);
        if ($dateObject > (new \DateTime())) {
            return true;
        }
        return false;
    }

    protected function checkDateNotEmptyAndNotInFuture($dateToCheck, $name, $errors)
    {
        if (!$dateToCheck) {
            $errors[] = [
                $name => [
                    self::ERROR_FIELD_IS_REQUIRED => 'Field is required'
                ]
            ];
        } elseif ($this->isDateInFuture($dateToCheck)) {
            $errors[] = [
                $name => [
                    self::ERROR_DATE_IN_FUTURE => 'Date should not be in future'
                ]
            ];
        }
        return $errors;
    }

    protected function checkRequiredField($field, $name, $errors)
    {
        if (!$field) {
            $errors[] = [
                $name => [
                    self::ERROR_FIELD_IS_REQUIRED => 'Field is required'
                ]
            ];
        }
        return $errors;
    }

    protected function checkEmptyPreviousLicenceType($previousLicenceType)
    {
        if (!$previousLicenceType) {
            $errors[] = [
                'previousLicenceType' => [
                    self::ERROR_TYPE_IS_EMPTY => 'Processing of empty previousLicenceType is not implemented'
                ]
            ];
            throw new ValidationException($errors);
        }
    }
}
