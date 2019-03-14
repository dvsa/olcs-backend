<?php

/**
 * ResetToValidTest
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ResetToValid as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\ResetToValid as Cmd;
use Mockery as m;

/**
 * ResetToValidTest
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class ResetToValidTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['lsts_valid'];

        $this->references = [
            Licence::class => m::mock(Licence::class)
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];

        $licence = m::mock(Licence::class)->makePartial();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with(1)
            ->andReturn(
                $licence
            )
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Licence::class))
            ->andReturnUsing(
                function (Licence $licence) {
                    $this->assertEquals($this->refData['lsts_valid'], $licence->getStatus());
                    $this->assertEquals(null, $licence->getCurtailedDate());
                    $this->assertEquals(null, $licence->getSuspendedDate());
                    $this->assertEquals(null, $licence->getRevokedDate());
                    $this->assertEquals(null, $licence->getCnsDate());
                }
            );

        $removeRulesResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            [
                'licence' => $licence
            ],
            $removeRulesResult
        );

        $command = Cmd::create($data);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $result->toArray(),
            [
                'id' => [],
                'messages' => [
                    'Licence ID  reset to valid'
                ]
            ]
        );
    }
}
