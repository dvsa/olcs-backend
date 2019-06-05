<?php

namespace Dvsa\Olcs\Api\Service\Qa\PostProcessor;

use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;

class NullSelfservePagePostProcessor implements SelfservePagePostProcessorInterface
{
    public function process(SelfservePage $page)
    {
    }
}
