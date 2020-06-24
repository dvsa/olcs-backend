<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\ByLicence as QryHandler;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc;
use Dvsa\Olcs\Api\Domain\Repository\PsvDisc;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Query\Surrender\ByLicence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class ByLicenceTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QryHandler();
        $this->mockRepo('Surrender', Surrender::class);
        $this->mockRepo('SystemParameter', SystemParameter::class);
        $this->mockRepo('GoodsDisc', GoodsDisc::class);
        $this->mockRepo('PsvDisc', PsvDisc::class);
        parent::setUp();
    }

    public function testQueryHandle()
    {
        $query = ByLicence::create(['id' => 1]);

        $surrender = m::mock(Surrender::class);
        $surrender->shouldReceive('getLicence->getCorrespondenceCd->getAddress->getLastModifiedOn')->andReturn(new DateTime());
        $surrender->shouldReceive('getLicence->getLicenceType->getId')->andReturn('ltyp_si');
        $this->repoMap['Surrender']->shouldReceive(
            'fetchOneByLicence'
        )->andReturn($surrender);

        $this->repoMap['SystemParameter']->shouldReceive(
            'getDisableGdsVerifySignatures'
        )->andReturn(true);

        $this->repoMap['GoodsDisc']->shouldReceive('countForLicence')->with($query->getId())
            ->andReturn(9);

        $this->repoMap['PsvDisc']->shouldReceive('countForLicence')->with($query->getId())
            ->andReturn(7);


        $expected = new Result(
            $surrender,
            [
                'licence' => [
                    'correspondenceCd' => [
                        'address' => [
                            'countryCode',
                        ],
                        'phoneContacts' => [
                            'phoneContactType',
                        ]
                    ],
                    'organisation'
                ],
                'status',
                'licenceDocumentStatus',
                'communityLicenceDocumentStatus',
                'digitalSignature',
                'signatureType'
            ],
            [
                'disableSignatures' => true,
                'goodsDiscsOnLicence' => 9,
                'psvDiscsOnLicence' => 7,
                'addressLastModified' => new DateTime(),
                'isInternationalLicence' => true

            ]
        );
        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
