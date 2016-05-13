<?php

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\Category;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\PrintLicence;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\PrintLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrintLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintLicence();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    public function testHandleCommandGoods()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => 'GV_LICENCE_V1',
            'query' => ['licence' => 111],
            'description' => 'GV Licence',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null,
            'dispatch' => true
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvSr()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn(false);
        $licence->shouldReceive('isSpecialRestricted')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => 'PSVSRLicence',
            'query' => ['licence' => 111],
            'description' => 'PSV-SR Licence',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null,
            'dispatch' => true
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsv()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn(false);
        $licence->shouldReceive('isSpecialRestricted')->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => 'PSV_LICENCE_V1',
            'query' => ['licence' => 111],
            'description' => 'PSV Licence',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null,
            'dispatch' => true
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
