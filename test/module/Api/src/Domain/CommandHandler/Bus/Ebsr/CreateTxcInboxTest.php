<?php

/**
 * Create TxcInbox Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\CreateTxcInbox;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Orm\Query;

/**
 * Create TxcInbox Test
 */
class CreateTxcInboxTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateTxcInbox();
        $this->mockRepo('Bus', BusRepo::class);
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);

        parent::setUp();
    }

    /**
     * Tests handleCommand
     */
    public function testHandleCommand()
    {
        $busRegId = 11;

        $organisationName = 'organisation name';
        $organisation = new OrganisationEntity();
        $organisation->setName($organisationName);

        $zipDocument = m::mock(DocumentEntity::class);
        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getOrganisation')->andReturn($organisation);
        $ebsrSubmission->shouldReceive('getDocument')->andReturn($zipDocument);
        $ebsrSubmissions = new ArrayCollection([$ebsrSubmission]);

        $localAuthorityDescription = 'local authority description';
        $localAuthority = new LocalAuthorityEntity();
        $localAuthority->setDescription($localAuthorityDescription);
        $localAuthorities = new ArrayCollection([$localAuthority]);

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getEbsrSubmissions')->andReturn($ebsrSubmissions);
        $busReg->shouldReceive('getLocalAuthoritys')->andReturn($localAuthorities);
        $busReg->shouldReceive('isFromEbsr')->andReturn(true);
        $busReg->shouldReceive('getVariationNo')->andReturn(123456);

        $command = Cmd::create(['id' => $busRegId]);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->once()
            ->andReturn($busReg);

        $this->repoMap['TxcInbox']->shouldReceive('save')
            ->once()
            ->with(m::type(TxcInboxEntity::class))
            ->andReturnUsing(
                function (TxcInboxEntity $txcInboxLa) use (&$savedTxcInboxLa) {
                    $txcInboxLa->setId(22);
                }
            );

        $this->repoMap['TxcInbox']->shouldReceive('save')
            ->once()
            ->with(m::type(TxcInboxEntity::class))
            ->andReturnUsing(
                function (TxcInboxEntity $txcInboxOrg) use (&$savedTxcInboxOrg) {
                    $txcInboxOrg->setId(33);
                }
            );

        $laTxcId = 22;
        $orgTxcId = 33;

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'txcInbox_' . $laTxcId => $laTxcId,
                'txcInbox_' . $orgTxcId => $orgTxcId
            ],
            'messages' => [
                'Txc Inbox record created for ' . $localAuthorityDescription,
                'Txc Inbox record created for ' . $organisationName
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
