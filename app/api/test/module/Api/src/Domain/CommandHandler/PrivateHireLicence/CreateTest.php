<?php

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence\Create as CommandHandler;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Create as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
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
        $this->refData = ['ct_hackney'];

        $this->references = [
            Country::class => [
                'CC' => m::mock(Country::class)
            ],
            Licence::class => [
                323 => m::mock(Licence::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $params =[
            'licence' => 323,
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

        $this->repoMap['ContactDetails']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $cd) use ($params) {
                $cd->setId(648);
                $this->assertSame($params['councilName'], $cd->getDescription());
                $this->assertSame($this->refData['ct_hackney'], $cd->getContactType());
                $cd->getAddress()->setId(45);
                $this->assertSame($params['address']['addressLine1'], $cd->getAddress()->getAddressLine1());
                $this->assertSame($params['address']['addressLine2'], $cd->getAddress()->getAddressLine2());
                $this->assertSame($params['address']['addressLine3'], $cd->getAddress()->getAddressLine3());
                $this->assertSame($params['address']['addressLine4'], $cd->getAddress()->getAddressLine4());
                $this->assertSame($params['address']['town'], $cd->getAddress()->getTown());
                $this->assertSame($params['address']['postcode'], $cd->getAddress()->getPostcode());
                $this->assertSame($this->references[Country::class]['CC'], $cd->getAddress()->getCountryCode());
            }
        );

        $this->repoMap['PrivateHireLicence']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence $phl) use ($params) {
                $phl->setId(7);
                $this->assertSame($params['privateHireLicenceNo'], $phl->getPrivateHireLicenceNo());
                $this->assertSame($this->references[Licence::class][323], $phl->getLicence());
                $this->assertSame(648, $phl->getContactDetails()->getId());

            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['address' => 45, 'contactDetails' => 648, 'privateHireLicence' => 7], $response->getIds());
        $this->assertSame(['PrivateHireLicence created'], $response->getMessages());
    }
}
