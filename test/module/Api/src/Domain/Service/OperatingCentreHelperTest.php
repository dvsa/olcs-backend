<?php

/**
 * Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Service;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\Licence\CreateOperatingCentre;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper;

/**
 * Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreHelperTest extends MockeryTestCase
{
    /**
     * @var OperatingCentreHelper
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new OperatingCentreHelper();
    }

    /**
     * @dataProvider validateWithErrors
     */
    public function testValidateWithErrors($isPsv, $commandData, $expected)
    {
        $entity = m::mock();
        $entity->shouldReceive('isPsv')
            ->andReturn($isPsv);

        $entity->shouldReceive('isGoods')
            ->andReturn(!$isPsv);

        $command = CreateOperatingCentre::create($commandData);

        try {
            $this->sut->validate($entity, $command);
            // If we are expecting errors, but the validate method didn't throw an exception
            if (!empty($expected)) {
                $this->fail('Validation Exception was not thrown');
            }
        } catch (ValidationException $ex) {
            // If we were not expecting any errors, but the exception was thrown
            if (empty($expected)) {
                $this->fail('Validation Exception was thrown');
            }

            $this->assertEquals($expected, $ex->getMessages());
        }
    }

    public function testSaveDocuments()
    {
        $entity = m::mock();

        $operatingCentre = m::mock(OperatingCentre::class);

        /** @var \Dvsa\Olcs\Api\Entity\Doc\Document $document */
        $document = m::mock(\Dvsa\Olcs\Api\Entity\Doc\Document::class)->makePartial();

        $documents = [
            $document
        ];

        $documentRepo = m::mock(Document::class);
        $documentRepo->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($entity)
            ->andReturn($documents)
            ->shouldReceive('save')
            ->with($document);

        $this->sut->saveDocuments($entity, $operatingCentre, $documentRepo);

        $this->assertSame($operatingCentre, $document->getOperatingCentre());
    }

    public function testCreateOperatingCentre()
    {
        $data = [
            'address' => [
                'addressLine1' => '123 foo street'
            ]
        ];
        $command = CreateOperatingCentre::create($data);

        $result = new Result();
        $result->addId('address', 123);

        $commandHandler = m::mock(CommandHandlerManager::class);
        $commandHandler->shouldReceive('handleCommand')
            ->with(m::type(SaveAddress::class))
            ->andReturn($result);

        $result = new Result();

        $address = m::mock(Address::class);

        $ocRepo = m::mock(\Dvsa\Olcs\Api\Domain\Repository\OperatingCentre::class);
        $ocRepo->shouldReceive('getReference')
            ->with(Address::class, 123)
            ->andReturn($address)
            ->shouldReceive('save')
            ->with(m::type(OperatingCentre::class));

        $oc = $this->sut->createOperatingCentre($command, $commandHandler, $result, $ocRepo);

        $expected = [
            'id' => [
                'address' => 123
            ],
            'messages' => []
        ];

        $this->assertSame($address, $oc->getAddress());
        $this->assertEquals($expected, $result->toArray());
    }

    public function testUpdateOperatingCentreLinkPsv()
    {
        $data = [
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 11,
            'permission' => 'Y',
            'sufficientParking' => 'Y',
        ];
        $command = CreateOperatingCentre::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->initCollections();
        $licence->shouldReceive('isPsv')->andReturn(true);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();

        $repo = m::mock();
        $repo->shouldReceive('save')->once()->with($loc);

        $this->sut->updateOperatingCentreLink($loc, $licence, $command, $repo);

        $this->assertEquals(10, $loc->getNoOfVehiclesRequired());
        $this->assertNull($loc->getNoOfTrailersRequired());
        $this->assertEquals('Y', $loc->getPermission());
        $this->assertEquals('Y', $loc->getSufficientParking());
    }

    public function testUpdateOperatingCentreLink()
    {
        $data = [
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 11,
            'permission' => 'Y',
            'sufficientParking' => 'Y',
            'adPlaced' => 'Y',
            'adPlacedIn' => 'Foo',
            'adPlacedDate' => '2015-01-01'
        ];
        $command = CreateOperatingCentre::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->initCollections();
        $licence->shouldReceive('isPsv')->andReturn(false);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();

        $repo = m::mock();
        $repo->shouldReceive('save')->once()->with($loc);

        $this->sut->updateOperatingCentreLink($loc, $licence, $command, $repo);

        $this->assertEquals(10, $loc->getNoOfVehiclesRequired());
        $this->assertEquals(11, $loc->getNoOfTrailersRequired());
        $this->assertEquals('Y', $loc->getPermission());
        $this->assertEquals('Y', $loc->getSufficientParking());

        $this->assertEquals('Y', $loc->getAdPlaced());
        $this->assertEquals('Foo', $loc->getAdPlacedIn());
        $this->assertEquals('2015-01-01', $loc->getAdPlacedDate()->format('Y-m-d'));
    }

    public function validateWithErrors()
    {
        return [
            [
                true,
                [
                    'noOfVehiclesRequired' => 0
                ],
                [
                    'noOfVehiclesRequired' => [
                        [
                            'ERR_OC_VR_1B' => 'ERR_OC_VR_1B'
                        ]
                    ]
                ]
            ],
            [
                false,
                [
                    'noOfVehiclesRequired' => 0,
                    'noOfTrailersRequired' => 0,
                    'adPlaced' => 'N'
                ],
                [
                    'noOfVehiclesRequired' => [
                        [
                            'ERR_OC_VR_1A' => 'ERR_OC_VR_1A'
                        ]
                    ],
                    'noOfTrailersRequired' => [
                        [
                            'ERR_OC_VR_1A' => 'ERR_OC_VR_1A'
                        ]
                    ]
                ]
            ],
            [
                false,
                [
                    'noOfVehiclesRequired' => 0,
                    'noOfTrailersRequired' => 1,
                    'adPlaced' => 'Y',
                    'adPlacedIn' => '',
                    'adPlacedDate' => ''
                ],
                [
                    'adPlacedIn' => [
                        [
                            'ERR_OC_AD_IN_1' => 'ERR_OC_AD_IN_1'
                        ]
                    ],
                    'adPlacedDate' => [
                        [
                            'ERR_OC_AD_DT_1' => 'ERR_OC_AD_DT_1'
                        ]
                    ]
                ]
            ],
            [
                false,
                [
                    'noOfVehiclesRequired' => 0,
                    'noOfTrailersRequired' => 1,
                    'adPlaced' => 'Y',
                    'adPlacedIn' => 'sasdasd',
                    'adPlacedDate' => 'asdsad'
                ],
                []
            ]
        ];
    }
}
