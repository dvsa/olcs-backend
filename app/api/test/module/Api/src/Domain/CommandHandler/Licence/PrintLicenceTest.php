<?php

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
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\PrintLicence
 */
class PrintLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintLicence();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    public function testHandleCommandFailNull()
    {
        $command = Cmd::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->andReturnNull();

        static::assertNull($this->sut->handleCommand($command));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHandleCommand($command, $isGoods, $isSpecialRestricted, $niFlag, array $expect)
    {
        /** @var LicenceEntity | m\MockInterface $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn($isGoods);
        $licence->shouldReceive('isSpecialRestricted')->andReturn($isSpecialRestricted);
        $licence->shouldReceive('getNiFlag')->andReturn($niFlag);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => $expect['docId'],
            'query' => ['licence' => 111],
            'description' => $expect['desc'],
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
            'dispatch' => true,
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dataProvider()
    {
        $command = Cmd::create(['id' => 111]);

        return [
            [
                'cmd' => $command,
                'isGoods' => true,
                'isSpecialRestricted' => false,
                'niFlag' => 'N',
                'expect' => [
                    'docId' => 1254,
                    'desc' => 'GV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => false,
                'isSpecialRestricted' => false,
                'niFlag' => 'N',
                'expect' => [
                    'docId' => 1255,
                    'desc' => 'PSV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => true,
                'isSpecialRestricted' => true,
                'niFlag' => 'N',
                'expect' => [
                    'docId' => 1254,
                    'desc' => 'GV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => false,
                'isSpecialRestricted' => true,
                'niFlag' => 'N',
                'expect' => [
                    'docId' => 1310,
                    'desc' => 'PSV-SR Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => true,
                'isSpecialRestricted' => false,
                'niFlag' => 'Y',
                'expect' => [
                    'docId' => 1512,
                    'desc' => 'GV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => false,
                'isSpecialRestricted' => false,
                'niFlag' => 'Y',
                'expect' => [
                    'docId' => 1516,
                    'desc' => 'PSV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => true,
                'isSpecialRestricted' => true,
                'niFlag' => 'Y',
                'expect' => [
                    'docId' => 1512,
                    'desc' => 'GV Licence',
                ],
            ],
            [
                'cmd' => $command,
                'isGoods' => false,
                'isSpecialRestricted' => true,
                'niFlag' => 'Y',
                'expect' => [
                    'docId' => 1518,
                    'desc' => 'PSV-SR Licence',
                ],
            ],
        ];
    }

    public function testHandleCommandDispatchFalse()
    {
        $command = Cmd::create(['id' => 111, 'dispatch' => false]);

        /** @var LicenceEntity | m\MockInterface $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn(false);
        $licence->shouldReceive('isSpecialRestricted')->andReturn(true);
        $licence->shouldReceive('getNiFlag')->andReturn('Y');

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => 1518,
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
            'dispatch' => false,
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
