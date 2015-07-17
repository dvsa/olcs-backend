<?php

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence\Update as CommandHandler;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('PrivateHireLicence', \Dvsa\Olcs\Api\Domain\Repository\PrivateHireLicence::class);
        $this->mockRepo('ContactDetails', \Dvsa\Olcs\Api\Domain\Repository\ContactDetails::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Country::class => [
                'CC' => m::mock(Country::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $params =[
            'id' => 323,
            'version' => 323,
            'privateHireLicenceNo' => 'TOPDOG 1',
            'councilName' => 'Leeds',
            'address' => [
                'addressLine1' => 'LINE 1',
                'addressLine2' => 'LINE 2',
                'addressLine3' => 'LINE 3',
                'addressLine4' => 'LINE 4',
                'town' => 'TOWN',
                'postcode' => 'S1 4QT',
                'countryCode' => 'CC',
            ]
        ];
        $command = Command::create($params);

        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setId(564)
            ->setContactDetails($cd);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchUsingId')->once()->andReturn($phl);

        $this->repoMap['PrivateHireLicence']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence $savePhl) use ($params) {
                $this->assertSame($params['privateHireLicenceNo'], $savePhl->getPrivateHireLicenceNo());
            }
        );

        $this->repoMap['ContactDetails']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $cd) use ($params) {
                $this->assertSame($params['councilName'], $cd->getDescription());
                $this->assertSame($params['address']['addressLine1'], $cd->getAddress()->getAddressLine1());
                $this->assertSame($params['address']['addressLine2'], $cd->getAddress()->getAddressLine2());
                $this->assertSame($params['address']['addressLine3'], $cd->getAddress()->getAddressLine3());
                $this->assertSame($params['address']['addressLine4'], $cd->getAddress()->getAddressLine4());
                $this->assertSame($params['address']['town'], $cd->getAddress()->getTown());
                $this->assertSame($params['address']['postcode'], $cd->getAddress()->getPostcode());
                $this->assertSame($this->references[Country::class]['CC'], $cd->getAddress()->getCountryCode());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['privateHireLicence' => 564], $response->getIds());
        $this->assertSame(['PrivateHireLicence ID 564 updated'], $response->getMessages());
    }
}
