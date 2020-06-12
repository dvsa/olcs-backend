<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class CertificatesAnswerSaver implements AnswerSaverInterface
{
    use AnyTrait;

    /** @var BaseAnswerSaver */
    private $baseAnswerSaver;

    /**
     * Create service instance
     *
     * @param BaseAnswerSaver $baseAnswerSaver
     *
     * @return CertificatesAnswerSaver
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
        $this->baseAnswerSaver->save($qaContext, $postData, Question::QUESTION_TYPE_BOOLEAN);
    }
}
