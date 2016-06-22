<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateBusinessDetails;
use Dvsa\Olcs\Transfer\Command\Application\UpdateBusinessDetails as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateBusinessDetails as LicenceCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateBusinessDetailsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateBusinessDetails();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        $this->references = [

        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111,
            'licence' => 222,
            'version' => 1
        ];

        $command = Cmd::create($data);

        $licenceCmdData = [
            'id' => 222,
            'version' => 1,
            'name' => null,
            'natureOfBusiness' => null,
            'companyOrLlpNo' => null,
            'registeredAddress' => null,
            'tradingNames' => [],
            'partial' => null
        ];

        $result1 = new Result();
        $result1->addMessage('Business Details updated');
        $result1->setFlag('hasChanged', true);

        $this->expectedSideEffect(LicenceCmd::class, $licenceCmdData, $result1);

        $updateData = ['id' => 111, 'section' => 'businessDetails'];

        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $updateData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Business Details updated',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
