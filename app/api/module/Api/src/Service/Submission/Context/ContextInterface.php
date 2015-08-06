<?php

namespace Dvsa\Olcs\Api\Service\Submission\Context;

use Dvsa\Olcs\Api\Entity\Submission\Submission;

/**
 * Interface ContextInterface
 * @package Dvsa\Olcs\Api\Service\Submission\Context
 */
interface ContextInterface
{
    public function provide(Submission $submission, \ArrayObject $context);
}
