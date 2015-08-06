<?php

namespace Dvsa\Olcs\Api\Service\Submission\Context\Sections;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousHearingBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing;

/**
 * Class CaseSummary
 * @package Dvsa\Olcs\Api\Service\Submission\Context\CaseSummary
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class CaseSummary extends AbstractContext
{
    private static $bundle = [];

    public function provide(SubmissionEntity $submission, \ArrayObject $context)
    {
        $params = [
            'id' => $publication->getPi()->getId(),
            'hearingDate' => $context->offsetGet('hearingDate'),
            'bundle' => self::$bundle
        ];

        /** @var PiHearing $previousHearing */
        $query = PreviousHearingBundle::create($params);
        $previousHearing = $this->handleQuery($query);

        if (!empty($previousHearing)) {
            $context->offsetSet('case-summary', $date->format('d F Y'));
        }

        return $context;
    }
}
