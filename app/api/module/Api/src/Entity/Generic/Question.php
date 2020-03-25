<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

/**
 * Question Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="question",
 *    indexes={
 *        @ORM\Index(name="fk_question_question_type_ref_data_id", columns={"question_type"})
 *    }
 * )
 */
class Question extends AbstractQuestion
{
    // Standard question types
    const FORM_CONTROL_TYPE_CHECKBOX = 'form_control_checkbox';
    const FORM_CONTROL_TYPE_RADIO = 'form_control_radio';
    const FORM_CONTROL_TYPE_TEXT = 'form_control_text';

    // Custom question types
    const FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS = 'form_control_ecmt_rem_no_permits';
    const FORM_CONTROL_ECMT_REMOVAL_PERMIT_START_DATE = 'form_control_ecmt_rem_per_st_dat';
    const FORM_CONTROL_ECMT_SHORT_TERM_NO_OF_PERMITS = 'form_control_ecmt_st_no_permits';
    const FORM_CONTROL_ECMT_SHORT_TERM_PERMIT_USAGE = 'form_control_ecmt_st_perm_usage';
    const FORM_CONTROL_ECMT_SHORT_TERM_INTERNATIONAL_JOURNEYS = 'form_control_ecmt_st_int_journ';
    const FORM_CONTROL_ECMT_SHORT_TERM_RESTRICTED_COUNTRIES = 'form_control_ecmt_st_rest_count';
    const FORM_CONTROL_ECMT_SHORT_TERM_ANNUAL_TRIPS_ABROAD = 'form_control_ecmt_st_ann_trips';
    const FORM_CONTROL_ECMT_SHORT_TERM_SECTORS = 'form_control_ecmt_st_sectors';
    const FORM_CONTROL_CERT_ROADWORTHINESS_MOT_EXPIRY_DATE = 'form_control_cert_road_mot_exp';
    const FORM_CONTROL_COMMON_CERTIFICATES = 'form_control_common_certificates';
    const FORM_CONTROL_ECMT_SHORT_TERM_EARLIEST_PERMIT_DATE = 'form_control_ecmt_st_ear_per_dat';
    const FORM_CONTROL_ECMT_ANNUAL_2018_NO_OF_PERMITS = 'form_control_ecmt_an_2018_nop';
    const FORM_CONTROL_BILATERAL_PERMIT_USAGE = 'form_control_bi_per_usage';
    const FORM_CONTROL_BILATERAL_CABOTAGE_ONLY = 'form_control_bi_cab_only';
    const FORM_CONTROL_BILATERAL_CABOTAGE_STD_AND_CABOTAGE = 'form_control_bi_cab_std_and_cab';

    // Question data types
    const QUESTION_TYPE_STRING = 'question_type_string';
    const QUESTION_TYPE_INTEGER = 'question_type_integer';
    const QUESTION_TYPE_BOOLEAN = 'question_type_boolean';
    const QUESTION_TYPE_DATE = 'question_type_date';
    const QUESTION_TYPE_CUSTOM = 'question_type_custom';

    // Question ids
    const QUESTION_ID_SHORT_TERM_ANNUAL_TRIPS_ABROAD = 10;
    const QUESTION_ID_REMOVAL_PERMIT_START_DATE = 13;
    const QUESTION_ID_ROADWORTHINESS_VEHICLE_MOT_EXPIRY = 20;
    const QUESTION_ID_ROADWORTHINESS_TRAILER_MOT_EXPIRY = 25;

    /**
     * Is custom
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getQuestionType()->getId() === self::QUESTION_TYPE_CUSTOM;
    }

    /**
     * Get an active question text
     *
     * @param \DateTime $dateTime DateTime to check against
     *
     * @return QuestionText|null
     */
    public function getActiveQuestionText(\DateTime $dateTime = null)
    {
        if (!isset($dateTime)) {
            // get the latest active if specific datetime not provided
            $dateTime = new DateTime();
        }

        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->lte('effectiveFrom', $dateTime));
        $criteria->orderBy(['effectiveFrom' => Criteria::DESC]);
        $criteria->setMaxResults(1);

        $activeQuestionTexts = $this->getQuestionTexts()->matching($criteria);

        return !$activeQuestionTexts->isEmpty() ? $activeQuestionTexts->first() : null;
    }

    /**
     * Get the decoded form of the option source for this question
     *
     * @return array
     */
    public function getDecodedOptionSource()
    {
        return json_decode($this->optionSource, true);
    }

    /**
     * Get the answer corresponding to a question for a non-custom question type
     *
     * @param QaEntityInterface $qaEntity
     * @param \DateTime $applicationPathLockedOn
     *
     * @return mixed|null
     */
    public function getStandardAnswer(QaEntityInterface $qaEntity, \DateTime $applicationPathLockedOn)
    {
        $activeQuestionText = $this->getActiveQuestionText($applicationPathLockedOn);

        if (!isset($activeQuestionText)) {
            return null;
        }

        /** @var Answer $answer answers are indexed by question_text_id */
        $answer = $qaEntity->getAnswers()->get($activeQuestionText->getId());

        if (!isset($answer)) {
            return null;
        }

        return $answer->getValue();
    }
}
