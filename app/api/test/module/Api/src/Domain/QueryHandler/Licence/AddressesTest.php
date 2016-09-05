<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Addresses;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Addresses
 */
class AddressesTest extends QueryHandlerTestCase
{
    const CONTACT_DETAILS_ID = 8888;

    /** @var  Addresses */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Addresses();

        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('PhoneContact', Repository\PhoneContact::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var Entity\Licence\Licence $licence */
        $mockLicenceEntity = m::mock(Entity\Licence\Licence::class);
        $mockLicenceEntity
            ->shouldReceive('serialize')
            ->with(m::type('array'))
            ->once()
            ->andReturn(
                [
                    'correspondenceCd' => [
                        'phoneContacts' => 'expect_be_replaced',
                    ],
                    'LicenceData' => 'EXPECTED',
                ]
            )
            //
            ->shouldReceive('getCorrespondenceCd->getId')
            ->once()
            ->andReturn(self::CONTACT_DETAILS_ID);

        /** @var Entity\ContactDetails\PhoneContact | m\MockInterface $mockPhoneContact */
        $mockPhoneContact = m::mock(Entity\ContactDetails\PhoneContact::class)
            ->shouldReceive('serialize')
            ->with(['phoneContactType'])
            ->times(2)
            ->andReturn('EXPECTED_PHONE_CONTACT')
            ->getMock();

        $query = TransferQry\Licence\Addresses::create([]);

        $this->repoMap['Licence']->shouldReceive('fetchWithAddressesUsingId')
            ->with($query)
            ->andReturn($mockLicenceEntity);

        $this->repoMap['PhoneContact']->shouldReceive('fetchList')
            ->andReturnUsing(
                function (
                    TransferQry\ContactDetail\PhoneContact\GetList $qry,
                    $hydrateMethod
                ) use ($mockPhoneContact) {
                    static::assertEquals(self::CONTACT_DETAILS_ID, $qry->getContactDetailsId());
                    static::assertEquals('_type, phoneNumber', $qry->getSort());
                    static::assertEquals(Query::HYDRATE_OBJECT, $hydrateMethod);

                    return [$mockPhoneContact, clone $mockPhoneContact];
                }
            );

        //  call & check
        $result = $this->sut->handleQuery($query);

        static::assertInstanceOf(Result::class, $result);

        $expected = [
            'correspondenceCd' => [
                'phoneContacts' => [
                    'EXPECTED_PHONE_CONTACT',
                    'EXPECTED_PHONE_CONTACT',
                ],
            ],
            'LicenceData' => 'EXPECTED',
        ];

        static::assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryCdNull()
    {
        /** @var Entity\Licence\Licence $licence */
        $mockLicenceEntity = m::mock(Entity\Licence\Licence::class)->makePartial();

        $this->repoMap['Licence']
            ->shouldReceive('fetchWithAddressesUsingId')->andReturn($mockLicenceEntity);

        $this->repoMap['PhoneContact']->shouldNotReceive('fetchList');

        //  call
        $this->sut->handleQuery(
            TransferQry\Licence\Addresses::create([])
        );
    }
}
