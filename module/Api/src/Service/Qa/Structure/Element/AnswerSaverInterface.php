<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

interface AnswerSaverInterface
{
    /**
     * Save an answer corresponding to the supplied context to persistent storage using the supplied post data as the
     * source of the answer. Optionally returns a string representing what the frontend should do after saving
     *
     * @param QaContext $qaContext
     * @param array $postData
     *
     * @return string|null
     */
    public function save(QaContext $qaContext, array $postData);

    /**
     * Whether this answer saver supports the specified entity
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function supports(QaEntityInterface $qaEntity);
}
