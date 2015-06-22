<?php

/**
 * Create Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Statement;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Statement\CreateStatement;
use Dvsa\Olcs\Api\Domain\Repository\Statement as StatementRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Statement\CreateStatement as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Statement as StatementEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Create Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CreateStatementTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateStatement();
        $this->mockRepo('Statement', StatementRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    private function getPayload()
    {
        return [
            "case" => 24,
            "statementType" => "statement_t_36",
            "vrm" => "AB12CDE",
            "requestorsContactDetails" =>  [
                "person" =>  [
                    "title" => "title_mr",
                    "forename" =>  "Bob",
                    "familyName" =>  "Smith"
                ],
                "address" =>  [
                    "addressLine1" =>  "Unit 5",
                    "addressLine2" =>  "12 Albert Street",
                    "addressLine3" =>  "Westpoint",
                    "addressLine4" =>  "",
                    "countryCode" =>  "GB",
                    "postcode" =>  "LS9 6NA",
                    "town" =>  "Leeds"
                ]
            ],
            "requestorsBody" => "REQUESTORS BODY",
            "stoppedDate" => "2015-01-05",
            "requestedDate" => "2015-05-05",
            "contactType" => "cm_email",
            "authorisersDecision" => "Decision is ..."
        ];
    }

    private function getReferencedPayload()
    {
        return [
            "case" => 24,
            "statementType" => new RefDataEntity(),
            "vrm" => "AB12CDE",
            "requestorsContactDetails" =>  [
                "person" =>  [
                    "title" => new RefDataEntity(),
                    "forename" =>  "Bob",
                    "familyName" =>  "Smith"
                ],
                "address" =>  [
                    "addressLine1" => "Unit 5",
                    "addressLine2" => "12 Albert Street",
                    "addressLine3" => "Westpoint",
                    "addressLine4" => "",
                    "countryCode" => new CountryEntity(),
                    "postcode" =>  "LS9 6NA",
                    "town" =>  "Leeds"
                ]
            ],
            "requestorsBody" => "REQUESTORS BODY",
            "stoppedDate" => "2015-01-05",
            "requestedDate" => "2015-05-05",
            "contactType" => new RefDataEntity(),
            "authorisersDecision" => "Decision is ..."
        ];
    }

    protected function initReferences()
    {
        $this->refData = [
            'ct_requestor',
            'statement_t_36'
        ];

        $this->references = [
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ],
            LicenceEntity::class => [
                7 => m::mock(LicenceEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $payload = $this->getPayload();
        $referencedPayload = $this->getReferencedPayload();

        $command = Cmd::create(
            $payload
        );

        $this->repoMap['ContactDetails']
            ->shouldReceive('populateRefDataReference')
            ->with($payload['requestorsContactDetails'])
            ->once()
            ->andReturn(
                $referencedPayload['requestorsContactDetails']
            );

        $this->references[LicenceEntity::class][7]->shouldReceive('getLicNo')->andReturn('12345');
        $this->references[CasesEntity::class][24]->setLicence($this->references[LicenceEntity::class][7]);

        /** @var StatementEntity $se */
        $se = null;

        $this->repoMap['Statement']
            ->shouldReceive('save')
            ->with(m::type(StatementEntity::class))
            ->andReturnUsing(
                function (StatementEntity $statement) use (&$se) {
                    $se = $statement;
                    $statement->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Statement created', $result->getMessages());
    }
}
