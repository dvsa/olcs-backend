<?php

namespace Dvsa\OlcsTest\Api\Entity\Generic;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Generic\Answer as Entity;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Mockery as m;
use RuntimeException;

/**
 * Answer Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class AnswerEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    public function testCreateNewForIrhpApplication()
    {
        $questionText = m::mock(QuestionText::class);
        $irhpApplication = m::mock(IrhpApplication::class);

        $entity = $this->entityClass::createNewForIrhpApplication($questionText, $irhpApplication);

        $this->assertInstanceOf($this->entityClass, $entity);
        $this->assertSame($questionText, $entity->getQuestionText());
        $this->assertSame($irhpApplication, $entity->getIrhpApplication());
    }

    /**
    * @dataProvider dpValueGetterAndSetter
    */
    public function testValueGetterAndSetter($questionType, $answerValue)
    {
        $this->assertNull($this->entity->getValue());

        $this->entity->setValue($questionType, $answerValue);

        $this->assertEquals($answerValue, $this->entity->getValue());
    }

    public function dpValueGetterAndSetter()
    {
        return [
            // string
            [Question::QUESTION_TYPE_STRING, 'abc'],
            [Question::QUESTION_TYPE_STRING, ''],
            [Question::QUESTION_TYPE_STRING, 123],
            [Question::QUESTION_TYPE_STRING, 0],
            [Question::QUESTION_TYPE_STRING, true],
            [Question::QUESTION_TYPE_STRING, false],
            [Question::QUESTION_TYPE_STRING, 'true'],
            [Question::QUESTION_TYPE_STRING, 'false'],
            // int
            [Question::QUESTION_TYPE_INTEGER, 'abc'],
            [Question::QUESTION_TYPE_INTEGER, ''],
            [Question::QUESTION_TYPE_INTEGER, 123],
            [Question::QUESTION_TYPE_INTEGER, 0],
            [Question::QUESTION_TYPE_INTEGER, true],
            [Question::QUESTION_TYPE_INTEGER, false],
            [Question::QUESTION_TYPE_INTEGER, 'true'],
            [Question::QUESTION_TYPE_INTEGER, 'false'],
            // bool
            [Question::QUESTION_TYPE_BOOLEAN, 'abc'],
            [Question::QUESTION_TYPE_BOOLEAN, ''],
            [Question::QUESTION_TYPE_BOOLEAN, 123],
            [Question::QUESTION_TYPE_BOOLEAN, 0],
            [Question::QUESTION_TYPE_BOOLEAN, true],
            [Question::QUESTION_TYPE_BOOLEAN, false],
            [Question::QUESTION_TYPE_BOOLEAN, 'true'],
            [Question::QUESTION_TYPE_BOOLEAN, 'false'],
        ];
    }

    public function testSetValueForCustomType()
    {
        $this->expectException(RuntimeException::class);

        $this->entity->setValue(Question::QUESTION_TYPE_CUSTOM, 'custom');
    }

    /**
    * @dataProvider dpIsEqualTo
    */
    public function testIsEqualTo($questionType, $answerValue, $checkValue, $expected)
    {
        $this->assertNull($this->entity->getValue());

        $this->entity->setValue($questionType, $answerValue);

        $this->assertEquals($expected, $this->entity->isEqualTo($checkValue));
    }

    public function dpIsEqualTo()
    {
        return [
            // matching values
            // string
            [Question::QUESTION_TYPE_STRING, 'abc', 'abc', true],
            [Question::QUESTION_TYPE_STRING, 'abc', true, true],
            [Question::QUESTION_TYPE_STRING, '', '', true],
            [Question::QUESTION_TYPE_STRING, 'false', true, true],
            [Question::QUESTION_TYPE_STRING, '', 0, true],
            [Question::QUESTION_TYPE_STRING, '', false, true],
            [Question::QUESTION_TYPE_STRING, '0', 0, true],
            [Question::QUESTION_TYPE_STRING, '0', false, true],
            // int
            [Question::QUESTION_TYPE_INTEGER, 123, 123, true],
            [Question::QUESTION_TYPE_INTEGER, 123, '123', true],
            [Question::QUESTION_TYPE_INTEGER, 0, 0, true],
            [Question::QUESTION_TYPE_INTEGER, 0, '0', true],
            [Question::QUESTION_TYPE_INTEGER, 0, false, true],
            [Question::QUESTION_TYPE_INTEGER, 1, true, true],
            [Question::QUESTION_TYPE_INTEGER, 123, true, true],
            // bool
            [Question::QUESTION_TYPE_BOOLEAN, true, true, true],
            [Question::QUESTION_TYPE_BOOLEAN, true, 1, true],
            [Question::QUESTION_TYPE_BOOLEAN, true, '1', true],
            [Question::QUESTION_TYPE_BOOLEAN, true, 'false', true],
            [Question::QUESTION_TYPE_BOOLEAN, true, 'abc', true],
            [Question::QUESTION_TYPE_BOOLEAN, false, false, true],
            [Question::QUESTION_TYPE_BOOLEAN, false, 0, true],
            [Question::QUESTION_TYPE_BOOLEAN, false, '0', true],
            // different values
            // string
            [Question::QUESTION_TYPE_STRING, 'abc', 'def', false],
            [Question::QUESTION_TYPE_STRING, '', 'def', false],
            [Question::QUESTION_TYPE_STRING, 'false', false, false],
            // int
            [Question::QUESTION_TYPE_INTEGER, 123, 987, false],
            // bool
            [Question::QUESTION_TYPE_BOOLEAN, true, false, false],
            [Question::QUESTION_TYPE_BOOLEAN, false, true, false],
        ];
    }

    public function testIsEqualToWhenValueNotSet()
    {
        $this->expectException(RuntimeException::class);

        $this->entity->isEqualTo('anything');
    }
}
