<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

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
     * @param ElementInterface $element
     * @param bool $isSnapshot
     *
     * @return array
     */
    public function getTemplateVariables(QaContext $qaContext, ElementInterface $element, $isSnapshot);

    /**
     * Whether this answer summary provider supports the specified entity
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function supports(QaEntityInterface $qaEntity);

    /**
     * Whether this answer summary provider should include slug
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function shouldIncludeSlug(QaEntityInterface $qaEntity);
}
