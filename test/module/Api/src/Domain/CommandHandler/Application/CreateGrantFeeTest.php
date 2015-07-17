<?php

/**
 * Create Grant Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateGrantFee;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateGrantFee as Cmd;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator as DocGenerator;

/**
 * Create Grant Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateGrantFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateGrantFee();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocGenerator::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $params = [
            'fee' => 123,
            'application' => 111,
            'licence' => 222
        ];

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn('12345678')
            ->shouldReceive('getSize')
            ->andReturn(100);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->with('FEE_REQ_GRANT_GV', $params)
            ->andReturn($file);

        $dtoData = ['id' => 111, 'feeTypeFeeType' => FeeType::FEE_TYPE_GRANT];
        $result1 = new Result();
        $result1->addId('fee', 123);
        $result1->addMessage('CreateApplicationFee');
        $this->expectedSideEffect(CreateApplicationFee::class, $dtoData, $result1);

        $docData = [
            'identifier' => '12345678',
            'size' => 100,
            'description' => 'Goods Grant Fee Request',
            'filename'    => 'Goods_Grant_Fee_Request.rtf',
            'application' => 111,
            'licence'     => 222,
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_FEE_REQUEST,
            'isExternal'  => false,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isReadOnly' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result2 = new Result();
        $result2->addMessage('DispatchDocument');
        $this->expectedSideEffect(DispatchDocument::class, $docData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 123
            ],
            'messages' => [
                'CreateApplicationFee',
                'DispatchDocument'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
