<?php

namespace Dvsa\OlcsTest\Snapshot\View\Helper;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Snapshot\View\Helper\AnswerFormatter;
use Mockery as m;
use Zend\View\Renderer\RendererInterface;

/**
 * Class AnswerFormatterTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class AnswerFormatterTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /** @var AnswerFormatter */
    private $sut;

    /** @var RendererInterface|m\MockInterface */
    private $view;

    public function setUp()
    {
        $this->view = m::mock(RendererInterface::class);
        $this->sut = new AnswerFormatter();
        $this->sut->setView($this->view);
    }

    public function testInvokeBoolean()
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            'answer' => [
                true, false, 1, 0,
            ],
            'escape' => true,
        ];

        $this->view->shouldReceive('translate')
            ->twice()
            ->with('Yes', 'snapshot')
            ->andReturn('translated yes');

        $this->view->shouldReceive('escapeHtml')
            ->twice()
            ->with('translated yes')
            ->andReturn('escaped and translated yes');

        $this->view->shouldReceive('translate')
            ->twice()
            ->with('No', 'snapshot')
            ->andReturn('translated no');

        $this->view->shouldReceive('escapeHtml')
            ->twice()
            ->with('translated no')
            ->andReturn('escaped and translated no');

        $expected =
            'escaped and translated yes' . AnswerFormatter::SEPARATOR .
            'escaped and translated no' . AnswerFormatter::SEPARATOR .
            'escaped and translated yes' . AnswerFormatter::SEPARATOR .
            'escaped and translated no';

        self::assertEquals($expected, $this->sut->__invoke($input));
    }

    public function testInvokeBooleanNoEscape()
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            'answer' => [
                true, false, 1, 0,
            ],
            'escape' => false,
        ];

        $this->view->shouldReceive('translate')
            ->twice()
            ->with('Yes', 'snapshot')
            ->andReturn('translated yes');

        $this->view->shouldReceive('translate')
            ->twice()
            ->with('No', 'snapshot')
            ->andReturn('translated no');

        $this->view->shouldReceive('escapeHtml')->never();

        $expected =
            'translated yes' . AnswerFormatter::SEPARATOR .
            'translated no' . AnswerFormatter::SEPARATOR .
            'translated yes' . AnswerFormatter::SEPARATOR .
            'translated no';

        self::assertEquals($expected, $this->sut->__invoke($input));
    }

    public function testInvokeInteger()
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => Question::QUESTION_TYPE_INTEGER,
            'answer' => [
                1, 0, 999,
            ]
        ];

        $output = '1' . AnswerFormatter::SEPARATOR . '0' . AnswerFormatter::SEPARATOR . '999';

        self::assertEquals($output, $this->sut->__invoke($input));
    }

    /**
     * Tests a single answer is converted to an array containing one answer and is still processed correctly
     */
    public function testInvokeWithAnswerNotArray()
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => Question::QUESTION_TYPE_INTEGER,
            'answer' => 1,
        ];

        self::assertEquals(1, $this->sut->__invoke($input));
    }

    /**
     * Tests answer rendered as expected for string/custom
     *
     * @dataProvider dpInvokeOther
     */
    public function testInvokeOther($questionType)
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => $questionType,
            'answer' => [
                1, 0, 'text',
            ],
            'escape' => true,
        ];

        $this->view->shouldReceive('translate')
            ->times(3)
            ->andReturnUsing(
                function ($arg) {
                    return 'translated ' . $arg;
                }
            );

        $this->view->shouldReceive('escapeHtml')
            ->times(3)
            ->andReturnUsing(
                function ($arg) {
                    return 'escaped and ' . $arg;
                }
            );

        $expected =
            'escaped and translated 1' . AnswerFormatter::SEPARATOR .
            'escaped and translated 0' . AnswerFormatter::SEPARATOR .
            'escaped and translated text';

        self::assertEquals($expected, $this->sut->__invoke($input));
    }

    /**
     * Tests answer rendered as expected for string/custom
     *
     * @dataProvider dpInvokeOther
     */
    public function testInvokeOtherNoEscape($questionType)
    {
        $input = [
            'question' => 'qanda.question',
            'questionType' => $questionType,
            'answer' => [
                1, 0, 'text',
            ],
            'escape' => false,
        ];

        $this->view->shouldReceive('translate')
            ->times(3)
            ->andReturnUsing(
                function ($arg) {
                    return 'translated ' . $arg;
                }
            );

        $this->view->shouldReceive('escapeHtml')->never();

        $expected =
            'translated 1' . AnswerFormatter::SEPARATOR .
            'translated 0' . AnswerFormatter::SEPARATOR .
            'translated text';

        self::assertEquals($expected, $this->sut->__invoke($input));
    }

    public function dpInvokeOther()
    {
        return [
            [Question::QUESTION_TYPE_STRING],
            [Question::QUESTION_TYPE_CUSTOM],
        ];
    }
}
