<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Subjects;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\MessagingSubject as SubjectRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class All extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [SubjectRepo::class];

    public function handleQuery(QueryInterface $query): array
    {
        $subjectRepository = $this->getRepo(SubjectRepo::class);

        $subjectsQuery = $subjectRepository->getAll($query);
        $subjects = $subjectRepository->fetchAll($subjectsQuery);

        foreach ($subjects as $key => $value) {
            $subjects['label'] = $value['subject'];
            $subjects['value'] = $value['category_id'];
            $subjects['category'] = $value['category_id'];
            $subjects['sub_category'] = $value['sub_category_id'];
        }

        return [
            'result' => $subjects,
        ];
    }
}
