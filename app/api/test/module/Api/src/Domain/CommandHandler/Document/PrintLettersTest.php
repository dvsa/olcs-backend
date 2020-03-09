<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\PrintLetters;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetters as PrintLettersCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

class PrintLettersTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintLetters();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $result = new Result();

        $this->expectedSideEffect(PrintLetter::class, [], $result, 3);

        $data = [
            'ids' => [1, 2, 3],
            'method' => 'email'
        ];
        $cmd = PrintLettersCmd::create($data);

        $this->sut->handleCommand($cmd);

        $expectedResult = [
            'id' => [],
            'messages' => []
        ];
        $this->assertEquals($expectedResult, $result->toArray());
    }
}
