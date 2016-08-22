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

    /**
     * @dataProvider dataProvider
     *
     * @param $isGoods
     * @param $isSpecialRestricted
     * @param $niFlag
     * @param $expectedDocumentId
     * @param $expectedDesc
     */
    public function testHandleCommand($isGoods, $isSpecialRestricted, $niFlag, $expectedDocumentId, $expectedDesc)
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);
        $licence->shouldReceive('isGoods')->andReturn($isGoods);
        $licence->shouldReceive('isSpecialRestricted')->andReturn($isSpecialRestricted);
        $licence->shouldReceive('getNiFlag')->andReturn($niFlag);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'template' => $expectedDocumentId,
            'query' => ['licence' => 111],
            'description' => $expectedDesc,
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

    public function dataProvider()
    {
        return [
            // isGoods, isSpecialRestricted, Ni flag 'N' or 'Y', expected document ID, expected description
            [true,      false,             'N',                1254,                 'GV Licence'],
            [false,     false,             'N',                1255,                 'PSV Licence'],
            [true,      true,              'N',                1254,                 'GV Licence'],
            [false,     true,              'N',                1310,                 'PSV-SR Licence'],
            [true,      false,             'Y',                1512,                 'GV Licence'],
            [false,     false,             'Y',                1516,                 'PSV Licence'],
            [true,      true,              'Y',                1512,                 'GV Licence'],
            [false,     true,              'Y',                1518,                 'PSV-SR Licence'],
        ];
    }
}
