<?php

/**
 * Delete Impounding Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Impounding;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Impounding\DeleteImpounding;
use Dvsa\Olcs\Api\Domain\Repository\Impounding;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Impounding\DeleteImpounding as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Impounding as ImpoundingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue;

/**
 * Delete Impounding Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class DeleteImpoundingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteImpounding();
        $this->mockRepo('Impounding', Impounding::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'impt_hearing'
        ];

        $this->references = [
            Cases::class => [
                24 => m::mock(Cases::class)
            ],
            PiVenue::class => [
                8 => m::mock(PiVenue::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            [
                'id' => 99,
                'version' => 1,
                'applicationReceiptDate' => '2014-06-09',
                'closeDate' => '2015-05-28T10:53:34+0100',
                'DeletedOn' => '2015-05-28T10:53:34+0100',
                'hearingDate' => '2014-06-10T15:45:00+0100',
                'lastModifiedOn' => '2015-05-28T10:53:34+0100',
                'notes' => 'Some notes - db default',
                'outcomeSentDate' => '2014-06-11',
                'piVenueOther' => null,
                'piVenue' => 8,
                'vrm' => 'vrm1',
                'impoundingLegislationTypes' => [
                    'imlgis_type_goods_ni1',
                    'imlgis_type_goods_ni2'
                ],
                'impoundingType' => 'impt_hearing',
                'outcome' => 'impo_returned',
                'presidingTc' => 1
            ]
        );

        /** @var ImpoundingEntity $impounding */
        $impounding = m::mock(ImpoundingEntity::class)->makePartial();
        $impounding->setId($command->getId());

        $this->repoMap['Impounding']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($impounding)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('delete')
            ->with(m::type(ImpoundingEntity::class))
            ->andReturnUsing(
                function (ImpoundingEntity $impounding) use (&$imp) {
                    $imp = $impounding;
                    $impounding->setId(99);
                }
            )
            ->shouldReceive('commit')
            ->once();


        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Impounding deleted', $result->getMessages());
    }
}
