<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Approve as ApproveHandler;
use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Licence\SurrenderLicence;
use Dvsa\Olcs\Transfer\Command\Surrender\Approve as ApproveCommand;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateCommand;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class ApproveTest extends CommandHandlerTestCase
{
    /**
     * @var ApproveHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApproveHandler();
        $this->refData = [];
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::SURRENDER_STATUS_APPROVED,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {

        $now = new \DateTime();
        $data = [
            'id' => 45,
            'surrenderDate' => $now->format('Y-m-d')
        ];

        $command = ApproveCommand::create($data);

        $this->expectedSideEffect(
            UpdateCommand::class,
            [
                'id' => 45,
                'status' => RefData::SURRENDER_STATUS_APPROVED,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            SurrenderLicence::class,
            [
                'id' => 45,
                'surrenderDate' => $data['surrenderDate'],
                'terminated' => false
            ],
            new Result()
        );

        $licenceEntity = m::mock(Licence::class);
        $licenceEntity->shouldReceive('getCreatedBy->getId')->andReturn(5);
        $licenceEntity->shouldReceive('getGoodsOrPsv->getId')->andReturn($data['goodsOrPsv']);
        $licenceEntity->shouldReceive('getLicenceType->getId')->andReturn($data['licType']);
        $licenceEntity->shouldReceive('isNi')->andReturn($data['isNi']);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => $expected['template'],
                'query' => [
                    'licence' => 45,
                    'user' => 5
                ],
                'description' => $expected['description'],
                'licence' => 45,
                'category' => Category::CATEGORY_LICENSING,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SURRENDER,
                'isExternal' => true,
                'isScan' => false,
                'dispatch' => true
            ]
        );

        if () {
            $this->expectException(Exception::class);
        }

        $this->sut->handleCommand($command);
    }

    public function dpTestHandleCommand()
    {
        return [
            'psv_sn_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_PSV',
                    'description' => 'PSV - Surrender actioned letter',
                ]
            ],
            'psv_si_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_PSV',
                    'description' => 'PSV - Surrender actioned letter',
                ]
            ],
            'psv_restricted_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                    'licType' => Licence::LICENCE_TYPE_RESTRICTED,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_PSV',
                    'description' => 'PSV - Surrender actioned letter',
                ]
            ],
            'gv_sn_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_GV_GB',
                    'description' => 'GV - Surrender actioned letter',
                ]
            ],
            'gv_si_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_GV_GB',
                    'description' => 'GV - Surrender actioned letter',
                ]
            ],
            'gv_restricted_notNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_RESTRICTED,
                    'isNi' => false,
                ],
                'expected' => [
                    'template' => 'GB/SURRENDER_LETTER_TO_OPERATOR_GV_GB',
                    'description' => 'GV - Surrender actioned letter',
                ]
            ],
            'gv_sn_isNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'isNi' => true,
                ],
                'expected' => [
                    'template' => 'NI/SURRENDER_LETTER_TO_OPERATOR_GV_NI',
                    'description' => 'GV - Surrender actioned letter (NI)',
                ]
            ],
            'gv_si_isNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'isNi' => true,
                ],
                'expected' => [
                    'template' => 'NI/SURRENDER_LETTER_TO_OPERATOR_GV_NI',
                    'description' => 'GV - Surrender actioned letter (NI)',
                ]
            ],
            'gv_restricted_isNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licType' => Licence::LICENCE_TYPE_RESTRICTED,
                    'isNi' => true,
                ],
                'expected' => [
                    'template' => 'NI/SURRENDER_LETTER_TO_OPERATOR_GV_NI',
                    'description' => 'GV - Surrender actioned letter (NI)',
                ]
            ],
            'psv_restricted_isNi' => [
                'data' => [
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                    'licType' => Licence::LICENCE_TYPE_RESTRICTED,
                    'isNi' => true,
                ],
                'expected' => [
                ]
            ],
        ];
    }
}
