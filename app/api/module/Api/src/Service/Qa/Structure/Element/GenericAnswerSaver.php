<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class GenericAnswerSaver implements AnswerSaverInterface
{
    use AnyTrait;

    /** @var BaseAnswerSaver */
    private $baseAnswerSaver;

    /**
     * Create service instance
     *
     * @param BaseAnswerSaver $baseAnswerSaver
     *
     * @return GenericAnswerSaver
     */
    public function __construct(BaseAnswerSaver $baseAnswerSaver)
    {
        $this->baseAnswerSaver = $baseAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $this->baseAnswerSaver->save($qaContext, $postData);
    }
}
