<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission;



/**
 * Class CanAssignSubmission
 *
 * @package Dvsa\Olcs\Api\Domain\Validation\Validators
 */
class CanAssignSubmission extends AbstractValidator
{


    /**
     * isValid
     *
     * @param AssignSubmission $assignSubmission
     * @param                  $informationCompleteDate
     *
     * @return bool
     */
    public function isValid(AssignSubmission $assignSubmission, $informationCompleteDate){

        $dateFirstAssigned = $assignSubmission->getDateFirstAssigned();

        if(!empty($dateFirstAssigned)){
            $format = 'Y-m-d';
            $dateFirstAssigned = DateTime::createFromFormat ( $format , $dateFirstAssigned);
            $informationCompleteDate = DateTime::createFromFormat($format, $informationCompleteDate);
            return $dateFirstAssigned < $informationCompleteDate;
        }

        return true;
    }
}
