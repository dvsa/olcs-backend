<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateCompanySubsidiary
 */
class UpdateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    const ID = 666;
    const APP_ID = 8888;
    const LICENCE_ID = 7777;

    /** @var  UpdateCompanySubsidiary|m\MockInterface */
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock(UpdateCompanySubsidiary::class . '[update]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('CompanySubsidiary', Repository\CompanySubsidiary::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'application' => self::APP_ID,
            'name' => 'unit_Name',
            'companyNo' => '12345678',
        ];
        $command = TransferCmd\Application\CreateCompanySubsidiary::create($data);

        //  mock create result
        $result = (new Result())
            ->setFlag('hasChanged', true)
            ->addMessage('Company Subsidiary updated');

        $this->sut->shouldReceive('update')->once()->with($command)->andReturn($result);

        //  mock Application completion
        $dataAppComplete = [
            'id' => self::APP_ID,
            'section' => 'businessDetails',
            'data' => [
                'hasChanged' => true,
            ],
        ];

        $resultAppComplete = (new Result())
            ->addMessage('Section updated');

        $this->expectedSideEffect(
            DomainCmd\Application\UpdateApplicationCompletion::class, $dataAppComplete, $resultAppComplete
        );

        //  call & check
        $actual = $this->sut->handleCommand($command);

        static::assertInstanceOf(Result::class, $actual);

        $expected = [
            'id' => [],
            'messages' => [
                'Company Subsidiary updated',
                'Section updated',
            ]
        ];
        static::assertEquals($expected, $actual->toArray());
    }
}
