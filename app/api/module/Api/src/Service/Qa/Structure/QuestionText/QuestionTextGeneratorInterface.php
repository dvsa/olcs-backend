<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

interface QuestionTextGeneratorInterface
{
    /**
     * Build and return an QuestionText instance using the appropriate data sources
     *
     * @param QaContext $qaContext
     *
     * @return QuestionText
     */
    public function generate(QaContext $qaContext);

    /**
     * Whether this question text generator supports the specified entity
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function supports(QaEntityInterface $qaEntity);
}
