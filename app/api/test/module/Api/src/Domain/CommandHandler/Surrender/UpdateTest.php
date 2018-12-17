<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Update as Sut;
use Dvsa\Olcs\Api\Domain\Repository\Query\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Surrender as SurrenderRepo;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class UpdateTest extends CommandHandlerTestCase
{

    /** @var Sut */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockRepo('Surrender', SurrenderRepo::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    /**
     * @dataProvider handleCommandProvider
     *
     */
    public function testHandleCommand($data)
    {
        $command = Cmd::create($data);

        $surrenderEntity = m::mock(SurrenderEntity::class);

        if (array_key_exists('communityLicenceDocumentStatus', $data)) {
            $surrenderEntity->shouldReceive('setCommunityLicenceDocumentStatus')->once();
        }
        if (array_key_exists('digitalSignature', $data)) {
            $surrenderEntity->shouldReceive('setDigitalSignature')->once();
        }
        if (array_key_exists('licenceDocumentStatus', $data)) {
            $surrenderEntity->shouldReceive('setLicenceDocumentStatus')->once();
            if ($data['licenceDocumentStatus'] == Surrender::SURRENDER_DOC_STATUS_DESTROYED) {
                $surrenderEntity->shouldReceive('setLicenceDocumentInfo')->with(null)->once();
            } else {
                if (array_key_exists('licenceDocumentInfo', $data)) {
                    $surrenderEntity->shouldReceive('setLicenceDocumentInfo')->once();
                }

            }
        }
        if (array_key_exists('status', $data)) {
            $surrenderEntity->shouldReceive('setStatus')->once();
        }
        if (array_key_exists('discDestroyed', $data)) {
            $surrenderEntity->shouldReceive('setDiscDestroyed')->once();
        }
        if (array_key_exists('discLost', $data)) {
            $surrenderEntity->shouldReceive('setDiscLost')->once();
        }
        if (array_key_exists('discLostInfo', $data)) {
            $surrenderEntity->shouldReceive('setDiscLostInfo')->once();
        }
        if (array_key_exists('discStolen', $data)) {
            $surrenderEntity->shouldReceive('setDiscStolen')->once();
        }
        if (array_key_exists('discStolenInfo', $data)) {
            $surrenderEntity->shouldReceive('setDiscStolenInfo')->once();
        }

        if (array_key_exists('signatureType', $data)) {
            $surrenderEntity->shouldReceive('setSignatureType')->once();
        }


        if (array_key_exists('communityLicenceDocumentInfo', $data)) {
            $surrenderEntity->shouldReceive('setCommunityLicenceDocumentInfo')->once();
        }


        $surrenderEntity->shouldReceive('getId')->once()->andReturn(1);

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->andReturn($surrenderEntity)
            ->once();

        $this->repoMap['Surrender']
            ->shouldReceive('save')
            ->with(m::type(SurrenderEntity::class))
            ->once();


        $result = $this->sut->handleCommand($command);

        $this->assertSame(1, $result->getId('surrender'));
        $this->assertSame(['Surrender successfully updated.'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }

    public function handleCommandProvider()
    {
        $data = [
            'case_01' => [
                [
                    'licence' => 11,
                    'communityLicenceDocumentStatus' => 'doc_sts_lost',
                    'digitalSignature' => '1',
                    'discDestroyed' => '1',
                    'discLost' => '0',
                    'discLostInfo' => 'text',
                    'discStolen' => '2',
                    'discStolenInfo' => 'text',
                    'licenceDocumentStatus' => 'doc_sts_destroyed',
                    'licenceDocumentInfo' => 'some licence doc info',
                    'status' => 'surr_sts_comm_lic_docs_complete',
                    'signatureType' => 'sig_physical_signature',
                    'communityLicenceDocumentInfo' => 'some community licence doc info'
                ]
            ],
            'case_02' => [
                [
                    'licence' => 11,
                    'status' => 'surr_sts_comm_lic_docs_complete',
                    'signatureType' => 'sig_digital_signature',
                    'licenceDocumentStatus' => 'doc_sts_stolen',
                    'licenceDocumentInfo' => 'some licence doc info',
                ]
            ],
            'case_03' => [
                [
                    'licence' => 11,
                    'licenceDocumentStatus' => 'doc_sts_destroyed',
                ]
            ],
        ];
        return $data;
    }
}
