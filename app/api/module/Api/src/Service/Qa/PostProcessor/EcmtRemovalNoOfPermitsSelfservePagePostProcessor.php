<?php

namespace Dvsa\Olcs\Api\Service\Qa\PostProcessor;

use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;

class EcmtRemovalNoOfPermitsSelfservePagePostProcessor implements SelfservePagePostProcessorInterface
{
    public function process(SelfservePage $page)
    {
        $translateableText = $page->getQuestionText()->getGuidance()->getTranslateableText();
        //$translateableText->setParameter(0, '352');
    }
}
