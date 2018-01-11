<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ProposeToRevoke;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ProposeToRevoke\UpdateProposeToRevokeSla;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke as ProposeToRevokeEntity;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Transfer\Command\Cases\ProposeToRevoke\UpdateProposeToRevokeSla as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Update ProposeToRevoke Test
 */
class UpdateProposeToRevokeSlaTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateProposeToRevokeSla();
        $this->mockRepo('ProposeToRevoke', ProposeToRevoke::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            PresidingTc::class => [
                1 => m::mock(PresidingTc::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'isSubmissionRequiredForApproval' => 1,
            'approvalSubmissionIssuedDate' => '2017-01-01',
            'approvalSubmissionReturnedDate' => '2017-01-01',
            'approvalSubmissionPresidingTc' => 1,
            'iorLetterIssuedDate' => '2017-01-01',
            'operatorResponseDueDate' => '2017-01-01',
            'operatorResponseReceivedDate' => '2017-01-01',
            'isSubmissionRequiredForAction' => 0,
            'finalSubmissionIssuedDate' => '2017-01-01',
            'finalSubmissionReturnedDate' => '2017-01-01',
            'finalSubmissionPresidingTc' => 1,
            'actionToBeTaken' => 1,
            'revocationLetterIssuedDate' => '2017-01-01',
            'nfaLetterIssuedDate' => '2017-01-01',
            'warningLetterIssuedDate' => '2017-01-01',
            'piAgreedDate' => '2017-01-01',
            'otherActionAgreedDate' => '2017-01-01',
        ];

        $command = Cmd::create($data);

        /** @var ProposeToRevokeEntity|m\MockInterface $proposeToRevoke */
        $proposeToRevoke = m::mock(ProposeToRevokeEntity::class)->makePartial();
        $proposeToRevoke->setId(1);

        $this->repoMap['ProposeToRevoke']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($proposeToRevoke);

        $this->repoMap['ProposeToRevoke']->shouldReceive('save')
            ->once()
            ->with(m::type(ProposeToRevokeEntity::class))
            ->andReturnSelf();

        $result = $this->sut->handleCommand($command);


        foreach ($data as $key => $value) {

            $getter = 'get' . ucwords($key);
            $savedValue = $proposeToRevoke->$getter();

            if($key == 'approvalSubmissionPresidingTc' || $key == 'finalSubmissionPresidingTc') {
                /** @var PresidingTc $presidingTc */
                $presidingTc = $savedValue;
                $savedValue = $presidingTc->getId();
            }

            $this->assertSame($value, $savedValue);

        }

        $expectedResult = [
            'id' => [
                'proposeToRevoke' => $data['id'],
            ],
            'messages' => [
                'Revocation Sla updated successfully'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
    }
}
