<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

interface AnswerSummaryProviderInterface
{
    /**
     * Return the template name to be used in building the answer summary
     *
     * @return string
     */
    public function getTemplateName();

    /**
     * Return the template variables to be used in building the answer summary
     *
     * @param QaContext $qaContext
     * @param bool $isSnapshot
     *
     * @return array
     */
    public function getTemplateVariables(QaContext $qaContext, $isSnapshot);

    /**
     * Whether this answer summary provider supports the specified entity
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function supports(QaEntityInterface $qaEntity);
}
