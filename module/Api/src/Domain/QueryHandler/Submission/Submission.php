<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Submission;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;

/**
 * Submission
 */
final class Submission extends AbstractQueryHandler
{
    protected $repoServiceName = 'Submission';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        // retrieve reason even if deleted
        $repo->disableSoftDeleteable(
            [
                \Dvsa\Olcs\Api\Entity\Pi\Reason::class
            ]
        );

        $submission = $repo->fetchUsingId($query);

        return $this->result(
            $submission,
            [
                'case',
                'recipientUser' => [
                    'contactDetails' => [
                        'person'
                    ]
                ],
                'senderUser' => [
                    'contactDetails' => [
                        'person'
                    ]
                ],
                'documents' => [
                    'category',
                    'subCategory'
                ],
                'submissionSectionComments' => [
                    'submissionSection'
                ],
                'submissionActions' => [
                    'actionTypes',
                    'reasons'
                ]
            ],
            [
                'canClose' => $submission->canClose(),
                'isClosed' => $submission->isClosed(),
                'canReopen' => $submission->canReopen(),
                'submissionTypeTitle' => $this->getSubmissionTypeTitle($submission),
                'isNi' => $submission->isNi()
            ]
        );
    }

    /**
     * Method that takes the submission type and looks up the ref data title for that submission type
     *
     * @param SubmissionEntity $submission
     * @return string
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getSubmissionTypeTitle(SubmissionEntity $submission)
    {
        $titleId = str_replace('_o_', '_t_', $submission->getSubmissionType()->getId());
        return $this->getRepo()->getRefdataReference($titleId);
    }
}
