<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\NoResultException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\Answer as Entity;

/**
 * Answer
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class Answer extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Get an answer to the given application step
     *
     * @param int $questionId
     * @param int $irhpApplicationId
     *
     * @return mix|null
     */
    public function fetchByQuestionIdAndIrhpApplicationId($questionId, $irhpApplicationId)
    {
        try {
            return $this->getEntityManager()->createQueryBuilder()
                ->select('a')
                ->from(Entity::class, 'a')
                ->innerJoin('a.questionText', 'qt')
                ->where('IDENTITY(qt.question) = ?1')
                ->andWhere('IDENTITY(a.irhpApplication) = ?2')
                ->setParameter(1, $questionId)
                ->setParameter(2, $irhpApplicationId)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new NotFoundException('Answer not found');
        }
    }
}
