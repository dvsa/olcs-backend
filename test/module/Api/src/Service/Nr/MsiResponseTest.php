<?php

namespace OlcsTest\Nr\Filter\Format;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\Nr\MsiResponse;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class MsiDetailTest
 * @package OlcsTest\Nr\Filter\Format
 */
class MsiDetailTest extends MockeryTestCase
{

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCreateThrowsException()
    {
        $cases = m::mock(CasesEntity::class);
        $cases->shouldReceive('canSendMsiResponse')->once()->andReturn(false);

        $sut = new MsiResponse();
        $sut->create($cases);
    }

    /**
     * Tests create
     *
     * @param $licence
     * @param $authority
     *
     * @dataProvider createDataProvider
     */
    public function testCreate($licence, $authority)
    {
        $siPenaltyTypeId1 = 101;
        $siPenaltyTypeId2 = 102;
        $reasonNotImposed = 'reason not imposed';
        $memberStateCode = 'PL';
        $notificationNumber = 214124;
        $erruOriginatingAuthority = 'originating authority';
        $erruTransportUndertaking = 'transport undertaking';
        $startDate = '2015-01-31';
        $endDate = '2015-05-16';
        $workflowId = "FB4F5CE2-4D38-4AB8-8185-03947C939393";

        $penalty1 = m::mock(SiPenaltyEntity::class)->makePartial();
        $penalty1->shouldReceive('getSiPenaltyType->getId')->once()->andReturn($siPenaltyTypeId1);
        $penalty1->shouldReceive('getStartDate')->once()->andReturn(null);
        $penalty1->shouldReceive('getEndDate')->once()->andReturn(null);
        $penalty1->shouldReceive('getImposed')->once()->andReturn('N');
        $penalty1->shouldReceive('getReasonNotImposed')->once()->andReturn($reasonNotImposed);

        $penalty2 = m::mock(SiPenaltyEntity::class);
        $penalty2->shouldReceive('getSiPenaltyType->getId')->once()->andReturn($siPenaltyTypeId2);
        $penalty2->shouldReceive('getStartDate')->once()->andReturn($startDate);
        $penalty2->shouldReceive('getEndDate')->once()->andReturn($endDate);
        $penalty2->shouldReceive('getImposed')->once()->andReturn('Y');
        $penalty2->shouldReceive('getReasonNotImposed')->never();

        $appliedPenalties = new ArrayCollection([$penalty1, $penalty2]);

        $seriousInfringement = m::mock(SiEntity::class);
        $seriousInfringement->shouldReceive('getAppliedPenalties')->once()->andReturn($appliedPenalties);
        $seriousInfringement->shouldReceive('getNotificationNumber')->once()->andReturn($notificationNumber);
        $seriousInfringement->shouldReceive('getWorkflowId')->once()->andReturn($workflowId);
        $seriousInfringement->shouldReceive('getMemberStateCode->getId')->once()->andReturn($memberStateCode);

        $seriousInfringements = new ArrayCollection([$seriousInfringement]);

        $cases = m::mock(CasesEntity::class);
        $cases->shouldReceive('canSendMsiResponse')->once()->andReturn(true);
        $cases->shouldReceive('getSeriousInfringements')->once()->andReturn($seriousInfringements);
        $cases->shouldReceive('getErruTransportUndertakingName')->once()->andReturn($erruTransportUndertaking);
        $cases->shouldReceive('getErruOriginatingAuthority')->once()->andReturn($erruOriginatingAuthority);
        $cases->shouldReceive('getLicence')->once()->andReturn($licence);

        $sut = new MsiResponse();
        $actualOutput = $sut->create($cases);

        $header = [
            'name' => 'Header',
            'attributes' => [
                'technicalId' => $sut->getTechnicalId(),
                'workflowId' => $workflowId,
                'sentAt' => $sut->getResponseDateTime(),
                'from' => 'UK'
            ],
            'nodes' => [
                0 => [
                    'name' => 'To',
                    'nodes' => [
                        0 => [
                            'name' => 'MemberState',
                            'attributes' => [
                                'code' => $memberStateCode
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $body = [
            'name' => 'Body',
            'attributes' => [
                'businessCaseId' => $notificationNumber,
                'originatingAuthority' => $erruOriginatingAuthority,
                'licensingAuthority' => $authority,
                'responseDateTime' => $sut->getResponseDateTime()
            ],
            'nodes' => [
                0 => [
                    'name' => 'TransportUndertaking',
                    'attributes' => [
                        'name' => $erruTransportUndertaking
                    ],
                    'nodes' => [
                        0 => [
                            'name' => 'PenaltyImposed',
                            'attributes' => [
                                'authorityImposingPenalty' => $authority,
                                'penaltyTypeImposed' => $siPenaltyTypeId1,
                                'isImposed' => 'No',
                                'reasonNotImposed' => $reasonNotImposed
                            ]
                        ],
                        1 => [
                            'name' => 'PenaltyImposed',
                            'attributes' => [
                                'authorityImposingPenalty' => $authority,
                                'penaltyTypeImposed' => $siPenaltyTypeId2,
                                'isImposed' => 'Yes',
                                'startDate' => $startDate,
                                'endDate' => $endDate
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expectedOutput = [
            'Header' => $header,
            'Body' => $body
        ];

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    /**
     * Data provider for testCreate
     *
     * @return array
     */
    public function createDataProvider()
    {
        return [
            [null, MsiResponse::AUTHORITY_TRU],
            ['licence', MsiResponse::AUTHORITY_TC]
        ];
    }
}
