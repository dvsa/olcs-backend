<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanDeleteSurrender;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

class CanDeleteSurrenderTest extends AbstractValidatorsTestCase
{

    /**
     * @var CanDeleteSurrender
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanDeleteSurrender();
        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($surrender, $expected)
    {
        $statusEntity = m::mock(RefData::class);
        $statusEntity->shouldReceive('getId')->andReturn($surrender['status']);

        $surrenderEntity = m::mock(Surrender::class);
        $surrenderEntity->shouldReceive('getStatus')->andReturn($statusEntity);
        $surrenderEntity->shouldReceive('getCreatedOn')->andReturn($surrender['createdOn']);
        $surrenderEntity->shouldReceive('getLastModifiedOn')->andReturn($surrender['lastModifiedOn']);

        $repo = $this->mockRepo('Surrender');
        $repo->shouldReceive('fetchOneByLicenceId')->with(1)->andReturn($surrenderEntity);

        $this->assertSame($expected, $this->sut->isValid(1));
    }

    public function provider()
    {
        return [
            'is_withdrawn' => [
                'surrender' => [
                    'status' => RefData::SURRENDER_STATUS_WITHDRAWN,
                    'createdOn' => new \DateTime(),
                    'lastModifiedOn' => new \DateTime()
                ],
                'expected' => true
            ],
            'has_expired_created_on' => [
                'surrender' => [
                    'status' => RefData::SURRENDER_STATUS_COMM_LIC_DOCS_COMPLETE,
                    'createdOn' => date_create('3 days ago'),
                    'lastModifiedOn' => null
                ],
                'expected' => true
            ],
            'has_expired_last_modified' => [
                'surrender' => [
                    'status' => RefData::SURRENDER_STATUS_COMM_LIC_DOCS_COMPLETE,
                    'createdOn' => date_create('5 days ago'),
                    'lastModifiedOn' => date_create('4 days ago')
                ],
                'expected' => true
            ],
            'not_withdrawn_or_expired' => [
                'surrender' => [
                    'status' => RefData::SURRENDER_STATUS_DETAILS_CONFIRMED,
                    'createdOn' => new \DateTime(),
                    'lastModifiedOn' => new \DateTime()
                ],
                'expected' => false
            ],
        ];
    }

}
