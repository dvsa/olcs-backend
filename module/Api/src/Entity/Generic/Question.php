<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;

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
    const QUESTION_TYPE_CUSTOM = 'question_type_custom';

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
     * @param \DateTime $dateTime DateTime to change against
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
        $criteria->where(
            $criteria->expr()->lte(
                'effectiveFrom',
                $dateTime->format(DateTime::ISO8601)
            )
        );
        $criteria->orderBy(['effectiveFrom' => Criteria::DESC]);
        $criteria->setMaxResults(1);

        $activeQuestionTexts = $this->getQuestionTexts()->matching($criteria);

        return !$activeQuestionTexts->isEmpty() ? $activeQuestionTexts->first() : null;
    }
}
