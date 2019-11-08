<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Dvsa\Olcs\Api\Entity\Task\Task;

class SubmissionTaskProperties
{
    const CATEGORY = Task::CATEGORY_PERMITS;
    const SUBCATEGORY = Task::SUBCATEGORY_APPLICATION;
}
